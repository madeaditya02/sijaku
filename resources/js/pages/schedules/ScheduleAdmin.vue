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
import { MataKuliah, Ruangan, Semester } from '@/types/model';
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
  // DialogTrigger,
} from '@/components/ui/dialog'
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
  matkulSemester: MataKuliah[],
  listRuangan: Ruangan[]
}>()
const listSemester = ref(props.semester.map(smt => ({ ...smt, id: Object.values(smt).join('-') })))
const selectedSemesterId = ref(Object.values(props.semesterIni).join('-'))
const selectedSemester = computed(() => listSemester.value.find((smt) => smt.id == selectedSemesterId.value))
watch(selectedSemesterId, val => {
  const params = val.split('-')
  router.get(usePage().url, {
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
  const selectedMatkul = props.matkulSemester.find(m => m.id_matkul == id_matkul)?.jadwal
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

</script>
<template>
  <AppLayout>
    <h1 class="text-2xl font-medium mb-4">Jadwal Mata Kuliah</h1>
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
      <Button as-child v-if="matkulSemester.length != 0">
        <Link
          :href="`/schedules/add?semester=${selectedSemester?.semester}&tahun_1=${selectedSemester?.tahun_ajaran_pertama}&tahun_2=${selectedSemester?.tahun_ajaran_kedua}`">
        <Plus class="size-5 mr-1" /> Tambah Mata Kuliah
        </Link>
      </Button>
    </div>

    <div class="mt-20 text-center" v-if="matkulSemester.length == 0">
      <h2 class="text-2xl font-medium">Belum ada mata kuliah di semester ini</h2>
      <p class="text-lg mt-4 mb-6">Silahkan tambahkan mata kuliah untuk semester ini serta tentukan jadwalnya.</p>
      <Button size="lg" as-child>
        <Link
          :href="`/schedules/add?semester=${selectedSemester?.semester}&tahun_1=${selectedSemester?.tahun_ajaran_pertama}&tahun_2=${selectedSemester?.tahun_ajaran_kedua}`">
        <Plus class="size-5 mr-1" /> Tambah Mata Kuliah
        </Link>
      </Button>
    </div>

    <div class="mt-6 bg-white border rounded-lg" v-if="matkulSemester.length > 0">
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
          <TableRow v-for="matkul in matkulSemester" :key="matkul.id_matkul">
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
          <Button @click="formJadwal.transform(data => ({ ...data, id_ruangan: formJadwal.ruangan?.id_ruangan })).post(`/schedules/${editedJadwal}/jadwal`, {
            onSuccess() {
              formJadwalClosed()
              toast.success('Jadwal Mata Kuliah', { description: 'Jadwal mata kuliah berhasil diubah' })
              // router.reload()
            }
          })">
            Simpan Jadwal
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>