<template>
  <div class="min-h-screen bg-[#F8FAFC]">
    <LayoutSidebar :menu-items="menuItems" user-role="Kepala Sekolah" />
    <div :class="['transition-all duration-300 ease-in-out', isSidebarOpen ? 'lg:pl-64' : 'lg:pl-20']">
      <LayoutHeader />
      <main class="p-4 sm:p-6 lg:p-8 mt-16 max-w-[1600px] mx-auto">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { useSidebar } from '~/composables/useSidebar'

const { isSidebarOpen } = useSidebar()

const menuItems = [
  {
    name: 'Dashboard',
    icon: 'home',
    path: '/kepsek/dashboard'
  },
  {
    name: 'Wali Kelas',
    icon: 'users',
    path: '/kepsek/wali-kelas'
  },
  {
    name: 'Kasus Siswa',
    icon: 'exclamation-triangle',
    path: '/kepsek/kasus-siswa'
  },
  {
    name: 'Kasus Guru',
    icon: 'user-minus',
    path: '/kepsek/kasus-guru'
  },
  {
    name: 'Supervisi Guru',
    icon: 'clipboard-document-check',
    path: '/kepsek/supervisi'
  }
]
</script>
