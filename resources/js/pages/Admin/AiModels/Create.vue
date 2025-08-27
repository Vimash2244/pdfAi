<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto space-y-6">
            <div>
                <Heading>Add AI Model</Heading>
                <HeadingSmall>Configure a new AI model for PDF parsing</HeadingSmall>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>AI Model Configuration</CardTitle>
                    <CardDescription>
                        Enter the details for your AI model configuration
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
                                required
                            />
                            <InputError v-if="errors['config.api_key']" :message="errors['config.api_key']" />
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
                                <Plus v-if="!processing" class="h-4 w-4 mr-2" />
                                {{ processing ? 'Creating...' : 'Create AI Model' }}
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
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import InputError from '@/components/InputError.vue';
import { Plus } from 'lucide-vue-next';

interface Props {
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
        title: 'Create',
        href: '/admin/ai-models/create',
    },
];

const form = useForm({
    name: '',
    model_identifier: '',
    description: '',
    is_active: true,
    config: {
        api_key: '',
        endpoint: '',
    },
});

const submitForm = () => {
    form.post('/admin/ai-models');
};
</script>
