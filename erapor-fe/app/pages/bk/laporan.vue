<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50 relative animate-fadeIn">
    
    <!-- Mobile Tabs -->
    <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-2 grid grid-cols-2 gap-2 z-20 no-print">
      <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTabMobile = tab.id"
        :class="activeTabMobile === tab.id ? 'bg-gradient-to-br from-rose-500 to-rose-600 text-white shadow-md shadow-rose-500/20 ring-2 ring-rose-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
        class="rounded-xl flex flex-col items-center justify-center py-2 px-1 transition-all active:scale-95">
        <span class="text-lg mb-0.5 transition-transform" :class="activeTabMobile === tab.id ? 'scale-110' : ''">{{ tab.icon }}</span>
        <span class="text-[9px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
      </button>
    </div>

    <div class="flex-1 flex overflow-hidden relative">
      <!-- Panel Dock Kiri (Filter Kelas) -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all no-print', activeTabMobile === 'dock' || isDesktop ? 'flex' : 'hidden xl:flex', !isDesktop ? 'pt-[60px]' : '']">
        
        <div class="p-6 shrink-0 no-print">
          <div class="bg-gradient-to-r from-rose-600 to-rose-700 rounded-2xl p-5 border border-rose-500 shadow-sm relative overflow-hidden flex items-center gap-4">
            <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0 relative z-10">🏫</div>
            <div class="relative z-10">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">Filter Kelas</h3>
                <p class="text-[10px] text-rose-100 font-semibold uppercase mt-0.5">Laporan Kedisiplinan</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-20 text-white">
              <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"></path></svg>
            </div>
          </div>
        </div>

        <div class="px-6 pb-6 bg-white shrink-0 no-print flex flex-col gap-4">

        <!-- Opsi Semua Kelas -->
        <button @click="selectKelas(null)"
            class="w-full text-left px-5 py-4 border-b border-slate-100 hover:bg-rose-50 transition-colors flex items-center justify-between group shrink-0"
            :class="selectedKelasId === null ? 'bg-rose-50 border-l-4 border-l-rose-500' : 'bg-white border-l-4 border-l-transparent'">
            <div>
                <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 group-hover:text-rose-700 transition-colors">Semua Kelas (Global)</h4>
                <p class="text-[9px] font-bold text-slate-400 uppercase mt-1">Seluruh Siswa</p>
            </div>
            <span v-if="selectedKelasId === null" class="text-rose-500 text-lg">▶</span>
        </button>

            <!-- Cari Kelas -->
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Cari Kelas</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
                    <input type="text" v-model="searchKelas" placeholder="Cari nama kelas..." class="w-full pl-9 pr-3 py-2.5 bg-white border-2 border-slate-200/70 rounded-xl focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all text-xs font-bold outline-none text-slate-700">
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar bg-white">
            <div v-if="isLoading && kelases.length === 0" class="p-8 text-center flex flex-col items-center justify-center h-full opacity-60">
                <div class="w-8 h-8 border-4 border-rose-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Kelas...</span>
            </div>
            <div v-else>
                <button v-for="kelas in filteredKelases" :key="kelas.id" @click="selectKelas(kelas.id)"
                    class="w-full text-left px-5 py-4 border-b border-slate-100 hover:bg-rose-50 transition-colors flex items-center justify-between group"
                    :class="selectedKelasId === kelas.id ? 'bg-rose-50 border-l-4 border-l-rose-500' : 'bg-white border-l-4 border-l-transparent'">
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-wider transition-colors" :class="selectedKelasId === kelas.id ? 'text-rose-700' : 'text-slate-700 group-hover:text-rose-700'">
                            {{ kelas.tingkat }} {{ kelas.nama_kelas }}
                        </h4>
                        <p class="text-[9px] font-bold text-slate-400 uppercase mt-1">{{ kelas.siswas_count || 0 }} Siswa Aktif</p>
                    </div>
                    <span v-if="selectedKelasId === kelas.id" class="text-rose-500 text-lg">▶</span>
                </button>
                <div v-if="filteredKelases.length === 0" class="p-8 text-center text-slate-400 text-xs font-bold">
                    Tidak ada kelas ditemukan.
                </div>
            </div>
        </div>
      </div>

      <!-- Panel Flow Kanan (Charts Area) -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative print:block print:bg-white', activeTabMobile === 'flow' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[60px]' : '']">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0 print:p-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0 print:shadow-none print:border-none">
            <!-- Header Flow -->
            <div class="px-6 py-5 bg-white border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shrink-0 z-10 shadow-sm print:hidden">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-500 hidden sm:flex text-xl">📊</div>
                <div>
                    <div class="flex items-center gap-3">
                        <h3 class="text-sm font-black uppercase tracking-widest text-slate-700">Grafik Kedisiplinan</h3>
                        <span v-if="activeTahunAjaran" class="bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-md text-[10px] font-black tracking-widest border border-indigo-100 uppercase no-print">
                            TA. {{ activeTahunAjaran }}
                        </span>
                    </div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">
                        Visual analitik - {{ getSelectedKelasName() }}
                    </p>
                </div>
            </div>
            <button onclick="window.print()" class="bg-slate-800 hover:bg-black text-white px-5 py-2.5 rounded-xl font-bold shadow-md shadow-slate-300 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2 no-print active:scale-95">
                <span>🖨️</span> Cetak Laporan
            </button>
        </div>

        <div class="flex-1 overflow-auto bg-slate-50 p-4 sm:p-6 custom-scrollbar print:p-0 print:bg-white">
            <div v-if="isLoading" class="bg-white rounded-3xl p-16 flex flex-col items-center justify-center border border-slate-200/60 shadow-sm opacity-60 h-full max-h-[400px]">
                <div class="w-8 h-8 border-4 border-rose-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Statistik...</span>
            </div>

            <div v-else class="space-y-8 pb-8">
                <!-- Header Print -->
                <div class="hidden print:block mb-8 text-center border-b border-slate-800 pb-4">
                    <h2 class="text-2xl font-black text-slate-800 uppercase">Laporan Kedisiplinan & Absensi</h2>
                    <p class="text-sm font-bold text-slate-600 mt-1 uppercase">{{ getSelectedKelasName() }}</p>
                </div>

                <!-- CHART PELANGGARAN -->
                <div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center gap-2"><span class="text-xl">⚠️</span> Statistik Pelanggaran</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Bar Chart (Tren Bulanan) -->
                        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200 shadow-sm print:shadow-none print:border-slate-300 relative h-96">
                            <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-6 border-b border-slate-100 pb-2">Tren Kasus Bulanan</h3>
                            <div class="absolute inset-0 top-16 p-6">
                                <Bar v-if="barChartData.datasets" :data="barChartData" :options="barOptions" />
                            </div>
                        </div>

                        <!-- Doughnut Chart (Proporsi Kategori) -->
                        <div class="lg:col-span-1 bg-white rounded-2xl p-6 border border-slate-200 shadow-sm print:shadow-none print:border-slate-300 relative h-96 flex flex-col">
                            <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-6 border-b border-slate-100 pb-2">Proporsi Kasus</h3>
                            <div class="flex-grow flex items-center justify-center relative">
                                <div v-if="totalKasus === 0" class="text-center text-slate-400 font-bold uppercase tracking-widest text-[10px]">Belum ada kasus</div>
                                <Doughnut v-else v-if="doughnutChartData.datasets" :data="doughnutChartData" :options="doughnutOptions" />
                            </div>
                            
                            <!-- Legend Custom -->
                            <div class="mt-4 grid grid-cols-3 gap-2 border-t border-slate-100 pt-4">
                                <div class="text-center">
                                    <span class="block w-3 h-3 rounded-full bg-amber-400 mx-auto mb-1"></span>
                                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Ringan</span>
                                    <span class="block text-sm font-black text-slate-800">{{ stats.categories[0] }}</span>
                                </div>
                                <div class="text-center border-x border-slate-100">
                                    <span class="block w-3 h-3 rounded-full bg-orange-500 mx-auto mb-1"></span>
                                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Sedang</span>
                                    <span class="block text-sm font-black text-slate-800">{{ stats.categories[1] }}</span>
                                </div>
                                <div class="text-center">
                                    <span class="block w-3 h-3 rounded-full bg-rose-600 mx-auto mb-1"></span>
                                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Berat</span>
                                    <span class="block text-sm font-black text-slate-800">{{ stats.categories[2] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CHART ABSENSI -->
                <div class="border-t border-slate-200 pt-8 print:pt-4">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center gap-2"><span class="text-xl">📅</span> Statistik Ketidakhadiran (S, I, A)</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Bar Chart (Tren Bulanan Absensi) -->
                        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200 shadow-sm print:shadow-none print:border-slate-300 relative h-96">
                            <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-6 border-b border-slate-100 pb-2">Tren Ketidakhadiran Bulanan</h3>
                            <div class="absolute inset-0 top-16 p-6">
                                <Bar v-if="barAbsensiChartData.datasets" :data="barAbsensiChartData" :options="barOptions" />
                            </div>
                        </div>

                        <!-- Doughnut Chart (Proporsi Absensi) -->
                        <div class="lg:col-span-1 bg-white rounded-2xl p-6 border border-slate-200 shadow-sm print:shadow-none print:border-slate-300 relative h-96 flex flex-col">
                            <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-6 border-b border-slate-100 pb-2">Proporsi Ketidakhadiran</h3>
                            <div class="flex-grow flex items-center justify-center relative">
                                <div v-if="totalAbsensi === 0" class="text-center text-slate-400 font-bold uppercase tracking-widest text-[10px]">Belum ada data absensi</div>
                                <Doughnut v-else v-if="doughnutAbsensiChartData.datasets" :data="doughnutAbsensiChartData" :options="doughnutOptions" />
                            </div>
                            
                            <!-- Legend Custom -->
                            <div class="mt-4 grid grid-cols-3 gap-2 border-t border-slate-100 pt-4">
                                <div class="text-center">
                                    <span class="block w-3 h-3 rounded-full bg-amber-400 mx-auto mb-1"></span>
                                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Sakit</span>
                                    <span class="block text-sm font-black text-slate-800">{{ stats.absensi_categories[0] }}</span>
                                </div>
                                <div class="text-center border-x border-slate-100">
                                    <span class="block w-3 h-3 rounded-full bg-sky-500 mx-auto mb-1"></span>
                                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Izin</span>
                                    <span class="block text-sm font-black text-slate-800">{{ stats.absensi_categories[1] }}</span>
                                </div>
                                <div class="text-center">
                                    <span class="block w-3 h-3 rounded-full bg-rose-600 mx-auto mb-1"></span>
                                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Alpa</span>
                                    <span class="block text-sm font-black text-slate-800">{{ stats.absensi_categories[2] }}</span>
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
import { ref, onMounted, computed } from 'vue'
import { Bar, Doughnut } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement)

definePageMeta({
  layout: 'bk',
  middleware: 'bk',
  title: 'Laporan Kedisiplinan'
})

// UI State
const windowWidth = ref(1024)
const isDesktop = computed(() => windowWidth.value >= 1024) // lg breakpoint
const activeTabMobile = ref('dock')
const mobileTabs = [
  { id: 'dock', title: 'Filter', icon: '🏫' },
  { id: 'flow', title: 'Grafik', icon: '📊' }
]

const isLoading = ref(true)
const kelases = ref([])
const selectedKelasId = ref(null)
const searchKelas = ref('')
const activeTahunAjaran = ref('')
const stats = ref({
    monthly: [],
    categories: [0, 0, 0],
    absensi_monthly: [],
    absensi_categories: [0, 0, 0]
})

const filteredKelases = computed(() => {
    if (!searchKelas.value) return kelases.value
    const ls = searchKelas.value.toLowerCase()
    return kelases.value.filter(k => 
        k.nama_kelas.toLowerCase().includes(ls) || 
        (k.tingkat + ' ' + k.nama_kelas).toLowerCase().includes(ls)
    )
})

const getSelectedKelasName = () => {
    if (selectedKelasId.value === null) return 'Global (Semua Kelas)'
    const kelas = kelases.value.find(k => k.id === selectedKelasId.value)
    return kelas ? `${kelas.tingkat} ${kelas.nama_kelas}` : ''
}

const totalKasus = computed(() => {
    return (stats.value.categories || []).reduce((a, b) => a + b, 0)
})

const totalAbsensi = computed(() => {
    return (stats.value.absensi_categories || []).reduce((a, b) => a + b, 0)
})

const selectKelas = (id) => {
    selectedKelasId.value = id
    if (!isDesktop.value) activeTabMobile.value = 'flow'
    fetchStats()
}

const fetchStats = async () => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        let url = import.meta.env.VITE_API_BASE_URL + '/api/bk/laporan'
        if (selectedKelasId.value) {
            url += `?kelas_id=${selectedKelasId.value}`
        }
        const response = await $fetch(url, {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            stats.value = response.data
            if (response.data.kelases && kelases.value.length === 0) {
                kelases.value = response.data.kelases
            }
            if (response.data.tahun_aktif) {
                activeTahunAjaran.value = response.data.tahun_aktif?.tahun ?? '2024/2025'
            }
        }
    } catch (error) {
        console.error('Fetch error:', error)
        useSwal().toast('Gagal memuat statistik', 'error')
    } finally {
        isLoading.value = false
    }
}

// ========================
// CHART CONFIGURATIONS
// ========================

const barChartData = computed(() => ({
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
    datasets: [
        {
            label: 'Jumlah Kasus/Pelanggaran',
            backgroundColor: '#e11d48', // rose-600
            borderRadius: 6,
            data: stats.value.monthly || [0,0,0,0,0,0,0,0,0,0,0,0]
        }
    ]
}))

const barOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false }
    },
    scales: {
        y: { 
            beginAtZero: true, 
            ticks: { precision: 0, font: { size: 10, weight: 'bold' } },
            grid: { color: '#f1f5f9' }
        },
        x: {
            ticks: { font: { size: 10, weight: 'bold' } },
            grid: { display: false }
        }
    }
}

const doughnutChartData = computed(() => ({
    labels: ['Ringan', 'Sedang', 'Berat'],
    datasets: [
        {
            backgroundColor: ['#fbbf24', '#f97316', '#e11d48'], // amber-400, orange-500, rose-600
            borderWidth: 0,
            data: stats.value.categories || [0,0,0]
        }
    ]
}))

const doughnutOptions = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '70%',
    plugins: {
        legend: { display: false } // We use custom html legend
    }
}

// Chart Absensi
const barAbsensiChartData = computed(() => ({
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
    datasets: [
        {
            label: 'Total Ketidakhadiran (S, I, A)',
            backgroundColor: '#0ea5e9', // sky-500
            borderRadius: 6,
            data: stats.value.absensi_monthly || [0,0,0,0,0,0,0,0,0,0,0,0]
        }
    ]
}))

const doughnutAbsensiChartData = computed(() => ({
    labels: ['Sakit', 'Izin', 'Alpa'],
    datasets: [
        {
            backgroundColor: ['#fbbf24', '#0ea5e9', '#e11d48'], // amber-400 (S), sky-500 (I), rose-600 (A)
            borderWidth: 0,
            data: stats.value.absensi_categories || [0,0,0]
        }
    ]
}))

onMounted(() => {
    windowWidth.value = window.innerWidth
    window.addEventListener('resize', () => { windowWidth.value = window.innerWidth })
    fetchStats()
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.animate-fadeIn { animation: fadeIn 0.4s ease-out forwards; }

@media print {
    .no-print { display: none !important; }
    body { background-color: white !important; }
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
}
</style>
