<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex flex-col xl:flex-row overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="xl:w-[360px] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] overflow-y-auto custom-scrollbar">
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
                  <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 pointer-events-none">🔍</span>
                  <input type="text" v-model="searchQuery" placeholder="Cari nama siswa..." 
                    class="w-full pl-10 pr-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          
          <!-- Loading State -->
          <div v-if="pending" class="flex-grow flex flex-col items-center justify-center p-20 opacity-60 bg-white rounded-3xl shadow-sm border border-slate-200/60">
            <div class="w-10 h-10 border-4 border-teal-500 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Memuat Data...</span>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="flex-grow flex flex-col items-center justify-center p-16 text-center bg-white rounded-3xl shadow-sm border border-slate-200/60">
            <div class="text-rose-500 text-4xl mb-4">🔒</div>
            <h3 class="text-rose-800 font-black mb-1">Akses Ditolak</h3>
            <p class="text-rose-600 text-sm font-semibold max-w-md">{{ error.message || 'Terjadi kesalahan saat memuat data.' }}</p>
            <button @click="fetchData" class="mt-6 px-6 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm rounded-lg shadow-sm transition-colors">Coba Lagi</button>
          </div>

          <!-- Matrix Editor Container -->
          <div v-else-if="pageData" class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            
            <!-- Header Card Kanan -->
            <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-white shrink-0 z-10">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-lg border border-emerald-100">🌿</div>
                <div>
                  <h3 class="text-[13px] font-black leading-none uppercase tracking-wide text-slate-800">Catatan Capaian P5</h3>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Otomatis tersimpan saat mengetik</p>
                </div>
              </div>
            </div>

            <!-- Form Matrix -->
            <div class="flex-1 overflow-auto custom-scrollbar relative bg-slate-50/30 flex flex-col">
                <table class="w-full text-left border-collapse min-w-[800px] bg-white">
                    <thead class="sticky top-0 z-20 shadow-sm">
                        <tr>
                            <th class="py-4 px-6 border-b border-slate-300 bg-slate-100 sticky left-0 z-30 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] border-r text-[10px] font-black text-slate-500 uppercase tracking-widest min-w-[200px] w-64 align-middle">
                                Nama Peserta Didik
                            </th>
                            <th v-for="(tm, tmIndex) in pageData.periodes" :key="tm.id" :class="tmIndex % 2 === 1 ? 'bg-indigo-50 border-indigo-100' : 'bg-teal-50 border-emerald-100'" class="py-4 px-4 border-b border-r text-center align-middle">
                                <span :class="tmIndex % 2 === 1 ? 'text-indigo-700' : 'text-emerald-700'" class="text-[10px] font-black uppercase tracking-widest block mb-1">Capaian Projek ({{ tm.nama_periode_panjang || tm.nama_periode }})</span>
                                <span v-if="!tm.is_aktif" class="text-[8px] font-bold text-rose-500 uppercase">Tertutup (Read-Only)</span>
                                <span v-else class="text-[8px] font-bold text-slate-400 uppercase">Aktif</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="siswa in filteredStudents" :key="activeTab + '-' + siswa.id" class="hover:bg-sky-50/30 transition-colors group">
                            
                            <!-- Nama Siswa (Sticky) -->
                            <td class="py-4 px-6 text-[11px] font-black text-slate-700 uppercase bg-white group-hover:bg-slate-50 sticky left-0 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.02)] transition-colors align-top border-r border-slate-100">
                                {{ siswa.nama_lengkap }}
                            </td>
                            
                            <!-- Textarea Kolom -->
                            <td v-for="(tm, tmIndex) in pageData.periodes" :key="tm.id" :class="tmIndex % 2 === 1 ? 'bg-indigo-50/10 border-indigo-50/50' : 'bg-teal-50/10 border-emerald-50/50'" class="py-3 px-3 border-r align-top">
                                <div class="relative">
                                    <textarea 
                                        v-model="formKo[siswa.id][tm.id]" 
                                        rows="2"
                                        placeholder="Ketercapaian projek..." 
                                        :disabled="!tm.is_aktif"
                                        @input="handleInput(siswa.id, tm.id)"
                                        :class="tmIndex % 2 === 1 ? 'focus:ring-indigo-500/20 focus:border-indigo-500 border-indigo-200' : 'focus:ring-teal-500/20 focus:border-teal-500 border-emerald-200'"
                                        class="w-full rounded-xl text-[11px] font-semibold shadow-sm py-2.5 px-3 transition-colors outline-none disabled:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed bg-white border resize-none"
                                    ></textarea>
                                    
                                    <!-- Save Status Indicator Mini -->
                                    <div class="absolute bottom-2 right-2 text-[10px]">
                                        <span v-if="saveStatus[siswa.id]?.[tm.id] === 'saving'" class="text-slate-400 animate-pulse">⏳</span>
                                        <span v-else-if="saveStatus[siswa.id]?.[tm.id] === 'saved'" class="text-emerald-500">✓</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredStudents.length === 0">
                            <td :colspan="pageData.periodes.length + 1" class="py-12 text-center text-slate-500 bg-slate-50/50">
                                <div class="text-3xl mb-2">🔍</div>
                                <div class="text-xs font-bold">Tidak ada siswa yang cocok.</div>
                            </td>
                        </tr>
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

const token = useCookie('auth_token')
const pending = ref(true)
const error = ref(null)
const pageData = ref(null)
const searchQuery = ref('')

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
