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
        
        <!-- Superadmin Empty State -->
        <div v-if="isSuperadminWithoutImpersonation" class="flex-grow flex flex-col items-center justify-center py-20 text-center bg-white rounded-2xl shadow-sm border border-slate-200 m-8">
          <div class="text-amber-500 mb-6 bg-amber-50 p-5 rounded-full border border-amber-100 shadow-inner">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </div>
          <h3 class="text-2xl font-black text-slate-800 tracking-tight">Menunggu Pilihan Guru</h3>
          <p class="text-base text-slate-500 mt-3 max-w-md">Anda sedang dalam Mode Superadmin. Silakan pilih nama guru dari bilah menu di <strong class="text-amber-600 font-bold">pojok kanan atas</strong> untuk melihat Dashboard Guru.</p>
        </div>

        <!-- Loading State -->
        <div v-else-if="isLoading" class="flex-grow flex items-center justify-center py-12">
          <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-sky-600"></div>
        </div>

        <div v-else class="p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">
            
            <!-- STATISTIK UTAMA (4 COLUMNS) -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
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
                <!-- Card 4 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center gap-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-rose-200">
                    <div class="h-12 w-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 text-2xl border border-rose-100">📓</div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5 truncate">Total Pertemuan</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ dashboardData?.stats?.total_pertemuan || 0 }}</p>
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

          <!-- ADS MODAL (PENGINGAT ABSENSI) -->
          <Transition name="bounce">
            <div v-if="showAdsModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6">
              <!-- Backdrop -->
              <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeAdsModal"></div>
              
              <!-- Modal Content -->
              <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-sm relative z-10 overflow-hidden transform transition-all border-4 border-white">
                <!-- Close Button -->
                <button @click="closeAdsModal" class="absolute top-4 right-4 bg-black/20 hover:bg-black/40 backdrop-blur-md text-white rounded-full p-2 z-20 transition-colors">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                
                <!-- Illustration Area -->
                <div class="bg-gradient-to-br from-amber-400 to-orange-500 h-48 relative flex items-center justify-center overflow-hidden">
                  <!-- Decorative circles -->
                  <div class="absolute top-[-20%] right-[-10%] w-32 h-32 bg-white/20 rounded-full blur-xl"></div>
                  <div class="absolute bottom-[-10%] left-[-20%] w-40 h-40 bg-orange-600/30 rounded-full blur-2xl"></div>
                  
                  <div class="relative z-10 text-center animate-bounce-slow">
                    <div class="text-[5rem] leading-none filter drop-shadow-lg">🏃‍♂️💨</div>
                  </div>
                </div>
                
                <!-- Content Area -->
                <div class="p-6 text-center bg-white relative">
                  <!-- Badge -->
                  <div class="absolute -top-5 left-1/2 -translate-x-1/2 bg-rose-500 text-white text-xs font-black px-4 py-1.5 rounded-full shadow-lg border-2 border-white uppercase tracking-widest whitespace-nowrap">
                    Penting Hari Ini!
                  </div>
                  
                  <h3 class="text-xl font-black text-slate-800 mt-2 mb-2 leading-tight">Waktunya Mengajar!<br/>Pertemuan di kelas berapa?</h3>
                  <p class="text-sm text-slate-500 font-medium mb-6 leading-relaxed">
                    Jangan lupa, ini adalah **pertemuan saat mengajar**. Yuk, catat absensi pertemuan kelas Anda sekarang sebelum kelupaan!
                  </p>
                  
                  <div class="space-y-3">
                    <NuxtLink to="/guru/absensi" @click="closeAdsModal" class="flex items-center justify-center w-full bg-orange-500 hover:bg-orange-600 text-white font-black py-3.5 px-4 rounded-xl shadow-lg shadow-orange-200 transition-all hover:-translate-y-1">
                      <span>Catat Pertemuan Sekarang</span>
                      <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </NuxtLink>
                    <button @click="closeAdsModal" class="w-full bg-slate-50 hover:bg-slate-100 text-slate-500 font-bold py-3 px-4 rounded-xl transition-colors text-sm">
                      Nanti Saja, Masuk Dashboard
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </Transition>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'
import { Bar } from 'vue-chartjs'

definePageMeta({ layout: "guru", middleware: "guru", title: 'Dashboard Guru Mapel' })

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const tokenCookie = useCookie('auth_token')
const showAdsModal = ref(false)

onMounted(() => {
  const hasSeenAds = sessionStorage.getItem('hasSeenGuruAdsModal')
  if (!hasSeenAds) {
    // Show modal slightly after page load for better effect
    setTimeout(() => {
      showAdsModal.value = true
    }, 800)
  }
})

function closeAdsModal() {
  showAdsModal.value = false
  sessionStorage.setItem('hasSeenGuruAdsModal', 'true')
}

const { data: response, pending: isLoading, error } = await useFetch(import.meta.env.VITE_API_BASE_URL + '/api/guru/dashboard', {
  headers: {
    'Authorization': `Bearer ${tokenCookie.value}`,
    'Accept': 'application/json'
  }
})

const errorMessage = computed(() => error.value?.message || (!response.value?.success && response.value?.message ? response.value?.message : ''))
const userProfile = useCookie('user_profile')

const isSuperadminWithoutImpersonation = computed(() => {
  let role = null;
  if (typeof userProfile.value === 'string') {
    try {
      role = JSON.parse(userProfile.value)?.role
    } catch (e) {
      role = null;
    }
  } else {
    role = userProfile.value?.role
  }
  return role === 'superadmin' && !!errorMessage.value
})

const dashboardData = computed(() => response.value?.data || null)

const chartData = computed(() => {
  const dataList = dashboardData.value?.grafik_nilai || [];
  const periodeLabels = dashboardData.value?.periode_labels || [];
  
  // Daftar warna untuk masing-masing periode
  const bgColors = ['#6366f1', '#14b8a6', '#f59e0b', '#ef4444']; 
  const hoverColors = ['#4f46e5', '#0d9488', '#d97706', '#dc2626'];

  const datasets = periodeLabels.map((periodeName, index) => {
    return {
      label: periodeName,
      backgroundColor: bgColors[index % bgColors.length],
      hoverBackgroundColor: hoverColors[index % hoverColors.length],
      borderRadius: 4,
      data: dataList.map(item => item.series[index])
    };
  });
  
  return {
    labels: dataList.map(item => `${item.kelas} - ${item.mapel}`),
    datasets: datasets
  }
})

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  indexAxis: 'y', // Horizontal bar chart
  plugins: {
    legend: { 
      display: true,
      position: 'top',
      align: 'end',
      labels: { font: { size: 10, weight: 'bold' }, usePointStyle: true, boxWidth: 8 }
    },
    tooltip: {
      callbacks: {
        label: (context) => ` ${context.dataset.label}: ${context.parsed.x}`
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

.animate-bounce-slow {
  animation: bounce-slow 3s infinite;
}

@keyframes bounce-slow {
  0%, 100% {
    transform: translateY(-5%);
    animation-timing-function: cubic-bezier(0.8,0,1,1);
  }
  50% {
    transform: none;
    animation-timing-function: cubic-bezier(0,0,0.2,1);
  }
}

.bounce-enter-active {
  animation: bounce-in 0.5s;
}
.bounce-leave-active {
  animation: bounce-in 0.3s reverse;
}
@keyframes bounce-in {
  0% {
    transform: scale(0.3);
    opacity: 0;
  }
  50% {
    transform: scale(1.05);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}
</style>
