<script setup lang="ts">
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogClose
  // DialogTrigger,
} from '@/components/ui/dialog'
import Button from './ui/button/Button.vue';

defineProps<{
  open: boolean,
  title?: string,
  text?: string,
  confirmButton?: string,
  cancelButton?: string,
}>()
defineEmits(['update-open', 'confirm'])
</script>
<template>
  <Dialog :open="open" @update:open="opened => $emit('update-open', opened)">
    <DialogContent>
      <DialogHeader>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
          aria-hidden="true" data-slot="icon" class="size-28 mx-auto mt-4 text-accent-yellow">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"></path>
        </svg>
        <DialogTitle class="text-center text-2xl my-3" v-if="title">{{ title }}</DialogTitle>
        <DialogDescription class="text-center mb-4 text-lg" v-if="text">
          {{ text }}
        </DialogDescription>
      </DialogHeader>

      <DialogFooter class="flex gap-6 w-full justify-center">
        <DialogClose as-child class="grow">
          <Button variant="destructive">{{ cancelButton ?? 'Batal' }}</Button>
        </DialogClose>
        <Button class="grow" @click="$emit('confirm')">{{ confirmButton ?? 'Ya' }}</Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>