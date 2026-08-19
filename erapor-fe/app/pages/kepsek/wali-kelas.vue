<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-1.5 flex gap-1.5 shadow-sm z-20">
        <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTabMobile = tab.id"
          :class="activeTabMobile === tab.id ? 'bg-gradient-to-br from-purple-500 to-fuchsia-600 text-white shadow-md shadow-purple-500/20 ring-2 ring-purple-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="flex-1 rounded-lg flex flex-col items-center justify-center py-2 px-1 transition-all active:scale-95">
          <AppIcon :name="tab.icon" class="text-lg mb-0.5 transition-transform" :class="activeTabMobile === tab.id ? 'scale-110' : ''" />
          <span class="text-[10px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
        </button>
      </div>

      <!-- Panel Dock Kiri (Daftar Kelas) -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all', activeTabMobile === 'list' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[52px]' : '']">
        
        <div class="p-4 pb-2 shrink-0 z-10 relative">
          <div class="bg-gradient-to-r from-purple-600 to-fuchsia-600 rounded-2xl p-4 border border-purple-500 shadow-sm relative overflow-hidden flex items-center gap-3">
            <div class="w-8 h-8 flex items-center justify-center shrink-0 bg-white/10 rounded-lg relative z-10 text-white"><AppIcon name="users" class="w-5 h-5" /></div>
            <div class="relative z-10">
                <h3 class="text-xs font-black uppercase tracking-widest text-white">Daftar Wali Kelas</h3>
                <p class="text-[10px] text-purple-100 font-semibold uppercase mt-0.5">Pemantauan & Evaluasi</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-15 text-white pointer-events-none">
              <svg class="w-16 h-16 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg>
            </div>
          </div>
        </div>
        
        <div class="flex-1 overflow-y-auto custom-scrollbar p-0">
          <div v-if="isLoadingKelas" class="flex flex-col items-center justify-center h-full opacity-60">
             <div class="w-8 h-8 border-4 border-purple-400 border-t-transparent rounded-full animate-spin mb-4"></div>
             <span class="text-[10px] font-black text-purple-500 uppercase tracking-widest">Memuat...</span>
          </div>
          <table v-else class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-y border-slate-200 text-xs text-slate-500 uppercase tracking-widest font-black sticky top-0 z-10 shadow-sm">
              <tr>
                <th class="p-3 w-20">Kelas</th>
                <th class="p-3">Wali Kelas</th>
                <th class="p-3 text-center w-16">Aksi</th>
              </tr>
            </thead>
            <tbody class="text-sm divide-y divide-slate-100">
              <template v-for="group in groupedKelas" :key="group.tingkat">
                <tr>
                  <td colspan="3" class="bg-slate-100/50 py-1.5 px-3 text-[10px] font-black uppercase text-slate-500 tracking-widest border-y border-slate-200">
                    Tingkat {{ group.tingkat }}
                  </td>
                </tr>
                <tr v-for="item in group.items" :key="item.id" class="hover:bg-purple-50 transition-colors group cursor-pointer" :class="selectedKelasId === item.id ? 'bg-purple-50 border-l-4 border-purple-500' : 'border-l-4 border-transparent'" @click="selectKelas(item)">
                  <td class="p-3 pl-4">
                    <p class="font-black text-slate-800 text-xs">{{ item.tingkat }} {{ item.nama_kelas }}</p>
                  </td>
                  <td class="p-3">
                    <p class="font-medium text-slate-600 text-xs truncate max-w-[120px]">{{ item.wali_kelas?.guru?.name || 'Belum Ditugaskan' }}</p>
                  </td>
                  <td class="p-3 text-center">
                    <button type="button" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:border-purple-200 hover:bg-purple-50 hover:text-purple-600 flex items-center justify-center transition-all shadow-sm mx-auto" :class="selectedKelasId === item.id ? 'text-purple-600 border-purple-300 bg-purple-50' : ''">
                      <AppIcon name="eye" class="w-4 h-4" />
                    </button>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Panel Flow Kanan (Dashboard Walas) -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative', activeTabMobile === 'dashboard' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[52px]' : '']">
        <div class="p-0 sm:pt-3 sm:pb-6 sm:px-6 lg:pt-3 lg:pb-8 lg:px-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-none sm:rounded-[2rem] shadow-[0_2px_15px_-3px_rgba(0,0,0,0.05)] overflow-hidden flex flex-col flex-1 relative min-h-0 border-0 sm:border sm:border-slate-200/60">
            <div class="px-6 py-5 border-b border-slate-100 flex flex-row justify-between items-center gap-2 shrink-0 z-10 bg-white">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-purple-50 shadow-sm border border-purple-100 flex items-center justify-center text-xl hidden sm:flex text-purple-500"><AppIcon name="chart-bar" class="w-6 h-6" /></div>
                    <div>
                        <h3 class="text-[11px] sm:text-sm font-black uppercase tracking-widest text-purple-700">Dashboard Evaluasi</h3>
                        <p class="text-[10px] sm:text-[10px] font-bold text-slate-400 uppercase mt-0.5">
                            {{ selectedKelas ? selectedKelas.nama_kelas + ' - ' + (selectedKelas.wali_kelas?.guru?.name || 'Belum Ditugaskan') : 'Pilih Kelas' }}
                        </p>
                    </div>
                </div>
                <button @click="fetchDashboard(selectedKelasId)" v-if="selectedKelasId" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-slate-200 hover:text-slate-700 font-bold transition-colors shrink-0" title="Refresh">
                    <AppIcon name="arrow-path" class="w-5 h-5" />
                </button>
            </div>

            <!-- Dashboard Content -->
            <div class="flex-1 overflow-y-auto custom-scrollbar relative bg-slate-50 p-6">
                <div v-if="!selectedKelasId" class="flex-grow flex flex-col items-center justify-center h-full py-20 text-center opacity-50">
                    <AppIcon name="hand-raised" class="w-16 h-16 text-slate-400 mb-4" />
                    <h3 class="text-xl font-black text-slate-800 tracking-tight">Belum Ada Kelas yang Dipilih</h3>
                    <p class="text-sm text-slate-500 mt-2">Silakan pilih kelas dari panel kiri untuk melihat dashboard wali kelas.</p>
                </div>
                <div v-else-if="isLoadingDashboard" class="flex-grow flex flex-col items-center justify-center h-full py-20 text-center">
                    <div class="w-10 h-10 border-4 border-purple-400 border-t-transparent rounded-full animate-spin mb-4 mx-auto"></div>
                    <span class="text-xs font-black text-purple-500 uppercase tracking-widest">Memuat Dashboard...</span>
                </div>
                <div v-else-if="dashboardError" class="bg-red-50 text-red-600 p-4 rounded-xl border border-red-100 text-center text-sm font-medium my-4">
                    {{ dashboardError }}
                </div>
                <div v-else-if="wStats" class="space-y-6">
                    
                    <!-- STATISTIK KELAS (4 COLUMNS) -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-sky-200">
                            <div class="h-12 w-12 rounded-xl bg-sky-50 flex items-center justify-center text-sky-600 text-2xl border border-sky-100"><AppIcon name="users" /></div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total Siswa</p>
                                <p class="text-2xl font-black text-slate-800 leading-none">{{ wStats.populasi?.total || 0 }}</p>
                            </div>
                        </div>
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-blue-200">
                            <div class="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 text-2xl border border-blue-100"><AppIcon name="user" /></div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Laki-laki</p>
                                <p class="text-2xl font-black text-slate-800 leading-none">{{ wStats.populasi?.laki || 0 }}</p>
                            </div>
                        </div>
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-rose-200">
                            <div class="h-12 w-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 text-2xl border border-rose-100"><AppIcon name="user" /></div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Perempuan</p>
                                <p class="text-2xl font-black text-slate-800 leading-none">{{ wStats.populasi?.perempuan || 0 }}</p>
                            </div>
                        </div>
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center space-x-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-emerald-200">
                            <div class="h-12 w-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-2xl border border-emerald-100"><AppIcon name="star" /></div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Rata-rata Kelas</p>
                                <p class="text-2xl font-black text-slate-800 leading-none">{{ wStats.rata_rata_kelas || 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Notifikasi Eskalasi -->
                    <div v-if="wStats.notifikasi?.length > 0" class="mb-6">
                        <div class="bg-gradient-to-r from-rose-50 to-orange-50 p-6 rounded-2xl shadow-sm border border-rose-200/60">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-rose-600 text-xl border border-rose-100 shadow-sm animate-pulse"><AppIcon name="exclamation-triangle" /></div>
                                    <div>
                                        <h3 class="text-sm font-bold text-rose-800">Peringatan Sistem (Eskalasi)</h3>
                                        <p class="text-[10px] font-medium text-rose-600 uppercase tracking-widest">Siswa butuh tindak lanjut wali kelas segera</p>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div v-for="notif in wStats.notifikasi" :key="notif.id" class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl bg-white border border-rose-100 shadow-sm gap-3">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="text-xs font-black text-slate-800">{{ notif.siswa }}</p>
                                            <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-black uppercase tracking-widest rounded">Baru</span>
                                        </div>
                                        <p class="text-[11px] font-medium text-slate-600">{{ notif.deskripsi }}</p>
                                    </div>
                                    <div class="text-left sm:text-right shrink-0">
                                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Dilaporkan oleh: <span class="text-sky-600">{{ notif.guru }}</span></p>
                                        <p class="text-[10px] font-bold text-slate-400">{{ notif.waktu }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grafik Prestasi Akademik (2 Cards: Per-Siswa & Per-Kelas) -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Card 1: Tren Nilai Per Siswa -->
                        <div class="bg-white rounded-none sm:rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col">
                            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center text-sky-600 text-xl border border-sky-100"><AppIcon name="user" /></div>
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-800">Perkembangan Nilai Siswa</h3>
                                        <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest">Pilih siswa untuk melihat tren</p>
                                    </div>
                                </div>
                                <div class="w-full sm:w-48 shrink-0">
                                    <select v-model="selectedChartSiswa" class="w-full px-3 py-2 rounded-xl border-2 border-slate-200/70 bg-white focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none">
                                        <option v-for="s in wStats.grafik_siswa" :key="s.id" :value="s.id">{{ s.nama }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="p-6 h-72 flex-1">
                                <ClientOnly>
                                    <Line :data="chartProgressData" :options="chartProgressOptions" />
                                    <template #fallback>
                                        <div class="flex items-center justify-center h-full text-slate-400 text-xs font-bold">Memuat Grafik...</div>
                                    </template>
                                </ClientOnly>
                            </div>
                        </div>

                        <!-- Card 2: Tren Rata-rata 1 Kelas Per Periode -->
                        <div class="bg-white rounded-none sm:rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col">
                            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 text-xl border border-purple-100"><AppIcon name="chart-bar" /></div>
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-800">Rata-Rata Nilai Kelas</h3>
                                        <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest">Tren performa kelas per periode</p>
                                    </div>
                                </div>
                                <!-- Indikator Warna -->
                                <div class="flex items-center gap-2 text-[10px] font-black tracking-widest uppercase bg-white px-2.5 py-1.5 rounded-xl border border-slate-200 shadow-sm">
                                    <span class="flex items-center gap-1 text-emerald-600" title="Aman (>= 80)"><div class="w-2 h-2 rounded-full bg-emerald-500"></div> &ge;80</span>
                                    <span class="flex items-center gap-1 text-amber-600" title="Waspada (70-79)"><div class="w-2 h-2 rounded-full bg-amber-500"></div> 70-79</span>
                                    <span class="flex items-center gap-1 text-rose-600" title="Bahaya (< 70)"><div class="w-2 h-2 rounded-full bg-rose-500"></div> &lt;70</span>
                                </div>
                            </div>
                            <div class="p-6 h-72 flex-1">
                                <ClientOnly>
                                    <Line :data="chartClassProgressData" :options="chartProgressOptions" />
                                    <template #fallback>
                                        <div class="flex items-center justify-center h-full text-slate-400 text-xs font-bold">Memuat Grafik...</div>
                                    </template>
                                </ClientOnly>
                            </div>
                        </div>
                    </div>

                    <!-- Evaluasi KKM Per Periode (4 Doughnut Charts) -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col mb-6">
                        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600 text-xl border border-teal-100"><AppIcon name="presentation-chart-bar" /></div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-800">Evaluasi Ketuntasan Belajar (KKM)</h3>
                                    <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest">Akumulasi seluruh siswa & mata pelajaran</p>
                                </div>
                            </div>
                            <!-- Legend -->
                            <div class="flex items-center gap-3 text-[10px] font-black tracking-widest uppercase bg-white px-3 py-2 rounded-xl border border-slate-200 shadow-sm">
                                <span class="flex items-center gap-1.5 text-emerald-600"><div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div> Tuntas</span>
                                <span class="flex items-center gap-1.5 text-rose-600"><div class="w-2.5 h-2.5 rounded-full bg-rose-500"></div> Belum Tuntas</span>
                                <span class="flex items-center gap-1.5 text-slate-400"><div class="w-2.5 h-2.5 rounded-full bg-slate-300"></div> Abu-abu</span>
                            </div>
                        </div>
                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div v-for="(kkm, idx) in kkmCharts" :key="idx" class="flex flex-col items-center">
                                <h4 class="text-[11px] font-black uppercase text-slate-700 tracking-wider mb-4">{{ kkm.periode }}</h4>
                                <div class="relative w-32 h-32 mb-4">
                                    <ClientOnly>
                                        <Doughnut :data="kkm.chartData" :options="kkm.chartOptions" />
                                        <template #fallback>
                                            <div class="flex items-center justify-center h-full w-full bg-slate-50 rounded-full border border-slate-100 animate-pulse"></div>
                                        </template>
                                    </ClientOnly>
                                    <!-- Inner Text -->
                                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-center px-2">
                                        <span v-if="kkm.kkm_set && kkm.has_data" class="text-lg font-black text-slate-800 leading-none">{{ kkm.percentageText }}</span>
                                        <span v-if="kkm.kkm_set && kkm.has_data" class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mt-1">Tuntas</span>
                                        <span v-if="!kkm.kkm_set" class="text-[10px] font-bold text-slate-400 uppercase leading-tight">KKM<br>Belum<br>Diseting</span>
                                        <span v-if="kkm.kkm_set && (!kkm.has_data || kkm.total === 0)" class="text-[10px] font-bold text-slate-400 uppercase leading-tight">Belum<br>Ada<br>Nilai</span>
                                    </div>
                                </div>
                                <div v-if="kkm.kkm_set" class="text-center bg-slate-50 border border-slate-100 rounded-lg px-3 py-1.5 w-full">
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Batas KKM: <span class="text-slate-800">{{ kkm.kkm_value }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Peringkat & Bintang Kelas -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Top 10 Besar -->
                        <div class="bg-white rounded-none sm:rounded-2xl shadow-sm border border-slate-200/60 flex flex-col overflow-hidden">
                            <div class="p-5 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50 shrink-0">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-xl border border-emerald-100"><AppIcon name="star" /></div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-800">Top 10 Siswa Berprestasi</h3>
                                    <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest">Rata-rata Nilai Tertinggi</p>
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

                        <!-- Prestasi Tiap Mapel (Bintang Kelas) -->
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 flex flex-col">
                            <div class="flex items-center gap-3 mb-5 shrink-0">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 text-xl border border-amber-100"><AppIcon name="star" /></div>
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
                                        <p class="text-[10px] text-emerald-600 font-bold truncate mt-0.5"><AppIcon name="star" /> {{ pm.siswa }}</p>
                                    </div>
                                    <span class="inline-flex items-center justify-center min-w-[36px] px-1.5 py-1 rounded bg-slate-100 text-[11px] font-black text-slate-700 shrink-0">
                                        {{ pm.nilai }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gender & Penanganan -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                        <!-- Chart Gender -->
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 flex flex-col h-80">
                            <div class="flex items-center gap-3 mb-6 shrink-0">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-xl border border-indigo-100"><AppIcon name="chart-pie" /></div>
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

                        <!-- Butuh Penanganan -->
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200/60 col-span-1 lg:col-span-2 flex flex-col h-80">
                            <div class="flex items-center gap-3 mb-5 shrink-0">
                                <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 text-xl border border-rose-100"><AppIcon name="exclamation-triangle" /></div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-800">Siswa Butuh Penanganan</h3>
                                    <p class="text-[10px] font-medium text-slate-500 uppercase tracking-widest">Risiko poin & absensi</p>
                                </div>
                            </div>
                            <div class="space-y-3 overflow-y-auto custom-scrollbar flex-1 pr-2">
                                <div v-if="wStats.penanganan?.length === 0" class="text-center py-4 text-slate-400 text-xs font-bold bg-slate-50 rounded-xl border border-slate-100">Semua siswa aman.</div>
                                <div v-for="p in wStats.penanganan" :key="p.id" class="flex items-center justify-between p-3 rounded-xl bg-rose-50/50 border border-rose-100/50">
                                    <div>
                                        <p class="text-xs font-bold text-slate-800">{{ p.nama }}</p>
                                        <p class="text-[10px] text-slate-500 mt-0.5">Skor Risiko: <span class="font-bold text-rose-600">{{ p.skor_risiko }}</span></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] font-black uppercase tracking-wider text-rose-500">{{ p.poin_pelanggaran }} Poin BK</p>
                                        <p class="text-[10px] font-black uppercase tracking-wider text-amber-500">{{ p.alpha }} Hari Alpha</p>
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Chart as ChartJS, ArcElement, Tooltip, Legend, LineElement, PointElement, LineController, CategoryScale, LinearScale } from 'chart.js'
import { Doughnut, Line } from 'vue-chartjs'

ChartJS.register(ArcElement, Tooltip, Legend, LineElement, PointElement, LineController, CategoryScale, LinearScale)

definePageMeta({ layout: "kepsek", middleware: "kepsek", title: 'Pemantauan Wali Kelas' })

// Responsiveness & Mobile Tabs
const windowWidth = ref(1024)
const isDesktop = computed(() => windowWidth.value >= 1280)
const activeTabMobile = ref('list')
const mobileTabs = [
  { id: 'list', title: 'Daftar Kelas', icon: 'list-bullet' },
  { id: 'dashboard', title: 'Dashboard Walas', icon: 'chart-bar' }
]

onMounted(() => {
    windowWidth.value = window.innerWidth
    window.addEventListener('resize', () => windowWidth.value = window.innerWidth)
    fetchDaftarKelas()
})
onUnmounted(() => {
    window.removeEventListener('resize', () => windowWidth.value = window.innerWidth)
})

const tokenCookie = useCookie('auth_token')

// DATA KELAS
const kelass = ref([])
const isLoadingKelas = ref(true)

const fetchDaftarKelas = async () => {
    isLoadingKelas.value = true
    try {
        const response = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/kepsek/wali-kelas', {
            headers: { Authorization: `Bearer ${tokenCookie.value}` }
        })
        kelass.value = response?.data || []
    } catch (error) {
        console.error('Failed to fetch kelas data:', error)
    } finally {
        isLoadingKelas.value = false
    }
}

// DASHBOARD STATE
const groupedKelas = computed(() => {
    const groups = {}
    kelass.value.forEach(item => {
        if (!groups[item.tingkat]) {
            groups[item.tingkat] = []
        }
        groups[item.tingkat].push(item)
    })
    return Object.keys(groups).sort().map(tingkat => ({
        tingkat: tingkat,
        items: groups[tingkat]
    }))
})

const selectedKelasId = ref(null)
const selectedKelas = computed(() => kelass.value.find(k => k.id === selectedKelasId.value))
const wStats = ref(null)
const isLoadingDashboard = ref(false)
const dashboardError = ref(null)

const selectKelas = (kelasItem) => {
    selectedKelasId.value = kelasItem.id
    if (!isDesktop.value) activeTabMobile.value = 'dashboard'
    fetchDashboard(kelasItem.id)
}

const fetchDashboard = async (kelasId) => {
    if (!kelasId) return;
    isLoadingDashboard.value = true
    dashboardError.value = null
    wStats.value = null
    try {
        const response = await $fetch(import.meta.env.VITE_API_BASE_URL + `/api/kepsek/wali-kelas/${kelasId}/dashboard`, {
            headers: { Authorization: `Bearer ${tokenCookie.value}` }
        })
        if(response.success) {
            wStats.value = response.data
        } else {
            dashboardError.value = response.message || 'Gagal memuat dashboard wali kelas'
        }
    } catch (error) {
        dashboardError.value = error.response?._data?.message || 'Terjadi kesalahan saat memuat data.'
    } finally {
        isLoadingDashboard.value = false
    }
}

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
    if ((!selectedChartSiswa.value || !wStats.value.grafik_siswa.find(s => s.id === selectedChartSiswa.value)) && wStats.value.grafik_siswa.length > 0) {
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

const chartClassProgressData = computed(() => {
    const labels = wStats.value?.periode_labels || [];
    const dataObj = wStats.value?.grafik_kelas || {};
    
    const dataPoints = labels.map(label => dataObj[label] || 0);

    return {
        labels: labels,
        datasets: [
            {
                label: 'Rata-rata Kelas',
                backgroundColor: '#a855f7', // Purple
                borderColor: '#a855f7',
                borderWidth: 3,
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                tension: 0.3,
                data: dataPoints,
                segment: {
                    borderColor: (ctx) => {
                        if (!ctx.p1) return '#a855f7';
                        const val = ctx.p1.parsed.y;
                        if (val >= 80) return '#10b981';
                        if (val >= 70) return '#eab308';
                        return '#ef4444';
                    }
                },
                pointBackgroundColor: (ctx) => {
                    const val = ctx.raw;
                    if (val === undefined || val === null) return '#a855f7';
                    if (val >= 80) return '#10b981';
                    if (val >= 70) return '#eab308';
                    return '#ef4444';
                }
            }
        ]
    }
})

const kkmCharts = computed(() => {
    if (!wStats.value || !wStats.value.grafik_kkm) return []
    return wStats.value.grafik_kkm.map(k => {
        let datasets = []
        if (!k.kkm_set || !k.has_data || k.total === 0) {
            datasets = [{
                data: [1],
                backgroundColor: ['#e2e8f0'], // Gray
                borderWidth: 0
            }]
        } else {
            datasets = [{
                data: [k.tuntas, k.belum_tuntas],
                backgroundColor: ['#10b981', '#ef4444'], // Green, Red
                borderWidth: 0,
                hoverOffset: 4
            }]
        }
        
        let percentageText = "0%";
        if (k.kkm_set && k.has_data && k.total > 0) {
            percentageText = Math.round((k.tuntas / k.total) * 100) + "%";
        }
        
        return {
            periode: k.periode,
            kkm_set: k.kkm_set,
            has_data: k.has_data,
            total: k.total,
            tuntas: k.tuntas,
            belum_tuntas: k.belum_tuntas,
            kkm_value: k.kkm_value,
            percentageText: percentageText,
            chartData: {
                labels: (!k.kkm_set || !k.has_data || k.total === 0) ? ['Belum Tersedia'] : ['Tuntas (≥ ' + k.kkm_value + ')', 'Belum Tuntas (< ' + k.kkm_value + ')'],
                datasets: datasets
            },
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '80%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.parsed !== null) {
                                    if (!k.kkm_set || !k.has_data || k.total === 0) {
                                        return 'Belum ada data';
                                    }
                                    let percentage = Math.round((context.parsed / k.total) * 100) + '%';
                                    return context.parsed + ' Nilai (' + percentage + ')';
                                }
                                return '';
                            }
                        }
                    }
                }
            }
        }
    })
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
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
