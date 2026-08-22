<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-1.5 flex gap-1.5 shadow-sm z-20">
        <button type="button" @click="activeTabMobile = 'form'"
          :class="activeTabMobile === 'form' ? 'bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-md shadow-indigo-500/20 ring-2 ring-indigo-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="flex-1 rounded-lg flex flex-col items-center justify-center py-2 px-1 transition-all active:scale-95">
          <AppIcon name="filter" class="text-lg mb-0.5 transition-transform" :class="activeTabMobile === 'form' ? 'scale-110' : ''" />
          <span class="text-[10px] font-black uppercase tracking-wider text-center leading-none">Filter</span>
        </button>
        <button type="button" @click="activeTabMobile = 'table'"
          :class="activeTabMobile === 'table' ? 'bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-md shadow-indigo-500/20 ring-2 ring-indigo-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="flex-1 rounded-lg flex flex-col items-center justify-center py-2 px-1 transition-all active:scale-95">
          <AppIcon name="document-text" class="text-lg mb-0.5 transition-transform" :class="activeTabMobile === 'table' ? 'scale-110' : ''" />
          <span class="text-[10px] font-black uppercase tracking-wider text-center leading-none">Data Jurnal</span>
        </button>
      </div>

      <!-- Panel Dock Kiri (Filter Form) -->
      <div :class="['w-full xl:w-[320px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all', activeTabMobile === 'form' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[52px]' : '']">
        
        <div class="p-4 pb-2 shrink-0 z-10 relative">
          <div class="bg-gradient-to-r from-indigo-600 to-blue-600 rounded-2xl p-4 border border-indigo-500 shadow-sm relative overflow-hidden flex items-center gap-3">
            <div class="w-8 h-8 flex items-center justify-center shrink-0 bg-white/10 rounded-lg relative z-10 text-white"><AppIcon name="filter" class="w-5 h-5" /></div>
            <div class="relative z-10">
                <h3 class="text-xs font-black uppercase tracking-widest text-white">Filter Jurnal</h3>
                <p class="text-[10px] text-indigo-100 font-semibold uppercase mt-0.5">Pilih Target Pencarian</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-15 text-white pointer-events-none">
              <svg class="w-16 h-16 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            </div>
          </div>
        </div>
        
        <div class="flex-1 overflow-y-auto custom-scrollbar p-4 pb-6">
            <div class="space-y-5">
                
                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pilih Guru</label>
                    <select v-model="filter.guru_id" @change="handleFilterChange" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold text-slate-800 outline-none">
                        <option value="">-- Semua Guru / Pilih --</option>
                        <option v-for="g in guruList" :key="g.id" :value="g.id">{{ g.name }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pilih Bulan</label>
                    <input type="month" v-model="filter.bulan" @change="handleFilterChange" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold text-slate-800 outline-none">
                </div>

            </div>
        </div>
      </div>

      <!-- Panel Flow Kanan (Data Jurnal) -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative', activeTabMobile === 'table' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[52px]' : '']">
        <div class="p-0 sm:pt-3 sm:pb-6 sm:px-6 lg:pt-3 lg:pb-8 lg:px-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-none sm:rounded-[2rem] shadow-[0_2px_15px_-3px_rgba(0,0,0,0.05)] overflow-hidden flex flex-col flex-1 relative min-h-0 border-0 sm:border sm:border-slate-200/60">
            <div class="px-6 py-5 border-b border-slate-100 flex flex-row justify-between items-center gap-2 shrink-0 z-10 bg-white">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 shadow-sm border border-indigo-100 flex items-center justify-center text-xl hidden sm:flex text-indigo-500"><AppIcon name="document-text" class="w-6 h-6" /></div>
                    <div>
                        <h3 class="text-[11px] sm:text-sm font-black uppercase tracking-widest text-indigo-700">Pantau Jurnal Mengajar</h3>
                        <p class="text-[10px] sm:text-[10px] font-bold text-slate-400 uppercase mt-0.5">Laporan Materi & Kehadiran Guru</p>
                    </div>
                </div>
                <button @click="fetchData" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-slate-200 hover:text-slate-700 font-bold transition-colors shrink-0" title="Refresh">
                    <AppIcon name="arrow-path" class="w-5 h-5" :class="{'animate-spin': isLoading}" />
                </button>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto custom-scrollbar relative bg-slate-50/50 p-4 sm:p-6">
                <!-- Loading State -->
                <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60 h-full">
                    <div class="w-8 h-8 border-4 border-indigo-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                    <span class="text-xs font-black text-indigo-500 uppercase tracking-widest">Memuat Jurnal...</span>
                </div>

                <!-- Empty State (No Guru Selected) -->
                <div v-else-if="!filter.guru_id" class="bg-white rounded-3xl p-16 text-center border border-slate-100 shadow-sm flex flex-col items-center justify-center h-full">
                    <div class="w-20 h-20 rounded-full bg-indigo-50 text-indigo-300 flex items-center justify-center mb-4">
                        <AppIcon name="user" class="w-10 h-10" />
                    </div>
                    <h3 class="text-lg font-black text-slate-700">Pilih Guru Dahulu</h3>
                    <p class="text-sm text-slate-500 mt-2 max-w-sm">Silakan pilih nama guru dari panel kiri untuk melihat riwayat jurnal mengajarnya.</p>
                </div>

                <!-- No Data State -->
                <div v-else-if="jurnalList.length === 0" class="bg-white rounded-3xl p-16 text-center border border-slate-100 shadow-sm flex flex-col items-center justify-center h-full">
                    <div class="w-20 h-20 rounded-full bg-slate-50 text-slate-300 flex items-center justify-center mb-4">
                        <AppIcon name="document-text" class="w-10 h-10" />
                    </div>
                    <h3 class="text-lg font-black text-slate-700">Belum Ada Jurnal</h3>
                    <p class="text-sm text-slate-500 mt-2">Guru ini belum mengisi jurnal pada bulan yang dipilih.</p>
                </div>

                <!-- Data List -->
                <div v-else class="space-y-4">
                    <div v-for="(item, idx) in jurnalList" :key="item.id" class="bg-white rounded-2xl p-5 border border-slate-100 shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)] hover:shadow-md transition-shadow relative overflow-hidden group flex flex-col md:flex-row gap-5">
                        <!-- Left Info (Waktu & Kelas) -->
                        <div class="md:w-64 shrink-0 flex flex-col justify-between border-b md:border-b-0 md:border-r border-slate-100 pb-4 md:pb-0 md:pr-5">
                            <div>
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-[10px] font-black uppercase tracking-widest">{{ item.kelas }}</div>
                                    <span class="text-[10px] font-bold text-slate-400">{{ formatDate(item.tanggal) }}</span>
                                </div>
                                <h4 class="font-black text-slate-800 text-sm leading-tight">{{ item.mapel }}</h4>
                                <div class="flex items-center gap-1.5 mt-2 text-xs font-bold text-slate-500">
                                    <AppIcon name="clock" class="w-3.5 h-3.5 text-slate-400" /> {{ item.waktu }}
                                </div>
                            </div>
                            
                            <!-- Absensi Summary -->
                            <div class="mt-4 pt-3 border-t border-slate-50 flex items-center justify-between">
                                <div class="text-center">
                                    <span class="block text-xs font-black text-emerald-600">{{ item.kehadiran.h || '-' }}</span>
                                    <span class="text-[9px] uppercase tracking-widest text-slate-400 font-bold">H</span>
                                </div>
                                <div class="text-center">
                                    <span class="block text-xs font-black text-amber-600">{{ item.kehadiran.s || '-' }}</span>
                                    <span class="text-[9px] uppercase tracking-widest text-slate-400 font-bold">S</span>
                                </div>
                                <div class="text-center">
                                    <span class="block text-xs font-black text-blue-600">{{ item.kehadiran.i || '-' }}</span>
                                    <span class="text-[9px] uppercase tracking-widest text-slate-400 font-bold">I</span>
                                </div>
                                <div class="text-center">
                                    <span class="block text-xs font-black text-rose-600">{{ item.kehadiran.a || '-' }}</span>
                                    <span class="text-[9px] uppercase tracking-widest text-slate-400 font-bold">A</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Info (Materi) -->
                        <div class="flex-1">
                            <h5 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Materi / Jurnal Kegiatan</h5>
                            <div class="text-sm text-slate-700 bg-slate-50/50 rounded-xl p-4 min-h-[80px] border border-slate-100/50 leading-relaxed whitespace-pre-wrap">
                                {{ item.materi || '(Guru belum/tidak mengisi deskripsi materi untuk pertemuan ini)' }}
                            </div>
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
import { ref, onMounted, computed, onUnmounted } from 'vue'

definePageMeta({
  layout: 'kurikulum',
  middleware: 'kurikulum',
  title: 'Pantau Jurnal'
})

const guruList = ref([])
const jurnalList = ref([])
const isLoading = ref(false)

const activeTabMobile = ref('form')
const windowWidth = ref(1024)

const isDesktop = computed(() => {
  if (import.meta.server) return true
  return windowWidth.value >= 1280
})

const handleResize = () => {
  windowWidth.value = window.innerWidth
}

const filter = ref({
  guru_id: '',
  bulan: new Date().toISOString().substring(0, 7) // YYYY-MM
})

const getAuthToken = () => useCookie('auth_token').value

const fetchGuru = async () => {
  try {
    const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/kurikulum/jurnal/guru', {
      headers: { Authorization: `Bearer ${getAuthToken()}` }
    })
    if (res.success) {
      guruList.value = res.data
    }
  } catch (error) {
    console.error('Failed to fetch guru list', error)
  }
}

const fetchData = async () => {
  if (!filter.value.guru_id) {
    jurnalList.value = []
    return
  }
  
  isLoading.value = true
  try {
    const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/kurikulum/jurnal', {
      headers: { Authorization: `Bearer ${getAuthToken()}` },
      query: filter.value
    })
    if (res.success) {
      jurnalList.value = res.data || []
    }
  } catch (error) {
    console.error('Failed to fetch jurnal', error)
  } finally {
    isLoading.value = false
  }
}

const handleFilterChange = () => {
  if (!isDesktop.value && filter.value.guru_id) {
    activeTabMobile.value = 'table'
  }
  fetchData()
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  const date = new Date(dateStr)
  return new Intl.DateTimeFormat('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' }).format(date)
}

onMounted(() => {
  windowWidth.value = window.innerWidth
  window.addEventListener('resize', handleResize)
  fetchGuru()
})

onUnmounted(() => {
  if (!import.meta.server) {
    window.removeEventListener('resize', handleResize)
  }
})
</script>

<style scoped>
.custom-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.custom-scrollbar::-webkit-scrollbar { display: none; }
</style>
