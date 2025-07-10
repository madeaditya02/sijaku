<script setup lang="ts">
import Label from '@/components/ui/label/Label.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { Checkbox } from '@/components/ui/checkbox'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover/'
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { Combobox, ComboboxAnchor, ComboboxEmpty, ComboboxGroup, ComboboxInput, ComboboxItem, ComboboxItemIndicator, ComboboxList } from '@/components/ui/combobox/'
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger
} from '@/components/ui/tooltip'
import { Check, Info, Search } from 'lucide-vue-next'
import { Dosen } from '@/types/model';
import InputError from '@/components/InputError.vue';
interface Semester {
  semester: string,
  tahun_ajaran_pertama: number,
  tahun_ajaran_kedua: number,
}
interface Matkul {
  kode: string,
  nama_matakuliah: string,
  semester: number,
}
const props = defineProps<{
  semester: Semester,
  mata_kuliah: Matkul[],
  listDosen: Dosen[]
}>()

const form = useForm<{
  semester: string,
  tahun_ajaran_pertama: number,
  tahun_ajaran_kedua: number,
  mata_kuliah: any[],
  dosen: string,
  kelas: number | undefined,
  kuota: number | undefined
}>({
  mata_kuliah: [],
  dosen: '',
  ...props.semester,
  kelas: undefined,
  kuota: undefined
})
</script>
<template>
  <AppLayout>
    <h1 class="text-2xl font-medium mb-4">Tambah Mata Kuliah Tawar</h1>
    <form class="grid grid-cols-12 gap-6 p-4 bg-white rounded-lg border border-stroke-grey"
      @submit.prevent="form.post('/schedules/add')">
      <div class="col-span-6">
        <div class="flex gap-2 items-center">
          <Label for="mata_kuliah">Mata Kuliah</Label>
          <TooltipProvider>
            <Tooltip>
              <TooltipTrigger>
                <Info class="size-3.5" />
              </TooltipTrigger>
              <TooltipContent class="max-w-xs">
                <p>Jika memilih lebih dari 1 mata kuliah, data dosen dan jumlah kelas akan berlaku sama ke semua mata
                  kuliah yang dipilih.</p>
              </TooltipContent>
            </Tooltip>
          </TooltipProvider>
        </div>
        <Popover class="w-full">
          <PopoverTrigger as-child>
            <Button variant="outline" class="w-full mt-2 justify-start font-normal">
              Pilih Mata Kuliah{{ form.mata_kuliah.length > 0 ? ` (${form.mata_kuliah.length})` : '' }}
            </Button>
          </PopoverTrigger>
          <PopoverContent align="start">
            <div class="flex gap-2 items-center mt-3 first:mt-0" v-for="matkul in mata_kuliah" :key="matkul.kode">
              <Checkbox :id="matkul.kode" :value="matkul.kode"
                :model-value="!!form.mata_kuliah.find(m => m.kode == matkul.kode)"
                @update:model-value="value => value ? form.mata_kuliah.push(matkul) : form.mata_kuliah = form.mata_kuliah.filter(m => m.kode != matkul.kode)" />
              <Label :for="matkul.kode">{{ matkul.kode }} - {{ matkul.nama_matakuliah }}</Label>
            </div>
          </PopoverContent>
        </Popover>
        <InputError class="mt-1" :message="form.errors.mata_kuliah" />
      </div>
      <div class="col-span-6">
        <Label for="mata_kuliah" class="mb-2">Dosen Pengampu</Label>
        <Combobox by="label" class="w-full left-0 right-0">
          <ComboboxAnchor class="w-full">
            <div class="relative w-full">
              <ComboboxInput class="pl-4 w-full" placeholder="Pilih dosen pengampu"
                :display-value="() => listDosen.find(d => d.nip == form.dosen)?.nama ?? ''" />
              <span class="absolute start-0 inset-y-0 flex items-center justify-center px-3">
                <Search class="size-4 text-muted-foreground" />
              </span>
            </div>
          </ComboboxAnchor>
          <ComboboxList class="w-full" align="start">
            <ComboboxEmpty class="w-full">
              Dosen tidak ditemukan.
            </ComboboxEmpty>

            <ComboboxGroup class="w-full">
              <ComboboxItem @select="() => form.dosen = dosen.nip" v-for="dosen in listDosen" :key="dosen.nip"
                :value="dosen.nip" class="w-full">
                {{ dosen.nama }}
                <ComboboxItemIndicator>
                  <Check class="ml-auto h-4 w-4" />
                </ComboboxItemIndicator>
              </ComboboxItem>
            </ComboboxGroup>
          </ComboboxList>
        </Combobox>
        <InputError class="mt-1" :message="form.errors.dosen" />
      </div>
      <div class="col-span-6">
        <Label for="mata_kuliah" class="mb-2">Semester</Label>
        <Select v-model="form.semester">
          <SelectTrigger class="w-full">
            <SelectValue placeholder="Pilih Semester" />
          </SelectTrigger>
          <SelectContent>
            <SelectGroup>
              <SelectItem value="Ganjil">
                Ganjil
              </SelectItem>
              <SelectItem value="Genap">
                Genap
              </SelectItem>
            </SelectGroup>
          </SelectContent>
        </Select>
        <InputError class="mt-1" :message="form.errors.semester" />
      </div>
      <div class="col-span-6">
        <Label for="mata_kuliah" class="mb-2">Tahun Ajaran</Label>
        <div class="grid grid-cols-[auto_max-content_auto] items-center gap-4">
          <div>
            <Input type="number" v-model="form.tahun_ajaran_pertama" />
            <InputError class="mt-1" :message="form.errors.tahun_ajaran_pertama" />
          </div>
          <div>/</div>
          <div>
            <Input type="number" v-model="form.tahun_ajaran_kedua" />
            <InputError class="mt-1" :message="form.errors.tahun_ajaran_kedua" />
          </div>
        </div>
      </div>
      <div class="col-span-6">
        <Label for="mata_kuliah" class="mb-2">Jumlah Kelas</Label>
        <Input type="number" v-model="form.kelas" />
        <InputError class="mt-1" :message="form.errors.kelas" />
      </div>
      <div class="col-span-6">
        <Label for="kuota" class="mb-2">Kuota per Kelas</Label>
        <Input type="number" v-model="form.kuota" />
        <InputError class="mt-1" :message="form.errors.kuota" />
      </div>
      <div class="col-span-12">
        <Button>Tambah</Button>
      </div>
    </form>
  </AppLayout>
</template>