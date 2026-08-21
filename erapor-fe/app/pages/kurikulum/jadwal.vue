<template>
  <div class="p-6 max-w-[1600px] mx-auto">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-black text-slate-800">Jadwal Mengajar</h1>
        <p class="text-slate-500 text-sm mt-1">Kelola jadwal pelajaran kelas secara massal (grid view).</p>
      </div>
      <button 
        @click="saveJadwal"
        :disabled="isSaving"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
        <span v-if="isSaving">Menyimpan...</span>
        <template v-else>
          <AppIcon name="device-floppy" class="w-5 h-5" />
          Simpan Jadwal {{ activeDay }}
        </template>
      </button>
    </div>

    <!-- Tabs Hari -->
    <div class="flex overflow-x-auto custom-scrollbar gap-2 mb-6 border-b border-slate-200/60 pb-1">
      <button 
        v-for="hari in days" 
        :key="hari"
        @click="changeDay(hari)"
        :class="activeDay === hari 
          ? 'bg-indigo-600 text-white shadow-md' 
          : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
        class="px-6 py-2.5 rounded-t-xl font-bold text-sm transition-all whitespace-nowrap">
        {{ hari }}
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="isLoadingOptions || isLoadingJadwal" class="flex justify-center items-center py-20 bg-white rounded-2xl border border-slate-200">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
    </div>

    <!-- Matrix Table (Excel style) -->
    <div v-else class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col relative">
      <div class="overflow-x-auto custom-scrollbar flex-1 relative max-h-[70vh]">
        <table class="w-full text-xs text-left whitespace-nowrap border-collapse">
          <thead class="bg-slate-50 text-slate-700 font-black sticky top-0 z-20 shadow-sm">
            <!-- Row Header 1: Tingkat -->
            <tr>
              <th rowspan="2" class="p-3 text-center border-r border-b border-slate-200 bg-slate-100 sticky left-0 z-30 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] w-12">JP</th>
              <th v-for="(classes, tingkat) in kelasPerTingkat" :key="tingkat" :colspan="classes.length" class="p-2 text-center border-r border-b border-slate-200 uppercase tracking-widest text-indigo-700 bg-indigo-50/50">
                Tingkat {{ tingkat }}
              </th>
            </tr>
            <!-- Row Header 2: Kelas -->
            <tr>
              <template v-for="(classes, tingkat) in kelasPerTingkat" :key="'sub-'+tingkat">
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
              <template v-for="(classes, tingkat) in kelasPerTingkat" :key="'col-'+tingkat">
                <td v-for="cls in classes" :key="cls.id" class="p-1 border-r border-b border-slate-200">
                  <select 
                    v-model="formJadwal[`${jp}_${cls.id}`]" 
                    class="w-full text-[11px] p-1.5 border border-transparent rounded bg-transparent hover:bg-white hover:border-slate-300 hover:shadow-sm focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-all cursor-pointer">
                    <option value="">- Kosong -</option>
                    <option v-for="opt in mapelOptions" :key="opt.value" :value="opt.value" :title="opt.label">
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
</template>

<script setup>
import { ref, onMounted } from 'vue'

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
  // Reset form
  formJadwal.value = {}
  
  try {
    const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/kurikulum/jadwal-mengajar?hari=${hari}`, {
      headers: { Authorization: `Bearer ${tokenCookie.value}` }
    })
    if (res.success) {
      formJadwal.value = res.data // { "1_2": "14_null", "2_2": "15_3", ... }
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
  
  // Convert formJadwal object back to array payload
  const payloadJadwals = []
  
  for (const [key, val] of Object.entries(formJadwal.value)) {
    if (!val) continue // Skip empty selections
    
    // key is "jamKe_kelasId"
    const [jamKe, kelasId] = key.split('_')
    
    // val is "mapelId_guruId"
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
  background: #f1f5f9; 
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1; 
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8; 
}
</style>
