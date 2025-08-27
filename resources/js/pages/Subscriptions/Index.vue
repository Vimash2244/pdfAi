<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
        <!-- Error Message -->
        <Card v-if="error" class="border-red-200 bg-red-50">
            <CardContent class="p-4">
                <div class="flex items-center text-red-700">
                    <AlertCircle class="h-5 w-5 mr-2" />
                    {{ error }}
                </div>
            </CardContent>
        </Card>

        <div>
            <Heading>My Subscriptions</Heading>
            <HeadingSmall>Manage your subscription plans and access to PDF Parse AI features</HeadingSmall>
        </div>

        <!-- Current Subscription -->
        <Card v-if="userSubscription">
            <CardHeader>
                <CardTitle>Current Subscription</CardTitle>
                <CardDescription>
                    Your active subscription plan and its features
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">{{ userSubscription.subscription.name }}</h3>
                            <p class="text-muted-foreground">{{ userSubscription.subscription.description }}</p>
                        </div>
                        <Badge variant="default">Active</Badge>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center p-4 border rounded-lg">
                            <div class="text-2xl font-bold text-primary">{{ userSubscription.subscription.price }}</div>
                            <div class="text-sm text-muted-foreground">{{ userSubscription.subscription.billing_cycle }}</div>
                        </div>
                        <div class="text-center p-4 border rounded-lg">
                            <div class="text-2xl font-bold text-primary">{{ userSubscription.subscription.api_calls_limit }}</div>
                            <div class="text-sm text-muted-foreground">API Calls / Month</div>
                        </div>
                        <div class="text-center p-4 border rounded-lg">
                            <div class="text-2xl font-bold text-primary">{{ userSubscription.subscription.pdf_size_limit_mb }}MB</div>
                            <div class="text-sm text-muted-foreground">Max PDF Size</div>
                        </div>
                    </div>

                    <div v-if="userSubscription.subscription.features" class="space-y-2">
                        <h4 class="font-medium">Features:</h4>
                        <ul class="list-disc list-inside space-y-1 text-sm text-muted-foreground">
                            <li v-for="feature in userSubscription.subscription.features" :key="feature">
                                {{ feature }}
                            </li>
                        </ul>
                    </div>

                    <div class="text-sm text-muted-foreground">
                        <div>Started: {{ new Date(userSubscription.started_at).toLocaleDateString() }}</div>
                        <div v-if="userSubscription.expires_at">
                            Expires: {{ new Date(userSubscription.expires_at).toLocaleDateString() }}
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Available Plans -->
        <Card>
            <CardHeader>
                <CardTitle>Available Plans</CardTitle>
                <CardDescription>
                    Choose a subscription plan that fits your needs
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div v-if="subscriptions.length === 0" class="text-center py-8">
                    <div class="text-muted-foreground">No subscription plans available</div>
                </div>
                <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div v-for="subscription in subscriptions" :key="subscription.id" class="border rounded-lg p-6 space-y-4">
                        <div class="text-center">
                            <h3 class="text-xl font-semibold">{{ subscription.name }}</h3>
                            <div class="text-3xl font-bold text-primary mt-2">{{ subscription.price }}</div>
                            <div class="text-sm text-muted-foreground">{{ subscription.billing_cycle }}</div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm">API Calls:</span>
                                <span class="font-medium">{{ subscription.api_calls_limit_display || subscription.api_calls_limit }}/month</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm">PDF Size Limit:</span>
                                <span class="font-medium">{{ subscription.pdf_size_limit_mb }}MB</span>
                            </div>
                        </div>

                        <div v-if="subscription.features" class="space-y-2">
                            <h4 class="font-medium text-sm">Features:</h4>
                            <ul class="text-sm text-muted-foreground space-y-1">
                                <li v-for="feature in subscription.features" :key="feature" class="flex items-center">
                                    <Check class="h-4 w-4 text-green-500 mr-2" />
                                    {{ feature }}
                                </li>
                            </ul>
                        </div>

                        <Button 
                            v-if="userSubscription?.subscription_id === subscription.id"
                            class="w-full" 
                            variant="outline"
                            disabled
                        >
                            Current Plan
                        </Button>
                        <Button 
                            v-else
                            class="w-full" 
                            @click="goToPayment(subscription)"
                        >
                            Choose Plan
                        </Button>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- No Subscription State -->
        <Card v-if="!userSubscription">
            <CardContent class="flex flex-col items-center justify-center py-12">
                <CreditCard class="h-12 w-12 text-muted-foreground mb-4" />
                <h3 class="text-lg font-semibold mb-2">No Active Subscription</h3>
                <p class="text-muted-foreground text-center mb-4">
                    You need a subscription to access PDF Parse AI features. Choose a plan below to get started.
                </p>
            </CardContent>
        </Card>
    </div>
</AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Check, CreditCard, AlertCircle } from 'lucide-vue-next';

interface Subscription {
    id: number;
    name: string;
    description?: string;
    price: string;
    billing_cycle: string;
    api_calls_limit: number;
    pdf_size_limit_mb: number;
    features: string[];
}

interface UserSubscription {
    id: number;
    subscription_id: number;
    started_at: string;
    expires_at?: string;
    subscription: Subscription;
}

interface Props {
    subscriptions: Subscription[];
    userSubscription?: UserSubscription;
    error?: string;
}

const props = defineProps<Props>();

const breadcrumbs = [
    {
        title: 'My Subscriptions',
        href: '/subscriptions',
    },
];

const goToPayment = (subscription: Subscription) => {
    window.location.href = `/subscriptions/${subscription.id}/payment`;
};
</script>
