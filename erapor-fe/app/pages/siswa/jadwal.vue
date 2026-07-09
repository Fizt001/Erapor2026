<template>
  <div class="animate-fadeIn pb-12 mt-4 relative">
    
    <!-- Header -->
    <div class="mb-6">
      <h2 class="text-2xl font-black text-slate-800 tracking-tight">Jadwal & Agenda</h2>
      <p class="text-slate-500 text-sm font-medium mt-1">Jadwal pelajaran harian dan kalender akademik sekolah.</p>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex flex-col items-center justify-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-500 mb-4"></div>
      <p class="text-slate-500 font-medium">Memuat jadwal...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="errorMessage" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-10 flex flex-col items-center text-center">
      <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center text-2xl mb-4">
        ⚠️
      </div>
      <h3 class="text-lg font-black text-slate-800 mb-2">Gagal Memuat Data</h3>
      <p class="text-slate-500 text-sm max-w-md mx-auto">{{ errorMessage }}</p>
      <button @click="fetchData" class="mt-6 px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-lg transition-colors shadow-sm">
        Coba Lagi
      </button>
    </div>

    <div v-else class="space-y-8">
      
      <!-- Jadwal Pelajaran -->
      <div>
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl shadow-sm">
            📅
          </div>
          <div>
            <h3 class="text-lg font-black text-slate-800">Jadwal Pelajaran</h3>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">MINGGUAN</p>
          </div>
        </div>

        <div v-if="Object.keys(jadwalData.jadwal).length === 0" class="bg-white border border-slate-200 border-dashed rounded-2xl p-10 text-center">
          <span class="text-4xl block mb-2 opacity-50 grayscale">🕒</span>
          <p class="text-slate-500 font-medium text-sm">Jadwal pelajaran belum diatur oleh kurikulum.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
          <div v-for="(hariJadwal, hari) in jadwalData.jadwal" :key="hari" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col hover:border-indigo-200 transition-colors">
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-200">
              <h4 class="text-sm font-black text-slate-800 uppercase tracking-wider">{{ hari }}</h4>
            </div>
            <div class="p-0 flex-1">
              <div v-for="j in hariJadwal" :key="j.id" class="px-5 py-3 border-b border-slate-100 last:border-0 hover:bg-indigo-50/30 transition-colors">
                <div class="flex items-start gap-3">
                  <div class="w-10 pt-1 text-center">
                    <span class="text-[10px] font-bold text-slate-400 block mb-0.5">JAM KE</span>
                    <span class="w-6 h-6 mx-auto rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-xs font-black">{{ j.jam_ke }}</span>
                  </div>
                  <div class="flex-1">
                    <p class="text-[13px] font-bold text-slate-800 line-clamp-1">{{ j.mapel }}</p>
                    <p class="text-[11px] font-medium text-slate-500 line-clamp-1">{{ j.guru }}</p>
                  </div>
                  <div class="text-right">
                    <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded">{{ j.waktu_mulai }} - {{ j.waktu_selesai }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Kalender Akademik -->
      <div>
        <div class="flex items-center gap-3 mb-4 mt-10">
          <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl shadow-sm">
            🗓️
          </div>
          <div>
            <h3 class="text-lg font-black text-slate-800">Kalender Akademik</h3>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">TAHUN INI</p>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
          <div class="p-6">
            <div v-if="jadwalData.kalender.length === 0" class="py-10 flex flex-col items-center justify-center text-center">
              <span class="text-4xl mb-3 opacity-50 grayscale">📝</span>
              <p class="text-sm text-slate-500">Belum ada agenda akademik.</p>
            </div>
            
            <div v-else class="space-y-4">
              <div v-for="item in jadwalData.kalender" :key="item.id" class="flex items-start sm:items-center gap-4 p-4 rounded-xl border border-slate-100 hover:bg-slate-50 transition-colors">
                <div class="w-14 h-14 rounded-lg flex flex-col items-center justify-center border border-slate-200 bg-white shadow-sm flex-shrink-0">
                  <span class="text-[10px] font-bold text-rose-600 uppercase">{{ formatMonth(item.tanggal_mulai) }}</span>
                  <span class="text-lg font-black text-slate-800">{{ formatDay(item.tanggal_mulai) }}</span>
                </div>
                <div class="flex-1">
                  <h4 class="text-sm font-black text-slate-800">{{ item.nama_kegiatan }}</h4>
                  <p class="text-[11px] font-bold text-slate-500 mt-1">
                    {{ formatDateRange(item.tanggal_mulai, item.tanggal_selesai) }}
                  </p>
                </div>
                <div class="hidden sm:block">
                  <span class="text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider border"
                        :class="getBadgeClass(item.jenis_kegiatan)">
                    {{ item.jenis_kegiatan }}
                  </span>
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
import { ref, onMounted } from 'vue'

definePageMeta({
  layout: 'siswa',
  title: 'Jadwal & Agenda',
  middleware: 'siswa'
})

const isLoading = ref(true)
const errorMessage = ref('')
const jadwalData = ref(null)

const fetchData = async () => {
    isLoading.value = true
    errorMessage.value = ''
    
    try {
        const tokenCookie = useCookie('auth_token')
        const res = await $fetch('http://localhost:8000/api/siswa/jadwal', {
            headers: {
                'Authorization': `Bearer ${tokenCookie.value}`,
                'Accept': 'application/json'
            }
        })
        
        if (res.success && res.data) {
            jadwalData.value = res.data
        } else {
            errorMessage.value = res.message || 'Gagal mengambil data'
        }
    } catch (error) {
        console.error('Fetch error:', error)
        errorMessage.value = error.response?._data?.message || 'Terjadi kesalahan jaringan atau server.'
    } finally {
        isLoading.value = false
    }
}

const formatMonth = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return new Intl.DateTimeFormat('id-ID', { month: 'short' }).format(date);
}

const formatDay = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.getDate().toString().padStart(2, '0');
}

const formatDateRange = (start, end) => {
    if (!start) return '';
    const startObj = new Date(start);
    const startStr = new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(startObj);
    
    if (!end || start === end) {
        return startStr;
    }
    
    const endObj = new Date(end);
    const endStr = new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(endObj);
    return `${startStr} s/d ${endStr}`;
}

const getBadgeClass = (jenis) => {
    const lJenis = (jenis || '').toLowerCase();
    if (lJenis.includes('libur')) return 'bg-rose-50 text-rose-600 border-rose-100';
    if (lJenis.includes('ujian') || lJenis.includes('asesmen')) return 'bg-amber-50 text-amber-600 border-amber-100';
    return 'bg-blue-50 text-blue-600 border-blue-100';
}

onMounted(() => {
    fetchData()
})
</script>

<style scoped>
.animate-fadeIn {
  animation: fadeIn 0.3s ease-out forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
