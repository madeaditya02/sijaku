<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import Button from '@/components/ui/button/Button.vue';
import Calendar from '@/components/ui/calendar/Calendar.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Popover from '@/components/ui/popover/Popover.vue';
import PopoverContent from '@/components/ui/popover/PopoverContent.vue';
import PopoverTrigger from '@/components/ui/popover/PopoverTrigger.vue';
import RadioGroup from '@/components/ui/radio-group/RadioGroup.vue';
import RadioGroupItem from '@/components/ui/radio-group/RadioGroupItem.vue';
import Select from '@/components/ui/select/Select.vue';
import SelectContent from '@/components/ui/select/SelectContent.vue';
import SelectGroup from '@/components/ui/select/SelectGroup.vue';
import SelectItem from '@/components/ui/select/SelectItem.vue';
import SelectTrigger from '@/components/ui/select/SelectTrigger.vue';
import SelectValue from '@/components/ui/select/SelectValue.vue';

import AppLayout from '@/layouts/AppLayout.vue';
import { cn } from '@/lib/utils';
import { useForm } from '@inertiajs/vue3';
import {
  DateFormatter,
  type DateValue,
  getLocalTimeZone,
} from '@internationalized/date'
import { CalendarIcon } from 'lucide-vue-next'
import moment from 'moment';
import { ref, watch } from 'vue';

const df = new DateFormatter('id-ID', {
  dateStyle: 'long',
})
const form = useForm({
  nama: '',
  nim: '',
  angkatan: (new Date()).getFullYear(),
  nomor_telpon: '',
  tempat_lahir: '',
  tanggal_lahir: '',
  jenis_kelamin: '',
  agama: '',
})
const tanggal_lahir = ref<DateValue>()
watch(tanggal_lahir, val => form.tanggal_lahir = moment(val?.toDate('GMT')).format('YYYY-MM-DD'))
</script>
<template>
  <AppLayout>
    <h1 class="text-3xl font-medium">Tambah Mahasiswa</h1>
    <form @submit.prevent="form.post('/students')" class="mt-8 grid grid-cols-6 gap-6">
      <div class="col-span-6 sm:col-span-3 md:col-span-2">
        <Label for="nim" class="mb-2">NIM</Label>
        <Input id="nim" v-model="form.nim" placeholder="Masukkan NIM" />
        <InputError :message="form.errors.nim" class="mt-1" />
      </div>
      <div class="col-span-6 sm:col-span-3 md:col-span-2">
        <Label for="nama" class="mb-2">Nama</Label>
        <Input id="nama" v-model="form.nama" placeholder="Masukkan nama" />
        <InputError :message="form.errors.nama" class="mt-1" />
      </div>
      <div class="col-span-6 sm:col-span-3 md:col-span-2">
        <Label for="angkatan" class="mb-2">Tahun Angkatan</Label>
        <Input id="angkatan" type="number" v-model="form.angkatan" placeholder="Masukkan angkatan" />
        <InputError :message="form.errors.angkatan" class="mt-1" />
      </div>
      <div class="col-span-6 sm:col-span-3 md:col-span-2">
        <Label for="nomor_telepon" class="mb-2">No. Telepon</Label>
        <Input id="nomor_telepon" v-model="form.nomor_telpon" placeholder="Masukkan nomor telepon" />
        <InputError :message="form.errors.nomor_telpon" class="mt-1" />
      </div>
      <div class="col-span-6 sm:col-span-3 md:col-span-2">
        <Label for="tempat_lahir" class="mb-2">Tempat Lahir</Label>
        <Input id="tempat_lahir" v-model="form.tempat_lahir" placeholder="Masukkan tempat lahir" />
        <InputError :message="form.errors.tempat_lahir" class="mt-1" />
      </div>
      <div class="col-span-6 sm:col-span-3 md:col-span-2">
        <Label for="tanggal_lahir" class="mb-2">Tanggal Lahir</Label>
        <Popover>
          <PopoverTrigger as-child>
            <Button variant="outline" :class="cn(
              'w-full justify-start text-left font-normal',
              !tanggal_lahir && 'text-muted-foreground',
            )">
              <CalendarIcon class="mr-2 h-4 w-4" />
              {{ tanggal_lahir ? df.format(tanggal_lahir.toDate(getLocalTimeZone())) : "Pilih tanggal lahir" }}
            </Button>
          </PopoverTrigger>
          <PopoverContent class="w-auto p-0">
            <Calendar v-model="tanggal_lahir" initial-focus />
          </PopoverContent>
        </Popover>
        <InputError :message="form.errors.tanggal_lahir" class="mt-1" />
      </div>
      <div class="col-span-6 sm:col-span-3 md:col-span-2 flex flex-col">
        <Label for="jenis_kelamin" class="mb-2">Jenis Kelamin</Label>
        <RadioGroup orientation="horizontal" class="flex items-center gap-x-2 h-full" v-model="form.jenis_kelamin">
          <div class="flex items-center space-x-2">
            <RadioGroupItem id="r1" value="Laki-laki" />
            <Label for="r1" class="font-normal">Laki-laki</Label>
          </div>
          <div class="flex items-center space-x-2">
            <RadioGroupItem id="r2" value="Perempuan" />
            <Label for="r2" class="font-normal">Perempuan</Label>
          </div>
        </RadioGroup>
        <InputError :message="form.errors.jenis_kelamin" class="mt-1" />
      </div>
      <div class="col-span-6 sm:col-span-3 md:col-span-2">
        <Label for="agama" class="mb-2">Agama</Label>
        <Select class="bg-white" v-model="form.agama">
          <SelectTrigger class="w-full">
            <SelectValue placeholder="Pilih Agama" />
          </SelectTrigger>
          <SelectContent>
            <SelectGroup>
              <SelectItem value="Hindu">Hindu</SelectItem>
              <SelectItem value="Islam">Islam</SelectItem>
              <SelectItem value="Kristen Protestan">Kristen Protestan</SelectItem>
              <SelectItem value="Katolik">Katolik</SelectItem>
              <SelectItem value="Buddha">Buddha</SelectItem>
              <SelectItem value="Konghuchu">Konghuchu</SelectItem>
            </SelectGroup>
          </SelectContent>
        </Select>
        <InputError :message="form.errors.agama" class="mt-1" />
      </div>
      <div class="col-span-full">
        <Button>Simpan</Button>
      </div>
    </form>
  </AppLayout>
</template>