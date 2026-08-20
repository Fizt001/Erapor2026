<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow (Standard 2 Card Layout) -->
    <div class="flex-1 flex flex-col xl:flex-row xl:overflow-hidden overflow-y-auto relative">
      
      <!-- CARD 1: Panel Dock Kiri -->
      <div class="xl:w-[320px] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col xl:h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] xl:overflow-y-auto custom-scrollbar">
        <div class="p-6 space-y-6">
          
          <!-- Welcome Widget -->
          <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl p-5 shadow-sm text-white relative overflow-hidden border border-indigo-500">
            <div class="relative z-10">
              <h2 class="text-lg font-black mb-1">Halo, {{ userProfile?.name?.split(' ')[0] || 'Kepala Sekolah' }} <span class="text-amber-300">👋</span></h2>
              <p class="text-indigo-100 text-[10px] uppercase tracking-widest font-bold">
                Tahun Ajaran <span class="text-white bg-indigo-900/50 px-1.5 py-0.5 rounded ml-1">{{ ta_aktif?.tahun || 'Memuat...' }}</span>
              </p>
            </div>
            <div class="absolute right-[-10px] bottom-[-10px] opacity-10">
              <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
            </div>
          </div>

          <!-- Status Institusi -->
          <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center">Identitas Sekolah</h3>
            <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
                <p class="font-black text-slate-800 text-sm leading-tight">{{ sekolah?.nama || 'SMK TINTA EMAS INDONESIA' }}</p>
                <p class="text-[10px] font-bold text-slate-500 mt-1 uppercase tracking-wider">NPSN: <span class="text-indigo-600">{{ sekolah?.npsn || '20253779' }}</span></p>
            </div>
          </div>
          
          <!-- Quick Actions -->
          <div class="bg-slate-900 p-5 rounded-2xl shadow-sm text-white relative overflow-hidden">
             <div class="relative z-10">
                <h3 class="text-[10px] font-black uppercase tracking-widest mb-3 text-slate-400">Akses Cepat</h3>
                <div class="space-y-2">
                    <NuxtLink to="/kepsek/supervisi" class="block bg-slate-800 hover:bg-indigo-600 transition-colors p-3 rounded-xl text-xs font-bold flex justify-between items-center group border border-slate-700 hover:border-indigo-500">
                        Supervisi Guru
                        <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                    </NuxtLink>
                    <NuxtLink to="/kepsek/jurnal" class="block bg-slate-800 hover:bg-indigo-600 transition-colors p-3 rounded-xl text-xs font-bold flex justify-between items-center group border border-slate-700 hover:border-indigo-500">
                        Pantau Jurnal KBM
                        <span class="opacity-0 group-hover:opacity-100 transition-opacity">→</span>
                    </NuxtLink>
                </div>
             </div>
          </div>

        </div>
      </div>

      <!-- CARD 2: Panel Flow Kanan -->
      <div class="xl:flex-1 flex-shrink-0 bg-slate-50 flex flex-col xl:h-full min-w-0 xl:overflow-y-auto custom-scrollbar">
        
        <!-- Loading State -->
        <div v-if="isLoading" class="flex-grow flex items-center justify-center py-12">
          <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
        </div>

        <div v-else class="p-4 lg:p-8 space-y-6 max-w-[1400px] mx-auto w-full">
            
            <!-- SECTION 1: 4 SMALL CARDS (Statistik Utama) -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
                <!-- Card Guru -->
                <div class="bg-white p-4 lg:p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center gap-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-indigo-300">
                    <div class="h-10 w-10 lg:h-12 lg:w-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-xl border border-indigo-100 shrink-0">👨‍🏫</div>
                    <div class="min-w-0">
                        <p class="text-[9px] lg:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5 truncate">Guru Pengampu</p>
                        <p class="text-xl lg:text-2xl font-black text-slate-800 leading-none">{{ stats.totalGuru || 0 }}</p>
                    </div>
                </div>
                <!-- Card Siswa -->
                <div class="bg-white p-4 lg:p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center gap-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-emerald-300">
                    <div class="h-10 w-10 lg:h-12 lg:w-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-xl border border-emerald-100 shrink-0">👨‍🎓</div>
                    <div class="min-w-0">
                        <p class="text-[9px] lg:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5 truncate">Total Siswa</p>
                        <p class="text-xl lg:text-2xl font-black text-slate-800 leading-none">{{ stats.totalSiswa || 0 }}</p>
                    </div>
                </div>
                <!-- Card Kelas -->
                <div class="bg-white p-4 lg:p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center gap-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-amber-300">
                    <div class="h-10 w-10 lg:h-12 lg:w-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 text-xl border border-amber-100 shrink-0">🏫</div>
                    <div class="min-w-0">
                        <p class="text-[9px] lg:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5 truncate">Jumlah Kelas</p>
                        <p class="text-xl lg:text-2xl font-black text-slate-800 leading-none">{{ stats.totalKelas || 0 }}</p>
                    </div>
                </div>
                <!-- Card Periode -->
                <div class="bg-white p-4 lg:p-5 rounded-2xl shadow-sm border border-slate-200/60 flex items-center gap-4 transition-all hover:-translate-y-1 hover:shadow-md hover:border-rose-300">
                    <div class="h-10 w-10 lg:h-12 lg:w-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 text-xl border border-rose-100 shrink-0">📅</div>
                    <div class="min-w-0">
                        <p class="text-[9px] lg:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5 truncate">Periode Aktif</p>
                        <p class="text-sm lg:text-base font-black text-slate-800 leading-tight truncate">{{ stats.taAktif || '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: 3 COLUMN ANALYTICS (Ranking 3 Besar per Tingkat) -->
            <div class="pt-4">
              <div class="flex items-center gap-2 mb-4 px-1">
                  <div class="h-8 w-1.5 bg-indigo-500 rounded-full"></div>
                  <div>
                      <h3 class="text-sm font-black text-slate-800">Analitik Peringkat Siswa</h3>
                      <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Top 3 Nilai Rata-rata Sumatif Per Kelas</p>
                  </div>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6">
                  
                  <!-- TINGKAT X -->
                  <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 flex flex-col h-full">
                      <div class="flex items-center justify-between mb-4">
                          <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-black rounded-lg border border-indigo-100 uppercase tracking-widest">Tingkat X</span>
                      </div>
                      
                      <!-- Slider Kelas X -->
                      <div class="flex items-center justify-between bg-slate-50 rounded-xl px-2 py-1.5 border border-slate-200 mb-5">
                          <button @click="slideKelas('X', -1)" class="p-1 hover:bg-slate-200 rounded-lg text-slate-500 transition-colors">
                              <span class="text-xs font-black">◀</span>
                          </button>
                          <span class="text-[11px] font-black text-slate-700 text-center flex-1 truncate px-2">Kelas {{ currentKelasName('X') }}</span>
                          <button @click="slideKelas('X', 1)" class="p-1 hover:bg-slate-200 rounded-lg text-slate-500 transition-colors">
                              <span class="text-xs font-black">▶</span>
                          </button>
                      </div>
                      
                      <!-- Podium Container -->
                      <div class="flex-1 flex flex-col justify-end min-h-[220px]">
                          <template v-if="!selectedKelasX">
                              <div class="flex items-center justify-center h-full text-slate-300 text-[10px] font-black uppercase tracking-widest text-center px-4">Pilih kelas di atas untuk melihat peringkat</div>
                          </template>
                          <template v-else-if="!topRankingX || topRankingX.length === 0">
                              <div class="flex items-center justify-center h-full text-slate-300 text-[10px] font-black uppercase tracking-widest text-center px-4">Belum ada data nilai di kelas ini</div>
                          </template>
                          <template v-else>
                              <div class="flex items-end justify-center gap-2 h-full pt-4">
                                  <!-- Juara 2 -->
                                  <div v-if="topRankingX[1]" class="flex flex-col items-center w-1/3">
                                      <div class="text-center mb-2">
                                          <p class="text-[10px] font-black text-slate-700 leading-tight line-clamp-2 px-1" :title="topRankingX[1].nama">{{ topRankingX[1].nama }}</p>
                                          <span class="inline-block px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded text-[9px] font-black mt-1">{{ topRankingX[1].rata_rata }}</span>
                                      </div>
                                      <div class="w-full bg-gradient-to-t from-slate-300 to-slate-200 rounded-t-xl h-[80px] border-t-4 border-slate-400 flex items-start justify-center pt-2 shadow-inner">
                                          <span class="text-2xl drop-shadow-md">🥈</span>
                                      </div>
                                  </div>
                                  
                                  <!-- Juara 1 -->
                                  <div v-if="topRankingX[0]" class="flex flex-col items-center w-1/3 z-10">
                                      <div class="text-center mb-2">
                                          <p class="text-[10px] font-black text-indigo-900 leading-tight line-clamp-2 px-1" :title="topRankingX[0].nama">{{ topRankingX[0].nama }}</p>
                                          <span class="inline-block px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded text-[9px] font-black mt-1">{{ topRankingX[0].rata_rata }}</span>
                                      </div>
                                      <div class="w-full bg-gradient-to-t from-amber-300 to-amber-200 rounded-t-xl h-[120px] border-t-4 border-amber-400 flex items-start justify-center pt-2 shadow-[0_-5px_15px_rgba(251,191,36,0.3)]">
                                          <span class="text-3xl drop-shadow-lg">🏆</span>
                                      </div>
                                  </div>
                                  
                                  <!-- Juara 3 -->
                                  <div v-if="topRankingX[2]" class="flex flex-col items-center w-1/3">
                                      <div class="text-center mb-2">
                                          <p class="text-[10px] font-black text-slate-700 leading-tight line-clamp-2 px-1" :title="topRankingX[2].nama">{{ topRankingX[2].nama }}</p>
                                          <span class="inline-block px-1.5 py-0.5 bg-orange-100 text-orange-700 rounded text-[9px] font-black mt-1">{{ topRankingX[2].rata_rata }}</span>
                                      </div>
                                      <div class="w-full bg-gradient-to-t from-orange-300/50 to-orange-200/50 rounded-t-xl h-[60px] border-t-4 border-orange-300 flex items-start justify-center pt-2 shadow-inner">
                                          <span class="text-xl drop-shadow-md">🥉</span>
                                      </div>
                                  </div>
                              </div>
                          </template>
                      </div>
                  </div>
                  
                  <!-- TINGKAT XI -->
                  <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 flex flex-col h-full">
                      <div class="flex items-center justify-between mb-4">
                          <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-black rounded-lg border border-emerald-100 uppercase tracking-widest">Tingkat XI</span>
                      </div>
                      
                      <!-- Slider Kelas XI -->
                      <div class="flex items-center justify-between bg-slate-50 rounded-xl px-2 py-1.5 border border-slate-200 mb-5">
                          <button @click="slideKelas('XI', -1)" class="p-1 hover:bg-slate-200 rounded-lg text-slate-500 transition-colors">
                              <span class="text-xs font-black">◀</span>
                          </button>
                          <span class="text-[11px] font-black text-slate-700 text-center flex-1 truncate px-2">Kelas {{ currentKelasName('XI') }}</span>
                          <button @click="slideKelas('XI', 1)" class="p-1 hover:bg-slate-200 rounded-lg text-slate-500 transition-colors">
                              <span class="text-xs font-black">▶</span>
                          </button>
                      </div>
                      
                      <!-- Podium Container -->
                      <div class="flex-1 flex flex-col justify-end min-h-[220px]">
                          <template v-if="!selectedKelasXI">
                              <div class="flex items-center justify-center h-full text-slate-300 text-[10px] font-black uppercase tracking-widest text-center px-4">Pilih kelas di atas untuk melihat peringkat</div>
                          </template>
                          <template v-else-if="!topRankingXI || topRankingXI.length === 0">
                              <div class="flex items-center justify-center h-full text-slate-300 text-[10px] font-black uppercase tracking-widest text-center px-4">Belum ada data nilai di kelas ini</div>
                          </template>
                          <template v-else>
                              <div class="flex items-end justify-center gap-2 h-full pt-4">
                                  <div v-if="topRankingXI[1]" class="flex flex-col items-center w-1/3">
                                      <div class="text-center mb-2">
                                          <p class="text-[10px] font-black text-slate-700 leading-tight line-clamp-2 px-1">{{ topRankingXI[1].nama }}</p>
                                          <span class="inline-block px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded text-[9px] font-black mt-1">{{ topRankingXI[1].rata_rata }}</span>
                                      </div>
                                      <div class="w-full bg-gradient-to-t from-slate-300 to-slate-200 rounded-t-xl h-[80px] border-t-4 border-slate-400 flex items-start justify-center pt-2 shadow-inner"><span class="text-2xl drop-shadow-md">🥈</span></div>
                                  </div>
                                  <div v-if="topRankingXI[0]" class="flex flex-col items-center w-1/3 z-10">
                                      <div class="text-center mb-2">
                                          <p class="text-[10px] font-black text-indigo-900 leading-tight line-clamp-2 px-1">{{ topRankingXI[0].nama }}</p>
                                          <span class="inline-block px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded text-[9px] font-black mt-1">{{ topRankingXI[0].rata_rata }}</span>
                                      </div>
                                      <div class="w-full bg-gradient-to-t from-amber-300 to-amber-200 rounded-t-xl h-[120px] border-t-4 border-amber-400 flex items-start justify-center pt-2 shadow-[0_-5px_15px_rgba(251,191,36,0.3)]"><span class="text-3xl drop-shadow-lg">🏆</span></div>
                                  </div>
                                  <div v-if="topRankingXI[2]" class="flex flex-col items-center w-1/3">
                                      <div class="text-center mb-2">
                                          <p class="text-[10px] font-black text-slate-700 leading-tight line-clamp-2 px-1">{{ topRankingXI[2].nama }}</p>
                                          <span class="inline-block px-1.5 py-0.5 bg-orange-100 text-orange-700 rounded text-[9px] font-black mt-1">{{ topRankingXI[2].rata_rata }}</span>
                                      </div>
                                      <div class="w-full bg-gradient-to-t from-orange-300/50 to-orange-200/50 rounded-t-xl h-[60px] border-t-4 border-orange-300 flex items-start justify-center pt-2 shadow-inner"><span class="text-xl drop-shadow-md">🥉</span></div>
                                  </div>
                              </div>
                          </template>
                      </div>
                  </div>
                  
                  <!-- TINGKAT XII -->
                  <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 flex flex-col h-full">
                      <div class="flex items-center justify-between mb-4">
                          <span class="px-3 py-1 bg-amber-50 text-amber-700 text-xs font-black rounded-lg border border-amber-100 uppercase tracking-widest">Tingkat XII</span>
                      </div>
                      
                      <!-- Slider Kelas XII -->
                      <div class="flex items-center justify-between bg-slate-50 rounded-xl px-2 py-1.5 border border-slate-200 mb-5">
                          <button @click="slideKelas('XII', -1)" class="p-1 hover:bg-slate-200 rounded-lg text-slate-500 transition-colors">
                              <span class="text-xs font-black">◀</span>
                          </button>
                          <span class="text-[11px] font-black text-slate-700 text-center flex-1 truncate px-2">Kelas {{ currentKelasName('XII') }}</span>
                          <button @click="slideKelas('XII', 1)" class="p-1 hover:bg-slate-200 rounded-lg text-slate-500 transition-colors">
                              <span class="text-xs font-black">▶</span>
                          </button>
                      </div>
                      
                      <!-- Podium Container -->
                      <div class="flex-1 flex flex-col justify-end min-h-[220px]">
                          <template v-if="!selectedKelasXII">
                              <div class="flex items-center justify-center h-full text-slate-300 text-[10px] font-black uppercase tracking-widest text-center px-4">Pilih kelas di atas untuk melihat peringkat</div>
                          </template>
                          <template v-else-if="!topRankingXII || topRankingXII.length === 0">
                              <div class="flex items-center justify-center h-full text-slate-300 text-[10px] font-black uppercase tracking-widest text-center px-4">Belum ada data nilai di kelas ini</div>
                          </template>
                          <template v-else>
                              <div class="flex items-end justify-center gap-2 h-full pt-4">
                                  <div v-if="topRankingXII[1]" class="flex flex-col items-center w-1/3">
                                      <div class="text-center mb-2">
                                          <p class="text-[10px] font-black text-slate-700 leading-tight line-clamp-2 px-1">{{ topRankingXII[1].nama }}</p>
                                          <span class="inline-block px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded text-[9px] font-black mt-1">{{ topRankingXII[1].rata_rata }}</span>
                                      </div>
                                      <div class="w-full bg-gradient-to-t from-slate-300 to-slate-200 rounded-t-xl h-[80px] border-t-4 border-slate-400 flex items-start justify-center pt-2 shadow-inner"><span class="text-2xl drop-shadow-md">🥈</span></div>
                                  </div>
                                  <div v-if="topRankingXII[0]" class="flex flex-col items-center w-1/3 z-10">
                                      <div class="text-center mb-2">
                                          <p class="text-[10px] font-black text-indigo-900 leading-tight line-clamp-2 px-1">{{ topRankingXII[0].nama }}</p>
                                          <span class="inline-block px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded text-[9px] font-black mt-1">{{ topRankingXII[0].rata_rata }}</span>
                                      </div>
                                      <div class="w-full bg-gradient-to-t from-amber-300 to-amber-200 rounded-t-xl h-[120px] border-t-4 border-amber-400 flex items-start justify-center pt-2 shadow-[0_-5px_15px_rgba(251,191,36,0.3)]"><span class="text-3xl drop-shadow-lg">🏆</span></div>
                                  </div>
                                  <div v-if="topRankingXII[2]" class="flex flex-col items-center w-1/3">
                                      <div class="text-center mb-2">
                                          <p class="text-[10px] font-black text-slate-700 leading-tight line-clamp-2 px-1">{{ topRankingXII[2].nama }}</p>
                                          <span class="inline-block px-1.5 py-0.5 bg-orange-100 text-orange-700 rounded text-[9px] font-black mt-1">{{ topRankingXII[2].rata_rata }}</span>
                                      </div>
                                      <div class="w-full bg-gradient-to-t from-orange-300/50 to-orange-200/50 rounded-t-xl h-[60px] border-t-4 border-orange-300 flex items-start justify-center pt-2 shadow-inner"><span class="text-xl drop-shadow-md">🥉</span></div>
                                  </div>
                              </div>
                          </template>
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
import { computed, ref, onMounted, watch } from 'vue'

definePageMeta({
  layout: 'kepsek',
  middleware: 'kepsek',
  title: 'Dashboard Kepsek'
})

const { sekolah, fetchSekolah } = useSekolah()
onMounted(() => {
    fetchSekolah()
})

const userCookie = useCookie('user_profile')
const tokenCookie = useCookie('auth_token')

const userProfile = computed(() => {
  if (!userCookie.value) return null
  return typeof userCookie.value === 'string' ? JSON.parse(userCookie.value) : userCookie.value
})

const { data: response, pending: isLoading } = await useFetch(import.meta.env.VITE_API_BASE_URL + '/api/kepsek/dashboard', {
  headers: { Authorization: `Bearer ${tokenCookie.value}` }
})

const defaultStats = {
  taAktif: '-',
  totalGuru: 0,
  totalSiswa: 0,
  totalKelas: 0,
  kelasPerTingkat: { X: [], XI: [], XII: [] },
  topRankingAll: {}
}

const stats = computed(() => response.value?.data || defaultStats)
const kelasPerTingkat = computed(() => stats.value.kelasPerTingkat)

// State untuk dropdown pilihan kelas di setiap tingkat
const selectedKelasX = ref('')
const selectedKelasXI = ref('')
const selectedKelasXII = ref('')

// Auto-select kelas pertama jika datanya ada
watch(() => kelasPerTingkat.value, (newVal) => {
    if (newVal['X']?.length > 0) selectedKelasX.value = newVal['X'][0].id
    if (newVal['XI']?.length > 0) selectedKelasXI.value = newVal['XI'][0].id
    if (newVal['XII']?.length > 0) selectedKelasXII.value = newVal['XII'][0].id
}, { immediate: true })

const currentKelasName = (tingkat) => {
    const classes = kelasPerTingkat.value[tingkat] || []
    if (classes.length === 0) return 'Kosong'
    const currentId = tingkat === 'X' ? selectedKelasX.value : tingkat === 'XI' ? selectedKelasXI.value : selectedKelasXII.value
    const cls = classes.find(c => c.id === currentId)
    return cls ? cls.nama_kelas : 'Kosong'
}

const slideKelas = (tingkat, direction) => {
    const classes = kelasPerTingkat.value[tingkat] || []
    if (classes.length === 0) return

    const currentId = tingkat === 'X' ? selectedKelasX.value : tingkat === 'XI' ? selectedKelasXI.value : selectedKelasXII.value
    let currentIndex = classes.findIndex(c => c.id === currentId)
    
    if (currentIndex === -1) currentIndex = 0
    
    let nextIndex = currentIndex + direction
    if (nextIndex >= classes.length) nextIndex = 0
    if (nextIndex < 0) nextIndex = classes.length - 1
    
    const nextId = classes[nextIndex].id
    
    if (tingkat === 'X') selectedKelasX.value = nextId
    else if (tingkat === 'XI') selectedKelasXI.value = nextId
    else if (tingkat === 'XII') selectedKelasXII.value = nextId
}

// Computed properties untuk memotong ranking berdasarkan kelas terpilih
const topRankingX = computed(() => stats.value.topRankingAll[selectedKelasX.value] || [])
const topRankingXI = computed(() => stats.value.topRankingAll[selectedKelasXI.value] || [])
const topRankingXII = computed(() => stats.value.topRankingAll[selectedKelasXII.value] || [])

</script>

<style scoped>
.custom-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.custom-scrollbar::-webkit-scrollbar { display: none; }
</style>
