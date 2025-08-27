<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <div>
                <Heading>Subscribe to {{ subscription.name }}</Heading>
                <HeadingSmall>Complete your payment to activate your subscription</HeadingSmall>
            </div>

            <!-- Error Message -->
            <Card v-if="error" class="border-red-200 bg-red-50">
                <CardContent class="p-4">
                    <div class="flex items-center text-red-700">
                        <AlertCircle class="h-5 w-5 mr-2" />
                        {{ error }}
                    </div>
                </CardContent>
            </Card>

            <!-- Subscription Details -->
            <Card>
                <CardHeader>
                    <CardTitle>Subscription Details</CardTitle>
                    <CardDescription>
                        Review your selected plan before proceeding to payment
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold">{{ subscription.name }}</h3>
                                <p class="text-muted-foreground">{{ subscription.description }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-primary">₹{{ subscription.price }}</div>
                                <div class="text-sm text-muted-foreground">{{ subscription.billing_cycle }}</div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="text-center p-4 border rounded-lg">
                                <div class="text-2xl font-bold text-primary">{{ subscription.api_calls_limit }}</div>
                                <div class="text-sm text-muted-foreground">API Calls / Month</div>
                            </div>
                            <div class="text-center p-4 border rounded-lg">
                                <div class="text-2xl font-bold text-primary">{{ subscription.pdf_size_limit_mb }}MB</div>
                                <div class="text-sm text-muted-foreground">Max PDF Size</div>
                            </div>
                        </div>

                        <div v-if="subscription.features" class="space-y-2">
                            <h4 class="font-medium">Features:</h4>
                            <ul class="list-disc list-inside space-y-1 text-sm text-muted-foreground">
                                <li v-for="feature in subscription.features" :key="feature">
                                    {{ feature }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Payment Form -->
            <Card>
                <CardHeader>
                    <CardTitle>Payment Information</CardTitle>
                    <CardDescription>
                        Click the button below to proceed to secure payment
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-muted rounded-lg">
                            <div>
                                <div class="font-medium">Total Amount</div>
                                <div class="text-sm text-muted-foreground">One-time payment</div>
                            </div>
                            <div class="text-2xl font-bold text-primary">₹{{ subscription.price }}</div>
                        </div>

                        <Button 
                            @click="initiatePayment" 
                            :disabled="isProcessing || isScriptLoading"
                            class="w-full"
                            size="lg"
                        >
                            <Loader2 v-if="isProcessing || isScriptLoading" class="h-4 w-4 mr-2 animate-spin" />
                            {{ isProcessing ? 'Processing...' : isScriptLoading ? 'Loading Payment Gateway...' : 'Proceed to Payment' }}
                        </Button>

                        <p class="text-xs text-muted-foreground text-center">
                            You will be redirected to Razorpay's secure payment gateway
                        </p>
                        
                        <div v-if="error" class="text-center">
                            <Button 
                                @click="retryScriptLoad" 
                                variant="outline" 
                                size="sm"
                                class="mt-2 mr-2"
                            >
                                Retry Loading Payment Gateway
                            </Button>
                            <Button 
                                @click="refreshPage" 
                                variant="outline" 
                                size="sm"
                                class="mt-2"
                            >
                                Refresh Page
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Security Notice -->
            <Card class="border-blue-200 bg-blue-50">
                <CardContent class="p-4">
                    <div class="flex items-start">
                        <Shield class="h-5 w-5 mr-2 text-blue-600 mt-0.5" />
                        <div class="text-sm text-blue-700">
                            <div class="font-medium">Secure Payment</div>
                            <div>Your payment information is encrypted and secure. We use Razorpay, a trusted payment gateway.</div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { AlertCircle, Loader2, Shield } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';

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

interface User {
    id: number;
    name: string;
    email: string;
}

interface Props {
    subscription: Subscription;
    user: User;
    error?: string;
}

const props = defineProps<Props>();

const isProcessing = ref(false);
const error = ref(props.error || '');
const isScriptLoading = ref(false);

const breadcrumbs = [
    {
        title: 'Subscriptions',
        href: '/subscriptions',
    },
    {
        title: 'Payment',
        href: '#',
    },
];

const initiatePayment = async () => {
    try {
        // Validate subscription data
        if (!props.subscription || !props.subscription.id) {
            throw new Error('Invalid subscription data');
        }

        if (!props.subscription.price || parseFloat(props.subscription.price) <= 0) {
            throw new Error('Invalid subscription price');
        }

        isProcessing.value = true;
        error.value = '';

        // Get CSRF token from multiple sources
        const csrfToken = window.csrf_token || 
                         document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('input[name="_token"]')?.value || '';

        if (!csrfToken) {
            throw new Error('CSRF token not found. Please refresh the page and try again.');
        }

        // Create payment order
        const response = await fetch(`/subscriptions/${props.subscription.id}/create-order`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                subscription_id: props.subscription.id,
            }),
        });

        if (!response.ok) {
            if (response.status === 419) {
                throw new Error('Session expired. Please refresh the page and try again.');
            } else if (response.status === 401) {
                throw new Error('You are not authorized. Please log in again.');
            } else if (response.status === 422) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Validation error. Please check your input.');
            } else {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
        }

        const result = await response.json();

        if (!result.success) {
            throw new Error(result.error || 'Failed to create payment order');
        }

        // Check if Razorpay is loaded
        if (!(window as any).Razorpay) {
            if (isScriptLoading.value) {
                throw new Error('Payment gateway is still loading. Please wait a moment and try again.');
            } else {
                throw new Error('Payment gateway failed to load. Please refresh the page or click retry.');
            }
        }

        // Initialize Razorpay checkout
        const options = {
            key: result.key_id,
            amount: result.amount,
            currency: result.currency,
            name: 'PDF Parse AI',
            description: `Subscription to ${props.subscription.name} plan`,
            order_id: result.order_id,
            handler: function (response: any) {
                // Redirect to success page with payment details
                const successUrl = `/payment/success?razorpay_payment_id=${response.razorpay_payment_id}&razorpay_order_id=${response.razorpay_order_id}&razorpay_signature=${response.razorpay_signature}`;
                window.location.href = successUrl;
            },
            prefill: {
                name: props.user.name,
                email: props.user.email,
            },
            theme: {
                color: '#3b82f6',
            },
            modal: {
                ondismiss: function() {
                    isProcessing.value = false;
                },
            },
        };

        const rzp = new (window as any).Razorpay(options);
        rzp.open();

    } catch (err: any) {
        error.value = err.message || 'An error occurred while processing your payment';
        isProcessing.value = false;
    }
};

const retryScriptLoad = () => {
    error.value = '';
    isScriptLoading.value = true;
    
    // Remove existing script if any
    const existingScript = document.querySelector('script[src*="checkout.razorpay.com"]');
    if (existingScript) {
        existingScript.remove();
    }
    
    // Load script again
    const script = document.createElement('script');
    script.src = 'https://checkout.razorpay.com/v1/checkout.js';
    script.onload = () => {
        console.log('Razorpay script loaded successfully on retry');
        isScriptLoading.value = false;
    };
    script.onerror = () => {
        error.value = 'Failed to load payment gateway. Please check your internet connection and try again.';
        isScriptLoading.value = false;
    };
    document.head.appendChild(script);
};

const refreshPage = () => {
    window.location.reload();
};

onMounted(() => {
    // Dynamically load Razorpay script
    if (!(window as any).Razorpay) {
        isScriptLoading.value = true;
        const script = document.createElement('script');
        script.src = 'https://checkout.razorpay.com/v1/checkout.js';
        script.onload = () => {
            console.log('Razorpay script loaded successfully');
            isScriptLoading.value = false;
        };
        script.onerror = () => {
            error.value = 'Failed to load payment gateway. Please refresh the page and try again.';
            isScriptLoading.value = false;
        };
        document.head.appendChild(script);
    }
});
</script>
