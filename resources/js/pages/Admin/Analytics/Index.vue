<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
        <div>
            <Heading>System Analytics</Heading>
            <HeadingSmall>Overview of system usage and performance metrics</HeadingSmall>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">Total Users</CardTitle>
                    <Users class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">1,234</div>
                    <p class="text-xs text-muted-foreground">+12% from last month</p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">PDFs Parsed</CardTitle>
                    <FileText class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">8,567</div>
                    <p class="text-xs text-muted-foreground">+25% from last month</p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">API Calls</CardTitle>
                    <BarChart3 class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">45,678</div>
                    <p class="text-xs text-muted-foreground">+18% from last month</p>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">Active Subscriptions</CardTitle>
                    <CreditCard class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">789</div>
                    <p class="text-xs text-muted-foreground">+8% from last month</p>
                </CardContent>
            </Card>
        </div>

        <!-- Usage Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Daily Usage -->
            <Card>
                <CardHeader>
                    <CardTitle>Daily PDF Parsing</CardTitle>
                    <CardDescription>PDF parsing activity over the last 7 days</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div v-for="day in dailyUsage" :key="day.date" class="flex items-center justify-between">
                            <span class="text-sm">{{ day.date }}</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-24 bg-muted rounded-full h-2">
                                    <div 
                                        class="bg-primary h-2 rounded-full" 
                                        :style="{ width: `${(day.count / maxDailyCount) * 100}%` }"
                                    ></div>
                                </div>
                                <span class="text-sm font-medium w-8 text-right">{{ day.count }}</span>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- AI Model Usage -->
            <Card>
                <CardHeader>
                    <CardTitle>AI Model Usage</CardTitle>
                    <CardDescription>Distribution of AI model usage</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div v-for="model in modelUsage" :key="model.name" class="flex items-center justify-between">
                            <span class="text-sm">{{ model.name }}</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-24 bg-muted rounded-full h-2">
                                    <div 
                                        class="bg-primary h-2 rounded-full" 
                                        :style="{ width: `${model.percentage}%` }"
                                    ></div>
                                </div>
                                <span class="text-sm font-medium w-12 text-right">{{ model.percentage }}%</span>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Recent Activity -->
        <Card>
            <CardHeader>
                <CardTitle>Recent Activity</CardTitle>
                <CardDescription>Latest system activities and events</CardDescription>
            </CardHeader>
            <CardContent>
                <div class="space-y-4">
                    <div v-for="activity in recentActivity" :key="activity.id" class="flex items-center space-x-3 p-3 border rounded-lg">
                        <div class="w-2 h-2 bg-primary rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-sm">{{ activity.description }}</p>
                            <p class="text-xs text-muted-foreground">{{ activity.timestamp }}</p>
                        </div>
                        <Badge :variant="activity.type === 'success' ? 'default' : 'secondary'">
                            {{ activity.type }}
                        </Badge>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- System Health -->
        <Card>
            <CardHeader>
                <CardTitle>System Health</CardTitle>
                <CardDescription>Current system status and health metrics</CardDescription>
            </CardHeader>
            <CardContent>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 border rounded-lg">
                        <div class="text-lg font-semibold text-green-600">99.9%</div>
                        <div class="text-sm text-muted-foreground">Uptime</div>
                    </div>
                    <div class="text-center p-4 border rounded-lg">
                        <div class="text-lg font-semibold text-blue-600">1.2s</div>
                        <div class="text-sm text-muted-foreground">Avg Response Time</div>
                    </div>
                    <div class="text-center p-4 border rounded-lg">
                        <div class="text-lg font-semibold text-purple-600">0.1%</div>
                        <div class="text-sm text-muted-foreground">Error Rate</div>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</AppLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Users, FileText, BarChart3, CreditCard } from 'lucide-vue-next';

const breadcrumbs = [
    {
        title: 'Admin',
        href: '/admin',
    },
    {
        title: 'System Analytics',
        href: '/admin/analytics',
    },
];

// Mock data for demonstration
const dailyUsage = [
    { date: 'Mon', count: 120 },
    { date: 'Tue', count: 95 },
    { date: 'Wed', count: 140 },
    { date: 'Thu', count: 108 },
    { date: 'Fri', count: 160 },
    { date: 'Sat', count: 85 },
    { date: 'Sun', count: 75 },
];

const maxDailyCount = computed(() => Math.max(...dailyUsage.map(d => d.count)));

const modelUsage = [
    { name: 'OpenAI GPT-4', percentage: 65 },
    { name: 'Google Gemini Pro', percentage: 35 },
];

const recentActivity = [
    {
        id: 1,
        description: 'New user registered: john.doe@example.com',
        timestamp: '2 minutes ago',
        type: 'success'
    },
    {
        id: 2,
        description: 'PDF parsed successfully using GPT-4',
        timestamp: '5 minutes ago',
        type: 'success'
    },
    {
        id: 3,
        description: 'API key generated for user@company.com',
        timestamp: '8 minutes ago',
        type: 'info'
    },
    {
        id: 4,
        description: 'Subscription upgraded to Pro plan',
        timestamp: '12 minutes ago',
        type: 'success'
    },
    {
        id: 5,
        description: 'AI model configuration updated',
        timestamp: '15 minutes ago',
        type: 'info'
    },
];
</script>
