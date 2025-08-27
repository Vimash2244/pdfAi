<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $subscriptions = [
            [
                'name' => 'Basic',
                'description' => 'Basic PDF parsing with limited features',
                'price' => 9.99,
                'billing_cycle' => 'monthly',
                'api_calls_limit' => 100,
                'pdf_size_limit_mb' => 5,
                'features' => [
                    'Basic PDF parsing',
                    '100 API calls per month',
                    '5MB PDF size limit',
                    'Email support'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Pro',
                'description' => 'Professional PDF parsing with advanced features',
                'price' => 29.99,
                'billing_cycle' => 'monthly',
                'api_calls_limit' => 1000,
                'pdf_size_limit_mb' => 25,
                'features' => [
                    'Advanced PDF parsing',
                    '1000 API calls per month',
                    '25MB PDF size limit',
                    'Priority support',
                    'Custom AI model selection'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'description' => 'Enterprise-grade PDF parsing with unlimited features',
                'price' => 99.99,
                'billing_cycle' => 'monthly',
                'api_calls_limit' => -1, // Unlimited
                'pdf_size_limit_mb' => 100,
                'features' => [
                    'Enterprise PDF parsing',
                    'Unlimited API calls',
                    '100MB PDF size limit',
                    '24/7 priority support',
                    'Custom AI model selection',
                    'Advanced analytics',
                    'Custom integrations'
                ],
                'is_active' => true,
            ],
        ];

        foreach ($subscriptions as $subscription) {
            Subscription::create($subscription);
        }
    }
}
