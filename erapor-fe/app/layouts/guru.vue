<template>
  <div class="min-h-screen bg-slate-50 flex text-slate-800 text-sm print:bg-white print:block print:min-h-0">
    <VisiMisiDialog ref="visiMisiDialog" />
    <!-- Sidebar -->
    <aside class="group bg-slate-900 text-white flex-shrink-0 min-h-screen fixed lg:static z-50 transform lg:translate-x-0 transition-all duration-300 ease-in-out overflow-x-hidden print:hidden" :class="[sidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full lg:translate-x-0 lg:w-[72px] lg:hover:w-64 w-64']">
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

        <nav class="space-y-2 h-[calc(100vh-140px)] overflow-y-auto custom-scrollbar pr-2">
          
          <!-- GURU MENGAJAR MODULE -->
          <div class="border border-sky-900/30 rounded-xl overflow-hidden bg-slate-900/40">
            <button @click="toggleMenu('guru')" class="w-full flex items-center px-3 py-3 hover:bg-slate-800 text-sky-100 transition-colors whitespace-nowrap" :class="{'bg-slate-800/80': openMenu === 'guru'}">
              <span class="mr-3 text-lg shrink-0">👨‍🏫</span>
              <div class="flex-1 flex items-center justify-between opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300 overflow-hidden">
                 <span class="font-bold text-[13px] tracking-wide text-sky-400">Guru Mengajar</span>
                 <span class="text-xs transition-transform duration-200 text-sky-500" :class="{ 'rotate-180': openMenu === 'guru' }">▼</span>
              </div>
            </button>
            
            <div v-show="openMenu === 'guru'" class="bg-slate-950/40 py-2 flex flex-col space-y-1 border-t border-sky-900/30">
              <template v-for="(menu, idx) in guruMenus" :key="idx">
                <div v-if="menu.divider" class="pt-3 pb-1 px-3 text-[10px] font-bold text-sky-400 uppercase tracking-widest whitespace-nowrap opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300">
                  {{ menu.dividerLabel }}
                </div>
                <NuxtLink v-else :to="menu.path" class="group flex items-center px-3 py-2 text-[13px] font-medium rounded-md text-slate-300 hover:bg-slate-800 hover:text-white transition-colors whitespace-nowrap mx-1" active-class="bg-sky-600 text-white shadow">
                  <span class="mr-3 text-lg shrink-0">{{ menu.icon }}</span>
                  <span class="opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300">{{ menu.name }}</span>
                </NuxtLink>
              </template>
            </div>
          </div>

          <!-- WALI KELAS MODULE -->
          <div v-if="isWalas" class="border border-amber-900/30 rounded-xl overflow-hidden bg-slate-900/40">
            <button @click="toggleMenu('walas')" class="w-full flex items-center px-3 py-3 hover:bg-slate-800 text-amber-100 transition-colors whitespace-nowrap" :class="{'bg-slate-800/80': openMenu === 'walas'}">
              <span class="mr-3 text-lg shrink-0">👨‍👩‍👧</span>
              <div class="flex-1 flex items-center justify-between opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300 overflow-hidden">
                 <span class="font-bold text-[13px] tracking-wide text-amber-400">Wali Kelas</span>
                 <span class="text-xs transition-transform duration-200 text-amber-500" :class="{ 'rotate-180': openMenu === 'walas' }">▼</span>
              </div>
            </button>
            
            <div v-show="openMenu === 'walas'" class="bg-slate-950/40 py-2 flex flex-col space-y-1 border-t border-amber-900/30">
              <template v-for="(menu, idx) in walasMenus" :key="'w'+idx">
                <div v-if="menu.divider" class="pt-3 pb-1 px-3 text-[10px] font-bold text-amber-500 uppercase tracking-widest whitespace-nowrap opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300">
                  {{ menu.dividerLabel }}
                </div>
                <NuxtLink v-else :to="menu.path" class="group flex items-center px-3 py-2 text-[13px] font-medium rounded-md text-slate-300 hover:bg-slate-800 hover:text-white transition-colors whitespace-nowrap mx-1" active-class="bg-amber-600 text-white shadow">
                  <span class="mr-3 text-lg shrink-0">{{ menu.icon }}</span>
                  <span class="opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300">{{ menu.name }}</span>
                </NuxtLink>
              </template>
            </div>
          </div>

        </nav>
      </div>
    </aside>

    <!-- Overlay when sidebar is open on mobile -->
    <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden backdrop-blur-sm print:hidden"></div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden print:h-auto print:overflow-visible print:block">
      <!-- Navbar -->
      <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-[60] shadow-sm relative print:hidden shrink-0">
        <div class="flex items-center">
          <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 mr-2 -ml-2 text-slate-500 hover:text-sky-600 focus:outline-none hover:bg-sky-50 rounded-lg transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
          </button>
          
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
const openMenu = ref('guru')

const { sekolah, fetchSekolah } = useSekolah()

onMounted(() => {
  fetchSekolah()
  const path = window.location.pathname;
  if (path.startsWith('/guru/walas')) {
      openMenu.value = 'walas'
  } else {
      openMenu.value = 'guru'
  }
})

const toggleMenu = (menu) => {
    openMenu.value = openMenu.value === menu ? null : menu;
}

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
</style>

