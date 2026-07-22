<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex flex-col xl:flex-row xl:overflow-hidden overflow-y-auto relative">
      
      <!-- Panel Dock Kiri -->
      <div class="xl:w-[360px] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col xl:h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] xl:overflow-y-auto custom-scrollbar">
        <div class="p-6 space-y-6">
          
          <div v-if="isLoading" class="flex flex-col items-center justify-center py-20">
            <div class="w-10 h-10 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
            <p class="mt-4 text-sm font-semibold text-slate-500 animate-pulse">Memuat data dashboard...</p>
          </div>
          
          <template v-else-if="dashboardData">
            <!-- Welcome Widget -->
            <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-2xl p-5 shadow-sm text-white relative overflow-hidden">
              <div class="relative z-10">
                <h2 class="text-lg font-extrabold mb-1">Halo, {{ dashboardData.siswa.nama.split(' ')[0] }} 👋</h2>
                <p class="text-indigo-100 text-xs leading-relaxed">
                  Tahun Ajaran <span class="font-bold text-white bg-indigo-900/50 px-1.5 py-0.5 rounded">{{ dashboardData.tahun_ajaran.nama }}</span>
                </p>
              </div>
              <div class="absolute right-0 bottom-0 opacity-10">
                <svg class="w-24 h-24 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0z"></path></svg>
              </div>
            </div>

            <!-- Identitas Akademik -->
            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-indigo-100 opacity-50"></div>
                
                <div class="flex items-center gap-4 mb-5 relative z-10">
                    <div class="w-16 h-16 bg-white rounded-full border-4 border-white shadow-sm flex items-center justify-center overflow-hidden">
                        <img v-if="dashboardData.siswa.foto" :src="dashboardData.siswa.foto" class="w-full h-full object-cover" alt="Foto Siswa" />
                        <span v-else class="text-2xl">🧑‍🎓</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800">{{ dashboardData.siswa.nama }}</h3>
                        <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest mt-0.5">{{ dashboardData.kelas.nama_kelas }}</p>
                    </div>
                </div>

                <div class="space-y-2 relative z-10">
                    <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
                        <span class="text-[9px] text-slate-400 uppercase tracking-widest font-black">NIS / NISN</span>
                        <span class="font-bold text-slate-700 text-xs">{{ dashboardData.siswa.nis }} / {{ dashboardData.siswa.nisn }}</span>
                    </div>
                    <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
                        <span class="text-[9px] text-slate-400 uppercase tracking-widest font-black">Wali Kelas</span>
                        <span class="font-bold text-slate-700 text-xs">{{ dashboardData.kelas.walas }}</span>
                    </div>
                    <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
                        <span class="text-[9px] text-slate-400 uppercase tracking-widest font-black">Periode</span>
                        <span class="font-bold text-indigo-600 text-xs bg-indigo-50 px-2 py-0.5 rounded">{{ dashboardData.periode.nama }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="bg-slate-900 p-5 rounded-2xl shadow-sm text-white relative overflow-hidden">
               <div class="relative z-10">
                  <h3 class="text-xs font-bold uppercase tracking-widest mb-3 text-slate-300 flex items-center">⚡ Akses Cepat</h3>
                  <div class="space-y-2">
                      <NuxtLink to="/siswa/biodata" class="block bg-slate-800 hover:bg-indigo-600 transition-colors p-3 rounded-xl text-xs font-semibold flex justify-between items-center group">
                          Perbarui Biodata
                          <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                      </NuxtLink>
                      <NuxtLink to="/siswa/rapor" class="block bg-slate-800 hover:bg-indigo-600 transition-colors p-3 rounded-xl text-xs font-semibold flex justify-between items-center group">
                          Lihat Rapor
                          <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                      </NuxtLink>
                  </div>
               </div>
            </div>
          </template>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col xl:h-full min-w-0 relative">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          
            <!-- Superadmin Empty State -->
            <div v-if="isSuperadminWithoutImpersonation" class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-xl shadow-sm border border-slate-200 h-full">
              <div class="text-amber-500 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
              </div>
              <h3 class="text-xl font-black text-slate-800">Menunggu Pilihan Siswa</h3>
              <p class="text-sm font-semibold text-slate-500 mt-2 max-w-md">Anda masuk sebagai Superadmin. Silakan pilih kelas dan nama siswa dari menu di <strong class="text-amber-600">pojok kanan atas</strong> untuk melihat Dashboard Siswa ini.</p>
            </div>

            <!-- Error State -->
            <div v-else-if="errorMessage" class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-xl shadow-sm border border-slate-200 h-full">
              <div class="text-red-500 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <h3 class="text-lg font-bold text-slate-800">Gagal Memuat Data</h3>
              <p class="text-sm text-slate-500 mt-1 max-w-md">{{ errorMessage }}</p>
              <button @click="loadDashboard" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold shadow-sm hover:bg-indigo-700">Coba Lagi</button>
            </div>

            <!-- Content Grid -->
            <div v-else-if="dashboardData" class="grid grid-cols-1 lg:grid-cols-2 gap-6 h-full pb-10 xl:pb-0">
                
                <!-- Grafik Kemajuan -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 flex flex-col h-full min-h-[400px]">
                  <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                      <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                      </div>
                      <div>
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Kemajuan Akademis</h3>
                        <p class="text-[10px] font-bold text-slate-400 mt-0.5 uppercase tracking-widest">Rata-rata Nilai Rapor</p>
                      </div>
                    </div>
                  </div>

                  <div class="flex-1 relative w-full h-full flex flex-col items-center justify-center">
                    <Line v-if="chartData.labels.length > 0" :data="chartData" :options="chartOptions" class="w-full h-full max-h-[300px]" />
                    <div v-else class="absolute inset-0 flex flex-col items-center justify-center bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                      <span class="text-3xl mb-2">📊</span>
                      <p class="text-sm font-bold text-slate-500">Belum ada data rapor</p>
                      <p class="text-xs text-slate-400 mt-1">Grafik akan muncul setelah ada nilai final.</p>
                    </div>
                  </div>
                </div>

                <!-- Rekap Kedisiplinan -->
                <div class="space-y-6 flex flex-col h-full">
                    <!-- Absensi -->
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                        <div class="flex items-center gap-3 mb-6">
                          <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                          </div>
                          <div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Rekap Absensi</h3>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">TA {{ dashboardData.tahun_ajaran.nama }}</p>
                          </div>
                        </div>

                        <div class="space-y-4">
                          <!-- Semester Ganjil -->
                          <div>
                            <div class="flex items-center justify-between mb-2">
                              <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">SMT Ganjil</span>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                              <div class="bg-slate-50 rounded-lg py-2 px-1 text-center border border-slate-100 shadow-sm">
                                <p class="text-lg font-black text-slate-700 leading-none">{{ dashboardData.rekap.absensi_ganjil.s }}</p>
                                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mt-1">Sakit</p>
                              </div>
                              <div class="bg-slate-50 rounded-lg py-2 px-1 text-center border border-slate-100 shadow-sm">
                                <p class="text-lg font-black text-slate-700 leading-none">{{ dashboardData.rekap.absensi_ganjil.i }}</p>
                                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mt-1">Izin</p>
                              </div>
                              <div class="bg-rose-50 rounded-lg py-2 px-1 text-center border border-rose-100 shadow-sm">
                                <p class="text-lg font-black text-rose-600 leading-none">{{ dashboardData.rekap.absensi_ganjil.a }}</p>
                                <p class="text-[9px] font-bold text-rose-500 uppercase tracking-wider mt-1">Alpha</p>
                              </div>
                            </div>
                          </div>
                          <!-- Semester Genap -->
                          <div class="pt-4 border-t border-slate-100">
                            <div class="flex items-center justify-between mb-2">
                              <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">SMT Genap</span>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                              <div class="bg-slate-50 rounded-lg py-2 px-1 text-center border border-slate-100 shadow-sm">
                                <p class="text-lg font-black text-slate-700 leading-none">{{ dashboardData.rekap.absensi_genap.s }}</p>
                                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mt-1">Sakit</p>
                              </div>
                              <div class="bg-slate-50 rounded-lg py-2 px-1 text-center border border-slate-100 shadow-sm">
                                <p class="text-lg font-black text-slate-700 leading-none">{{ dashboardData.rekap.absensi_genap.i }}</p>
                                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider mt-1">Izin</p>
                              </div>
                              <div class="bg-rose-50 rounded-lg py-2 px-1 text-center border border-rose-100 shadow-sm">
                                <p class="text-lg font-black text-rose-600 leading-none">{{ dashboardData.rekap.absensi_genap.a }}</p>
                                <p class="text-[9px] font-bold text-rose-500 uppercase tracking-wider mt-1">Alpha</p>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>

                    <!-- Poin BK -->
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 flex-1 flex flex-col justify-center">
                        <div class="flex items-center gap-3 mb-4">
                          <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                          </div>
                          <div>
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Total Poin</h3>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Catatan Kedisiplinan</p>
                          </div>
                        </div>
                        
                        <div class="flex items-end justify-between px-2">
                          <div class="text-4xl font-black text-slate-800">{{ dashboardData.rekap.total_poin }} <span class="text-sm text-slate-500 font-bold ml-1">poin</span></div>
                          <div v-if="dashboardData.rekap.total_poin > 50" class="text-xs font-bold text-red-600 px-3 py-1.5 bg-red-100 rounded-md uppercase tracking-widest">Perhatian</div>
                          <div v-else-if="dashboardData.rekap.total_poin > 0" class="text-xs font-bold text-orange-600 px-3 py-1.5 bg-orange-100 rounded-md uppercase tracking-widest">Peringatan</div>
                          <div v-else class="text-[10px] font-black text-indigo-600 px-3 py-1.5 bg-indigo-100 rounded-md uppercase tracking-widest">Bersih</div>
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
import { ref, onMounted, computed } from 'vue'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js'
import { Line } from 'vue-chartjs'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
)

definePageMeta({
  layout: 'siswa',
  middleware: 'siswa',
  title: 'Dashboard Siswa'
})

const tokenCookie = useCookie('auth_token')
const userProfile = useCookie('user_profile')

const { data: response, pending: isLoading, error, execute: loadDashboard } = await useFetch(import.meta.env.VITE_API_BASE_URL + '/api/siswa/dashboard', {
  headers: {
    'Authorization': `Bearer ${tokenCookie.value}`
  }
})

const dashboardData = computed(() => response.value?.data || null)
const errorMessage = computed(() => error.value?.message || (!response.value?.success && response.value?.message ? response.value?.message : ''))

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

const chartData = computed(() => {
  if (!dashboardData.value || !dashboardData.value.rekap.grafik_akademis) return { labels: [], datasets: [] };
  
  const grafik = dashboardData.value.rekap.grafik_akademis;
  return {
    labels: grafik.map(item => item.label),
    datasets: [
      {
        label: 'Rata-rata Nilai Rapor',
        backgroundColor: '#cbd5e1', // default fallback
        borderColor: '#cbd5e1', // default fallback
        pointBorderColor: '#ffffff',
        pointHoverBorderColor: '#ffffff',
        pointRadius: 5,
        pointHoverRadius: 7,
        pointBorderWidth: 2,
        borderWidth: 3,
        data: grafik.map(item => item.rata_rata),
        tension: 0.3,
        segment: {
            borderColor: (ctx) => {
                if (!ctx.p1) return '#cbd5e1';
                const val = ctx.p1.parsed.y;
                if (val >= 80) return '#10b981'; // Emerald/Green
                if (val >= 70) return '#eab308'; // Yellow
                return '#ef4444'; // Red
            }
        },
        pointBackgroundColor: (ctx) => {
            const val = ctx.raw;
            if (val === undefined || val === null) return '#cbd5e1';
            if (val >= 80) return '#10b981';
            if (val >= 70) return '#eab308';
            return '#ef4444';
        }
      }
    ]
  }
})

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    },
    tooltip: {
      backgroundColor: 'rgba(15, 23, 42, 0.9)', // Slate 900
      titleFont: { size: 13, family: "'Inter', sans-serif" },
      bodyFont: { size: 14, family: "'Inter', sans-serif", weight: 'bold' },
      padding: 12,
      cornerRadius: 8,
      displayColors: false,
      callbacks: {
        label: function(context) {
          return 'Nilai: ' + context.parsed.y;
        }
      }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      max: 100,
      grid: {
        color: '#f1f5f9', // Slate 100
        drawBorder: false,
      },
      ticks: {
        font: { family: "'Inter', sans-serif", weight: 'bold', size: 11 },
        color: '#94a3b8' // Slate 400
      }
    },
    x: {
      grid: {
        display: false,
        drawBorder: false,
      },
      ticks: {
        font: { family: "'Inter', sans-serif", weight: 'bold', size: 11 },
        color: '#64748b' // Slate 500
      }
    }
  },
  interaction: {
    intersect: false,
    mode: 'index',
  },
}


</script>

<style scoped>
/* Animasi fadeIn */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
    animation: fadeIn 0.4s ease-out forwards;
}
</style>
