<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <div>
            <Heading>Support</Heading>
            <HeadingSmall>Get help and support for PDF Parse AI</HeadingSmall>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Documentation -->
            <Card>
                <CardHeader>
                    <CardTitle>Documentation</CardTitle>
                    <CardDescription>
                        Learn how to use the PDF Parse AI service
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <FileText class="h-5 w-5 text-primary" />
                            <div>
                                <h4 class="font-medium">API Documentation</h4>
                                <p class="text-sm text-muted-foreground">
                                    Complete API reference and examples
                                </p>
                            </div>
                        </div>
                        <Button variant="outline" class="w-full" @click="$inertia.visit('/api-documentation')">
                            View API Docs
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Contact Support -->
            <Card>
                <CardHeader>
                    <CardTitle>Contact Support</CardTitle>
                    <CardDescription>
                        Get help from our support team
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <Mail class="h-5 w-5 text-primary" />
                            <div>
                                <h4 class="font-medium">Email Support</h4>
                                <p class="text-sm text-muted-foreground">
                                    support@pdfparseai.com
                                </p>
                            </div>
                        </div>
                        <Button variant="outline" class="w-full" @click="openEmail">
                            Send Email
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- FAQ -->
            <Card>
                <CardHeader>
                    <CardTitle>Frequently Asked Questions</CardTitle>
                    <CardDescription>
                        Common questions and answers
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div class="space-y-3">
                            <details class="group">
                                <summary class="flex cursor-pointer items-center justify-between font-medium">
                                    How do I get started with PDF Parse AI?
                                    <ChevronDown class="h-4 w-4 transition-transform group-open:rotate-180" />
                                </summary>
                                <p class="mt-3 text-sm text-muted-foreground">
                                    Upload a PDF file, select an AI model, and click "Parse PDF". The AI will extract structured information and return it as JSON.
                                </p>
                            </details>
                            
                            <details class="group">
                                <summary class="flex cursor-pointer items-center justify-between font-medium">
                                    What file formats are supported?
                                    <ChevronDown class="h-4 w-4 transition-transform group-open:rotate-180" />
                                </summary>
                                <p class="mt-3 text-sm text-muted-foreground">
                                    Currently, only PDF files are supported. Maximum file size depends on your subscription plan.
                                </p>
                            </details>
                            
                            <details class="group">
                                <summary class="flex cursor-pointer items-center justify-between font-medium">
                                    How do I use the API?
                                    <ChevronDown class="h-4 w-4 transition-transform group-open:rotate-180" />
                                </summary>
                                <p class="mt-3 text-sm text-muted-foreground">
                                    Create an API key in your dashboard, then use it to authenticate requests to our API endpoints. See the API documentation for details.
                                </p>
                            </details>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- System Status -->
            <Card>
                <CardHeader>
                    <CardTitle>System Status</CardTitle>
                    <CardDescription>
                        Current system status and performance
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm">API Service</span>
                            <Badge variant="default">Operational</Badge>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm">AI Models</span>
                            <Badge variant="default">Available</Badge>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm">File Processing</span>
                            <Badge variant="default">Normal</Badge>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
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
import { FileText, Mail, ChevronDown } from 'lucide-vue-next';

const breadcrumbs = [
    {
        title: 'Support',
        href: '/support',
    },
];

const openEmail = () => {
    window.location.href = 'mailto:support@pdfparseai.com?subject=PDF Parse AI Support';
};
</script>
