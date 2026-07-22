<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-2 grid grid-cols-2 gap-2 z-20">
        <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTabMobile = tab.id"
          :class="activeTabMobile === tab.id ? 'bg-gradient-to-br from-amber-500 to-amber-600 text-white shadow-md shadow-amber-500/20 ring-2 ring-amber-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="rounded-xl flex flex-col items-center justify-center py-2 px-1 transition-all active:scale-95">
          <AppIcon :name="tab.icon" class="w-6 h-6 mb-0.5 transition-transform" :class="activeTabMobile === tab.id ? 'scale-110' : ''" />
          <span class="text-[9px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
        </button>
      </div>

      <!-- Panel Dock Kiri (Form & Filter) -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all', activeTabMobile === 'form' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[60px]' : '']">
        
        <div class="p-6 shrink-0 z-10 relative">
          <div class="bg-gradient-to-r from-amber-600 to-orange-600 rounded-2xl p-5 border border-amber-500 shadow-sm relative overflow-hidden flex flex-col gap-4">
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0"><AppIcon name="briefcase" class="w-6 h-6" /></div>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-white">Struktur Kejuruan</h3>
                    <p class="text-[10px] text-amber-200 font-semibold uppercase mt-0.5">Plotting Mapel Kejuruan</p>
                </div>
            </div>
            
            <div class="absolute right-0 bottom-0 opacity-20 text-white pointer-events-none">
              <svg class="w-24 h-24 transform translate-x-6 translate-y-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path></svg>
            </div>

            <!-- Kurikulum & Tingkat Selector -->
            <div class="space-y-2 relative z-10">
                <select v-model="selectedKurikulumId" @change="fetchData" class="w-full px-3 py-2 rounded-xl bg-amber-900/40 border border-amber-500/50 text-sm font-bold text-white focus:ring-2 focus:ring-white/50 outline-none">
                    <option value="" disabled>-- Pilih Kurikulum --</option>
                    <option v-for="kur in kurikulums" :key="kur.id" :value="kur.id" class="text-slate-800">{{ kur.nama_kurikulum }}</option>
                </select>
                <div class="grid gap-1" :style="`grid-template-columns: repeat(${refTingkatKelas.length || 3}, minmax(0, 1fr))`">
                    <button v-for="tk in refTingkatKelas" :key="tk.kode" @click="tingkat = tk.kode" :class="tingkat === tk.kode ? 'bg-white text-amber-700 shadow-sm' : 'bg-amber-900/40 text-amber-200 hover:bg-amber-900/60'" class="py-1.5 rounded-lg font-black text-[10px] uppercase tracking-widest transition-all">{{ tk.nama }}</button>
                </div>
            </div>
          </div>
        </div>
        
        <div class="flex-1 overflow-y-auto custom-scrollbar p-5">
            <div v-if="!selectedKurikulumId" class="text-center p-6 border-2 border-dashed border-slate-200 rounded-2xl">
                <span class="text-3xl mb-2 block opacity-50"><AppIcon name="hand-raised" class="w-6 h-6" /></span>
                <p class="text-xs font-bold text-slate-500">Pilih kurikulum terlebih dahulu di atas.</p>
            </div>
            <form v-else @submit.prevent="saveData" class="space-y-4">
                
                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">{{ tingkat === 'X' ? 'Program Keahlian' : 'Konsentrasi Keahlian' }}</label>
                    <select v-model="formData.unit_id" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-bold text-slate-800 outline-none cursor-pointer">
                        <option value="" disabled>-- Pilih Unit --</option>
                        <option v-for="unit in dataUnit" :key="unit.id" :value="unit.id">{{ unit.nama_program || unit.nama_kejuruan }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Mata Pelajaran</label>
                    <select v-model="formData.mapel_id" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-bold text-slate-800 outline-none cursor-pointer">
                        <option value="" disabled>-- Pilih Mapel Kejuruan --</option>
                        <option v-for="mapel in availableMapels" :key="mapel.id" :value="mapel.id">{{ mapel.nama_mapel }}</option>
                    </select>
                    <p v-if="formData.unit_id && availableMapels.length === 0" class="text-[10px] text-rose-500 mt-1.5 font-bold ml-1">Semua mapel sudah diplot ke unit ini.</p>
                </div>

                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Jumlah Jam Pelajaran (JP)</label>
                    <input type="number" v-model="formData.jp" required min="1" max="50" placeholder="Contoh: 4" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-bold text-slate-800 outline-none">
                </div>
                
                <div class="pt-4 border-t border-slate-100 flex gap-3">
                    <button type="button" @click="resetForm" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">
                        Reset
                    </button>
                    <button type="submit" :disabled="isSaving || !formData.mapel_id || availableMapels.length === 0" class="flex-[2] py-3 bg-gradient-to-r from-amber-500 to-amber-600 text-white font-bold rounded-2xl shadow-lg shadow-amber-500/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 text-xs uppercase tracking-widest disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0">
                        <span v-if="isSaving" class="animate-spin"><AppIcon name="clock" class="w-6 h-6" /></span>
                        <span v-else><AppIcon name="plus" class="w-6 h-6" /></span> 
                        Tambahkan
                    </button>
                </div>
            </form>
        </div>
      </div>

      <!-- Panel Flow Kanan (Tabel) -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0', activeTabMobile === 'table' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[60px]' : '']">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
        <!-- Header Flow -->
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shrink-0 z-10 sticky top-0 bg-white/80 backdrop-blur-xl">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 shadow-sm border border-amber-100 flex items-center justify-center text-xl hidden sm:flex text-amber-500"><AppIcon name="cog-6-tooth" class="w-6 h-6" /></div>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-amber-700">Database Struktur Kejuruan</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Daftar mapel produktif Kelas {{ tingkat }}</p>
                </div>
            </div>
            <button @click="fetchData" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-slate-200 hover:text-slate-700 font-bold transition-colors" title="Refresh">
                🔄
            </button>
        </div>

        <!-- Table Container -->
        <div class="flex-1 overflow-y-auto custom-scrollbar relative bg-slate-50 p-4 sm:p-6">
            <!-- Loading State -->
            <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60 h-full">
                <div class="w-8 h-8 border-4 border-amber-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                <span class="text-xs font-black text-amber-500 uppercase tracking-widest">Memuat Data...</span>
            </div>

            <div v-else-if="!selectedKurikulumId" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60 h-full text-center">
                <div class="text-6xl mb-4"><AppIcon name="building-office" class="w-6 h-6" /></div>
                <span class="text-sm font-black text-slate-500 uppercase tracking-widest">Pilih Kurikulum Terlebih Dahulu</span>
            </div>

            <!-- Empty State -->
            <div v-else-if="dataUnit.length === 0" class="flex-grow flex items-center justify-center flex-col p-16 text-center h-full">
                <div class="text-6xl opacity-20 mb-4"><AppIcon name="archive-box-x-mark" class="w-6 h-6" /></div>
                <p class="text-sm font-bold text-slate-500">Belum ada unit keahlian.</p>
                <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-2 font-bold">Pastikan master Program/Konsentrasi Keahlian sudah terisi.</p>
            </div>

            <!-- Content Grouped by Unit Keahlian -->
            <div v-else class="space-y-6 max-w-4xl mx-auto">
                <div v-for="unit in dataUnit" :key="unit.id" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-5 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center font-black text-sm"><AppIcon name="wrench" class="w-6 h-6" /></span>
                            <div>
                                <p class="text-[9px] font-black uppercase text-slate-400 tracking-widest">{{ tingkat === 'X' ? 'Program Keahlian' : 'Konsentrasi Keahlian' }}</p>
                                <h4 class="font-black text-slate-800 text-sm uppercase tracking-wider mt-0.5">{{ unit.nama_program || unit.nama_kejuruan }}</h4>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="hidden md:table-row bg-white text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-slate-100">
                                    <th class="p-4 pl-6 w-16 text-center">No</th>
                                    <th class="p-4">Mata Pelajaran Produktif</th>
                                    <th class="p-4 text-center w-24">JP</th>
                                    <th class="p-4 text-center w-24">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm font-medium text-slate-700 md:divide-y md:divide-slate-50">
                                <tr v-if="unit.struktur_kejuruans.length === 0" class="bg-white">
                                    <td colspan="4" class="p-6 text-center text-xs font-bold text-slate-400">Belum ada mapel produktif di unit ini.</td>
                                </tr>
                                <tr v-for="(struk, index) in unit.struktur_kejuruans" :key="struk.id" class="hover:bg-slate-50/50 transition-colors bg-white group flex flex-col md:table-row border-b md:border-b-0 border-slate-100 p-4 md:p-0">
                                    <td class="p-0 md:p-4 md:pl-6 text-left md:text-center text-[11px] font-bold text-slate-400 flex items-center justify-between md:table-cell mb-2 md:mb-0">
                                        <span class="md:hidden font-black uppercase tracking-widest">No</span>
                                        <span>{{ index + 1 }}</span>
                                    </td>
                                    <td class="p-0 md:p-4 flex flex-col md:table-cell mb-2 md:mb-0">
                                        <span class="md:hidden text-[9px] font-black uppercase text-slate-400 tracking-widest mb-1">Mata Pelajaran Produktif</span>
                                        <div class="font-black text-slate-800">{{ struk.mapel?.nama_mapel }}</div>
                                        <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ struk.mapel?.kode_mapel }}</div>
                                    </td>
                                    <td class="p-0 md:p-4 text-left md:text-center flex items-center justify-between md:table-cell mb-3 md:mb-0">
                                        <span class="md:hidden text-[9px] font-black uppercase text-slate-400 tracking-widest">JP</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-[11px] font-black tracking-widest bg-amber-50 text-amber-600 border border-amber-200">
                                            {{ struk.jp }} JP
                                        </span>
                                    </td>
                                    <td class="p-0 md:p-4 text-center border-t border-slate-50 pt-3 md:border-t-0 md:pt-0 flex items-center justify-end md:table-cell">
                                        <div class="flex items-center justify-center opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                            <button @click="confirmDeleteStruktur(struk)" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 flex items-center justify-center transition-all shadow-sm" title="Hapus"><AppIcon name="trash" class="w-4 h-4" /></button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
              </div>
            </div>

        </div>
      </div>
    </div>

    <!-- Delete Modal -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade text-center">
            <div class="p-8">
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner border-4 border-white ring-4 ring-rose-50"><AppIcon name="exclamation-triangle" class="w-6 h-6" /></div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Hapus Data?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Anda yakin ingin menghapus mapel produktif:<br>
                    <span class="font-bold text-rose-600">{{ deleteTarget?.nama }}</span>?
                </p>
                
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isDeleteModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">Batal</button>
                    <button @click="executeDelete" class="flex-1 py-3 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        <span>Hapus</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed, watch } from 'vue'

definePageMeta({
  layout: 'kurikulum',
  middleware: 'kurikulum',
  title: 'Struktur Kurikulum Kejuruan'
})

// Responsiveness detector
const windowWidth = ref(1024) 
const isDesktop = computed(() => windowWidth.value >= 1280) // xl breakpoint

// Tabs for Mobile
const activeTabMobile = ref('form')
const mobileTabs = [
  { id: 'form', title: 'Plotting Mapel', icon: 'document-text' },
  { id: 'table', title: 'Database', icon: 'clipboard-document-list' }
]

const kurikulums = ref([])
const selectedKurikulumId = ref('')
const tingkat = ref('')
const mapels = ref([])
const dataUnit = ref([])
const allDataUnit = ref({ X: [], XI: [], XII: [] })  // Cache all tingkat
const refTingkatKelas = ref([])

const isLoading = ref(true)
const isSaving = ref(false)

const formData = reactive({
    unit_id: '',
    mapel_id: '',
    jp: 4
})

const resetForm = () => {
    formData.mapel_id = ''
    formData.jp = 4
}

watch(() => tingkat.value, () => {
    formData.unit_id = ''
    formData.mapel_id = ''
    // Switch from cache - no API call, no loading!
    if (allDataUnit.value[tingkat.value]?.length > 0) {
        dataUnit.value = allDataUnit.value[tingkat.value]
        formData.unit_id = dataUnit.value[0]?.id || ''
    } else {
        dataUnit.value = []
    }
})

// selectedKurikulumId is triggered via @change="fetchData" on the select element directly

const availableMapels = computed(() => {
    if (!formData.unit_id) return []
    const unit = dataUnit.value.find(u => u.id === formData.unit_id)
    if (!unit) return mapels.value
    
    const existingMapelIds = unit.struktur_kejuruans.map(s => s.mapel_id)
    return mapels.value.filter(m => !existingMapelIds.includes(m.id))
})

// Delete State
const isDeleteModalOpen = ref(false)
const deleteTarget = ref({ id: null, nama: '' })

const fetchReferensi = async () => {
    const token = useCookie('auth_token').value
    try {
        const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/referensi?jenis=tingkat_kelas`, {
            headers: { 'Authorization': `Bearer ${token}` }
        })
        if (res.data) {
            refTingkatKelas.value = res.data
            if (refTingkatKelas.value.length > 0) {
                tingkat.value = refTingkatKelas.value[0].kode
            }
        }
    } catch (error) {
        console.error('Error fetching tingkat kelas', error)
    }
}

const fetchData = async () => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        const url = `${import.meta.env.VITE_API_BASE_URL}/api/kurikulum/struktur-kejuruan` + (selectedKurikulumId.value ? `?kurikulum_id=${selectedKurikulumId.value}` : '')
        const response = await $fetch(url, {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            kurikulums.value = response.data.kurikulums
            selectedKurikulumId.value = response.data.selectedKurikulumId
            mapels.value = response.data.mapels
            
            // Cache all tingkat data
            allDataUnit.value = response.data.allDataUnit || { X: [], XI: [], XII: [] }
            
            // Set current tingkat from cache
            dataUnit.value = allDataUnit.value[tingkat.value] || []

            // Reset selection
            if (dataUnit.value.length > 0 && !formData.unit_id) {
                formData.unit_id = dataUnit.value[0].id;
            }
        }
    } catch (error) {
        console.error('Failed to fetch:', error)
        useSwal().toast(error.response?._data?.message || 'Gagal memuat data struktur kejuruan.', 'error')
    } finally {
        isLoading.value = false
    }
}

const saveData = async () => {
    if(!selectedKurikulumId.value || !formData.unit_id || !formData.mapel_id) return
    isSaving.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/kurikulum/struktur-kejuruan', {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` },
            body: {
                kurikulum_id: selectedKurikulumId.value,
                mapel_id: formData.mapel_id,
                tingkat: tingkat.value,
                jp: formData.jp,
                program_id: tingkat.value === 'X' ? formData.unit_id : null,
                konsentrasi_id: tingkat.value !== 'X' ? formData.unit_id : null,
            }
        })
        if (response.success) {
            useSwal().toast(response.message, 'success')
            resetForm()
            fetchData()
            if (!isDesktop.value) activeTabMobile.value = 'table'
        }
    } catch (error) {
        console.error('Add struktur failed:', error)
        useSwal().toast(error.response?._data?.message || 'Gagal menambahkan mapel produktif.', 'error')
    } finally {
        isSaving.value = false
    }
}

const confirmDeleteStruktur = (s) => {
    deleteTarget.value = { id: s.id, nama: s.mapel?.nama_mapel }
    isDeleteModalOpen.value = true
}

const executeDelete = async () => {
    if(!deleteTarget.value.id) return
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/kurikulum/struktur-kejuruan/${deleteTarget.value.id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            isDeleteModalOpen.value = false
            useSwal().toast(response.message, 'success')
            fetchData()
        }
    } catch (error) {
        console.error('Delete failed:', error)
        useSwal().toast('Gagal menghapus data.', 'error')
    }
}

onMounted(async () => {
    windowWidth.value = window.innerWidth
    window.addEventListener('resize', () => { windowWidth.value = window.innerWidth })
    
    if (isDesktop.value) {
        activeTabMobile.value = 'form'
    } else {
        activeTabMobile.value = 'table'
    }

    await fetchReferensi()
    await fetchData()
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #94a3b8;
}
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }

@keyframes slideUpFade {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-slideUpFade { animation: slideUpFade 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
</style>
