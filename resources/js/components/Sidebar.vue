<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import SidebarLink from './SidebarLink.vue';
import { computed, ref } from 'vue';
import ConfirmModal from './ConfirmModal.vue';

defineProps<{ show: boolean }>()
defineEmits(['close'])
const page = usePage<{ auth: { user: any } }>()
const username = computed(() => page.props.auth.user.mahasiswa ? page.props.auth.user.mahasiswa.nama : (page.props.auth.user.dosen ? page.props.auth.user.dosen.nama : page.props.auth.user.admin.nama))
const role = computed(() => page.props.auth.user.mahasiswa ? "Mahasiswa" : (page.props.auth.user.dosen ? "Dosen" : "Admin"))

const confirmLogout = ref(false)
</script>
<template>
  <div
    class="sidebar w-72 text-white bg-primary rounded-r-[3rem] fixed left-0 top-0 bottom-0 px-8 py-8 max-h-screen flex flex-col transition-all duration-200 z-20"
    :class="!show ? '-ml-72 lg:ml-0' : 'ml-0'">
    <div>
      <img :src="page.props.auth.user.profile_picture ?? '/user-2.png'" alt=""
        class="size-20 rounded-full mx-auto max-w-full">
      <h3 class="mt-6 mb-2 text-center font-medium">{{ username }}</h3>
      <p class="text-center font-medium">{{ role }}</p>
      <hr class="h-0.5 bg-white my-4">
    </div>
    <div class="mt-5 flex flex-col justify-between grow">
      <div>
        <SidebarLink menu="Dashboard" href="/" :active="page.url == '/' || page.url.startsWith('/?')" />
        <SidebarLink menu="Activity" href="/activities" v-if="role == 'Admin'"
          :active="page.url.startsWith('/activities')" />
        <SidebarLink menu="Schedule" href="/schedules"
          :active="(role == 'Dosen' ? page.url.startsWith('/activities') : false) || page.url.startsWith('/schedules')" />
        <SidebarLink menu="KRS" href="/krs" v-if="role == 'Mahasiswa'" :active="page.url.startsWith('/krs')" />
        <SidebarLink menu="Mata Kuliah" href="/mata-kuliah" v-if="role == 'Admin'"
          :active="page.url.startsWith('/mata-kuliah')" />
        <SidebarLink menu="Mahasiswa" href="/students" v-if="role == 'Admin'"
          :active="page.url.startsWith('/students')" />
        <SidebarLink menu="Dosen" href="/lecturers" v-if="role == 'Admin'"
          :active="page.url.startsWith('/lecturers')" />
      </div>
      <div class="h-full flex flex-col justify-end">
        <button class="inline-flex gap-3 items-center w-full cursor-pointer" @click="confirmLogout = true">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
          </svg>
          Log Out
        </button>
      </div>
    </div>
  </div>
  <div class="sidebar-overlay fixed inset-0 bg-[rgba(0,0,0,0.3)] z-20" :class="show ? 'block lg:hidden' : 'hidden'"
    @click="$emit('close')">
  </div>

  <ConfirmModal :open="confirmLogout" @update-open="opened => confirmLogout = opened" title="Logout"
    text="Anda yakin ingin logout?" confirm-button="Logout" @confirm="router.post('/logout')" />
</template>