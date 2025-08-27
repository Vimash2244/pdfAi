<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import Icon from '@/components/Icon.vue';
import { Separator } from '@/components/ui/separator';

interface ApiKey {
    id: number;
    name: string;
    key: string;
    masked_key: string;
    is_active: boolean;
    last_used_at: string | null;
    created_at: string;
    status: string;
}

interface PdfParse {
    id: number;
    filename: string;
    status: string;
    ai_model: string;
    processed_at: string;
    file_size: string;
}

interface PdfStats {
    total_parses: number;
    completed_parses: number;
    failed_parses: number;
    recent_parses: PdfParse[];
}

interface Subscription {
    name: string;
    status: string;
    expires_at: string;
    days_remaining: number;
    features: string[];
    can_use_ai: boolean;
}

interface QuickStats {
    api_keys_count: number;
    active_api_keys: number;
    total_pdf_parses: number;
    success_rate: number;
}

interface AiModel {
    id: number;
    name: string;
    model_identifier: string;
    description: string;
}

interface User {
    name: string;
    email: string;
    role: string;
    email_verified_at: string | null;
}

interface Props {
    apiKeys: ApiKey[];
    pdfStats: PdfStats;
    subscription: Subscription | null;
    aiModels: AiModel[];
    quickStats: QuickStats;
    user: User;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const getStatusColor = (status: string) => {
    switch (status.toLowerCase()) {
        case 'active':
        case 'completed':
            return 'bg-green-500/10 text-green-700 dark:text-green-400';
        case 'inactive':
        case 'failed':
            return 'bg-red-500/10 text-red-700 dark:text-red-400';
        case 'pending':
            return 'bg-yellow-500/10 text-yellow-700 dark:text-yellow-400';
        default:
            return 'bg-gray-500/10 text-gray-700 dark:text-gray-400';
    }
};

const getStatusIcon = (status: string) => {
    switch (status.toLowerCase()) {
        case 'active':
        case 'completed':
            return 'check-circle';
        case 'inactive':
        case 'failed':
            return 'x-circle';
        case 'pending':
            return 'clock';
        default:
            return 'help-circle';
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6 overflow-x-auto">
            <!-- Welcome Section -->
            <div class="space-y-2">
                <h1 class="text-3xl font-bold tracking-tight">Welcome back, {{ user.name }}!</h1>
                <p class="text-muted-foreground">
                    Here's what's happening with your PDF Parse AI account.
                </p>
            </div>

            <!-- Quick Stats Cards -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">API Keys</CardTitle>
                        <Icon name="key" class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ quickStats.api_keys_count }}</div>
                        <p class="text-xs text-muted-foreground">
                            {{ quickStats.active_api_keys }} active
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">PDF Parses</CardTitle>
                        <Icon name="file-text" class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ quickStats.total_pdf_parses }}</div>
                        <p class="text-xs text-muted-foreground">
                            {{ quickStats.success_rate }}% success rate
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">AI Models</CardTitle>
                        <Icon name="brain" class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ aiModels.length }}</div>
                        <p class="text-xs text-muted-foreground">
                            Available models
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Subscription</CardTitle>
                        <Icon name="credit-card" class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ subscription ? subscription.name : 'None' }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            {{ subscription ? `${subscription.days_remaining} days left` : 'No active plan' }}
                        </p>
                    </CardContent>
                </Card>
            </div>

            <!-- Main Content Grid -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- API Keys Section -->
                <div class="lg:col-span-2">
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Icon name="key" class="h-5 w-5" />
                                API Keys
                            </CardTitle>
                            <CardDescription>
                                Manage your API keys for PDF parsing
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div v-if="apiKeys.length === 0" class="text-center py-8">
                                <Icon name="key" class="h-12 w-12 text-muted-foreground mx-auto mb-4" />
                                <h3 class="text-lg font-medium mb-2">No API keys yet</h3>
                                <p class="text-muted-foreground mb-4">
                                    Create your first API key to start using the PDF Parse AI service.
                                </p>
                                <Button as="a" href="/api-keys" variant="default">
                                    Create API Key
                                </Button>
                            </div>
                            <div v-else class="space-y-3">
                                <div v-for="apiKey in apiKeys" :key="apiKey.id" 
                                     class="flex items-center justify-between p-3 rounded-lg border">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium">{{ apiKey.name }}</span>
                                            <Badge :class="getStatusColor(apiKey.status)">
                                                <Icon :name="getStatusIcon(apiKey.status)" class="h-3 w-3 mr-1" />
                                                {{ apiKey.status }}
                                            </Badge>
                                        </div>
                                        <div class="text-sm text-muted-foreground font-mono">
                                            {{ apiKey.masked_key }}
                                        </div>
                                        <div class="text-xs text-muted-foreground">
                                            Created {{ apiKey.created_at }}
                                            <span v-if="apiKey.last_used_at">
                                                • Last used {{ apiKey.last_used_at }}
                                            </span>
                                        </div>
                                    </div>
                                    <Button as="a" href="/api-keys" variant="outline" size="sm">
                                        Manage
                                    </Button>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Subscription Status -->
                <div>
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Icon name="credit-card" class="h-5 w-5" />
                                Subscription
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="subscription" class="space-y-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold mb-2">{{ subscription.name }}</div>
                                    <Badge :class="getStatusColor(subscription.status)">
                                        {{ subscription.status }}
                                    </Badge>
                                </div>
                                
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-muted-foreground">Expires:</span>
                                        <span class="text-sm font-medium">{{ subscription.expires_at }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-muted-foreground">Days left:</span>
                                        <span class="text-sm font-medium">{{ subscription.days_remaining }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-muted-foreground">AI Access:</span>
                                        <Badge :class="subscription.can_use_ai ? 'bg-green-500/10 text-green-700 dark:text-green-400' : 'bg-red-500/10 text-red-700 dark:text-red-400'">
                                            {{ subscription.can_use_ai ? 'Enabled' : 'Disabled' }}
                                        </Badge>
                                    </div>
                                </div>

                                <div v-if="subscription.features.length > 0">
                                    <Separator class="my-3" />
                                    <h4 class="text-sm font-medium mb-2">Features:</h4>
                                    <div class="space-y-1">
                                        <div v-for="feature in subscription.features" :key="feature" 
                                             class="flex items-center gap-2 text-sm">
                                            <Icon name="check" class="h-4 w-4 text-green-500" />
                                            {{ feature }}
                                        </div>
                                    </div>
                                </div>

                                <Button as="a" href="/subscriptions" variant="outline" class="w-full">
                                    Manage Subscription
                                </Button>
                            </div>
                            <div v-else class="text-center py-8">
                                <Icon name="credit-card" class="h-12 w-12 text-muted-foreground mx-auto mb-4" />
                                <h3 class="text-lg font-medium mb-2">No active subscription</h3>
                                <p class="text-muted-foreground mb-4">
                                    Get a subscription to unlock AI-powered PDF parsing features.
                                </p>
                                <Button as="a" href="/subscriptions" variant="default">
                                    View Plans
                                </Button>
                </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- PDF Parse Statistics -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Icon name="file-text" class="h-5 w-5" />
                        PDF Parse Statistics
                    </CardTitle>
                    <CardDescription>
                        Overview of your PDF parsing activity
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-3 mb-6">
                        <div class="text-center p-4 rounded-lg border">
                            <div class="text-2xl font-bold text-green-600">{{ pdfStats.completed_parses }}</div>
                            <div class="text-sm text-muted-foreground">Completed</div>
                        </div>
                        <div class="text-center p-4 rounded-lg border">
                            <div class="text-2xl font-bold text-red-600">{{ pdfStats.failed_parses }}</div>
                            <div class="text-sm text-muted-foreground">Failed</div>
                        </div>
                        <div class="text-center p-4 rounded-lg border">
                            <div class="text-2xl font-bold text-blue-600">{{ pdfStats.total_parses }}</div>
                            <div class="text-sm text-muted-foreground">Total</div>
                        </div>
                    </div>

                    <div v-if="pdfStats.recent_parses.length > 0">
                        <h4 class="text-sm font-medium mb-3">Recent Parses:</h4>
                        <div class="space-y-2">
                            <div v-for="parse in pdfStats.recent_parses" :key="parse.id" 
                                 class="flex items-center justify-between p-3 rounded-lg border">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium truncate">{{ parse.filename }}</span>
                                        <Badge :class="getStatusColor(parse.status)">
                                            {{ parse.status }}
                                        </Badge>
                                    </div>
                                    <div class="text-sm text-muted-foreground">
                                        {{ parse.ai_model }} • {{ parse.file_size }}
                                    </div>
                                    <div class="text-xs text-muted-foreground">
                                        {{ parse.processed_at }}
                                    </div>
                                </div>
                                <Button as="a" :href="`/pdf-parse/${parse.id}`" variant="outline" size="sm">
                                    View
                                </Button>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <Button as="a" href="/pdf-parse/history" variant="outline">
                                View All History
                            </Button>
                        </div>
                    </div>
                    <div v-else class="text-center py-8">
                        <Icon name="file-text" class="h-12 w-12 text-muted-foreground mx-auto mb-4" />
                        <h3 class="text-lg font-medium mb-2">No PDF parses yet</h3>
                        <p class="text-muted-foreground mb-4">
                            Start parsing your first PDF document to see statistics here.
                        </p>
                        <Button as="a" href="/pdf-parse" variant="default">
                            Parse PDF
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Quick Actions -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Icon name="zap" class="h-5 w-5" />
                        Quick Actions
                    </CardTitle>
                    <CardDescription>
                        Get started with common tasks
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <Button as="a" href="/pdf-parse" variant="outline" class="h-auto p-4 flex-col gap-2">
                            <Icon name="upload" class="h-6 w-6" />
                            <span>Parse PDF</span>
                        </Button>
                        <Button as="a" href="/api-keys" variant="outline" class="h-auto p-4 flex-col gap-2">
                            <Icon name="key" class="h-6 w-6" />
                            <span>Manage API Keys</span>
                        </Button>
                        <Button as="a" href="/pdf-parse/history" variant="outline" class="h-auto p-4 flex-col gap-2">
                            <Icon name="clock" class="h-6 w-6" />
                            <span>View History</span>
                        </Button>
                        <Button as="a" href="/subscriptions" variant="outline" class="h-auto p-4 flex-col gap-2">
                            <Icon name="credit-card" class="h-6 w-6" />
                            <span>Subscriptions</span>
                        </Button>
            </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
