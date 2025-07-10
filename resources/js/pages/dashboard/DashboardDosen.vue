<script setup lang="ts">
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
import { Link, router, usePage } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import moment from 'moment';
import { ref } from 'vue';
const props = defineProps<{
  perkuliahan: Perkuliahan[],
  belum_diacc: number,
  activeDate: string,
  tanggal_pending: string[]
}>()
const page = usePage<{ auth: { user: any } }>()
const datesInWeek = ref(new Array(5).fill('').map((d, i) => moment(props.activeDate).startOf('week').add(i + 1, 'day')))
const prevWeek = () => {
  datesInWeek.value = datesInWeek.value.map(date => date.subtract(1, 'week'))
}
const nextWeek = () => {
  datesInWeek.value = datesInWeek.value.map(date => date.add(1, 'week'))
}
</script>
<template>
  <AppLayout>
    <div class="bg-white px-10 py-5 rounded-xl border border-[#bdbdbd] mb-6">
      <h1 class="text-2xl font-medium">Selamat Datang, {{ page.props.auth.user.dosen.nama }}</h1>
    </div>
    <div class="my-7 flex gap-8">
      <div class="bg-white rounded-xl border border-[#bdbdbd] w-4/6">
        <div class="px-3 py-2 border-b font-medium text-center text-lg">Notifikasi</div>
        <div class="px-6 py-4 rounded-xl border mx-3 my-4"
          :class="belum_diacc ? 'bg-[#ff0000]/10 border-[#ff0000]' : 'bg-green-600/10 border-green-600'">
          <h3 :class="belum_diacc ? 'text-[#ff0000]' : 'text-green-700'">
            {{
              belum_diacc ? `Alert: ${belum_diacc} Kelas Belum Diacc`
                : (perkuliahan.length > 0 ? 'Semua Kelas Sudah Dikonfirmasi' : 'Tidak ada kelas di tanggal tersebut')
            }}
          </h3>
          <p class="text-sm mt-2" v-if="perkuliahan.length > 0">
            {{ belum_diacc ? `Silahkan konfirmasi jadwal mengajar anda!` : 'Silahkan lakukan PBM sesuai jadwal!' }}
          </p>
        </div>
      </div>
      <div class="bg-white rounded-xl border border-[#bdbdbd] w-full">
        <div class="px-3 py-2 border-b flex items-center">
          <Button variant="ghost" @click="prevWeek">
            <ChevronLeft />
          </Button>
          <div class="font-medium text-center text-lg w-full">Kalender Mingguan</div>
          <Button variant="ghost" @click="nextWeek">
            <ChevronRight />
          </Button>
        </div>
        <div class="py-2 px-3 text-center text-sm">
          {{ datesInWeek[0].format('MMMM') + ' ' + (datesInWeek[datesInWeek.length - 1].isSame(datesInWeek[0], 'month')
            ?
            datesInWeek[0].year() :
            `- ${datesInWeek[datesInWeek.length - 1].format('MMMM')} ${datesInWeek[datesInWeek.length - 1].year()}`) }}
        </div>
        <div class="mx-3 my-2 grid grid-cols-5 gap-3">
          <div v-for="(date, i) in datesInWeek" :key="i" class="flex flex-col items-center gap-2">
            <div
              class="px-2 py-1.5 rounded bg-[#3dadff]/20 border border-[#023ec4] text-[#023ec4] text-center text-xs w-full">
              {{
                date.format('dddd') }}
            </div>
            <div class="size-9 rounded-full flex justify-center items-center cursor-pointer relative"
              :class="{ 'bg-[#3dadff]/20': date.isSame(moment(activeDate), 'day') }"
              @click="router.get('', { date: date.format('yyyy-MM-DD') }, { except: ['tanggal_pending'] })">
              <span>{{ date.date() }}</span>
              <div class="size-1.5 rounded-full bg-red-700 absolute top-1 right-1"
                v-if="tanggal_pending.includes(date.format('YYYY-MM-DD'))"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="bg-white" v-if="perkuliahan.length > 0">
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
            <TableHead></TableHead>
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
            <TableCell>
              <Button variant="ghost" size="sm" as-child>
                <Link :href="`/activities/${kuliah.id_kuliah}`">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                  stroke="currentColor" class="size-4">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                </svg>
                </Link>
              </Button>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
  </AppLayout>
</template>