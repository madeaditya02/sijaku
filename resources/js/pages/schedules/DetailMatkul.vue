<script setup lang="ts">
import Badge from '@/components/ui/badge/Badge.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { badgeType, namaHari } from '@/lib/utils';
import { MataKuliah, Perkuliahan } from '@/types/model';

defineProps<{
  mata_kuliah: MataKuliah,
  perkuliahan: Perkuliahan[]
}>()
</script>
<template>
  <AppLayout>
    <h1 class="text-2xl font-medium mb-4">Detail Mata Kuliah</h1>
    <div class="bg-white p-6 rounded-lg">
      <div class="grid grid-cols-2 gap-6">
        <div>
          <Label for="kode_matkul">Kode Mata Kuliah</Label>
          <Input readonly :default-value="mata_kuliah.kode_matkul" id="kode_matkul"
            class="border-0 border-b shadow-none rounded-none focus:ring-0 focus-visible:ring-0 mt-1.5 !text-black" />
        </div>
        <div>
          <Label for="nama_matkul">Nama Mata Kuliah</Label>
          <Input readonly :default-value="mata_kuliah.nama_matkul" id="nama_matkul"
            class="border-0 border-b shadow-none rounded-none focus:ring-0 focus-visible:ring-0 mt-1.5 !text-black" />
        </div>
        <div>
          <Label for="semester">Semester</Label>
          <Input readonly :default-value="mata_kuliah.semester" id="semester"
            class="border-0 border-b shadow-none rounded-none focus:ring-0 focus-visible:ring-0 mt-1.5 !text-black" />
        </div>
        <div>
          <Label for="jadwal">Jadwal</Label>
          <Input readonly v-if="mata_kuliah.jadwal" id="jadwal"
            :default-value="`${namaHari[mata_kuliah.jadwal.hari]}, ${mata_kuliah.jadwal.jam_mulai.split(':').slice(0, 2).join(':')}`"
            class="border-0 border-b shadow-none rounded-none focus:ring-0 focus-visible:ring-0 mt-1.5 !text-black" />
          <Input readonly v-if="!mata_kuliah.jadwal" id="jadwal" value="Belum Ditentukan"
            class="border-0 border-b shadow-none rounded-none focus:ring-0 focus-visible:ring-0 mt-1.5 !text-black" />
        </div>
        <div>
          <Label for="dosen">Dosen Pengampu</Label>
          <Input readonly :default-value="mata_kuliah.dosen?.nama" id="dosen"
            class="border-0 border-b shadow-none rounded-none focus:ring-0 focus-visible:ring-0 mt-1.5 !text-black" />
        </div>
        <div>
          <Label for="ruangan">Ruangan</Label>
          <Input readonly :default-value="mata_kuliah.jadwal?.ruangan.nama_ruangan" id="ruangan"
            class="border-0 border-b shadow-none rounded-none focus:ring-0 focus-visible:ring-0 mt-1.5 !text-black" />
        </div>
        <div>
          <Label for="sks">SKS</Label>
          <Input readonly :default-value="mata_kuliah.sks.jumlah_sks" id="sks"
            class="border-0 border-b shadow-none rounded-none focus:ring-0 focus-visible:ring-0 mt-1.5 !text-black" />
        </div>
        <div>
          <Label for="sks_tatap_muka">SKS Tatap Muka</Label>
          <Input readonly :default-value="mata_kuliah.sks.sks_tatap_muka" id="sks_tatap_muka"
            class="border-0 border-b shadow-none rounded-none focus:ring-0 focus-visible:ring-0 mt-1.5 !text-black" />
        </div>
        <div>
          <Label for="sks_praktikum">SKS Praktikum</Label>
          <Input readonly :default-value="mata_kuliah.sks.sks_praktikum" id="sks_praktikum"
            class="border-0 border-b shadow-none rounded-none focus:ring-0 focus-visible:ring-0 mt-1.5 !text-black" />
        </div>
      </div>
    </div>
    <div class="bg-white p-6 rounded-lg mt-5">
      <div v-if="perkuliahan.length > 0">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Hari, Tanggal</TableHead>
              <TableHead>Jam</TableHead>
              <TableHead>Status</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="aktivitas in perkuliahan" :key="aktivitas.id_kuliah">
              <TableCell class="font-medium">{{ aktivitas.hari_tanggal }}</TableCell>
              <TableCell>{{ aktivitas.jam }}</TableCell>
              <TableCell>
                <Badge :variant="badgeType(aktivitas.status)">{{ aktivitas.status }}</Badge>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
      <h2 class="text-xl text-center font-medium my-4" v-else>Belum ada kegiatan perkuliahan</h2>
    </div>
  </AppLayout>
</template>