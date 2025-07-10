<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { MataKuliah, Paginator } from '@/types/model';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Link, useForm } from '@inertiajs/vue3';
import Input from '@/components/ui/input/Input.vue';
import Button from '@/components/ui/button/Button.vue';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import { Pagination, PaginationContent, PaginationItem, PaginationNext, PaginationPrevious } from '@/components/ui/pagination';
import axios from 'axios';

defineProps<{
  matkulKRS: number[],
  listMatkul: Paginator<MataKuliah>,
}>()

const params = new URLSearchParams(window.location.search)
const formFilter = useForm({
  show: params.get('show') == 'all' ? 'all' : parseInt(params.get('show') ?? '10'),
  search: params.get('search') ?? ''
})
// const showQuantity = ref<number | 'all'>(params.get('show') == 'all' ? 'all' : (parseInt(params.get('show')!) ?? 10))
// watch(() => formFilter.show, () => {
//   formFilter.get('')
// })

const selectMatkul = (val: string | boolean, id: number) => {
  axios.post('/krs/ajukan/' + id, { select: val })
}

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
      <h1 class="text-3xl font-semibold">Kartu Rencana Studi (KRS)</h1>
    </div>
    <form @submit.prevent="formFilter.get('')" class="mt-6 flex gap-4">
      <Select class="bg-white" v-model="formFilter.show">
        <SelectTrigger class="w-28">
          <SelectValue placeholder="10" />
        </SelectTrigger>
        <SelectContent>
          <SelectGroup>
            <SelectItem :value="10">10</SelectItem>
            <SelectItem :value="25">25</SelectItem>
            <SelectItem :value="50">50</SelectItem>
            <SelectItem :value="100">100</SelectItem>
            <SelectItem :value="'all'">Semua</SelectItem>
          </SelectGroup>
        </SelectContent>
      </Select>
      <Input class="w-full" placeholder="Cari mata kuliah..." v-model="formFilter.search" />
      <Button>Cari</Button>
    </form>
    <div class="mt-6 bg-white border rounded-lg" v-if="listMatkul.data.length > 0">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead></TableHead>
            <TableHead>Kode Mata Kuliah</TableHead>
            <TableHead>Mata Kuliah</TableHead>
            <TableHead>SKS</TableHead>
            <TableHead>Kelas</TableHead>
            <TableHead>Dosen Pengampu</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="matkul in listMatkul.data" :key="matkul.id_matkul">
            <TableCell>
              <Checkbox :disabled="matkul.kuota <= matkul.jumlah_mahasiswa"
                :default-value="matkulKRS.includes(matkul.id_matkul)"
                @update:model-value="val => selectMatkul(val, matkul.id_matkul)" />
            </TableCell>
            <TableCell class="font-medium">{{ matkul.kode_matkul }}</TableCell>
            <TableCell>{{ matkul.nama_matkul }}</TableCell>
            <TableCell>{{ matkul.sks.jumlah_sks }}</TableCell>
            <TableCell>{{ matkul.kelas }}</TableCell>
            <TableCell>{{ matkul.dosen?.nama }}</TableCell>
          </TableRow>
        </TableBody>
      </Table>
      <Pagination v-slot="{ page }" :items-per-page="listMatkul.meta.per_page" :total="listMatkul.meta.total"
        :default-page="listMatkul.meta.current_page" class="ml-auto justify-end my-4">
        <PaginationContent v-slot="{ items }">
          <PaginationPrevious />

          <template v-for="(item, index) in items" :key="index">
            <PaginationItem v-if="item.type === 'page'" :value="item.value" :is-active="item.value === page" as-child>
              <Link :href="listMatkul.meta.links[index + 1].url">
              {{ item.value }}
              </Link>
            </PaginationItem>
          </template>

          <!-- <PaginationEllipsis :index="4" /> -->

          <PaginationNext />
        </PaginationContent>
      </Pagination>
    </div>
  </AppLayout>
</template>