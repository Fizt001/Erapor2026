<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-2 grid grid-cols-2 gap-2 z-20">
        <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTabMobile = tab.id"
          :class="activeTabMobile === tab.id ? 'bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-md shadow-indigo-500/20 ring-2 ring-indigo-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="rounded-xl flex flex-col items-center justify-center py-2 px-1 transition-all active:scale-95">
          <span class="text-lg mb-0.5 transition-transform" :class="activeTabMobile === tab.id ? 'scale-110' : ''">{{ tab.icon }}</span>
          <span class="text-[9px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
        </button>
      </div>

      <!-- Panel Dock Kiri (Filter) -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all', activeTabMobile === 'filter' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[60px]' : '']">
        
        <div class="p-6 shrink-0 z-10 relative">
          <div class="bg-gradient-to-r from-indigo-600 to-blue-700 rounded-2xl p-5 border border-indigo-500 shadow-sm relative overflow-hidden flex items-center gap-4">
            <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0 relative z-10">👩‍🏫</div>
            <div class="relative z-10">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">Filter Plotting</h3>
                <p class="text-[10px] text-indigo-100 font-semibold uppercase mt-0.5">Pilih Kriteria Kelas</p>
            </div>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto px-6 pb-6 space-y-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pilih Kurikulum</label>
                    <select v-model="filterKurikulum" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold text-slate-700 outline-none">
                        <option value="">Semua Kurikulum</option>
                        <option v-for="kur in listKurikulum" :key="kur.id" :value="kur.id">{{ kur.nama_kurikulum }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pilih Tingkat</label>
                    <select v-model="filterTingkat" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold text-slate-700 outline-none">
                        <option value="">Semua Tingkat</option>
                        <option v-for="tk in refTingkat" :key="tk.kode" :value="tk.kode">{{ tk.nama }}</option>
                    </select>
                </div>
            </div>

            <button @click="fetchData" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] rounded-2xl text-white shadow-lg shadow-indigo-200 transition-all flex items-center justify-center gap-2 group">
                <span class="text-[11px] font-black uppercase tracking-widest group-hover:tracking-wider transition-all">Refresh Data</span>
                <span class="group-hover:rotate-180 transition-transform duration-500">🔄</span>
            </button>
        </div>
      </div>

      <!-- Main Content Flow -->
      <div class="flex-1 flex flex-col min-w-0 bg-slate-50 relative h-full" :class="activeTabMobile === 'flow' || isDesktop ? 'flex' : 'hidden'">
            
            <!-- Header Flow -->
            <div class="p-4 bg-white border-b border-slate-200 flex justify-between items-center gap-4 shrink-0 z-10 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 shadow-sm border border-indigo-100 flex items-center justify-center text-xl hidden sm:flex text-indigo-500">👩‍🏫</div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-indigo-700">Wali Kelas</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Kelola penugasan wali kelas</p>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto custom-scrollbar p-4 md:p-6 lg:p-8">
                
                <!-- Loading State -->
                <div v-if="isLoading" class="flex flex-col items-center justify-center h-full">
                    <div class="w-10 h-10 border-4 border-indigo-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</span>
                </div>

                <!-- Empty State (No Data) -->
                <div v-else-if="filteredKelas.length === 0" class="flex flex-col items-center justify-center h-full opacity-60">
                    <span class="text-6xl mb-4 grayscale opacity-50">📭</span>
                    <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest">Tidak ada kelas yang ditemukan</h3>
                </div>

                <!-- Data Grid -->
                <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 items-start">
                    
                    <div v-for="k in filteredKelas" :key="k.id" class="group relative bg-white rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden border border-slate-200/60 hover:border-indigo-300">
                        <!-- Decorative Header -->
                        <div class="h-2 w-full bg-gradient-to-r from-indigo-500 to-blue-500 absolute top-0 left-0"></div>
                        
                        <div class="p-6 flex flex-col h-full gap-5 mt-2">
                            <div class="flex justify-between items-start gap-4">
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-black text-slate-800 text-xl tracking-tight truncate group-hover:text-indigo-700 transition-colors">
                                        {{ k.tingkat }} {{ k.nama_kelas }}
                                    </h4>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1.5 truncate flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                                        {{ k.kurikulum?.nama_kurikulum || 'Belum Diatur' }}
                                    </p>
                                </div>
                                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-xl shadow-inner border border-indigo-100 shrink-0 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                    🏫
                                </div>
                            </div>

                            <div class="flex flex-col gap-3 pt-5 border-t border-slate-100/80 mt-auto">
                                <div>
                                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest flex items-center gap-1.5 mb-2">
                                        👩‍🏫 Pilih Wali Kelas
                                    </label>
                                    <div class="relative group/select">
                                        <select v-model="form[k.id]" class="w-full px-4 py-3.5 text-xs font-bold text-slate-700 rounded-xl border-2 border-slate-200/70 bg-slate-50 hover:bg-white focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none appearance-none cursor-pointer transition-all pr-10">
                                            <option value="" disabled>-- Pilih Guru --</option>
                                            <option v-for="g in listGuru" :key="g.id" :value="g.id">{{ g.name }}</option>
                                        </select>
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-xs group-hover/select:text-indigo-500 transition-colors">▼</span>
                                    </div>
                                </div>
                                
                                <button @click="saveWaliKelas(k.id)" class="mt-1 w-full py-3.5 bg-slate-800 hover:bg-indigo-600 active:bg-indigo-700 text-white text-[11px] font-black uppercase tracking-widest rounded-xl transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed shadow-md hover:shadow-indigo-500/30 flex items-center justify-center gap-2 group/btn" :disabled="isSaving[k.id]">
                                    <span v-if="isSaving[k.id]" class="animate-spin text-sm">⏳</span>
                                    <span v-else class="text-sm group-hover/btn:-translate-y-0.5 transition-transform">💾</span>
                                    <span>{{ isSaving[k.id] ? 'Menyimpan...' : 'Simpan' }}</span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

definePageMeta({
  layout: 'kurikulum',
  title: 'Wali Kelas'
})

const isLoading = ref(false)
const listKelas = ref([])
const listGuru = ref([])
const refTingkat = ref([])
const filterTingkat = ref('')
const filterKurikulum = ref('')
const listKurikulum = ref([])
const activeTahunAjaran = ref(null)
const isSaving = ref({})
const form = ref({})

// Responsiveness
const windowWidth = ref(1024)
const isDesktop = computed(() => windowWidth.value >= 1280)
const activeTabMobile = ref('filter')
const mobileTabs = [
  { id: 'filter', title: 'Filter', icon: '🎛️' },
  { id: 'flow', title: 'Daftar Kelas', icon: '👩‍🏫' }
]

const { fetchReferensi } = useReferensi()

const filteredKelas = computed(() => {
    return listKelas.value.filter(k => {
        const matchTingkat = !filterTingkat.value || k.tingkat === filterTingkat.value
        const matchKurikulum = !filterKurikulum.value || k.kurikulum_id === filterKurikulum.value
        return matchTingkat && matchKurikulum
    })
})

const fetchData = async () => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        const [resTingkat, resKurikulum, resWaliKelas] = await Promise.all([
            fetchReferensi('TINGKAT_KELAS'),
            $fetch('http://localhost:8000/api/kurikulum/titimangsa', {
                headers: { Authorization: `Bearer ${token}` }
            }),
            $fetch('http://localhost:8000/api/kurikulum/wali-kelas', {
                headers: { Authorization: `Bearer ${token}` }
            })
        ])

        refTingkat.value = resTingkat || []
        listKurikulum.value = resKurikulum.data?.kurikulums || []
        
        listKelas.value = resWaliKelas.data || []
        listGuru.value = resWaliKelas.gurus || []

        if (resWaliKelas.tahun_ajaran && resWaliKelas.active_tahun_ajaran_id) {
            activeTahunAjaran.value = resWaliKelas.tahun_ajaran.find(t => t.id === resWaliKelas.active_tahun_ajaran_id)
        }
        
        listKelas.value.forEach(k => {
            form.value[k.id] = k.wali_kelas?.guru_id || ''
        })
        
        if (!isDesktop.value) activeTabMobile.value = 'flow'
    } catch (error) {
        console.error('Failed to fetch data:', error)
    } finally {
        isLoading.value = false
    }
}

const saveWaliKelas = async (kelasId) => {
    const guruId = form.value[kelasId]
    if (!guruId) {
        useSwal().toast('Pilih guru terlebih dahulu', 'error')
        return
    }

    isSaving.value[kelasId] = true
    const token = useCookie('auth_token').value
    
    try {
        const response = await $fetch('http://localhost:8000/api/kurikulum/wali-kelas', {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` },
            body: {
                kelas_id: kelasId,
                guru_id: guruId
            }
        })

        if (response.success) {
            useSwal().toast('Wali kelas berhasil disimpan', 'success')
            const kIdx = listKelas.value.findIndex(k => k.id === kelasId)
            if (kIdx !== -1) {
                if (!listKelas.value[kIdx].wali_kelas) {
                    listKelas.value[kIdx].wali_kelas = {}
                }
                listKelas.value[kIdx].wali_kelas.guru_id = guruId
            }
        }
    } catch (error) {
        console.error('Failed to save:', error)
        useSwal().toast('Gagal menyimpan wali kelas', 'error')
    } finally {
        isSaving.value[kelasId] = false
    }
}

onMounted(() => {
    windowWidth.value = window.innerWidth
    window.addEventListener('resize', () => { windowWidth.value = window.innerWidth })
    
    if (isDesktop.value) {
        activeTabMobile.value = 'filter'
    }
    
    fetchData()
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 20px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #94a3b8;
}

@keyframes slideUp {
    from { transform: translate(-50%, 100%); opacity: 0; }
    to { transform: translate(-50%, 0); opacity: 1; }
}
.animate-slideUp {
    animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>