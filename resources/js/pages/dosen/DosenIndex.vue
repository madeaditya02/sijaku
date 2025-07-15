<script setup lang="ts">
import ConfirmModal from '@/components/ConfirmModal.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Pagination from '@/components/ui/pagination/Pagination.vue';
import PaginationContent from '@/components/ui/pagination/PaginationContent.vue';
import PaginationItem from '@/components/ui/pagination/PaginationItem.vue';
import PaginationNext from '@/components/ui/pagination/PaginationNext.vue';
import PaginationPrevious from '@/components/ui/pagination/PaginationPrevious.vue';
import Popover from '@/components/ui/popover/Popover.vue';
import PopoverContent from '@/components/ui/popover/PopoverContent.vue';
import PopoverTrigger from '@/components/ui/popover/PopoverTrigger.vue';
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
import { Dosen, Paginator } from '@/types/model';
import { Link, router, useForm } from '@inertiajs/vue3';
import { Ellipsis, Pencil, Search, Trash } from 'lucide-vue-next';
import { PopoverClose } from 'reka-ui';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

defineProps<{
  data: Paginator<Dosen>
}>()
const params = new URLSearchParams(window.location.search)

const formFilter = useForm({
  search: params.get('search') ?? '',
  show: parseInt(params.get('show') ?? '10'),
})

const confirmDelete = ref<string | null>()
</script>
<template>
  <AppLayout>
    <h1 class="text-3xl font-semibold">Daftar Dosen</h1>
    <div class="flex justify-between items-center mt-8">
      <div class="flex items-center gap-3">
        <Select class="bg-white" v-model="formFilter.show" @update:model-value="formFilter.get('')">
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
        <form class="flex items-center gap-3" @submit.prevent="formFilter.get('')">
          <div class="relative w-full max-w-sm items-center">
            <Input id="search" type="text" placeholder="Search..." class="pl-10" v-model="formFilter.search" />
            <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
              <Search class="size-4 text-muted-foreground" />
            </span>
          </div>
          <Button variant="tertiary">Search</Button>
        </form>
      </div>
      <div class="flex gap-4 flex-wrap">
        <Button as-child>
          <Link href="/lecturers/create">
          Tambah Dosen
          </Link>
        </Button>
        <Button as-child variant="tertiary">
          <a href="/lecturers/download">
            Print PDF
          </a>
        </Button>
      </div>
    </div>
    <div class="mt-6 bg-white border rounded-lg" v-if="data.data.length > 0">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>NIP</TableHead>
            <TableHead>Nama</TableHead>
            <TableHead>No. Telepon</TableHead>
            <TableHead></TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="dosen in data.data" :key="dosen.nip">
            <TableCell class="font-medium">{{ dosen.nip }}</TableCell>
            <TableCell>{{ dosen.nama }}</TableCell>
            <TableCell>{{ dosen.nomor_telepon }}</TableCell>
            <TableCell>
              <Popover @update:open="console.log(this)">
                <PopoverTrigger>
                  <Button variant="ghost">
                    <Ellipsis />
                  </Button>
                </PopoverTrigger>
                <PopoverContent align="end" class="w-50 p-0">
                  <Button as-child class="w-full justify-start" variant="ghost" size="lg">
                    <Link :href="`/lecturers/${dosen.nip}/edit`">
                    <Pencil class="size-4 mr-1.5" />
                    Edit
                    </Link>
                  </Button>
                  <PopoverClose class="w-full">
                    <Button class="!w-full justify-start" variant="ghost" size="lg" @click="confirmDelete = dosen.nip">
                      <Trash class="size-4 mr-1.5" />
                      Hapus
                    </Button>
                  </PopoverClose>
                </PopoverContent>
              </Popover>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
      <Pagination v-slot="{ page }" :items-per-page="data.meta.per_page" :total="data.meta.total"
        :default-page="data.meta.current_page" class="ml-auto justify-end my-4">
        <PaginationContent v-slot="{ items }">
          <PaginationPrevious />

          <template v-for="(item, index) in items" :key="index">
            <PaginationItem v-if="item.type === 'page'" :value="item.value" :is-active="item.value === page" as-child>
              <Link :href="data.meta.links[index + 1].url">
              {{ item.value }}
              </Link>
            </PaginationItem>
          </template>

          <!-- <PaginationEllipsis :index="4" /> -->

          <PaginationNext />
        </PaginationContent>
      </Pagination>
    </div>

    <ConfirmModal :open="!!confirmDelete" @update-open="opened => confirmDelete = (!opened ? null : confirmDelete)"
      title="Hapus Data Dosen" :text="`Anda yakin ingin menghapus data dosen dengan NIP ${confirmDelete}?`"
      confirm-button="Hapus" @confirm="router.delete(`/lecturers/${confirmDelete}`, {
        onSuccess: () => {
          toast.success('Data dosen berhasil dihapus')
          confirmDelete = null
        }
      })" />
  </AppLayout>
</template>