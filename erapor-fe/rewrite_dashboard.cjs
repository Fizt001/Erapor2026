const fs = require('fs');

let adminDash = fs.readFileSync('app/pages/admin/dashboard.vue', 'utf8');
let kurikulumDash = fs.readFileSync('app/pages/kurikulum/dashboard.vue', 'utf8');

// I will extract the script block from Kurikulum dash to keep the logic
const kurikulumScript = kurikulumDash.match(/<script setup>([\s\S]*?)<\/script>/)[1];

// And inject it into a template based on Admin dash
const newTemplate = `
<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
<!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex flex-col xl:flex-row xl:overflow-hidden overflow-y-auto relative">
      
      <!-- Panel Dock Kiri -->
      <div class="xl:w-[360px] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col xl:h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] xl:overflow-y-auto custom-scrollbar">
        <div class="p-6 space-y-6">
          
          <!-- Welcome Widget -->
          <div class="bg-gradient-to-br from-amber-500 to-amber-700 rounded-2xl p-5 shadow-sm text-white relative overflow-hidden">
            <div class="relative z-10">
              <h2 class="text-lg font-extrabold mb-1">Halo, {{ userProfile?.name?.split(' ')[0] || 'Waka Kurikulum' }} <AppIcon name="hand-raised" class="inline-block w-5 h-5" /></h2>
              <p class="text-amber-100 text-xs leading-relaxed">
                Tahun Ajaran <span class="font-bold text-white bg-amber-900/50 px-1.5 py-0.5 rounded">{{ ta_aktif?.tahun || 'Memuat...' }}</span>
              </p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-10">
              <svg class="w-24 h-24 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 20 20"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path></svg>
            </div>
          </div>

          <!-- Status Institusi Widget (Dari role Admin) -->
          <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-widest mb-4 flex items-center"><span class="mr-2"><AppIcon name="pin" /></span> Status Institusi</h3>
            <div class="space-y-3">
              <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-[9px] text-slate-400 uppercase tracking-widest font-black mb-1">Identitas Sekolah</p>
                <p class="font-bold text-slate-800 text-sm leading-tight">{{ sekolah?.nama || 'Memuat...' }}</p>
                <p class="text-[10px] text-slate-500 mt-1 font-medium">NPSN: {{ sekolah?.npsn || '-' }}</p>
              </div>
            </div>
          </div>
          
          <!-- Quick Actions -->
          <div class="bg-slate-900 p-5 rounded-2xl shadow-sm text-white relative overflow-hidden">
             <div class="relative z-10">
                <h3 class="text-xs font-bold uppercase tracking-widest mb-3 text-slate-300 flex items-center"><AppIcon name="bolt" /> Akses Cepat</h3>
                <div class="space-y-2">
                    <NuxtLink to="/kurikulum/mapel" class="block bg-slate-800 hover:bg-amber-600 transition-colors p-3 rounded-xl text-xs font-semibold flex justify-between items-center group">
                        Master Mata Pelajaran
                        <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                    </NuxtLink>
                    <NuxtLink to="/kurikulum/pengampu" class="block bg-slate-800 hover:bg-amber-600 transition-colors p-3 rounded-xl text-xs font-semibold flex justify-between items-center group">
                        Plot Guru Pengampu
                        <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                    </NuxtLink>
                    <NuxtLink to="/kurikulum/wali-kelas" class="block bg-slate-800 hover:bg-amber-600 transition-colors p-3 rounded-xl text-xs font-semibold flex justify-between items-center group">
                        Penugasan Wali Kelas
                        <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                    </NuxtLink>
                </div>
             </div>
             <div class="absolute -right-4 -bottom-4 text-slate-800 opacity-50 text-6xl font-black">
                +
             </div>
          </div>

          <!-- Readiness Balance (dari kurikulum sebelumnya) -->
          <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col">
              <div class="p-5 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
                  <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-400 to-indigo-600 shadow-sm flex items-center justify-center text-white text-xl shrink-0"><AppIcon name="scale" class="w-5 h-5 inline-block mr-1" /></div>
                  <div>
                      <h3 class="text-sm font-black text-slate-800">Neraca Kesiapan</h3>
                      <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Progress Persiapan</p>
                  </div>
              </div>
              <div class="p-5 flex-grow space-y-5">
                  <div>
                      <div class="flex justify-between items-end mb-2">
                          <div>
                              <h4 class="text-[11px] font-bold text-slate-700 uppercase tracking-widest">Wali Kelas</h4>
                              <p class="text-[10px] text-slate-500 mt-0.5" v-if="stats.alerts?.kelasTanpaWalas > 0"><b class="text-rose-500">{{ stats.alerts?.kelasTanpaWalas }} kelas</b> kosong</p>
                              <p class="text-[10px] text-emerald-600 font-bold mt-0.5" v-else>Terisi Penuh <AppIcon name="check-circle" class="w-4 h-4 inline-block align-text-bottom" /></p>
                          </div>
                          <span class="text-lg font-black" :class="readinessWalas === 100 ? 'text-emerald-500' : 'text-slate-700'">{{ readinessWalas }}%</span>
                      </div>
                      <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                          <div class="h-full rounded-full transition-all duration-1000 ease-out" 
                                :class="readinessWalas === 100 ? 'bg-emerald-500' : (readinessWalas > 50 ? 'bg-amber-400' : 'bg-rose-500')"
                                :style="{ width: \`\${readinessWalas}%\` }"></div>
                      </div>
                  </div>
                  <div>
                      <div class="flex justify-between items-end mb-2">
                          <div>
                              <h4 class="text-[11px] font-bold text-slate-700 uppercase tracking-widest">Guru Pengampu</h4>
                              <p class="text-[10px] text-slate-500 mt-0.5" v-if="stats.alerts?.mapelTanpaGuru > 0"><b class="text-amber-500">{{ stats.alerts?.mapelTanpaGuru }} mapel</b> kosong</p>
                              <p class="text-[10px] text-emerald-600 font-bold mt-0.5" v-else>Terisi Penuh <AppIcon name="check-circle" class="w-4 h-4 inline-block align-text-bottom" /></p>
                          </div>
                          <span class="text-lg font-black" :class="readinessMapel === 100 ? 'text-emerald-500' : 'text-slate-700'">{{ readinessMapel }}%</span>
                      </div>
                      <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                          <div class="h-full rounded-full transition-all duration-1000 ease-out" 
                                :class="readinessMapel === 100 ? 'bg-emerald-500' : (readinessMapel > 50 ? 'bg-amber-400' : 'bg-rose-500')"
                                :style="{ width: \`\${readinessMapel}%\` }"></div>
                      </div>
                  </div>
                  <div class="pt-4 border-t border-slate-100 mt-auto">
                      <NuxtLink to="/kurikulum/struktur-umum" class="w-full block text-center py-2.5 bg-slate-50 hover:bg-amber-50 border border-slate-200 hover:border-amber-200 text-slate-700 hover:text-amber-700 font-bold text-[10px] uppercase tracking-widest rounded-xl transition-colors">
                          Buka Pengaturan
                      </NuxtLink>
                  </div>
              </div>
          </div>

        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="xl:flex-1 flex-shrink-0 bg-slate-50 flex flex-col xl:h-full min-w-0 xl:overflow-y-auto custom-scrollbar">
        
        <!-- Loading State -->
        <div v-if="isLoading" class="flex-grow flex items-center justify-center py-12">
          <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-amber-600"></div>
        </div>

        <div v-else class="p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">
            
            <!-- STATISTIK UTAMA (4 COLUMNS) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Card 1 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-amber-200">
                    <div class="h-12 w-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 text-2xl border border-amber-100"><AppIcon name="book-open" /></div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Mata Pelajaran</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ stats.totalMapel || 0 }}</p>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-orange-200">
                    <div class="h-12 w-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 text-2xl border border-orange-100"><AppIcon name="academic-cap" /></div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Guru Pengampu</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ stats.totalGuru || 0 }}</p>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-rose-200">
                    <div class="h-12 w-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 text-2xl border border-rose-100"><AppIcon name="building" /></div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total Kelas</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ stats.totalKelas || 0 }}</p>
                    </div>
                </div>
                <!-- Card 4 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-indigo-200">
                    <div class="h-12 w-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-2xl border border-indigo-100"><AppIcon name="flag" /></div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Ekstrakurikuler</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ stats.totalEkskul || 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- MAIN GRID FOR CHARTS & NOTIFICATIONS -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Chart Distribusi JP -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60">
                    <div class="mb-6 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-xl border border-indigo-100"><AppIcon name="chart-bar" /></div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Distribusi Jam Pelajaran</h3>
                            <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest">Berdasarkan Tingkat Kelas</p>
                        </div>
                    </div>
                    <div class="h-64 flex-1 flex justify-center">
                        <ClientOnly>
                            <Bar :data="barChartData" :options="barChartOptions" />
                            <template #fallback>
                                <div class="flex items-center justify-center h-full text-slate-400 text-xs font-bold">Memuat Grafik...</div>
                            </template>
                        </ClientOnly>
                    </div>
                </div>
                
                <!-- Chart Komposisi Mapel -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60">
                    <div class="mb-6 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 text-xl border border-amber-100"><AppIcon name="chart-pie" /></div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Komposisi Mata Pelajaran</h3>
                            <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest">Struktur Kurikulum</p>
                        </div>
                    </div>
                    <div class="h-64 relative flex items-center justify-center">
                        <ClientOnly>
                            <Doughnut :data="doughnutChartData" :options="doughnutChartOptions" />
                            <template #fallback>
                                <div class="flex items-center justify-center h-full text-slate-400 text-xs font-bold">Memuat Grafik...</div>
                            </template>
                        </ClientOnly>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-[-20px]">
                            <span class="text-2xl font-black text-slate-800">{{ stats.totalMapel }}</span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Mapel</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Traffic Notifikasi SP2 & SP3 -->
            <div v-if="stats.notifikasi?.length > 0" class="bg-white rounded-2xl shadow-sm border border-rose-200/60 overflow-hidden mt-6">
                <div class="p-5 border-b border-rose-100 bg-rose-50/50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-rose-600 border border-rose-200 shadow-sm animate-pulse"><AppIcon name="exclamation-triangle" class="w-6 h-6" /></div>
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
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
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

const { sekolah, ta_aktif, fetchSekolah } = useSekolah()
onMounted(() => {
    fetchSekolah()
})

const userCookie = useCookie('user_profile')
const tokenCookie = useCookie('auth_token')

const userProfile = computed(() => {
  if (!userCookie.value) return null
  return typeof userCookie.value === 'string' ? JSON.parse(userCookie.value) : userCookie.value
})

const { data: response, pending: isLoading } = await useFetch(import.meta.env.VITE_API_BASE_URL + '/api/kurikulum/dashboard', {
  headers: { Authorization: \`Bearer \${tokenCookie.value}\` }
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
  alerts: { kelasTanpaWalas: 0, mapelTanpaGuru: 0 },
  notifikasi: []
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
    y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#f1f5f9' } },
    x: { grid: { display: false } }
  }
}

// Chart 2: Komposisi Mapel (Doughnut Chart)
const doughnutChartData = computed(() => ({
  labels: ['Mapel Umum', 'Mapel Kejuruan'],
  datasets: [
    {
      backgroundColor: ['#f59e0b', '#10b981'],
      borderWidth: 0,
      hoverOffset: 4,
      data: [
        stats.value.distribusiMapel?.umum || 0,
        stats.value.distribusiMapel?.kejuruan || 0
      ]
    }
  ]
}))

const doughnutChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  cutout: '75%',
  plugins: {
    legend: { position: 'bottom', labels: { boxWidth: 12, usePointStyle: true, padding: 20, font: { size: 11, family: "'Inter', sans-serif" } } }
  }
}

// Logic for Readiness Metrics
const readinessWalas = computed(() => {
  const total = stats.value.totalKelas || 0
  const kosong = stats.value.alerts?.kelasTanpaWalas || 0
  if (total === 0) return 0
  return Math.round(((total - kosong) / total) * 100)
})

const readinessMapel = computed(() => {
  const total = stats.value.totalMapel || 0
  const kosong = stats.value.alerts?.mapelTanpaGuru || 0
  if (total === 0) return 0
  return Math.round(((total - kosong) / total) * 100)
})
</script>

<style scoped>
.custom-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.custom-scrollbar::-webkit-scrollbar { display: none; }
</style>
`;

fs.writeFileSync('app/pages/kurikulum/dashboard.vue', newTemplate);
console.log('Dashboard updated');
