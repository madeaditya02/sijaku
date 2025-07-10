<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { MataKuliah, Paginator, Semester } from '@/types/model';
import { ref, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import Button from '@/components/ui/button/Button.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Pagination, PaginationContent, PaginationItem, PaginationNext, PaginationPrevious } from '@/components/ui/pagination';
// import moment from 'moment';

const props = defineProps<{
  semesterIni: Semester,
  semester: Semester[],
  matkulKRS: Paginator<MataKuliah>,
  adaPerkuliahan: number,
}>()
const page = usePage()
const listSemester = ref(props.semester.map(smt => ({ ...smt, id: Object.values(smt).join('-') })))
const selectedSemesterId = ref(Object.values(props.semesterIni).join('-'))
console.log(props.semesterIni)
// const selectedSemester = computed(() => listSemester.value.find((smt) => smt.id == selectedSemesterId.value))
watch(selectedSemesterId, val => {
  const params = val.split('-')
  router.get(page.url, {
    semester: params[0],
    tahun_1: params[1],
    tahun_2: params[2],
  }, { replace: true })
})
// const startDate = computed(() => {
//   const smt = selectedSemesterId.value.split('-')
//   return smt[0] == 'Genap' ? moment(`${smt[2]}-03-01`) : moment(`${smt[1]}-09-01`);
// })

const params = new URLSearchParams(window.location.search)
interface Params {
  semester?: string | null,
  tahun_1?: string | null,
  tahun_2?: string | null,
  show?: string | null,
}
const showQuantity = ref<number | 'all'>(params.get('show') == 'all' ? 'all' : (parseInt(params.get('show')!) ?? 10))
watch(showQuantity, val => {
  const obj: Params = {}
  for (const key of params.keys())
    obj[key as keyof Params] = params.get(key)
  obj.show = val as string
  router.get('', { ...obj })
})
</script>
<template>
  <AppLayout>
    <h1 class="text-3xl font-semibold">Kartu Rencana Studi (KRS)</h1>
    <div class="flex justify-between items-center mt-8 flex-wrap gap-4">
      <Select v-model="selectedSemesterId" class="bg-white">
        <SelectTrigger class="w-[240px]">
          <SelectValue placeholder="Pilih Semester" />
        </SelectTrigger>
        <SelectContent>
          <SelectGroup>
            <SelectItem :value="smt.id" v-for="smt in listSemester" :key="smt.id">
              {{ smt.semester }} - {{ smt.tahun_ajaran_pertama }}/{{ smt.tahun_ajaran_kedua }}
            </SelectItem>
          </SelectGroup>
        </SelectContent>
      </Select>
      <Button as-child v-if="!adaPerkuliahan">
        <Link href="/krs/ajukan">
        {{ matkulKRS.meta.total > 0 ? 'Ubah KRS' : 'Ajukan KRS' }}
        </Link>
      </Button>
    </div>
    <div class="mt-4">
      <Select class="bg-white" v-model="showQuantity">
        <SelectTrigger class="w-32">
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
    </div>

    <div class="mt-6 bg-white border rounded-lg" v-if="matkulKRS.data.length > 0">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>Kode Mata Kuliah</TableHead>
            <TableHead>Mata Kuliah</TableHead>
            <TableHead>SKS</TableHead>
            <TableHead>Kelas</TableHead>
            <TableHead></TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="matkul in matkulKRS.data" :key="matkul.id_matkul">
            <TableCell class="font-medium">{{ matkul.kode_matkul }}</TableCell>
            <TableCell>{{ matkul.nama_matkul }}</TableCell>
            <TableCell>{{ matkul.sks.jumlah_sks }}</TableCell>
            <TableCell>{{ matkul.kelas }}</TableCell>
            <TableCell>
              <Button as-child size="sm">
                <Link :href="`/krs/details/${matkul.id_matkul}`">
                List Mahasiswa & Pengampu
                </Link>
              </Button>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
      <Pagination v-slot="{ page }" :items-per-page="matkulKRS.meta.per_page" :total="matkulKRS.meta.total"
        :default-page="matkulKRS.meta.current_page" class="ml-auto justify-end my-4">
        <PaginationContent v-slot="{ items }">
          <PaginationPrevious />

          <template v-for="(item, index) in items" :key="index">
            <PaginationItem v-if="item.type === 'page'" :value="item.value" :is-active="item.value === page" as-child>
              <Link :href="matkulKRS.meta.links[index + 1].url">
              {{ item.value }}
              </Link>
            </PaginationItem>
          </template>

          <!-- <PaginationEllipsis :index="4" /> -->

          <PaginationNext />
        </PaginationContent>
      </Pagination>
    </div>
    <h2 class="mt-6 text-center text-2xl font-medium" v-else>Tidak ada mata kuliah di KRS semester ini</h2>
  </AppLayout>
</template>