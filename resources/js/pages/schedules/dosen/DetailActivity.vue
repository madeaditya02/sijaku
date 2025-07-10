<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import Button from '@/components/ui/button/Button.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import DialogTrigger from '@/components/ui/dialog/DialogTrigger.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
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
import { Mahasiswa, Perkuliahan } from '@/types/model';
import { useForm } from '@inertiajs/vue3';
import moment from 'moment';
import { watch } from 'vue';

const props = defineProps<{
  kuliah: Perkuliahan,
  mahasiswa: Mahasiswa[]
}>()
const waktu_mulai = moment(props.kuliah.waktu_mulai)

const formKonfirmasi = useForm({
  status_kehadiran: props.kuliah.status == 'Offline' || props.kuliah.status == 'Online' ? 'Hadir' : props.kuliah.status,
  start_date: waktu_mulai.format('YYYY-MM-DD'),
  start_time: waktu_mulai.format('hh:mm'),
  status_kegiatan: props.kuliah.status == 'Offline' || props.kuliah.status == 'Online' ? props.kuliah.status : ''
})
watch(() => formKonfirmasi.status_kehadiran, val => {
  if (val != 'Rescheduled') {
    formKonfirmasi.reset('start_date', 'start_time')
  }
})

</script>
<template>
  <AppLayout>
    <h1 class="text-2xl font-medium">Detail Schedule</h1>
    <div class="bg-white px-8 py-4 rounded-xl border border-[#bdbdbd] mt-4">
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-medium">Informasi Mata Kuliah</h2>
        <Dialog>
          <DialogTrigger>
            <Button variant="tertiary">List Mahasiswa</Button>
          </DialogTrigger>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Daftar Mahasiswa</DialogTitle>
            </DialogHeader>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>No.</TableHead>
                  <TableHead>NIM</TableHead>
                  <TableHead>Nama</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="(mhs, i) in mahasiswa" :key="mhs.nim">
                  <TableCell>{{ i + 1 }}</TableCell>
                  <TableCell>{{ mhs.nim }}</TableCell>
                  <TableCell>{{ mhs.nama }}</TableCell>
                </TableRow>
              </TableBody>
            </Table>
            <!-- <DialogFooter>
        Save changes
      </DialogFooter> -->
          </DialogContent>
        </Dialog>
      </div>
      <hr class="mt-3 mb-4">
      <form @submit.prevent="formKonfirmasi.post(`/activities/${kuliah.id_kuliah}/confirm`)"
        class="grid grid-cols-6 gap-6">
        <div class="col-span-2">
          <Label>Nama Mata Kuliah</Label>
          <Input disabled :default-value="kuliah.mata_kuliah.nama_matkul" class="mt-2" />
        </div>
        <div class="col-span-2">
          <Label>Kode Mata Kuliah</Label>
          <Input disabled :default-value="kuliah.mata_kuliah.id_matkul" class="mt-2" />
        </div>
        <div class="col-span-2">
          <Label>Kelas</Label>
          <Input disabled :default-value="kuliah.mata_kuliah.kelas" class="mt-2" />
        </div>
        <div class="col-span-2">
          <Label class="mb-2">Status Konfirmasi</Label>
          <Select class="w-full" v-model="formKonfirmasi.status_kehadiran">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Pilih Status" />
            </SelectTrigger>
            <SelectContent class="w-full">
              <SelectGroup>
                <SelectItem value="Hadir">Hadir</SelectItem>
                <SelectItem value="Rescheduled">Rescheduled</SelectItem>
                <SelectItem value="Batal">Batal</SelectItem>
                <SelectItem value="Pending">Pending</SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
          <InputError :message="formKonfirmasi.errors.status_kehadiran" />
        </div>
        <div class="col-span-2">
          <Label class="mb-2">Status Kegiatan</Label>
          <Select class="w-full" v-model="formKonfirmasi.status_kegiatan">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Pilih Offline/Online" />
            </SelectTrigger>
            <SelectContent class="w-full">
              <SelectGroup>
                <SelectItem value="Offline">Offline</SelectItem>
                <SelectItem value="Online">Online</SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
          <InputError :message="formKonfirmasi.errors.status_kegiatan" />
        </div>
        <div class="col-span-2">
          <Label>Ruangan</Label>
          <Input disabled :default-value="kuliah.ruangan" class="mt-2" />
        </div>
        <div class="col-span-2">
          <Label>{{ formKonfirmasi.status_kehadiran == 'Rescheduled' ? 'Tanggal Rescheduled' : 'Tanggal' }}</Label>
          <Input :disabled="formKonfirmasi.status_kehadiran != 'Rescheduled'" type="date"
            v-model="formKonfirmasi.start_date" class="mt-2" />
          <InputError :message="formKonfirmasi.errors.start_date" />
        </div>
        <div class="col-span-2">
          <Label>{{ formKonfirmasi.status_kehadiran == 'Rescheduled' ? 'Jam Rescheduled' : 'Waktu' }}</Label>
          <Input :disabled="formKonfirmasi.status_kehadiran != 'Rescheduled'" type="time"
            v-model="formKonfirmasi.start_time" class="mt-2" />
          <InputError :message="formKonfirmasi.errors.start_time" />
        </div>
        <div class="col-span-6">
          <Button>Simpan</Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>