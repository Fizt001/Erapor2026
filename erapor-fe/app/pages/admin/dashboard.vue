<template>
  <div class="space-y-5 animate-fadeIn">
    <!-- 1. BANNER SELAMAT DATANG -->
    <div class="bg-gradient-to-r from-emerald-600 to-emerald-800 rounded-2xl p-5 shadow-sm text-white relative overflow-hidden">
      <div class="relative z-10">
        <h1 class="text-xl sm:text-2xl font-extrabold mb-1">Selamat Datang, {{ userProfile?.name }} 👋</h1>
        <p class="text-emerald-100 max-w-xl text-sm sm:text-base leading-relaxed">
          Sistem e-Rapor aktif pada Tahun Ajaran <span class="font-bold text-white bg-emerald-900/50 px-2 py-0.5 rounded">{{ stats?.academic?.tahun_ajaran || '...' }}</span>. 
          Anda login sebagai Administrator.
        </p>
      </div>
      <div class="absolute right-0 bottom-0 opacity-10">
        <svg class="w-48 h-48 transform translate-x-8 translate-y-8" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="pending" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-emerald-600"></div>
    </div>

    <div v-else class="space-y-5">
      <!-- 2. STATISTIK UTAMA (4 COLUMNS) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1 -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4 hover:shadow-md transition-shadow">
          <div class="h-10 w-10 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 text-xl border border-emerald-100">
            👥
          </div>
          <div>
            <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Total Siswa</p>
            <p class="text-2xl font-black text-slate-800">{{ stats?.users?.siswa || 0 }}</p>
          </div>
        </div>
        <!-- Card 2 -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4 hover:shadow-md transition-shadow">
          <div class="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 text-xl border border-blue-100">
            👨‍🏫
          </div>
          <div>
            <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Guru Aktif</p>
            <p class="text-2xl font-black text-slate-800">{{ stats?.users?.guru || 0 }}</p>
          </div>
        </div>
        <!-- Card 3 -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4 hover:shadow-md transition-shadow">
          <div class="h-10 w-10 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600 text-xl border border-purple-100">
            🏫
          </div>
          <div>
            <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Total Kelas</p>
            <p class="text-2xl font-black text-slate-800">{{ stats?.master?.kelas || 0 }}</p>
          </div>
        </div>
        <!-- Card 4 -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex items-center space-x-4 hover:shadow-md transition-shadow">
          <div class="h-10 w-10 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600 text-xl border border-rose-100">
            🏷️
          </div>
          <div>
            <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wide">Mata Pelajaran</p>
            <p class="text-2xl font-black text-slate-800">{{ stats?.master?.mapel || 0 }}</p>
          </div>
        </div>
      </div>

      <!-- 3. MAIN GRID 75:25 -->
      <div class="grid grid-cols-1 xl:grid-cols-4 gap-5">
        <!-- Kiri 75% -->
        <div class="xl:col-span-3 space-y-5">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- Chart User -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
              <h3 class="text-base font-extrabold text-slate-800 mb-4 flex items-center"><span class="text-xl mr-2">🍩</span> Komposisi Pengguna</h3>
              <div class="h-56 flex justify-center relative">
                <ClientOnly>
                  <Doughnut :data="chartUserData" :options="chartUserOptions" />
                  <template #fallback>
                    <div class="flex items-center justify-center h-full text-slate-400">Loading chart...</div>
                  </template>
                </ClientOnly>
              </div>
            </div>
            <!-- Chart Master -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
              <h3 class="text-base font-extrabold text-slate-800 mb-4 flex items-center"><span class="text-xl mr-2">📊</span> Data Master Terisi</h3>
              <div class="h-56 flex justify-center">
                <ClientOnly>
                  <Bar :data="chartMasterData" :options="chartMasterOptions" />
                  <template #fallback>
                    <div class="flex items-center justify-center h-full text-slate-400">Loading chart...</div>
                  </template>
                </ClientOnly>
              </div>
            </div>
          </div>
        </div>

        <!-- Kanan 25% -->
        <div class="xl:col-span-1 space-y-5">
          <!-- Status Akademik Widget -->
          <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
            <h3 class="text-base font-extrabold text-slate-800 mb-4 flex items-center"><span class="text-xl mr-2">📌</span> Status Akademik</h3>
            <div class="space-y-3">
              <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold mb-0.5">Sekolah Identitas</p>
                <p class="font-black text-slate-800 text-base">{{ stats?.sekolah?.nama }}</p>
                <p class="text-xs text-slate-500 mt-0.5">NPSN: {{ stats?.sekolah?.npsn }}</p>
              </div>
              <div class="bg-emerald-50 p-3 rounded-lg border border-emerald-100">
                <p class="text-[10px] text-emerald-600 uppercase tracking-widest font-bold mb-0.5">Tahun Ajaran Aktif</p>
                <p class="font-black text-emerald-800 text-base">{{ stats?.academic?.tahun_ajaran }}</p>
              </div>
            </div>
          </div>
          
          <!-- Quick Actions -->
          <div class="bg-slate-900 p-5 rounded-2xl shadow-sm text-white relative overflow-hidden">
             <div class="relative z-10">
                <h3 class="text-base font-extrabold mb-3 text-white flex items-center">⚡ Akses Cepat</h3>
                <div class="space-y-2">
                    <NuxtLink to="/admin/sekolah" class="block bg-slate-800 hover:bg-emerald-600 transition-colors p-3 rounded-lg text-sm font-semibold flex justify-between items-center group">
                        Edit Data Sekolah
                        <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                    </NuxtLink>
                    <NuxtLink to="/admin/users" class="block bg-slate-800 hover:bg-emerald-600 transition-colors p-3 rounded-lg text-sm font-semibold flex justify-between items-center group">
                        Tambah User Baru
                        <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                    </NuxtLink>
                </div>
             </div>
             <div class="absolute -right-6 -bottom-6 text-slate-800 opacity-50 text-8xl font-black">
                +
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
        labels: { font: { weight: 'bold', size: 12 } }
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
      borderRadius: 12,
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
        ticks: { font: { weight: 'bold' } }
    },
    x: { 
        grid: { display: false },
        ticks: { font: { weight: 'bold' } }
    }
  }
}
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
  animation: fadeIn 0.5s ease-out forwards;
}
</style>
