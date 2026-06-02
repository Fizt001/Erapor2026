<template>
  <div class="min-h-screen bg-slate-50 flex text-slate-800 text-sm">
    <!-- Sidebar -->
    <aside class="w-56 bg-slate-900 text-white flex-shrink-0 min-h-screen fixed lg:static z-50 transition-transform transform lg:translate-x-0" :class="{ '-translate-x-full': !sidebarOpen }">
      <div class="h-14 flex items-center px-4 bg-slate-950 font-bold text-base tracking-wider border-b border-slate-800">
        <span class="text-emerald-500 mr-2">e</span>-Rapor
      </div>
      
      <div class="p-3">


        <nav class="space-y-1">
          <NuxtLink to="/admin/dashboard" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-md text-slate-300 hover:bg-slate-800 hover:text-white transition-colors" active-class="bg-emerald-600 text-white shadow">
            <span class="mr-3 text-lg">📊</span> Dashboard
          </NuxtLink>

          <div class="pt-4 pb-1 px-3 text-[10px] font-bold text-emerald-500 uppercase tracking-widest">
            Master Data
          </div>

          <NuxtLink to="/admin/sekolah" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-md text-slate-300 hover:bg-slate-800 hover:text-white transition-colors" active-class="bg-emerald-600 text-white shadow">
            <span class="mr-3 text-lg">🏢</span> Data Sekolah
          </NuxtLink>
          <NuxtLink to="/admin/users" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-md text-slate-300 hover:bg-slate-800 hover:text-white transition-colors" active-class="bg-emerald-600 text-white shadow">
            <span class="mr-3 text-lg">👥</span> Manajemen User
          </NuxtLink>
          <NuxtLink to="/admin/master-database" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-md text-slate-300 hover:bg-slate-800 hover:text-white transition-colors" active-class="bg-emerald-600 text-white shadow">
            <span class="mr-3 text-lg">🗄️</span> Master Database
          </NuxtLink>
          <NuxtLink to="/admin/kejuruan" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-md text-slate-300 hover:bg-slate-800 hover:text-white transition-colors" active-class="bg-emerald-600 text-white shadow">
            <span class="mr-3 text-lg">🛠️</span> Master Kejuruan
          </NuxtLink>
          <NuxtLink to="/admin/kelas" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-md text-slate-300 hover:bg-slate-800 hover:text-white transition-colors" active-class="bg-emerald-600 text-white shadow">
            <span class="mr-3 text-lg">🏫</span> Master Kelas
          </NuxtLink>

          <div class="pt-4 pb-1 px-3 text-[10px] font-bold text-emerald-500 uppercase tracking-widest">
            Sistem Akademik
          </div>
          <NuxtLink to="/admin/tahun-ajaran" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-md text-slate-300 hover:bg-slate-800 hover:text-white transition-colors" active-class="bg-emerald-600 text-white shadow">
            <span class="mr-3 text-lg">🗓️</span> Tahun Ajaran
          </NuxtLink>
          <NuxtLink to="/admin/kurikulum" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-md text-slate-300 hover:bg-slate-800 hover:text-white transition-colors" active-class="bg-emerald-600 text-white shadow">
            <span class="mr-3 text-lg">📚</span> Master Kurikulum
          </NuxtLink>

          <div class="pt-4 pb-1 px-3 text-[10px] font-bold text-emerald-500 uppercase tracking-widest">
            Laporan & Arsip
          </div>
          <NuxtLink to="/admin/buku-induk" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-md text-slate-300 hover:bg-slate-800 hover:text-white transition-colors" active-class="bg-emerald-600 text-white shadow">
            <span class="mr-3 text-lg">📖</span> Buku Induk
          </NuxtLink>
          <NuxtLink to="/admin/backup" class="group flex items-center px-3 py-1.5 text-[13px] font-medium rounded-md text-slate-300 hover:bg-slate-800 hover:text-white transition-colors" active-class="bg-emerald-600 text-white shadow">
            <span class="mr-3 text-lg">💾</span> Backup & Arsip
          </NuxtLink>
        </nav>
      </div>
    </aside>

    <!-- Overlay when sidebar is open on mobile -->
    <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden backdrop-blur-sm"></div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
      <!-- Navbar -->
      <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-10 shadow-sm flex-shrink-0">
        <div class="flex items-center">
          <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-slate-500 hover:text-emerald-600 transition-colors focus:outline-none">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          <h2 class="hidden lg:block text-base font-bold text-slate-800 ml-3 border-l-2 border-emerald-500 pl-3 py-1 uppercase tracking-wider">{{ route.meta.title || 'Admin Workspace' }}</h2>
        </div>
        
        <div class="flex-1 lg:flex-none flex justify-end items-center space-x-4">
          <!-- Profile in Navbar -->
          <div class="flex items-center space-x-3 text-right">
            <div class="hidden sm:block min-w-0">
              <p class="text-[13px] font-bold text-slate-700 truncate leading-tight">{{ userProfile?.name || 'Administrator' }}</p>
              <p class="text-[10px] text-slate-500 truncate uppercase tracking-wider">{{ userProfile?.email || 'admin@erapor.com' }}</p>
            </div>
            <div class="h-9 w-9 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-black shadow-md text-sm border-2 border-white">
              {{ userInitials }}
            </div>
          </div>
          
          <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

          <!-- Logout Button (Icon Only) -->
          <button @click="handleLogout" title="Logout" class="text-xl text-rose-500 hover:text-rose-700 hover:bg-rose-50 flex items-center justify-center p-2 rounded-xl transition-all active:scale-95">
            🚪
          </button>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto p-4 sm:p-5 bg-slate-100">
        <NuxtPage />
        
        <!-- Footer Info -->
        <div class="mt-10 pt-4 border-t border-slate-200 text-center pb-4">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Created by <span class="text-emerald-600">SMK-Yatindo</span></p>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()
const sidebarOpen = ref(false)

const userCookie = useCookie('user_profile')
const userProfile = computed(() => {
  if (!userCookie.value) return null
  return typeof userCookie.value === 'string' ? JSON.parse(userCookie.value) : userCookie.value
})

const userInitials = computed(() => {
  if (!userProfile.value || !userProfile.value.name) return 'A'
  return userProfile.value.name.charAt(0).toUpperCase()
})

const handleLogout = async () => {
  try {
    const tokenCookie = useCookie('auth_token')
    
    // Panggil API logout 
    if (tokenCookie.value) {
      await $fetch('http://localhost:8000/api/logout', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${tokenCookie.value}`
        }
      })
    }
  } catch (error) {
    console.error('Logout error', error)
  } finally {
    // Hapus cookies dan arahkan ke login
    useCookie('auth_token').value = null
    useCookie('user_profile').value = null
    router.push('/login')
  }
}
</script>
