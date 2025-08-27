<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
        <div>
            <Heading>Parse History</Heading>
            <HeadingSmall>View all your PDF parsing operations and results</HeadingSmall>
        </div>

        <!-- Parse History List -->
        <Card v-if="pdfParses.data.length > 0">
            <CardHeader>
                <CardTitle>Your PDF Parse History</CardTitle>
                <CardDescription>
                    {{ pdfParses.total }} PDF{{ pdfParses.total !== 1 ? 's' : '' }} parsed
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div class="space-y-4">
                    <div v-for="pdfParse in pdfParses.data" :key="pdfParse.id" class="border rounded-lg p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <FileText class="h-5 w-5 text-muted-foreground" />
                                <div>
                                    <h4 class="font-medium">{{ pdfParse.original_filename }}</h4>
                                    <p class="text-sm text-muted-foreground">
                                        {{ new Date(pdfParse.created_at).toLocaleDateString() }} at {{ new Date(pdfParse.created_at).toLocaleTimeString() }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <Badge :variant="getStatusVariant(pdfParse.status)">
                                    {{ pdfParse.status }}
                                </Badge>
                                <Button variant="outline" size="sm" @click="viewResult(pdfParse)">
                                    View Result
                                </Button>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-muted-foreground">File Size:</span>
                                <span class="ml-2 font-medium">{{ formatFileSize(pdfParse.file_size_bytes) }}</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground">AI Model:</span>
                                <span class="ml-2 font-medium">{{ pdfParse.ai_model?.name || 'Unknown' }}</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground">Processing Time:</span>
                                <span class="ml-2 font-medium">
                                    {{ pdfParse.processed_at ? formatProcessingTime(pdfParse.created_at, pdfParse.processed_at) : 'Pending' }}
                                </span>
                            </div>
                        </div>

                        <div v-if="pdfParse.error_message" class="p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                            <strong>Error:</strong> {{ pdfParse.error_message }}
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="pdfParses.last_page > 1" class="mt-6 flex items-center justify-center space-x-2">
                    <Button 
                        variant="outline" 
                        size="sm" 
                        :disabled="pdfParses.current_page === 1"
                        @click="changePage(pdfParses.current_page - 1)"
                    >
                        Previous
                    </Button>
                    <span class="text-sm text-muted-foreground">
                        Page {{ pdfParses.current_page }} of {{ pdfParses.last_page }}
                    </span>
                    <Button 
                        variant="outline" 
                        size="sm" 
                        :disabled="pdfParses.current_page === pdfParses.last_page"
                        @click="changePage(pdfParses.current_page + 1)"
                    >
                        Next
                    </Button>
                </div>
            </CardContent>
        </Card>

        <!-- Empty State -->
        <Card v-else>
            <CardContent class="flex flex-col items-center justify-center py-12">
                <FileText class="h-12 w-12 text-muted-foreground mb-4" />
                <h3 class="text-lg font-semibold mb-2">No PDFs Parsed Yet</h3>
                <p class="text-muted-foreground text-center mb-4">
                    Start using the PDF Parse AI service to see your parsing history here.
                </p>
                <Button @click="$inertia.visit('/pdf-parse')">
                    Parse Your First PDF
                </Button>
            </CardContent>
        </Card>
    </div>
</AppLayout>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { FileText } from 'lucide-vue-next';

interface AiModel {
    id: number;
    name: string;
}

interface PdfParse {
    id: number;
    original_filename: string;
    file_size_bytes: number;
    status: string;
    created_at: string;
    processed_at?: string;
    error_message?: string;
    ai_model?: AiModel;
}

interface PaginatedData {
    data: PdfParse[];
    current_page: number;
    last_page: number;
    total: number;
}

interface Props {
    pdfParses: PaginatedData;
}

const props = defineProps<Props>();

const breadcrumbs = [
    {
        title: 'Parse History',
        href: '/pdf-parse/history',
    },
];

const getStatusVariant = (status: string) => {
    switch (status.toLowerCase()) {
        case 'completed':
            return 'default';
        case 'processing':
            return 'secondary';
        case 'failed':
            return 'destructive';
        default:
            return 'outline';
    }
};

const formatFileSize = (bytes: number) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const formatProcessingTime = (createdAt: string, processedAt: string) => {
    const created = new Date(createdAt);
    const processed = new Date(processedAt);
    const diffMs = processed.getTime() - created.getTime();
    const diffSeconds = Math.round(diffMs / 1000);
    
    if (diffSeconds < 60) return `${diffSeconds}s`;
    const diffMinutes = Math.round(diffSeconds / 60);
    return `${diffMinutes}m ${diffSeconds % 60}s`;
};

const viewResult = (pdfParse: PdfParse) => {
    router.visit(`/pdf-parse/${pdfParse.id}`);
};

const changePage = (page: number) => {
    router.visit(`/pdf-parse/history?page=${page}`);
};
</script>
