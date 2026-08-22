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
          <AppIcon name="calendar" class="text-lg mb-0.5 transition-transform" :class="activeTabMobile === 'table' ? 'scale-110' : ''" />
          <span class="text-[10px] font-black uppercase tracking-wider text-center leading-none">Kalender Jurnal</span>
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
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pilih Kelas</label>
                    <select v-model="filter.kelas_id" @change="handleFilterChange" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold text-slate-800 outline-none">
                        <option value="">-- Semua Kelas / Pilih --</option>
                        <option v-for="k in kelasList" :key="k.id" :value="k.id">{{ k.nama }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pilih Bulan</label>
                    <input type="month" v-model="filter.bulan" @change="handleFilterChange" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold text-slate-800 outline-none">
                </div>

            </div>
        </div>
      </div>

      <!-- Panel Flow Kanan (Data Jurnal Kalender) -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative', activeTabMobile === 'table' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[52px]' : '']">
        <div class="p-0 sm:pt-3 sm:pb-6 sm:px-6 lg:pt-3 lg:pb-8 lg:px-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-none sm:rounded-[2rem] shadow-[0_2px_15px_-3px_rgba(0,0,0,0.05)] overflow-hidden flex flex-col flex-1 relative min-h-0 border-0 sm:border sm:border-slate-200/60">
            <div class="px-6 py-5 border-b border-slate-100 flex flex-row justify-between items-center gap-2 shrink-0 z-10 bg-white">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 shadow-sm border border-indigo-100 flex items-center justify-center text-xl hidden sm:flex text-indigo-500"><AppIcon name="calendar" class="w-6 h-6" /></div>
                    <div>
                        <h3 class="text-[11px] sm:text-sm font-black uppercase tracking-widest text-indigo-700">Kalender Jurnal Kelas</h3>
                        <p class="text-[10px] sm:text-[10px] font-bold text-slate-400 uppercase mt-0.5">Rekam Jejak Kehadiran Siswa Harian</p>
                    </div>
                </div>
                <button @click="fetchData" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-slate-200 hover:text-slate-700 font-bold transition-colors shrink-0" title="Refresh">
                    <AppIcon name="arrow-path" class="w-5 h-5" :class="{'animate-spin': isLoading}" />
                </button>
            </div>

            <!-- Content Area: Calendar Grid -->
            <div class="flex-1 overflow-y-auto custom-scrollbar relative bg-slate-50/50 p-4 sm:p-6">
                
                <!-- Loading State -->
                <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60 h-full">
                    <div class="w-8 h-8 border-4 border-indigo-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                    <span class="text-xs font-black text-indigo-500 uppercase tracking-widest">Memuat Kalender...</span>
                </div>

                <!-- Empty State (No Kelas Selected) -->
                <div v-else-if="!filter.kelas_id" class="bg-white rounded-3xl p-16 text-center border border-slate-100 shadow-sm flex flex-col items-center justify-center h-full">
                    <div class="w-20 h-20 rounded-full bg-indigo-50 text-indigo-300 flex items-center justify-center mb-4">
                        <AppIcon name="user-group" class="w-10 h-10" />
                    </div>
                    <h3 class="text-lg font-black text-slate-700">Pilih Kelas Dahulu</h3>
                    <p class="text-sm text-slate-500 mt-2 max-w-sm">Silakan pilih nama kelas dari panel kiri untuk melihat kalender jurnal mengajarnya.</p>
                </div>

                <!-- Calendar View -->
                <div v-else class="h-full flex flex-col">
                  <!-- Header Hari -->
                  <div class="grid grid-cols-7 gap-1 sm:gap-2 mb-2 shrink-0">
                    <div v-for="day in ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']" :key="day" class="text-center font-black text-[10px] sm:text-xs text-slate-400 uppercase tracking-widest py-2">
                      {{ day }}
                    </div>
                  </div>
                  
                  <!-- Grid Tanggal -->
                  <div class="grid grid-cols-7 gap-1 sm:gap-2 auto-rows-[60px] sm:auto-rows-[80px] md:auto-rows-[100px]">
                    <!-- Empty offsets for the first day of month -->
                    <div v-for="offset in blankDays" :key="'blank-'+offset" class="bg-slate-100/50 rounded-xl sm:rounded-2xl border border-transparent opacity-50 pointer-events-none"></div>
                    
                    <!-- Date Cards -->
                    <button v-for="date in daysInMonth" :key="date.fullDate" 
                            @click="openModal(date.fullDate)"
                            :disabled="!hasData(date.fullDate)"
                            :class="[
                              'relative p-2 sm:p-3 rounded-xl sm:rounded-2xl border text-left transition-all flex flex-col justify-between overflow-hidden',
                              hasData(date.fullDate) 
                                ? 'bg-white border-indigo-200 shadow-sm hover:shadow-md hover:border-indigo-400 cursor-pointer active:scale-95 group' 
                                : 'bg-transparent border-slate-200/50 opacity-60 cursor-default'
                            ]">
                      <!-- Tanggal Angka -->
                      <span :class="[
                        'text-sm sm:text-lg font-black', 
                        hasData(date.fullDate) ? 'text-indigo-600 group-hover:text-indigo-700' : 'text-slate-400'
                      ]">
                        {{ date.day }}
                      </span>
                      
                      <!-- Badge / Info (if has data) -->
                      <div v-if="hasData(date.fullDate)" class="mt-auto">
                        <div class="text-[8px] sm:text-[10px] font-bold text-indigo-500 uppercase tracking-wider bg-indigo-50 px-1.5 py-0.5 rounded-md truncate">
                          {{ jurnals[date.fullDate].length }} Sesi
                        </div>
                      </div>
                    </button>
                  </div>
                </div>

            </div>
          </div>
        </div>
      </div>

    </div>
    
    <!-- MODAL DETAIL JURNAL (Matrix Table) -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4 sm:p-6 opacity-100 transition-opacity">
      <div class="bg-white w-full max-w-6xl max-h-[90vh] rounded-[2rem] shadow-2xl flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 shrink-0">
          <div>
            <h3 class="text-base sm:text-lg font-black text-slate-800">Rekam Jejak Kehadiran Siswa</h3>
            <p class="text-xs font-bold text-indigo-600 mt-0.5">{{ formatDateComplete(selectedDate) }}</p>
          </div>
          <button @click="closeModal" class="w-10 h-10 rounded-full bg-slate-200 hover:bg-rose-100 text-slate-500 hover:text-rose-600 flex items-center justify-center transition-colors">
            <AppIcon name="x-mark" class="w-5 h-5" />
          </button>
        </div>
        
        <!-- Modal Body (Table Matriks) -->
        <div class="flex-1 overflow-auto p-0">
          <div class="min-w-max border-b border-slate-100">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-100/50">
                  <th class="sticky left-0 z-20 bg-slate-100/90 backdrop-blur border-r border-b border-slate-200 p-4 w-12 text-center text-xs font-black text-slate-500 uppercase tracking-widest">No</th>
                  <th class="sticky left-[48px] z-20 bg-slate-100/90 backdrop-blur border-r border-b border-slate-200 p-4 text-xs font-black text-slate-500 uppercase tracking-widest min-w-[200px]">Nama Siswa</th>
                  <!-- Kolom Sesi -->
                  <th v-for="(sesi, index) in selectedSesis" :key="sesi.id" class="border-r border-b border-slate-200 p-3 min-w-[150px] align-top bg-white">
                    <div class="flex flex-col gap-1">
                      <div class="bg-indigo-50 text-indigo-700 text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded-md text-center">Sesi {{ index + 1 }}</div>
                      <div class="text-[11px] font-bold text-slate-700 text-center truncate" :title="sesi.mapel">{{ sesi.mapel }}</div>
                      <div class="text-[9px] font-semibold text-slate-500 text-center truncate">Oleh: {{ sesi.guru }}</div>
                      <div class="text-[9px] font-bold text-slate-400 text-center mt-1"><AppIcon name="clock" class="inline w-3 h-3 align-text-top" /> {{ sesi.waktu }}</div>
                    </div>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(siswa, sIndex) in siswas" :key="siswa.id" class="hover:bg-slate-50 transition-colors group">
                  <td class="sticky left-0 z-10 bg-white group-hover:bg-slate-50 border-r border-b border-slate-100 p-3 text-center text-xs font-bold text-slate-400">{{ sIndex + 1 }}</td>
                  <td class="sticky left-[48px] z-10 bg-white group-hover:bg-slate-50 border-r border-b border-slate-100 p-3 text-xs font-bold text-slate-700">{{ siswa.nama_lengkap }}</td>
                  <!-- Sel Absensi -->
                  <td v-for="sesi in selectedSesis" :key="sesi.id" class="border-r border-b border-slate-100 p-3 text-center">
                    <span :class="[
                      'inline-flex items-center justify-center w-6 h-6 rounded-md text-[11px] font-black',
                      getAbsensiColor(sesi.absensi[siswa.id])
                    ]">
                      {{ sesi.absensi[siswa.id] || '-' }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <!-- Materi Section -->
          <div class="p-6 bg-slate-50">
            <h4 class="text-xs font-black uppercase tracking-widest text-slate-500 mb-4">Materi yang Disampaikan</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div v-for="(sesi, index) in selectedSesis" :key="'materi-'+sesi.id" class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                  <span class="bg-indigo-100 text-indigo-700 text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded">Sesi {{ index + 1 }}</span>
                  <span class="text-[11px] font-bold text-slate-700 truncate">{{ sesi.mapel }}</span>
                </div>
                <p class="text-[11px] text-slate-600 leading-relaxed whitespace-pre-wrap">{{ sesi.materi || '(Tidak ada deskripsi materi)' }}</p>
              </div>
            </div>
          </div>
        </div>
        
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

definePageMeta({
  layout: 'kurikulum',
  middleware: 'kurikulum',
  title: 'Pantau Jurnal'
})

const kelasList = ref([])
const siswas = ref([])
const jurnals = ref({}) // key: tanggal
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
  kelas_id: '',
  bulan: new Date().toISOString().substring(0, 7) // YYYY-MM
})

// Modal State
const isModalOpen = ref(false)
const selectedDate = ref('')
const selectedSesis = computed(() => {
  if (!selectedDate.value || !jurnals.value[selectedDate.value]) return []
  return jurnals.value[selectedDate.value]
})

const openModal = (dateStr) => {
  if (!hasData(dateStr)) return
  selectedDate.value = dateStr
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  selectedDate.value = ''
}

const getAuthToken = () => useCookie('auth_token').value

const fetchKelas = async () => {
  try {
    const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/kurikulum/jurnal/kelas', {
      headers: { Authorization: `Bearer ${getAuthToken()}` }
    })
    if (res.success) {
      kelasList.value = res.data
    }
  } catch (error) {
    console.error('Failed to fetch kelas list', error)
  }
}

const fetchData = async () => {
  if (!filter.value.kelas_id || !filter.value.bulan) {
    jurnals.value = {}
    siswas.value = []
    return
  }
  
  isLoading.value = true
  try {
    const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/kurikulum/jurnal', {
      headers: { Authorization: `Bearer ${getAuthToken()}` },
      query: filter.value
    })
    if (res.success) {
      siswas.value = res.siswas || []
      jurnals.value = res.jurnals_per_tanggal || {}
    }
  } catch (error) {
    console.error('Failed to fetch jurnal', error)
  } finally {
    isLoading.value = false
  }
}

const handleFilterChange = () => {
  if (!isDesktop.value && filter.value.kelas_id) {
    activeTabMobile.value = 'table'
  }
  fetchData()
}

// Calendar Logic
const blankDays = computed(() => {
  if (!filter.value.bulan) return []
  const [year, month] = filter.value.bulan.split('-')
  const firstDay = new Date(year, month - 1, 1).getDay()
  // Adjust so Monday is 0, Sunday is 6
  let offset = firstDay === 0 ? 6 : firstDay - 1
  return Array.from({ length: offset }, (_, i) => i)
})

const daysInMonth = computed(() => {
  if (!filter.value.bulan) return []
  const [year, month] = filter.value.bulan.split('-')
  const daysCount = new Date(year, month, 0).getDate()
  
  return Array.from({ length: daysCount }, (_, i) => {
    const day = i + 1
    const dayStr = day < 10 ? '0' + day : '' + day
    const fullDate = `${filter.value.bulan}-${dayStr}`
    return { day, fullDate }
  })
})

const hasData = (dateStr) => {
  return jurnals.value[dateStr] && jurnals.value[dateStr].length > 0
}

const formatDateComplete = (dateStr) => {
  if (!dateStr) return '-'
  const date = new Date(dateStr)
  return new Intl.DateTimeFormat('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }).format(date)
}

const getAbsensiColor = (status) => {
  switch (status) {
    case 'H': return 'bg-emerald-100 text-emerald-700'
    case 'S': return 'bg-amber-100 text-amber-700'
    case 'I': return 'bg-blue-100 text-blue-700'
    case 'A': return 'bg-rose-100 text-rose-700'
    default: return 'bg-slate-100 text-slate-400'
  }
}

onMounted(() => {
  windowWidth.value = window.innerWidth
  window.addEventListener('resize', handleResize)
  fetchKelas()
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
/* Disable body scroll when modal is open if needed, handled mostly by overlay */
</style>
