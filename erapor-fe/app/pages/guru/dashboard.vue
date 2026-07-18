<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex flex-col xl:flex-row overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="xl:w-[360px] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] overflow-y-auto custom-scrollbar">
        <div class="p-6 space-y-6">
          
          <!-- Welcome Widget -->
          <div class="bg-gradient-to-br from-sky-600 to-indigo-800 rounded-2xl p-5 shadow-sm text-white relative overflow-hidden">
            <div class="relative z-10">
              <h2 class="text-lg font-extrabold mb-1">Halo, {{ dashboardData?.user?.name?.split(' ')[0] || 'Bapak/Ibu' }} 👋</h2>
              <p class="text-sky-100 text-xs leading-relaxed">
                Tahun Ajaran <span class="font-bold text-white bg-indigo-900/50 px-1.5 py-0.5 rounded">{{ dashboardData?.akademik?.tahun_ajaran || '...' }}</span>
                <br>Periode: <span class="font-bold text-white">{{ dashboardData?.akademik?.periode || '...' }}</span>
              </p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-10">
              <svg class="w-24 h-24 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"></path><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
            </div>
          </div>

          <!-- Peran Status Widget -->
          <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-widest mb-4 flex items-center"><span class="mr-2">🛡️</span> Hak Akses Aktif</h3>
            <div class="space-y-3">
              <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3 border-l-4 border-l-sky-500">
                <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center text-sm font-black">GM</div>
                <div>
                  <p class="font-bold text-slate-800 text-[11px] uppercase tracking-wider">Guru Mata Pelajaran</p>
                  <p class="text-[9px] text-slate-500 mt-0.5">Akses input nilai formatif & sumatif</p>
                </div>
              </div>

            </div>
          </div>

        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0 overflow-y-auto custom-scrollbar">
        
        <!-- Loading State -->
        <div v-if="isLoading" class="flex-grow flex items-center justify-center py-12">
          <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-sky-600"></div>
        </div>

        <div v-else class="p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">
            
            <!-- STATISTIK UTAMA (3 COLUMNS) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Card 1 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center gap-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-indigo-200">
                    <div class="h-12 w-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-2xl border border-indigo-100">🏫</div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Kelas Diajar</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ dashboardData?.stats?.total_kelas || 0 }}</p>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center gap-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-sky-200">
                    <div class="h-12 w-12 rounded-xl bg-sky-50 flex items-center justify-center text-sky-600 text-2xl border border-sky-100">📚</div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Mata Pelajaran</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ dashboardData?.stats?.total_mapel || 0 }}</p>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center gap-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-emerald-200">
                    <div class="h-12 w-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-2xl border border-emerald-100">👥</div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total Siswa</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ dashboardData?.stats?.total_siswa || 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- AKSI CEPAT -->
            <div>
              <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center">⚡ Akses Cepat Guru</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Tombol Formatif -->
                <NuxtLink to="/guru/formatif/nilai" class="group bg-white rounded-2xl p-5 border border-slate-200/60 shadow-sm hover:border-sky-300 hover:shadow-md hover:shadow-sky-100 transition-all cursor-pointer block relative overflow-hidden">
                  <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center text-sky-600 group-hover:bg-sky-500 group-hover:text-white transition-colors border border-sky-100 group-hover:border-sky-500 shadow-sm">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <svg class="w-5 h-5 text-slate-300 group-hover:text-sky-500 transition-colors transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                  </div>
                  <div class="font-black text-slate-800 text-sm mb-1 uppercase tracking-wide">Input Formatif</div>
                  <div class="text-[11px] font-medium text-slate-500">Isi capaian harian tiap Tujuan Pembelajaran</div>
                </NuxtLink>

                <!-- Tombol Sumatif -->
                <NuxtLink to="/guru/sumatif/nilai" class="group bg-white rounded-2xl p-5 border border-slate-200/60 shadow-sm hover:border-indigo-300 hover:shadow-md hover:shadow-indigo-100 transition-all cursor-pointer block relative overflow-hidden">
                  <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 group-hover:bg-indigo-500 group-hover:text-white transition-colors border border-indigo-100 group-hover:border-indigo-500 shadow-sm">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <svg class="w-5 h-5 text-slate-300 group-hover:text-indigo-500 transition-colors transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                  </div>
                  <div class="font-black text-slate-800 text-sm mb-1 uppercase tracking-wide">Input Sumatif</div>
                  <div class="text-[11px] font-medium text-slate-500">Isi nilai akhir ujian (STS / SAS)</div>
                </NuxtLink>

                <!-- Tombol Rekapitulasi -->
                <NuxtLink to="/guru/sumatif/rekap" class="group bg-white rounded-2xl p-5 border border-slate-200/60 shadow-sm hover:border-emerald-300 hover:shadow-md hover:shadow-emerald-100 transition-all cursor-pointer block relative overflow-hidden">
                  <div class="absolute top-0 right-0 bg-emerald-500 text-white text-[9px] font-black px-2.5 py-1 rounded-bl-xl shadow-sm">REKOMENDASI</div>
                  
                  <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-500 group-hover:text-white transition-colors border border-emerald-100 group-hover:border-emerald-500 shadow-sm">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <svg class="w-5 h-5 text-slate-300 group-hover:text-emerald-500 transition-colors transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                  </div>
                  <div class="font-black text-slate-800 text-sm mb-1 uppercase tracking-wide">Rekap Mapel</div>
                  <div class="text-[11px] font-medium text-slate-500">Cetak leger dan pantau nilai akhir kelas</div>
                </NuxtLink>
              </div>
            </div>
            
            <!-- GRAFIK NILAI SISWA -->
            <div v-if="dashboardData && dashboardData.grafik_nilai" class="mt-8">
              <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center">📊 Rata-Rata Nilai Akhir Siswa (Per Kelas/Mapel)</h3>
              <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
                  <div class="h-64 sm:h-80" v-if="dashboardData.grafik_nilai.length > 0">
                      <ClientOnly>
                          <Bar :data="chartData" :options="chartOptions" />
                          <template #fallback>
                              <div class="flex items-center justify-center h-full text-slate-400 text-xs font-bold">Memuat Grafik...</div>
                          </template>
                      </ClientOnly>
                  </div>
                  <div v-else class="h-64 sm:h-80 flex flex-col items-center justify-center text-center">
                      <div class="text-4xl mb-3 opacity-50 grayscale">📉</div>
                      <p class="text-sm font-bold text-slate-600">Belum ada data nilai</p>
                      <p class="text-[11px] text-slate-400 mt-1 max-w-sm">Grafik rata-rata akan muncul setelah Anda memasukkan dan menyimpan nilai akhir Sumatif (STS/SAS) untuk kelas dan mapel yang Anda ampu.</p>
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
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'
import { Bar } from 'vue-chartjs'

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend)

definePageMeta({ layout: "guru", middleware: "guru", title: 'Dashboard Guru' })

const tokenCookie = useCookie('auth_token')

const { data: response, pending: isLoading } = await useFetch('http://localhost:8000/api/guru/dashboard', {
  headers: {
    'Authorization': `Bearer ${tokenCookie.value}`,
    'Accept': 'application/json'
  }
})

const dashboardData = computed(() => response.value?.data || null)

const chartData = computed(() => {
  const dataList = dashboardData.value?.grafik_nilai || [];
  
  return {
    labels: dataList.map(item => `${item.kelas} - ${item.mapel}`),
    datasets: [
      {
        label: 'Rata-rata Nilai',
        backgroundColor: '#6366f1', // indigo-500
        hoverBackgroundColor: '#4f46e5', // indigo-600
        borderRadius: 6,
        data: dataList.map(item => item.rata_rata)
      }
    ]
  }
})

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  indexAxis: 'y', // Horizontal bar chart
  plugins: {
    legend: { display: false },
    tooltip: {
      callbacks: {
        label: (context) => ` Rata-rata: ${context.parsed.x}`
      }
    }
  },
  scales: {
    x: {
      beginAtZero: true,
      max: 100,
      grid: { borderDash: [2, 4], color: '#f1f5f9' },
      ticks: { font: { size: 10, weight: 'bold' }, color: '#94a3b8' }
    },
    y: {
      grid: { display: false },
      ticks: { 
          font: { size: 10, weight: 'bold' }, 
          color: '#475569',
          callback: function(value) {
             let label = this.getLabelForValue(value);
             if (label.length > 25) {
                 return label.substr(0, 25) + '...';
             }
             return label;
          }
      }
    }
  }
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
