<script setup lang="ts">
import DateRangePicker from '@/components/DateRangePicker.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { badgeType } from '@/lib/utils';
import { Perkuliahan } from '@/types/model';
import { useForm } from '@inertiajs/vue3';
import moment from 'moment';

const props = defineProps<{
  perkuliahan: Perkuliahan[],
  start?: { year?: number, month?: number, day?: number },
  end?: { year?: number, month?: number, day?: number }
}>()
const filter = useForm({
  range: {
    start: props.start ?? {},
    end: props.end ?? {},
  }
})
// const filter = useForm({
//   start: props.start,
//   end: props.end,
// })
</script>
<template>
  <AppLayout>
    <h1 class="text-2xl font-medium">Perkuliahan</h1>
    <form class="flex mt-4 mb-6 gap-3 items-center" @submit.prevent="filter.transform(({ range }) => ({
      start: moment({ ...range.start, month: range.start?.month ? range.start?.month - 1 : 0 }).format('yyyy-MM-DD'),
      end: moment({ ...range.end, month: range.end?.month ? range.end?.month - 1 : 0 }).format('yyyy-MM-DD'),
    })).submit('get', '')">
      <!-- <Input type="date" name="start" class="w-auto" v-model="filter.start" />
      <span>-</span>
      <Input type="date" name="end" class="w-auto" v-model="filter.end" /> -->
      <DateRangePicker v-model="filter.range" />
      <Button size="sm">Filter</Button>
    </form>
    <div v-if="perkuliahan.length > 0" class="bg-white">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>Hari & Tanggal</TableHead>
            <TableHead>Waktu</TableHead>
            <TableHead>Mata Kuliah</TableHead>
            <TableHead>SKS</TableHead>
            <TableHead>Status</TableHead>
            <TableHead>Kelas</TableHead>
            <TableHead>Ruangan</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="kuliah in perkuliahan" :key="kuliah.id_kuliah">
            <TableCell class="font-medium">{{ kuliah.hari_tanggal }}</TableCell>
            <TableCell>{{ kuliah.jam }}</TableCell>
            <TableCell>{{ kuliah.mata_kuliah.nama_matkul }}</TableCell>
            <TableCell>{{ kuliah.mata_kuliah.sks.jumlah_sks }}</TableCell>
            <TableCell>
              <Badge :variant="badgeType(kuliah.status)">{{ kuliah.status }}</Badge>
            </TableCell>
            <TableCell>{{ kuliah.mata_kuliah.kelas }}</TableCell>
            <TableCell>{{ kuliah.ruangan }}</TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
    <h2 v-else class="text-xl font-medium my-10 text-center">Tidak ada perkuliahan di periode tersebut.</h2>
  </AppLayout>
</template>