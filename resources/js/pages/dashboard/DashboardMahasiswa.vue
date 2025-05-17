<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

// defineProps<{
//   total_kelas: number,
//   total_terkonfirmasi: number,
//   total_belum_konfirm: number,
//   perkuliahan_belum_konfirm: Perkuliahan[],
// }>()
// const page = usePage<{ auth: { user: any } }>()
</script>
<template>

  <Head title="Dashboard" />

  <AppLayout>
    <div>Hello Mahasiswa</div>
  </AppLayout>
</template>