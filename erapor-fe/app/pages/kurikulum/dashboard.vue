<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex flex-col xl:flex-row overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="xl:w-[360px] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] overflow-y-auto custom-scrollbar">
        <div class="p-6 space-y-6">
          
          <!-- Welcome Widget -->
          <div class="p-6 shrink-0 z-10 relative -mx-6 -mt-6 mb-6">
            <div class="bg-gradient-to-r from-indigo-600 to-blue-700 rounded-2xl p-5 border border-indigo-500 shadow-sm relative overflow-hidden flex flex-col gap-2">
              <div class="relative z-10">
                <h2 class="text-lg font-extrabold mb-1 text-white">Halo, {{ userProfile?.name?.split(' ')[0] || 'Waka Kurikulum' }} 👋</h2>
                <p class="text-indigo-100 text-xs leading-relaxed">
                  Manajemen <span class="font-bold text-white bg-indigo-900/50 px-1.5 py-0.5 rounded">Kurikulum</span>
                </p>
              </div>
              <div class="absolute right-0 bottom-0 opacity-20 text-white">
                <svg class="w-24 h-24 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path></svg>
              </div>
            </div>
          </div>

          <!-- Status Akademik Widget -->
          <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-widest mb-4 flex items-center"><span class="mr-2">📌</span> Periode Aktif</h3>
            <div class="space-y-3">
              <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[9px] text-slate-400 uppercase tracking-widest font-black mb-1">Tahun Ajaran</p>
                    <p class="font-bold text-slate-800 text-sm leading-tight">{{ stats.taAktif?.tahun || 'Belum Diset' }}</p>
                </div>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold border border-indigo-100">📅</div>
              </div>
              <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[9px] text-slate-400 uppercase tracking-widest font-black mb-1">Semester</p>
                    <p class="font-bold text-slate-800 text-sm leading-tight uppercase">{{ stats.taAktif ? (stats.taAktif.semester == 1 ? 'Ganjil' : 'Genap') : 'Belum Diset' }}</p>
                </div>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold border border-emerald-100">🎓</div>
              </div>
            </div>
          </div>
          
          <!-- Quick Actions -->
          <div class="bg-gradient-to-r from-indigo-600 to-blue-700 p-5 rounded-2xl shadow-sm text-white relative overflow-hidden border border-indigo-500">
             <div class="relative z-10">
                <h3 class="text-xs font-bold uppercase tracking-widest mb-3 text-indigo-100 flex items-center">⚡ Akses Cepat</h3>
                <div class="space-y-2">
                    <NuxtLink to="/kurikulum/mapel" class="block bg-indigo-900/40 border border-indigo-500/50 hover:bg-indigo-900/80 transition-colors p-3 rounded-xl text-xs font-semibold flex justify-between items-center group">
                        Master Mata Pelajaran
                        <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                    </NuxtLink>
                    <NuxtLink to="/kurikulum/pengampu" class="block bg-indigo-900/40 border border-indigo-500/50 hover:bg-indigo-900/80 transition-colors p-3 rounded-xl text-xs font-semibold flex justify-between items-center group">
                        Plot Guru Pengampu
                        <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                    </NuxtLink>
                    <NuxtLink to="/kurikulum/wali-kelas" class="block bg-indigo-900/40 border border-indigo-500/50 hover:bg-indigo-900/80 transition-colors p-3 rounded-xl text-xs font-semibold flex justify-between items-center group">
                        Penugasan Wali Kelas
                        <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                    </NuxtLink>
                </div>
             </div>
             <div class="absolute -right-4 -bottom-4 text-indigo-800 opacity-50 text-6xl font-black">
                +
             </div>
          </div>

        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white/80 backdrop-blur-xl z-20">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-400 to-indigo-600 shadow-sm flex items-center justify-center text-2xl text-white">📊</div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-indigo-700">Dashboard Akademik</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Ringkasan data kurikulum dan peringatan tugas akademik.</p>
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar relative z-0">
                
                <!-- Loading State -->
                <div v-if="isLoading" class="flex-grow flex items-center justify-center py-12">
                  <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
                </div>

                <div v-else class="p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">

            <!-- Notifikasi Peringatan Sistem (SP2 & SP3) -->
            <div v-if="stats.notifikasi?.length > 0" class="mb-6">
                <div class="bg-gradient-to-r from-rose-50 to-orange-50 p-6 rounded-2xl shadow-sm border border-rose-200/60">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-rose-600 text-xl border border-rose-100 shadow-sm animate-pulse">⚠️</div>
                            <div>
                                <h3 class="text-sm font-bold text-rose-800">Peringatan Sistem (Eskalasi)</h3>
                                <p class="text-[10px] font-medium text-rose-600 uppercase tracking-widest">Siswa dengan kasus SP2 dan SP3</p>
                            </div>
                        </div>
                        <NuxtLink to="/kurikulum/penanganan" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-colors shadow-sm shadow-rose-500/30 shrink-0 text-center">
                            Lihat Penanganan Kasus
                        </NuxtLink>
                    </div>
                    <div class="space-y-3">
                        <div v-for="notif in stats.notifikasi" :key="notif.id" class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl bg-white border border-rose-100 shadow-sm gap-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="text-xs font-black text-slate-800">{{ notif.siswa }}</p>
                                    <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[9px] font-black uppercase tracking-widest rounded shadow-sm">{{ notif.kategori }}</span>
                                </div>
                                <p class="text-[11px] font-medium text-slate-600">{{ notif.deskripsi }}</p>
                            </div>
                            <div class="text-left sm:text-right shrink-0">
                                <p class="text-[9px] font-black uppercase tracking-wider text-slate-400">Kelas: <span class="text-sky-600">{{ notif.kelas }}</span></p>
                                <p class="text-[9px] font-bold text-slate-400">{{ notif.waktu }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STATISTIK UTAMA (4 COLUMNS) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Card 1 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-indigo-200 group">
                    <div class="h-12 w-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-2xl border border-indigo-100 group-hover:scale-110 transition-transform shrink-0">📚</div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total Mapel</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ stats.totalMapel }}</p>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-emerald-200 group">
                    <div class="h-12 w-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-2xl border border-emerald-100 group-hover:scale-110 transition-transform shrink-0">⏱️</div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total JP Struktur</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ stats.totalJP }}</p>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-amber-200 group">
                    <div class="h-12 w-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 text-2xl border border-amber-100 group-hover:scale-110 transition-transform shrink-0">🎯</div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Rata-rata KKM</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ stats.avgKkm }}</p>
                    </div>
                </div>
                <!-- Card 4 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-blue-200 group">
                    <div class="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 text-2xl border border-blue-100 group-hover:scale-110 transition-transform shrink-0">👨‍🏫</div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total Guru</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ stats.totalGuru }}</p>
                    </div>
                </div>
            </div>

            <!-- MAIN GRID -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <!-- Alerts & Action Needed -->
                <div class="xl:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
                        <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                            <h3 class="font-black text-slate-800 flex items-center gap-2"><span class="text-lg">📋</span> Checklist Persiapan Akademik</h3>
                            <span class="text-[10px] px-3 py-1 bg-rose-50 text-rose-600 rounded-full font-bold uppercase tracking-widest border border-rose-200" v-if="stats.alerts?.kelasTanpaWalas > 0 || stats.alerts?.mapelTanpaGuru > 0">Ada Tugas Tertunda</span>
                            <span class="text-[10px] px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full font-bold uppercase tracking-widest border border-emerald-200" v-else>Semua Selesai</span>
                        </div>
                        <div class="p-6 space-y-4">
                            
                            <!-- Walas Alert -->
                            <div class="p-4 rounded-xl border flex flex-col sm:flex-row items-start sm:items-center gap-4 transition-colors" :class="stats.alerts?.kelasTanpaWalas > 0 ? 'bg-rose-50/50 border-rose-200 hover:bg-rose-50' : 'bg-emerald-50/50 border-emerald-200 hover:bg-emerald-50'">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl bg-white shadow-sm shrink-0 border" :class="stats.alerts?.kelasTanpaWalas > 0 ? 'text-rose-500 border-rose-100' : 'text-emerald-500 border-emerald-100'">
                                    {{ stats.alerts?.kelasTanpaWalas > 0 ? '⚠️' : '✅' }}
                                </div>
                                <div class="flex-grow">
                                    <h4 class="font-bold text-slate-800 text-sm">Penugasan Wali Kelas</h4>
                                    <p class="text-xs text-slate-600 mt-1" v-if="stats.alerts?.kelasTanpaWalas > 0">Ada <b class="text-rose-600">{{ stats.alerts?.kelasTanpaWalas }} kelas</b> yang belum ditugaskan Wali Kelasnya.</p>
                                    <p class="text-xs text-emerald-600 font-semibold mt-1" v-else>Semua kelas sudah memiliki Wali Kelas.</p>
                                </div>
                                <NuxtLink to="/kurikulum/wali-kelas" class="shrink-0 px-4 py-2 bg-white text-xs font-bold rounded-lg border shadow-sm transition-colors" :class="stats.alerts?.kelasTanpaWalas > 0 ? 'border-rose-200 text-rose-600 hover:bg-rose-100' : 'border-emerald-200 text-emerald-600 hover:bg-emerald-100'">
                                    {{ stats.alerts?.kelasTanpaWalas > 0 ? 'Selesaikan' : 'Lihat Detail' }}
                                </NuxtLink>
                            </div>

                            <!-- Guru Pengampu Alert -->
                            <div class="p-4 rounded-xl border flex flex-col sm:flex-row items-start sm:items-center gap-4 transition-colors" :class="stats.alerts?.mapelTanpaGuru > 0 ? 'bg-amber-50/50 border-amber-200 hover:bg-amber-50' : 'bg-emerald-50/50 border-emerald-200 hover:bg-emerald-50'">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl bg-white shadow-sm shrink-0 border" :class="stats.alerts?.mapelTanpaGuru > 0 ? 'text-amber-500 border-amber-100' : 'text-emerald-500 border-emerald-100'">
                                    {{ stats.alerts?.mapelTanpaGuru > 0 ? '⚠️' : '✅' }}
                                </div>
                                <div class="flex-grow">
                                    <h4 class="font-bold text-slate-800 text-sm">Penugasan Guru Pengampu</h4>
                                    <p class="text-xs text-slate-600 mt-1" v-if="stats.alerts?.mapelTanpaGuru > 0">Ada <b class="text-amber-600">{{ stats.alerts?.mapelTanpaGuru }} jam pelajaran</b> yang belum diisi guru pengampunya.</p>
                                    <p class="text-xs text-emerald-600 font-semibold mt-1" v-else>Semua mata pelajaran sudah ada pengampunya.</p>
                                </div>
                                <NuxtLink to="/kurikulum/pengampu" class="shrink-0 px-4 py-2 bg-white text-xs font-bold rounded-lg border shadow-sm transition-colors" :class="stats.alerts?.mapelTanpaGuru > 0 ? 'border-amber-200 text-amber-600 hover:bg-amber-100' : 'border-emerald-200 text-emerald-600 hover:bg-emerald-100'">
                                    {{ stats.alerts?.mapelTanpaGuru > 0 ? 'Selesaikan' : 'Lihat Detail' }}
                                </NuxtLink>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Distribusi Mapel -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 flex flex-col">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 text-xl border border-purple-100 shrink-0">📊</div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Distribusi Mapel</h3>
                            <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest">Kategori Pelajaran</p>
                        </div>
                    </div>
                    
                    <div class="flex-grow flex flex-col justify-center space-y-4">
                        <div class="p-4 rounded-xl border border-slate-100 bg-slate-50 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-black shrink-0">A</div>
                                <p class="text-xs font-bold text-slate-700">Mapel Umum</p>
                            </div>
                            <h4 class="text-2xl font-black text-slate-800">{{ stats.distribusiMapel?.umum || 0 }}</h4>
                        </div>
                        <div class="p-4 rounded-xl border border-slate-100 bg-slate-50 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-sm font-black shrink-0">B</div>
                                <p class="text-xs font-bold text-slate-700">Mapel Kejuruan</p>
                            </div>
                            <h4 class="text-2xl font-black text-slate-800">{{ stats.distribusiMapel?.kejuruan || 0 }}</h4>
                        </div>
                        <div class="p-4 rounded-xl border border-slate-100 bg-slate-50 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm font-black shrink-0">X</div>
                                <p class="text-xs font-bold text-slate-700">Ekstrakurikuler</p>
                            </div>
                            <h4 class="text-2xl font-black text-slate-800">{{ stats.totalEkskul || 0 }}</h4>
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

const { data: response, pending: isLoading } = await useFetch('http://localhost:8000/api/kurikulum/dashboard', {
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
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
