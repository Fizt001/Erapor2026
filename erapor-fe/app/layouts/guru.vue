<template>
  <div class="min-h-screen bg-slate-50 flex text-slate-800 text-sm print:bg-white print:block print:min-h-0">
    <VisiMisiDialog ref="visiMisiDialog" />
    <!-- Sidebar (Desktop Only) -->
    <aside class="group bg-slate-900 text-white flex-shrink-0 min-h-screen hidden lg:flex flex-col fixed lg:static z-50 transition-all duration-300 ease-in-out overflow-x-hidden print:hidden" :class="[sidebarOpen ? 'w-64' : 'lg:w-[72px] lg:hover:w-64']">
      <div class="h-14 flex items-center pl-5 pr-4 bg-slate-950 font-bold text-base tracking-wider border-b border-slate-800 whitespace-nowrap overflow-hidden">
        <img v-if="sekolah?.logo" :src="sekolah.logo" alt="Logo" class="h-8 w-8 object-contain lg:mr-3 shrink-0 cursor-pointer hover:scale-110 transition-transform" @click="visiMisiDialog?.open()" />
        <span v-else class="text-sky-500 mr-1 text-xl shrink-0 cursor-pointer hover:scale-110 transition-transform" @click="visiMisiDialog?.open()">e</span>
        <span class="opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300 flex items-center cursor-pointer" @click="visiMisiDialog?.open()">
            <span v-if="sekolah?.logo" class="ml-1">e-Rapor</span>
            <span v-else>-Rapor</span>
            <span class="ml-2 text-[10px] bg-sky-500/20 text-sky-300 px-2 py-0.5 rounded border border-sky-500/30">GURU</span>
        </span>
      </div>
      
      <div class="p-3">
        <nav class="space-y-1 h-[calc(100vh-140px)] overflow-y-auto custom-scrollbar pr-2">
          <template v-for="(menu, idx) in guruMenus" :key="idx">
            <div v-if="menu.divider" class="pt-4 pb-1 px-3 text-[10px] font-bold text-sky-500 uppercase tracking-widest whitespace-nowrap opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300">{{ menu.dividerLabel }}</div>
            <NuxtLink v-else :to="menu.path" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-md text-slate-300 hover:bg-slate-800 hover:text-white transition-colors whitespace-nowrap" active-class="bg-sky-600 text-white shadow">
              <span class="mr-3 text-lg">{{ menu.icon }}</span>
              <span class="opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300">{{ menu.name }}</span>
            </NuxtLink>
          </template>
          
          <!-- PINTU DORAEMON WALAS (Desktop) -->
          <div class="pt-6 pb-2 px-3">
             <NuxtLink v-if="isWalas" to="/guru/walas/dashboard" class="flex flex-col items-center justify-center p-3 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-white shadow-lg shadow-amber-500/20 transition-all hover:-translate-y-1 group/door">
                 <span class="text-2xl mb-1 group-hover/door:scale-110 transition-transform">🚪</span>
                 <span class="text-[10px] font-black uppercase tracking-widest text-center opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">Masuk Role Walas</span>
             </NuxtLink>
             <div v-else class="flex flex-col items-center justify-center p-3 rounded-xl bg-slate-800 text-slate-500 cursor-not-allowed border border-slate-700/50">
                 <span class="text-2xl mb-1 grayscale opacity-50">🔒</span>
                 <span class="text-[10px] font-black uppercase tracking-widest text-center opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">Bukan Walas</span>
             </div>
          </div>
        </nav>
      </div>
    </aside>



    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden print:h-auto print:overflow-visible print:block">
      <!-- Navbar -->
      <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-[60] shadow-sm relative print:hidden shrink-0">
        <div class="flex items-center">
          <div class="lg:hidden flex items-center gap-2 cursor-pointer hover:scale-105 transition-transform" @click="visiMisiDialog?.open()">
            <img v-if="sekolah?.logo" :src="sekolah.logo" alt="Logo" class="h-7 w-7 object-contain" />
            <span v-else class="text-sky-600 font-black text-lg">e</span>
            <span class="font-black text-slate-700 text-sm">e-Rapor <span class="text-sky-600">Guru</span></span>
          </div>
          <h2 class="hidden lg:block text-base font-bold text-slate-800 ml-3 border-l-2 border-sky-500 pl-3 py-1 uppercase tracking-wider">{{ route.meta.title || 'Guru Workspace' }}</h2>
        </div>
        
        <div class="flex-1 lg:flex-none flex justify-end items-center space-x-4">
          <!-- Profile Dropdown in Navbar -->
          <div class="relative">
            <button @click="profileDropdownOpen = !profileDropdownOpen" class="flex items-center space-x-3 text-right focus:outline-none bg-slate-50 hover:bg-slate-100 p-1.5 pl-3 rounded-full border border-slate-200 transition-all">
              <div class="hidden sm:block min-w-0 pr-2">
                <p class="text-[13px] font-bold text-slate-700 truncate leading-tight">{{ userProfile?.name || 'Guru Pengampu' }}</p>
                <p class="text-[10px] text-slate-500 truncate uppercase tracking-wider">{{ userProfile?.email || 'guru@erapor.com' }}</p>
              </div>
              <div class="h-9 w-9 rounded-full bg-gradient-to-br from-sky-500 to-indigo-600 flex items-center justify-center text-white font-black shadow-md text-sm border-2 border-white shrink-0">
                {{ userInitials }}
              </div>
            </button>

            <!-- Dropdown Menu -->
            <div v-show="profileDropdownOpen" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-200 py-2 z-[60] origin-top-right overflow-hidden flex flex-col">
              <div class="px-4 py-2 border-b border-slate-100 mb-1 block">
                <p class="text-[13px] font-bold text-slate-700 truncate">{{ userProfile?.name || 'Guru Pengampu' }}</p>
                <p class="text-[10px] text-slate-500 truncate">{{ userProfile?.email || 'guru@erapor.com' }}</p>
              </div>
              <button @click="router.push('/guru/profil'); profileDropdownOpen = false" type="button" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-sky-50 hover:text-sky-700 transition-colors w-full text-left">
                <span class="mr-2">👤</span> Profil Saya
              </button>
              <button @click="handleLogout" type="button" class="flex items-center px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 transition-colors w-full text-left">
                <span class="mr-2">🚪</span> Logout
              </button>
            </div>
            
            <!-- Close on click outside -->
            <div v-if="profileDropdownOpen" @click="profileDropdownOpen = false" class="fixed inset-0 z-40 bg-transparent"></div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto p-4 sm:p-5 bg-slate-100 relative print:p-0 print:bg-white print:overflow-visible print:block">
        <NuxtPage />
        
        <!-- Footer Info -->
        <div class="mt-10 pt-4 border-t border-slate-200 text-center pb-4">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Created by <span class="text-sky-600">SMK-Yatindo</span></p>
        </div>
      </main>
    </div>

    <!-- MOBILE BOTTOM NAV BAR -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-slate-200 shadow-[0_-4px_20px_-4px_rgba(0,0,0,0.1)] print:hidden">
      <div class="flex items-stretch h-16">
        <NuxtLink to="/guru/dashboard" class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors" :class="route.path === '/guru/dashboard' ? 'text-sky-600' : 'text-slate-400'">
          <span class="text-xl leading-none">📊</span>
          <span class="text-[9px] font-black uppercase tracking-wider">Dashboard</span>
        </NuxtLink>
        <button @click="openDrawer('kbm')" class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors" :class="activeDrawer === 'kbm' && drawerOpen ? 'text-sky-600' : 'text-slate-400'">
          <span class="text-xl leading-none">📓</span>
          <span class="text-[9px] font-black uppercase tracking-wider">KBM</span>
        </button>
        <button @click="openDrawer('formatif')" class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors" :class="activeDrawer === 'formatif' && drawerOpen ? 'text-sky-600' : 'text-slate-400'">
          <span class="text-xl leading-none">📝</span>
          <span class="text-[9px] font-black uppercase tracking-wider">Formatif</span>
        </button>
        <button @click="openDrawer('sumatif')" class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors" :class="activeDrawer === 'sumatif' && drawerOpen ? 'text-sky-600' : 'text-slate-400'">
          <span class="text-xl leading-none">📋</span>
          <span class="text-[9px] font-black uppercase tracking-wider">Sumatif</span>
        </button>
        
        <NuxtLink v-if="isWalas" to="/guru/walas/dashboard" class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors text-amber-500 hover:text-amber-600 relative">
          <span class="absolute -top-2 right-2 flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500 border border-white"></span>
          </span>
          <span class="text-xl leading-none">🚪</span>
          <span class="text-[9px] font-black uppercase tracking-wider">Walas</span>
        </NuxtLink>
        <div v-else class="flex-1 flex flex-col items-center justify-center gap-1 text-slate-300 cursor-not-allowed">
          <span class="text-xl leading-none grayscale opacity-50">🔒</span>
          <span class="text-[9px] font-black uppercase tracking-wider">Walas</span>
        </div>
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
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl shadow-sm transition-all" :class="route.path === menu.path ? 'bg-sky-500 shadow-sky-200 shadow-lg' : 'bg-slate-100'">{{ menu.icon }}</div>
                <span class="text-[9px] font-bold text-center leading-tight w-full" :class="route.path === menu.path ? 'text-sky-700' : 'text-slate-500'">{{ menu.name }}</span>
              </NuxtLink>
            </template>
          </div>
        </div>
      </div>
    </Transition>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { guruMenus, walasMenus } from '~/utils/menus'

const router = useRouter()
const route = useRoute()
const visiMisiDialog = ref(null)
const sidebarOpen = ref(false)
const profileDropdownOpen = ref(false)

const { sekolah, fetchSekolah } = useSekolah()

// DRAWER LOGIC
const drawerOpen = ref(false)
const activeDrawer = ref(null)

const drawerTitle = computed(() => {
  if (activeDrawer.value === 'kbm') return 'Kegiatan KBM'
  if (activeDrawer.value === 'formatif') return 'Penilaian Formatif'
  if (activeDrawer.value === 'sumatif') return 'Penilaian Sumatif'
  return 'Menu'
})

// guruMenus indexing:
// 0: Dashboard
// 1: divider (KBM)
// 2: Absensi
// 3: Jurnal
// 4: divider (Formatif)
// 5: Master
// 6: Nilai
// 7: divider (Sumatif)
// 8: Nilai
// 9: Rekap

const currentDrawerMenus = computed(() => {
  let startIndex = 0
  let endIndex = guruMenus.length

  if (activeDrawer.value === 'kbm') {
    startIndex = 2
    endIndex = 4
  } else if (activeDrawer.value === 'formatif') {
    startIndex = 5
    endIndex = 7
  } else if (activeDrawer.value === 'sumatif') {
    startIndex = 8
    endIndex = 10
  }

  return guruMenus.slice(startIndex, endIndex)
})

const openDrawer = (drawerName) => {
  activeDrawer.value = drawerName
  drawerOpen.value = true
}

const closeDrawer = () => {
  drawerOpen.value = false
}

onMounted(() => {
  fetchSekolah()
})

const tokenCookie = useCookie('auth_token')
const { data: dashboardStatus } = await useFetch(import.meta.env.VITE_API_BASE_URL + '/api/guru/dashboard', {
  headers: { Authorization: `Bearer ${tokenCookie.value}` }
})
const isWalas = computed(() => dashboardStatus.value?.data?.is_walas || false)

const userCookie = useCookie('user_profile')
const userProfile = computed(() => {
  if (!userCookie.value) return null
  return typeof userCookie.value === 'string' ? JSON.parse(userCookie.value) : userCookie.value
})

const userInitials = computed(() => {
  if (!userProfile.value || !userProfile.value.name) return 'G'
  return userProfile.value.name.charAt(0).toUpperCase()
})



const handleLogout = async () => {
  try {
    const tokenCookie = useCookie('auth_token')
    
    // Panggil API logout 
    if (tokenCookie.value) {
      await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/logout', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${tokenCookie.value}`
        }
      })
    }
  } catch (error) {
    console.error('Logout error', error)
  } finally {
    useCookie('auth_token').value = null
    useCookie('user_profile').value = null
    router.push('/login')
  }
}
</script>

<style scoped>
.custom-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
.custom-scrollbar::-webkit-scrollbar {
  display: none;
}
.drawer-overlay-enter-active,
.drawer-overlay-leave-active {
  transition: opacity 0.3s ease;
}
.drawer-overlay-enter-from,
.drawer-overlay-leave-to {
  opacity: 0;
}

.drawer-panel-enter-active,
.drawer-panel-leave-active {
  transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.drawer-panel-enter-from,
.drawer-panel-leave-to {
  transform: translateY(100%);
}
</style>

