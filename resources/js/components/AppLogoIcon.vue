<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" :class="className" v-bind="$attrs">
        <!-- Background rounded square with gradient -->
        <defs>
            <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#3B82F6;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#6366F1;stop-opacity:1" />
            </linearGradient>
        </defs>
        
        <!-- Rounded square background -->
        <rect x="2" y="2" width="28" height="28" rx="8" fill="url(#logoGradient)"/>
        
        <!-- Document icon -->
        <path d="M8 6C8 5.44772 8.44772 5 9 5H19L25 11V26C25 26.5523 24.5523 27 24 27H9C8.44772 27 8 26.5523 8 26V6Z" 
              fill="white" fill-opacity="0.9"/>
        
        <!-- Document fold -->
        <path d="M19 5L25 11H20C19.4477 11 19 10.5523 19 10V5Z" 
              fill="white" fill-opacity="0.7"/>
        
        <!-- Text lines -->
        <rect x="11" y="14" width="10" height="1.5" rx="0.75" fill="#3B82F6"/>
        <rect x="11" y="17" width="8" height="1.5" rx="0.75" fill="#3B82F6"/>
        <rect x="11" y="20" width="6" height="1.5" rx="0.75" fill="#3B82F6"/>
    </svg>
</template>
