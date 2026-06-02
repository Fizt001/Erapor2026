<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4">
    <!-- Header -->
    <div class="mb-8">
      <h2 class="text-2xl font-black text-slate-800 tracking-tight">Dashboard Akademik</h2>
      <p class="text-sm font-semibold text-slate-500 mt-1">Ringkasan data kurikulum dan peringatan tugas akademik.</p>
    </div>

    <div v-if="isLoading" class="flex items-center justify-center py-20">
      <div class="w-10 h-10 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
    </div>

    <div v-else class="space-y-6">
      
      <!-- Top Overview Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Total Mapel -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 flex items-center justify-between group hover:border-indigo-300 transition-colors">
          <div>
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Mapel</p>
            <h3 class="text-3xl font-black text-slate-800 mt-1">{{ stats.totalMapel }}</h3>
          </div>
          <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">📚</div>
        </div>

        <!-- Total JP -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 flex items-center justify-between group hover:border-emerald-300 transition-colors">
          <div>
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total JP Struktur</p>
            <h3 class="text-3xl font-black text-slate-800 mt-1">{{ stats.totalJP }}</h3>
          </div>
          <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">⏱️</div>
        </div>

        <!-- Rata-rata KKM -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 flex items-center justify-between group hover:border-amber-300 transition-colors">
          <div>
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Rata-rata KKM</p>
            <h3 class="text-3xl font-black text-slate-800 mt-1">{{ stats.avgKkm }}</h3>
          </div>
          <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">🎯</div>
        </div>

        <!-- Total Guru -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 flex items-center justify-between group hover:border-blue-300 transition-colors">
          <div>
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Guru</p>
            <h3 class="text-3xl font-black text-slate-800 mt-1">{{ stats.totalGuru }}</h3>
          </div>
          <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">👨‍🏫</div>
        </div>

      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Alerts & Action Needed -->
        <div class="lg:col-span-2 space-y-6">
          <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
              <h3 class="font-black text-slate-800">Checklist Persiapan Akademik</h3>
              <span class="text-[10px] px-3 py-1 bg-rose-50 text-rose-600 rounded-full font-bold uppercase tracking-widest border border-rose-200" v-if="stats.alerts.kelasTanpaWalas > 0 || stats.alerts.mapelTanpaGuru > 0">Ada Tugas Tertunda</span>
              <span class="text-[10px] px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full font-bold uppercase tracking-widest border border-emerald-200" v-else>Semua Selesai</span>
            </div>
            <div class="p-6 space-y-4">
              
              <!-- Walas Alert -->
              <div class="p-4 rounded-2xl border" :class="stats.alerts.kelasTanpaWalas > 0 ? 'bg-rose-50 border-rose-200' : 'bg-emerald-50 border-emerald-200'">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl bg-white shadow-sm" :class="stats.alerts.kelasTanpaWalas > 0 ? 'text-rose-500' : 'text-emerald-500'">
                    {{ stats.alerts.kelasTanpaWalas > 0 ? '⚠️' : '✅' }}
                  </div>
                  <div>
                    <h4 class="font-bold text-slate-800 text-sm">Penugasan Wali Kelas</h4>
                    <p class="text-xs text-slate-600 mt-1" v-if="stats.alerts.kelasTanpaWalas > 0">Ada <b>{{ stats.alerts.kelasTanpaWalas }} kelas</b> yang belum ditugaskan Wali Kelasnya.</p>
                    <p class="text-xs text-emerald-600 font-semibold mt-1" v-else>Semua kelas sudah memiliki Wali Kelas.</p>
                  </div>
                  <NuxtLink to="/kurikulum/wali-kelas" class="ml-auto px-4 py-2 bg-white text-xs font-bold rounded-lg border shadow-sm transition-colors" :class="stats.alerts.kelasTanpaWalas > 0 ? 'border-rose-200 text-rose-600 hover:bg-rose-100' : 'border-emerald-200 text-emerald-600 hover:bg-emerald-100'">
                    {{ stats.alerts.kelasTanpaWalas > 0 ? 'Selesaikan' : 'Lihat' }}
                  </NuxtLink>
                </div>
              </div>

              <!-- Guru Pengampu Alert -->
              <div class="p-4 rounded-2xl border" :class="stats.alerts.mapelTanpaGuru > 0 ? 'bg-amber-50 border-amber-200' : 'bg-emerald-50 border-emerald-200'">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl bg-white shadow-sm" :class="stats.alerts.mapelTanpaGuru > 0 ? 'text-amber-500' : 'text-emerald-500'">
                    {{ stats.alerts.mapelTanpaGuru > 0 ? '⚠️' : '✅' }}
                  </div>
                  <div>
                    <h4 class="font-bold text-slate-800 text-sm">Penugasan Guru Pengampu</h4>
                    <p class="text-xs text-slate-600 mt-1" v-if="stats.alerts.mapelTanpaGuru > 0">Ada <b>{{ stats.alerts.mapelTanpaGuru }} jam pelajaran / rombel</b> di struktur yang belum diisi guru pengampunya.</p>
                    <p class="text-xs text-emerald-600 font-semibold mt-1" v-else>Semua mata pelajaran di struktur sudah ada pengampunya.</p>
                  </div>
                  <NuxtLink to="/kurikulum/pengampu" class="ml-auto px-4 py-2 bg-white text-xs font-bold rounded-lg border shadow-sm transition-colors" :class="stats.alerts.mapelTanpaGuru > 0 ? 'border-amber-200 text-amber-600 hover:bg-amber-100' : 'border-emerald-200 text-emerald-600 hover:bg-emerald-100'">
                    {{ stats.alerts.mapelTanpaGuru > 0 ? 'Selesaikan' : 'Lihat' }}
                  </NuxtLink>
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- Info Card (Tahun Ajaran Aktif) -->
        <div class="bg-gradient-to-br from-indigo-900 to-slate-900 rounded-2xl shadow-xl border border-indigo-800 overflow-hidden text-white relative">
          <!-- Background decoration -->
          <div class="absolute -top-24 -right-24 w-48 h-48 bg-indigo-500/20 rounded-full blur-3xl"></div>
          
          <div class="p-6 relative z-10">
            <h3 class="text-sm font-black uppercase tracking-widest text-indigo-400 mb-6">Informasi Periode</h3>
            
            <div class="bg-indigo-950/50 rounded-2xl p-5 border border-indigo-800/50 mb-4">
              <p class="text-[10px] text-indigo-300 font-bold uppercase tracking-widest">Tahun Ajaran Aktif</p>
              <h2 class="text-2xl font-black mt-1" v-if="stats.taAktif">{{ stats.taAktif.tahun }}</h2>
              <h2 class="text-2xl font-black mt-1 text-rose-400" v-else>Belum Diset</h2>
            </div>
            
            <div class="bg-indigo-950/50 rounded-2xl p-5 border border-indigo-800/50">
              <p class="text-[10px] text-indigo-300 font-bold uppercase tracking-widest">Semester Aktif</p>
              <h2 class="text-xl font-black mt-1 uppercase" v-if="stats.taAktif">{{ stats.taAktif.semester == 1 ? 'Ganjil' : 'Genap' }}</h2>
              <h2 class="text-xl font-black mt-1 text-rose-400 uppercase" v-else>Belum Diset</h2>
            </div>

            <div class="mt-6 flex gap-2">
              <div class="flex-1 bg-white/10 rounded-xl p-3 text-center border border-white/5">
                <p class="text-[9px] uppercase tracking-widest text-indigo-200 mb-1 font-bold">Mapel Umum</p>
                <p class="text-lg font-black">{{ stats.distribusiMapel.umum }}</p>
              </div>
              <div class="flex-1 bg-white/10 rounded-xl p-3 text-center border border-white/5">
                <p class="text-[9px] uppercase tracking-widest text-indigo-200 mb-1 font-bold">Mapel Kejuruan</p>
                <p class="text-lg font-black">{{ stats.distribusiMapel.kejuruan }}</p>
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
  layout: 'kurikulum',
  middleware: 'kurikulum', // Ganti ke kurikulum middleware
  title: 'Dashboard Waka Kurikulum'
})

const isLoading = ref(true)
const stats = ref({
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
})

const fetchDashboard = async () => {
  isLoading.value = true
  const token = useCookie('auth_token').value
  try {
    const response = await $fetch('http://localhost:8000/api/kurikulum/dashboard', {
      headers: { Authorization: `Bearer ${token}` }
    })
    if (response.success) {
      stats.value = response.data
    }
  } catch (error) {
    console.error('Failed to fetch dashboard data', error)
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
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn { animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
