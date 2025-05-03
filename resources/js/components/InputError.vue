<script setup lang="ts">
defineProps<{
    message?: string;
}>();
</script>

<template>
    <div v-show="message">
        <p class="text-sm text-red-600 dark:text-red-500">
            {{ message ? (message?.charAt(0).toUpperCase() + message?.slice(1)) : "" }}
        </p>
    </div>
</template>
