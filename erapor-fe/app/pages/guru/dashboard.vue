<template>
  <div class="animate-fadeIn max-w-7xl mx-auto space-y-6">
    <!-- Header Welcome -->
    <div class="bg-gradient-to-r from-sky-600 to-indigo-700 rounded-3xl p-8 sm:p-10 text-white shadow-lg relative overflow-hidden">
      <!-- Dekorasi Lingkaran -->
      <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/4"></div>
      
      <div class="relative z-10">
        <h1 class="text-3xl sm:text-4xl font-black tracking-tight mb-2">Selamat Datang, {{ dashboardData?.user?.name || 'Bapak/Ibu Guru' }}!</h1>
        <p class="text-sky-100 text-lg font-medium mb-6">
          Tahun Ajaran <span class="font-bold text-white">{{ dashboardData?.akademik?.tahun_ajaran }}</span> — 
          <span class="font-bold text-white">{{ dashboardData?.akademik?.periode }}</span>
        </p>
        
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-sm font-bold shadow-sm">
          <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
          Peran: Guru Mata Pelajaran <span v-if="dashboardData?.is_walas" class="text-emerald-300 ml-1">& Wali Kelas</span>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-10 w-10 border-4 border-slate-200 border-t-sky-600"></div>
    </div>

    <!-- Konten Dashboard -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      
      <!-- STATISTIK (Minimalis) -->
      <div class="lg:col-span-12 grid grid-cols-1 sm:grid-cols-3 gap-6">
        <!-- Card 1 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 flex items-center gap-5 hover:shadow-md transition-shadow">
          <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
          </div>
          <div>
            <div class="text-3xl font-black text-slate-800">{{ dashboardData?.stats?.total_kelas || 0 }}</div>
            <div class="text-sm font-bold text-slate-500 uppercase tracking-wider mt-1">Kelas Diajar</div>
          </div>
        </div>
        
        <!-- Card 2 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 flex items-center gap-5 hover:shadow-md transition-shadow">
          <div class="w-14 h-14 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
          </div>
          <div>
            <div class="text-3xl font-black text-slate-800">{{ dashboardData?.stats?.total_mapel || 0 }}</div>
            <div class="text-sm font-bold text-slate-500 uppercase tracking-wider mt-1">Mata Pelajaran</div>
          </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 flex items-center gap-5 hover:shadow-md transition-shadow">
          <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
          </div>
          <div>
            <div class="text-3xl font-black text-slate-800">{{ dashboardData?.stats?.total_siswa || 0 }}</div>
            <div class="text-sm font-bold text-slate-500 uppercase tracking-wider mt-1">Total Siswa</div>
          </div>
        </div>
      </div>

      <!-- AKSI CEPAT -->
      <div class="lg:col-span-12">
        <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4 ml-1">Akses Cepat</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <!-- Tombol Formatif -->
          <NuxtLink to="/guru/formatif/nilai" class="group bg-white rounded-xl p-5 border border-slate-200/60 shadow-sm hover:border-sky-300 hover:shadow-md hover:shadow-sky-100 transition-all cursor-pointer block">
            <div class="flex items-center justify-between mb-3">
              <div class="w-10 h-10 rounded-full bg-sky-50 flex items-center justify-center text-sky-600 group-hover:bg-sky-500 group-hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
              </div>
              <svg class="w-5 h-5 text-slate-300 group-hover:text-sky-500 transition-colors transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
            <div class="font-bold text-slate-800 mb-1">Input Nilai Formatif</div>
            <div class="text-xs font-medium text-slate-500">Isi capaian harian tiap TP</div>
          </NuxtLink>

          <!-- Tombol Sumatif -->
          <NuxtLink to="/guru/sumatif/nilai" class="group bg-white rounded-xl p-5 border border-slate-200/60 shadow-sm hover:border-indigo-300 hover:shadow-md hover:shadow-indigo-100 transition-all cursor-pointer block">
            <div class="flex items-center justify-between mb-3">
              <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 group-hover:bg-indigo-500 group-hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
              </div>
              <svg class="w-5 h-5 text-slate-300 group-hover:text-indigo-500 transition-colors transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
            <div class="font-bold text-slate-800 mb-1">Input Nilai Sumatif</div>
            <div class="text-xs font-medium text-slate-500">Isi nilai akhir (STS / SAS)</div>
          </NuxtLink>

          <!-- Tombol Rekapitulasi -->
          <NuxtLink to="/guru/sumatif/rekap" class="group bg-white rounded-xl p-5 border border-slate-200/60 shadow-sm hover:border-emerald-300 hover:shadow-md hover:shadow-emerald-100 transition-all cursor-pointer block relative overflow-hidden">
            <!-- Ribbon / Badge Highlight -->
            <div class="absolute top-0 right-0 bg-emerald-500 text-white text-[10px] font-black px-3 py-1 rounded-bl-lg">REKOMENDASI</div>
            
            <div class="flex items-center justify-between mb-3">
              <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
              </div>
              <svg class="w-5 h-5 text-slate-300 group-hover:text-emerald-500 transition-colors transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </div>
            <div class="font-bold text-slate-800 mb-1">Rekap Nilai Semua Mapel</div>
            <div class="text-xs font-medium text-slate-500">Cetak & pantau nilai akhir</div>
          </NuxtLink>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

definePageMeta({ layout: "guru", middleware: "guru", title: 'Dashboard Guru' })

const dashboardData = ref(null)
const isLoading = ref(true)
const tokenCookie = useCookie('auth_token')

const fetchDashboardData = async () => {
  try {
    const res = await $fetch('http://localhost:8000/api/guru/dashboard', {
      headers: {
        'Authorization': `Bearer ${tokenCookie.value}`,
        'Accept': 'application/json'
      }
    })
    
    if (res.success) {
      dashboardData.value = res.data
    }
  } catch (error) {
    console.error('Failed to fetch dashboard data:', error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchDashboardData()
})
</script>
