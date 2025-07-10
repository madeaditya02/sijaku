<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  // SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import AppLayout from '@/layouts/AppLayout.vue';
import { MataKuliah, Ruangan, Semester, Paginator } from '@/types/model';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import { Check, Clock, Ellipsis, ExternalLink, Plus } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table/'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import {
  Dialog,
  DialogContent,
  // DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  // DialogClose
  DialogTrigger,
} from '@/components/ui/dialog'
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationNext,
  PaginationPrevious,
} from '@/components/ui/pagination'
import { Combobox, ComboboxAnchor, ComboboxEmpty, ComboboxGroup, ComboboxInput, ComboboxItem, ComboboxItemIndicator, ComboboxList } from '@/components/ui/combobox'
import InputError from '@/components/InputError.vue';
import Label from '@/components/ui/label/Label.vue';
import { cn, namaHari } from '@/lib/utils';
import Input from '@/components/ui/input/Input.vue';
import { toast } from 'vue-sonner';
import { PopoverClose } from 'reka-ui';
const props = defineProps<{
  semesterIni: Semester,
  semester: Semester[],
  matkulSemester: Paginator<MataKuliah>,
  listRuangan: Ruangan[]
}>()
const page = usePage()
const params = new URLSearchParams(window.location.search)
const listSemester = ref(props.semester.map(smt => ({ ...smt, id: Object.values(smt).join('-') })))
const selectedSemesterId = ref(Object.values(props.semesterIni).join('-'))
const selectedSemester = computed(() => listSemester.value.find((smt) => smt.id == selectedSemesterId.value))
watch(selectedSemesterId, val => {
  const params = val.split('-')
  router.get(page.url, {
    semester: params[0],
    tahun_1: params[1],
    tahun_2: params[2],
  }, { replace: true })
})

const editedJadwal = ref<null | number>(null)
const formJadwal = useForm<{
  hari: undefined | number,
  jam_mulai: undefined | string,
  jam_selesai: undefined | string,
  ruangan: undefined | {
    id_ruangan: number,
    nama_ruangan: string,
    kapasitas: number,
  },
}>({
  hari: undefined,
  jam_mulai: undefined,
  jam_selesai: undefined,
  ruangan: undefined,
})
function openFormJadwal(id_matkul: number) {
  const selectedMatkul = props.matkulSemester.data.find(m => m.id_matkul == id_matkul)?.jadwal
  formJadwal.hari = selectedMatkul?.hari
  formJadwal.jam_mulai = selectedMatkul?.jam_mulai
  formJadwal.jam_selesai = selectedMatkul?.jam_selesai
  formJadwal.ruangan = selectedMatkul?.ruangan
  editedJadwal.value = id_matkul
  // formJadwal.ruangan = selectedMatkul
}
function formJadwalClosed() {
  editedJadwal.value = null
  formJadwal.reset()
}
const showQuantity = ref<number | 'all'>(params.get('show') == 'all' ? 'all' : (parseInt(params.get('show')!) ?? 6))
interface Params {
  semester?: string | null,
  tahun_1?: string | null,
  tahun_2?: string | null,
  show?: string | null,
}
// type Params2 = {
//   [key: string]: string
// }
watch(showQuantity, val => {
  // const params = new URLSearchParams(window.location.search)
  const obj: Params = {}
  for (const key of params.keys())
    obj[key as keyof Params] = params.get(key)
  obj.show = val as string
  router.get('', { ...obj })
})
</script>
<template>
  <AppLayout>
    <h1 class="text-2xl font-medium mb-4">Jadwal Mata Kuliah</h1>
    <div class="flex justify-between">
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
      <Button as-child v-if="matkulSemester.data.length != 0">
        <Link
          :href="`/schedules/add?semester=${selectedSemester?.semester}&tahun_1=${selectedSemester?.tahun_ajaran_pertama}&tahun_2=${selectedSemester?.tahun_ajaran_kedua}`">
        <Plus class="size-5 mr-1" /> Tambah Mata Kuliah
        </Link>
      </Button>
    </div>
    <div class="mt-3 flex items-center gap-2">
      <!-- <Label>Tampil : </Label> -->
      <Select class="bg-white" v-model="showQuantity">
        <SelectTrigger class="w-32">
          <SelectValue placeholder="6" />
        </SelectTrigger>
        <SelectContent>
          <SelectGroup>
            <SelectItem :value="6">6</SelectItem>
            <SelectItem :value="15">15</SelectItem>
            <SelectItem :value="25">25</SelectItem>
            <SelectItem :value="50">50</SelectItem>
            <SelectItem :value="'all'">Semua</SelectItem>
          </SelectGroup>
        </SelectContent>
      </Select>
      <Dialog>
        <DialogTrigger>
          <!-- <Button variant="outline" class="w-26">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-4">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
            </svg>
            Filter
          </Button> -->
        </DialogTrigger>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Filter Data</DialogTitle>
          </DialogHeader>
          <form action="">
            <div>
              <Label for="">Semester</Label>
            </div>
            <DialogFooter>
              <Button>Filter</Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>
    </div>

    <div class="mt-20 text-center" v-if="matkulSemester.data.length == 0">
      <h2 class="text-2xl font-medium">Belum ada mata kuliah di semester ini</h2>
      <p class="text-lg mt-4 mb-6">Silahkan tambahkan mata kuliah untuk semester ini serta tentukan jadwalnya.</p>
      <Button size="lg" as-child>
        <Link
          :href="`/schedules/add?semester=${selectedSemester?.semester}&tahun_1=${selectedSemester?.tahun_ajaran_pertama}&tahun_2=${selectedSemester?.tahun_ajaran_kedua}`">
        <Plus class="size-5 mr-1" /> Tambah Mata Kuliah
        </Link>
      </Button>
    </div>

    <div class="mt-6 bg-white border rounded-lg" v-if="matkulSemester.data.length > 0">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead class="w-[100px]">Kode Mata Kuliah</TableHead>
            <TableHead>Mata Kuliah</TableHead>
            <TableHead>Semester</TableHead>
            <TableHead>Kelas</TableHead>
            <TableHead>Hari, Waktu</TableHead>
            <TableHead>Dosen Pengampu</TableHead>
            <TableHead></TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="matkul in matkulSemester.data" :key="matkul.id_matkul">
            <TableCell class="font-medium">{{ matkul.kode_matkul }}</TableCell>
            <TableCell>{{ matkul.nama_matkul }}</TableCell>
            <TableCell>{{ matkul.semester }}</TableCell>
            <TableCell>{{ matkul.kelas }}</TableCell>
            <TableCell v-if="matkul.jadwal">
              {{ namaHari[matkul.jadwal?.hari] }}, {{ matkul.jadwal?.jam_mulai.split(':').slice(0, 2).join(':') }} -
              {{
                matkul.jadwal?.jam_selesai.split(':').slice(0, 2).join(':') }}
            </TableCell>
            <TableCell v-else class="text-accent-red">
              Belum ditentukan
            </TableCell>
            <TableCell>{{ matkul.dosen?.nama }}</TableCell>
            <TableCell>
              <Popover @update:open="console.log(this)">
                <PopoverTrigger>
                  <Button variant="ghost">
                    <Ellipsis />
                  </Button>
                </PopoverTrigger>
                <PopoverContent align="end" class="w-50 p-0">
                  <Button as-child class="w-full justify-start" variant="ghost" size="lg">
                    <Link :href="`/schedules/${matkul.id_matkul}`">
                    <ExternalLink class="size-4 mr-1.5" />
                    Detail Mata Kuliah
                    </Link>
                  </Button>
                  <PopoverClose class="w-full">
                    <Button class="!w-full justify-start" variant="ghost" size="lg"
                      @click="openFormJadwal(matkul.id_matkul); $emit('focusOutside')">
                      <Clock class="size-4 mr-1.5" />
                      Tentukan Jadwal
                    </Button>
                  </PopoverClose>
                </PopoverContent>
              </Popover>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
      <Pagination v-slot="{ page }" :items-per-page="matkulSemester.meta.per_page" :total="matkulSemester.meta.total"
        :default-page="matkulSemester.meta.current_page" class="ml-auto justify-end my-4">
        <PaginationContent v-slot="{ items }">
          <PaginationPrevious />

          <template v-for="(item, index) in items" :key="index">
            <PaginationItem v-if="item.type === 'page'" :value="item.value" :is-active="item.value === page" as-child>
              <Link :href="matkulSemester.meta.links[index + 1].url">
              {{ item.value }}
              </Link>
            </PaginationItem>
          </template>

          <PaginationEllipsis :index="4" />

          <PaginationNext />
        </PaginationContent>
      </Pagination>
    </div>

    <Dialog :open="!!editedJadwal" @update:open="opened => opened ? '' : formJadwalClosed()">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Ubah Jadwal</DialogTitle>
        </DialogHeader>
        <div class="grid grid-cols-2 gap-3 mt-3">
          <div>
            <Label for="hari" class="mb-2">Hari</Label>
            <Select v-model="formJadwal.hari">
              <SelectTrigger class="w-full" id="hari">
                <SelectValue placeholder="Pilih Hari" />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectItem :value="i + 1" v-for="(day, i) in namaHari.slice(1, 6)" :key="i">
                    {{ day }}
                  </SelectItem>
                </SelectGroup>
              </SelectContent>
            </Select>
            <InputError class="mt-1" :message="formJadwal.errors.hari" />
          </div>
          <div>
            <Label for="ruangan" class="mb-2">Ruangan</Label>
            <Combobox by="label" id="ruangan" class="border border-input shadow-xs rounded-md"
              v-model="formJadwal.ruangan">
              <ComboboxAnchor>
                <div class="relative w-full">
                  <ComboboxInput :display-value="(val) => val?.nama_ruangan ?? ''" placeholder="Pilih Ruangan..." />
                </div>
              </ComboboxAnchor>
              <ComboboxList>
                <ComboboxEmpty>
                  Ruangan tidak ditemukan.
                </ComboboxEmpty>

                <ComboboxGroup>
                  <ComboboxItem v-for="ruangan in listRuangan" :key="ruangan.id_ruangan" :value="ruangan">
                    {{ ruangan.nama_ruangan }}
                    <ComboboxItemIndicator v-if="ruangan == formJadwal.ruangan">
                      <Check :class="cn('ml-auto h-4 w-4')" />
                    </ComboboxItemIndicator>
                  </ComboboxItem>
                </ComboboxGroup>
              </ComboboxList>
            </Combobox>
            <InputError class="mt-1" :message="formJadwal.errors.ruangan" />
          </div>
          <div>
            <Label for="jam_mulai" class="mb-2">Jam Mulai</Label>
            <Input type="time" id="jam_mulai" v-model="formJadwal.jam_mulai" />
            <InputError class="mt-1" :message="formJadwal.errors.jam_mulai" />
          </div>
          <div>
            <Label for="jam_selesai" class="mb-2">Jam Selesai</Label>
            <Input type="time" id="jam_selesai" v-model="formJadwal.jam_selesai" />
            <InputError class="mt-1" :message="formJadwal.errors.jam_selesai" />
          </div>
        </div>
        <DialogFooter>
          <Button @click="formJadwal.transform(data => ({
            ...data,
            id_ruangan: formJadwal.ruangan?.id_ruangan,
            semester: semesterIni.semester,
            tahun_ajaran_pertama: semesterIni.tahun_ajaran_pertama,
            tahun_ajaran_kedua: semesterIni.tahun_ajaran_kedua,
          })).post(`/schedules/${editedJadwal}/jadwal`, {
            onSuccess() {
              formJadwalClosed()
              toast.success('Jadwal Mata Kuliah', { description: 'Jadwal mata kuliah berhasil diubah' })
              // router.reload()
            },
            onError(err) {
              if (err.jadwal) {
                formJadwalClosed()
                toast.error('Jadwal mata kuliah gagal diubah', { description: 'Terdapat jadwal lain di waktu tersebut' })
              }
            }
          })">
            Simpan Jadwal
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>