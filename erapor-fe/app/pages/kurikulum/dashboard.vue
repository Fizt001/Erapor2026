<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <div class="flex-1 flex overflow-hidden relative">
      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0">
        <div class="w-full h-full flex flex-col relative z-0">
          <div class="bg-transparent flex flex-col flex-1 relative min-h-0">
            
      <!-- Removed redundant header -->

            <div class="flex-1 overflow-y-auto custom-scrollbar relative z-0">
                
                <!-- Loading State -->
                <div v-if="isLoading" class="flex-grow flex items-center justify-center py-12">
                  <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
                </div>

                <div v-else class="p-6 lg:p-8 space-y-6 w-full">

                    <!-- Welcome & Quick Actions Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Welcome Widget -->
                        <div class="lg:col-span-2 bg-gradient-to-r from-indigo-600 to-blue-700 rounded-2xl p-6 border border-indigo-500 shadow-sm relative overflow-hidden flex flex-col justify-center">
                            <div class="relative z-10">
                                <h2 class="text-2xl font-extrabold mb-2 text-white">Halo, {{ userProfile?.name?.split(' ')[0] || 'Waka Kurikulum' }} 👋</h2>
                                <p class="text-indigo-100 text-sm leading-relaxed">
                                    Selamat datang di Manajemen <span class="font-bold text-white bg-indigo-900/50 px-2 py-1 rounded">Kurikulum</span>
                                </p>
                            </div>
                            <div class="absolute right-0 bottom-0 opacity-20 text-white">
                                <svg class="w-32 h-32 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path></svg>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex flex-col justify-center">
                            <h3 class="text-xs font-bold uppercase tracking-widest mb-3 text-slate-700 flex items-center">⚡ Akses Cepat</h3>
                            <div class="space-y-2">
                                <NuxtLink to="/kurikulum/mapel" class="block bg-slate-50 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-200 text-slate-700 hover:text-indigo-700 transition-colors p-3 rounded-xl text-xs font-semibold flex justify-between items-center group">
                                    Master Mata Pelajaran
                                    <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                                </NuxtLink>
                                <NuxtLink to="/kurikulum/pengampu" class="block bg-slate-50 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-200 text-slate-700 hover:text-indigo-700 transition-colors p-3 rounded-xl text-xs font-semibold flex justify-between items-center group">
                                    Plot Guru Pengampu
                                    <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                                </NuxtLink>
                                <NuxtLink to="/kurikulum/wali-kelas" class="block bg-slate-50 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-200 text-slate-700 hover:text-indigo-700 transition-colors p-3 rounded-xl text-xs font-semibold flex justify-between items-center group">
                                    Penugasan Wali Kelas
                                    <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                                </NuxtLink>
                            </div>
                        </div>
                    </div>

                    <!-- ANALYTIC CHARTS & READINESS -->
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                        
                        <!-- Col 1 & 2: Charts -->
                        <div class="xl:col-span-2 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Chart Distribusi JP -->
                                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60">
                                    <div class="mb-4">
                                        <h3 class="text-sm font-bold text-slate-800">Distribusi Jam Pelajaran</h3>
                                        <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest">Berdasarkan Tingkat Kelas</p>
                                    </div>
                                    <div class="h-48">
                                        <Bar :data="barChartData" :options="barChartOptions" />
                                    </div>
                                </div>
                                
                                <!-- Chart Komposisi Mapel -->
                                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60">
                                    <div class="mb-4">
                                        <h3 class="text-sm font-bold text-slate-800">Komposisi Mata Pelajaran</h3>
                                        <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest">Struktur Kurikulum</p>
                                    </div>
                                    <div class="h-48 relative flex items-center justify-center">
                                        <Doughnut :data="doughnutChartData" :options="doughnutChartOptions" />
                                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-[-20px]">
                                            <span class="text-2xl font-black text-slate-800">{{ stats.totalMapel }}</span>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Mapel</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Traffic Notifikasi SP2 & SP3 -->
                            <div v-if="stats.notifikasi?.length > 0" class="bg-white rounded-2xl shadow-sm border border-rose-200/60 overflow-hidden">
                                <div class="p-5 border-b border-rose-100 bg-rose-50/50 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-rose-600 border border-rose-200 shadow-sm animate-pulse">⚠️</div>
                                        <div>
                                            <h3 class="text-sm font-bold text-rose-800">Trafik Peringatan Sistem</h3>
                                            <p class="text-[10px] font-medium text-rose-600 uppercase tracking-widest">Eskalasi Kasus BK (SP2 & SP3)</p>
                                        </div>
                                    </div>
                                    <NuxtLink to="/kurikulum/penanganan" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-colors shadow-sm">Lihat Semua</NuxtLink>
                                </div>
                                <div class="divide-y divide-slate-100 max-h-[300px] overflow-y-auto custom-scrollbar">
                                    <div v-for="notif in stats.notifikasi" :key="notif.id" class="p-4 hover:bg-slate-50 flex flex-col sm:flex-row justify-between gap-4">
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <p class="text-sm font-black text-slate-800">{{ notif.siswa }}</p>
                                                <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[9px] font-black uppercase tracking-widest rounded">{{ notif.kategori }}</span>
                                            </div>
                                            <p class="text-xs font-medium text-slate-600">{{ notif.deskripsi }}</p>
                                        </div>
                                        <div class="text-left sm:text-right shrink-0">
                                            <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Kelas: <span class="text-sky-600">{{ notif.kelas }}</span></p>
                                            <p class="text-[10px] font-bold text-slate-400 mt-1">{{ notif.waktu }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Col 3: Readiness Balance -->
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col">
                            <div class="p-6 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 shadow-sm flex items-center justify-center text-white text-xl shrink-0">⚖️</div>
                                <div>
                                    <h3 class="text-sm font-black text-slate-800">Neraca Kesiapan</h3>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Progress Persiapan Akademik</p>
                                </div>
                            </div>
                            
                            <div class="p-6 flex-grow space-y-6">
                                <!-- Walas Progress -->
                                <div>
                                    <div class="flex justify-between items-end mb-2">
                                        <div>
                                            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-widest">Wali Kelas</h4>
                                            <p class="text-[10px] text-slate-500 mt-0.5" v-if="stats.alerts?.kelasTanpaWalas > 0"><b class="text-rose-500">{{ stats.alerts?.kelasTanpaWalas }} kelas</b> belum terisi</p>
                                            <p class="text-[10px] text-emerald-600 font-bold mt-0.5" v-else>Terisi Penuh ✅</p>
                                        </div>
                                        <span class="text-xl font-black" :class="readinessWalas === 100 ? 'text-emerald-500' : 'text-slate-700'">{{ readinessWalas }}%</span>
                                    </div>
                                    <div class="h-3 w-full bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-1000 ease-out" 
                                             :class="readinessWalas === 100 ? 'bg-emerald-500' : (readinessWalas > 50 ? 'bg-amber-400' : 'bg-rose-500')"
                                             :style="{ width: `${readinessWalas}%` }"></div>
                                    </div>
                                </div>

                                <!-- Pengampu Progress -->
                                <div>
                                    <div class="flex justify-between items-end mb-2">
                                        <div>
                                            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-widest">Guru Pengampu</h4>
                                            <p class="text-[10px] text-slate-500 mt-0.5" v-if="stats.alerts?.mapelTanpaGuru > 0"><b class="text-amber-500">{{ stats.alerts?.mapelTanpaGuru }} mapel</b> kosong</p>
                                            <p class="text-[10px] text-emerald-600 font-bold mt-0.5" v-else>Terisi Penuh ✅</p>
                                        </div>
                                        <span class="text-xl font-black" :class="readinessMapel === 100 ? 'text-emerald-500' : 'text-slate-700'">{{ readinessMapel }}%</span>
                                    </div>
                                    <div class="h-3 w-full bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-1000 ease-out" 
                                             :class="readinessMapel === 100 ? 'bg-emerald-500' : (readinessMapel > 50 ? 'bg-amber-400' : 'bg-rose-500')"
                                             :style="{ width: `${readinessMapel}%` }"></div>
                                    </div>
                                </div>
                                
                                <div class="pt-6 border-t border-slate-100 mt-auto">
                                    <NuxtLink to="/kurikulum/struktur-umum" class="w-full block text-center py-3 bg-slate-50 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-200 text-slate-700 hover:text-indigo-700 font-bold text-xs uppercase tracking-widest rounded-xl transition-colors">
                                        Buka Pengaturan
                                    </NuxtLink>
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
  </div>
</template>

<script setup>
import { computed } from 'vue'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  ArcElement
} from 'chart.js'
import { Bar, Doughnut } from 'vue-chartjs'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement)

definePageMeta({
  layout: 'kurikulum',
  middleware: 'kurikulum',
  title: 'Dashboard Akademik'
})

const userCookie = useCookie('user_profile')
const tokenCookie = useCookie('auth_token')

const userProfile = computed(() => {
  if (!userCookie.value) return null
  return typeof userCookie.value === 'string' ? JSON.parse(userCookie.value) : userCookie.value
})

const { data: response, pending: isLoading } = await useFetch(import.meta.env.VITE_API_BASE_URL + '/api/kurikulum/dashboard', {
  headers: { Authorization: `Bearer ${tokenCookie.value}` }
})

const defaultStats = {
  taAktif: null,
  totalGuru: 0,
  totalMapel: 0,
  totalKelas: 0,
  totalEkskul: 0,
  totalJP: 0,
  avgKkm: 0,
  distribusiMapel: { umum: 0, kejuruan: 0 },
  jpPerTingkat: { X: 0, XI: 0, XII: 0 },
  alerts: { kelasTanpaWalas: 0, mapelTanpaGuru: 0 }
}

const stats = computed(() => response.value?.data || defaultStats)

// Chart 1: Distribusi JP (Bar Chart)
const barChartData = computed(() => ({
  labels: ['Kelas X', 'Kelas XI', 'Kelas XII'],
  datasets: [
    {
      label: 'Jam Pelajaran',
      backgroundColor: ['#6366f1', '#8b5cf6', '#ec4899'],
      borderRadius: 6,
      data: [
        stats.value.jpPerTingkat?.X || 0,
        stats.value.jpPerTingkat?.XI || 0,
        stats.value.jpPerTingkat?.XII || 0
      ]
    }
  ]
}))

const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false }
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: { display: true, color: '#f1f5f9' },
      border: { display: false }
    },
    x: {
      grid: { display: false },
      border: { display: false }
    }
  }
}

// Chart 2: Komposisi Mapel (Doughnut Chart)
const doughnutChartData = computed(() => ({
  labels: ['Umum', 'Kejuruan', 'Ekstrakurikuler'],
  datasets: [
    {
      backgroundColor: ['#3b82f6', '#f43f5e', '#10b981'],
      borderWidth: 0,
      hoverOffset: 4,
      data: [
        stats.value.distribusiMapel?.umum || 0,
        stats.value.distribusiMapel?.kejuruan || 0,
        stats.value.totalEkskul || 0
      ]
    }
  ]
}))

const doughnutChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  cutout: '75%',
  plugins: {
    legend: {
      position: 'bottom',
      labels: { usePointStyle: true, padding: 20, font: { size: 11, family: 'Inter' } }
    }
  }
}

// Persentase Kesiapan (Neraca)
const readinessWalas = computed(() => {
  const total = stats.value.totalKelas || 0;
  if (total === 0) return 0;
  const tanpaWalas = stats.value.alerts?.kelasTanpaWalas || 0;
  return Math.round(((total - tanpaWalas) / total) * 100);
});

const readinessMapel = computed(() => {
  const total = stats.value.alerts?.totalMapelTerstruktur || 0;
  if (total === 0) return 0;
  const tanpaGuru = stats.value.alerts?.mapelTanpaGuru || 0;
  return Math.round(((total - tanpaGuru) / total) * 100);
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
