<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    public function index(): Response
    {
        try {
            $user = Auth::user();
            $subscriptions = Subscription::active()->get();
            $userSubscription = $user->activeSubscription;

            // Validate subscription data
            $subscriptions = $subscriptions->map(function ($subscription) {
                // Handle unlimited API calls display
                if ($subscription->api_calls_limit === -1) {
                    $subscription->api_calls_limit_display = 'Unlimited';
                } else {
                    $subscription->api_calls_limit_display = $subscription->api_calls_limit;
                }
                
                return $subscription;
            });

            return Inertia::render('Subscriptions/Index', [
                'subscriptions' => $subscriptions,
                'userSubscription' => $userSubscription,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error loading subscriptions', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
            
            return Inertia::render('Subscriptions/Index', [
                'subscriptions' => collect(),
                'userSubscription' => null,
                'error' => 'Failed to load subscription plans. Please try again.',
            ]);
        }
    }

    public function show(Subscription $subscription): Response
    {
        $user = Auth::user();
        $userSubscription = $user->userSubscriptions
            ->where('subscription_id', $subscription->id)
            ->first();

        return Inertia::render('Subscriptions/Show', [
            'subscription' => $subscription,
            'userSubscription' => $userSubscription,
        ]);
    }

    public function adminIndex(): Response
    {
        $subscriptions = Subscription::withCount('userSubscriptions')->get();

        return Inertia::render('Admin/Subscriptions/Index', [
            'subscriptions' => $subscriptions,
        ]);
    }

    public function adminShow(Subscription $subscription): Response
    {
        $userSubscriptions = UserSubscription::where('subscription_id', $subscription->id)
            ->with(['user', 'subscription'])
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Subscriptions/Show', [
            'subscription' => $subscription,
            'userSubscriptions' => $userSubscriptions,
        ]);
    }
}

