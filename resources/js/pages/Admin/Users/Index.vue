<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
        <div>
            <Heading>User Management</Heading>
            <HeadingSmall>Manage all users, their roles, and subscriptions</HeadingSmall>
        </div>

        <!-- Users List -->
        <Card>
            <CardHeader>
                <CardTitle>All Users</CardTitle>
                <CardDescription>
                    {{ users.total }} user{{ users.total !== 1 ? 's' : '' }} registered
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div class="space-y-4">
                    <div v-for="user in users.data" :key="user.id" class="border rounded-lg p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                                    <span class="text-primary font-semibold">{{ user.name.charAt(0).toUpperCase() }}</span>
                                </div>
                                <div>
                                    <h4 class="font-medium">{{ user.name }}</h4>
                                    <p class="text-sm text-muted-foreground">{{ user.email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <Badge :variant="user.role === 'super' ? 'destructive' : 'default'">
                                    {{ user.role === 'super' ? 'Super Admin' : 'User' }}
                                </Badge>
                                <Badge v-if="user.activeSubscription" variant="secondary">
                                    {{ user.activeSubscription.subscription.name }}
                                </Badge>
                                <Badge v-else variant="outline">No Subscription</Badge>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-muted-foreground">Joined:</span>
                                <span class="ml-2 font-medium">{{ new Date(user.created_at).toLocaleDateString() }}</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground">Last Login:</span>
                                <span class="ml-2 font-medium">{{ user.last_login_at ? new Date(user.last_login_at).toLocaleDateString() : 'Never' }}</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground">PDFs Parsed:</span>
                                <span class="ml-2 font-medium">{{ user.pdf_parses_count || 0 }}</span>
                            </div>
                            <div>
                                <span class="text-muted-foreground">API Keys:</span>
                                <span class="ml-2 font-medium">{{ user.api_keys_count || 0 }}</span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <Button 
                                variant="outline" 
                                size="sm" 
                                @click="openRoleDialog(user)"
                                :disabled="user.role === 'super'"
                            >
                                Change Role
                            </Button>
                            <Button 
                                variant="outline" 
                                size="sm" 
                                @click="openSubscriptionDialog(user)"
                            >
                                Manage Subscription
                            </Button>
                            <Button 
                                variant="outline" 
                                size="sm" 
                                @click="viewUserDetails(user)"
                            >
                                View Details
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="users.last_page > 1" class="mt-6 flex items-center justify-center space-x-2">
                    <Button 
                        variant="outline" 
                        size="sm" 
                        :disabled="users.current_page === 1"
                        @click="changePage(users.current_page - 1)"
                    >
                        Previous
                    </Button>
                    <span class="text-sm text-muted-foreground">
                        Page {{ users.current_page }} of {{ users.last_page }}
                    </span>
                    <Button 
                        variant="outline" 
                        size="sm" 
                        :disabled="users.current_page === users.last_page"
                        @click="changePage(users.current_page + 1)"
                    >
                        Next
                    </Button>
                </div>
            </CardContent>
        </Card>

        <!-- Role Change Dialog -->
        <Dialog v-model:open="showRoleDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Change User Role</DialogTitle>
                    <DialogDescription>
                        Change the role for user: {{ selectedUser?.name }}
                    </DialogDescription>
                </DialogHeader>
                <form @submit.prevent="updateUserRole" class="space-y-4">
                    <div>
                        <Label for="role">User Role</Label>
                        <select id="role" v-model="newRole" class="w-full p-2 border rounded-md">
                            <option value="user">User</option>
                            <option value="super">Super Admin</option>
                        </select>
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="outline" @click="showRoleDialog = false">
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="!newRole">
                            Update Role
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Subscription Management Dialog -->
        <Dialog v-model:open="showSubscriptionDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Manage User Subscription</DialogTitle>
                    <DialogDescription>
                        Manage subscription for user: {{ selectedUser?.name }}
                    </DialogDescription>
                </DialogHeader>
                <div class="space-y-4">
                    <div v-if="selectedUser?.activeSubscription">
                        <h4 class="font-medium mb-2">Current Subscription</h4>
                        <div class="p-3 bg-muted rounded-md">
                            <div class="font-medium">{{ selectedUser.activeSubscription.subscription.name }}</div>
                            <div class="text-sm text-muted-foreground">
                                Started: {{ new Date(selectedUser.activeSubscription.started_at).toLocaleDateString() }}
                            </div>
                        </div>
                        <Button 
                            variant="destructive" 
                            size="sm" 
                            class="mt-2"
                            @click="revokeSubscription"
                        >
                            Revoke Subscription
                        </Button>
                    </div>
                    
                    <div v-else>
                        <h4 class="font-medium mb-2">Assign Subscription</h4>
                        <div class="space-y-2">
                            <div v-for="subscription in availableSubscriptions" :key="subscription.id" class="flex items-center justify-between p-2 border rounded">
                                <div>
                                    <div class="font-medium">{{ subscription.name }}</div>
                                    <div class="text-sm text-muted-foreground">{{ subscription.price }} / {{ subscription.billing_cycle }}</div>
                                </div>
                                <Button 
                                    size="sm" 
                                    @click="assignSubscription(subscription.id)"
                                >
                                    Assign
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
                <DialogFooter>
                    <Button variant="outline" @click="showSubscriptionDialog = false">
                        Close
                    </Button>
                </DialogFooter>
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
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';

interface Subscription {
    id: number;
    name: string;
    price: string;
    billing_cycle: string;
}

interface UserSubscription {
    id: number;
    subscription: Subscription;
    started_at: string;
}

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
    created_at: string;
    last_login_at?: string;
    pdf_parses_count: number;
    api_keys_count: number;
    activeSubscription?: UserSubscription;
}

interface PaginatedData {
    data: User[];
    current_page: number;
    last_page: number;
    total: number;
}

interface Props {
    users: PaginatedData;
    availableSubscriptions: Subscription[];
}

const props = defineProps<Props>();

const breadcrumbs = [
    {
        title: 'Admin',
        href: '/admin',
    },
    {
        title: 'User Management',
        href: '/admin/users',
    },
];

const showRoleDialog = ref(false);
const showSubscriptionDialog = ref(false);
const selectedUser = ref<User | null>(null);
const newRole = ref('');

const openRoleDialog = (user: User) => {
    selectedUser.value = user;
    newRole.value = user.role;
    showRoleDialog.value = true;
};

const openSubscriptionDialog = (user: User) => {
    selectedUser.value = user;
    showSubscriptionDialog.value = true;
};

const updateUserRole = () => {
    if (selectedUser.value && newRole.value) {
        router.patch(`/admin/users/${selectedUser.value.id}/role`, {
            role: newRole.value
        }, {
            onSuccess: () => {
                showRoleDialog.value = false;
                selectedUser.value = null;
                newRole.value = '';
            }
        });
    }
};

const assignSubscription = (subscriptionId: number) => {
    if (selectedUser.value) {
        router.post(`/admin/users/${selectedUser.value.id}/subscription`, {
            subscription_id: subscriptionId
        });
    }
};

const revokeSubscription = () => {
    if (selectedUser.value) {
        if (confirm('Are you sure you want to revoke this subscription?')) {
            router.delete(`/admin/users/${selectedUser.value.id}/subscription`);
        }
    }
};

const viewUserDetails = (user: User) => {
    router.visit(`/admin/users/${user.id}`);
};

const changePage = (page: number) => {
    router.visit(`/admin/users?page=${page}`);
};
</script>
