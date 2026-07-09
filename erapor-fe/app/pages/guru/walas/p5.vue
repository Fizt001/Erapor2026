<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex flex-col xl:flex-row overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="xl:w-[360px] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] overflow-y-auto custom-scrollbar">
        <div class="p-6 space-y-6">
          <div class="bg-gradient-to-r from-sky-600 to-blue-700 rounded-2xl p-5 border border-sky-500 shadow-sm relative overflow-hidden">
            <div class="relative z-10">
              <h3 class="text-sm font-black uppercase tracking-widest text-white">Capaian P5</h3>
              <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Input catatan capaian Projek P5 peserta didik</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-10">
              <svg class="w-24 h-24 transform translate-x-6 translate-y-6" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
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
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-1">Kurikulum</span>
                <select 
                  v-model="pageData.kelas.kurikulum_id"
                  @change="saveKurikulum(pageData.kelas.kurikulum_id)"
                  class="text-[11px] font-bold text-indigo-700 bg-transparent border-none p-0 focus:ring-0 cursor-pointer outline-none text-right max-w-[150px]"
                >
                  <option v-for="kuri in pageData.master_kurikulum" :key="kuri.id" :value="kuri.id">{{ kuri.nama_kurikulum }}</option>
                </select>
              </div>
            </div>

            <div class="bg-indigo-50 p-4 rounded-2xl border border-indigo-100 flex flex-col items-center justify-center text-center">
                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center mb-2">
                    <span class="animate-pulse">💾</span>
                </div>
                <h4 class="text-[11px] font-black uppercase tracking-widest text-indigo-800">Auto-Save</h4>
                <p class="text-[10px] font-bold text-indigo-600 mt-1">Catatan akan tersimpan otomatis saat Anda selesai mengetik.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          
          <!-- Loading State -->
          <div v-if="pending" class="flex-grow flex flex-col items-center justify-center p-20 opacity-60 bg-white rounded-3xl shadow-sm border border-slate-200/60">
            <div class="w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Memuat Data...</span>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="flex-grow flex flex-col items-center justify-center p-16 text-center bg-white rounded-3xl shadow-sm border border-slate-200/60">
            <div class="text-rose-500 text-4xl mb-4">🔒</div>
            <h3 class="text-rose-800 font-black mb-1">Akses Ditolak</h3>
            <p class="text-rose-600 text-sm font-semibold max-w-md">{{ error.message || 'Terjadi kesalahan saat memuat data.' }}</p>
            <button @click="fetchData" class="mt-4 px-4 py-2 bg-rose-100 hover:bg-rose-200 text-rose-700 text-[10px] font-black uppercase tracking-widest rounded-lg transition-colors">
              Coba Lagi
            </button>
          </div>

          <!-- Main Content Container -->
          <div v-else-if="pageData" class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            <div class="flex-1 overflow-auto custom-scrollbar relative bg-slate-50/30">
              <table class="w-full text-left border-collapse bg-white">
                <thead class="sticky top-0 z-20 shadow-sm">
                  <tr class="bg-slate-100 border-b border-slate-200">
                    <th class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500 border-r border-slate-200 min-w-[200px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] sticky left-0 bg-slate-100 z-30">Nama Peserta Didik</th>
                    <th v-for="(tm, tmIndex) in pageData.periodes" :key="tm.id" :class="tmIndex % 2 === 1 ? 'bg-indigo-50 border-indigo-100' : 'bg-teal-50 border-emerald-100'" class="py-3 px-4 text-center border-r border-slate-200">
                        <span :class="tmIndex % 2 === 1 ? 'text-indigo-700' : 'text-emerald-700'" class="text-[10px] font-black uppercase tracking-widest block mb-1">Capaian Projek ({{ tm.nama_periode_panjang || tm.nama_periode }})</span>
                        <span v-if="!tm.is_aktif" class="text-[8px] font-bold text-rose-500 uppercase tracking-widest">Tertutup (Read-Only)</span>
                        <span v-else class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Aktif</span>
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-for="siswa in pageData.data" :key="activeTab + '-' + siswa.id" class="hover:bg-slate-50/80 transition-colors group">
                    <td class="py-4 px-4 border-r border-slate-100 sticky left-0 bg-white group-hover:bg-slate-50/90 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.02)] z-10 align-top">
                      <div class="text-[12px] font-black text-slate-700 uppercase tracking-wide">{{ siswa.nama_lengkap }}</div>
                    </td>
                    <td v-for="(tm, tmIndex) in pageData.periodes" :key="tm.id" :class="tmIndex % 2 === 1 ? 'bg-indigo-50/20' : 'bg-teal-50/20'" class="py-2 px-3 border-r border-slate-100 align-top">
                        <div class="relative w-full">
                            <textarea 
                                v-model="formKo[siswa.id][tm.id]" 
                                rows="3"
                                placeholder="Ketercapaian projek..." 
                                :disabled="!tm.is_aktif"
                                @input="handleInput(siswa.id, tm.id)"
                                :class="tmIndex % 2 === 1 ? 'focus:ring-indigo-500/10 focus:border-indigo-500' : 'focus:ring-teal-500/10 focus:border-teal-500'"
                                class="w-full rounded-xl text-xs font-bold text-slate-700 placeholder-slate-300 py-3 px-3 transition-all outline-none disabled:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed bg-white border-2 border-slate-200/70 resize-none custom-scrollbar"
                            ></textarea>
                            <div class="absolute bottom-2 right-3 pointer-events-none transition-opacity duration-300" 
                                :class="savingStatus[`${siswa.id}_${tm.id}`] ? 'opacity-100' : 'opacity-0'">
                                <span v-if="savingStatus[`${siswa.id}_${tm.id}`] === 'saving'" class="flex items-center text-[10px] font-bold text-indigo-500 bg-white/90 backdrop-blur px-2 py-0.5 rounded shadow-sm">
                                    <svg class="animate-spin -ml-1 mr-1.5 h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Menyimpan...
                                </span>
                                <span v-else-if="savingStatus[`${siswa.id}_${tm.id}`] === 'saved'" class="flex items-center text-[10px] font-bold text-emerald-500 bg-white/90 backdrop-blur px-2 py-0.5 rounded shadow-sm">
                                    ✓ Tersimpan
                                </span>
                                <span v-else-if="savingStatus[`${siswa.id}_${tm.id}`] === 'error'" class="flex items-center text-[10px] font-bold text-rose-500 bg-white/90 backdrop-blur px-2 py-0.5 rounded shadow-sm">
                                    ⚠️ Gagal
                                </span>
                            </div>
                        </div>
                    </td>
                  </tr>
                  <tr v-if="!pageData.data || pageData.data.length === 0">
                    <td :colspan="pageData.periodes.length + 1" class="py-12 text-center text-slate-500 bg-slate-50/50">
                      <div class="text-3xl mb-3 opacity-50">👥</div>
                      <div class="text-xs font-bold">Tidak ada data siswa.</div>
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
</template>

<script setup>
import { ref, onMounted } from 'vue'

definePageMeta({
    layout: 'guru',
    middleware: 'guru'
})

const pending = ref(true)
const error = ref(null)
const pageData = ref(null)

// State for inputs: formKo[siswa_id][titimangsa_id] = "keterangan"
const formKo = ref({})

// Debounce timer tracking: debounceTimers[siswa_id][titimangsa_id]
const debounceTimers = {}

// Saving Status
const savingStatus = ref({})

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

const handleInput = (siswaId, tmId) => {
    const key = `${siswaId}_${tmId}`
    savingStatus.value[key] = 'saving'

    if (debounceTimers[siswaId] && debounceTimers[siswaId][tmId]) {
        clearTimeout(debounceTimers[siswaId][tmId])
    }

    if (!debounceTimers[siswaId]) debounceTimers[siswaId] = {}

    debounceTimers[siswaId][tmId] = setTimeout(() => {
        saveData(siswaId, tmId)
    }, 1000)
}

const saveData = async (siswaId, tmId) => {
    const key = `${siswaId}_${tmId}`
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
            savingStatus.value[key] = 'saved'
            setTimeout(() => {
                if (savingStatus.value[key] === 'saved') {
                    savingStatus.value[key] = null
                }
            }, 2000)
        } else {
            savingStatus.value[key] = 'error'
        }
    } catch (e) {
        savingStatus.value[key] = 'error'
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
            useNuxtApp().$swal.fire({
                title: 'Berhasil',
                text: 'Kurikulum berhasil diubah.',
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })
        } else {
            throw new Error(response.message || 'Gagal merubah kurikulum.')
        }
    } catch (e) {
        useNuxtApp().$swal.fire({
            title: 'Gagal',
            text: e.data?.message || e.message || 'Terjadi kesalahan jaringan.',
            icon: 'error',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        })
    }
}

onMounted(() => {
    fetchData()
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #94a3b8; }
</style>
