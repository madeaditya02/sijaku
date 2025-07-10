<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import { Mahasiswa, MataKuliah } from '@/types/model';
import { Link } from '@inertiajs/vue3';

interface Matkul extends MataKuliah {
  mahasiswa: Mahasiswa[]
}
defineProps<{
  matkul: Matkul
}>()
</script>
<template>
  <AppLayout>
    <div class="flex gap-5">
      <Button variant="ghost" as-child>
        <Link href="/krs">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
          class="size-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        </Link>
      </Button>
      <h1 class="text-3xl font-semibold">List Mahasiswa & Pengampu</h1>
    </div>
    <div class="mt-6 bg-white border rounded-lg p-6">
      <Table>
        <TableBody>
          <TableRow>
            <TableCell class="!w-fit font-medium">Nama Mata Kuliah</TableCell>
            <TableCell>{{ matkul.nama_matkul }}</TableCell>
          </TableRow>
          <TableRow>
            <TableCell class="!w-fit font-medium">Kelas</TableCell>
            <TableCell>{{ matkul.kelas }}</TableCell>
          </TableRow>
          <TableRow>
            <TableCell class="!w-fit font-medium">Nama Dosen</TableCell>
            <TableCell>{{ matkul.dosen?.nama }}</TableCell>
          </TableRow>
          <TableRow>
            <TableCell class="!w-fit font-medium">No. Telepon</TableCell>
            <TableCell>{{ matkul.dosen?.nomor_telepon }}</TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
    <div class="mt-6 bg-white border rounded-lg p-6">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>NIM</TableHead>
            <TableHead>Nama</TableHead>
            <TableHead>No. Telepon</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="mahasiswa in matkul.mahasiswa" :key="mahasiswa.nim">
            <TableCell class="font-medium">{{ mahasiswa.nim }}</TableCell>
            <TableCell>{{ mahasiswa.nama }}</TableCell>
            <TableCell>{{ mahasiswa.nomor_telepon }}</TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
  </AppLayout>
</template>