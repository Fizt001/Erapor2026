<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'

definePageMeta({
  layout: 'guru'
})

const days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']
const selectedDay = ref('Senin')
const isLoading = ref(false)
const jadwals = ref<any[]>([])

const currentDate = ref('')
const tanggalMulai = ref('')
const maxDate = ref('')

const fetchJadwal = async () => {
  isLoading.value = true
  try {
    const tokenCookie = useCookie('auth_token')
    let url = import.meta.env.VITE_API_BASE_URL + `/api/guru/jadwal-harian?hari=${selectedDay.value}`
    if (currentDate.value) {
        url += `&tanggal=${currentDate.value}`
    }
    
    const response: any = await $fetch(url, {
      headers: {
        'Authorization': `Bearer ${tokenCookie.value}`,
        'Accept': 'application/json'
      }
    })
    
    tanggalMulai.value = response.tanggal_mulai || ''
    currentDate.value = response.target_tanggal || ''
    if (!maxDate.value) {
        maxDate.value = response.target_tanggal || ''
    }

    // Inisialisasi form data untuk tiap kelas
    const data = response.data.map((j: any) => {
      // Jika belum diisi, array absensi kita wrap ke reaktif
      if (!j.jurnal) j.jurnal = ''
      return j
    })
    
    jadwals.value = data
  } catch (error) {
    console.error('Failed to fetch jadwal harian', error)
    const { $toast } = useNuxtApp()
    $toast.error('Gagal mengambil data jadwal mengajar')
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchJadwal()
})

const selectDay = (day: string) => {
  selectedDay.value = day
  currentDate.value = ''
  maxDate.value = ''
  fetchJadwal()
}

const canGoPrev = computed(() => {
    if (!currentDate.value || !tanggalMulai.value) return false
    const curr = new Date(currentDate.value)
    const min = new Date(tanggalMulai.value)
    const prev = new Date(curr)
    prev.setDate(prev.getDate() - 7)
    return prev >= min
})

const canGoNext = computed(() => {
    if (!currentDate.value || !maxDate.value) return false
    return currentDate.value < maxDate.value
})

const prevWeek = () => {
    if (!canGoPrev.value || !currentDate.value) return
    const d = new Date(currentDate.value)
    d.setDate(d.getDate() - 7)
    currentDate.value = d.toISOString().split('T')[0]
    fetchJadwal()
}

const nextWeek = () => {
    if (!canGoNext.value || !currentDate.value) return
    const d = new Date(currentDate.value)
    d.setDate(d.getDate() + 7)
    currentDate.value = d.toISOString().split('T')[0]
    fetchJadwal()
}


const simpanJurnal = async (jadwal: any) => {
  try {
    const tokenCookie = useCookie('token')
    const { $toast } = useNuxtApp()
    
    const payload = {
      kelas_id: jadwal.kelas_id,
      mapel_id: jadwal.mapel_id,
      tanggal: jadwal.tanggal,
      jam_ke_string: jadwal.jam_ke_string,
      waktu_mulai: jadwal.waktu_mulai,
      waktu_selesai: jadwal.waktu_selesai,
      materi: jadwal.jurnal,
      absensi: jadwal.absensi
    }
    
    await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/guru/jadwal-simpan', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${tokenCookie.value}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: payload
    })
    
    $toast.success('Jurnal dan Absensi berhasil disimpan!')
    
    // Refresh to get updated pertemuan_ke if it was the first time
    fetchJadwal()
  } catch (error) {
    const { $toast } = useNuxtApp()
    $toast.error('Gagal menyimpan jurnal & absensi')
    console.error(error)
  }
}

const getStatusBadgeColor = (status: string) => {
  switch (status) {
    case 'H': return 'bg-green-100 text-green-800 border-green-200'
    case 'S': return 'bg-blue-100 text-blue-800 border-blue-200'
    case 'I': return 'bg-yellow-100 text-yellow-800 border-yellow-200'
    case 'A': return 'bg-red-100 text-red-800 border-red-200'
    case 'L': return 'bg-purple-100 text-purple-800 border-purple-200'
    case 'P': return 'bg-indigo-100 text-indigo-800 border-indigo-200'
    default: return 'bg-gray-100 text-gray-800'
  }
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Jadwal Mengajar</h1>
        <p class="text-slate-500 mt-1">Kelola presensi dan jurnal mengajar kelas Anda</p>
      </div>
    </div>

    <!-- Main Content: 2 Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
      <!-- Left Card: Days Navigation -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden sticky top-6">
          <div class="p-4 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">PILIH HARI</h2>
          </div>
          <div class="p-2 space-y-1">
            <button
              v-for="day in days"
              :key="day"
              @click="selectDay(day)"
              class="w-full flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200"
              :class="[
                selectedDay === day
                  ? 'bg-blue-50 text-blue-700 border border-blue-200 shadow-sm'
                  : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 border border-transparent'
              ]"
            >
              <AppIcon name="calendar" class="w-4 h-4 mr-3" :class="selectedDay === day ? 'text-blue-500' : 'text-slate-400'" />
              {{ day }}
              <div v-if="selectedDay === day" class="ml-auto w-1.5 h-1.5 rounded-full bg-blue-500"></div>
            </button>
          </div>
        </div>
      </div>

      <!-- Right Card: Class List -->
      <div class="lg:col-span-3 space-y-6">
        
        <!-- Date Navigation Header -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center justify-between">
            <button @click="prevWeek" :disabled="!canGoPrev || isLoading" class="p-2 rounded-xl transition-all" :class="canGoPrev ? 'text-slate-600 hover:bg-slate-100 active:scale-95' : 'text-slate-300 cursor-not-allowed'">
                <AppIcon name="arrow-left" class="w-5 h-5" />
            </button>
            <div class="text-center">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-0.5">Tanggal Mengajar</p>
                <h3 class="text-sm font-bold text-slate-800">{{ currentDate ? new Date(currentDate).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : '-' }}</h3>
            </div>
            <button @click="nextWeek" :disabled="!canGoNext || isLoading" class="p-2 rounded-xl transition-all" :class="canGoNext ? 'text-slate-600 hover:bg-slate-100 active:scale-95' : 'text-slate-300 cursor-not-allowed'">
                <AppIcon name="arrow-right" class="w-5 h-5" />
            </button>
        </div>

        <div v-if="isLoading" class="flex justify-center py-12 bg-white rounded-2xl border border-slate-100">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        </div>
        
        <div v-else-if="jadwals.length === 0" class="bg-white rounded-2xl border border-slate-100 p-12 text-center">
          <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <AppIcon name="calendar" class="w-8 h-8 text-slate-400" />
          </div>
          <h3 class="text-lg font-bold text-slate-700">Kosong</h3>
          <p class="text-slate-500 mt-1">Bapak/Ibu tidak memiliki jadwal mengajar di hari {{ selectedDay }}.</p>
        </div>

        <div v-else v-for="(j, index) in jadwals" :key="index" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md">
          
          <!-- Card Header -->
          <div class="flex items-center justify-between p-5" :class="j.status_waktu === 'belum_waktunya' ? 'bg-slate-50' : 'bg-gradient-to-r from-blue-50 to-white'">
            <div class="flex items-center space-x-4">
              <div class="flex flex-col items-center justify-center w-14 h-14 rounded-xl" :class="j.status_waktu === 'belum_waktunya' ? 'bg-slate-200 text-slate-500' : 'bg-blue-600 text-white shadow-md shadow-blue-200'">
                <span class="text-xs font-bold uppercase tracking-wider opacity-80">JP</span>
                <span class="text-xl font-black leading-none mt-0.5">{{ j.jam_ke_string }}</span>
              </div>
              <div>
                <h3 class="text-xl font-bold text-slate-800">{{ j.kelas_nama }}</h3>
                <div class="flex items-center text-sm text-slate-500 mt-1 space-x-3">
                  <span class="flex items-center"><AppIcon name="book" class="w-4 h-4 mr-1.5 opacity-70" /> {{ j.mapel_nama }}</span>
                  <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                  <span class="flex items-center"><AppIcon name="clock" class="w-4 h-4 mr-1.5 opacity-70" /> {{ j.waktu }}</span>
                </div>
              </div>
            </div>

            <!-- Status Badge -->
            <div>
              <span v-if="j.status_waktu === 'belum_waktunya'" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                <AppIcon name="lock" class="w-3 h-3 mr-1.5" /> Belum Waktunya
              </span>
              <span v-else-if="j.status_waktu === 'sekarang'" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200 animate-pulse">
                <AppIcon name="check-circle" class="w-3 h-3 mr-1.5" /> Sedang Berlangsung
              </span>
              <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700 border border-orange-200">
                <AppIcon name="history" class="w-3 h-3 mr-1.5" /> Sudah Lewat
              </span>
            </div>
          </div>

          <!-- Card Body: Blocked View -->
          <div v-if="j.status_waktu === 'belum_waktunya'" class="p-8 text-center bg-slate-50/50 border-t border-slate-100">
             <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 mb-3">
               <AppIcon name="lock" class="w-5 h-5 text-slate-400" />
             </div>
             <p class="text-slate-500 text-sm">Formulir absensi dan jurnal akan terbuka saat jam pelajaran dimulai.</p>
          </div>

          <!-- Card Body: Active View -->
          <div v-else class="p-6 border-t border-slate-100 bg-white">
             <!-- Jurnal Input -->
             <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                  <label class="block text-sm font-bold text-slate-700">Materi yang diajarkan</label>
                  <span class="text-xs font-medium px-2 py-1 bg-blue-50 text-blue-700 rounded-md border border-blue-100">
                    Pertemuan ke-{{ j.pertemuan_ke }}
                  </span>
                </div>
                <textarea 
                  v-model="j.jurnal"
                  rows="2" 
                  class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all text-sm resize-none bg-slate-50 focus:bg-white"
                  placeholder="Deskripsikan materi singkat yang diajarkan hari ini..."
                ></textarea>
             </div>

             <!-- Absensi Table -->
             <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">Presensi Kehadiran Siswa</label>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                  <table class="w-full text-left border-collapse">
                    <thead>
                      <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-12 text-center">No</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Siswa</th>
                        <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Kehadiran</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                      <tr v-for="(siswa, sIdx) in j.absensi" :key="siswa.siswa_id" class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3 px-4 text-sm text-slate-400 text-center">{{ sIdx + 1 }}</td>
                        <td class="py-3 px-4">
                          <div class="text-sm font-bold text-slate-700">{{ siswa.nama_lengkap }}</div>
                          <div class="text-xs text-slate-400 mt-0.5">{{ siswa.nisn }}</div>
                        </td>
                        <td class="py-3 px-4 text-center">
                          <div class="inline-flex bg-slate-100 rounded-lg p-1 border border-slate-200 shadow-inner">
                            <button v-for="st in ['H','S','I','A','L','P']" :key="st" 
                                    @click="siswa.status = st"
                                    class="w-8 h-8 rounded-md text-xs font-bold flex items-center justify-center transition-all duration-200"
                                    :class="siswa.status === st ? getStatusBadgeColor(st) + ' shadow-sm transform scale-105' : 'text-slate-500 hover:bg-white hover:shadow-sm'">
                              {{ st }}
                            </button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
             </div>

             <!-- Action Footer -->
             <div class="mt-6 flex justify-end">
               <button @click="simpanJurnal(j)" class="flex items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-sm shadow-blue-200 transition-all active:scale-95">
                 <AppIcon name="save" class="w-4 h-4 mr-2" />
                 Simpan Jurnal & Absensi
               </button>
             </div>
          </div>
          
        </div>
      </div>
    </div>
  </div>
</template>
