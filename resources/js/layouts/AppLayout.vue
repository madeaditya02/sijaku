<script setup lang="ts">
import Sidebar from '@/components/Sidebar.vue';
import type { BreadcrumbItemType } from '@/types';
import { ref } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const show = ref(false)
</script>

<template>
    <!-- <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout> -->
    <Sidebar :show="show" @close="show = false" />
    <div :class="'pl-0 lg:pl-80'">
        <div class="px-8 py-10">
            <div class="mb-4 lg:hidden">
                <button class="cursor-pointer" @click="show = true">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
            <slot />
        </div>
    </div>
</template>
