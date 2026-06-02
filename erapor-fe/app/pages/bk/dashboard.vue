<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Dashboard Bimbingan Konseling</h2>
        <p class="text-sm font-semibold text-slate-500 mt-1">Pantau statistik kedisiplinan dan penanganan kasus siswa.</p>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="bg-white rounded-2xl p-10 flex flex-col items-center justify-center shadow-sm opacity-60">
        <div class="w-8 h-8 border-4 border-rose-400 border-t-transparent rounded-full animate-spin mb-4"></div>
        <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Dashboard...</span>
    </div>

    <!-- Stats Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        <!-- Stat 1 -->
        <div class="bg-gradient-to-br from-rose-500 to-rose-600 rounded-2xl p-6 text-white shadow-lg shadow-rose-500/30 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 text-7xl opacity-20 transform group-hover:scale-110 group-hover:rotate-12 transition-transform duration-500">🚨</div>
            <p class="text-[10px] font-black uppercase tracking-widest text-rose-200 mb-1">Kasus Hari Ini</p>
            <h3 class="text-4xl font-black">{{ stats.kasus_hari_ini || 0 }}</h3>
            <p class="text-[10px] font-medium text-rose-100 mt-2">Pelanggaran tercatat hari ini</p>
        </div>

        <!-- Stat 2 -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 text-7xl opacity-10 transform group-hover:scale-110 transition-transform duration-500">👨‍🎓</div>
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Siswa Bermasalah</p>
            <h3 class="text-4xl font-black">{{ stats.siswa_bermasalah || 0 }}</h3>
            <p class="text-[10px] font-medium text-slate-400 mt-2">Siswa dengan riwayat poin negatif</p>
        </div>

        <!-- Stat 3 -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 text-7xl opacity-5 transform group-hover:scale-110 transition-transform duration-500">⚖️</div>
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Total Penanganan</p>
            <h3 class="text-4xl font-black text-slate-800">{{ stats.total_penanganan || 0 }}</h3>
            <p class="text-[10px] font-medium text-slate-400 mt-2">Riwayat penanganan/SP yang dikeluarkan</p>
        </div>
    </div>

    <!-- Top Pelanggaran -->
    <div v-if="!isLoading" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-black uppercase tracking-widest text-slate-700 flex items-center gap-2">
                <span class="text-lg">📈</span> Top 5 Pelanggaran Bulan Ini
            </h3>
        </div>
        <div class="p-6">
            <div v-if="stats.top_pelanggaran && stats.top_pelanggaran.length > 0" class="space-y-4">
                <div v-for="(item, index) in stats.top_pelanggaran" :key="index" class="flex items-center gap-4">
                    <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center font-black text-xs">{{ index + 1 }}</div>
                    <div class="flex-grow">
                        <p class="text-sm font-bold text-slate-800">{{ item.pelanggaran?.nama_pelanggaran }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-black text-rose-600">{{ item.total }} <span class="text-[10px] text-slate-400 uppercase tracking-widest">Kasus</span></p>
                    </div>
                </div>
            </div>
            <div v-else class="text-center py-8">
                <span class="text-4xl opacity-20 mb-2 block">✨</span>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Belum ada pelanggaran bulan ini. Bagus!</p>
            </div>
        </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

definePageMeta({
  layout: 'bk',
  middleware: 'bk',
  title: 'Dashboard BK'
})

const isLoading = ref(true)
const stats = ref({})

const fetchDashboard = async () => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch('http://localhost:8000/api/bk/dashboard', {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            stats.value = response.data
        }
    } catch (error) {
        console.error('Failed to fetch dashboard:', error)
    } finally {
        isLoading.value = false
    }
}

onMounted(() => {
    fetchDashboard()
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-fadeIn { animation: fadeIn 0.4s ease-out forwards; }
</style>
