<template>
  <div class="min-h-screen bg-slate-50 flex text-slate-800 text-sm print:bg-white print:block print:min-h-0">
    <VisiMisiDialog ref="visiMisiDialog" />
    <!-- Sidebar (Desktop Only) -->
    <aside class="group bg-slate-900 text-white flex-shrink-0 min-h-screen hidden lg:flex flex-col fixed lg:static z-50 transition-all duration-300 ease-in-out overflow-x-hidden print:hidden" :class="[sidebarOpen ? 'w-64' : 'lg:w-[72px] lg:hover:w-64']">
      <div class="h-14 flex items-center pl-5 pr-4 bg-slate-950 font-bold text-base tracking-wider border-b border-slate-800 whitespace-nowrap overflow-hidden">
        <img v-if="sekolah?.logo" :src="sekolah.logo" alt="Logo" class="h-8 w-8 object-contain lg:mr-3 shrink-0 cursor-pointer hover:scale-110 transition-transform" @click="visiMisiDialog?.open()" />
        <span v-else class="text-amber-500 mr-1 text-xl shrink-0 cursor-pointer hover:scale-110 transition-transform" @click="visiMisiDialog?.open()">e</span>
        <span class="opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300 flex items-center cursor-pointer" @click="visiMisiDialog?.open()">
            <span v-if="sekolah?.logo" class="ml-1">e-Rapor</span>
            <span v-else>-Rapor</span>
            <span class="ml-2 text-[10px] bg-amber-500/20 text-amber-300 px-2 py-0.5 rounded border border-amber-500/30">WALAS</span>
        </span>
      </div>
      
      <div class="p-3">
        <nav class="space-y-1 h-[calc(100vh-140px)] overflow-y-auto custom-scrollbar pr-2">
          <template v-for="(menu, idx) in walasMenus" :key="idx">
            <div v-if="menu.divider" class="pt-4 pb-1 px-3 text-[10px] font-bold text-amber-500 uppercase tracking-widest whitespace-nowrap opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300">{{ menu.dividerLabel }}</div>
            <NuxtLink v-else :to="menu.path" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-md text-slate-300 hover:bg-slate-800 hover:text-white transition-colors whitespace-nowrap" active-class="bg-amber-600 text-white shadow">
              <span class="mr-3 text-lg">{{ menu.icon }}</span>
              <span class="opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300">{{ menu.name }}</span>
            </NuxtLink>
          </template>
          
          <!-- PINTU KEMBALI KE GURU (Desktop) -->
          <div class="pt-6 pb-2 px-3">
             <NuxtLink to="/guru/dashboard" class="flex flex-col items-center justify-center p-3 rounded-xl bg-gradient-to-br from-sky-500 to-indigo-600 hover:from-sky-400 hover:to-indigo-500 text-white shadow-lg shadow-sky-500/20 transition-all hover:-translate-y-1 group/door">
                 <span class="text-2xl mb-1 group-hover/door:-translate-x-1 transition-transform">🔙</span>
                 <span class="text-[10px] font-black uppercase tracking-widest text-center opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">Guru Mengajar</span>
             </NuxtLink>
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
            <span v-else class="text-amber-600 font-black text-lg">e</span>
            <span class="font-black text-slate-700 text-sm">e-Rapor <span class="text-amber-600">Walas</span></span>
          </div>
          <h2 class="hidden lg:block text-base font-bold text-slate-800 ml-3 border-l-2 border-amber-500 pl-3 py-1 uppercase tracking-wider">{{ route.meta.title || 'Walas Workspace' }}</h2>
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
              <button @click="router.push('/guru/profil'); profileDropdownOpen = false" type="button" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-700 transition-colors w-full text-left">
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
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Created by <span class="text-amber-600">SMK-Yatindo</span></p>
        </div>
      </main>
    </div>

    <!-- MOBILE BOTTOM NAV BAR -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-slate-200 shadow-[0_-4px_20px_-4px_rgba(0,0,0,0.1)] print:hidden">
      <div class="flex items-stretch h-16">
        <NuxtLink to="/guru/walas/dashboard" class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors" :class="route.path === '/guru/walas/dashboard' ? 'text-amber-600' : 'text-slate-400'">
          <span class="text-xl leading-none">📊</span>
          <span class="text-[9px] font-black uppercase tracking-wider">Dashboard</span>
        </NuxtLink>
        <button @click="openDrawer('siswa')" class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors" :class="activeDrawer === 'siswa' && drawerOpen ? 'text-amber-600' : 'text-slate-400'">
          <span class="text-xl leading-none">👨‍👩‍👧</span>
          <span class="text-[9px] font-black uppercase tracking-wider">Siswa</span>
        </button>
        <button @click="openDrawer('kegiatan')" class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors" :class="activeDrawer === 'kegiatan' && drawerOpen ? 'text-amber-600' : 'text-slate-400'">
          <span class="text-xl leading-none">🎭</span>
          <span class="text-[9px] font-black uppercase tracking-wider">Kegiatan</span>
        </button>
        <button @click="openDrawer('rapor')" class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors" :class="activeDrawer === 'rapor' && drawerOpen ? 'text-amber-600' : 'text-slate-400'">
          <span class="text-xl leading-none">📋</span>
          <span class="text-[9px] font-black uppercase tracking-wider">Rapor</span>
        </button>
        
        <NuxtLink to="/guru/dashboard" class="flex-1 flex flex-col items-center justify-center gap-1 transition-colors text-sky-500 hover:text-sky-600 relative">
          <span class="absolute -top-2 right-2 flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-sky-500 border border-white"></span>
          </span>
          <span class="text-xl leading-none">🔙</span>
          <span class="text-[9px] font-black uppercase tracking-wider">Guru</span>
        </NuxtLink>
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
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl shadow-sm transition-all" :class="route.path === menu.path ? 'bg-amber-500 shadow-amber-200 shadow-lg' : 'bg-slate-100'">{{ menu.icon }}</div>
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
  if (activeDrawer.value === 'siswa') return 'Data Siswa'
  if (activeDrawer.value === 'kegiatan') return 'Kegiatan Ekstra'
  if (activeDrawer.value === 'rapor') return 'Proses Rapor'
  return 'Menu'
})

// walasMenus indexing:
// 0: Dashboard
// 1: divider (Siswa)
// 2: Biodata
// 3: Absensi
// 4: Jurnal
// 5: Catatan
// 6: divider (Kegiatan)
// 7: Ekskul
// 8: P5
// 9: Kokurikuler
// 10: Prestasi
// 11: divider (Proses Akhir)
// 12: Monitoring
// 13: Cetak Rapor
// 14: Leger
// 15: Kenaikan

const currentDrawerMenus = computed(() => {
  let startIndex = 0
  let endIndex = walasMenus.length

  if (activeDrawer.value === 'siswa') {
    startIndex = 2
    endIndex = 6
  } else if (activeDrawer.value === 'kegiatan') {
    startIndex = 7
    endIndex = 11
  } else if (activeDrawer.value === 'rapor') {
    startIndex = 12
    endIndex = 16
  }

  return walasMenus.slice(startIndex, endIndex)
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

