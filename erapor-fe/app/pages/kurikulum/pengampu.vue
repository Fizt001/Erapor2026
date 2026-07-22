<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-2 grid grid-cols-2 gap-2 z-20">
        <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTabMobile = tab.id"
          :class="activeTabMobile === tab.id ? 'bg-gradient-to-br from-amber-500 to-amber-600 text-white shadow-md shadow-amber-500/20 ring-2 ring-amber-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="rounded-xl flex flex-col items-center justify-center py-2 px-1 transition-all active:scale-95">
          <span class="text-lg mb-0.5 transition-transform" :class="activeTabMobile === tab.id ? 'scale-110' : ''">{{ tab.icon }}</span>
          <span class="text-[9px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
        </button>
      </div>

      <!-- Panel Dock Kiri -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all', activeTabMobile === 'filter' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[60px]' : '']">
        
        <div class="p-6 shrink-0 z-10 relative">
          <div class="bg-gradient-to-r from-amber-600 to-orange-600 rounded-2xl p-5 border border-amber-500 shadow-sm relative overflow-hidden flex items-center gap-4">
            <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0 relative z-10">🎛️</div>
            <div class="relative z-10">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">Filter Plotting</h3>
                <p class="text-[10px] text-amber-100 font-semibold uppercase mt-0.5">Pilih Kriteria Mapel</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-20 text-white">
              <svg class="w-24 h-24 transform translate-x-6 translate-y-6" fill="currentColor" viewBox="0 0 24 24"><path d="M3 4c0-1.1.9-2 2-2h14c1.1 0 2 .9 2 2v16c0 1.1-.9 2-2 2H5c-1.1 0-2-.9-2-2V4zm2 0v16h14V4H5zm2 4h10v2H7V8zm0 4h10v2H7v-2zm0 4h7v2H7v-2z"/></svg>
            </div>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto px-6 pb-6 space-y-6">
            
            <div class="flex bg-slate-100/80 p-1.5 rounded-2xl border border-slate-200 shadow-inner w-full">
                <button @click="activeKategori = 'umum'; fetchPengampu()" 
                    class="flex-1 text-center py-2.5 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all duration-300"
                    :class="activeKategori === 'umum' ? 'bg-white text-amber-600 shadow-sm ring-1 ring-slate-200/50' : 'text-slate-400 hover:text-slate-600'">
                    Unit Umum
                </button>
                <button @click="activeKategori = 'kejuruan'; fetchPengampu()" 
                    class="flex-1 text-center py-2.5 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all duration-300"
                    :class="activeKategori === 'kejuruan' ? 'bg-white text-amber-600 shadow-sm ring-1 ring-slate-200/50' : 'text-slate-400 hover:text-slate-600'">
                    Kejuruan
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pilih Kurikulum</label>
                    <select v-model="selectedKurikulumId" @change="fetchPengampu" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-bold text-slate-700 outline-none">
                        <option value="">-- Pilih Kurikulum --</option>
                        <option v-for="kur in listKurikulum" :key="kur.id" :value="kur.id">{{ kur.nama_kurikulum }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pilih Tingkat</label>
                    <select v-model="selectedTingkat" @change="fetchPengampu" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-bold text-slate-700 outline-none">
                        <option v-for="tk in refTingkatKelas" :key="tk.kode" :value="tk.kode">{{ tk.nama }}</option>
                    </select>
                </div>
            </div>

            <button @click="fetchPengampu" class="w-full py-4 bg-amber-600 hover:bg-amber-700 active:scale-[0.98] rounded-2xl text-white shadow-lg shadow-amber-200 transition-all flex items-center justify-center gap-2 group">
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
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 shadow-sm border border-amber-100 flex items-center justify-center text-xl hidden sm:flex text-amber-500">👨‍🏫</div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-amber-700">Plot Guru Mapel</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Tugaskan guru ke dalam struktur</p>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto custom-scrollbar p-4 md:p-6 lg:p-8">
                
                <!-- Loading State -->
                <div v-if="isLoading" class="flex flex-col items-center justify-center h-full">
                    <div class="w-10 h-10 border-4 border-amber-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</span>
                </div>

                <!-- Empty State (No Filter) -->
                <div v-else-if="!selectedKurikulumId" class="flex flex-col items-center justify-center h-full opacity-60">
                    <span class="text-6xl mb-4 grayscale opacity-50">📑</span>
                    <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest text-center">Silakan Pilih Kurikulum Terlebih Dahulu</h3>
                </div>

                <!-- Empty State (No Data) -->
                <div v-else-if="strukturs.length === 0 || kelases.length === 0" class="flex flex-col items-center justify-center h-full opacity-60">
                    <span class="text-6xl mb-4 grayscale opacity-50">📭</span>
                    <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest">Struktur/Kelas Belum Tersedia</h3>
                </div>

                <!-- Data Grid -->
                <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-3 gap-6 items-start">
                    
                    <div v-for="struktur in strukturs" :key="struktur.id" class="bg-white border border-slate-200 rounded-2xl shadow-sm flex flex-col overflow-hidden hover:shadow-md transition-all">
                        
                        <!-- Card Header -->
                        <div class="p-4 bg-slate-50/80 border-b border-slate-100 shrink-0">
                            <div class="flex justify-between items-start mb-2">
                                <div class="text-[10px] font-black tracking-widest text-amber-500 uppercase">{{ struktur.mapel?.kode_mapel }}</div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 text-[9px] font-black text-amber-700 bg-white border border-amber-100 rounded-md shadow-sm">
                                        {{ struktur.jp }} JP
                                    </span>
                                </div>
                            </div>
                            <h4 class="font-black text-slate-800 text-xs leading-snug uppercase tracking-wide">{{ struktur.mapel?.nama_mapel }}</h4>
                            <div class="mt-2 text-[10px] text-slate-400 font-bold tracking-widest flex items-center gap-2">
                                <span>{{ activeKategori === 'umum' ? 'UMUM' : (struktur.program?.nama_program || 'KEJURUAN') }}</span>
                            </div>
                        </div>

                        <!-- Class List -->
                        <div class="p-4 flex-1 flex flex-col gap-4">
                            <div v-for="kelas in kelases" :key="kelas.id" class="border border-slate-100 rounded-xl p-3 bg-white hover:border-amber-100 transition-colors shadow-sm">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-[11px] font-black uppercase tracking-widest text-slate-700">{{ kelas.nama_kelas }}</span>
                                    <span class="text-[9px] font-black tracking-widest px-2 py-1 rounded-md" 
                                        :class="getSisaJp(struktur, kelas.id) <= 0 ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-rose-50 text-rose-600 border border-rose-100'">
                                        {{ getSisaJp(struktur, kelas.id) <= 0 ? '✓ Tuntas' : 'Sisa ' + getSisaJp(struktur, kelas.id) + ' JP' }}
                                    </span>
                                </div>

                                <!-- Existing Pengampus -->
                                <div class="space-y-1.5 mb-2">
                                    <div v-for="p in getPengampus(struktur, kelas.id)" :key="p.id" class="flex justify-between items-center bg-slate-50 border border-slate-100 p-2 rounded-lg text-[10px] group hover:border-amber-100 transition-colors">
                                        <div class="flex flex-col truncate pr-2">
                                            <span class="font-black text-slate-800 uppercase truncate" :title="p.guru?.name">{{ p.guru?.name }}</span>
                                            <span class="text-[8px] text-amber-600 font-black tracking-widest">{{ p.jp }} JAM</span>
                                        </div>
                                        <button @click="confirmDelete(p.id)" class="text-slate-300 hover:text-rose-600 p-1.5 bg-white rounded-md border border-slate-100 shadow-sm transition-colors active:scale-90 shrink-0" title="Hapus Guru">
                                            <svg viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Add Form -->
                                <div v-if="getSisaJp(struktur, kelas.id) > 0" class="flex items-center gap-1.5 pt-2 border-t border-dashed border-slate-200">
                                    <select v-model="formAssign[struktur.id + '_' + kelas.id].guru" class="flex-1 py-1.5 px-2 text-[9px] font-bold rounded-lg border-slate-200 bg-slate-50 cursor-pointer min-w-0 focus:ring-2 focus:ring-amber-500 outline-none border">
                                        <option value="">+ Guru</option>
                                        <option v-for="g in gurus" :key="g.id" :value="g.id">{{ g.name }}</option>
                                    </select>
                                    <input type="number" min="1" :max="getSisaJp(struktur, kelas.id)" v-model="formAssign[struktur.id + '_' + kelas.id].jp" class="w-12 py-1.5 px-2 text-[10px] font-bold rounded-lg border border-slate-200 bg-white text-center focus:ring-2 focus:ring-amber-500 outline-none shrink-0" />
                                    <button @click="assignGuru(struktur, kelas.id)" class="w-7 h-7 rounded-lg bg-amber-600 text-white flex items-center justify-center hover:bg-amber-700 active:scale-90 transition-all shrink-0 disabled:opacity-50" :disabled="isSaving[struktur.id + '_' + kelas.id]">
                                        <span v-if="isSaving[struktur.id + '_' + kelas.id]" class="w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                                        <span v-else>+</span>
                                    </button>
                                </div>
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
import { ref, onMounted, computed, reactive } from 'vue'

definePageMeta({
  layout: 'kurikulum',
  title: 'Plot Guru Mapel'
})

const isLoading = ref(false)
const listKurikulum = ref([])
const refTingkatKelas = ref([])
const titimangsas = ref([])
const strukturs = ref([])
const kelases = ref([])
const gurus = ref([])
const activeTahunAjaran = ref(null)

const activeKategori = ref('umum')
const selectedKurikulumId = ref('')
const selectedTingkat = ref('')

const isSaving = ref({})
const formAssign = ref({})

// Responsiveness
const windowWidth = ref(1024)
const isDesktop = computed(() => windowWidth.value >= 1280)
const activeTabMobile = ref('filter')
const mobileTabs = [
  { id: 'filter', title: 'Filter', icon: '🎛️' },
  { id: 'flow', title: 'Plotting', icon: '👨‍🏫' }
]

const { fetchReferensi } = useReferensi()

const fetchInitial = async () => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        const [resTingkat, resKurikulum] = await Promise.all([
            fetchReferensi('TINGKAT_KELAS'),
            $fetch(import.meta.env.VITE_API_BASE_URL + '/api/kurikulum/titimangsa', {
                headers: { Authorization: `Bearer ${token}` }
            })
        ])

        refTingkatKelas.value = resTingkat || []
        if (refTingkatKelas.value.length > 0 && !selectedTingkat.value) {
            selectedTingkat.value = refTingkatKelas.value[0].kode
        }
        
        listKurikulum.value = resKurikulum.data?.kurikulums || []
        if (listKurikulum.value.length > 0) {
            selectedKurikulumId.value = listKurikulum.value[0].id
            await fetchPengampu()
        }
    } catch (error) {
        console.error('Failed to fetch initial data:', error)
    } finally {
        isLoading.value = false
    }
}

const fetchPengampu = async () => {
    if (!selectedKurikulumId.value || !selectedTingkat.value) return
    
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/kurikulum/pengampu?kurikulum_id=${selectedKurikulumId.value}&tingkat=${selectedTingkat.value}&kategori=${activeKategori.value}`, {
            headers: { Authorization: `Bearer ${token}` }
        })
        
        titimangsas.value = response.data?.titimangsas || []
        strukturs.value = response.data?.strukturs || []
        kelases.value = response.data?.kelases || []
        gurus.value = response.gurus || []
        
        if (response.tahun_ajarans && response.active_tahun_ajaran_id) {
            activeTahunAjaran.value = response.tahun_ajarans.find(t => t.id === response.active_tahun_ajaran_id)
        }
        
        strukturs.value.forEach(s => {
            getRelevantClasses(s).forEach(k => {
                const key = s.id + '_' + k.id
                if (!formAssign.value[key]) {
                    formAssign.value[key] = { guru: '', jp: '' }
                }
            })
        })
        
        useSwal().toast('Data berhasil dimuat', 'success')
        
        // Auto switch to flow tab on mobile after fetch if user was on filter
        if (!isDesktop.value) activeTabMobile.value = 'flow'
        
    } catch (error) {
        console.error('Failed to fetch pengampu:', error)
        useSwal().toast('Gagal memuat data', 'error')
    } finally {
        isLoading.value = false
    }
}

const getRelevantClasses = (struktur) => {
    return kelases.value
}

const getPengampus = (struktur, kelasId) => {
    if (!struktur.pengampus) return []
    return struktur.pengampus.filter(p => p.kelas_id === kelasId)
}

const getSisaJp = (struktur, kelasId) => {
    const assignedJp = getPengampus(struktur, kelasId).reduce((sum, p) => sum + parseInt(p.jp || 0), 0)
    return Math.max(0, parseInt(struktur.jp || 0) - assignedJp)
}

const assignGuru = async (struktur, kelasId) => {
    const key = struktur.id + '_' + kelasId
    const form = formAssign.value[key]
    
    if (!form.guru || !form.jp) {
        useSwal().toast('Guru dan JP harus diisi', 'error')
        return
    }

    const maxJp = getSisaJp(struktur, kelasId)
    if (form.jp > maxJp) {
        useSwal().toast(`Maksimal JP tersisa adalah ${maxJp}`, 'error')
        return
    }

    isSaving.value[key] = true
    const token = useCookie('auth_token').value
    
    try {
        const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/kurikulum/pengampu', {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` },
            body: {
                kelas_id: kelasId,
                struktur_id: struktur.id,
                jenis: activeKategori.value,
                guru_id: form.guru,
                jp: form.jp
            }
        })

        if (res.success) {
            useSwal().toast('Berhasil menugaskan guru', 'success')
            formAssign.value[key] = { guru: '', jp: '' }
            await fetchPengampu()
        }
    } catch (error) {
        console.error('Failed to assign:', error)
        useSwal().toast('Gagal menugaskan guru', 'error')
    } finally {
        isSaving.value[key] = false
    }
}

const confirmDelete = async (id) => {
    const isConfirmed = await useSwal().fire({
        title: 'Hapus Plot Guru?',
        text: 'Apakah Anda yakin ingin menghapus tugas mengajar ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-3xl',
            confirmButton: 'rounded-xl text-sm font-bold px-6',
            cancelButton: 'rounded-xl text-sm font-bold px-6'
        }
    }).then(result => result.isConfirmed)

    if (!isConfirmed) return

    const token = useCookie('auth_token').value
    try {
        const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/kurikulum/pengampu/${id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })

        if (res.success) {
            useSwal().toast('Berhasil menghapus tugas', 'success')
            await fetchPengampu()
        }
    } catch (error) {
        console.error('Failed to delete:', error)
        useSwal().toast('Gagal menghapus tugas', 'error')
    }
}

onMounted(() => {
    windowWidth.value = window.innerWidth
    window.addEventListener('resize', () => { windowWidth.value = window.innerWidth })
    
    if (isDesktop.value) {
        activeTabMobile.value = 'filter'
    }
    
    fetchInitial()
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