<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex flex-col xl:flex-row xl:overflow-hidden overflow-y-auto relative">
      
      <!-- Panel Dock Kiri -->
      <div class="xl:w-[360px] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col xl:h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] overflow-y-auto custom-scrollbar">
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
              <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3 border-l-4 border-l-amber-500">
                <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-sm font-black">WK</div>
                <div>
                  <p class="font-bold text-slate-800 text-[11px] uppercase tracking-wider">Wali Kelas</p>
                  <p class="text-[9px] text-slate-500 mt-0.5">Akses kelola siswa & cetak rapor</p>
                </div>
              </div>

            </div>
          </div>

          <!-- Panduan Alur Kerja Walas -->
          <WorkflowGuide
            title="Alur Kerja Wali Kelas"
            icon="🧑‍🏫"
            color="amber"
            :note="'Lakukan pengisian secara berurutan pada setiap akhir semester (khususnya akhir tahun ajaran).'"
            :steps="walasWorkflow"
          />

        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col xl:h-full min-w-0 overflow-y-auto custom-scrollbar">
        
        <!-- Superadmin Empty State -->
        <div v-if="isSuperadminWithoutImpersonation" class="flex-grow flex flex-col items-center justify-center py-20 text-center bg-white rounded-2xl shadow-sm border border-slate-200 m-8">
          <div class="text-amber-500 mb-6 bg-amber-50 p-5 rounded-full border border-amber-100 shadow-inner">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </div>
          <h3 class="text-2xl font-black text-slate-800 tracking-tight">Menunggu Pilihan Wali Kelas</h3>
          <p class="text-base text-slate-500 mt-3 max-w-md">Anda sedang dalam Mode Superadmin. Silakan pilih nama wali kelas dari bilah menu di <strong class="text-amber-600 font-bold">pojok kanan atas</strong> untuk melihat Dashboard Wali Kelas.</p>
        </div>

        <!-- Loading State -->
        <div v-else-if="isLoading || statsLoading" class="flex-grow flex items-center justify-center py-12">
          <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-sky-600"></div>
        </div>

        <div v-else class="p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">
            
            <div v-if="statsLoading" class="flex justify-center py-6"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-sky-600"></div></div>

            <!-- STATISTIK KELAS (4 COLUMNS) -->
            <div v-if="!statsLoading && wStats" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-sky-200">
                    <div class="h-12 w-12 rounded-xl bg-sky-50 flex items-center justify-center text-sky-600 text-2xl border border-sky-100">👥</div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total Siswa</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ wStats.populasi?.total || 0 }}</p>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-blue-200">
                    <div class="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 text-2xl border border-blue-100">👦</div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Laki-laki</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ wStats.populasi?.laki || 0 }}</p>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-rose-200">
                    <div class="h-12 w-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 text-2xl border border-rose-100">👧</div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Perempuan</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ wStats.populasi?.perempuan || 0 }}</p>
                    </div>
                </div>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-emerald-200">
                    <div class="h-12 w-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-2xl border border-emerald-100">⭐</div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Rata-rata Kelas</p>
                        <p class="text-2xl font-black text-slate-800 leading-none">{{ wStats.rata_rata_kelas || 0 }}</p>
                    </div>
                </div>
            </div>

            <div v-if="!statsLoading && wStats" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Chart Gender -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 flex flex-col">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-xl border border-indigo-100">🍩</div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Komposisi Gender</h3>
                            <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest">Siswa di Kelas</p>
                        </div>
                    </div>
                    <div class="h-48 flex-1 flex justify-center relative">
                        <ClientOnly>
                            <Doughnut :data="chartGenderData" :options="chartGenderOptions" />
                            <template #fallback>
                                <div class="flex items-center justify-center h-full text-slate-400 text-xs font-bold">Memuat...</div>
                            </template>
                        </ClientOnly>
                    </div>
                </div>

                <!-- Top 10 Besar -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 flex flex-col col-span-1 lg:col-span-2 overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-xl border border-emerald-100">🏆</div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Top 10 Siswa Berprestasi</h3>
                            <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest">Berdasarkan Rata-rata Nilai Tertinggi</p>
                        </div>
                    </div>
                    <div class="flex-1 overflow-auto custom-scrollbar p-0">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50/70 text-[10px] font-black text-slate-400 uppercase tracking-widest sticky top-0 border-b border-slate-200">
                                <tr>
                                    <th class="py-2.5 px-4 w-12 text-center">Rnk</th>
                                    <th class="py-2.5 px-4">Nama Siswa</th>
                                    <th class="py-2.5 px-4 text-center">Rata-rata</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-sm">
                                <tr v-if="wStats.top_10?.length === 0">
                                    <td colspan="3" class="text-center py-6 text-slate-400 text-xs font-bold">Belum ada data nilai</td>
                                </tr>
                                <tr v-for="(siswa, index) in wStats.top_10" :key="siswa.id" class="hover:bg-slate-50/80 transition-colors group">
                                    <td class="py-2.5 px-4 text-center font-black text-slate-400 group-hover:text-emerald-500">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="py-2.5 px-4">
                                        <p class="font-bold text-slate-800 text-[13px]">{{ siswa.nama }}</p>
                                    </td>
                                    <td class="py-2.5 px-4 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-black bg-emerald-100 text-emerald-700">
                                            {{ siswa.rata_rata }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Notifikasi Eskalasi -->
            <div v-if="!statsLoading && wStats && wStats.notifikasi?.length > 0" class="mb-6">
                <div class="bg-gradient-to-r from-rose-50 to-orange-50 p-6 rounded-2xl shadow-sm border border-rose-200/60">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-rose-600 text-xl border border-rose-100 shadow-sm animate-pulse">⚠️</div>
                            <div>
                                <h3 class="text-sm font-bold text-rose-800">Peringatan Sistem (Eskalasi)</h3>
                                <p class="text-[10px] font-medium text-rose-600 uppercase tracking-widest">Siswa butuh tindak lanjut wali kelas segera</p>
                            </div>
                        </div>
                        <NuxtLink to="/guru/walas/bimbingan" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-colors shadow-sm shadow-rose-500/30 shrink-0 text-center">
                            Tindak Lanjut Kasus
                        </NuxtLink>
                    </div>
                    <div class="space-y-3">
                        <div v-for="notif in wStats.notifikasi" :key="notif.id" class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl bg-white border border-rose-100 shadow-sm gap-3">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="text-xs font-black text-slate-800">{{ notif.siswa }}</p>
                                    <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[9px] font-black uppercase tracking-widest rounded">Baru</span>
                                </div>
                                <p class="text-[11px] font-medium text-slate-600">{{ notif.deskripsi }}</p>
                            </div>
                            <div class="text-left sm:text-right shrink-0">
                                <p class="text-[9px] font-black uppercase tracking-wider text-slate-400">Dilaporkan oleh: <span class="text-sky-600">{{ notif.guru }}</span></p>
                                <p class="text-[9px] font-bold text-slate-400">{{ notif.waktu }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grafik Prestasi Akademik -->
            <div v-if="!statsLoading && wStats && wStats.grafik_siswa?.length > 0" class="mb-6">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center text-sky-600 text-xl border border-sky-100">📈</div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-800">Perkembangan Nilai (4 Periode)</h3>
                                <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest">Pilih siswa untuk melihat tren</p>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 w-full sm:w-auto shrink-0 mt-3 sm:mt-0">
                            <!-- Indikator Warna -->
                            <div class="flex items-center gap-3 text-[9px] font-black tracking-widest uppercase bg-white px-3 py-2 rounded-xl border border-slate-200 shadow-sm">
                                <span class="flex items-center gap-1.5 text-emerald-600" title="Aman (Nilai >= 80)"><div class="w-2 h-2 rounded-full bg-emerald-500"></div> &ge; 80</span>
                                <span class="flex items-center gap-1.5 text-amber-600" title="Waspada (Nilai 70 - 79)"><div class="w-2 h-2 rounded-full bg-amber-500"></div> 70-79</span>
                                <span class="flex items-center gap-1.5 text-rose-600" title="Bahaya (Nilai < 70)"><div class="w-2 h-2 rounded-full bg-rose-500"></div> &lt; 70</span>
                            </div>

                            <div class="w-full sm:w-64 shrink-0">
                                <select v-model="selectedChartSiswa" class="w-full px-4 py-2.5 rounded-xl border-2 border-slate-200/70 bg-white focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none">
                                    <option v-for="s in wStats.grafik_siswa" :key="s.id" :value="s.id">{{ s.nama }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 h-72">
                        <ClientOnly>
                            <Line :data="chartProgressData" :options="chartProgressOptions" />
                            <template #fallback>
                                <div class="flex items-center justify-center h-full text-slate-400 text-xs font-bold">Memuat Grafik...</div>
                            </template>
                        </ClientOnly>
                    </div>
                </div>
            </div>

            <!-- Analisis Khusus & Prestasi Mapel -->
            <div v-if="!statsLoading && wStats" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Butuh Penanganan -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 text-xl border border-rose-100">🚨</div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Siswa Butuh Penanganan</h3>
                            <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest">Risiko poin & absensi</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div v-if="wStats.penanganan?.length === 0" class="text-center py-4 text-slate-400 text-xs font-bold bg-slate-50 rounded-xl border border-slate-100">Semua siswa aman.</div>
                        <div v-for="p in wStats.penanganan" :key="p.id" class="flex items-center justify-between p-3 rounded-xl bg-rose-50/50 border border-rose-100/50">
                            <div>
                                <p class="text-xs font-bold text-slate-800">{{ p.nama }}</p>
                                <p class="text-[10px] text-slate-500 mt-0.5">Skor Risiko: <span class="font-bold text-rose-600">{{ p.skor_risiko }}</span></p>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] font-black uppercase tracking-wider text-rose-500">{{ p.poin_pelanggaran }} Poin BK</p>
                                <p class="text-[9px] font-black uppercase tracking-wider text-amber-500">{{ p.alpha }} Hari Alpha</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Prestasi Tiap Mapel -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 flex flex-col h-80">
                    <div class="flex items-center gap-3 mb-5 shrink-0">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 text-xl border border-amber-100">🏅</div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Bintang Kelas per Mapel</h3>
                            <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest">Nilai tertinggi</p>
                        </div>
                    </div>
                    <div class="flex-1 overflow-y-auto custom-scrollbar pr-2 space-y-2">
                        <div v-if="wStats.prestasi_mapel?.length === 0" class="text-center py-4 text-slate-400 text-xs font-bold bg-slate-50 rounded-xl border border-slate-100">Belum ada data nilai.</div>
                        <div v-for="(pm, idx) in wStats.prestasi_mapel" :key="idx" class="flex items-center justify-between p-2.5 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-100 transition-colors">
                            <div class="min-w-0 pr-3">
                                <p class="text-[11px] font-bold text-slate-800 truncate">{{ pm.mapel }}</p>
                                <p class="text-[10px] text-emerald-600 font-bold truncate mt-0.5">🏆 {{ pm.siswa }}</p>
                            </div>
                            <span class="inline-flex items-center justify-center min-w-[36px] px-1.5 py-1 rounded bg-slate-100 text-[11px] font-black text-slate-700 shrink-0">
                                {{ pm.nilai }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>
            
            <!-- MENU WALI KELAS -->
            <div class="mt-2">
              <h3 class="text-xs font-black text-amber-500 uppercase tracking-widest mb-4 flex items-center">✨ Panel Wali Kelas</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <NuxtLink to="/guru/walas/monitoring" class="group bg-white rounded-2xl p-5 border border-slate-200/60 shadow-sm hover:border-amber-300 hover:shadow-md hover:shadow-amber-100 transition-all cursor-pointer block">
                  <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 group-hover:bg-amber-500 group-hover:text-white transition-colors border border-amber-100">
                      <span class="text-lg">👀</span>
                    </div>
                    <div>
                      <div class="font-black text-slate-800 text-[11px] uppercase tracking-wide mb-0.5">Monitoring Nilai</div>
                      <div class="text-[9px] font-bold text-slate-400 uppercase">Pantau kelengkapan mapel</div>
                    </div>
                  </div>
                </NuxtLink>
                <NuxtLink to="/guru/walas/biodata" class="group bg-white rounded-2xl p-5 border border-slate-200/60 shadow-sm hover:border-amber-300 hover:shadow-md hover:shadow-amber-100 transition-all cursor-pointer block">
                  <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 group-hover:bg-amber-500 group-hover:text-white transition-colors border border-amber-100">
                      <span class="text-lg">🧑‍🎓</span>
                    </div>
                    <div>
                      <div class="font-black text-slate-800 text-[11px] uppercase tracking-wide mb-0.5">Kelola Biodata</div>
                      <div class="text-[9px] font-bold text-slate-400 uppercase">Validasi data siswa</div>
                    </div>
                  </div>
                </NuxtLink>
                <NuxtLink to="/guru/walas/rapor" class="group bg-white rounded-2xl p-5 border border-slate-200/60 shadow-sm hover:border-amber-300 hover:shadow-md hover:shadow-amber-100 transition-all cursor-pointer block">
                  <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 group-hover:bg-amber-500 group-hover:text-white transition-colors border border-amber-100">
                      <span class="text-lg">🖨️</span>
                    </div>
                    <div>
                      <div class="font-black text-slate-800 text-[11px] uppercase tracking-wide mb-0.5">Cetak Rapor</div>
                      <div class="text-[9px] font-bold text-slate-400 uppercase">Hasilkan dokumen resmi</div>
                    </div>
                  </div>
                </NuxtLink>
              </div>
            </div>
            
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Chart as ChartJS, ArcElement, Tooltip, Legend, LineElement, PointElement, LineController, CategoryScale, LinearScale } from 'chart.js'
import { Doughnut, Line } from 'vue-chartjs'

ChartJS.register(ArcElement, Tooltip, Legend, LineElement, PointElement, LineController, CategoryScale, LinearScale)

definePageMeta({ layout: "walas", middleware: "guru", title: 'Dashboard Wali Kelas' })

const tokenCookie = useCookie('auth_token')

const { data: response, pending: isLoading, error: err1 } = await useFetch(import.meta.env.VITE_API_BASE_URL + '/api/guru/dashboard', {
  headers: {
    'Authorization': `Bearer ${tokenCookie.value}`,
    'Accept': 'application/json'
  }
})
const dashboardData = computed(() => response.value?.data || null)

const { data: statsRes, pending: statsLoading, error: err2 } = await useFetch(import.meta.env.VITE_API_BASE_URL + '/api/guru/walas/dashboard-stats', {
  headers: {
    'Authorization': `Bearer ${tokenCookie.value}`,
    'Accept': 'application/json'
  }
})

const errorMessage = computed(() => err1.value?.message || err2.value?.message || (!response.value?.success && response.value?.message ? response.value?.message : '') || (!statsRes.value?.success && statsRes.value?.message ? statsRes.value?.message : ''))
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
const wStats = computed(() => statsRes.value?.data || null)

// --- Chart User (Doughnut) ---
const chartGenderData = computed(() => ({
  labels: ['Laki-laki', 'Perempuan'],
  datasets: [
    {
      backgroundColor: ['#3b82f6', '#f43f5e'],
      borderWidth: 0,
      hoverOffset: 5,
      data: [
        wStats.value?.populasi?.laki || 0,
        wStats.value?.populasi?.perempuan || 0
      ]
    }
  ]
}))

const chartGenderOptions = {
  responsive: true,
  maintainAspectRatio: false,
  cutout: '70%',
  plugins: {
    legend: { 
        position: 'bottom',
        labels: { font: { weight: 'bold', size: 10, family: "'Inter', sans-serif" } }
    }
  }
}

// --- Chart Progress (Line) ---
const selectedChartSiswa = ref('')
const selectedSiswaData = computed(() => {
    if (!wStats.value?.grafik_siswa) return null;
    
    // Auto select first student if empty
    if (!selectedChartSiswa.value && wStats.value.grafik_siswa.length > 0) {
        selectedChartSiswa.value = wStats.value.grafik_siswa[0].id;
    }
    
    return wStats.value.grafik_siswa.find(s => s.id === selectedChartSiswa.value);
})

const chartProgressData = computed(() => {
    const labels = wStats.value?.periode_labels || [];
    const dataObj = selectedSiswaData.value?.series || {};
    
    const dataPoints = labels.map(label => dataObj[label] || 0);

    return {
        labels: labels,
        datasets: [
            {
                label: 'Rata-rata Nilai',
                backgroundColor: '#cbd5e1', // default fallback
                borderColor: '#cbd5e1',     // default fallback
                borderWidth: 3,
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                tension: 0.3,
                data: dataPoints,
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

const chartProgressOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false }
    },
    scales: {
        y: {
            beginAtZero: true,
            max: 100,
            grid: { borderDash: [2, 4], color: '#f1f5f9' },
            ticks: { font: { size: 9, weight: 'bold' }, color: '#94a3b8' }
        },
        x: {
            grid: { display: false },
            ticks: { font: { size: 9, weight: 'bold' }, color: '#94a3b8' }
        }
    }
}

// --- Panduan Alur Kerja Walas ---
const walasWorkflow = computed(() => {
    return [
        {
            label: 'Monitor Nilai Mapel',
            desc: 'Pastikan seluruh guru mapel sudah menyelesaikan input nilai.',
            emoji: '👀',
            active: true,
            done: false,
            to: '/guru/walas/monitoring'
        },
        {
            label: 'Input Absensi',
            desc: 'Rekap kehadiran siswa jika tidak otomatis dari BK.',
            emoji: '📅',
            active: true,
            done: false,
            to: '/guru/walas/rekap'
        },
        {
            label: 'Input Ekstrakurikuler',
            desc: 'Masukkan nilai ekskul yang diikuti siswa.',
            emoji: '🎯',
            active: true,
            done: false,
            to: '/guru/walas/ekskul'
        },
        {
            label: 'Catatan & Keputusan',
            desc: 'Isi catatan dan tentukan kelulusan/kenaikan kelas.',
            emoji: '📝',
            active: true,
            done: false,
            to: '/guru/walas/catatan'
        },
        {
            label: 'Cetak Rapor',
            desc: 'Hasilkan dokumen PDF rapor.',
            emoji: '🖨️',
            active: true,
            done: false,
            to: '/guru/walas/rapor'
        }
    ]
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
