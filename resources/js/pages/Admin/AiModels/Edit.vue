<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto space-y-6">
            <div>
                <Heading>Edit AI Model</Heading>
                <HeadingSmall>Update AI model configuration</HeadingSmall>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>AI Model Configuration</CardTitle>
                    <CardDescription>
                        Update the details for your AI model configuration
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submitForm" class="space-y-4">
                        <div class="space-y-2">
                            <Label for="name">Model Name</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="e.g., OpenAI GPT-4"
                                required
                            />
                            <InputError v-if="errors.name" :message="errors.name" />
                        </div>

                        <div class="space-y-2">
                            <Label for="model_identifier">Model Identifier</Label>
                            <Input
                                id="model_identifier"
                                v-model="form.model_identifier"
                                type="text"
                                placeholder="e.g., gpt-4"
                                required
                            />
                            <InputError v-if="errors.model_identifier" :message="errors.model_identifier" />
                        </div>

                        <div class="space-y-2">
                            <Label for="description">Description</Label>
                            <Input
                                id="description"
                                v-model="form.description"
                                type="text"
                                placeholder="Brief description of the model"
                            />
                            <InputError v-if="errors.description" :message="errors.description" />
                        </div>

                        <div class="space-y-2">
                            <Label for="endpoint">API Endpoint</Label>
                            <Input
                                id="endpoint"
                                v-model="form.config.endpoint"
                                type="url"
                                placeholder="https://api.openai.com/v1/chat/completions"
                            />
                            <InputError v-if="errors['config.endpoint']" :message="errors['config.endpoint']" />
                        </div>

                        <div class="space-y-2">
                            <Label for="api_key">API Key</Label>
                            <Input
                                id="api_key"
                                v-model="form.config.api_key"
                                type="password"
                                placeholder="Enter your API key"
                            />
                            <InputError v-if="errors['config.api_key']" :message="errors['config.api_key']" />
                            <p class="text-sm text-muted-foreground">
                                Leave blank to keep the existing API key unchanged
                            </p>
                        </div>

                        <div class="flex items-center space-x-2">
                            <Checkbox
                                id="is_active"
                                v-model:checked="form.is_active"
                            />
                            <Label for="is_active">Active</Label>
                        </div>

                        <div class="flex items-center space-x-2 pt-4">
                            <Button type="submit" :disabled="processing">
                                <Save v-if="!processing" class="h-4 w-4 mr-2" />
                                {{ processing ? 'Updating...' : 'Update AI Model' }}
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                @click="$inertia.visit('/admin/ai-models')"
                            >
                                Cancel
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import InputError from '@/components/InputError.vue';
import { Save } from 'lucide-vue-next';

interface AiModel {
    id: number;
    name: string;
    model_identifier: string;
    description?: string;
    is_active: boolean;
    config?: {
        api_key: string;
        endpoint?: string;
    };
}

interface Props {
    aiModel: AiModel;
    errors?: Record<string, string>;
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
    {
        title: 'Edit',
        href: `/admin/ai-models/${props.aiModel.id}/edit`,
    },
];

const form = useForm({
    name: props.aiModel.name,
    model_identifier: props.aiModel.model_identifier,
    description: props.aiModel.description || '',
    is_active: props.aiModel.is_active,
    config: {
        api_key: '', // Leave empty for editing - user can choose to update or keep existing
        endpoint: props.aiModel.config?.endpoint || '',
    },
});

const submitForm = () => {
    console.log('Form submission started');
    
    // Create a clean data object for submission
    const submitData = {
        name: form.name,
        model_identifier: form.model_identifier,
        description: form.description,
        is_active: form.is_active,
        config: {}
    };
    
    // Only include API key if it's been provided
    if (form.config.api_key && form.config.api_key.trim() !== '') {
        submitData.config.api_key = form.config.api_key;
    }
    
    // Only include endpoint if it's been provided
    if (form.config.endpoint && form.config.endpoint.trim() !== '') {
        submitData.config.endpoint = form.config.endpoint;
    }
    
    // If config is empty, set it to null
    if (Object.keys(submitData.config).length === 0) {
        submitData.config = null;
    }
    
    console.log('Submitting data:', submitData);
    
    // Use Inertia router directly for more control
    router.put(`/admin/ai-models/${props.aiModel.id}`, submitData, {
        onSuccess: () => {
            console.log('Form submitted successfully');
        },
        onError: (errors) => {
            console.error('Form submission errors:', errors);
        },
        onFinish: () => {
            console.log('Form submission finished');
        }
    });
};
</script>
