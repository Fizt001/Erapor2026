<template>
  <div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Monitoring Nilai Kelas</h1>
        <p class="text-slate-500 text-sm mt-1">
          Pantau progres pengisian nilai Formatif dan Sumatif oleh guru mata pelajaran.
        </p>
      </div>
      
      <!-- Summary Chips -->
      <div v-if="!pending && !error && totalMapel > 0" class="flex gap-3">
        <div class="bg-emerald-50 border border-emerald-100 px-4 py-2 rounded-xl flex flex-col shadow-sm">
          <span class="text-[10px] uppercase font-bold text-emerald-600 tracking-wider">Formatif Selesai</span>
          <span class="text-xl font-black text-emerald-700">{{ formatifCompletedCount }} / {{ totalMapel }}</span>
        </div>
        <div class="bg-blue-50 border border-blue-100 px-4 py-2 rounded-xl flex flex-col shadow-sm">
          <span class="text-[10px] uppercase font-bold text-blue-600 tracking-wider">Sumatif Selesai</span>
          <span class="text-xl font-black text-blue-700">{{ sumatifCompletedCount }} / {{ totalMapel }}</span>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="pending" class="flex flex-col items-center justify-center py-20 bg-white rounded-2xl shadow-sm border border-slate-100">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-amber-500 mb-4"></div>
      <p class="text-slate-500 font-medium">Memuat progres nilai...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 p-6 rounded-2xl border border-red-100 flex items-start">
      <div class="text-red-500 text-xl mr-4 mt-0.5">⚠️</div>
      <div>
        <h3 class="text-red-800 font-bold mb-1">Gagal Memuat Data</h3>
        <p class="text-red-600 text-sm">{{ error.message || 'Terjadi kesalahan pada server.' }}</p>
        <button @click="refreshData" class="mt-3 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 text-sm font-medium rounded-lg transition-colors">
          Coba Lagi
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-[13px] uppercase tracking-wider">
              <th class="p-4 font-bold w-12 text-center">No</th>
              <th class="p-4 font-bold">Mata Pelajaran</th>
              <th class="p-4 font-bold">Guru Pengampu</th>
              <th class="p-4 font-bold min-w-[200px]">Progres Formatif</th>
              <th class="p-4 font-bold min-w-[200px]">Progres Sumatif</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-sm">
            <tr v-for="(item, index) in mapelList" :key="item.mapel_id" class="hover:bg-slate-50 transition-colors">
              <td class="p-4 text-center text-slate-400 font-medium">{{ index + 1 }}</td>
              <td class="p-4 font-bold text-slate-800">{{ item.nama_mapel }}</td>
              <td class="p-4 text-slate-600">
                <div class="flex items-center">
                  <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-xs font-bold mr-2 uppercase">
                    {{ item.guru_pengampu.charAt(0) }}
                  </div>
                  {{ item.guru_pengampu }}
                </div>
              </td>
              
              <!-- Progress Formatif -->
              <td class="p-4">
                <div class="flex justify-between text-xs mb-1">
                  <span :class="item.status_formatif ? 'text-emerald-600 font-bold' : 'text-slate-500'">
                    {{ item.status_formatif ? 'Selesai' : 'Belum Selesai' }}
                  </span>
                  <span class="font-medium text-slate-700">{{ item.formatif_terisi }} / {{ item.total_siswa }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2.5">
                  <div class="h-2.5 rounded-full transition-all duration-500" 
                    :class="item.status_formatif ? 'bg-emerald-500' : (item.formatif_terisi > 0 ? 'bg-amber-400' : 'bg-slate-300')"
                    :style="{ width: getPercentage(item.formatif_terisi, item.total_siswa) + '%' }">
                  </div>
                </div>
              </td>

              <!-- Progress Sumatif -->
              <td class="p-4">
                <div class="flex justify-between text-xs mb-1">
                  <span :class="item.status_sumatif ? 'text-blue-600 font-bold' : 'text-slate-500'">
                    {{ item.status_sumatif ? 'Selesai' : 'Belum Selesai' }}
                  </span>
                  <span class="font-medium text-slate-700">{{ item.sumatif_terisi }} / {{ item.total_siswa }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2.5">
                  <div class="h-2.5 rounded-full transition-all duration-500" 
                    :class="item.status_sumatif ? 'bg-blue-500' : (item.sumatif_terisi > 0 ? 'bg-amber-400' : 'bg-slate-300')"
                    :style="{ width: getPercentage(item.sumatif_terisi, item.total_siswa) + '%' }">
                  </div>
                </div>
              </td>
            </tr>
            <tr v-if="mapelList.length === 0">
              <td colspan="5" class="p-8 text-center text-slate-500">
                Belum ada data mata pelajaran yang di-plotting ke kelas ini.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

definePageMeta({
  layout: 'guru',
  middleware: 'guru',
  title: 'Monitoring Nilai'
})

const token = useCookie('auth_token')
const pending = ref(true)
const error = ref(null)
const mapelList = ref([])

const totalMapel = computed(() => mapelList.value.length)
const formatifCompletedCount = computed(() => mapelList.value.filter(m => m.status_formatif).length)
const sumatifCompletedCount = computed(() => mapelList.value.filter(m => m.status_sumatif).length)

const getPercentage = (filled, total) => {
  if (!total || total === 0) return 0;
  const raw = (filled / total) * 100;
  return Math.min(Math.round(raw), 100);
}

const fetchData = async () => {
  pending.value = true
  error.value = null
  try {
    const res = await $fetch('http://localhost:8000/api/guru/walas/monitoring', {
      headers: { Authorization: `Bearer ${token.value}` }
    })
    
    if (res.success) {
      mapelList.value = res.data
    } else {
      throw new Error(res.message || 'Gagal memuat data')
    }
  } catch (err) {
    error.value = err
  } finally {
    pending.value = false
  }
}

const refreshData = () => {
  fetchData()
}

onMounted(() => {
  fetchData()
})
</script>
