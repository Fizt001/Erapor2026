<template>
  <div class="min-h-screen bg-slate-50 flex text-slate-800 text-sm print:bg-white print:block print:min-h-0">
    
    <!-- Drawer / Sidebar -->
    <aside class="group bg-slate-900 text-white flex-shrink-0 min-h-screen fixed lg:static z-50 transform transition-all duration-300 ease-in-out overflow-x-hidden print:hidden w-64" :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0']">
      <div class="h-14 flex items-center pl-5 pr-4 bg-slate-950 font-bold text-base tracking-wider border-b border-slate-800 whitespace-nowrap overflow-hidden justify-between">
        <div class="flex items-center">
            <span class="text-amber-500 mr-2 text-xl shrink-0">★</span>
            <span class="uppercase tracking-widest text-xs">Superadmin</span>
        </div>
        <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>
      
      <div class="p-3">
        <nav class="space-y-2 h-[calc(100vh-140px)] overflow-y-auto custom-scrollbar pr-2">
          
          <!-- ADMIN MODULE -->
          <div class="border border-slate-700 rounded-md overflow-hidden">
            <button @click="toggleMenu('admin')" class="w-full flex items-center justify-between px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 transition-colors">
              <div class="flex items-center font-semibold text-[13px]"><span class="mr-2">⚙️</span> Admin</div>
              <span class="text-xs transition-transform duration-200" :class="{ 'rotate-180': openMenu === 'admin' }">▼</span>
            </button>
            <div v-show="openMenu === 'admin'" class="bg-slate-900 py-1 flex flex-col space-y-1">
              <template v-for="(menu, idx) in adminMenus" :key="idx">
                <NuxtLink v-if="!menu.divider" :to="menu.path" class="px-8 py-1.5 text-xs text-slate-400 hover:text-white" active-class="text-amber-400 font-bold">{{ menu.name }}</NuxtLink>
              </template>
            </div>
          </div>

          <!-- KURIKULUM MODULE -->
          <div class="border border-slate-700 rounded-md overflow-hidden">
            <button @click="toggleMenu('kurikulum')" class="w-full flex items-center justify-between px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 transition-colors">
              <div class="flex items-center font-semibold text-[13px]"><span class="mr-2">📚</span> Kurikulum</div>
              <span class="text-xs transition-transform duration-200" :class="{ 'rotate-180': openMenu === 'kurikulum' }">▼</span>
            </button>
            <div v-show="openMenu === 'kurikulum'" class="bg-slate-900 py-1 flex flex-col space-y-1">
              <template v-for="(menu, idx) in kurikulumMenus" :key="idx">
                <NuxtLink v-if="!menu.divider" :to="menu.path" class="px-8 py-1.5 text-xs text-slate-400 hover:text-white" active-class="text-amber-400 font-bold">{{ menu.name }}</NuxtLink>
              </template>
            </div>
          </div>

          <!-- GURU MODULE -->
          <div class="border border-slate-700 rounded-md overflow-hidden">
            <button @click="toggleMenu('guru')" class="w-full flex items-center justify-between px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 transition-colors">
              <div class="flex items-center font-semibold text-[13px]"><span class="mr-2">👨‍🏫</span> Guru Mapel</div>
              <span class="text-xs transition-transform duration-200" :class="{ 'rotate-180': openMenu === 'guru' }">▼</span>
            </button>
            <div v-show="openMenu === 'guru'" class="bg-slate-900 py-1 flex flex-col space-y-1">
              <template v-for="(menu, idx) in guruMenus" :key="idx">
                <NuxtLink v-if="!menu.divider" :to="menu.path" class="px-8 py-1.5 text-xs text-slate-400 hover:text-white" active-class="text-amber-400 font-bold">{{ menu.name }}</NuxtLink>
              </template>
            </div>
          </div>

          <!-- WALAS MODULE -->
          <div class="border border-slate-700 rounded-md overflow-hidden">
            <button @click="toggleMenu('walas')" class="w-full flex items-center justify-between px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 transition-colors">
              <div class="flex items-center font-semibold text-[13px]"><span class="mr-2">👨‍👩‍👧</span> Wali Kelas</div>
              <span class="text-xs transition-transform duration-200" :class="{ 'rotate-180': openMenu === 'walas' }">▼</span>
            </button>
            <div v-show="openMenu === 'walas'" class="bg-slate-900 py-1 flex flex-col space-y-1">
              <template v-for="(menu, idx) in walasMenus" :key="idx">
                <NuxtLink v-if="!menu.divider" :to="menu.path" class="px-8 py-1.5 text-xs text-slate-400 hover:text-white" active-class="text-amber-400 font-bold">{{ menu.name }}</NuxtLink>
              </template>
            </div>
          </div>

          <!-- BK MODULE -->
          <div class="border border-slate-700 rounded-md overflow-hidden">
            <button @click="toggleMenu('bk')" class="w-full flex items-center justify-between px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 transition-colors">
              <div class="flex items-center font-semibold text-[13px]"><span class="mr-2">⚖️</span> Bimbingan Konseling</div>
              <span class="text-xs transition-transform duration-200" :class="{ 'rotate-180': openMenu === 'bk' }">▼</span>
            </button>
            <div v-show="openMenu === 'bk'" class="bg-slate-900 py-1 flex flex-col space-y-1">
              <template v-for="(menu, idx) in bkMenus" :key="idx">
                <NuxtLink v-if="!menu.divider" :to="menu.path" class="px-8 py-1.5 text-xs text-slate-400 hover:text-white" active-class="text-amber-400 font-bold">{{ menu.name }}</NuxtLink>
              </template>
            </div>
          </div>

          <!-- SISWA MODULE -->
          <div class="border border-slate-700 rounded-md overflow-hidden">
            <button @click="toggleMenu('siswa')" class="w-full flex items-center justify-between px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 transition-colors">
              <div class="flex items-center font-semibold text-[13px]"><span class="mr-2">🎓</span> Siswa</div>
              <span class="text-xs transition-transform duration-200" :class="{ 'rotate-180': openMenu === 'siswa' }">▼</span>
            </button>
            <div v-show="openMenu === 'siswa'" class="bg-slate-900 py-1 flex flex-col space-y-1">
              <template v-for="(menu, idx) in siswaMenus" :key="idx">
                <NuxtLink v-if="!menu.divider" :to="menu.path" class="px-8 py-1.5 text-xs text-slate-400 hover:text-white" active-class="text-amber-400 font-bold">{{ menu.name }}</NuxtLink>
              </template>
            </div>
          </div>

        </nav>
      </div>
    </aside>

    <!-- Overlay when sidebar is open on mobile -->
    <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden backdrop-blur-sm print:hidden"></div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden print:h-auto print:overflow-visible print:block relative">
      
      <!-- Topbar / Navbar -->
      <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-[40] shadow-sm flex-shrink-0 print:hidden relative">
        <div class="flex items-center">
          <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-amber-600 transition-colors focus:outline-none mr-3">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          <div class="hidden lg:flex items-center gap-2 border-l-2 border-amber-500 pl-3">
             <span class="text-xs font-black uppercase tracking-widest text-slate-800">Mode Superadmin</span>
          </div>
        </div>
        
        <div class="flex items-center space-x-4">
            <button @click="logout" type="button" class="flex items-center px-4 py-1.5 text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-full transition-colors border border-rose-100 shadow-sm">
                <span class="mr-1.5">🚪</span> Keluar Sistem
            </button>
        </div>
      </header>

      <!-- Impersonation Bar (Sticky) -->
      <div class="bg-amber-400 border-b border-amber-500 px-4 py-2 flex flex-col xl:flex-row items-center justify-between gap-3 shadow-md z-[35] relative">
          <div class="flex items-center space-x-3 font-bold text-sm shrink-0 w-full xl:w-auto overflow-x-auto custom-scrollbar pb-1 xl:pb-0">
             <div class="flex items-center whitespace-nowrap bg-amber-500 text-amber-950 px-3 py-1 rounded-full text-xs shadow-inner">
                <span class="mr-2 animate-pulse">🔴</span>
                OVERRIDE MODE
             </div>
             
             <!-- Pilih Guru -->
             <div class="relative shrink-0 flex items-center bg-white/50 rounded-lg pr-1 border border-amber-500/20 shadow-sm hover:bg-white transition-colors focus-within:bg-white">
                <span class="text-[10px] uppercase font-black px-2 text-amber-900 border-r border-amber-500/20">Guru</span>
                <select v-model="selectedGuru" @change="applyImpersonation('guru')" class="bg-transparent border-none text-xs font-bold text-slate-800 focus:ring-0 py-1 pl-2 pr-6 appearance-none cursor-pointer">
                   <option :value="null">-- Pilih --</option>
                   <option v-for="g in gurus" :key="g.user_id" :value="g.user_id">{{ g.nama }}</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-amber-800">
                    <svg class="fill-current h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                </div>
             </div>

             <!-- Pilih Walas -->
             <div class="relative shrink-0 flex items-center bg-white/50 rounded-lg pr-1 border border-amber-500/20 shadow-sm hover:bg-white transition-colors focus-within:bg-white">
                <span class="text-[10px] uppercase font-black px-2 text-amber-900 border-r border-amber-500/20">Walas</span>
                <select v-model="selectedWalas" @change="applyImpersonation('walas')" class="bg-transparent border-none text-xs font-bold text-slate-800 focus:ring-0 py-1 pl-2 pr-6 appearance-none cursor-pointer">
                   <option :value="null">-- Pilih --</option>
                   <option v-for="w in walasList" :key="w.user_id" :value="w.user_id">{{ w.nama_kelas }} ({{ w.walas }})</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-amber-800">
                    <svg class="fill-current h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                </div>
             </div>

             <!-- Pilih Siswa (Dua Tahap) -->
             <div class="relative shrink-0 flex items-center bg-white/50 rounded-lg pr-1 border border-amber-500/20 shadow-sm hover:bg-white transition-colors focus-within:bg-white">
                <span class="text-[10px] uppercase font-black px-2 text-amber-900 border-r border-amber-500/20">Siswa</span>
                <select v-model="selectedSiswaKelas" class="bg-transparent border-none text-xs font-bold text-slate-800 focus:ring-0 py-1 pl-2 pr-6 appearance-none cursor-pointer border-r border-amber-500/20">
                   <option :value="null">-Kelas-</option>
                   <option v-for="k in uniqueSiswaClasses" :key="k.kelas_id" :value="k.kelas_id">{{ k.nama_kelas }}</option>
                </select>
                <select v-model="selectedSiswa" @change="applyImpersonation('siswa')" :disabled="!selectedSiswaKelas" class="bg-transparent border-none text-xs font-bold text-slate-800 focus:ring-0 py-1 pl-2 pr-6 appearance-none cursor-pointer disabled:opacity-50">
                   <option :value="null">-Nama-</option>
                   <option v-for="s in filteredSiswaList" :key="s.user_id" :value="s.user_id">{{ s.nama }}</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-amber-800">
                    <svg class="fill-current h-3 w-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                </div>
             </div>
             
             <!-- Clear Button -->
             <button v-if="impersonateUserId" @click="clearImpersonationMode" class="ml-2 shrink-0 bg-red-600 hover:bg-red-700 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full shadow-sm transition-colors">
                 Hapus Override
             </button>
          </div>

          <!-- Peringatan Aktif -->
          <div v-if="impersonateUserId" class="bg-amber-950 text-amber-100 text-[10px] font-bold px-3 py-1.5 rounded-lg flex items-center shrink-0 shadow-inner w-full xl:w-auto justify-center">
             <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
             AKTIF ({{ impersonateRole?.toUpperCase() }}) - Hapus jika error saat pindah menu.
          </div>
      </div>

      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto bg-slate-100 relative print:p-0 print:bg-white print:overflow-visible print:block">
        <slot />
      </main>
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { adminMenus, kurikulumMenus, guruMenus, walasMenus, bkMenus, siswaMenus } from '~/utils/menus'
import { useCookie, useFetch } from '#app'
import { useImpersonation } from '../composables/useImpersonation'

const sidebarOpen = ref(false)
const openMenu = ref('admin')
const tokenCookie = useCookie('auth_token')
const userCookie = useCookie('user_profile')
const router = useRouter()
const route = useRoute()
const visiMisiDialog = ref(null)
const { impersonateUserId, impersonateRole, setImpersonation, clearImpersonation } = useImpersonation()

const isGuruMenu = computed(() => route.path.startsWith('/guru') && !route.path.startsWith('/guru/walas'))
const isWalasMenu = computed(() => route.path.startsWith('/guru/walas'))
const isSiswaMenu = computed(() => route.path.startsWith('/siswa'))
const isKurikulumMenu = computed(() => route.path.startsWith('/kurikulum'))
const isKepsekMenu = computed(() => route.path.startsWith('/kepsek'))

// Auto-clear impersonation jika masuk ke menu admin
watch(() => route.path, (newPath) => {
    if (newPath.startsWith('/admin') && impersonateUserId.value) {
        clearImpersonation()
    }
})

// Lifecycle: Saat pertama dimuat, jika di rute admin tapi ada impersonation yang nyangkut, bersihkan.
onMounted(() => {
    if (route.path.startsWith('/admin') && impersonateUserId.value) {
        clearImpersonation()
    }

    // Buka accordion menu sesuai path saat ini
    const path = window.location.pathname;
    if (path.startsWith('/admin')) openMenu.value = 'admin'
    else if (path.startsWith('/kurikulum')) openMenu.value = 'kurikulum'
    else if (path.startsWith('/guru/walas')) openMenu.value = 'walas'
    else if (path.startsWith('/guru')) openMenu.value = 'guru'
    else if (path.startsWith('/bk')) openMenu.value = 'bk'
    else if (path.startsWith('/siswa')) openMenu.value = 'siswa'

    loadImpersonationData()
})

const gurus = ref([])
const walasList = ref([])
const siswaList = ref([])

const isLoadingData = ref(true)

const selectedGuru = ref(null)
const selectedWalas = ref(null)
const selectedSiswa = ref(null)
const selectedSiswaKelas = ref(null)

const toggleMenu = (menu) => {
    openMenu.value = openMenu.value === menu ? null : menu;
}

const uniqueSiswaClasses = computed(() => {
    const map = new Map();
    siswaList.value.forEach(s => {
        if (s.user_id && !map.has(s.kelas_id)) {
            map.set(s.kelas_id, { kelas_id: s.kelas_id, nama_kelas: s.nama_kelas });
        }
    });
    return Array.from(map.values()).sort((a, b) => a.nama_kelas.localeCompare(b.nama_kelas));
});

const filteredSiswaList = computed(() => {
    if (!selectedSiswaKelas.value) return [];
    return siswaList.value.filter(s => s.kelas_id === selectedSiswaKelas.value && s.user_id);
});

const loadImpersonationData = async () => {
    isLoadingData.value = true
    try {
        const headers = { 
            'Authorization': `Bearer ${tokenCookie.value}`, 
            'Accept': 'application/json',
            'X-Bypass-Impersonation': 'true'
        }

        const [gurusRes, walasRes, siswaRes] = await Promise.all([
            $fetch(import.meta.env.VITE_API_BASE_URL + '/api/superadmin/gurus', { headers }).catch(() => ({ data: [] })),
            $fetch(import.meta.env.VITE_API_BASE_URL + '/api/superadmin/walas-classes', { headers }).catch(() => ({ data: [] })),
            $fetch(import.meta.env.VITE_API_BASE_URL + '/api/superadmin/siswas', { headers }).catch(() => ({ data: [] }))
        ])

        gurus.value = gurusRes.data || []
        walasList.value = walasRes.data || []
        siswaList.value = siswaRes.data || []
        
        // Restore selection state
        if (impersonateRole.value === 'guru') selectedGuru.value = impersonateUserId.value
        if (impersonateRole.value === 'walas') selectedWalas.value = impersonateUserId.value
        if (impersonateRole.value === 'siswa') {
            selectedSiswa.value = impersonateUserId.value
            const matchedSiswa = siswaList.value.find(s => s.user_id == impersonateUserId.value)
            if (matchedSiswa) {
                selectedSiswaKelas.value = matchedSiswa.kelas_id
            }
        }

    } catch (e) {
        console.error("Gagal memuat data superadmin:", e)
    } finally {
        isLoadingData.value = false
    }
}

const applyImpersonation = (roleType) => {
    if (roleType === 'guru' && selectedGuru.value) {
        setImpersonation(selectedGuru.value, 'guru')
        selectedWalas.value = null; selectedSiswa.value = null;
        window.location.href = '/guru/dashboard' // Full reload to clear cache
    }
    else if (roleType === 'walas' && selectedWalas.value) {
        setImpersonation(selectedWalas.value, 'walas')
        selectedGuru.value = null; selectedSiswa.value = null;
        window.location.href = '/guru/walas/dashboard'
    }
    else if (roleType === 'siswa' && selectedSiswa.value) {
        setImpersonation(selectedSiswa.value, 'siswa')
        selectedGuru.value = null; selectedWalas.value = null;
        window.location.href = '/siswa/dashboard'
    }
}

const clearImpersonationMode = () => {
    clearImpersonation()
    selectedGuru.value = null
    selectedWalas.value = null
    selectedSiswa.value = null
    window.location.href = '/admin/dashboard'
}

const logout = async () => {
    try {
        await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/logout', {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${tokenCookie.value}` }
        })
    } catch (error) {
        console.error(error)
    } finally {
        tokenCookie.value = null
        userCookie.value = null
        clearImpersonation()
        router.push('/login')
    }
}

onMounted(() => {
    // Buka accordion menu sesuai path saat ini
    const path = window.location.pathname;
    if (path.startsWith('/admin')) openMenu.value = 'admin'
    else if (path.startsWith('/kurikulum')) openMenu.value = 'kurikulum'
    else if (path.startsWith('/guru/walas')) openMenu.value = 'walas'
    else if (path.startsWith('/guru')) openMenu.value = 'guru'
    else if (path.startsWith('/bk')) openMenu.value = 'bk'
    else if (path.startsWith('/siswa')) openMenu.value = 'siswa'

    loadImpersonationData()
})
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
