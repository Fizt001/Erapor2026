<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- Panel Dock Kiri (Pilih Hari) -->
      <div class="w-[280px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)]">
        <div class="p-6 pb-4 border-b border-slate-100 shrink-0">
            <h1 class="text-xl font-black text-slate-800 leading-tight">Jadwal Mengajar</h1>
            <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mt-1">Konfigurasi Matriks</p>
        </div>
        <div class="p-4 flex-1 overflow-y-auto custom-scrollbar">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-2">Pilih Hari</h3>
            <div class="space-y-2">
                <button v-for="hari in days" :key="hari" @click="changeDay(hari)"
                    :class="activeDay === hari ? 'bg-indigo-50 border-indigo-200 text-indigo-700 shadow-sm ring-1 ring-indigo-500/20' : 'bg-white border-transparent text-slate-600 hover:bg-slate-50'"
                    class="w-full text-left px-4 py-3 rounded-xl border font-bold text-sm flex items-center justify-between transition-all">
                    <div class="flex items-center gap-3">
                        <AppIcon name="calendar" class="w-5 h-5" :class="activeDay === hari ? 'text-indigo-500' : 'text-slate-400'"/>
                        {{ hari }}
                    </div>
                    <span v-if="activeDay === hari" class="w-2 h-2 rounded-full bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.6)]"></span>
                </button>
            </div>
        </div>
      </div>

      <!-- Panel Flow Kanan (Matrix Table) -->
      <div class="flex-1 flex-shrink-0 bg-slate-50 flex flex-col h-full min-w-0 overflow-hidden relative">
        
        <!-- Header Actions -->
        <div class="p-4 lg:px-8 lg:py-5 bg-white border-b border-slate-200 flex items-center justify-between shrink-0 shadow-sm z-10 relative">
            <div>
                <h2 class="text-lg font-black text-slate-800">Jadwal Hari {{ activeDay }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">Isi jadwal per jam pelajaran (JP) untuk setiap kelas.</p>
            </div>
            <button 
                @click="saveJadwal"
                :disabled="isSaving"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <span v-if="isSaving">Menyimpan...</span>
                <template v-else>
                <AppIcon name="save" class="w-5 h-5" />
                Simpan Jadwal
                </template>
            </button>
        </div>

        <!-- Scrollable Area -->
        <div class="flex-1 overflow-auto custom-scrollbar p-4 lg:p-8">
            
            <!-- Loading State -->
            <div v-if="isLoadingOptions || isLoadingJadwal" class="flex justify-center items-center py-20 bg-white rounded-2xl border border-slate-200 shadow-sm">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
            </div>

            <!-- Empty State for No Classes -->
            <div v-if="!hasAnyKelas" class="flex flex-col items-center justify-center py-20 bg-white rounded-2xl border border-dashed border-slate-300">
                <span class="text-4xl mb-4">🏫</span>
                <h3 class="text-lg font-black text-slate-700">Belum Ada Kelas Aktif</h3>
                <p class="text-sm text-slate-500 mt-1 max-w-md text-center">Silakan buat dan aktifkan kelas di menu Master Kelas terlebih dahulu agar matriks jadwal dapat ditampilkan.</p>
            </div>

            <!-- Matrix Table -->
            <div v-else class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar max-h-[calc(100vh-180px)]">
                    <table class="w-full text-xs text-left whitespace-nowrap border-collapse">
                        <thead class="bg-slate-50 text-slate-700 font-black sticky top-0 z-20 shadow-sm">
                            <!-- Row Header 1: Tingkat -->
                            <tr>
                                <th rowspan="2" class="p-3 text-center border-r border-b border-slate-200 bg-slate-100 sticky left-0 z-30 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] w-12">JP</th>
                                <template v-for="(classes, tingkat) in filteredKelasPerTingkat" :key="tingkat">
                                    <th :colspan="classes.length" class="p-2 text-center border-r border-b border-slate-200 uppercase tracking-widest text-indigo-700 bg-indigo-50/50">
                                        Tingkat {{ tingkat }}
                                    </th>
                                </template>
                            </tr>
                            <!-- Row Header 2: Kelas -->
                            <tr>
                                <template v-for="(classes, tingkat) in filteredKelasPerTingkat" :key="'sub-'+tingkat">
                                    <th v-for="cls in classes" :key="cls.id" class="p-2 text-center border-r border-b border-slate-200 min-w-[220px]">
                                        Kelas {{ cls.nama_kelas }}
                                    </th>
                                </template>
                            </tr>
                        </thead>
                        
                        <tbody class="text-slate-600">
                            <tr v-for="jp in 12" :key="jp" class="hover:bg-indigo-50/30 transition-colors">
                                <td class="p-3 text-center font-black border-r border-b border-slate-200 bg-slate-50 sticky left-0 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] text-slate-400">
                                    {{ jp }}
                                </td>
                                <template v-for="(classes, tingkat) in filteredKelasPerTingkat" :key="'col-'+tingkat">
                                    <td v-for="cls in classes" :key="cls.id" class="p-1 border-r border-b border-slate-200">
                                        <select 
                                            v-model="formJadwal[`${jp}_${cls.id}`]" 
                                            class="w-full text-[11px] p-1.5 border border-transparent rounded bg-transparent hover:bg-white hover:border-slate-300 hover:shadow-sm focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-all cursor-pointer">
                                            <option value="">- Kosong -</option>
                                            <option v-for="opt in getAvailableOptions(jp, cls.id)" :key="opt.value" :value="opt.value" :title="opt.label">
                                                {{ opt.label }}
                                            </option>
                                        </select>
                                    </td>
                                </template>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

definePageMeta({
  layout: 'kurikulum',
  middleware: 'kurikulum',
  title: 'Jadwal Mengajar'
})

const tokenCookie = useCookie('auth_token')
const { $toast } = useNuxtApp()

const days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']
const activeDay = ref('Senin')

const isLoadingOptions = ref(true)
const isLoadingJadwal = ref(false)
const isSaving = ref(false)

const kelasPerTingkat = ref({ '10': [], '11': [], '12': [] })
const mapelOptions = ref([])
const formJadwal = ref({})

// Computed properties to handle empty levels safely
const filteredKelasPerTingkat = computed(() => {
  const result = {}
  for (const [tingkat, classes] of Object.entries(kelasPerTingkat.value)) {
    if (classes && classes.length > 0) {
      result[tingkat] = classes
    }
  }
  return result
})

const hasAnyKelas = computed(() => {
  return Object.keys(filteredKelasPerTingkat.value).length > 0
})

const getAvailableOptions = (jp, kelasId) => {
  // Cari ID guru yang sudah dipakai di Jam Pelajaran ini (selain di kelas ini sendiri)
  const selectedGuruIdsInRow = new Set()
  
  for (const [key, val] of Object.entries(formJadwal.value)) {
    if (!val) continue
    const [k_jp, k_kelasId] = key.split('_')
    
    if (k_jp === jp.toString() && k_kelasId !== kelasId.toString()) {
      const [mapelId, guruId] = val.split('_')
      // Jika ada guru pengampu, tandai sebagai terpakai
      if (guruId && guruId !== 'null') {
        selectedGuruIdsInRow.add(guruId)
      }
    }
  }

  // Filter mapelOptions: buang yang gurunya sudah mengajar di kelas lain pada JP ini
  return mapelOptions.value.filter(opt => {
    if (!opt.guru_id) return true // Mapel tanpa guru bebas dipilih (misal untuk placeholder)
    return !selectedGuruIdsInRow.has(opt.guru_id.toString())
  })
}

const fetchOptions = async () => {
  isLoadingOptions.value = true
  try {
    const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/kurikulum/jadwal-mengajar/options', {
      headers: { Authorization: `Bearer ${tokenCookie.value}` }
    })
    if (res.success) {
      kelasPerTingkat.value = res.data.kelasPerTingkat
      mapelOptions.value = res.data.mapelOptions
    }
  } catch (error) {
    console.error('Failed to load options', error)
    $toast.error('Gagal memuat opsi kelas & mapel')
  } finally {
    isLoadingOptions.value = false
  }
}

const fetchJadwal = async (hari) => {
  isLoadingJadwal.value = true
  formJadwal.value = {}
  
  try {
    const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/kurikulum/jadwal-mengajar?hari=${hari}`, {
      headers: { Authorization: `Bearer ${tokenCookie.value}` }
    })
    if (res.success) {
      formJadwal.value = res.data
    }
  } catch (error) {
    console.error('Failed to load schedule', error)
    $toast.error(`Gagal memuat jadwal hari ${hari}`)
  } finally {
    isLoadingJadwal.value = false
  }
}

const changeDay = (hari) => {
  activeDay.value = hari
  fetchJadwal(hari)
}

const saveJadwal = async () => {
  isSaving.value = true
  
  const payloadJadwals = []
  
  for (const [key, val] of Object.entries(formJadwal.value)) {
    if (!val) continue
    
    const [jamKe, kelasId] = key.split('_')
    const [mapelId, guruId] = val.split('_')
    
    payloadJadwals.push({
      jam_ke: parseInt(jamKe),
      kelas_id: parseInt(kelasId),
      mapel_id: parseInt(mapelId),
      guru_id: guruId === 'null' ? null : parseInt(guruId)
    })
  }

  try {
    const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/kurikulum/jadwal-mengajar', {
      method: 'POST',
      headers: { Authorization: `Bearer ${tokenCookie.value}` },
      body: {
        hari: activeDay.value,
        jadwals: payloadJadwals
      }
    })
    
    if (res.success) {
      $toast.success(res.message || 'Jadwal berhasil disimpan')
    }
  } catch (error) {
    console.error('Failed to save schedule', error)
    $toast.error('Gagal menyimpan jadwal')
  } finally {
    isSaving.value = false
  }
}

onMounted(() => {
  fetchOptions().then(() => {
    fetchJadwal(activeDay.value)
  })
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  height: 8px;
  width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1; 
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8; 
}
</style>
