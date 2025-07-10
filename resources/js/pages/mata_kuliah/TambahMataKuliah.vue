<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Select from '@/components/ui/select/Select.vue';
import SelectContent from '@/components/ui/select/SelectContent.vue';
import SelectGroup from '@/components/ui/select/SelectGroup.vue';
import SelectItem from '@/components/ui/select/SelectItem.vue';
import SelectTrigger from '@/components/ui/select/SelectTrigger.vue';
import SelectValue from '@/components/ui/select/SelectValue.vue';

import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';


const form = useForm({
  nama_matakuliah: '',
  kode: '',
  semester: undefined as undefined | number,
  sks_tatap_muka: undefined as undefined | number,
  sks_praktikum: undefined as undefined | number,
  jenis_matakuliah: '',
})
</script>
<template>
  <AppLayout>
    <h1 class="text-3xl font-medium">Tambah Mata Kuliah</h1>
    <form @submit.prevent="form.post('/mata-kuliah')" class="mt-8 grid grid-cols-6 gap-6">
      <div class="col-span-6 sm:col-span-3 md:col-span-2">
        <Label for="kode" class="mb-2">Kode Mata Kuliah</Label>
        <Input id="kode" v-model="form.kode" placeholder="Masukkan kode mata kuliah" />
        <InputError :message="form.errors.kode" class="mt-1" />
      </div>
      <div class="col-span-6 sm:col-span-3 md:col-span-2">
        <Label for="nama_matakuliah" class="mb-2">Nama Mata Kuliah</Label>
        <Input id="nama_matakuliah" v-model="form.nama_matakuliah" placeholder="Masukkan nama mata kuliah" />
        <InputError :message="form.errors.nama_matakuliah" class="mt-1" />
      </div>
      <div class="col-span-6 sm:col-span-3 md:col-span-2">
        <Label for="semester" class="mb-2">Semester</Label>
        <Input id="semester" type="number" v-model="form.semester" placeholder="Masukkan semester" />
        <InputError :message="form.errors.semester" class="mt-1" />
      </div>
      <div class="col-span-6 sm:col-span-3 md:col-span-2">
        <Label for="sks_tatap_muka" class="mb-2">Jumlah SKS Tatap Muka</Label>
        <Input id="sks_tatap_muka" type="number" v-model="form.sks_tatap_muka"
          placeholder="Masukkan jumlah sks tatap muka" />
        <InputError :message="form.errors.sks_tatap_muka" class="mt-1" />
      </div>
      <div class="col-span-6 sm:col-span-3 md:col-span-2">
        <Label for="sks_praktikum" class="mb-2">Jumlah SKS Praktikum</Label>
        <Input id="sks_praktikum" type="number" v-model="form.sks_praktikum"
          placeholder="Masukkan jumlah sks praktikum" />
        <InputError :message="form.errors.sks_praktikum" class="mt-1" />
      </div>
      <div class="col-span-6 sm:col-span-3 md:col-span-2">
        <Label for="jenis_matkul" class="mb-2">Jenis Mata Kuliah</Label>
        <Select class="bg-white" id="jenis_matkul" v-model="form.jenis_matakuliah">
          <SelectTrigger class="w-full">
            <SelectValue placeholder="Pilih jenis mata kuliah" />
          </SelectTrigger>
          <SelectContent>
            <SelectGroup>
              <SelectItem value="Wajib">Wajib</SelectItem>
              <SelectItem value="Wajib Peminatan">Wajib Peminatan</SelectItem>
              <SelectItem value="Pilihan">Pilihan</SelectItem>
            </SelectGroup>
          </SelectContent>
        </Select>
        <InputError :message="form.errors.jenis_matakuliah" class="mt-1" />
      </div>
      <div class="col-span-full">
        <Button>Simpan</Button>
      </div>
    </form>
  </AppLayout>
</template>