<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <Heading>API Keys</Heading>
                <HeadingSmall>Manage your API keys for accessing the PDF Parse AI service</HeadingSmall>
            </div>
            <Button @click="showCreateDialog = true">
                <Plus class="h-4 w-4 mr-2" />
                Create API Key
            </Button>
        </div>

        <!-- API Keys List -->
        <Card v-if="apiKeys.length > 0">
            <CardHeader>
                <CardTitle>Your API Keys</CardTitle>
                <CardDescription>
                    Use these keys to authenticate your API requests
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div class="space-y-4">
                    <div v-for="apiKey in apiKeys" :key="apiKey.id" class="flex items-center justify-between p-4 border rounded-lg">
                        <div class="space-y-2">
                            <div class="flex items-center space-x-2">
                                <span class="font-medium">{{ apiKey.name }}</span>
                                <Badge :variant="apiKey.is_active ? 'default' : 'secondary'">
                                    {{ apiKey.is_active ? 'Active' : 'Inactive' }}
                                </Badge>
                            </div>
                            <div class="text-sm text-muted-foreground">
                                <div>Key: <code class="bg-muted px-2 py-1 rounded">{{ apiKey.key }}</code></div>
                                <div>Secret: <code class="bg-muted px-2 py-1 rounded">{{ apiKey.secret }}</code></div>
                                <div v-if="apiKey.last_used_at">
                                    Last used: {{ new Date(apiKey.last_used_at).toLocaleDateString() }}
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <Button
                                variant="outline"
                                size="sm"
                                @click="toggleApiKey(apiKey)"
                            >
                                {{ apiKey.is_active ? 'Deactivate' : 'Activate' }}
                            </Button>
                            <Button
                                variant="destructive"
                                size="sm"
                                @click="deleteApiKey(apiKey)"
                            >
                                Delete
                            </Button>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Empty State -->
        <Card v-else>
            <CardContent class="flex flex-col items-center justify-center py-12">
                <Key class="h-12 w-12 text-muted-foreground mb-4" />
                <h3 class="text-lg font-semibold mb-2">No API Keys Yet</h3>
                <p class="text-muted-foreground text-center mb-4">
                    Create your first API key to start using the PDF Parse AI service programmatically.
                </p>
                <Button @click="showCreateDialog = true">
                    <Plus class="h-4 w-4 mr-2" />
                    Create API Key
                </Button>
            </CardContent>
        </Card>

        <!-- Create API Key Dialog -->
        <Dialog v-model:open="showCreateDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Create New API Key</DialogTitle>
                    <DialogDescription>
                        Create a new API key to access the PDF Parse AI service. Give it a descriptive name.
                    </DialogDescription>
                </DialogHeader>
                <form @submit.prevent="createApiKey" class="space-y-4">
                    <div>
                        <Label for="name">API Key Name</Label>
                        <Input
                            id="name"
                            v-model="newApiKeyName"
                            placeholder="e.g., Production App, Test Environment"
                            required
                        />
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="outline" @click="showCreateDialog = false">
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="!newApiKeyName.trim()">
                            Create API Key
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </div>
</AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Key, Plus } from 'lucide-vue-next';

interface ApiKey {
    id: number;
    name: string;
    key: string;
    secret: string;
    is_active: boolean;
    last_used_at: string | null;
}

interface Props {
    apiKeys: ApiKey[];
}

const props = defineProps<Props>();

const breadcrumbs = [
    {
        title: 'API Keys',
        href: '/api-keys',
    },
];

const showCreateDialog = ref(false);
const newApiKeyName = ref('');

const createApiKey = () => {
    router.post('/api-keys', {
        name: newApiKeyName.value
    }, {
        onSuccess: () => {
            showCreateDialog.value = false;
            newApiKeyName.value = '';
        }
    });
};

const toggleApiKey = (apiKey: ApiKey) => {
    router.patch(`/api-keys/${apiKey.id}/toggle`);
};

const deleteApiKey = (apiKey: ApiKey) => {
    if (confirm(`Are you sure you want to delete the API key "${apiKey.name}"?`)) {
        router.delete(`/api-keys/${apiKey.id}`);
    }
};
</script>
