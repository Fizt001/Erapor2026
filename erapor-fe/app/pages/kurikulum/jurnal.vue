<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Header -->
    <div class="p-6 pb-4 border-b border-slate-100 bg-white shrink-0 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.05)] z-10">
      <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-500">
            <AppIcon name="document-text" class="w-6 h-6" />
          </div>
          <div>
            <h1 class="text-xl font-black text-slate-800 leading-tight">Pantau Jurnal Mengajar</h1>
            <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mt-1">Laporan Materi & Kehadiran Guru</p>
          </div>
        </div>
        
        <!-- Filter Controls -->
        <div class="flex items-center gap-3 w-full md:w-auto">
          <select v-model="filter.guru_id" @change="fetchData" class="flex-1 md:w-64 px-4 py-2.5 rounded-xl border-2 border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 outline-none text-sm font-bold text-slate-700 transition-all">
            <option value="">-- Pilih Guru --</option>
            <option v-for="g in guruList" :key="g.id" :value="g.id">{{ g.name }}</option>
          </select>
          <input type="month" v-model="filter.bulan" @change="fetchData" class="w-40 px-4 py-2.5 rounded-xl border-2 border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 outline-none text-sm font-bold text-slate-700 transition-all">
          <button @click="fetchData" class="w-11 h-11 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-600 flex items-center justify-center transition-colors border border-indigo-100 shrink-0">
            <AppIcon name="arrow-path" class="w-5 h-5" :class="{'animate-spin': isLoading}" />
          </button>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 overflow-y-auto custom-scrollbar p-4 lg:p-8">
      <div class="max-w-7xl mx-auto">
        <!-- Empty State (No Guru Selected) -->
        <div v-if="!filter.guru_id" class="bg-white rounded-3xl p-16 text-center border border-slate-100 shadow-sm flex flex-col items-center justify-center">
          <div class="w-20 h-20 rounded-full bg-indigo-50 text-indigo-300 flex items-center justify-center mb-4">
            <AppIcon name="user" class="w-10 h-10" />
          </div>
          <h3 class="text-lg font-black text-slate-700">Pilih Guru Dahulu</h3>
          <p class="text-sm text-slate-500 mt-2 max-w-sm">Silakan pilih nama guru dari dropdown di atas untuk melihat riwayat jurnal mengajarnya.</p>
        </div>

        <!-- Loading State -->
        <div v-else-if="isLoading" class="flex justify-center p-20 opacity-50">
          <div class="flex flex-col items-center">
            <div class="w-8 h-8 border-4 border-indigo-400 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-xs font-black text-indigo-500 uppercase tracking-widest">Memuat Jurnal...</span>
          </div>
        </div>

        <!-- No Data State -->
        <div v-else-if="jurnalList.length === 0" class="bg-white rounded-3xl p-16 text-center border border-slate-100 shadow-sm flex flex-col items-center justify-center">
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
</template>

<script setup>
import { ref, onMounted } from 'vue'

definePageMeta({
  layout: 'kurikulum',
  middleware: 'kurikulum',
  title: 'Pantau Jurnal'
})

const guruList = ref([])
const jurnalList = ref([])
const isLoading = ref(false)

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

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  const date = new Date(dateStr)
  return new Intl.DateTimeFormat('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' }).format(date)
}

onMounted(() => {
  fetchGuru()
})
</script>

<style scoped>
.custom-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.custom-scrollbar::-webkit-scrollbar { display: none; }
</style>
