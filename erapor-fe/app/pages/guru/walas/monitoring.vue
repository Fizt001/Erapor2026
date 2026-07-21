<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex flex-col xl:flex-row overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="xl:w-[360px] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] overflow-y-auto custom-scrollbar">
        <div class="p-6 space-y-6">
          <div class="bg-gradient-to-r from-sky-600 to-blue-700 rounded-2xl p-5 border border-sky-500 shadow-sm relative overflow-hidden">
            <div class="relative z-10">
              <h3 class="text-sm font-black uppercase tracking-widest text-white">Monitoring Nilai</h3>
              <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Pantau progres pengisian nilai guru</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-10">
              <svg class="w-24 h-24 transform translate-x-6 translate-y-6" fill="currentColor" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
          </div>
          
          <div class="space-y-4">
            <!-- Summary Chips -->
            <div v-if="!pending && !error && totalMapel > 0" class="grid grid-cols-2 gap-3">
              <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-2xl flex flex-col shadow-sm items-center justify-center text-center">
                <span class="text-[9px] uppercase font-black text-emerald-600 tracking-widest mb-1">Formatif Selesai</span>
                <span class="text-2xl font-black text-emerald-700">{{ formatifCompletedCount }}<span class="text-sm text-emerald-500 font-bold ml-1">/ {{ totalMapel }}</span></span>
              </div>
              <div class="bg-sky-50 border border-sky-100 p-4 rounded-2xl flex flex-col shadow-sm items-center justify-center text-center">
                <span class="text-[9px] uppercase font-black text-sky-600 tracking-widest mb-1">Sumatif Selesai</span>
                <span class="text-2xl font-black text-sky-700">{{ sumatifCompletedCount }}<span class="text-sm text-sky-500 font-bold ml-1">/ {{ totalMapel }}</span></span>
              </div>
            </div>

            <!-- Search / Filter -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pencarian</label>
              <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 pointer-events-none">🔍</span>
                  <input type="text" v-model="searchQuery" placeholder="Cari mapel atau guru..." 
                    class="w-full pl-10 pr-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-semibold text-xs text-slate-700 outline-none">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            
            <!-- Header Card Kanan -->
            <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-white shrink-0 z-10">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-lg border border-indigo-100">📊</div>
                <div>
                  <h3 class="text-[13px] font-black leading-none uppercase tracking-wide text-slate-800">Progres Pengisian Nilai Kelas</h3>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ filteredMapel.length }} Mata Pelajaran</p>
                </div>
              </div>
              
              <div class="flex items-center space-x-4 shrink-0 mt-4 md:mt-0">
                 <button @click="refreshData" class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 transition-all active:scale-95" :disabled="pending">
                  <span :class="{'animate-spin': pending}">🔄</span> <span>REFRESH</span>
                </button>
              </div>
            </div>

            <!-- Loading Indicator -->
            <div v-if="pending" class="flex-grow flex flex-col items-center justify-center p-20 opacity-60">
              <div class="w-8 h-8 border-4 border-indigo-400 border-t-transparent rounded-full animate-spin mb-4"></div>
              <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Memuat Progres Nilai...</span>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="flex-grow flex flex-col items-center justify-center p-16 text-center">
              <div class="text-rose-500 text-4xl mb-4">⚠️</div>
              <h3 class="text-rose-800 font-black mb-1">Gagal Memuat Data</h3>
              <p class="text-rose-600 text-sm font-semibold">{{ error.message || 'Terjadi kesalahan pada server.' }}</p>
              <button @click="refreshData" class="mt-4 px-4 py-2 bg-rose-100 hover:bg-rose-200 text-rose-700 text-[10px] font-black uppercase tracking-widest rounded-lg transition-colors">
                Coba Lagi
              </button>
            </div>

            <!-- List Workspace Area -->
            <div v-else class="flex-grow flex flex-col relative bg-slate-50/30 overflow-hidden">
              <div class="flex-1 overflow-auto custom-scrollbar relative">
                <table class="w-full text-left border-collapse bg-white">
                  <thead class="sticky top-0 z-20 shadow-sm">
                    <tr class="bg-slate-100 border-b border-slate-200">
                      <th class="py-3 px-4 text-center text-[10px] font-black uppercase tracking-widest text-slate-500 w-[60px] border-r border-slate-200">No</th>
                      <th class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500 border-r border-slate-200 min-w-[200px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] sticky left-0 bg-slate-100 z-30">Mata Pelajaran</th>
                      <th class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500 min-w-[150px] border-r border-slate-200">Guru Pengampu</th>
                      <th class="py-3 px-4 text-center text-[10px] font-black uppercase tracking-widest text-slate-500 min-w-[200px] border-r border-slate-200">Progres Formatif</th>
                      <th class="py-3 px-4 text-center text-[10px] font-black uppercase tracking-widest text-slate-500 min-w-[200px]">Progres Sumatif</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    <tr v-for="(item, index) in filteredMapel" :key="item.mapel_id" class="hover:bg-slate-50/80 transition-colors group">
                      <td class="py-3 px-4 text-center text-[11px] font-bold text-slate-400 border-r border-slate-100">{{ index + 1 }}</td>
                      <td class="py-3 px-4 border-r border-slate-100 sticky left-0 bg-white group-hover:bg-slate-50/90 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.02)] z-10">
                        <div class="text-[12px] font-black text-slate-700 uppercase tracking-wide">{{ item.nama_mapel }}</div>
                      </td>
                      <td class="py-3 px-4 border-r border-slate-100 text-slate-600">
                        <div class="flex items-center gap-3">
                          <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-[10px] font-black uppercase border border-indigo-100">
                            {{ item.guru_pengampu ? item.guru_pengampu.charAt(0) : '?' }}
                          </div>
                          <span class="text-[11px] font-bold uppercase">{{ item.guru_pengampu || 'Belum Diplot' }}</span>
                        </div>
                      </td>
                      
                      <!-- Progress Formatif -->
                      <td class="py-3 px-4 border-r border-slate-100">
                        <div class="flex justify-between items-center mb-1.5">
                          <span class="text-[10px] font-black tracking-widest uppercase" :class="item.status_formatif ? 'text-emerald-600' : 'text-amber-500'">
                            {{ item.status_formatif ? 'Selesai' : 'Proses' }}
                          </span>
                          <span class="text-[10px] font-black tracking-widest text-slate-600">{{ item.formatif_terisi }} / {{ item.total_siswa }}</span>
                        </div>
                        <div class="flex items-center space-x-3 w-full">
                            <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200/50">
                                <div class="h-full rounded-full transition-all duration-500" 
                                    :class="item.status_formatif ? 'bg-emerald-500' : (item.formatif_terisi > 0 ? 'bg-amber-400' : 'bg-slate-300')"
                                    :style="{ width: getPercentage(item.formatif_terisi, item.total_siswa) + '%' }">
                                </div>
                            </div>
                            <span class="text-[11px] font-black w-10 text-right tracking-widest" :class="item.status_formatif ? 'text-emerald-600' : (item.formatif_terisi > 0 ? 'text-amber-600' : 'text-slate-400')">
                                {{ getPercentage(item.formatif_terisi, item.total_siswa) }}%
                            </span>
                        </div>
                      </td>

                      <!-- Progress Sumatif -->
                      <td class="py-3 px-4">
                        <div class="flex justify-between items-center mb-1.5">
                          <span class="text-[10px] font-black tracking-widest uppercase" :class="item.status_sumatif ? 'text-sky-600' : 'text-amber-500'">
                            {{ item.status_sumatif ? 'Selesai' : 'Proses' }}
                          </span>
                          <span class="text-[10px] font-black tracking-widest text-slate-600">{{ item.sumatif_terisi }} / {{ item.total_siswa }}</span>
                        </div>
                        <div class="flex items-center space-x-3 w-full">
                            <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200/50">
                                <div class="h-full rounded-full transition-all duration-500" 
                                    :class="item.status_sumatif ? 'bg-sky-500' : (item.sumatif_terisi > 0 ? 'bg-amber-400' : 'bg-slate-300')"
                                    :style="{ width: getPercentage(item.sumatif_terisi, item.total_siswa) + '%' }">
                                </div>
                            </div>
                            <span class="text-[11px] font-black w-10 text-right tracking-widest" :class="item.status_sumatif ? 'text-sky-600' : (item.sumatif_terisi > 0 ? 'text-amber-600' : 'text-slate-400')">
                                {{ getPercentage(item.sumatif_terisi, item.total_siswa) }}%
                            </span>
                        </div>
                      </td>
                    </tr>
                    <tr v-if="filteredMapel.length === 0 && !pending">
                      <td colspan="5" class="py-12 text-center">
                        <div class="text-slate-300 text-4xl mb-3">🔍</div>
                        <div class="text-slate-500 font-bold text-sm">Tidak ada mata pelajaran yang sesuai.</div>
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
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

definePageMeta({
  layout: "walas",
  middleware: 'guru',
  title: 'Monitoring Nilai'
})

const token = useCookie('auth_token')
const pending = ref(true)
const error = ref(null)
const mapelList = ref([])
const searchQuery = ref('')

const filteredMapel = computed(() => {
    if (!searchQuery.value) return mapelList.value
    const q = searchQuery.value.toLowerCase()
    return mapelList.value.filter(m => 
        (m.nama_mapel && m.nama_mapel.toLowerCase().includes(q)) ||
        (m.guru_pengampu && m.guru_pengampu.toLowerCase().includes(q))
    )
})

const totalMapel = computed(() => mapelList.value.length)
const formatifCompletedCount = computed(() => mapelList.value.filter(m => m.status_formatif).length)
const sumatifCompletedCount = computed(() => mapelList.value.filter(m => m.status_sumatif).length)

const getPercentage = (filled, total) => {
  if (!total || total === 0) return 0;
  const raw = (filled / total) * 100;
  return Math.min(Math.round(raw), 100);
}

const fetchData = async () => {
  pending.value = true
  error.value = null
  try {
    const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/guru/walas/monitoring', {
      headers: { Authorization: `Bearer ${token.value}` }
    })
    
    if (res.success) {
      mapelList.value = res.data
    } else {
      throw new Error(res.message || 'Gagal memuat data')
    }
  } catch (err) {
    error.value = err
  } finally {
    pending.value = false
  }
}

const refreshData = () => {
  fetchData()
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
