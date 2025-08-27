<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Services\RazorpayPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    protected $razorpayService;

    public function __construct(RazorpayPaymentService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    /**
     * Show payment form for subscription
     */
    public function showPaymentForm(Subscription $subscription): Response
    {
        $user = Auth::user();
        
        // Check if user already has an active subscription
        if ($user->activeSubscription) {
            return Inertia::render('Subscriptions/Payment', [
                'subscription' => $subscription,
                'user' => $user,
                'error' => 'You already have an active subscription.',
            ]);
        }

        return Inertia::render('Subscriptions/Payment', [
            'subscription' => $subscription,
            'user' => $user,
        ]);
    }

    /**
     * Create payment order
     */
    public function createOrder(Request $request, Subscription $subscription)
    {
        $user = Auth::user();

        // Log the request for debugging
        \Illuminate\Support\Facades\Log::info('Payment order creation request', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'request_data' => $request->all(),
            'headers' => $request->headers->all(),
            'session_id' => $request->session()->getId(),
        ]);

        // Validate request
        $request->validate([
            'subscription_id' => 'required|exists:subscriptions,id',
        ]);

        // Check if subscription matches
        if ($request->subscription_id != $subscription->id) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid subscription selected',
            ], 400);
        }

        // Create payment order
        $result = $this->razorpayService->createPaymentOrder($subscription, $user);

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 500);
    }

    /**
     * Handle payment success callback
     */
    public function paymentSuccess(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $result = $this->razorpayService->processPaymentSuccess(
            $request->razorpay_payment_id,
            $request->razorpay_order_id,
            $request->razorpay_signature
        );

        if ($result['success']) {
            return redirect()->route('subscriptions.index')
                ->with('success', 'Payment successful! Your subscription is now active.');
        }

        return redirect()->route('subscriptions.index')
            ->with('error', 'Payment verification failed: ' . ($result['error'] ?? 'Unknown error'));
    }

    /**
     * Handle payment failure callback
     */
    public function paymentFailure(Request $request)
    {
        Log::warning('Payment failed', [
            'error_code' => $request->get('error_code'),
            'error_description' => $request->get('error_description'),
            'error_source' => $request->get('error_source'),
            'error_step' => $request->get('error_step'),
            'error_reason' => $request->get('error_reason'),
        ]);

        return redirect()->route('subscriptions.index')
            ->with('error', 'Payment failed. Please try again or contact support.');
    }

    /**
     * Handle webhook from Razorpay
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');

        if (!$signature) {
            Log::warning('Razorpay webhook received without signature');
            return response('Unauthorized', 401);
        }

        $processed = $this->razorpayService->processWebhook($payload, $signature);

        if ($processed) {
            return response('OK', 200);
        }

        return response('Error processing webhook', 500);
    }

    /**
     * Check payment status
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
        ]);

        $orderDetails = $this->razorpayService->getOrderDetails($request->order_id);
        
        if (!$orderDetails) {
            return response()->json([
                'success' => false,
                'error' => 'Order not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'order' => $orderDetails,
        ]);
    }
}
