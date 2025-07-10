<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import RadioGroup from '@/components/ui/radio-group/RadioGroup.vue';
import RadioGroupItem from '@/components/ui/radio-group/RadioGroupItem.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { DosenFull } from '@/types/model';
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
  dosen: DosenFull
}>()

const form = useForm({
  nama: props.dosen.nama,
  nip: props.dosen.nip,
  nomor_telpon: props.dosen.nomor_telpon,
  email: props.dosen.user.email,
  jenis_kelamin: props.dosen.jenis_kelamin,
})
</script>
<template>
  <AppLayout>
    <h1 class="text-3xl font-medium">Edit Dosen</h1>
    <form @submit.prevent="form.transform(data => ({ ...data, prevNIP: dosen.nip })).put(`/lecturers/${dosen.nip}`)"
      class="mt-8 grid grid-cols-6 gap-6">
      <div class="col-span-2">
        <Label for="nip" class="mb-2">NIP</Label>
        <Input id="nip" v-model="form.nip" placeholder="Masukkan NIP" />
        <InputError :message="form.errors.nip" class="mt-1" />
      </div>
      <div class="col-span-2">
        <Label for="nama" class="mb-2">Nama</Label>
        <Input id="nama" v-model="form.nama" placeholder="Masukkan nama" />
        <InputError :message="form.errors.nama" class="mt-1" />
      </div>
      <div class="col-span-2">
        <Label for="nomor_telepon" class="mb-2">No. Telepon</Label>
        <Input id="nomor_telepon" v-model="form.nomor_telpon" placeholder="Masukkan nomor telepon" />
        <InputError :message="form.errors.nomor_telpon" class="mt-1" />
      </div>
      <div class="col-span-2">
        <Label for="email" class="mb-2">Email</Label>
        <Input id="email" type="email" v-model="form.email" disabled placeholder="Masukkan email" />
        <InputError :message="form.errors.email" class="mt-1" />
      </div>
      <div class="col-span-2 flex flex-col">
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
      <div class="col-span-full">
        <Button>Simpan</Button>
      </div>
    </form>
  </AppLayout>
</template>