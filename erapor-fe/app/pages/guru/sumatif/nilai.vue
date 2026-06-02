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
            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Tentukan parameter ujian</p>
          </div>
          
          <div class="p-6 space-y-5">
            <!-- Tahun Ajaran -->
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tahun Ajaran</label>
              <select :value="filter.tahun_ajaran_id" @change="handleFilterChange('tahun_ajaran_id', $event)" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-medium text-slate-700 outline-none" :disabled="isLoading">
                <option v-for="ta in references.tahunAjarans" :key="ta.id" :value="ta.id">{{ ta.tahun }}</option>
              </select>
            </div>

            <!-- Kurikulum -->
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kurikulum</label>
              <select :value="filter.kurikulum_id" @change="handleFilterChange('kurikulum_id', $event)" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-medium text-slate-700 outline-none" :disabled="isLoading">
                <option v-for="kur in references.kurikulums" :key="kur.id" :value="kur.id">{{ kur.nama_kurikulum }}</option>
              </select>
            </div>

            <!-- Titimangsa -->
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Periode</label>
              <select :value="filter.titimangsa_id" @change="handleFilterChange('titimangsa_id', $event)" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-medium text-slate-700 outline-none" :disabled="isLoading">
                <option value="">-- Pilih Periode --</option>
                <option v-for="per in references.periodes" :key="per.id" :value="per.id">
                  {{ per.nama_periode }} {{ per.is_aktif ? '(Aktif)' : '(Ditutup)' }}
                </option>
              </select>
            </div>

            <!-- Kelas -->
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Rombel</label>
              <select :value="filter.kelas_id" @change="handleFilterChange('kelas_id', $event)" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-medium text-slate-700 outline-none" :disabled="isLoading">
                <option value="">-- Pilih Rombel --</option>
                <option v-for="kelas in references.kelases" :key="kelas.id" :value="kelas.id">{{ kelas.nama_kelas }}</option>
              </select>
            </div>

            <!-- Mapel -->
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Mapel</label>
              <select :value="filter.mapel_id" @change="handleFilterChange('mapel_id', $event)" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-medium text-slate-700 outline-none" :disabled="isLoading || !filter.kelas_id">
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
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col min-h-[600px]" :class="{ 'opacity-80': !references.is_titimangsa_aktif }">
          
          <!-- Header Card Kanan -->
          <div class="px-6 py-4 bg-sky-600 border-b border-sky-700 flex justify-between items-center text-white shrink-0 shadow-sm z-10">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-lg shadow-inner">🏫</div>
              <div>
                <h3 class="text-sm font-black leading-none uppercase tracking-wide">
                  {{ filter.kelas_id ? references.kelases.find(k => k.id === filter.kelas_id)?.nama_kelas : 'Pilih Rombel' }}
                </h3>
                <p class="text-[10px] font-bold text-sky-100 uppercase tracking-widest mt-1">{{ siswas.length }} Siswa Aktif</p>
              </div>
            </div>
            
            <!-- Tombol Aksi Kanan -->
            <div class="flex items-center space-x-4 shrink-0 mt-4 md:mt-0" v-if="isFilterComplete && references.is_titimangsa_aktif">
              <button @click="executeManualSave" class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest px-4 py-2.5 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 hover:shadow-lg transition-all active:scale-95" v-if="saveStatus !== 'saving'">
                <span>SIMPAN DATA</span>
              </button>
              <div class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest px-4 py-2.5 bg-amber-500/20 text-amber-100 rounded-lg shadow-inner border border-amber-500/50 transition-all" v-if="saveStatus === 'saving'">
                <span class="animate-spin text-sm">↻</span> <span>Menyimpan...</span>
              </div>
            </div>
          </div>

          <!-- Alert Belum Lengkap -->
          <div v-if="!isFilterComplete" class="flex-grow flex flex-col items-center justify-center p-16 text-center relative overflow-hidden bg-gradient-to-b from-transparent to-slate-50/50">
            <!-- Background Decoration -->
            <div class="absolute inset-0 flex items-center justify-center opacity-[0.02] pointer-events-none">
                <svg class="w-96 h-96" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zM7 10h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/></svg>
            </div>
            
            <div class="w-24 h-24 mb-6 rounded-full bg-indigo-50 flex items-center justify-center border-8 border-white shadow-sm relative z-10">
                <span class="text-4xl animate-bounce" style="animation-duration: 3s;">📋</span>
            </div>
            
            <h3 class="font-black text-slate-700 text-xl tracking-tight relative z-10 mb-2">Pilih Filter Data</h3>
            <p class="text-sm font-semibold text-slate-500 max-w-md relative z-10 leading-relaxed">
                Matriks penilaian masih kosong. Silakan lengkapi parameter filter di panel sebelah kiri untuk memuat data siswa.
            </p>
            
            <div class="mt-8 flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest relative z-10">
                <span class="animate-pulse text-lg">👈</span> Arahkan perhatian ke panel kiri
            </div>
          </div>

          <!-- Loading Indicator -->
          <div v-else-if="isLoading" class="flex-grow flex flex-col items-center justify-center p-20 opacity-60">
            <div class="w-8 h-8 border-4 border-sky-400 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Matriks...</span>
          </div>

          <!-- List Workspace Area (MATRIKS SUMATIF) -->
          <div v-else class="flex-grow flex flex-col relative bg-slate-50/50">
            
            <!-- Panel Konfigurasi Bobot Global (Compact) -->
            <div class="bg-white border-b border-slate-200 p-2.5 shrink-0 shadow-sm z-10 flex flex-col xl:flex-row xl:items-center gap-3 xl:gap-6 justify-between">
                
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-lg leading-none">⚖️</span>
                    <div>
                        <h4 class="text-[10px] font-black text-slate-700 uppercase tracking-widest leading-none">Konfigurasi Bobot (%)</h4>
                        <div class="text-[8px] font-black uppercase tracking-widest mt-1">
                            <span v-if="references.is_psts && (globalBobot.b_uh + globalBobot.b_ujian) !== 100" class="text-rose-500">⚠️ Harian+Ujian ≠ 100%</span>
                            <span v-else-if="!references.is_psts && (globalBobot.b_uh + globalBobot.b_ujian + globalBobot.b_psts_lalu) !== 100" class="text-rose-500">⚠️ UH+Ujian+PSTS ≠ 100%</span>
                            <span v-else-if="(globalBobot.b_praktek + globalBobot.b_teori) !== 100" class="text-rose-500">⚠️ Praktek+Teori ≠ 100%</span>
                            <span v-else class="text-emerald-500">✓ Valid</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-1 gap-3 max-w-3xl xl:ml-auto">
                    <!-- BOX 1: BOBOT RAPOR -->
                    <div class="flex-[3] border border-slate-200 rounded flex items-center bg-slate-50 overflow-hidden">
                        <div class="bg-slate-100 border-r border-slate-200 px-2 py-1.5 h-full flex items-center justify-center shrink-0">
                            <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap">Rapor</span>
                        </div>
                        <div class="flex-1 flex items-center divide-x divide-slate-200 h-full">
                            <div class="flex-1 flex items-center px-1">
                                <span class="text-[9px] font-bold text-slate-400 mr-1">UH</span>
                                <input type="number" v-model="globalBobot.b_uh" @input="markAsUnsaved(null, null)" min="0" max="100" :disabled="!references.is_titimangsa_aktif"
                                    class="w-full bg-transparent text-[11px] font-black text-slate-700 text-center outline-none">
                            </div>
                            <div class="flex-1 flex items-center px-1">
                                <span class="text-[9px] font-bold text-slate-400 mr-1">UJI</span>
                                <input type="number" v-model="globalBobot.b_ujian" @input="markAsUnsaved(null, null)" min="0" max="100" :disabled="!references.is_titimangsa_aktif"
                                    class="w-full bg-transparent text-[11px] font-black text-slate-700 text-center outline-none">
                            </div>
                            <div v-if="!references.is_psts" class="flex-1 flex items-center px-1">
                                <span class="text-[9px] font-bold text-slate-400 mr-1">PSTS</span>
                                <input type="number" v-model="globalBobot.b_psts_lalu" @input="markAsUnsaved(null, null)" min="0" max="100" :disabled="!references.is_titimangsa_aktif"
                                    class="w-full bg-transparent text-[11px] font-black text-slate-700 text-center outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- BOX 2: BOBOT UJIAN -->
                    <div class="flex-[2] border border-slate-200 rounded flex items-center bg-slate-50 overflow-hidden">
                        <div class="bg-slate-100 border-r border-slate-200 px-2 py-1.5 h-full flex items-center justify-center shrink-0">
                            <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap">Ujian</span>
                        </div>
                        <div class="flex-1 flex items-center divide-x divide-slate-200 h-full">
                            <div class="flex-1 flex items-center px-1">
                                <span class="text-[9px] font-bold text-slate-400 mr-1">PRK</span>
                                <input type="number" v-model="globalBobot.b_praktek" @input="markAsUnsaved(null, null)" min="0" max="100" :disabled="!references.is_titimangsa_aktif"
                                    class="w-full bg-transparent text-[11px] font-black text-slate-700 text-center outline-none">
                            </div>
                            <div class="flex-1 flex items-center px-1">
                                <span class="text-[9px] font-bold text-slate-400 mr-1">TEO</span>
                                <input type="number" v-model="globalBobot.b_teori" @input="markAsUnsaved(null, null)" min="0" max="100" :disabled="!references.is_titimangsa_aktif"
                                    class="w-full bg-transparent text-[11px] font-black text-slate-700 text-center outline-none">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Periode Terkunci (Portal) -->
            <div v-if="!references.is_titimangsa_aktif" class="p-3 bg-amber-50 border-b border-amber-200 flex items-center gap-3 shadow-sm z-20">
              <div class="w-8 h-8 bg-white border border-amber-200 rounded-full flex items-center justify-center text-sm shrink-0 shadow-sm">🔒</div>
              <div>
                  <h4 class="text-[11px] font-black text-amber-700 uppercase tracking-widest">Akses Terkunci (Read-Only)</h4>
                  <p class="text-[9px] font-bold text-amber-600 uppercase tracking-widest mt-0.5">Periode ini sudah ditutup Admin. Data nilai tidak dapat diubah.</p>
              </div>
            </div>

            <div class="flex-1 overflow-x-auto custom-scrollbar relative pb-10">
              <table class="w-full text-left border-collapse bg-white table-fixed">
                <thead class="sticky top-0 z-20 shadow-sm">
                  <!-- Header Grouping -->
                  <tr class="bg-slate-200 border-b border-slate-300">
                    <th rowspan="2" class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500 sticky left-0 bg-slate-100 z-30 w-[180px] sm:w-[250px] border-r border-slate-300 shadow-[1px_0_0_0_#cbd5e1] align-middle">
                      Nama Siswa
                    </th>
                    <th colspan="4" class="py-1.5 px-1 border-r border-slate-300 text-center bg-indigo-50 text-[10px] font-black text-indigo-700 uppercase tracking-widest">
                      Nilai Harian (UH)
                    </th>
                    <th colspan="2" class="py-1.5 px-1 border-r border-slate-300 text-center bg-teal-50 text-[10px] font-black text-teal-700 uppercase tracking-widest">
                      Ujian Sumatif
                    </th>
                    <th rowspan="2" class="py-2 px-1 border-r border-slate-300 text-center w-[80px] bg-slate-50 align-middle" v-if="!references.is_psts">
                      <span class="text-[9px] font-black text-slate-600 uppercase tracking-tight block leading-tight">Nilai PSTS<br>Lalu</span>
                    </th>
                    <th rowspan="2" class="py-2 px-1 border-r border-slate-300 text-center w-[80px] bg-slate-50 align-middle">
                      <span class="text-[9px] font-black text-slate-600 uppercase tracking-tight block leading-tight">Nilai<br>Literasi</span>
                    </th>
                    <th rowspan="2" class="py-2 px-1 border-r border-slate-300 text-center w-[90px] bg-sky-100 align-middle shadow-inner">
                      <span class="text-[10px] font-black text-sky-800 uppercase tracking-wider block leading-tight">Nilai Akhir<br>(NA)</span>
                    </th>
                  </tr>
                  <!-- Sub Header -->
                  <tr class="bg-slate-100 border-b border-slate-200">
                    <th class="py-2 px-1 border-r border-slate-200 text-center w-[60px] bg-indigo-50/30 text-[9px] font-bold text-indigo-600 uppercase">UH1</th>
                    <th class="py-2 px-1 border-r border-slate-200 text-center w-[60px] bg-indigo-50/30 text-[9px] font-bold text-indigo-600 uppercase">UH2</th>
                    <th class="py-2 px-1 border-r border-slate-200 text-center w-[60px] bg-indigo-50/30 text-[9px] font-bold text-indigo-600 uppercase">UH3</th>
                    <th class="py-2 px-1 border-r border-slate-300 text-center w-[60px] bg-indigo-50/30 text-[9px] font-bold text-indigo-600 uppercase">UH4</th>
                    
                    <th class="py-2 px-1 border-r border-slate-200 text-center w-[70px] bg-teal-50/30 text-[9px] font-bold text-teal-600 uppercase">Praktek</th>
                    <th class="py-2 px-1 border-r border-slate-300 text-center w-[70px] bg-teal-50/30 text-[9px] font-bold text-teal-600 uppercase">Teori</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-for="siswa in siswas" :key="siswa.id" class="hover:bg-sky-50/20 group transition-colors h-12">
                    <td class="py-2 px-4 sticky left-0 bg-white group-hover:bg-slate-50/90 z-10 border-r border-slate-100 shadow-[1px_0_0_0_#f1f5f9] overflow-hidden text-ellipsis whitespace-nowrap">
                      <h4 class="text-[11px] font-black text-slate-700 uppercase tracking-wide truncate" :title="siswa.nama">{{ siswa.nama }}</h4>
                      <p class="text-[9px] font-bold text-slate-400">{{ siswa.nis }}</p>
                    </td>
                    
                    <!-- INPUT Harian (UH1-UH4) -->
                    <td v-for="uhField in ['uh1', 'uh2', 'uh3', 'uh4']" :key="uhField" class="p-0 border-r border-slate-100 text-center h-12 relative">
                      <input type="text" v-model="getNilai(siswa.id)[uhField]" @input="markAsUnsaved(siswa.id, uhField)" :disabled="!references.is_titimangsa_aktif"
                        class="w-full h-full border-none p-0 text-center font-black text-xs transition-all focus:ring-inset focus:ring-2 focus:ring-indigo-500"
                        :class="!references.is_titimangsa_aktif ? 'bg-slate-50 text-slate-400 cursor-not-allowed' : 'bg-transparent text-indigo-800 focus:bg-white hover:bg-indigo-50/30'"
                        placeholder="-">
                    </td>

                    <!-- INPUT Ujian (Praktek, Teori) -->
                    <td v-for="ujiField in ['praktek', 'teori']" :key="ujiField" class="p-0 border-r border-slate-100 text-center h-12 relative">
                      <input type="text" v-model="getNilai(siswa.id)[ujiField]" @input="markAsUnsaved(siswa.id, ujiField)" :disabled="!references.is_titimangsa_aktif"
                        class="w-full h-full border-none p-0 text-center font-black text-xs transition-all focus:ring-inset focus:ring-2 focus:ring-teal-500"
                        :class="!references.is_titimangsa_aktif ? 'bg-slate-50 text-slate-400 cursor-not-allowed' : 'bg-transparent text-teal-800 focus:bg-white hover:bg-teal-50/30'"
                        placeholder="-">
                    </td>

                    <!-- NILAI PSTS LALU (Read Only) -->
                    <td v-if="!references.is_psts" class="p-0 border-r border-slate-100 text-center h-12 bg-amber-50/30">
                        <span class="font-black text-xs text-amber-700">{{ getNilai(siswa.id).psts_lalu || '-' }}</span>
                    </td>

                    <!-- INPUT Literasi -->
                    <td class="p-0 border-r border-slate-100 text-center h-12 relative">
                      <input type="text" v-model="getNilai(siswa.id).literasi" @input="markAsUnsaved(siswa.id, 'literasi')" :disabled="!references.is_titimangsa_aktif"
                        class="w-full h-full border-none p-0 text-center font-black text-xs transition-all focus:ring-inset focus:ring-2 focus:ring-slate-500"
                        :class="!references.is_titimangsa_aktif ? 'bg-slate-50 text-slate-400 cursor-not-allowed' : 'bg-transparent text-slate-700 focus:bg-white hover:bg-slate-100/50'"
                        placeholder="-">
                    </td>

                    <!-- HASIL NA (Computed Real-Time) -->
                    <td class="p-0 border-r border-slate-100 text-center h-12 bg-sky-50/50">
                        <span class="font-black text-sm text-sky-700 tracking-tight">{{ hitungNA(siswa.id) }}</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            
            <div class="p-3 bg-white border-t border-slate-100 flex items-center justify-between shrink-0 absolute bottom-0 left-0 right-0 z-30 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-1.5 bg-sky-500 rounded-full"></div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">
                        Pembagi Rata-Rata UH: {{ getMaxUh() }} Nilai Terbanyak
                    </p>
                </div>
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
  layout: "guru", 
  middleware: "guru",
  title: "Input Asessmen Sumatif"
})

const isLoading = ref(true)
const saveStatus = ref('idle')
const hasUnsavedChanges = ref(false)
const filter = ref({
  tahun_ajaran_id: '',
  kurikulum_id: '',
  titimangsa_id: '',
  mapel_id: '',
  kelas_id: ''
})

const references = ref({
  tahunAjarans: [],
  kurikulums: [],
  periodes: [],
  mapels: [],
  kelases: [],
  is_titimangsa_aktif: true,
  is_psts: false,
  jumlah_tp: 1
})

const globalBobot = ref({
    b_uh: 60,
    b_ujian: 40,
    b_praktek: 50,
    b_teori: 50,
    b_psts_lalu: 0
})

const getBobotLabel = (key) => {
    const labels = { b_uh: 'Harian', b_ujian: 'Ujian', b_praktek: 'Praktek', b_teori: 'Teori', b_psts_lalu: 'PSTS Lalu' }
    return labels[key] || key
}

const siswas = ref([])
const nilaiMatrix = ref([]) 

const isFilterComplete = computed(() => {
  return filter.value.tahun_ajaran_id && 
         filter.value.kurikulum_id && 
         filter.value.titimangsa_id && 
         filter.value.mapel_id && 
         filter.value.kelas_id
})

// === PORTAL FILTER ===
const handleFilterChange = async (key, event) => {
    const newVal = event.target.value
    if (hasUnsavedChanges.value) {
        const { $swal } = useNuxtApp()
        const result = await $swal.fire({
            title: 'Ada Nilai Belum Tersimpan!',
            text: 'Bapak/Ibu memiliki inputan yang belum tersimpan. Simpan sekarang secara manual sebelum pindah halaman?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Abaikan Data',
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#ef4444'
        })

        if (result.isConfirmed) {
            await executeManualSave()
            filter.value[key] = newVal
            fetchData()
        } else if (result.dismiss === $swal.DismissReason.cancel) {
            hasUnsavedChanges.value = false
            filter.value[key] = newVal
            fetchData()
        } else {
            event.target.value = filter.value[key]
        }
    } else {
        filter.value[key] = newVal
        fetchData()
    }
}

// === FETCH DATA ===
const fetchData = async () => {
  isLoading.value = true
  try {
    const token = useCookie('auth_token').value
    const queryParams = new URLSearchParams()
    Object.keys(filter.value).forEach(key => {
      if (filter.value[key]) queryParams.append(key, filter.value[key])
    })

    const res = await $fetch(`http://localhost:8000/api/guru/sumatif/nilai?${queryParams.toString()}`, {
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' }
    })

    if (res.success) {
      references.value.tahunAjarans = res.data.tahun_ajarans || []
      references.value.kurikulums = res.data.kurikulums || []
      references.value.periodes = res.data.periodes || []
      references.value.mapels = res.data.mapels || []
      references.value.kelases = res.data.kelases || []
      references.value.is_titimangsa_aktif = res.data.selections.is_titimangsa_aktif
      references.value.is_psts = res.data.selections.is_psts
      references.value.jumlah_tp = res.data.jumlah_tp || 1
      
      if (res.data.global_bobot) {
          globalBobot.value = { ...res.data.global_bobot }
      }
      
      if (!filter.value.tahun_ajaran_id) filter.value.tahun_ajaran_id = res.data.selections.tahun_ajaran_id
      if (!filter.value.kurikulum_id) filter.value.kurikulum_id = res.data.selections.kurikulum_id
      if (!filter.value.titimangsa_id) filter.value.titimangsa_id = res.data.selections.titimangsa_id

      siswas.value = res.data.siswas || []
      
      const serverNilai = res.data.nilai || []
      const uiMatrix = []
      
      siswas.value.forEach(s => {
          const n = serverNilai.find(x => x.siswa_id === s.id)
          const pLalu = res.data.psts_data[s.id] || null
          uiMatrix.push({
            siswa_id: s.id,
            uh1: n ? n.uh1 : '',
            uh2: n ? n.uh2 : '',
            uh3: n ? n.uh3 : '',
            uh4: n ? n.uh4 : '',
            praktek: n ? n.praktek : '',
            teori: n ? n.teori : '',
            literasi: n ? n.literasi : '',
            psts_lalu: n && n.psts_lalu !== null ? n.psts_lalu : pLalu,
          })
      })
      nilaiMatrix.value = uiMatrix
    }
  } catch (error) {
    console.error('Failed to fetch data:', error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => fetchData())

// === HELPER ===
const getNilai = (siswaId) => {
  return nilaiMatrix.value.find(n => n.siswa_id === siswaId) || {}
}

const safeNum = (val) => {
    if (val === null || val === undefined || val === '') return 0
    const numVal = parseFloat(val.toString().replace(',', '.'))
    return isNaN(numVal) ? 0 : numVal
}

const getMaxUh = () => {
    let max = 1
    siswas.value.forEach(s => {
        const row = getNilai(s.id)
        let count = 0
        if(safeNum(row.uh1) > 0) count++
        if(safeNum(row.uh2) > 0) count++
        if(safeNum(row.uh3) > 0) count++
        if(safeNum(row.uh4) > 0) count++
        if(count > max) max = count
    })
    return max
}

const hitungNA = (siswaId) => {
    const row = getNilai(siswaId)
    const bPrk = safeNum(globalBobot.value.b_praktek) / 100
    const bTeo = safeNum(globalBobot.value.b_teori) / 100
    const bUh = safeNum(globalBobot.value.b_uh) / 100
    const bUji = safeNum(globalBobot.value.b_ujian) / 100
    const bLalu = references.value.is_psts ? 0 : safeNum(globalBobot.value.b_psts_lalu) / 100
    
    const divider = getMaxUh()
    const totalUh = safeNum(row.uh1) + safeNum(row.uh2) + safeNum(row.uh3) + safeNum(row.uh4)
    const avgUh = totalUh / divider
    
    const nUjian = (safeNum(row.praktek) * bPrk) + (safeNum(row.teori) * bTeo)
    
    let na = (avgUh * bUh) + (nUjian * bUji) + (safeNum(row.psts_lalu) * bLalu) + safeNum(row.literasi)
    
    if (totalUh === 0 && nUjian === 0 && safeNum(row.literasi) === 0 && safeNum(row.psts_lalu) === 0) return '-'
    return na.toFixed(1)
}

// === MANUAL SAVE LOGIC ===
const markAsUnsaved = (siswaId = null, field = null) => {
    hasUnsavedChanges.value = true
    
    if (siswaId && field) {
        const cell = getNilai(siswaId)
        if(cell[field]) cell[field] = cell[field].toString().replace(/[^0-9.,]/g, '').replace(',', '.')
    }
}

const executeManualSave = async () => {
  if (!references.value.is_titimangsa_aktif || siswas.value.length === 0) return

  saveStatus.value = 'saving'
  
  try {
    const token = useCookie('auth_token').value
    
    const bulkData = nilaiMatrix.value.map(row => ({
      siswa_id: row.siswa_id,
      uh1: row.uh1, uh2: row.uh2, uh3: row.uh3, uh4: row.uh4,
      praktek: row.praktek, teori: row.teori, literasi: row.literasi,
      psts_lalu: row.psts_lalu,
    }))

    const payload = {
      tahun_ajaran_id: filter.value.tahun_ajaran_id,
      kurikulum_id: filter.value.kurikulum_id,
      titimangsa_id: filter.value.titimangsa_id,
      mapel_id: filter.value.mapel_id,
      kelas_id: filter.value.kelas_id,
      
      max_uh: getMaxUh(),
      
      b_uh: globalBobot.value.b_uh,
      b_ujian: globalBobot.value.b_ujian,
      b_praktek: globalBobot.value.b_praktek,
      b_teori: globalBobot.value.b_teori,
      b_psts_lalu: globalBobot.value.b_psts_lalu,
      
      data: bulkData
    }

    const res = await $fetch('http://localhost:8000/api/guru/sumatif/nilai/store', {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
      body: payload
    })

    if (res.success) {
      saveStatus.value = 'idle'
      hasUnsavedChanges.value = false
      const { $swal } = useNuxtApp()
      $swal.fire({
        title: 'Berhasil',
        text: 'Data Nilai Sumatif berhasil disimpan.',
        icon: 'success',
        toast: true,
        position: 'top-end',
        timer: 3000,
        showConfirmButton: false
      })
    }
  } catch (error) {
    console.error('Manual Save Error:', error)
    saveStatus.value = 'idle'
    const { $swal } = useNuxtApp()
    $swal.fire({
      title: 'Gagal Menyimpan!',
      text: 'Terjadi kesalahan saat menyimpan data. Cek koneksi atau hubungi admin.',
      icon: 'error',
      toast: true,
      position: 'top-end',
      timer: 3000,
      showConfirmButton: false
    })
  }
}

// Guard Router Leave
onBeforeRouteLeave((to, from, next) => {
    if (hasUnsavedChanges.value) {
        const answer = window.confirm('Bapak/Ibu memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman ini?')
        if (answer) {
            next()
        } else {
            next(false)
        }
    } else {
        next()
    }
})

</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #94a3b8; }
</style>
