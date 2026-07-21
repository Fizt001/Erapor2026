<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex flex-col xl:flex-row overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="xl:w-[360px] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] overflow-y-auto custom-scrollbar">
        <div class="p-6 space-y-6">
          
          <!-- Welcome Widget -->
          <div class="bg-gradient-to-br from-rose-600 to-rose-800 rounded-2xl p-5 shadow-sm text-white relative overflow-hidden">
            <div class="relative z-10">
              <h2 class="text-lg font-extrabold mb-1">Halo, {{ userProfile?.name?.split(' ')[0] || 'Guru BK' }} 👋</h2>
              <p class="text-rose-100 text-xs leading-relaxed">
                Bimbingan Konseling <span class="font-bold text-white bg-rose-900/50 px-1.5 py-0.5 rounded">Aktif</span>
              </p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-10">
              <svg class="w-24 h-24 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-gradient-to-r from-rose-600 to-rose-700 p-5 rounded-2xl shadow-sm text-white relative overflow-hidden border border-rose-500">
             <div class="relative z-10">
                <h3 class="text-xs font-bold uppercase tracking-widest mb-3 text-rose-100 flex items-center">⚡ Akses Cepat</h3>
                <div class="space-y-2">
                    <NuxtLink to="/bk/buku-kasus" class="block bg-rose-900/40 border border-rose-500/50 hover:bg-rose-900/80 transition-colors p-3 rounded-xl text-xs font-semibold flex justify-between items-center group">
                        Buku Kasus Siswa
                        <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                    </NuxtLink>
                    <NuxtLink to="/bk/poin" class="block bg-rose-900/40 border border-rose-500/50 hover:bg-rose-900/80 transition-colors p-3 rounded-xl text-xs font-semibold flex justify-between items-center group">
                        Input Poin Siswa
                        <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                    </NuxtLink>
                    <NuxtLink to="/bk/penanganan" class="block bg-rose-900/40 border border-rose-500/50 hover:bg-rose-900/80 transition-colors p-3 rounded-xl text-xs font-semibold flex justify-between items-center group">
                        Penanganan Kasus
                        <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                    </NuxtLink>
                </div>
             </div>
             <div class="absolute -right-4 -bottom-4 text-rose-800 opacity-50 text-6xl font-black">
                +
             </div>
          </div>

        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            <div class="flex-1 overflow-y-auto custom-scrollbar p-6 lg:p-8 space-y-6">
        
        <!-- Superadmin Empty State -->
        <div v-if="isSuperadminWithoutImpersonation" class="flex-grow flex flex-col items-center justify-center py-20 text-center bg-white rounded-2xl shadow-sm border border-slate-200">
          <div class="text-amber-500 mb-6 bg-amber-50 p-5 rounded-full border border-amber-100 shadow-inner">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </div>
          <h3 class="text-2xl font-black text-slate-800 tracking-tight">Menunggu Pilihan Guru BK</h3>
          <p class="text-base text-slate-500 mt-3 max-w-md">Anda sedang dalam Mode Superadmin. Silakan pilih nama guru BK dari bilah menu di <strong class="text-amber-600 font-bold">pojok kanan atas</strong> untuk melihat Dashboard BK.</p>
        </div>

        <!-- Loading State -->
        <div v-else-if="isLoading" class="flex-grow flex items-center justify-center py-12">
          <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-rose-600"></div>
        </div>

        <div v-else class="space-y-6">
            
            <div class="mb-2">
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Dashboard Bimbingan Konseling</h2>
                <p class="text-sm font-semibold text-slate-500 mt-1">Pantau statistik kedisiplinan dan penanganan kasus siswa.</p>
            </div>

            <!-- Notifikasi Peringatan Sistem (SP1, SP2, SP3) -->
            <div v-if="stats.notifikasi?.length > 0" class="mb-2">
                <div class="bg-gradient-to-r from-rose-50 to-orange-50 p-6 rounded-2xl shadow-sm border border-rose-200/60">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-rose-600 text-xl border border-rose-100 shadow-sm animate-pulse">⚠️</div>
                            <div>
                                <h3 class="text-sm font-bold text-rose-800">Peringatan Sistem (Eskalasi Kasus)</h3>
                                <p class="text-[10px] font-medium text-rose-600 uppercase tracking-widest">Siswa dengan kasus yang butuh penanganan BK</p>
                            </div>
                        </div>
                        <NuxtLink to="/bk/buku-kasus" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-colors shadow-sm shadow-rose-500/30 shrink-0 text-center">
                            Lihat Buku Kasus
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

            <!-- STATISTIK UTAMA (3 COLUMNS) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Card 1 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-rose-200 group">
                    <div class="h-12 w-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 text-2xl border border-rose-100 group-hover:scale-110 transition-transform">🚨</div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Kasus Hari Ini</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ stats.kasus_hari_ini || 0 }}</p>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-amber-200 group">
                    <div class="h-12 w-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 text-2xl border border-amber-100 group-hover:scale-110 transition-transform">👨‍🎓</div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Siswa Bermasalah</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ stats.siswa_bermasalah || 0 }}</p>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-blue-200 group">
                    <div class="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 text-2xl border border-blue-100 group-hover:scale-110 transition-transform">⚖️</div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total Penanganan</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ stats.total_penanganan || 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Top Pelanggaran -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col">
                <div class="p-6 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-black uppercase tracking-widest text-slate-700 flex items-center gap-2">
                        <span class="text-lg">📈</span> Top 5 Pelanggaran Bulan Ini
                    </h3>
                </div>
                <div class="p-6">
                    <div v-if="stats.top_pelanggaran && stats.top_pelanggaran.length > 0" class="space-y-4">
                        <div v-for="(item, index) in stats.top_pelanggaran" :key="index" class="flex items-center gap-4 p-3 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                            <div class="w-8 h-8 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center font-black text-xs border border-rose-100 shrink-0">{{ index + 1 }}</div>
                            <div class="flex-grow">
                                <p class="text-sm font-bold text-slate-800">{{ item.pelanggaran?.nama_pelanggaran }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-xl font-black text-rose-600">{{ item.total }} <span class="text-[10px] text-slate-400 uppercase tracking-widest">Kasus</span></p>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-12 bg-slate-50 rounded-xl border border-slate-100 border-dashed">
                        <span class="text-5xl opacity-20 mb-3 block">✨</span>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Belum ada pelanggaran bulan ini. Bagus!</p>
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
  layout: 'bk',
  middleware: 'bk',
  title: 'Dashboard BK'
})

const userCookie = useCookie('user_profile')
const tokenCookie = useCookie('auth_token')

const userProfile = computed(() => {
  if (!userCookie.value) return null
  if (typeof userCookie.value === 'string') {
      try {
          return JSON.parse(userCookie.value)
      } catch (e) {
          return null
      }
  }
  return userCookie.value
})

const { data: response, pending: isLoading, error } = await useFetch(import.meta.env.VITE_API_BASE_URL + '/api/bk/dashboard', {
  headers: { Authorization: `Bearer ${tokenCookie.value}` }
})

const errorMessage = computed(() => error.value?.message || (!response.value?.success && response.value?.message ? response.value?.message : ''))

const isSuperadminWithoutImpersonation = computed(() => {
  const role = userProfile.value?.role
  return role === 'superadmin' && !!errorMessage.value
})

const stats = computed(() => response.value?.data || {})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
