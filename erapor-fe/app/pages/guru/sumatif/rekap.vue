<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- MAIN GRID LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-8">
      
      <!-- ==============================================
           KIRI: FILTER BOX (25%) -> lg:col-span-3
           ============================================== -->
      <div class="lg:col-span-3 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
          <div class="p-6 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-700">
            <h3 class="text-sm font-black uppercase tracking-widest text-white">Filter Data</h3>
            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Tentukan parameter Rekap</p>
          </div>
          
          <div class="p-6 space-y-5">
            <!-- Tahun Ajaran -->
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tahun Ajaran</label>
              <select v-model="filter.tahun_ajaran_id" @change="handleFilterChange" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-medium text-slate-700 outline-none" :disabled="isLoading">
                <option v-for="ta in references.tahunAjarans" :key="ta.id" :value="ta.id">{{ ta.tahun }}</option>
              </select>
            </div>

            <!-- Kurikulum -->
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kurikulum</label>
              <select v-model="filter.kurikulum_id" @change="handleFilterChange" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-medium text-slate-700 outline-none" :disabled="isLoading">
                <option v-for="kur in references.kurikulums" :key="kur.id" :value="kur.id">{{ kur.nama_kurikulum }}</option>
              </select>
            </div>

            <!-- Titimangsa -->
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Periode</label>
              <select v-model="filter.titimangsa_id" @change="handleFilterChange" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-medium text-slate-700 outline-none" :disabled="isLoading">
                <option value="">-- Pilih Periode --</option>
                <option v-for="per in references.periodes" :key="per.id" :value="per.id">
                  {{ per.nama_periode }} {{ per.is_aktif ? '(Aktif)' : '(Ditutup)' }}
                </option>
              </select>
            </div>

            <!-- Kelas -->
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Rombel</label>
              <select v-model="filter.kelas_id" @change="() => { filter.mapel_id = ''; handleFilterChange(); }" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-medium text-slate-700 outline-none" :disabled="isLoading">
                <option value="">-- Pilih Rombel --</option>
                <option v-for="kelas in references.kelases" :key="kelas.id" :value="kelas.id">{{ kelas.tingkat }} {{ kelas.nama_kelas }}</option>
              </select>
            </div>

            <!-- Mapel -->
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Mata Pelajaran</label>
              <select v-model="filter.mapel_id" @change="handleFilterChange" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-medium text-slate-700 outline-none" :disabled="isLoading || !filter.kelas_id">
                <option value="">-- Pilih Mapel --</option>
                <option v-for="mapel in references.mapels" :key="mapel.id" :value="mapel.id">{{ mapel.nama_mapel }}</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- ==============================================
           KANAN: WORKSPACE AREA (75%) -> lg:col-span-9
           ============================================== -->
      <div class="lg:col-span-9">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col min-h-[500px]">
          
          <!-- Header Card Kanan -->
          <div class="px-6 py-4 bg-sky-600 border-b border-sky-700 flex justify-between items-center text-white shrink-0 shadow-sm z-10">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-lg shadow-inner">📈</div>
              <div>
                <h3 class="text-sm font-black leading-none uppercase tracking-wide">Rekapitulasi Akhir Sumatif</h3>
                <p class="text-[10px] font-bold text-sky-100 uppercase tracking-widest mt-1">{{ siswas.length }} Siswa Aktif</p>
              </div>
            </div>
            
            <div class="flex items-center space-x-4 shrink-0 mt-4 md:mt-0" v-if="isFilterComplete">
               <button class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest px-4 py-2.5 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 hover:shadow-lg transition-all active:scale-95" @click="printRekap">
                <span>🖨️ CETAK PDF</span>
              </button>
            </div>
          </div>

          <!-- Alert Belum Lengkap -->
          <div v-if="!isFilterComplete" class="flex-grow flex flex-col items-center justify-center p-16 text-center relative overflow-hidden bg-gradient-to-b from-transparent to-slate-50/50">
            <!-- Background Decoration -->
            <div class="absolute inset-0 flex items-center justify-center opacity-[0.02] pointer-events-none">
                <svg class="w-96 h-96" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zM7 10h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/></svg>
            </div>
            
            <div class="w-24 h-24 mb-6 rounded-full bg-indigo-50 flex items-center justify-center border-8 border-white shadow-sm relative z-10">
                <span class="text-4xl animate-bounce" style="animation-duration: 3s;">📊</span>
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
          <div v-else-if="isLoading" class="flex-grow flex flex-col items-center justify-center p-20 opacity-60">
            <div class="w-8 h-8 border-4 border-sky-400 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Rekapitulasi...</span>
          </div>

          <!-- List Workspace Area (REKAP SUMATIF) -->
          <div v-else class="flex-grow flex flex-col relative bg-slate-50/50">
            <div class="flex-1 overflow-x-auto custom-scrollbar relative">
              
              <!-- PRINT HEADER (Hanya Tampil Saat Dicetak) -->
              <div class="hidden print-header mb-6">
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
                <thead class="sticky top-0 z-20 shadow-sm">
                  <tr class="bg-slate-100 border-b border-slate-200">
                    <th class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500 sticky left-0 bg-slate-100 w-[250px] border-r">Nama Siswa</th>
                    <th class="py-3 px-4 text-center text-[10px] font-black uppercase tracking-widest text-slate-500 w-[100px] border-r">Nilai Akhir</th>
                    <th class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500 min-w-[300px]">Deskripsi Capaian Kompetensi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-for="siswa in siswas" :key="siswa.siswa_id" class="hover:bg-sky-50/20 transition-colors">
                    
                    <!-- Kolom Nama Siswa -->
                    <td class="py-3 px-4 sticky left-0 bg-white border-r group-hover:bg-sky-50/20">
                      <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500 border border-slate-200">
                          {{ siswa.nama.charAt(0) }}
                        </div>
                        <div>
                          <div class="text-[11px] font-bold text-slate-700 leading-tight uppercase">{{ siswa.nama }}</div>
                        </div>
                      </div>
                    </td>

                    <!-- Kolom Nilai Akhir -->
                    <td class="py-3 px-4 text-center border-r bg-slate-50/50">
                      <span v-if="siswa.angka !== null" class="text-sm font-black text-indigo-700 bg-white px-3 py-1 rounded shadow-sm border border-indigo-100">
                        {{ siswa.angka }}
                      </span>
                      <span v-else class="text-xs font-bold text-slate-300">-</span>
                    </td>

                    <!-- Kolom Deskripsi Capaian -->
                    <td class="py-3 px-4">
                      <p class="text-xs text-slate-600 leading-relaxed max-w-2xl text-justify">
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
  const periode = periodeObj ? periodeObj.nama_periode : '-'
  
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

    const res = await $fetch(`http://localhost:8000/api/guru/sumatif/nilai?${queryParams.toString()}`, {
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
    const res = await $fetch('http://localhost:8000/api/guru/sumatif/rekap', {
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
  background: #f1f5f9; 
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1; 
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8; 
}

</style>

<style>
@media print {
  @page {
    margin: 1cm;
  }

  /* Sembunyikan elemen layout global dan komponen yang tidak di-print */
  aside.min-h-screen, 
  aside.w-64,
  header.h-14,
  header, 
  .mt-10.pt-4.border-t, 
  .lg\:col-span-3, 
  .px-6.py-4.bg-sky-600, 
  button, 
  .w-7.h-7.rounded-full {
    display: none !important;
  }
  
  /* Reset secara brutal semua parent container agar menjadi block biasa (normal flow) */
  body, 
  html, 
  #__nuxt, 
  #__layout, 
  .min-h-screen, 
  .h-screen, 
  main, 
  .flex-1, 
  .flex-grow, 
  .animate-fadeIn, 
  .grid, 
  .lg\:col-span-9, 
  .bg-white.rounded-2xl.shadow-sm, 
  .overflow-x-auto, 
  .max-w-7xl {
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
    padding-top: 0 !important;
  }
  
  /* Pengaturan tabel agar tidak terpotong dengan rapi */
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
    vertical-align: middle !important;
  }
  
  /* Hilangkan styling tambahan (kotak/warna) di dalam cell tabel */
  table.print-table th *, table.print-table td * {
    border: none !important;
    box-shadow: none !important;
    background: transparent !important;
    color: black !important;
  }
  
  table.print-table thead tr {
    background-color: transparent !important;
    -webkit-print-color-adjust: exact;
  }
}
</style>
