<?php

namespace App\Services;

use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Payment;

class RazorpayPaymentService
{
    protected $api;
    protected $webhookSecret;

    public function __construct()
    {
        $this->api = new Api(
            config('services.razorpay.key_id'),
            config('services.razorpay.key_secret')
        );
        $this->webhookSecret = config('services.razorpay.webhook_secret');
    }

    /**
     * Create a payment order for subscription
     */
    public function createPaymentOrder(Subscription $subscription, User $user)
    {
        try {
            $orderData = [
                'receipt' => 'sub_' . $user->id . '_' . $subscription->id . '_' . time(),
                'amount' => $this->convertToSmallestUnit($subscription->price),
                'currency' => 'INR',
                'notes' => [
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'plan_name' => $subscription->name,
                ],
            ];

            $order = $this->api->order->create($orderData);

            // Create payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'razorpay_order_id' => $order->id,
                'amount' => $subscription->price,
                'currency' => 'INR',
                'status' => 'pending',
                'metadata' => [
                    'order_id' => $order->id,
                    'receipt' => $order->receipt,
                    'amount' => $order->amount,
                    'currency' => $order->currency,
                ],
            ]);

            return [
                'success' => true,
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'amount' => $order->amount,
                'currency' => $order->currency,
                'key_id' => config('services.razorpay.key_id'),
            ];

        } catch (\Exception $e) {
            Log::error('Razorpay order creation failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
            ]);

            return [
                'success' => false,
                'error' => 'Failed to create payment order: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verify payment signature
     */
    public function verifyPaymentSignature($attributes)
    {
        try {
            $this->api->utility->verifyPaymentSignature($attributes);
            return true;
        } catch (\Exception $e) {
            Log::error('Payment signature verification failed', [
                'error' => $e->getMessage(),
                'attributes' => $attributes,
            ]);
            return false;
        }
    }

    /**
     * Process payment success
     */
    public function processPaymentSuccess($paymentId, $orderId, $signature)
    {
        try {
            $attributes = [
                'razorpay_payment_id' => $paymentId,
                'razorpay_order_id' => $orderId,
                'razorpay_signature' => $signature,
            ];

            // Verify signature
            if (!$this->verifyPaymentSignature($attributes)) {
                return [
                    'success' => false,
                    'error' => 'Invalid payment signature',
                ];
            }

            // Find payment record
            $payment = Payment::where('razorpay_order_id', $orderId)->first();
            
            if (!$payment) {
                Log::warning('Payment not found for Razorpay order', ['order_id' => $orderId]);
                return [
                    'success' => false,
                    'error' => 'Payment record not found',
                ];
            }

            // Update payment status
            $payment->update([
                'status' => 'completed',
                'razorpay_payment_id' => $paymentId,
                'completed_at' => now(),
                'metadata' => array_merge($payment->metadata ?? [], [
                    'payment_id' => $paymentId,
                    'signature' => $signature,
                ]),
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
                'razorpay_payment_id' => $paymentId,
                'user_subscription_id' => $userSubscription->id,
            ]);

            return [
                'success' => true,
                'payment_id' => $payment->id,
                'user_subscription_id' => $userSubscription->id,
            ];

        } catch (\Exception $e) {
            Log::error('Error processing payment success', [
                'payment_id' => $paymentId,
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Error processing payment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Process webhook event
     */
    public function processWebhook($payload, $signature)
    {
        try {
            // Verify webhook signature
            $expectedSignature = hash_hmac('sha256', $payload, $this->webhookSecret);
            
            if (!hash_equals($expectedSignature, $signature)) {
                Log::warning('Invalid webhook signature', [
                    'expected' => $expectedSignature,
                    'received' => $signature,
                ]);
                return false;
            }

            $event = json_decode($payload, true);
            $eventType = $event['event'] ?? '';

            switch ($eventType) {
                case 'payment.captured':
                    return $this->handleWebhookPaymentSuccess($event);
                
                case 'payment.failed':
                    return $this->handleWebhookPaymentFailure($event);
                
                default:
                    Log::info('Unhandled Razorpay webhook event', ['type' => $eventType]);
                    return false;
            }

        } catch (\Exception $e) {
            Log::error('Error processing Razorpay webhook', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);
            return false;
        }
    }

    /**
     * Handle webhook payment success
     */
    protected function handleWebhookPaymentSuccess($event)
    {
        try {
            $paymentData = $event['payload']['payment']['entity'];
            $orderId = $paymentData['order_id'];
            $paymentId = $paymentData['id'];

            $payment = Payment::where('razorpay_order_id', $orderId)->first();
            
            if (!$payment) {
                Log::warning('Payment not found for webhook', ['order_id' => $orderId]);
                return false;
            }

            // Update payment if not already completed
            if (!$payment->isCompleted()) {
                $payment->update([
                    'status' => 'completed',
                    'razorpay_payment_id' => $paymentId,
                    'completed_at' => now(),
                    'metadata' => array_merge($payment->metadata ?? [], [
                        'webhook_event' => $event,
                        'payment_id' => $paymentId,
                    ]),
                ]);

                // Create or update user subscription
                UserSubscription::updateOrCreate(
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
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Error handling webhook payment success', [
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Handle webhook payment failure
     */
    protected function handleWebhookPaymentFailure($event)
    {
        try {
            $paymentData = $event['payload']['payment']['entity'];
            $orderId = $paymentData['order_id'];

            $payment = Payment::where('razorpay_order_id', $orderId)->first();
            
            if ($payment) {
                $payment->update([
                    'status' => 'failed',
                    'metadata' => array_merge($payment->metadata ?? [], [
                        'webhook_event' => $event,
                        'failure_reason' => $paymentData['error_description'] ?? 'Unknown error',
                    ]),
                ]);
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Error handling webhook payment failure', [
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Convert price to smallest currency unit (paise for INR)
     */
    protected function convertToSmallestUnit($price)
    {
        return (int) ($price * 100);
    }

    /**
     * Get payment details
     */
    public function getPaymentDetails($paymentId)
    {
        try {
            $payment = $this->api->payment->fetch($paymentId);
            return $payment->toArray();
        } catch (\Exception $e) {
            Log::error('Error fetching payment details', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get order details
     */
    public function getOrderDetails($orderId)
    {
        try {
            $order = $this->api->order->fetch($orderId);
            return $order->toArray();
        } catch (\Exception $e) {
            Log::error('Error fetching order details', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
