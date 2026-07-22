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

      <!-- Panel Dock Kiri -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all', activeTabMobile === 'info' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[60px]' : '']">
        
        <div class="p-6 shrink-0 z-10 relative">
          <div class="bg-gradient-to-r from-amber-600 to-orange-600 rounded-2xl p-5 border border-amber-500 shadow-sm relative overflow-hidden flex items-center gap-4">
            <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0 relative z-10"><AppIcon name="star" class="w-6 h-6" /></div>
            <div class="relative z-10">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">Standar Nilai</h3>
                <p class="text-[10px] text-amber-100 font-semibold uppercase mt-0.5">Petunjuk Pengaturan</p>
            </div>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto px-6 pb-6 space-y-6">
            <div class="bg-amber-50 border border-amber-100 p-4 rounded-2xl">
                <h4 class="text-xs font-black text-amber-800 uppercase tracking-widest mb-2">Cara Mengisi KKM:</h4>
                <ul class="text-[11px] text-amber-700 space-y-2 list-disc pl-4 font-medium leading-relaxed">
                    <li>Input nilai batas ketuntasan (KKM) pada kotak yang tersedia di tiap tingkat kelas.</li>
                    <li>Nilai harus berkisar antara <strong>0 hingga 100</strong>.</li>
                    <li>Tekan tombol <strong>Enter</strong> pada keyboard untuk menyimpan nilai tersebut.</li>
                    <li>Tanda centang hijau (✓) menandakan bahwa nilai telah berhasil tersimpan di sistem.</li>
                </ul>
            </div>

            <button @click="fetchData" class="w-full py-4 bg-amber-600 hover:bg-amber-700 active:scale-[0.98] rounded-2xl text-white shadow-lg shadow-amber-200 transition-all flex items-center justify-center gap-2 group">
                <span class="text-[11px] font-black uppercase tracking-widest group-hover:tracking-wider transition-all">Refresh Data</span>
                <span class="group-hover:rotate-180 transition-transform duration-500"><AppIcon name="arrow-path" class="w-6 h-6" /></span>
            </button>
        </div>
      </div>

      <!-- Main Content Flow -->
      <div class="flex-1 flex flex-col min-w-0 bg-slate-50 relative h-full" :class="activeTabMobile === 'flow' || isDesktop ? 'flex' : 'hidden'">
            
            <!-- Header Flow -->
            <div class="p-4 bg-white border-b border-slate-200 flex justify-between items-center gap-4 shrink-0 z-10 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 shadow-sm border border-amber-100 flex items-center justify-center text-xl hidden sm:flex text-amber-500"><AppIcon name="star" class="w-6 h-6" /></div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-amber-700">Kriteria Ketuntasan Minimal</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Kelola batas lulus per tingkat</p>
                    </div>
                </div>
            </div>

            <!-- Banner Read-Only -->
            <div v-if="!isLoading && !isActiveTahunAjaran && activeTahunAjaranId" class="bg-amber-50 border-b border-amber-200 px-6 py-3 flex items-center justify-center gap-2 shrink-0">
                <span class="text-amber-500"><AppIcon name="exclamation-triangle" class="w-6 h-6" /></span>
                <p class="text-xs font-bold text-amber-700 uppercase tracking-widest">Mode Read-Only: Tahun ajaran ini tidak aktif. Anda tidak dapat mengubah data.</p>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto custom-scrollbar p-4 md:p-6 lg:p-8">
                
                <!-- Loading State -->
                <div v-if="isLoading" class="flex flex-col items-center justify-center h-full">
                    <div class="w-10 h-10 border-4 border-amber-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</span>
                </div>

                <!-- Empty State (No Data) -->
                <div v-else-if="kkmData.length === 0" class="flex flex-col items-center justify-center h-full opacity-60">
                    <span class="text-6xl mb-4 grayscale opacity-50">📑</span>
                    <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest">Tidak ada data kurikulum</h3>
                </div>

                <!-- Data Grid -->
                <div v-else class="space-y-6 max-w-5xl mx-auto">
                    <div v-for="kur in kkmData" :key="kur.kurikulum_id" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                            <h4 class="font-black text-slate-700 text-sm uppercase tracking-widest">{{ kur.nama_kurikulum }}</h4>
                        </div>
                        
                        <div class="p-6 flex flex-wrap justify-center gap-8">
                            <div v-for="item in kur.tingkat" :key="item.tingkat" class="flex flex-col gap-2 w-full sm:w-72">
                                <label class="text-[11px] font-black text-slate-600 uppercase tracking-wider pl-1">Tingkat {{ item.tingkat }}</label>
                                <div class="relative">
                                    <input type="number" 
                                        v-model.number="item.nilai" 
                                        @change="saveKkm(kur.kurikulum_id, item.tingkat, item.nilai)"
                                        :disabled="!isActiveTahunAjaran"
                                        min="0" max="100" 
                                        placeholder="0" 
                                        class="w-full px-5 py-4 bg-slate-50 border-2 border-slate-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-2xl font-black text-slate-800 outline-none text-left disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-slate-100">
                                    
                                    <span v-if="isSaving[kur.kurikulum_id + '_' + item.tingkat]" class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 border-2 border-amber-400 border-t-transparent rounded-full animate-spin"></span>
                                    <span v-else-if="item.nilai !== null" class="absolute right-4 top-1/2 -translate-y-1/2 text-emerald-500 font-black text-xl">✓</span>
                                </div>
                                <p class="text-[9px] text-slate-400 text-left ml-1 font-bold mt-1">Tekan <kbd class="px-1 py-0.5 bg-slate-100 border border-slate-200 rounded">Enter</kbd> untuk simpan</p>
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
  middleware: 'kurikulum',
  title: 'Standar Nilai (KKM)'
})

const isLoading = ref(true)
const kkmData = ref([])
const listTahunAjaran = ref([])
const activeTahunAjaranId = ref('')
const activeTahunAjaran = ref(null)
const isSaving = ref({})

// Responsiveness
const windowWidth = ref(1024)
const isDesktop = computed(() => windowWidth.value >= 1280)
const activeTabMobile = ref('flow')
const mobileTabs = [
  { id: 'info', title: 'Petunjuk', icon: 'ℹ️' },
  { id: 'flow', title: 'Data KKM', icon: 'star' }
]

const isActiveTahunAjaran = computed(() => {
    if (!activeTahunAjaranId.value) return false
    const ta = listTahunAjaran.value.find(t => t.id === activeTahunAjaranId.value)
    return ta ? (ta.is_aktif === true || ta.is_aktif === 1) : false
})

const fetchTahunAjaran = async () => {
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/kurikulum/pengampu', {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            listTahunAjaran.value = response.tahun_ajarans || []
            
            if (response.active_tahun_ajaran_id) {
                activeTahunAjaranId.value = response.active_tahun_ajaran_id
            } else if (listTahunAjaran.value.length > 0) {
                activeTahunAjaranId.value = listTahunAjaran.value[0].id
            }

            activeTahunAjaran.value = listTahunAjaran.value.find(t => t.id === activeTahunAjaranId.value) || null
            await fetchData()
        } else {
            isLoading.value = false
        }
    } catch (error) {
        console.error('Failed to fetch tahun ajaran:', error)
        isLoading.value = false
    }
}

const fetchData = async () => {
    if (!activeTahunAjaranId.value) return
    
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/kurikulum/kkm?tahun_ajaran_id=${activeTahunAjaranId.value}`, {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            kkmData.value = response.data || []
        }
    } catch (error) {
        console.error('Failed to fetch KKM:', error)
        useSwal().toast('Gagal memuat standar nilai.', 'error')
    } finally {
        isLoading.value = false
    }
}

const saveKkm = async (kurikulum_id, tingkat, nilai) => {
    if (nilai === null || nilai === '' || nilai < 0 || nilai > 100) {
        useSwal().toast('Nilai KKM harus antara 0 dan 100', 'error')
        return
    }
    
    if (!activeTahunAjaranId.value) {
        useSwal().toast('Tahun Ajaran Aktif tidak ditemukan!', 'error')
        return
    }

    const key = kurikulum_id + '_' + tingkat
    isSaving.value[key] = true
    
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/kurikulum/kkm', {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` },
            body: { 
                tahun_ajaran_id: activeTahunAjaranId.value,
                kurikulum_id,
                tingkat,
                nilai
            }
        })
        if (response.success) {
            useSwal().toast(`KKM Tingkat ${tingkat} tersimpan.`, 'success')
        }
    } catch (error) {
        console.error('Save failed:', error)
        useSwal().toast('Gagal menyimpan nilai KKM.', 'error')
    } finally {
        isSaving.value[key] = false
    }
}

onMounted(() => {
    windowWidth.value = window.innerWidth
    window.addEventListener('resize', () => { windowWidth.value = window.innerWidth })
    
    fetchTahunAjaran()
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
  from { opacity: 0; transform: translate(-50%, 20px); }
  to { opacity: 1; transform: translate(-50%, 0); }
}
.animate-slideUp { animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
