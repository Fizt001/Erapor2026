<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex flex-col xl:flex-row overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="xl:w-[360px] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] overflow-y-auto custom-scrollbar">
        <div class="p-6 space-y-6">
          <div class="bg-gradient-to-r from-sky-600 to-blue-700 rounded-2xl p-5 border border-sky-500 shadow-sm relative overflow-hidden">
            <div class="relative z-10">
              <h3 class="text-sm font-black uppercase tracking-widest text-white">Filter Data</h3>
              <p class="text-[10px] text-sky-100 font-semibold mt-0.5">Tentukan parameter Tujuan Pembelajaran</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-20 text-white">
              <svg class="w-24 h-24 transform translate-x-6 translate-y-6" fill="currentColor" viewBox="0 0 24 24"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
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
              <select v-model="filter.kurikulum_id" @change="fetchData" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none" :disabled="isLoading || references.kurikulums.length === 0">
                <option v-if="references.kurikulums.length === 0" value="" disabled>Belum diseting</option>
                <option v-for="kur in references.kurikulums" :key="kur.id" :value="kur.id">{{ kur.nama_kurikulum }}</option>
              </select>
            </div>

            <!-- Titimangsa -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Periode</label>
              <select v-model="filter.titimangsa_id" @change="fetchData" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none" :disabled="isLoading || references.periodes.length === 0">
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
              <select v-model="filter.kelas_id" @change="() => { filter.mapel_id = ''; fetchData(); }" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none" :disabled="isLoading || references.kelases.length === 0">
                <option v-if="references.kelases.length === 0" value="" disabled>Belum diseting</option>
                <option v-else value="">- Pilih Rombel -</option>
                <option v-for="kelas in references.kelases" :key="kelas.id" :value="kelas.id">{{ kelas.tingkat }} {{ kelas.nama_kelas }}</option>
              </select>
            </div>

            <!-- Mapel -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Mata Pelajaran</label>
              <select v-model="filter.mapel_id" @change="fetchData" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none" :disabled="isLoading || !filter.kelas_id || references.mapels.length === 0">
                <option v-if="references.mapels.length === 0" value="" disabled>Belum diseting</option>
                <option v-else value="">- Pilih Mapel -</option>
                <option v-for="mapel in references.mapels" :key="mapel.id" :value="mapel.id">{{ mapel.nama_mapel }}</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0 overflow-y-auto custom-scrollbar relative">
        <div class="p-6 lg:p-8 max-w-5xl mx-auto w-full h-full flex flex-col">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative">
            
            <!-- Header Card Kanan -->
            <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-white shrink-0 z-10">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center text-lg border border-sky-100">📝</div>
                <div>
                  <h3 class="text-[13px] font-black leading-none uppercase tracking-wide text-slate-800">Daftar Elemen & TP</h3>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Ketik langsung untuk menyimpan otomatis</p>
                </div>
              </div>
              
              <!-- Save Status Indicator -->
              <div v-if="isFilterComplete" class="flex items-center shrink-0">
                <div class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-lg border border-emerald-200 transition-all" v-if="saveStatus === 'saved'">
                  <span class="text-sm">✓</span> <span>Tersimpan</span>
                </div>
                <div class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest px-3 py-1.5 bg-amber-50 text-amber-600 rounded-lg border border-amber-200 transition-all" v-if="saveStatus === 'saving'">
                  <span class="animate-spin text-sm">↻</span> <span>Menyimpan...</span>
                </div>
              </div>
            </div>

            <!-- Alert Belum Lengkap -->
            <div v-if="!isFilterComplete" class="flex-grow flex flex-col items-center justify-center p-16 text-center relative overflow-hidden bg-slate-50/50">
              <div class="w-24 h-24 mb-6 rounded-full bg-sky-50 flex items-center justify-center border-8 border-white shadow-sm relative z-10">
                  <span class="text-4xl animate-bounce" style="animation-duration: 3s;">📑</span>
              </div>
              <h3 class="font-black text-slate-700 text-xl tracking-tight relative z-10 mb-2">Pilih Filter Data</h3>
              <p class="text-sm font-semibold text-slate-500 max-w-md relative z-10 leading-relaxed">
                  Silakan lengkapi parameter filter di panel sebelah kiri (Tahun Ajaran, Kurikulum, dll) untuk mulai menyusun Tujuan Pembelajaran.
              </p>
            </div>

            <!-- Alert Periode Terkunci -->
            <div v-else-if="!references.is_titimangsa_aktif" class="flex-grow flex items-center justify-center flex-col p-16 text-center bg-slate-50/50">
              <div class="text-6xl mb-6">🔒</div>
              <h3 class="text-lg font-black text-slate-800 uppercase tracking-widest">Periode Terkunci</h3>
              <p class="text-xs font-bold text-slate-500 mt-3 tracking-wide max-w-sm leading-relaxed">
                Periode ini berstatus nonaktif atau telah ditutup oleh Kurikulum. Input data dibekukan sementara.
              </p>
            </div>

            <!-- Loading Indicator -->
            <div v-else-if="isLoading" class="flex-grow flex flex-col items-center justify-center p-20 opacity-60">
              <div class="w-8 h-8 border-4 border-sky-400 border-t-transparent rounded-full animate-spin mb-4"></div>
              <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Memuat Data...</span>
            </div>

            <!-- List Workspace Area -->
            <div v-else class="flex-grow p-6 bg-slate-50/30 overflow-y-auto custom-scrollbar">
              <div class="space-y-6">
                <div v-for="(elemen, eIndex) in elemens" :key="elemen.ui_id" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden transition-all duration-300 hover:border-sky-200 hover:shadow-md group/elemen">
                  
                  <!-- Elemen Header -->
                  <div class="bg-gradient-to-r from-sky-600 to-sky-700 border-b border-sky-600 p-4 sm:p-5 flex items-start gap-4">
                    <div class="mt-1 w-8 h-8 rounded-xl bg-white/20 border border-white/30 flex items-center justify-center text-white font-black text-xs shrink-0 shadow-inner">E{{ eIndex + 1 }}</div>
                    <div class="flex-1 min-w-0">
                      <label class="text-[9px] font-black text-sky-200 uppercase tracking-widest mb-1.5 block">Nama Elemen Pembelajaran</label>
                      <input type="text" v-model="elemen.nama_elemen" @blur="autoSaveElemen(elemen)" placeholder="Ketik nama elemen (Misal: Bilangan / Menyimak...)" class="w-full text-sm sm:text-base font-black text-white border-0 bg-transparent px-0 py-1 focus:ring-0 placeholder-sky-300">
                    </div>
                    <button @click="hapusElemen(elemen, eIndex)" class="p-2 text-sky-200 hover:text-white hover:bg-rose-500 rounded-xl transition-colors mt-2 opacity-0 group-hover/elemen:opacity-100 shrink-0" title="Hapus Elemen">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                    </button>
                  </div>

                  <!-- List TP -->
                  <div class="p-5 sm:p-6 bg-white relative">
                    <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-4 pl-12">Tujuan Pembelajaran (TP)</div>
                    
                    <div class="space-y-4">
                      <div v-for="(tp, tIndex) in elemen.tps" :key="tp.ui_id" class="flex items-start gap-4 group pl-12 relative">
                        <div class="absolute left-6 top-0 bottom-0 w-px bg-slate-200"></div>
                        <div class="absolute left-6 top-4 w-4 h-px bg-slate-200"></div>
                        
                        <div class="mt-1.5 text-[10px] font-black text-sky-600 w-8 bg-sky-50 rounded-lg py-1 text-center border border-sky-100 shrink-0">TP{{ tIndex + 1 }}</div>
                        
                        <div class="flex-1 bg-slate-50 border border-slate-200 rounded-xl p-2 focus-within:border-sky-400 focus-within:bg-white focus-within:ring-4 focus-within:ring-sky-500/10 transition-all">
                          <textarea v-model="tp.nama_tp" @blur="autoSaveTp(tp, elemen)" rows="2" placeholder="Ketik deskripsi tujuan pembelajaran..." class="w-full text-xs sm:text-sm font-semibold text-slate-700 border-0 bg-transparent px-3 py-2 focus:ring-0 resize-none placeholder-slate-400"></textarea>
                        </div>
                        
                        <button @click="hapusTp(tp, elemen, tIndex)" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl opacity-0 group-hover:opacity-100 transition-all mt-2.5 border border-transparent group-hover:border-rose-100 shrink-0">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                      </div>
                    </div>

                    <!-- Tombol Tambah TP -->
                    <button @click="tambahTp(elemen)" class="mt-4 ml-12 flex items-center text-[10px] font-black text-sky-600 hover:text-white uppercase tracking-widest bg-sky-50 hover:bg-sky-500 px-4 py-2 rounded-xl transition-all border border-sky-100 hover:border-sky-500 active:scale-95">
                      <span class="mr-2 text-sm leading-none">+</span> Tambah TP
                    </button>
                  </div>
                </div>
              </div>

              <!-- Tombol Tambah Elemen -->
              <button @click="tambahElemen" class="mt-8 w-full flex items-center justify-center text-[11px] font-black text-slate-500 hover:text-sky-600 bg-slate-50 hover:bg-sky-50 border-2 border-dashed border-slate-300 hover:border-sky-300 py-4 rounded-2xl transition-all uppercase tracking-widest active:scale-95">
                <span class="mr-2 text-lg leading-none">+</span> Tambah Elemen Pembelajaran Baru
              </button>
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
  title: "Data TP Formatif"
})

const isLoading = ref(true)
const saveStatus = ref('idle') // idle, saving, saved
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
  is_titimangsa_aktif: true
})

const elemens = ref([])

// Unique ID Generator for UI tracking
const generateUid = () => Math.random().toString(36).substring(2, 9)

const isFilterComplete = computed(() => {
  return filter.value.tahun_ajaran_id && 
         filter.value.kurikulum_id && 
         filter.value.titimangsa_id && 
         filter.value.mapel_id && 
         filter.value.kelas_id
})

// === FETCH DATA ===
const fetchData = async () => {
  isLoading.value = true
  try {
    const token = useCookie('auth_token').value
    // Build query params
    const queryParams = new URLSearchParams()
    Object.keys(filter.value).forEach(key => {
      if (filter.value[key]) queryParams.append(key, filter.value[key])
    })

    const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/guru/formatif/master?${queryParams.toString()}`, {
      headers: { 
        Authorization: `Bearer ${token}`,
        Accept: 'application/json'
      }
    })

    if (res.success) {
      references.value.tahunAjarans = res.data.tahun_ajarans || []
      references.value.kurikulums = res.data.kurikulums || []
      references.value.periodes = res.data.periodes || []
      references.value.mapels = res.data.mapels || []
      references.value.kelases = res.data.kelases || []
      references.value.is_titimangsa_aktif = res.data.selections.is_titimangsa_aktif

      
      // Update filter selects if they were empty initially
      if (!filter.value.tahun_ajaran_id) filter.value.tahun_ajaran_id = res.data.selections.tahun_ajaran_id
      if (!filter.value.kurikulum_id) filter.value.kurikulum_id = res.data.selections.kurikulum_id
      if (!filter.value.titimangsa_id) filter.value.titimangsa_id = res.data.selections.titimangsa_id

      // Inject UI ID for reactivity tracking
      const mappedElemens = (res.data.elemens || []).map(e => ({
        ...e,
        ui_id: generateUid(),
        tps: (e.tps || []).map(t => ({ ...t, ui_id: generateUid() }))
      }))
      
      elemens.value = mappedElemens
    }
  } catch (error) {
    console.error('Failed to fetch data:', error)
    displayToast('Gagal memuat data master.', 'error')
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchData()
})

// === UI ACTIONS ===
const tambahElemen = () => {
  elemens.value.push({
    id: null,
    ui_id: generateUid(),
    nama_elemen: '',
    tps: []
  })
  // Otomatis tambah 1 TP kosong
  tambahTp(elemens.value[elemens.value.length - 1])
}

const tambahTp = (elemen) => {
  elemen.tps.push({
    id: null,
    ui_id: generateUid(),
    nama_tp: ''
  })
}

// === AUTO SAVE LOGIC ===
const autoSaveElemen = async (elemen) => {
  if (!elemen.nama_elemen || elemen.nama_elemen.trim() === '') return
  if (!isFilterComplete.value) return

  saveStatus.value = 'saving'
  try {
    const token = useCookie('auth_token').value
    const payload = {
      id: elemen.id,
      tahun_ajaran_id: filter.value.tahun_ajaran_id,
      kurikulum_id: filter.value.kurikulum_id,
      titimangsa_id: filter.value.titimangsa_id,
      mapel_id: filter.value.mapel_id,
      kelas_id: filter.value.kelas_id,
      nama_elemen: elemen.nama_elemen
    }

    const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/guru/formatif/master/elemen', {
      method: 'POST',
      headers: { 
        Authorization: `Bearer ${token}`,
        Accept: 'application/json'
      },
      body: payload
    })

    if (res.success) {
      elemen.id = res.data.id // Update ID from DB
      saveStatus.value = 'saved'
      setTimeout(() => saveStatus.value = 'idle', 2000)
    }
  } catch (error) {
    console.error('AutoSave Elemen Error:', error)
    saveStatus.value = 'idle'
  }
}

const autoSaveTp = async (tp, elemen) => {
  if (!tp.nama_tp || tp.nama_tp.trim() === '') return
  if (!elemen.id) {
    // Jika elemen induk belum disave/gak ada id, paksa save elemen dulu
    await autoSaveElemen(elemen)
    if (!elemen.id) return // Gagal save elemen
  }

  saveStatus.value = 'saving'
  try {
    const token = useCookie('auth_token').value
    const payload = {
      id: tp.id,
      formatif_master_id: elemen.id,
      nama_tp: tp.nama_tp
    }

    const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/guru/formatif/master/tp', {
      method: 'POST',
      headers: { 
        Authorization: `Bearer ${token}`,
        Accept: 'application/json'
      },
      body: payload
    })

    if (res.success) {
      tp.id = res.data.id // Update ID from DB
      saveStatus.value = 'saved'
      setTimeout(() => saveStatus.value = 'idle', 2000)
    }
  } catch (error) {
    console.error('AutoSave TP Error:', error)
    saveStatus.value = 'idle'
  }
}

// === DELETE LOGIC ===
const hapusElemen = async (elemen, index) => {
  const answer = await useSwal().fire({
      title: 'Hapus elemen ini?',
      text: 'Semua TP di dalamnya akan ikut terhapus.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#94a3b8',
      confirmButtonText: 'Ya, Hapus',
      cancelButtonText: 'Batal'
  })
  if (!answer.isConfirmed) return
  
  if (elemen.id) {
    saveStatus.value = 'saving'
    try {
      const token = useCookie('auth_token').value
      await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/guru/formatif/master/elemen/${elemen.id}`, {
        method: 'DELETE',
        headers: { 
          Authorization: `Bearer ${token}`,
          Accept: 'application/json'
        }
      })
      saveStatus.value = 'saved'
      setTimeout(() => saveStatus.value = 'idle', 2000)
    } catch (error) {
      console.error('Delete Elemen Error:', error)
      saveStatus.value = 'idle'
      displayToast('Gagal menghapus elemen dari server.', 'error')
      return
    }
  }
  // Hapus dari UI
  elemens.value.splice(index, 1)
}

const hapusTp = async (tp, elemen, index) => {
  if (tp.id) {
    saveStatus.value = 'saving'
    try {
      const token = useCookie('auth_token').value
      await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/guru/formatif/master/tp/${tp.id}`, {
        method: 'DELETE',
        headers: { 
          Authorization: `Bearer ${token}`,
          Accept: 'application/json'
        }
      })
      saveStatus.value = 'saved'
      setTimeout(() => saveStatus.value = 'idle', 2000)
    } catch (error) {
      console.error('Delete TP Error:', error)
      saveStatus.value = 'idle'
      displayToast('Gagal menghapus TP dari server.', 'error')
      return
    }
  }
  // Hapus dari UI
  elemen.tps.splice(index, 1)
}

</script>
