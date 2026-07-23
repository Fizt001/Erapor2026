<template>
  <div class="min-h-screen bg-slate-50 flex text-slate-800 text-sm print:bg-white print:block print:min-h-0">
    <VisiMisiDialog ref="visiMisiDialog" />
    <!-- Sidebar (Desktop Only) -->
    <aside class="group bg-slate-900 text-white flex-shrink-0 min-h-screen hidden lg:flex flex-col fixed lg:static z-50 transition-all duration-300 ease-in-out overflow-x-hidden print:hidden" :class="[sidebarOpen ? 'w-64' : 'lg:w-[72px] lg:hover:w-64']">
      <div class="h-14 flex items-center pl-5 pr-4 bg-slate-950 font-bold text-base tracking-wider border-b border-slate-800 whitespace-nowrap overflow-hidden">
        <img v-if="sekolah?.logo" :src="sekolah.logo" alt="Logo" class="h-8 w-8 object-contain lg:mr-3 shrink-0 cursor-pointer hover:scale-110 transition-transform" @click="visiMisiDialog?.open()" />
        <span v-else class="text-amber-500 mr-1 text-xl shrink-0 cursor-pointer hover:scale-110 transition-transform" @click="visiMisiDialog?.open()">e</span>
        <span class="opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300 cursor-pointer" @click="visiMisiDialog?.open()">
          <span v-if="sekolah?.logo" class="ml-1">e-Rapor</span>
          <span v-else>-Rapor</span>
        </span>
      </div>
      <div class="p-3">
        <nav class="space-y-1 h-[calc(100vh-140px)] overflow-y-auto custom-scrollbar pr-2">
          <template v-for="(menu, idx) in kurikulumMenus" :key="idx">
            <div v-if="menu.divider" class="pt-4 pb-1 px-3 text-[10px] font-bold text-amber-500 uppercase tracking-widest whitespace-nowrap opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300">{{ menu.dividerLabel }}</div>
            <NuxtLink v-else :to="menu.path" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-md text-slate-300 hover:bg-slate-800 hover:text-white transition-colors whitespace-nowrap" active-class="bg-amber-600 text-white shadow">
              <span class="mr-3 text-lg flex items-center justify-center" v-html="getSvgIcon(menu.icon)"></span>
              <span class="opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300">{{ menu.name }}</span>
            </NuxtLink>
          </template>
        </nav>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden print:h-auto print:overflow-visible print:block">
      <header class="relative h-14 bg-white flex items-center justify-between px-4 sm:px-6 z-[60] shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)] flex-shrink-0 print:hidden">
        <div class="flex items-center">
          <div class="lg:hidden flex items-center gap-2 cursor-pointer hover:scale-105 transition-transform" @click="visiMisiDialog?.open()">
            <img v-if="sekolah?.logo" :src="sekolah.logo" alt="Logo" class="h-7 w-7 object-contain" />
            <span v-else class="text-amber-600 font-black text-lg">e</span>
            <span class="font-black text-slate-700 text-sm">e-Rapor <span class="text-amber-600">Kurikulum</span></span>
          </div>
          <h2 class="hidden lg:block text-base font-bold text-slate-800 ml-3 border-l-2 border-amber-500 pl-3 py-1 uppercase tracking-wider">{{ route.meta.title || 'Kurikulum Workspace' }}</h2>
        </div>
        <div class="flex-1 lg:flex-none flex justify-end items-center space-x-4">
          <!-- Active Year Siren Indicator -->
          <div v-if="tahunAjaranAktif" class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 border border-slate-200/80 rounded-full shadow-inner no-print hover:scale-105 transition-all select-none">
            <span class="relative flex h-2.5 w-2.5">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]"></span>
            </span>
            <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest leading-none">TA. {{ tahunAjaranAktif.tahun }}</span>
          </div>

          <div class="relative">
            <button @click="profileDropdownOpen = !profileDropdownOpen" class="flex items-center space-x-3 text-right focus:outline-none bg-slate-50 hover:bg-slate-100 p-1.5 pl-3 rounded-full border border-slate-200 transition-all">
              <div class="hidden sm:block min-w-0 pr-2">
                <p class="text-[13px] font-bold text-slate-700 truncate leading-tight">{{ userProfile?.name || 'Waka Kurikulum' }}</p>
                <p class="text-[10px] text-slate-500 truncate uppercase tracking-wider">{{ userProfile?.email || 'kurikulum@erapor.com' }}</p>
              </div>
              <div class="h-9 w-9 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white font-black shadow-md text-sm border-2 border-white shrink-0">{{ userInitials }}</div>
            </button>
            <div v-show="profileDropdownOpen" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.1)] py-2 z-[60] origin-top-right overflow-hidden flex flex-col">
              <div class="px-4 py-2 border-b border-slate-100 mb-1">
                <p class="text-[13px] font-bold text-slate-700 truncate">{{ userProfile?.name || 'Waka Kurikulum' }}</p>
                <p class="text-[10px] text-slate-500 truncate">{{ userProfile?.email || 'kurikulum@erapor.com' }}</p>
              </div>
              <button @click="router.push('/kurikulum/profil'); profileDropdownOpen = false" type="button" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-700 transition-colors w-full text-left">
                <span class="mr-2">👤</span> Profil Saya
              </button>
              <button @click="handleLogout" type="button" class="flex items-center px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 transition-colors w-full text-left">
                <span class="mr-2">🚪</span> Logout
              </button>
            </div>
            <div v-if="profileDropdownOpen" @click="profileDropdownOpen = false" class="fixed inset-0 z-40 bg-transparent"></div>
          </div>
        </div>
      </header>
      <main class="flex-1 overflow-y-auto p-4 sm:p-5 bg-slate-50 relative print:p-0 print:bg-white print:overflow-visible print:block pb-20 lg:pb-5">
        <NuxtPage />
        <div ref="footerRef" :class="showFooter ? 'mt-10 pt-4 pb-4 border-t border-slate-200 opacity-100' : 'h-0 opacity-0 overflow-hidden'" class="text-center print:hidden transition-all duration-1000">
          <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Created by <span class="text-amber-600">SMK-Yatindo</span></p>
        </div>
      </main>
    </div>

    <!-- MOBILE BOTTOM NAV BAR -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-slate-100 shadow-[0_-10px_20px_-10px_rgba(0,0,0,0.05)] print:hidden">
      <div class="flex items-stretch h-16">
        <NuxtLink to="/kurikulum/dashboard" class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors" :class="route.path === '/kurikulum/dashboard' ? 'text-slate-800' : 'text-slate-400'">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" :class="route.path === '/kurikulum/dashboard' ? 'stroke-[2.5px] text-amber-500' : 'stroke-[2px]'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
          <span class="text-[10px] font-bold" :class="route.path === '/kurikulum/dashboard' ? 'text-slate-800' : ''">Home</span>
        </NuxtLink>
        <button @click="openDrawer('persiapan')" class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors" :class="activeDrawer === 'persiapan' && drawerOpen ? 'text-slate-800' : 'text-slate-400'">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" :class="activeDrawer === 'persiapan' && drawerOpen ? 'stroke-[2.5px] text-amber-500' : 'stroke-[2px]'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
          <span class="text-[10px] font-bold" :class="activeDrawer === 'persiapan' && drawerOpen ? 'text-slate-800' : ''">Persiapan</span>
        </button>
        <button @click="openDrawer('tugas')" class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors" :class="activeDrawer === 'tugas' && drawerOpen ? 'text-slate-800' : 'text-slate-400'">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" :class="activeDrawer === 'tugas' && drawerOpen ? 'stroke-[2.5px] text-amber-500' : 'stroke-[2px]'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>
          <span class="text-[10px] font-bold" :class="activeDrawer === 'tugas' && drawerOpen ? 'text-slate-800' : ''">Tugas</span>
        </button>
        <button @click="openDrawer('standar')" class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors" :class="activeDrawer === 'standar' && drawerOpen ? 'text-slate-800' : 'text-slate-400'">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" :class="activeDrawer === 'standar' && drawerOpen ? 'stroke-[2.5px] text-amber-500' : 'stroke-[2px]'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 14l9-5-9-5-9 5 9 5z" /><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" /></svg>
          <span class="text-[10px] font-bold" :class="activeDrawer === 'standar' && drawerOpen ? 'text-slate-800' : ''">Standar</span>
        </button>
        <button @click="openDrawer('all')" class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors" :class="activeDrawer === 'all' && drawerOpen ? 'text-slate-800' : 'text-slate-400'">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" :class="activeDrawer === 'all' && drawerOpen ? 'stroke-[2.5px] text-amber-500' : 'stroke-[2px]'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
          <span class="text-[10px] font-bold" :class="activeDrawer === 'all' && drawerOpen ? 'text-slate-800' : ''">Menu</span>
        </button>
      </div>
    </nav>

    <!-- DRAWER OVERLAY -->
    <Transition name="drawer-overlay">
      <div v-if="drawerOpen" @click="closeDrawer" class="lg:hidden fixed inset-0 bg-black/50 z-[70] backdrop-blur-sm print:hidden"></div>
    </Transition>

    <!-- DRAWER PANEL -->
    <Transition name="drawer-panel">
      <div v-if="drawerOpen" class="lg:hidden fixed bottom-0 left-0 right-0 z-[80] bg-white rounded-t-3xl shadow-2xl print:hidden" style="max-height: 75vh;">
        <div class="flex justify-center pt-3 pb-2"><div class="w-10 h-1 bg-slate-300 rounded-full"></div></div>
        <div class="flex items-center justify-between px-6 pb-3 border-b border-slate-100">
          <h3 class="font-black text-slate-800 uppercase tracking-widest text-xs">{{ drawerTitle }}</h3>
          <button @click="closeDrawer" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        <div class="overflow-y-auto px-5 py-5" style="max-height: calc(75vh - 100px);">
          <div class="grid grid-cols-4 gap-y-5 gap-x-3">
            <template v-for="(menu, idx) in currentDrawerMenus" :key="'g-'+idx">
              <NuxtLink v-if="!menu.divider" :to="menu.path" @click="closeDrawer" class="flex flex-col items-center gap-1.5 active:scale-95 transition-transform">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-sm transition-all text-amber-600" :class="route.path === menu.path ? 'bg-amber-500 text-white shadow-amber-200 shadow-lg' : 'bg-slate-50 border border-slate-100'" v-html="getSvgIcon(menu.icon)"></div>
                <span class="text-[9px] font-bold text-center leading-tight w-full" :class="route.path === menu.path ? 'text-amber-700' : 'text-slate-500'">{{ menu.name }}</span>
              </NuxtLink>
            </template>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { kurikulumMenus } from '~/utils/menus'

const router = useRouter()
const route = useRoute()
const visiMisiDialog = ref(null)
const sidebarOpen = ref(false)
const profileDropdownOpen = ref(false)
const drawerOpen = ref(false)
const activeDrawer = ref(null)

const getSvgIcon = (emoji) => {
  const icons = {
    '📊': '<svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" /></svg>',
    '⏳': '<svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
    '📚': '<svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>',
    '📑': '<svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>',
    '⚙️': '<svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>',
    '🏃': '<svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>',
    '👨‍🏫': '<svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>',
    '👨‍👩‍👧‍👦': '<svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>',
    '🎯': '<svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>',
    '📝': '<svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>',
    '⚖️': '<svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" /></svg>',
  };
  return icons[emoji] || `<span class="text-xl">${emoji}</span>`;
}

const footerRef = ref(null)
const showFooter = ref(true)

onMounted(() => {
  if (footerRef.value) {
    const observer = new IntersectionObserver((entries) => {
      if (entries[0].isIntersecting && showFooter.value) {
        setTimeout(() => {
          showFooter.value = false
        }, 1000)
      }
    }, { threshold: 0.1 })
    observer.observe(footerRef.value)
  }
})

const drawerMenuGroups = {
  persiapan: {
    title: 'Persiapan & Struktur',
    menus: [
      { name: 'Periode & Titimangsa', path: '/kurikulum/titimangsa', icon: '⏳' },
      { name: 'Mata Pelajaran', path: '/kurikulum/mapel', icon: '📚' },
      { name: 'Struktur Umum', path: '/kurikulum/struktur-umum', icon: '📑' },
      { name: 'Struktur Kejuruan', path: '/kurikulum/struktur-kejuruan', icon: '⚙️' },
      { name: 'Ekstrakurikuler', path: '/kurikulum/ekskul', icon: '🏃' },
    ]
  },
  tugas: {
    title: 'Pembagian Tugas',
    menus: [
      { name: 'Plot Guru Mapel', path: '/kurikulum/pengampu', icon: '👨‍🏫' },
      { name: 'Wali Kelas', path: '/kurikulum/wali-kelas', icon: '👨‍👩‍👧‍👦' },
    ]
  },
  standar: {
    title: 'Standar & Referensi',
    menus: [
      { name: 'Standar Nilai (KKM)', path: '/kurikulum/kkm', icon: '🎯' },
      { name: 'Master Deskripsi', path: '/kurikulum/deskripsi', icon: '📝' },
      { name: 'Penanganan Kasus', path: '/kurikulum/penanganan', icon: '⚖️' },
    ]
  },
  all: {
    title: 'Semua Menu Kurikulum',
    menus: kurikulumMenus
  }
}

const drawerTitle = computed(() => activeDrawer.value ? drawerMenuGroups[activeDrawer.value]?.title : '')
const currentDrawerMenus = computed(() => activeDrawer.value ? drawerMenuGroups[activeDrawer.value]?.menus ?? [] : [])

const openDrawer = (group) => {
  if (drawerOpen.value && activeDrawer.value === group) { closeDrawer(); return }
  activeDrawer.value = group
  drawerOpen.value = true
}
const closeDrawer = () => {
  drawerOpen.value = false
  setTimeout(() => { activeDrawer.value = null }, 300)
}

watch(() => route.path, () => { closeDrawer() })
useHead({ style: [{ children: 'html { font-size: 80% !important; }' }] })

const { sekolah, fetchSekolah } = useSekolah()
const tahunAjaranAktif = ref(null)

const fetchTahunAjaranAktif = async () => {
  try {
    const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/public/stats')
    if (res.success && res.data?.tahun_aktif) {
      tahunAjaranAktif.value = res.data.tahun_aktif
    }
  } catch (e) {
    console.error('Failed to fetch active school year:', e)
  }
}

onMounted(() => { 
  fetchSekolah()
  fetchTahunAjaranAktif()
})

const userCookie = useCookie('user_profile')
const userProfile = computed(() => {
  if (!userCookie.value) return null
  return typeof userCookie.value === 'string' ? JSON.parse(userCookie.value) : userCookie.value
})
const userInitials = computed(() => {
  if (!userProfile.value?.name) return 'A'
  return userProfile.value.name.charAt(0).toUpperCase()
})
const handleLogout = async () => {
  try {
    const tokenCookie = useCookie('auth_token')
    if (tokenCookie.value) {
      await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/logout', {
        method: 'POST', headers: { 'Authorization': 'Bearer ' + tokenCookie.value }
      })
    }
  } catch (e) { console.error(e) }
  finally {
    useCookie('auth_token').value = null
    useCookie('user_profile').value = null
    router.push('/login')
  }
}
</script>

<style scoped>
.custom-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.custom-scrollbar::-webkit-scrollbar { display: none; }
.drawer-overlay-enter-active, .drawer-overlay-leave-active { transition: opacity 0.3s ease; }
.drawer-overlay-enter-from, .drawer-overlay-leave-to { opacity: 0; }
.drawer-panel-enter-active, .drawer-panel-leave-active { transition: transform 0.35s cubic-bezier(0.32, 0.72, 0, 1); }
.drawer-panel-enter-from, .drawer-panel-leave-to { transform: translateY(100%); }
</style>