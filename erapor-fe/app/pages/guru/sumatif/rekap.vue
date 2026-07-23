<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex flex-col xl:flex-row overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="xl:w-[360px] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] overflow-y-auto custom-scrollbar print:hidden">
        <div class="p-4 pb-2 space-y-4">
          <div class="bg-gradient-to-r from-sky-600 to-blue-700 rounded-2xl p-4 border border-sky-500 shadow-sm relative overflow-hidden flex items-center gap-3">
            <div class="w-8 h-8 flex items-center justify-center shrink-0 bg-white/10 rounded-lg relative z-10 text-white"><AppIcon name="document-text" class="w-5 h-5" /></div>
            <div class="relative z-10">
              <h3 class="text-xs font-black uppercase tracking-widest text-white">Filter Data</h3>
              <p class="text-[9px] text-sky-100 font-semibold uppercase mt-0.5">Rekapitulasi Nilai</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-15 text-white pointer-events-none">
              <svg class="w-16 h-16 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            </div>
          </div>
          
          <div class="space-y-4">
            <!-- Tahun Ajaran -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Tahun Ajaran</label>
              <div v-if="references.tahunAjarans.length" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 font-bold text-xs text-slate-700 flex items-center justify-between">
                  <span>{{ references.tahunAjarans.find(t => t.is_aktif)?.tahun || '-' }}</span>
                  <span class="px-2 py-1 bg-sky-100 text-sky-700 rounded text-[9px] font-black uppercase tracking-widest">Aktif</span>
              </div>
            </div>

            <!-- Kurikulum -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kurikulum</label>
              <select v-model="filter.kurikulum_id" @change="handleFilterChange" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none" :disabled="isLoading || references.kurikulums.length === 0">
                <option v-if="references.kurikulums.length === 0" value="" disabled>Belum diseting</option>
                <option v-for="kur in references.kurikulums" :key="kur.id" :value="kur.id">{{ kur.nama_kurikulum }}</option>
              </select>
            </div>

            <!-- Titimangsa -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Periode</label>
              <select v-model="filter.titimangsa_id" @change="handleFilterChange" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none" :disabled="isLoading || references.periodes.length === 0">
                <option v-if="references.periodes.length === 0" value="" disabled>Belum diseting</option>
                <option v-else value="">- Pilih Periode -</option>
                <option v-for="per in references.periodes" :key="per.id" :value="per.id">
                  {{ per.nama_periode_panjang || per.nama_periode }} {{ per.is_aktif ? '(Aktif)' : '(Ditutup)' }}
                </option>
              </select>
            </div>

            <!-- Kelas -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Rombongan Belajar</label>
              <select v-model="filter.kelas_id" @change="() => { filter.mapel_id = ''; handleFilterChange(); }" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none" :disabled="isLoading || references.kelases.length === 0">
                <option v-if="references.kelases.length === 0" value="" disabled>Belum diseting</option>
                <option v-else value="">- Pilih Rombel -</option>
                <option v-for="kelas in references.kelases" :key="kelas.id" :value="kelas.id">{{ kelas.tingkat }} {{ kelas.nama_kelas }}</option>
              </select>
            </div>

            <!-- Mapel -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Mata Pelajaran</label>
              <select v-model="filter.mapel_id" @change="handleFilterChange" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none" :disabled="isLoading || !filter.kelas_id || references.mapels.length === 0">
                <option v-if="references.mapels.length === 0" value="" disabled>Belum diseting</option>
                <option v-else value="">- Pilih Mapel -</option>
                <option v-for="mapel in references.mapels" :key="mapel.id" :value="mapel.id">{{ mapel.nama_mapel }}</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative print:bg-white print:block print:h-auto print:overflow-visible">
        <div class="p-6 lg:p-8 max-w-6xl mx-auto w-full h-full flex flex-col relative z-0 print:p-0 print:max-w-none print:block print:h-auto print:overflow-visible">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0 print-container print:border-none print:shadow-none print:block print:h-auto print:overflow-visible">
            
            <!-- Header Card Kanan -->
            <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-white shrink-0 z-10 print:hidden">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center text-lg border border-sky-100">📊</div>
                <div>
                  <h3 class="text-[13px] font-black leading-none uppercase tracking-wide text-slate-800">Rekapitulasi Akhir Sumatif</h3>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ isFilterComplete ? siswas.length + ' Siswa Aktif' : 'Pilih filter untuk memuat data' }}</p>
                </div>
              </div>
              
              <div class="flex items-center space-x-4 shrink-0 mt-4 md:mt-0" v-if="isFilterComplete">
                 <button class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest px-4 py-2.5 bg-slate-800 text-white rounded-xl shadow hover:bg-slate-700 hover:shadow-md transition-all active:scale-95" @click="printRekap">
                  <span>🖨️ CETAK PDF</span>
                </button>
              </div>
            </div>

            <!-- Alert Belum Lengkap -->
            <div v-if="!isFilterComplete" class="flex-grow flex flex-col items-center justify-center p-16 text-center relative overflow-hidden bg-slate-50/50 print:hidden">
              <div class="w-24 h-24 mb-6 rounded-full bg-sky-50 flex items-center justify-center border-8 border-white shadow-sm relative z-10">
                  <span class="text-4xl animate-bounce" style="animation-duration: 3s;">📈</span>
              </div>
              <h3 class="font-black text-slate-700 text-xl tracking-tight relative z-10 mb-2">Pilih Filter Data</h3>
              <p class="text-sm font-semibold text-slate-500 max-w-md relative z-10 leading-relaxed">
                  Tabel rekapitulasi nilai akhir masih kosong. Silakan lengkapi parameter filter di panel sebelah kiri untuk memuat data siswa.
              </p>
              <div class="mt-8 flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest relative z-10">
                  <span class="animate-pulse text-lg">👈</span> Arahkan perhatian ke panel kiri
              </div>
            </div>

            <!-- Loading Indicator -->
            <div v-else-if="isLoading" class="flex-grow flex flex-col items-center justify-center p-20 opacity-60 print:hidden">
              <div class="w-8 h-8 border-4 border-sky-400 border-t-transparent rounded-full animate-spin mb-4"></div>
              <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Memuat Rekapitulasi...</span>
            </div>

            <!-- List Workspace Area (REKAP SUMATIF) -->
            <div v-else class="flex-grow flex flex-col relative bg-slate-50/30 overflow-hidden print-full">
              <div class="flex-1 overflow-auto custom-scrollbar relative print-scrollable">
                
                <!-- PRINT HEADER (Hanya Tampil Saat Dicetak) -->
                <div class="hidden print-header mb-6 mt-4 px-6">
                  <div class="text-center font-bold text-lg leading-tight uppercase mb-6 border-b-2 border-black pb-4">
                    <div>LAPORAN REKAPITULASI AKHIR SUMATIF</div>
                    <div class="mt-1">{{ printData.periode }}</div>
                  </div>
                  
                  <div class="flex justify-between items-start text-sm font-semibold mb-6">
                    <!-- Kiri -->
                    <table class="w-auto">
                      <tbody>
                        <tr>
                          <td class="pr-8 py-1 border-none !px-0 whitespace-nowrap">Nama Guru Mapel</td>
                          <td class="border-none !px-0">: {{ printData.guru }}</td>
                        </tr>
                        <tr>
                          <td class="pr-8 py-1 border-none !px-0 whitespace-nowrap">Mata Pelajaran</td>
                          <td class="border-none !px-0">: {{ printData.mapel }}</td>
                        </tr>
                      </tbody>
                    </table>
                    
                    <!-- Kanan -->
                    <table class="w-auto">
                      <tbody>
                        <tr>
                          <td class="pr-8 py-1 border-none !px-0 whitespace-nowrap">Kelas</td>
                          <td class="border-none !px-0">: {{ printData.kelas }}</td>
                        </tr>
                        <tr>
                          <td class="pr-8 py-1 border-none !px-0 whitespace-nowrap">Asesmen</td>
                          <td class="border-none !px-0">: {{ printData.periode }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <table class="w-full text-left border-collapse bg-white print-table">
                  <thead class="sticky top-0 z-20 shadow-sm print-thead">
                    <tr class="bg-slate-100 border-b border-slate-200">
                      <th class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500 sticky left-0 bg-slate-100 w-[200px] z-30 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] border-r">Nama Siswa</th>
                      <th class="py-3 px-4 text-center text-[10px] font-black uppercase tracking-widest text-slate-500 w-[100px] border-r">Nilai Akhir</th>
                      <th class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500 min-w-[300px]">Deskripsi Capaian Kompetensi</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    <tr v-for="siswa in siswas" :key="siswa.siswa_id" class="hover:bg-sky-50/30 transition-colors">
                      
                      <!-- Kolom Nama Siswa -->
                      <td class="py-3 px-4 sticky left-0 bg-white border-r group-hover:bg-sky-50/10 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.02)]">
                        <div class="flex items-center gap-3">
                          <div class="w-7 h-7 rounded-lg bg-slate-50 flex items-center justify-center text-[10px] font-black text-slate-500 border border-slate-200 print:hidden">
                            {{ siswa.nama.charAt(0) }}
                          </div>
                          <div>
                            <div class="text-[11px] font-black text-slate-700 leading-tight uppercase">{{ siswa.nama }}</div>
                            <div class="text-[9px] font-bold text-slate-400 mt-0.5 print:hidden">{{ siswa.nis || 'NIS. -' }}</div>
                          </div>
                        </div>
                      </td>

                      <!-- Kolom Nilai Akhir -->
                      <td class="py-3 px-4 text-center border-r bg-slate-50/50 print-bg-white">
                        <span v-if="siswa.angka !== null" class="text-sm font-black text-sky-700 bg-white px-3 py-1 rounded-lg shadow-sm border border-sky-100 print-no-shadow">
                          {{ siswa.angka }}
                        </span>
                        <span v-else class="text-xs font-bold text-slate-300">-</span>
                      </td>

                      <!-- Kolom Deskripsi Capaian -->
                      <td class="py-3 px-4">
                        <p class="text-[11px] font-semibold text-slate-600 leading-relaxed max-w-3xl text-justify">
                          {{ siswa.deskripsi }}
                        </p>
                      </td>
                      
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
      </div>
      
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'

definePageMeta({
  layout: 'guru',
  middleware: 'guru',
  title: 'Rekapitulasi Akhir Sumatif'
})

const tokenCookie = useCookie('auth_token')
const isLoading = ref(false)

const filter = reactive({
  tahun_ajaran_id: '',
  kurikulum_id: '',
  titimangsa_id: '',
  kelas_id: '',
  mapel_id: ''
})

const references = reactive({
  tahunAjarans: [],
  kurikulums: [],
  periodes: [],
  kelases: [],
  mapels: [],
})

const siswas = ref([])

const userCookie = useCookie('user_profile')
const printData = computed(() => {
  const profile = typeof userCookie.value === 'string' ? JSON.parse(userCookie.value) : userCookie.value
  const guruName = profile?.name || 'Guru'
  
  const mapel = references.mapels.find(m => m.id === filter.mapel_id)?.nama_mapel || '-'
  
  const kelasObj = references.kelases.find(k => k.id === filter.kelas_id)
  const kelas = kelasObj ? `${kelasObj.tingkat} ${kelasObj.nama_kelas}` : '-'
  
  const periodeObj = references.periodes.find(p => p.id === filter.titimangsa_id)
  const periode = periodeObj ? (periodeObj.nama_periode_panjang || periodeObj.nama_periode) : '-'
  
  return {
    guru: guruName,
    mapel,
    kelas,
    periode
  }
})

const isFilterComplete = computed(() => {
  return filter.tahun_ajaran_id && filter.kurikulum_id && filter.titimangsa_id && filter.kelas_id && filter.mapel_id
})

const fetchReferences = async () => {
  try {
    const queryParams = new URLSearchParams()
    Object.keys(filter).forEach(key => {
      if (filter[key]) queryParams.append(key, filter[key])
    })

    const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/guru/sumatif/nilai?${queryParams.toString()}`, {
      headers: { 'Authorization': `Bearer ${tokenCookie.value}` }
    })
    
    if (res.success) {
      references.tahunAjarans = res.data.tahun_ajarans || []
      references.kurikulums = res.data.kurikulums || []
      references.periodes = res.data.periodes || []
      references.mapels = res.data.mapels || []
      references.kelases = res.data.kelases || []
      
      if (!filter.tahun_ajaran_id) filter.tahun_ajaran_id = res.data.selections?.tahun_ajaran_id || ''
      if (!filter.kurikulum_id) filter.kurikulum_id = res.data.selections?.kurikulum_id || ''
      if (!filter.titimangsa_id) filter.titimangsa_id = res.data.selections?.titimangsa_id || ''
    }
  } catch (error) {
    console.error('Error fetching references:', error)
  }
}

const handleFilterChange = async () => {
    await fetchReferences()
    if (isFilterComplete.value) {
        await fetchData()
    } else {
        siswas.value = []
    }
}

const fetchData = async () => {
  if (!isFilterComplete.value) return
  
  isLoading.value = true
  siswas.value = []

  try {
    const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/guru/sumatif/rekap', {
      params: {
        kelas_id: filter.kelas_id,
        mapel_id: filter.mapel_id,
        titimangsa_id: filter.titimangsa_id,
      },
      headers: { 'Authorization': `Bearer ${tokenCookie.value}` }
    })
    
    if (res.status === 'success') {
      siswas.value = res.data || []
    }
  } catch (error) {
    console.error('Error fetching rekap data:', error)
  } finally {
    isLoading.value = false
  }
}

const printRekap = () => {
    window.print()
}

onMounted(async () => {
  await fetchReferences()
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

<style>
@media print {
  @page { margin: 1cm; }

  /* Sembunyikan elemen layout global dan komponen yang tidak di-print */
  aside,
  header,
  .\print\:hidden,
  .xl\:w-\[360px\] { /* dock kiri */
    display: none !important;
  }
  
  /* Reset layout agar konten utama bisa mengembang dan mencetak dengan benar */
  body, 
  html, 
  #__nuxt, 
  #__layout,
  main,
  .h-full,
  .min-h-0,
  .flex-1,
  .flex-col,
  .xl\:flex-row,
  .bg-slate-50,
  .p-6,
  .lg\:p-8,
  .max-w-6xl,
  .mx-auto,
  .print-container,
  .print-full,
  .print-scrollable,
  .overflow-hidden,
  .overflow-auto,
  .relative,
  .z-0 {
    display: block !important;
    position: static !important;
    height: auto !important;
    min-height: 0 !important;
    max-height: none !important;
    width: 100% !important;
    max-width: none !important;
    overflow: visible !important;
    margin: 0 !important;
    padding: 0 !important;
    box-shadow: none !important;
    border: none !important;
    background: transparent !important;
    transform: none !important;
  }

  /* Memastikan print header tampil */
  .print-header {
    display: block !important;
    margin-bottom: 2rem !important;
  }
  
  /* Pengaturan tabel agar tidak terpotong */
  table.print-table {
    width: 100% !important;
    border-collapse: collapse;
    page-break-inside: auto;
  }
  
  table.print-table tr {
    page-break-inside: avoid;
    page-break-after: auto;
  }
  
  table.print-table th, table.print-table td {
    border: 1px solid black !important;
    padding: 8px !important;
    white-space: normal !important;
    background: transparent !important;
    color: black !important;
    vertical-align: top !important;
  }
  
  table.print-table th *, table.print-table td * {
    border: none !important;
    box-shadow: none !important;
    background: transparent !important;
    color: black !important;
  }
  
  table.print-table thead.print-thead tr th {
    background-color: transparent !important;
    -webkit-print-color-adjust: exact;
    position: static !important;
  }

  .print-bg-white {
    background-color: transparent !important;
  }
  .print-no-shadow {
    box-shadow: none !important;
    border: none !important;
    padding: 0 !important;
  }
}
</style>
