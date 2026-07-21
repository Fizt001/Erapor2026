<template>
  <div class="animate-fadeIn pb-12 mt-4 relative">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
      <div>
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Analisis Belajar</h2>
        <p class="text-slate-500 text-sm font-medium mt-1">Pemetaan kekuatan, kelemahan, dan pencapaian target nilai.</p>
      </div>
      
      <!-- Set Target Button -->
      <button v-if="currentTab && currentTab.tab_id !== 'akumulasi' && currentTab.has_data" 
              @click="openTargetModal"
              class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-lg shadow-sm transition-colors">
        <span>🎯</span> Atur Target Belajar
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex flex-col items-center justify-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-emerald-500 mb-4"></div>
      <p class="text-slate-500 font-medium">Memuat analisis belajar...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="errorMessage" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-10 flex flex-col items-center text-center">
      <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center text-2xl mb-4">
        ⚠️
      </div>
      <h3 class="text-lg font-black text-slate-800 mb-2">Gagal Memuat Data</h3>
      <p class="text-slate-500 text-sm max-w-md mx-auto">{{ errorMessage }}</p>
      <button @click="fetchData" class="mt-6 px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-lg transition-colors shadow-sm">
        Coba Lagi
      </button>
    </div>

    <div v-else-if="tabsData.length > 0">
      
      <!-- Tabs Selection -->
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 mb-6 p-2 overflow-x-auto custom-scrollbar flex gap-2">
        <button v-for="tab in tabsData" :key="tab.tab_id"
                @click="activeTabId = tab.tab_id"
                class="px-5 py-2.5 rounded-lg text-sm font-bold transition-all whitespace-nowrap flex-shrink-0"
                :class="activeTabId === tab.tab_id 
                        ? 'bg-emerald-600 text-white shadow-md' 
                        : 'bg-slate-50 text-slate-500 hover:bg-slate-100'">
          {{ tab.tab_name }}
        </button>
      </div>

      <!-- Empty Tab State -->
      <div v-if="!currentTab.has_data" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-10 flex flex-col items-center text-center">
        <div class="w-16 h-16 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center text-2xl mb-4">
          📭
        </div>
        <h3 class="text-lg font-black text-slate-800 mb-2">Belum Ada Data</h3>
        <p class="text-slate-500 text-sm max-w-md mx-auto">Data analisis belum tersedia atau belum dipublikasikan untuk <strong>{{ currentTab.periode }}</strong>.</p>
      </div>

      <!-- Content -->
      <div v-else class="space-y-6">
        
        <!-- Top Cards Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
          <!-- Radar Chart -->
          <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                🕸️
              </div>
              <div>
                <h3 class="text-sm font-black text-slate-800">Radar Pencapaian</h3>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">{{ currentTab.periode }}</p>
              </div>
            </div>
            
            <div class="flex-1 flex items-center justify-center relative min-h-[250px]">
              <Radar v-if="radarChartData.labels.length > 0" :data="radarChartData" :options="radarChartOptions" />
              <div v-else class="text-sm font-bold text-slate-400">Tidak cukup data untuk grafik</div>
            </div>
          </div>

          <!-- Strengths & Weaknesses -->
          <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Kekuatan -->
            <div class="bg-white rounded-2xl shadow-sm border border-emerald-100 p-6 flex flex-col relative overflow-hidden">
              <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-full -mr-10 -mt-10 blur-2xl pointer-events-none"></div>
              <div class="flex items-center gap-3 mb-6 relative z-10">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl shadow-sm">
                  ⭐
                </div>
                <div>
                  <h3 class="text-sm font-black text-emerald-900">Top Kekuatan</h3>
                  <p class="text-[10px] font-bold text-emerald-600/70 uppercase tracking-wider">3 NILAI TERTINGGI</p>
                </div>
              </div>
              
              <div class="flex-1 flex flex-col gap-3 relative z-10">
                <div v-if="currentTab.kekuatan.length === 0" class="text-sm text-slate-400 font-medium italic mt-4">Belum ada data nilai</div>
                <div v-for="(item, index) in currentTab.kekuatan" :key="'k-' + index" class="bg-emerald-50/50 border border-emerald-100/50 rounded-lg p-3 flex justify-between items-center">
                  <span class="text-[13px] font-bold text-emerald-800 line-clamp-1 pr-3">{{ item.nama_mapel }}</span>
                  <span class="text-sm font-black text-emerald-600 bg-white px-2 py-1 rounded shadow-sm">{{ item.nilai }}</span>
                </div>
              </div>
            </div>

            <!-- Kelemahan -->
            <div class="bg-white rounded-2xl shadow-sm border border-rose-100 p-6 flex flex-col relative overflow-hidden">
              <div class="absolute top-0 right-0 w-32 h-32 bg-rose-50 rounded-full -mr-10 -mt-10 blur-2xl pointer-events-none"></div>
              <div class="flex items-center gap-3 mb-6 relative z-10">
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl shadow-sm">
                  🎯
                </div>
                <div>
                  <h3 class="text-sm font-black text-rose-900">Perlu Ditingkatkan</h3>
                  <p class="text-[10px] font-bold text-rose-600/70 uppercase tracking-wider">3 NILAI TERENDAH</p>
                </div>
              </div>
              
              <div class="flex-1 flex flex-col gap-3 relative z-10">
                <div v-if="currentTab.kelemahan.length === 0" class="text-sm text-slate-400 font-medium italic mt-4">Belum ada data nilai</div>
                <div v-for="(item, index) in currentTab.kelemahan" :key="'w-' + index" class="bg-rose-50/50 border border-rose-100/50 rounded-lg p-3 flex justify-between items-center">
                  <span class="text-[13px] font-bold text-rose-800 line-clamp-1 pr-3">{{ item.nama_mapel }}</span>
                  <span class="text-sm font-black text-rose-600 bg-white px-2 py-1 rounded shadow-sm">{{ item.nilai }}</span>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- Detail Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
          <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <h3 class="text-base font-black text-slate-800">Rincian Pencapaian Belajar</h3>
              <p class="text-xs font-medium text-slate-500 mt-1">Detail perbandingan nilai aktual vs target untuk <strong>{{ currentTab.periode }}</strong>.</p>
            </div>
          </div>
          
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50/50 text-[10px] uppercase tracking-wider text-slate-500 border-b border-slate-200">
                  <th class="px-4 py-3 font-bold w-10 text-center">#</th>
                  <th class="px-4 py-3 font-bold">Mata Pelajaran</th>
                  <th class="px-4 py-3 font-bold text-center">UH/Formatif</th>
                  <th class="px-4 py-3 font-bold text-center">Praktek</th>
                  <th class="px-4 py-3 font-bold text-center">Teori</th>
                  <th class="px-4 py-3 font-bold text-center border-r border-slate-200">Literasi</th>
                  <th class="px-4 py-3 font-black text-indigo-600 text-center bg-indigo-50/50">Target</th>
                  <th class="px-4 py-3 font-black text-emerald-600 text-center bg-emerald-50/50">Nilai Akhir</th>
                  <th class="px-4 py-3 font-bold text-center">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="currentTab.detail.length === 0">
                  <td colspan="9" class="px-4 py-8 text-center text-slate-500 text-sm">Tidak ada data.</td>
                </tr>
                <tr v-for="(item, index) in currentTab.detail" :key="index" class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                  <td class="px-4 py-3 text-sm text-slate-400 font-medium text-center">{{ index + 1 }}</td>
                  <td class="px-4 py-3">
                    <p class="text-[13px] font-bold text-slate-700">{{ item.nama_mapel }}</p>
                    <p class="text-[10px] text-slate-400 font-medium">{{ item.kode_mapel || '-' }}</p>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <span class="text-xs font-bold text-slate-600">{{ calculateRataUH(item) }}</span>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <span class="text-xs font-bold text-slate-600">{{ item.praktek || '-' }}</span>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <span class="text-xs font-bold text-slate-600">{{ item.teori || '-' }}</span>
                  </td>
                  <td class="px-4 py-3 text-center border-r border-slate-100">
                    <span class="text-xs font-bold text-slate-600">{{ item.literasi || '-' }}</span>
                  </td>
                  <td class="px-4 py-3 text-center bg-indigo-50/30">
                    <span class="text-sm font-black text-indigo-600" v-if="item.target_nilai > 0">{{ item.target_nilai }}</span>
                    <span class="text-xs font-medium text-slate-400" v-else>Belum Set</span>
                  </td>
                  <td class="px-4 py-3 text-center bg-emerald-50/30">
                    <span class="text-sm font-black text-emerald-600 bg-emerald-50 px-2 py-1 rounded shadow-sm border border-emerald-100">{{ item.na_value }}</span>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <span v-if="item.target_nilai > 0 && item.na_value >= item.target_nilai" class="text-xs font-bold text-emerald-600 bg-emerald-100 px-2 py-1 rounded-md">Tercapai</span>
                    <span v-else-if="item.target_nilai > 0 && item.na_value < item.target_nilai" class="text-xs font-bold text-rose-600 bg-rose-100 px-2 py-1 rounded-md">Belum</span>
                    <span v-else class="text-xs font-medium text-slate-300">-</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        
      </div>
    </div>

    <!-- Modal Target Belajar -->
    <div v-if="showTargetModal" class="fixed inset-0 z-50 flex items-center justify-center px-4">
      <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeTargetModal"></div>
      
      <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden animate-fadeIn" style="max-height: 90vh; display: flex; flex-direction: column;">
        <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50">
          <div>
            <h3 class="text-lg font-black text-slate-800">Atur Target Belajar</h3>
            <p class="text-xs font-medium text-slate-500">Periode: {{ currentTab.periode }}</p>
          </div>
          <button @click="closeTargetModal" class="text-slate-400 hover:text-rose-500 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
          <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 flex gap-4 mb-6">
            <div class="text-2xl">💡</div>
            <p class="text-sm text-indigo-800 font-medium">Tentukan target nilai yang ingin kamu capai untuk masing-masing mata pelajaran. Target ini akan ditampilkan di grafik radar untuk memacu semangat belajarmu!</p>
          </div>

          <div class="space-y-4">
            <div v-for="(item, index) in targetForm" :key="item.mapel_id" class="flex items-center gap-4 p-3 rounded-lg border border-slate-200 hover:border-indigo-200 hover:bg-indigo-50/30 transition-colors">
              <div class="flex-1">
                <p class="text-sm font-bold text-slate-800">{{ item.nama_mapel }}</p>
              </div>
              <div class="w-32">
                <div class="relative">
                  <input type="number" min="0" max="100" v-model="item.target_nilai" 
                         class="w-full pl-3 pr-8 py-2 bg-white border border-slate-300 rounded-md text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                  <span class="absolute right-3 top-2 text-xs font-bold text-slate-400">/ 100</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex justify-end gap-3">
          <button @click="closeTargetModal" class="px-5 py-2 rounded-lg text-sm font-bold text-slate-600 hover:bg-slate-200 transition-colors">
            Batal
          </button>
          <button @click="saveTarget" :disabled="isSavingTarget" class="px-5 py-2 rounded-lg text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 transition-colors flex items-center gap-2 shadow-sm">
            <span v-if="isSavingTarget" class="animate-spin h-4 w-4 border-2 border-white/30 border-t-white rounded-full"></span>
            {{ isSavingTarget ? 'Menyimpan...' : 'Simpan Target' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <Teleport to="body">
      <div class="fixed bottom-5 right-5 z-[200] flex flex-col gap-2 pointer-events-none">
        <transition-group name="toast">
          <div v-for="toast in toasts" :key="toast.id" 
               class="bg-slate-800 text-white px-5 py-3 rounded-xl shadow-lg flex items-center gap-3 w-80 pointer-events-auto">
            <span v-if="toast.type === 'success'" class="text-emerald-400">✅</span>
            <span v-else-if="toast.type === 'error'" class="text-rose-400">❌</span>
            <p class="text-sm font-medium flex-1">{{ toast.message }}</p>
          </div>
        </transition-group>
      </div>
    </Teleport>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import {
  Chart as ChartJS,
  RadialLinearScale,
  PointElement,
  LineElement,
  Filler,
  Tooltip,
  Legend
} from 'chart.js'
import { Radar } from 'vue-chartjs'

ChartJS.register(
  RadialLinearScale,
  PointElement,
  LineElement,
  Filler,
  Tooltip,
  Legend
)

definePageMeta({
  layout: 'siswa',
  title: 'Analisis Belajar',
  middleware: 'siswa'
})

const isLoading = ref(true)
const errorMessage = ref('')
const tabsData = ref([])
const activeTabId = ref(null)

const showTargetModal = ref(false)
const targetForm = ref([])
const isSavingTarget = ref(false)

const currentTab = computed(() => {
    if (tabsData.value.length === 0) return null;
    return tabsData.value.find(t => t.tab_id === activeTabId.value) || tabsData.value[0];
})

const fetchData = async () => {
    isLoading.value = true
    errorMessage.value = ''
    
    try {
        const tokenCookie = useCookie('auth_token')
        const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/siswa/analisis', {
            headers: {
                'Authorization': `Bearer ${tokenCookie.value}`,
                'Accept': 'application/json'
            }
        })
        
        if (res.success && res.data) {
            tabsData.value = res.data;
            if (tabsData.value.length > 0 && !activeTabId.value) {
                activeTabId.value = tabsData.value[0].tab_id;
            }
        } else {
            errorMessage.value = res.message || 'Gagal mengambil data'
        }
    } catch (error) {
        console.error('Fetch error:', error)
        errorMessage.value = error.response?._data?.message || 'Terjadi kesalahan jaringan atau server.'
    } finally {
        isLoading.value = false
    }
}

const calculateRataUH = (item) => {
    let sum = 0;
    let count = 0;
    if (item.uh1) { sum += item.uh1; count++; }
    if (item.uh2) { sum += item.uh2; count++; }
    if (item.uh3) { sum += item.uh3; count++; }
    if (item.uh4) { sum += item.uh4; count++; }
    if (count === 0) return '-';
    return Math.round(sum / count);
}

const toasts = ref([])

const showToast = (message, type = 'success') => {
    const id = Date.now()
    toasts.value.push({ id, message, type })
    setTimeout(() => {
        toasts.value = toasts.value.filter(t => t.id !== id)
    }, 3000)
}

// Modal logic
const openTargetModal = () => {
    if (!currentTab.value || currentTab.value.tab_id === 'akumulasi') return;
    
    targetForm.value = currentTab.value.detail.map(d => ({
        mapel_id: d.mapel_id,
        nama_mapel: d.nama_mapel,
        target_nilai: d.target_nilai > 0 ? d.target_nilai : 80 // Default to 80 if not set
    }))
    
    showTargetModal.value = true
}

const closeTargetModal = () => {
    showTargetModal.value = false
}

const saveTarget = async () => {
    if (!currentTab.value) return;
    
    isSavingTarget.value = true
    
    try {
        const tokenCookie = useCookie('auth_token')
        const payload = {
            titimangsa_id: currentTab.value.tab_id,
            targets: targetForm.value.map(t => ({
                mapel_id: t.mapel_id,
                target_nilai: parseInt(t.target_nilai) || 0
            }))
        }
        
        const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/siswa/analisis/target', {
            method: 'POST',
            body: payload,
            headers: {
                'Authorization': `Bearer ${tokenCookie.value}`,
                'Accept': 'application/json'
            }
        })
        
        if (res.success) {
            closeTargetModal()
            await fetchData() // Refresh data to show new targets
            showToast('Target belajar berhasil disimpan!', 'success')
        } else {
            showToast(res.message || 'Gagal menyimpan target', 'error')
        }
    } catch (error) {
        console.error('Save error:', error)
        showToast('Terjadi kesalahan jaringan atau server.', 'error')
    } finally {
        isSavingTarget.value = false
    }
}

// Radar Chart reactivity
const radarChartData = computed(() => {
    if (!currentTab.value || !currentTab.value.radar) return { labels: [], datasets: [] };
    
    const radar = currentTab.value.radar;
    const datasets = [
        {
          label: 'Nilai Akhir',
          backgroundColor: 'rgba(79, 70, 229, 0.2)', // Indigo 600 with opacity
          borderColor: '#4f46e5', // Indigo 600
          pointBackgroundColor: '#4f46e5',
          pointBorderColor: '#fff',
          pointHoverBackgroundColor: '#fff',
          pointHoverBorderColor: '#4f46e5',
          data: radar.data,
          order: 1
        }
    ];

    // If targets exist and are greater than 0
    if (radar.target && radar.target.some(t => t > 0)) {
        datasets.push({
          label: 'Target Belajar',
          backgroundColor: 'rgba(16, 185, 129, 0.05)', // Emerald slightly visible
          borderColor: '#10b981', // Emerald 500
          borderDash: [5, 5], // Dashed line
          pointBackgroundColor: '#10b981',
          pointBorderColor: '#fff',
          data: radar.target,
          order: 2
        });
    }

    return {
      labels: radar.labels,
      datasets: datasets
    }
})

const radarChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        r: {
            angleLines: {
                display: true,
                color: 'rgba(0, 0, 0, 0.05)'
            },
            grid: {
                color: 'rgba(0, 0, 0, 0.05)'
            },
            suggestedMin: 0,
            suggestedMax: 100,
            ticks: {
                stepSize: 20,
                backdropColor: 'transparent',
                color: '#94a3b8',
                font: {
                    size: 10,
                    weight: 'bold'
                }
            },
            pointLabels: {
                font: {
                    size: 10,
                    family: "'Inter', sans-serif",
                    weight: 'bold'
                },
                color: '#64748b'
            }
        }
    },
    plugins: {
        legend: {
            display: true,
            position: 'bottom',
            labels: {
                font: { size: 11, family: "'Inter', sans-serif", weight: 'bold' },
                color: '#64748b',
                usePointStyle: true,
                boxWidth: 8
            }
        },
        tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.9)',
            titleFont: { size: 13, family: "'Inter', sans-serif" },
            bodyFont: { size: 12, family: "'Inter', sans-serif" },
            padding: 10,
            cornerRadius: 8,
            displayColors: true,
            callbacks: {
                label: function(context) {
                    return ` ${context.dataset.label}: ${context.raw}`;
                }
            }
        }
    }
}

onMounted(() => {
    fetchData()
})
</script>

<style scoped>
.animate-fadeIn {
  animation: fadeIn 0.3s ease-out forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}
.toast-enter-from {
  opacity: 0;
  transform: translateX(100px);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(100px);
}

.custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 6px; }
.custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #94a3b8; }
</style>
