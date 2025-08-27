<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Payment;

class RozarPaymentService
{
    protected $apiKey;
    protected $baseUrl;
    protected $webhookSecret;

    public function __construct()
    {
        $this->apiKey = config('services.rozar.api_key');
        $this->baseUrl = config('services.rozar.base_url', 'https://api.rozar.com');
        $this->webhookSecret = config('services.rozar.webhook_secret');
    }

    /**
     * Create a payment session for subscription
     */
    public function createPaymentSession(Subscription $subscription, User $user, string $returnUrl, string $cancelUrl)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/v1/payment-sessions', [
                'amount' => $this->convertToSmallestUnit($subscription->price),
                'currency' => 'USD',
                'description' => "Subscription to {$subscription->name} plan",
                'metadata' => [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'plan_name' => $subscription->name,
                ],
                'return_url' => $returnUrl,
                'cancel_url' => $cancelUrl,
                'webhook_url' => route('webhooks.rozar'),
                'customer' => [
                    'email' => $user->email,
                    'name' => $user->name,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Create payment record
                $payment = Payment::create([
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'rozar_session_id' => $data['id'],
                    'amount' => $subscription->price,
                    'currency' => 'USD',
                    'status' => 'pending',
                    'metadata' => $data,
                ]);

                return [
                    'success' => true,
                    'payment_id' => $payment->id,
                    'session_id' => $data['id'],
                    'checkout_url' => $data['checkout_url'] ?? null,
                    'payment_url' => $data['payment_url'] ?? null,
                ];
            }

            Log::error('Rozar payment session creation failed', [
                'response' => $response->json(),
                'status' => $response->status(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to create payment session',
            ];

        } catch (\Exception $e) {
            Log::error('Rozar payment service error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'Payment service error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature($payload, $signature)
    {
        $expectedSignature = hash_hmac('sha256', $payload, $this->webhookSecret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Process webhook event
     */
    public function processWebhook($event)
    {
        try {
            $eventType = $event['type'] ?? '';
            $sessionId = $event['data']['id'] ?? '';

            $payment = Payment::where('rozar_session_id', $sessionId)->first();
            
            if (!$payment) {
                Log::warning('Payment not found for Rozar session', ['session_id' => $sessionId]);
                return false;
            }

            switch ($eventType) {
                case 'payment.succeeded':
                    return $this->handlePaymentSuccess($payment, $event);
                
                case 'payment.failed':
                    return $this->handlePaymentFailure($payment, $event);
                
                case 'payment.cancelled':
                    return $this->handlePaymentCancellation($payment, $event);
                
                default:
                    Log::info('Unhandled Rozar webhook event', ['type' => $eventType]);
                    return false;
            }

        } catch (\Exception $e) {
            Log::error('Error processing Rozar webhook', [
                'error' => $e->getMessage(),
                'event' => $event,
            ]);
            return false;
        }
    }

    /**
     * Handle successful payment
     */
    protected function handlePaymentSuccess(Payment $payment, array $event)
    {
        try {
            // Update payment status
            $payment->update([
                'status' => 'completed',
                'completed_at' => now(),
                'metadata' => array_merge($payment->metadata ?? [], ['webhook_event' => $event]),
            ]);

            // Create or update user subscription
            $userSubscription = UserSubscription::updateOrCreate(
                [
                    'user_id' => $payment->user_id,
                    'subscription_id' => $payment->subscription_id,
                ],
                [
                    'status' => 'active',
                    'started_at' => now(),
                    'expires_at' => now()->addMonth(),
                    'api_calls_used' => 0,
                ]
            );

            Log::info('Payment processed successfully', [
                'payment_id' => $payment->id,
                'user_subscription_id' => $userSubscription->id,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error handling payment success', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Handle payment failure
     */
    protected function handlePaymentFailure(Payment $payment, array $event)
    {
        $payment->update([
            'status' => 'failed',
            'metadata' => array_merge($payment->metadata ?? [], ['webhook_event' => $event]),
        ]);

        Log::info('Payment marked as failed', ['payment_id' => $payment->id]);
        return true;
    }

    /**
     * Handle payment cancellation
     */
    protected function handlePaymentCancellation(Payment $payment, array $event)
    {
        $payment->update([
            'status' => 'cancelled',
            'metadata' => array_merge($payment->metadata ?? [], ['webhook_event' => $event]),
        ]);

        Log::info('Payment marked as cancelled', ['payment_id' => $payment->id]);
        return true;
    }

    /**
     * Convert price to smallest currency unit (cents for USD)
     */
    protected function convertToSmallestUnit($price)
    {
        return (int) ($price * 100);
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus($sessionId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/v1/payment-sessions/' . $sessionId);

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Error getting payment status', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
