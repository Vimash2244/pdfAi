<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <Heading>AI Models Management</Heading>
                <HeadingSmall>Manage AI models available for PDF parsing</HeadingSmall>
            </div>
            <Button @click="$inertia.visit('/admin/ai-models/create')">
                <Plus class="h-4 w-4 mr-2" />
                Add AI Model
            </Button>
        </div>

        <!-- AI Models List -->
        <Card v-if="aiModels.data.length > 0">
            <CardHeader>
                <CardTitle>AI Models</CardTitle>
                <CardDescription>
                    {{ aiModels.total }} AI model{{ aiModels.total !== 1 ? 's' : '' }} configured
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div class="space-y-4">
                    <div v-for="aiModel in aiModels.data" :key="aiModel.id" class="border rounded-lg p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <Brain class="h-8 w-8 text-primary" />
                                <div>
                                    <h4 class="font-medium">{{ aiModel.name }}</h4>
                                    <p class="text-sm text-muted-foreground">{{ aiModel.model_identifier }}</p>
                                    <p v-if="aiModel.description" class="text-sm text-muted-foreground">{{ aiModel.description }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <Badge :variant="aiModel.is_active ? 'default' : 'secondary'">
                                    {{ aiModel.is_active ? 'Active' : 'Inactive' }}
                                </Badge>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-muted-foreground">Created:</span>
                                <span class="ml-2 font-medium">{{ new Date(aiModel.created_at).toLocaleDateString() }}</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground">Last Updated:</span>
                                <span class="ml-2 font-medium">{{ new Date(aiModel.updated_at).toLocaleDateString() }}</span>
                            </div>
                        </div>

                        <div v-if="aiModel.config" class="p-3 bg-muted rounded-md">
                            <h5 class="font-medium mb-2">Configuration</h5>
                            <div class="space-y-1 text-sm">
                                <div v-if="aiModel.config.endpoint">
                                    <span class="text-muted-foreground">Endpoint:</span>
                                    <span class="ml-2 font-mono">{{ aiModel.config.endpoint }}</span>
                                </div>
                                <div>
                                    <span class="text-muted-foreground">API Key:</span>
                                    <span class="ml-2 font-mono">{{ maskApiKey(aiModel.config.api_key) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <Button 
                                variant="outline" 
                                size="sm" 
                                @click="$inertia.visit(`/admin/ai-models/${aiModel.id}/edit`)"
                            >
                                Edit
                            </Button>
                            <Button 
                                variant="outline" 
                                size="sm" 
                                @click="toggleStatus(aiModel)"
                            >
                                {{ aiModel.is_active ? 'Deactivate' : 'Activate' }}
                            </Button>
                            <Button 
                                variant="destructive" 
                                size="sm" 
                                @click="deleteModel(aiModel)"
                            >
                                Delete
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="aiModels.last_page > 1" class="mt-6 flex items-center justify-center space-x-2">
                    <Button 
                        variant="outline" 
                        size="sm" 
                        :disabled="aiModels.current_page === 1"
                        @click="changePage(aiModels.current_page - 1)"
                    >
                        Previous
                    </Button>
                    <span class="text-sm text-muted-foreground">
                        Page {{ aiModels.current_page }} of {{ aiModels.last_page }}
                    </span>
                    <Button 
                        variant="outline" 
                        size="sm" 
                        :disabled="aiModels.current_page === aiModels.last_page"
                        @click="changePage(aiModels.current_page + 1)"
                    >
                        Next
                    </Button>
                </div>
            </CardContent>
        </Card>

        <!-- Empty State -->
        <Card v-else>
            <CardContent class="flex flex-col items-center justify-center py-12">
                <Brain class="h-12 w-12 text-muted-foreground mb-4" />
                <h3 class="text-lg font-semibold mb-2">No AI Models Configured</h3>
                <p class="text-muted-foreground text-center mb-4">
                    Add AI models to enable PDF parsing functionality for your users.
                </p>
                <Button @click="$inertia.visit('/admin/ai-models/create')">
                    <Plus class="h-4 w-4 mr-2" />
                    Add First AI Model
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
import { Brain, Plus } from 'lucide-vue-next';

interface AiModel {
    id: number;
    name: string;
    model_identifier: string;
    description?: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    config?: {
        api_key: string;
        endpoint?: string;
    };
}

interface PaginatedData {
    data: AiModel[];
    current_page: number;
    last_page: number;
    total: number;
}

interface Props {
    aiModels: PaginatedData;
}

const props = defineProps<Props>();

const breadcrumbs = [
    {
        title: 'Admin',
        href: '/admin',
    },
    {
        title: 'AI Models',
        href: '/admin/ai-models',
    },
];

const maskApiKey = (apiKey: string) => {
    if (!apiKey) return '';
    return apiKey.slice(0, 8) + '...' + apiKey.slice(-4);
};

const toggleStatus = (aiModel: AiModel) => {
    router.patch(`/admin/ai-models/${aiModel.id}/toggle`, {
        is_active: !aiModel.is_active
    });
};

const deleteModel = (aiModel: AiModel) => {
    if (confirm(`Are you sure you want to delete the AI model "${aiModel.name}"?`)) {
        router.delete(`/admin/ai-models/${aiModel.id}`);
    }
};

const changePage = (page: number) => {
    router.visit(`/admin/ai-models?page=${page}`);
};
</script>
