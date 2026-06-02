<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Standar Nilai (KKM)</h2>
        <p class="text-sm font-semibold text-slate-500 mt-1">Kelola Kriteria Ketuntasan Minimal per tingkat dan kurikulum.</p>
      </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col min-h-[500px]">
        <div class="p-6 md:p-8 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white shadow-sm border border-slate-200 flex items-center justify-center text-2xl">💯</div>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-slate-700">Tahun Ajaran</h3>
                    <select v-model="activeTahunAjaranId" @change="fetchData" class="mt-1 px-3 py-1.5 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-xs font-bold text-slate-700 outline-none min-w-[200px]">
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        <option v-for="ta in listTahunAjaran" :key="ta.id" :value="ta.id">{{ ta.tahun }} {{ ta.is_aktif ? '(Aktif)' : '' }}</option>
                    </select>
                </div>
            </div>
            
            <button @click="fetchData" class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-slate-200 transition-colors" title="Refresh">
                🔄
            </button>
        </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60">
            <div class="w-8 h-8 border-4 border-slate-400 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</span>
        </div>

        <!-- Banner Read-Only -->
        <div v-if="!isLoading && !isActiveTahunAjaran && activeTahunAjaranId" class="bg-amber-50 border-y border-amber-200 px-6 py-3 flex items-center justify-center gap-2">
            <span class="text-amber-500">⚠️</span>
            <p class="text-xs font-bold text-amber-700 uppercase tracking-widest">Mode Read-Only: Tahun ajaran ini tidak aktif. Anda tidak dapat mengubah data.</p>
        </div>

        <div v-if="!isLoading" class="flex-grow p-6 space-y-8">
            <div v-for="kur in kkmData" :key="kur.kurikulum_id" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                    <h4 class="font-black text-slate-700 text-sm uppercase tracking-widest">{{ kur.nama_kurikulum }}</h4>
                </div>
                
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div v-for="item in kur.tingkat" :key="item.tingkat" class="flex flex-col gap-2">
                        <label class="text-xs font-bold text-slate-600 uppercase tracking-wider">Tingkat {{ item.tingkat }}</label>
                        <div class="relative">
                            <input type="number" 
                                v-model.number="item.nilai" 
                                @change="saveKkm(kur.kurikulum_id, item.tingkat, item.nilai)"
                                :disabled="!isActiveTahunAjaran"
                                min="0" max="100" 
                                placeholder="0" 
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-xl font-black text-slate-800 outline-none text-center disabled:opacity-50 disabled:cursor-not-allowed">
                            
                            <span v-if="isSaving[kur.kurikulum_id + '_' + item.tingkat]" class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 border-2 border-indigo-400 border-t-transparent rounded-full animate-spin"></span>
                            <span v-else-if="item.nilai !== null" class="absolute right-3 top-1/2 -translate-y-1/2 text-emerald-500 font-black">✓</span>
                        </div>
                        <p class="text-[10px] text-slate-400 text-center font-bold">Tekan <kbd class="px-1 py-0.5 bg-slate-100 border border-slate-200 rounded">Enter</kbd> untuk simpan</p>
                    </div>
                </div>
            </div>
            
            <div v-if="kkmData.length === 0" class="flex items-center justify-center flex-col p-16 text-center">
                <div class="text-6xl opacity-20 mb-4">📑</div>
                <p class="text-sm font-bold text-slate-500">Tidak ada data kurikulum.</p>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div v-if="showToast" class="fixed bottom-10 left-1/2 -translate-x-1/2 bg-slate-900/95 backdrop-blur-md text-white px-6 py-4 rounded-full shadow-2xl flex items-center gap-4 z-[100] animate-slideUp">
      <div :class="toastType === 'success' ? 'from-emerald-400 to-emerald-500' : 'from-rose-400 to-rose-500'" class="w-8 h-8 bg-gradient-to-br rounded-full flex items-center justify-center text-white text-sm shadow-inner shadow-white/20">
          {{ toastType === 'success' ? '✓' : '✕' }}
      </div>
      <p class="font-bold text-sm tracking-wide pr-2">{{ toastMessage }}</p>
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
const isSaving = ref({})

const isActiveTahunAjaran = computed(() => {
    if (!activeTahunAjaranId.value) return false
    const ta = listTahunAjaran.value.find(t => t.id === activeTahunAjaranId.value)
    return ta ? (ta.is_aktif === true || ta.is_aktif === 1) : false
})

const fetchTahunAjaran = async () => {
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch('http://localhost:8000/api/admin/tahun-ajaran', {
            headers: { Authorization: `Bearer ${token}` }
        })
        listTahunAjaran.value = response.data || []
        
        // Auto-select active year if available
        const active = listTahunAjaran.value.find(ta => ta.is_aktif === true || ta.is_aktif === 1)
        if (active) {
            activeTahunAjaranId.value = active.id
        } else if (listTahunAjaran.value.length > 0) {
            activeTahunAjaranId.value = listTahunAjaran.value[0].id
        }
        
        if (activeTahunAjaranId.value) {
            fetchData()
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
        const response = await $fetch(`http://localhost:8000/api/kurikulum/kkm?tahun_ajaran_id=${activeTahunAjaranId.value}`, {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            kkmData.value = response.data || []
        }
    } catch (error) {
        console.error('Failed to fetch KKM:', error)
        displayToast('Gagal memuat standar nilai.', 'error')
    } finally {
        isLoading.value = false
    }
}

const saveKkm = async (kurikulum_id, tingkat, nilai) => {
    if (nilai === null || nilai === '' || nilai < 0 || nilai > 100) {
        displayToast('Nilai KKM harus antara 0 dan 100', 'error')
        return
    }
    
    if (!activeTahunAjaranId.value) {
        displayToast('Tahun Ajaran Aktif tidak ditemukan!', 'error')
        return
    }

    const key = kurikulum_id + '_' + tingkat
    isSaving.value[key] = true
    
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch('http://localhost:8000/api/kurikulum/kkm', {
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
            displayToast(`KKM Tingkat ${tingkat} tersimpan.`, 'success')
        }
    } catch (error) {
        console.error('Save failed:', error)
        displayToast('Gagal menyimpan nilai KKM.', 'error')
    } finally {
        isSaving.value[key] = false
    }
}

const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('success')

const displayToast = (msg, type = 'success') => {
    toastMessage.value = msg
    toastType.value = type
    showToast.value = true
    setTimeout(() => { showToast.value = false }, 3500)
}

onMounted(() => {
    fetchTahunAjaran()
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }

@keyframes slideUp {
  from { opacity: 0; transform: translate(-50%, 20px); }
  to { opacity: 1; transform: translate(-50%, 0); }
}
.animate-slideUp { animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
