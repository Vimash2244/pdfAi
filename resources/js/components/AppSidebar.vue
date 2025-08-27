<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem, SidebarGroup, SidebarGroupLabel, SidebarMenuSub, SidebarMenuSubButton, SidebarMenuSubItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, FileText, Brain, Users, Settings, Key, BarChart3, Upload, History, Shield, Cog, Database, CreditCard } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const page = usePage();
const user = page.props.auth?.user;

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'PDF Parse AI',
        href: '/pdf-parse',
        icon: FileText,
    },
];

const adminNavItems: NavItem[] = [
    {
        title: 'User Management',
        href: '/admin/users',
        icon: Users,
    },
    {
        title: 'AI Models',
        href: '/admin/ai-models',
        icon: Brain,
    },
    {
        title: 'Subscriptions',
        href: '/admin/subscriptions',
        icon: CreditCard,
    },
    {
        title: 'System Analytics',
        href: '/admin/analytics',
        icon: BarChart3,
    },
];

const userNavItems: NavItem[] = [
    {
        title: 'API Keys',
        href: '/api-keys',
        icon: Key,
    },
    {
        title: 'Parse History',
        href: '/pdf-parse/history',
        icon: History,
    },
    {
        title: 'My Subscriptions',
        href: '/subscriptions',
        icon: CreditCard,
    },
    {
        title: 'Profile Settings',
        href: '/profile',
        icon: Settings,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Documentation',
        href: '/api-documentation',
        icon: BookOpen,
    },
    {
        title: 'Support',
        href: '/support',
        icon: Folder,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <!-- Main Navigation -->
            <SidebarGroup class="px-2 py-0">
                <SidebarGroupLabel>Main</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in mainNavItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="item.href === page.url" :tooltip="item.title">
                            <Link :href="item.href">
                                <component :is="item.icon" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <!-- Admin Navigation (Super Users Only) -->
            <SidebarGroup v-if="user?.role === 'super'" class="px-2 py-0">
                <SidebarGroupLabel>Admin Panel</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in adminNavItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="item.href === page.url" :tooltip="item.title">
                            <Link :href="item.href">
                                <component :is="item.icon" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <!-- User Navigation -->
            <SidebarGroup class="px-2 py-0">
                <SidebarGroupLabel>My Account</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem v-for="item in userNavItems" :key="item.title">
                        <SidebarMenuButton as-child :is-active="item.href === page.url" :tooltip="item.title">
                            <Link :href="item.href">
                                <component :is="item.icon" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
