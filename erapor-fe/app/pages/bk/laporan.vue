<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Grafik Kedisiplinan Siswa</h2>
            <p class="text-sm font-semibold text-slate-500 mt-1">Laporan visual analitik tren pelanggaran kedisiplinan sekolah.</p>
        </div>
        
        <button onclick="window.print()" class="bg-slate-800 hover:bg-black text-white px-5 py-2.5 rounded-xl font-bold shadow-md shadow-slate-300 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2 no-print active:scale-95">
            <span>🖨️</span> Cetak Laporan
        </button>
    </div>

    <!-- MAIN GRID LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mt-6">
        
        <!-- SIDEBAR: Filter Kelas (Col 3) -->
        <div class="lg:col-span-3 space-y-6 lg:sticky lg:top-6 no-print">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
                <div class="p-6 bg-gradient-to-r from-rose-900 to-rose-800 border-b border-rose-100 flex items-center gap-4">
                    <span class="text-3xl">🏫</span>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-white">Filter Kelas</h3>
                    </div>
                </div>
                
                <!-- Opsi Semua Kelas -->
                <button @click="selectKelas(null)"
                    class="w-full text-left px-5 py-4 border-b border-slate-100 hover:bg-rose-50 transition-colors flex items-center justify-between group"
                    :class="selectedKelasId === null ? 'bg-rose-50 border-l-4 border-l-rose-500' : 'bg-white border-l-4 border-l-transparent'">
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 group-hover:text-rose-700 transition-colors">Semua Kelas (Global)</h4>
                    </div>
                    <span v-if="selectedKelasId === null" class="text-rose-500">▶</span>
                </button>

                <div class="p-4 bg-slate-50 border-b border-slate-100">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
                        <input type="text" v-model="searchKelas" placeholder="Cari kelas..." class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all text-xs font-bold outline-none text-slate-700">
                    </div>
                </div>

                <div class="max-h-[50vh] overflow-y-auto custom-scrollbar">
                    <button v-for="kelas in filteredKelases" :key="kelas.id" @click="selectKelas(kelas.id)"
                        class="w-full text-left px-5 py-4 border-b border-slate-100 hover:bg-rose-50 transition-colors flex items-center justify-between group"
                        :class="selectedKelasId === kelas.id ? 'bg-rose-50 border-l-4 border-l-rose-500' : 'bg-white border-l-4 border-l-transparent'">
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 group-hover:text-rose-700 transition-colors">{{ kelas.nama_kelas }}</h4>
                        </div>
                        <span v-if="selectedKelasId === kelas.id" class="text-rose-500">▶</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Charts Area (Col 9) -->
        <div class="lg:col-span-9 w-full">
            <div v-if="isLoading" class="bg-white rounded-3xl p-16 flex flex-col items-center justify-center border border-slate-200/60 shadow-sm opacity-60">
                <div class="w-8 h-8 border-4 border-rose-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Statistik...</span>
            </div>

            <div v-else class="space-y-8">
                <!-- CHART PELANGGARAN -->
                <div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center gap-2"><span class="text-xl">⚠️</span> Statistik Pelanggaran</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Bar Chart (Tren Bulanan) -->
                        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200 shadow-sm print:shadow-none print:border-slate-300 relative h-96">
                            <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-6 border-b border-slate-100 pb-2">Tren Kasus {{ getSelectedKelasName() }}</h3>
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
                            <h3 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-6 border-b border-slate-100 pb-2">Tren Ketidakhadiran {{ getSelectedKelasName() }}</h3>
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

const isLoading = ref(true)
const kelases = ref([])
const selectedKelasId = ref(null)
const searchKelas = ref('')
const stats = ref({
    monthly: [],
    categories: [0, 0, 0],
    absensi_monthly: [],
    absensi_categories: [0, 0, 0]
})

const filteredKelases = computed(() => {
    if (!searchKelas.value) return kelases.value
    const ls = searchKelas.value.toLowerCase()
    return kelases.value.filter(k => k.nama_kelas.toLowerCase().includes(ls))
})

const getSelectedKelasName = () => {
    if (selectedKelasId.value === null) return 'Global (Semua Kelas)'
    const kelas = kelases.value.find(k => k.id === selectedKelasId.value)
    return kelas ? kelas.nama_kelas : ''
}

const totalKasus = computed(() => {
    return (stats.value.categories || []).reduce((a, b) => a + b, 0)
})

const totalAbsensi = computed(() => {
    return (stats.value.absensi_categories || []).reduce((a, b) => a + b, 0)
})

const selectKelas = (id) => {
    selectedKelasId.value = id
    fetchStats()
}

const fetchStats = async () => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        let url = 'http://localhost:8000/api/bk/laporan'
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
        }
    } catch (error) {
        console.error('Fetch error:', error)
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
    fetchStats()
})
</script>

<style scoped>
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.animate-fadeIn { animation: fadeIn 0.4s ease-out forwards; }

@media print {
    .no-print { display: none !important; }
    body { background-color: white !important; }
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
}
</style>
