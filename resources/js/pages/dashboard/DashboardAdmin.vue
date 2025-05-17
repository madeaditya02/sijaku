<script setup lang="ts">
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Perkuliahan } from '@/types/model';
import { Head, usePage } from '@inertiajs/vue3';

defineProps<{
  total_kelas: number,
  total_terkonfirmasi: number,
  total_belum_konfirm: number,
  perkuliahan_belum_konfirm: Perkuliahan[],
}>()
const page = usePage<{ auth: { user: any } }>()
</script>
<template>

  <Head title="Dashboard" />

  <AppLayout>
    <div class="px-4 py-5 rounded-xl border border-stroke-grey font-medium text-2xl bg-white">
      Selamat datang, {{ page.props.auth.user.admin.nama }}
    </div>
    <div class="my-6 px-10 py-5 rounded-xl bg-white border border-stroke-grey grid grid-cols-3 gap-6">
      <div class="p-5 rounded-2xl border border-stroke-grey text-center flex flex-col">
        <h2 class="text-4xl font-medium mb-2">{{ total_kelas ?? 0 }}</h2>
        <div class="flex items-center justify-center ">
          <h4>Kelas Hari Ini</h4>
        </div>
      </div>
      <div class="p-5 rounded-2xl border border-stroke-grey text-center flex flex-col">
        <h2 class="text-4xl font-medium mb-2">{{ total_terkonfirmasi ?? 0 }}</h2>
        <div class="flex items-center justify-center ">
          <h4>Kelas Terkonfirmasi</h4>
        </div>
      </div>
      <div class="p-5 rounded-2xl border border-stroke-grey text-center flex flex-col">
        <h2 class="text-4xl font-medium mb-2">{{ total_belum_konfirm ?? 0 }}</h2>
        <div class="flex items-center justify-center ">
          <h4>Kelas Belum Dikonfirmasi</h4>
        </div>
      </div>
    </div>
    <div class="px-10 py-5 rounded-xl bg-white border border-stroke-grey">
      <div v-if="perkuliahan_belum_konfirm.length > 0">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Kode Matkul</TableHead>
              <TableHead>Nama Mata Kuliah</TableHead>
              <TableHead>Dosen Pengampu</TableHead>
              <TableHead>Jam Kuliah</TableHead>
              <TableHead>Ruangan</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="kuliah in perkuliahan_belum_konfirm" :key="kuliah.id_kuliah">
              <TableCell class="font-medium">{{ kuliah.mata_kuliah.kode_matkul }}</TableCell>
              <TableCell>{{ kuliah.mata_kuliah.nama_matkul }}</TableCell>
              <TableCell>{{ kuliah.mata_kuliah.dosen?.nama }}</TableCell>
              <TableCell>{{ kuliah.jam }}</TableCell>
              <TableCell>{{ kuliah.ruangan }}</TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
      <div v-else class="text-center text-2xl font-medium my-6">
        Tidak ada perkuliahan hari ini.
      </div>
    </div>
  </AppLayout>
</template>