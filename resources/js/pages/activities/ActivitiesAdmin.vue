<script setup lang="ts">
import ConfirmModal from '@/components/ConfirmModal.vue';
import Badge from '@/components/ui/badge/Badge.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Select from '@/components/ui/select/Select.vue';
import SelectContent from '@/components/ui/select/SelectContent.vue';
import SelectGroup from '@/components/ui/select/SelectGroup.vue';
import SelectItem from '@/components/ui/select/SelectItem.vue';
import SelectTrigger from '@/components/ui/select/SelectTrigger.vue';
import SelectValue from '@/components/ui/select/SelectValue.vue';
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { badgeType } from '@/lib/utils';
import { Perkuliahan, Semester } from '@/types/model';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ExternalLink, ListPlus, Plus } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
  jumlahMatkul: number,
  jumlahJadwal: number,
  adaPerkuliahan: number,
  semester: Semester[],
  semesterIni: Semester,
  perkuliahanHariIni: Perkuliahan[],
  tanggal: string,
  dateRange: [string, string]
}>()
const tanggal = ref(props.tanggal)
const listSemester = ref(props.semester.map(smt => ({ ...smt, id: Object.values(smt).join('-') })))
const selectedSemesterId = ref(Object.values(props.semesterIni).join('-'))
const selectedSemester = computed(() => listSemester.value.find((smt) => smt.id == selectedSemesterId.value))
watch(selectedSemesterId, val => {
  const params = val.split('-')
  router.get(usePage().url.split('?')[0], {
    semester: params[0],
    tahun_1: params[1],
    tahun_2: params[2]
  }, { replace: true })
})
watch(tanggal, val => {
  const params = selectedSemesterId.value.split('-')
  router.get(usePage().url.split('?')[0], {
    semester: params[0],
    tahun_1: params[1],
    tahun_2: params[2],
    tanggal: val
  }, { replace: true })
})
const confirmGenerate = ref(false)
</script>
<template>
  <AppLayout>
    <h1 class="text-2xl font-medium mb-4">Perkuliahan</h1>
    <div class="flex justify-between">
      <Select v-model="selectedSemesterId" class="bg-white">
        <SelectTrigger class="w-[280px]">
          <SelectValue placeholder="Select a timezone" />
        </SelectTrigger>
        <SelectContent>
          <SelectGroup>
            <SelectItem :value="smt.id" v-for="smt in listSemester" :key="smt.id">
              {{ smt.semester }} - {{ smt.tahun_ajaran_pertama }}/{{ smt.tahun_ajaran_kedua }}
            </SelectItem>
          </SelectGroup>
        </SelectContent>
      </Select>
      <div v-if="jumlahMatkul > 0 && jumlahJadwal == jumlahMatkul && adaPerkuliahan">
        <Input type="date" v-model="tanggal" :min="dateRange[0]" :max="dateRange[1]" />
      </div>
    </div>
    <div class="p-6 bg-white rounded-lg mt-6" v-if="jumlahMatkul <= 0">
      <div class="text-center">
        <h2 class="text-2xl font-medium">Belum ada mata kuliah di semester ini</h2>
        <p class="text-lg mt-4 mb-6">Silahkan ke halaman Schedules untuk menambahkan mata kuliah di semester ini.</p>
        <Button size="lg" as-child>
          <Link
            :href="`/schedules/add?semester=${selectedSemester?.semester}&tahun_1=${selectedSemester?.tahun_ajaran_pertama}&tahun_2=${selectedSemester?.tahun_ajaran_kedua}`">
          <Plus class="size-5 mr-1" /> Tambah Mata Kuliah
          </Link>
        </Button>
      </div>
    </div>
    <div class="p-6 bg-white rounded-lg mt-6" v-else-if="jumlahJadwal != jumlahMatkul">
      <div class="text-center">
        <h2 class="text-2xl font-medium">Terdapat mata kuliah yang belum dijadwalkan!</h2>
        <p class="text-lg mt-4 mb-6">Tentukan jadwal untuk semua mata kuliah yang dibuat sebelum melakukan perkuliahan!
        </p>
        <Button size="lg" as-child>
          <Link :href="`/schedules`">
          <ExternalLink class="size-5 mr-1" /> Cek Jadwal Mata Kuliah
          </Link>
        </Button>
      </div>
    </div>
    <div class="p-6 bg-white rounded-lg mt-6" v-else-if="!adaPerkuliahan">
      <div class="text-center">
        <h2 class="text-2xl font-medium">Belum ada perkuliahan!</h2>
        <p class="text-lg mt-4 mb-6">
          Tekan tombol di bawah untuk membuat data perkuliahan semester ini.
        </p>
        <Button size="lg" @click="confirmGenerate = true">
          <ListPlus class="size-5 mr-1" /> Buat Perkuliahan
        </Button>
      </div>
    </div>
    <div class="p-6 bg-white rounded-lg mt-6" v-else-if="perkuliahanHariIni.length > 0">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>Hari, Tanggal</TableHead>
            <TableHead>Waktu</TableHead>
            <TableHead>Mata Kuliah</TableHead>
            <TableHead>Status</TableHead>
            <TableHead>Kelas</TableHead>
            <TableHead>Ruangan</TableHead>
            <TableHead></TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="kuliah in perkuliahanHariIni" :key="kuliah.id_kuliah">
            <TableCell class="font-medium">{{ kuliah.hari_tanggal }}</TableCell>
            <TableCell>{{ kuliah.jam }}</TableCell>
            <TableCell>{{ kuliah.mata_kuliah.nama_matkul }}</TableCell>
            <TableCell>
              <Badge :variant="badgeType(kuliah.status)">{{ kuliah.status }}</Badge>
            </TableCell>
            <TableCell>{{ kuliah.mata_kuliah.kelas }}</TableCell>
            <TableCell>{{ kuliah.ruangan }}</TableCell>
            <TableCell>
              <Button variant="tertiary" as-child>
                <Link :href="`/schedules/${kuliah.mata_kuliah.id_matkul}`">Detail</Link>
              </Button>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
    <div class="p-6 bg-white rounded-lg mt-6" v-else>
      <div class="text-center">
        <h2 class="text-2xl font-medium">Tidak ada perkuliahan.</h2>
      </div>
    </div>

    <ConfirmModal :open="confirmGenerate" title="Anda yakin?" @update-open="open => confirmGenerate = open"
      @confirm="router.post('/activities/generate', { semester: selectedSemesterId }, { onSuccess: () => confirmGenerate = false })"
      text="Dengan mengklik 'Ya', data perkuliahan akan dibuat untuk semua mata kuliah selama 16 minggu dimulai dari tanggal 1 Maret." />
  </AppLayout>
</template>