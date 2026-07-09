<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
<!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex flex-col xl:flex-row overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="xl:w-[360px] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] overflow-y-auto custom-scrollbar">
        <div class="p-6 space-y-6">
          
          <!-- Welcome Widget -->
          <div class="bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-2xl p-5 shadow-sm text-white relative overflow-hidden">
            <div class="relative z-10">
              <h2 class="text-lg font-extrabold mb-1">Halo, {{ userProfile?.name?.split(' ')[0] || 'Admin' }} 👋</h2>
              <p class="text-emerald-100 text-xs leading-relaxed">
                Tahun Ajaran <span class="font-bold text-white bg-emerald-900/50 px-1.5 py-0.5 rounded">{{ stats?.academic?.tahun_ajaran || '...' }}</span>
              </p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-10">
              <svg class="w-24 h-24 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
            </div>
          </div>

          <!-- Status Akademik Widget -->
          <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-widest mb-4 flex items-center"><span class="mr-2">📌</span> Status Institusi</h3>
            <div class="space-y-3">
              <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-[9px] text-slate-400 uppercase tracking-widest font-black mb-1">Identitas Sekolah</p>
                <p class="font-bold text-slate-800 text-sm leading-tight">{{ stats?.sekolah?.nama || 'Memuat...' }}</p>
                <p class="text-[10px] text-slate-500 mt-1 font-medium">NPSN: {{ stats?.sekolah?.npsn || '-' }}</p>
              </div>
            </div>
          </div>
          
          <!-- Quick Actions -->
          <div class="bg-slate-900 p-5 rounded-2xl shadow-sm text-white relative overflow-hidden">
             <div class="relative z-10">
                <h3 class="text-xs font-bold uppercase tracking-widest mb-3 text-slate-300 flex items-center">⚡ Akses Cepat</h3>
                <div class="space-y-2">
                    <NuxtLink to="/admin/sekolah" class="block bg-slate-800 hover:bg-emerald-600 transition-colors p-3 rounded-xl text-xs font-semibold flex justify-between items-center group">
                        Profil Sekolah
                        <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                    </NuxtLink>
                    <NuxtLink to="/admin/users" class="block bg-slate-800 hover:bg-emerald-600 transition-colors p-3 rounded-xl text-xs font-semibold flex justify-between items-center group">
                        Kelola Pengguna
                        <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                    </NuxtLink>
                </div>
             </div>
             <div class="absolute -right-4 -bottom-4 text-slate-800 opacity-50 text-6xl font-black">
                +
             </div>
          </div>

          <!-- Panduan Alur Kerja Admin -->
          <WorkflowGuide
            title="Alur Kerja Admin"
            icon="⚙️"
            color="emerald"
            :note="'Lakukan langkah ini setiap awal tahun ajaran baru sebelum guru mulai input nilai.'"
            :steps="adminWorkflow"
          />

        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0 overflow-y-auto custom-scrollbar">
        
        <!-- Loading State -->
        <div v-if="pending" class="flex-grow flex items-center justify-center py-12">
          <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-emerald-600"></div>
        </div>

        <div v-else class="p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">
            
            <!-- STATISTIK UTAMA (4 COLUMNS) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Card 1 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-emerald-200">
                    <div class="h-12 w-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-2xl border border-emerald-100">👥</div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total Siswa</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ stats?.users?.siswa || 0 }}</p>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-blue-200">
                    <div class="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 text-2xl border border-blue-100">👨‍🏫</div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Guru Aktif</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ stats?.users?.guru || 0 }}</p>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-purple-200">
                    <div class="h-12 w-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 text-2xl border border-purple-100">🏫</div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total Kelas</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ stats?.master?.kelas || 0 }}</p>
                    </div>
                </div>
                <!-- Card 4 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-rose-200">
                    <div class="h-12 w-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 text-2xl border border-rose-100">🏷️</div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Mata Pelajaran</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ stats?.master?.mapel || 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- MAIN GRID -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Chart User -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 flex flex-col">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-xl border border-indigo-100">🍩</div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Komposisi Pengguna</h3>
                            <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest">Berdasarkan Role</p>
                        </div>
                    </div>
                    <div class="h-64 flex-1 flex justify-center relative">
                        <ClientOnly>
                            <Doughnut :data="chartUserData" :options="chartUserOptions" />
                            <template #fallback>
                                <div class="flex items-center justify-center h-full text-slate-400 text-xs font-bold">Memuat Grafik...</div>
                            </template>
                        </ClientOnly>
                    </div>
                </div>

                <!-- Chart Master -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 flex flex-col">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 text-xl border border-amber-100">📊</div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Data Master Terisi</h3>
                            <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest">Kuantitas Database</p>
                        </div>
                    </div>
                    <div class="h-64 flex-1 flex justify-center">
                        <ClientOnly>
                            <Bar :data="chartMasterData" :options="chartMasterOptions" />
                            <template #fallback>
                                <div class="flex items-center justify-center h-full text-slate-400 text-xs font-bold">Memuat Grafik...</div>
                            </template>
                        </ClientOnly>
                    </div>
                </div>
            </div>
            
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Chart as ChartJS, ArcElement, Tooltip, Legend, CategoryScale, LinearScale, BarElement } from 'chart.js'
import { Doughnut, Bar } from 'vue-chartjs'

ChartJS.register(ArcElement, Tooltip, Legend, CategoryScale, LinearScale, BarElement)

definePageMeta({
  layout: 'admin',
  middleware: 'admin',
  title: 'Dashboard Utama'
})

const tokenCookie = useCookie('auth_token')
const userCookie = useCookie('user_profile')

const userProfile = computed(() => {
  if (!userCookie.value) return null
  return typeof userCookie.value === 'string' ? JSON.parse(userCookie.value) : userCookie.value
})

const { data: response, pending } = await useFetch('http://localhost:8000/api/admin/dashboard', {
  headers: {
    Authorization: `Bearer ${tokenCookie.value}`
  }
})

const stats = computed(() => response.value?.data || null)

// --- Panduan Alur Kerja Admin ---
const adminWorkflow = computed(() => {
    return [
        {
            label: 'Buat Tahun Ajaran Baru',
            desc: 'Tambahkan data tahun ajaran dan semester baru.',
            emoji: '🗓️',
            active: true,
            done: false,
            to: '/admin/tahun-ajaran'
        },
        {
            label: 'Buat Kelas & Rombel Baru',
            desc: 'Tambahkan kelas-kelas untuk tahun ajaran yang baru.',
            emoji: '🏫',
            active: true,
            done: false,
            to: '/admin/kelas'
        },
        {
            label: 'Proses Kenaikan Kelas',
            desc: 'Mutasikan siswa dari kelas lama ke kelas baru berdasar rekomendasi walas.',
            emoji: '🎓',
            active: true,
            done: false,
            to: '/admin/kenaikan-kelas'
        },
        {
            label: 'Input Siswa Baru (Kelas X)',
            desc: 'Daftarkan siswa baru ke kelas-kelas awal.',
            emoji: '📥',
            active: true,
            done: false,
            to: '/admin/buku-induk' // Atau kelola siswa, tapi buku induk biasanya pintu masuknya
        }
    ]
})

// --- Chart User (Doughnut) ---
const chartUserData = computed(() => ({
  labels: ['Admin', 'Guru', 'Siswa'],
  datasets: [
    {
      backgroundColor: ['#10b981', '#3b82f6', '#f43f5e'],
      borderWidth: 0,
      hoverOffset: 10,
      data: [
        stats.value?.users?.admin || 0,
        stats.value?.users?.guru || 0,
        stats.value?.users?.siswa || 0
      ]
    }
  ]
}))

const chartUserOptions = {
  responsive: true,
  maintainAspectRatio: false,
  cutout: '75%',
  plugins: {
    legend: { 
        position: 'bottom',
        labels: { font: { weight: 'bold', size: 11, family: "'Inter', sans-serif" } }
    }
  }
}

// --- Chart Master (Bar) ---
const chartMasterData = computed(() => ({
  labels: ['Mapel', 'Kelas', 'Ekskul'],
  datasets: [
    {
      label: 'Jumlah Terdaftar',
      backgroundColor: '#10b981',
      borderRadius: 8,
      borderSkipped: false,
      data: [
        stats.value?.master?.mapel || 0,
        stats.value?.master?.kelas || 0,
        stats.value?.master?.ekskul || 0
      ]
    }
  ]
}))

const chartMasterOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false }
  },
  scales: {
    y: { 
        beginAtZero: true, 
        grid: { borderDash: [4, 4], color: '#f1f5f9' },
        ticks: { font: { weight: 'bold', family: "'Inter', sans-serif" } }
    },
    x: { 
        grid: { display: false },
        ticks: { font: { weight: 'bold', family: "'Inter', sans-serif" } }
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
