<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex flex-col xl:flex-row overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-1.5 flex gap-1.5 z-20 shadow-sm">
        <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTabMobile = tab.id"
          :class="activeTabMobile === tab.id ? 'bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-md shadow-amber-500/20 ring-2 ring-amber-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="flex-1 rounded-lg flex flex-col items-center justify-center py-1.5 px-0.5 transition-all active:scale-95">
          <AppIcon :name="tab.icon" class="text-lg mb-0.5 transition-transform" :class="activeTabMobile === tab.id ? 'scale-110' : ''" />
          <span class="text-[8px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
        </button>
      </div>

      
      <!-- Panel Dock Kiri -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] overflow-y-auto custom-scrollbar transition-all', activeTabMobile === 'filter' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[60px]' : '']">
        <div class="p-4 pb-2 space-y-4">
          <div class="bg-gradient-to-r from-amber-600 to-amber-700 rounded-2xl p-4 border border-amber-500 shadow-sm relative overflow-hidden flex items-center gap-3">
            <div class="w-8 h-8 flex items-center justify-center shrink-0 bg-white/10 rounded-lg relative z-10 text-white"><AppIcon name="document-text" class="w-5 h-5" /></div>
            <div class="relative z-10">
              <h3 class="text-xs font-black uppercase tracking-widest text-white">Kokurikuler</h3>
              <p class="text-[9px] text-amber-100 font-semibold uppercase mt-0.5">Catatan Capaian P5 Rombel</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-15 text-white pointer-events-none">
              <svg class="w-16 h-16 transform translate-x-6 translate-y-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
          </div>
          
          <div class="space-y-4" v-if="pageData">
            <!-- Informasi Kelas & Tahun -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-3">
              <div class="flex justify-between items-center pb-2 border-b border-slate-200">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Kelas</span>
                <span class="text-[11px] font-bold text-slate-700">{{ pageData.kelas?.tingkat }} {{ pageData.kelas?.nama_kelas }}</span>
              </div>
              <div class="flex justify-between items-center pb-2 border-b border-slate-200">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Tahun Ajaran</span>
                <span class="text-[11px] font-bold text-slate-700">{{ pageData.tahun_ajaran?.tahun }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Kurikulum</span>
                <select 
                    v-model="pageData.kelas.kurikulum_id"
                    @change="saveKurikulum(pageData.kelas.kurikulum_id)"
                    class="text-[11px] font-bold text-slate-700 bg-transparent border-none p-0 pr-4 focus:ring-0 cursor-pointer outline-none text-right"
                >
                    <option v-for="kuri in pageData.master_kurikulum" :key="kuri.id" :value="kuri.id">{{ kuri.nama_kurikulum }}</option>
                </select>
              </div>
            </div>

            <!-- Search / Filter -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pencarian Siswa</label>
              <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 pointer-events-none"><AppIcon name="search" /></span>
                  <input type="text" v-model="searchQuery" placeholder="Cari nama siswa..." 
                    class="w-full pl-10 pr-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all font-semibold text-xs text-slate-700 outline-none">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative transition-all', activeTabMobile === 'flow' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[52px]' : '']">
        <div class="p-2 sm:pt-3 sm:pb-6 sm:px-6 lg:pt-3 lg:pb-8 lg:px-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          
          <!-- Loading State -->
          <div v-if="pending" class="flex-grow flex flex-col items-center justify-center p-20 opacity-60 bg-white rounded-3xl shadow-sm border border-slate-200/60">
            <div class="w-10 h-10 border-4 border-teal-500 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Memuat Data...</span>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="flex-grow flex flex-col items-center justify-center p-16 text-center bg-white rounded-3xl shadow-sm border border-slate-200/60">
            <div class="text-rose-500 text-4xl mb-4"><AppIcon name="lock-closed" /></div>
            <h3 class="text-rose-800 font-black mb-1">Akses Ditolak</h3>
            <p class="text-rose-600 text-sm font-semibold max-w-md">{{ error.message || 'Terjadi kesalahan saat memuat data.' }}</p>
            <button @click="fetchData" class="mt-6 px-6 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm rounded-lg shadow-sm transition-colors">Coba Lagi</button>
          </div>

          <!-- Matrix Editor Container -->
          <div v-else-if="pageData" class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            
            <!-- Header Card Kanan -->
            <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-white shrink-0 z-10">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-lg border border-emerald-100"><AppIcon name="sparkles" /></div>
                <div>
                  <h3 class="text-[13px] font-black leading-none uppercase tracking-wide text-slate-800">Catatan Capaian P5</h3>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Otomatis tersimpan saat mengetik</p>
                </div>
              </div>
            </div>

            <!-- Tabs Periode -->
            <div class="flex border-b border-slate-200 bg-slate-50 overflow-x-auto custom-scrollbar shrink-0">
                <button 
                    v-for="periode in pageData.periodes" 
                    :key="periode.id"
                    @click="activeTab = periode.id"
                    :class="[
                        'px-8 py-4 text-[11px] font-black uppercase tracking-widest transition-all whitespace-nowrap outline-none relative',
                        activeTab === periode.id 
                            ? 'text-amber-700 bg-white' 
                            : 'text-slate-400 hover:text-slate-600 hover:bg-slate-100/50',
                        !periode.is_aktif ? 'opacity-60' : ''
                    ]"
                >
                    <span v-if="!periode.is_aktif" class="mr-1"><AppIcon name="lock-closed" /></span>
                    {{ periode.nama_periode_panjang || periode.nama_periode }}
                    <div v-if="activeTab === periode.id" class="absolute bottom-0 left-0 w-full h-[3px] bg-amber-500 rounded-t-full"></div>
                </button>
            </div>
            
            <!-- Warning Closed -->
            <div v-if="activePeriodeData && !activePeriodeData.is_aktif" class="bg-amber-50 border-b border-amber-100 px-6 py-3 flex items-center gap-3 shrink-0">
                <span class="text-amber-500"><AppIcon name="lock-closed" /></span>
                <p class="text-[11px] font-black uppercase tracking-widest text-amber-700">Periode ini sudah ditutup. Catatan bersifat Read-Only.</p>
            </div>

            <!-- Form Table -->
            <div class="flex-1 overflow-auto custom-scrollbar relative bg-slate-50/30 flex flex-col">
                <table class="w-full text-left border-collapse bg-white">
                    <thead class="sticky top-0 z-20 shadow-sm">
                        <tr class="bg-slate-100 border-b border-slate-200">
                            <th class="py-3 px-4 text-center text-[10px] font-black uppercase tracking-widest text-slate-500 w-[60px] border-r border-slate-200">No</th>
                            <th class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500 border-r border-slate-200 min-w-[200px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] sticky left-0 bg-slate-100 z-30">Nama Siswa</th>
                            <th class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500 min-w-[300px]">Catatan Capaian P5</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template v-if="activePeriodeData">
                            <tr v-for="(siswa, idx) in filteredStudents" :key="activeTab + '-' + siswa.id" class="hover:bg-slate-50/80 transition-colors group">
                                <td class="py-3 px-4 text-center text-[11px] font-bold text-slate-400 border-r border-slate-100 align-top">
                                    {{ idx + 1 }}
                                </td>
                                <td class="py-3 px-4 border-r border-slate-100 sticky left-0 bg-white group-hover:bg-slate-50/90 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.02)] z-10 align-top">
                                    <div class="text-[12px] font-black text-slate-700 uppercase tracking-wide">{{ siswa.nama_lengkap }}</div>
                                </td>
                                <td class="py-3 px-4 align-top">
                                    <div class="relative w-full">
                                        <textarea 
                                            v-model="formKo[siswa.id][activeTab]" 
                                            :disabled="!activePeriodeData.is_aktif"
                                            @input="handleInput(siswa.id, activeTab)"
                                            :class="[
                                                'w-full text-xs font-bold text-slate-700 placeholder-slate-300 border-2 focus:ring-4 focus:ring-amber-500/10 rounded-xl p-3 min-h-[80px] transition-all resize-none custom-scrollbar outline-none',
                                                !activePeriodeData.is_aktif ? 'bg-slate-50 border-slate-200/50 opacity-70 cursor-not-allowed' : 'bg-white border-slate-200/70 focus:border-amber-500'
                                            ]"
                                            placeholder="Ketercapaian projek..." 
                                        ></textarea>
                                        <div class="absolute bottom-2 right-3 pointer-events-none transition-opacity duration-300" 
                                            :class="saveStatus[siswa.id]?.[activeTab] && saveStatus[siswa.id]?.[activeTab] !== 'idle' ? 'opacity-100' : 'opacity-0'">
                                            <span v-if="saveStatus[siswa.id]?.[activeTab] === 'saving'" class="flex items-center text-[10px] font-bold text-amber-500 bg-white/90 backdrop-blur px-2 py-0.5 rounded shadow-sm">
                                                <svg class="animate-spin -ml-1 mr-1.5 h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                Menyimpan...
                                            </span>
                                            <span v-else-if="saveStatus[siswa.id]?.[activeTab] === 'saved'" class="flex items-center text-[10px] font-bold text-emerald-500 bg-white/90 backdrop-blur px-2 py-0.5 rounded shadow-sm">
                                                <AppIcon name="check" /> Tersimpan
                                            </span>
                                            <span v-else-if="saveStatus[siswa.id]?.[activeTab] === 'error'" class="flex items-center text-[10px] font-bold text-rose-500 bg-white/90 backdrop-blur px-2 py-0.5 rounded shadow-sm">
                                                <AppIcon name="exclamation-triangle" /> Gagal
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredStudents.length === 0">
                                <td colspan="3" class="py-12 text-center text-slate-500 bg-slate-50/50">
                                    <div class="text-3xl mb-2"><AppIcon name="search" /></div>
                                    <div class="text-xs font-bold">Tidak ada siswa yang cocok.</div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
            <!-- Bottom Sticky Info -->
            <div class="p-3 bg-white border-t border-slate-100 flex items-center justify-between shrink-0 absolute bottom-0 left-0 right-0 z-30 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] pointer-events-none">
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">
                        Input otomatis disimpan (Debounce 1 detik)
                    </p>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'

definePageMeta({
    layout: "walas",
    middleware: 'guru',
    title: 'Kokurikuler'
})



const windowWidth = ref(1024)
const isDesktop = computed(() => windowWidth.value >= 1280)
const activeTabMobile = ref('filter')
const mobileTabs = [
  { id: 'filter', title: 'Filter / Form', icon: 'funnel' },
  { id: 'flow', title: 'Data Workspace', icon: 'table-cells' }
]
const token = useCookie('auth_token')
const pending = ref(true)
const error = ref(null)
const pageData = ref(null)
const searchQuery = ref('')
const activeTab = ref(null)

const activePeriodeData = computed(() => {
    return pageData.value?.periodes?.find(t => t.id === activeTab.value)
})

// State for inputs: formKo[siswa_id][titimangsa_id] = "keterangan"
const formKo = ref({})
// saveStatus: 'idle', 'saving', 'saved'
const saveStatus = ref({})

// Debounce timer tracking: debounceTimers[siswa_id][titimangsa_id]
const debounceTimers = {}

const filteredStudents = computed(() => {
    if (!pageData.value || !pageData.value.data) return []
    if (!searchQuery.value) return pageData.value.data
    const q = searchQuery.value.toLowerCase()
    return pageData.value.data.filter(s => s.nama_lengkap && s.nama_lengkap.toLowerCase().includes(q))
})

const fetchData = async () => {
    pending.value = true
    error.value = null
    try {
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/guru/walas/kokurikuler`, {
            headers: {
                'Authorization': `Bearer ${token.value}`,
                'Accept': 'application/json'
            }
        })
        
        if (response.success) {
            // Initialize form state FIRST to prevent undefined errors in template
            const state = {}
            const statusState = {}
            if (response.data && response.periodes) {
                response.data.forEach(siswa => {
                    state[siswa.id] = {}
                    statusState[siswa.id] = {}
                    response.periodes.forEach(tm => {
                        state[siswa.id][tm.id] = siswa['ko_'+tm.id] || ''
                        statusState[siswa.id][tm.id] = 'idle'
                    })
                })
            }
            formKo.value = state
            saveStatus.value = statusState
            
            pageData.value = response
            if (response.periodes && response.periodes.length > 0 && !activeTab.value) {
                activeTab.value = response.periodes[0].id
            }
        } else {
            error.value = { message: response.message || 'Gagal memuat data.' }
        }
    } catch (e) {
        error.value = { message: e.data?.message || 'Terjadi kesalahan koneksi.' }
    } finally {
        pending.value = false
    }
}

onMounted(() => {
    if (typeof window !== 'undefined') {
        windowWidth.value = window.innerWidth
        window.addEventListener('resize', () => {
            windowWidth.value = window.innerWidth
            if (isDesktop.value) activeTabMobile.value = 'filter'
        })
    }
    fetchData()
})

const handleInput = (siswaId, tmId) => {
    if (debounceTimers[siswaId] && debounceTimers[siswaId][tmId]) {
        clearTimeout(debounceTimers[siswaId][tmId])
    }

    if (!debounceTimers[siswaId]) debounceTimers[siswaId] = {}
    
    saveStatus.value[siswaId][tmId] = 'saving'

    debounceTimers[siswaId][tmId] = setTimeout(() => {
        saveData(siswaId, tmId)
    }, 1000)
}

const saveData = async (siswaId, tmId) => {
    try {
        const keterangan = formKo.value[siswaId][tmId]
        
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/guru/walas/kokurikuler/store`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token.value}`,
                'Accept': 'application/json'
            },
            body: {
                siswa_id: siswaId,
                titimangsa_id: tmId,
                keterangan: keterangan
            }
        })

        if (response.success) {
            saveStatus.value[siswaId][tmId] = 'saved'
            setTimeout(() => {
                if(saveStatus.value[siswaId][tmId] === 'saved') saveStatus.value[siswaId][tmId] = 'idle'
            }, 2000)
        } else {
            saveStatus.value[siswaId][tmId] = 'idle'
            useNuxtApp().$swal.fire({
                title: 'Gagal',
                text: response.message || 'Gagal menyimpan.',
                icon: 'error',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })
        }
    } catch (e) {
        saveStatus.value[siswaId][tmId] = 'idle'
        useNuxtApp().$swal.fire({
            title: 'Gagal',
            text: e.data?.message || 'Terjadi kesalahan jaringan.',
            icon: 'error',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        })
    }
}

const saveKurikulum = async (kurikulumId) => {
    try {
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/guru/walas/kokurikuler/kurikulum`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token.value}`,
                'Accept': 'application/json'
            },
            body: {
                kurikulum_id: kurikulumId
            }
        })

        if (response.success) {
            useNuxtApp().$swal.fire({
                title: 'Berhasil',
                text: 'Kurikulum berhasil diubah!',
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })
        } else {
            useNuxtApp().$swal.fire({
                title: 'Gagal',
                text: response.message || 'Gagal merubah kurikulum.',
                icon: 'error',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })
        }
    } catch (e) {
        useNuxtApp().$swal.fire({
            title: 'Gagal',
            text: e.data?.message || 'Terjadi kesalahan jaringan.',
            icon: 'error',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        })
    }
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #94a3b8; }
</style>
