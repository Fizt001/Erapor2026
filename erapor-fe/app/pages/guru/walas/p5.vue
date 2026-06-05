<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Kokurikuler</h2>
        <p class="text-sm font-semibold text-slate-500 mt-1 mb-3">Input catatan capaian Projek P5 peserta didik.</p>

        <!-- Compact Info Badges -->
        <div v-if="pageData" class="flex flex-wrap items-center gap-2 animate-fadeIn">
            <div class="px-3 py-1.5 bg-white shadow-sm rounded-lg flex items-center gap-2 border border-slate-200">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Kelas:</span>
                <span class="text-xs font-bold text-slate-700">{{ pageData.kelas?.tingkat }} {{ pageData.kelas?.nama_kelas }}</span>
            </div>
            <div class="px-3 py-1.5 bg-white shadow-sm rounded-lg flex items-center gap-2 border border-slate-200">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Tahun:</span>
                <span class="text-xs font-bold text-slate-700">{{ pageData.tahun_ajaran?.tahun }}</span>
            </div>
            <div class="px-3 py-1.5 bg-white shadow-sm rounded-lg flex items-center gap-2 border border-slate-200">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Kurikulum:</span>
                <select 
                    v-model="pageData.kelas.kurikulum_id"
                    @change="saveKurikulum(pageData.kelas.kurikulum_id)"
                    class="text-xs font-bold text-slate-700 bg-transparent border-none p-0 focus:ring-0 cursor-pointer outline-none min-w-[120px]"
                >
                    <option v-for="kuri in pageData.master_kurikulum" :key="kuri.id" :value="kuri.id">{{ kuri.nama_kurikulum }}</option>
                </select>
            </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="pending" class="flex items-center justify-center min-h-[400px]">
        <div class="flex flex-col items-center opacity-60">
            <div class="w-10 h-10 border-4 border-teal-500 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</span>
        </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-rose-50 border border-rose-200 rounded-2xl p-8 text-center min-h-[400px] flex flex-col items-center justify-center">
        <span class="text-4xl mb-4">🔒</span>
        <h3 class="text-lg font-black text-rose-700">Akses Ditolak</h3>
        <p class="text-sm font-semibold text-rose-500 mt-2 max-w-md">{{ error.message || 'Terjadi kesalahan saat memuat data.' }}</p>
        <button @click="fetchData" class="mt-6 px-6 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm rounded-lg shadow-sm transition-colors">Coba Lagi</button>
    </div>

    <!-- Main Content -->
    <div v-else-if="pageData" class="flex flex-col gap-6">
        
        <!-- Matrix Editor Container -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            
            <div class="p-0 overflow-x-auto relative min-h-[300px] custom-scrollbar">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr>
                            <th class="py-4 px-6 border-b border-slate-200 bg-white sticky left-0 z-30 shadow-[1px_0_0_0_#e2e8f0] text-[10px] font-black text-slate-400 uppercase tracking-widest min-w-[200px] w-64">
                                Nama Peserta Didik
                            </th>
                            <th v-for="tm in pageData.periodes" :key="tm.id" :class="tm.nama_periode == 'PSAS' ? 'bg-indigo-50 border-indigo-100' : 'bg-teal-50 border-emerald-100'" class="py-4 px-4 border-b border-l text-center">
                                <span :class="tm.nama_periode == 'PSAS' ? 'text-indigo-700' : 'text-emerald-700'" class="text-[10px] font-black uppercase tracking-widest block mb-1">Capaian Projek ({{ tm.nama_periode }})</span>
                                <span v-if="!tm.is_aktif" class="text-[8px] font-bold text-rose-500 uppercase">Tertutup (Read-Only)</span>
                                <span v-else class="text-[8px] font-bold text-slate-400 uppercase">Aktif</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <tr v-for="siswa in pageData.data" :key="siswa.id" class="hover:bg-slate-50 transition-colors group">
                            
                            <!-- Nama Siswa (Sticky) -->
                            <td class="py-3 px-6 text-[11px] font-black text-slate-700 uppercase bg-white group-hover:bg-slate-50 sticky left-0 z-20 shadow-[1px_0_0_0_#f1f5f9] transition-colors align-top">
                                {{ siswa.nama_lengkap }}
                            </td>
                            
                            <!-- Textarea Kolom -->
                            <td v-for="tm in pageData.periodes" :key="tm.id" :class="tm.nama_periode == 'PSAS' ? 'bg-indigo-50/20 border-indigo-50' : 'bg-teal-50/20 border-emerald-50'" class="py-2 px-3 border-l align-top">
                                <textarea 
                                    v-model="formKo[siswa.id][tm.id]" 
                                    rows="2"
                                    placeholder="Ketercapaian projek..." 
                                    :disabled="!tm.is_aktif"
                                    @input="handleInput(siswa.id, tm.id)"
                                    :class="tm.nama_periode == 'PSAS' ? 'focus:ring-indigo-500/20 focus:border-indigo-500 border-indigo-200' : 'focus:ring-teal-500/20 focus:border-teal-500 border-emerald-200'"
                                    class="w-full rounded-xl text-[10px] font-medium shadow-sm py-1.5 px-3 transition-colors outline-none disabled:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed bg-white border"
                                ></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Global Toast -->
    <div class="fixed bottom-6 right-6 z-50 flex flex-col gap-2">
      <TransitionGroup name="toast">
        <div v-for="toast in toasts" :key="toast.id" 
             class="flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg border text-sm font-bold"
             :class="toast.type === 'success' ? 'bg-teal-50 border-teal-200 text-teal-700' : 'bg-rose-50 border-rose-200 text-rose-700'">
          <span v-if="toast.type === 'success'">✅</span>
          <span v-else>⚠️</span>
          {{ toast.message }}
        </div>
      </TransitionGroup>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

definePageMeta({
    layout: 'guru',
    middleware: 'guru'
})

const config = useRuntimeConfig()
const pending = ref(true)
const error = ref(null)
const pageData = ref(null)

// State for inputs: formKo[siswa_id][titimangsa_id] = "keterangan"
const formKo = ref({})

// Debounce timer tracking: debounceTimers[siswa_id][titimangsa_id]
const debounceTimers = {}

const toasts = ref([])
let toastCounter = 0

const showToast = (msg, type = 'success') => {
  const id = toastCounter++
  toasts.value.push({ id, message: msg, type })
  setTimeout(() => {
    toasts.value = toasts.value.filter(t => t.id !== id)
  }, 3000)
}

const fetchData = async () => {
    pending.value = true
    error.value = null
    try {
        const token = useCookie('auth_token')
        const response = await $fetch(`http://localhost:8000/api/guru/walas/kokurikuler`, {
            headers: {
                'Authorization': `Bearer ${token.value}`,
                'Accept': 'application/json'
            }
        })
        
        if (response.success) {
            // Initialize form state FIRST to prevent undefined errors in template
            const state = {}
            if (response.data && response.periodes) {
                response.data.forEach(siswa => {
                    state[siswa.id] = {}
                    response.periodes.forEach(tm => {
                        state[siswa.id][tm.id] = siswa['ko_'+tm.id] || ''
                    })
                })
            }
            formKo.value = state
            
            pageData.value = response
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
    fetchData()
})

const handleInput = (siswaId, tmId) => {
    if (debounceTimers[siswaId] && debounceTimers[siswaId][tmId]) {
        clearTimeout(debounceTimers[siswaId][tmId])
    }

    if (!debounceTimers[siswaId]) debounceTimers[siswaId] = {}

    debounceTimers[siswaId][tmId] = setTimeout(() => {
        saveData(siswaId, tmId)
    }, 1000)
}

const saveData = async (siswaId, tmId) => {
    try {
        const token = useCookie('auth_token')
        const keterangan = formKo.value[siswaId][tmId]
        
        const response = await $fetch(`http://localhost:8000/api/guru/walas/kokurikuler/store`, {
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
            showToast('Catatan tersimpan!')
        } else {
            showToast(response.message || 'Gagal menyimpan.', 'error')
        }
    } catch (e) {
        showToast(e.data?.message || 'Terjadi kesalahan jaringan.', 'error')
    }
}

const saveKurikulum = async (kurikulumId) => {
    try {
        const token = useCookie('auth_token')
        const response = await $fetch(`http://localhost:8000/api/guru/walas/kokurikuler/kurikulum`, {
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
            showToast('Kurikulum berhasil diubah!')
        } else {
            showToast(response.message || 'Gagal merubah kurikulum.', 'error')
        }
    } catch (e) {
        showToast(e.data?.message || 'Terjadi kesalahan jaringan.', 'error')
    }
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from { opacity: 0; transform: translateY(20px); }
.toast-leave-to { opacity: 0; transform: translateX(20px); }
</style>
