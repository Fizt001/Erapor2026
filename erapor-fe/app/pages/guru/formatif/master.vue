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
            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Tentukan parameter TP</p>
          </div>
          
          <div class="p-6 space-y-5">
            <!-- Tahun Ajaran -->
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tahun Ajaran</label>
              <select v-model="filter.tahun_ajaran_id" @change="fetchData" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-medium text-slate-700 outline-none" :disabled="isLoading">
                <option v-for="ta in references.tahunAjarans" :key="ta.id" :value="ta.id">{{ ta.tahun }}</option>
              </select>
            </div>

            <!-- Kurikulum -->
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kurikulum</label>
              <select v-model="filter.kurikulum_id" @change="fetchData" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-medium text-slate-700 outline-none" :disabled="isLoading">
                <option v-for="kur in references.kurikulums" :key="kur.id" :value="kur.id">{{ kur.nama_kurikulum }}</option>
              </select>
            </div>

            <!-- Titimangsa -->
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Periode</label>
              <select v-model="filter.titimangsa_id" @change="fetchData" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-medium text-slate-700 outline-none" :disabled="isLoading">
                <option value="">-- Pilih Periode --</option>
                <option v-for="per in references.periodes" :key="per.id" :value="per.id">
                  {{ per.nama_periode }} {{ per.is_aktif ? '(Aktif)' : '(Ditutup)' }}
                </option>
              </select>
            </div>

            <!-- Kelas -->
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Rombel</label>
              <select v-model="filter.kelas_id" @change="() => { filter.mapel_id = ''; fetchData(); }" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-medium text-slate-700 outline-none" :disabled="isLoading">
                <option value="">-- Pilih Rombel --</option>
                <option v-for="kelas in references.kelases" :key="kelas.id" :value="kelas.id">{{ kelas.nama_kelas }}</option>
              </select>
            </div>

            <!-- Mapel -->
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Mapel</label>
              <select v-model="filter.mapel_id" @change="fetchData" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-medium text-slate-700 outline-none" :disabled="isLoading || !filter.kelas_id">
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
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col min-h-[600px]">
          
          <!-- Header Card Kanan -->
          <div class="px-6 py-4 bg-sky-600 border-b border-sky-700 flex justify-between items-center text-white shrink-0 shadow-sm z-10">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-lg shadow-inner">📝</div>
              <div>
                <h3 class="text-sm font-black leading-none uppercase tracking-wide">Daftar Elemen & TP</h3>
                <p class="text-[10px] font-bold text-sky-100 uppercase tracking-widest mt-1">Ketik langsung untuk menyimpan otomatis</p>
              </div>
            </div>
            
            <!-- Save Status Indicator -->
            <div v-if="isFilterComplete" class="flex items-center shrink-0">
              <div class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest px-4 py-2 bg-emerald-500/20 text-emerald-50 rounded-lg border border-emerald-400/50 shadow-inner transition-all" v-if="saveStatus === 'saved'">
                <span class="text-sm">✓</span> <span>Semua tersimpan</span>
              </div>
              <div class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest px-4 py-2 bg-amber-500/20 text-amber-50 rounded-lg border border-amber-400/50 shadow-inner transition-all" v-if="saveStatus === 'saving'">
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
            
            <div class="w-24 h-24 mb-6 rounded-full bg-sky-50 flex items-center justify-center border-8 border-white shadow-sm relative z-10">
                <span class="text-4xl animate-bounce" style="animation-duration: 3s;">📑</span>
            </div>
            
            <h3 class="font-black text-slate-700 text-xl tracking-tight relative z-10 mb-2">Pilih Filter Data</h3>
            <p class="text-sm font-semibold text-slate-500 max-w-md relative z-10 leading-relaxed">
                Silakan lengkapi parameter filter di panel sebelah kiri (Tahun Ajaran, Kurikulum, dll) untuk mulai menyusun Tujuan Pembelajaran.
            </p>
            
            <div class="mt-8 flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest relative z-10">
                <span class="animate-pulse text-lg">👈</span> Arahkan perhatian ke panel kiri
            </div>
          </div>

          <!-- Alert Periode Terkunci (Portal) -->
          <div v-else-if="!references.is_titimangsa_aktif" class="flex-grow flex items-center justify-center flex-col p-16 text-center bg-slate-50/50">
            <div class="text-6xl mb-6">🔒</div>
            <h3 class="text-lg font-black text-slate-800 uppercase tracking-widest">Periode Terkunci</h3>
            <p class="text-xs font-bold text-slate-500 mt-3 tracking-wide max-w-sm leading-relaxed">
              Periode ini berstatus nonaktif atau telah ditutup oleh Kurikulum. Input data dibekukan sementara.
            </p>
          </div>

          <!-- Loading Indicator -->
          <div v-else-if="isLoading" class="flex-grow flex flex-col items-center justify-center p-20 opacity-60">
            <div class="w-8 h-8 border-4 border-indigo-400 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</span>
          </div>

          <!-- List Workspace Area -->
          <div v-else class="flex-grow p-6 md:p-8 bg-slate-50/30">
            
            <div class="space-y-6">
              <div v-for="(elemen, eIndex) in elemens" :key="elemen.ui_id" class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden transition-all duration-300 hover:shadow-md">
                <!-- Elemen Header -->
                <div class="bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-700 p-5 flex items-start gap-4">
                  <div class="mt-1 w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-white font-black text-xs">E{{ eIndex + 1 }}</div>
                  <div class="flex-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 block">Nama Elemen Pembelajaran</label>
                    <input type="text" v-model="elemen.nama_elemen" @blur="autoSaveElemen(elemen)" placeholder="Ketik nama elemen (Misal: Bilangan / Menyimak...)" class="w-full text-base font-bold text-white border-0 bg-transparent px-0 py-1 focus:ring-0 placeholder-slate-500">
                  </div>
                  <button @click="hapusElemen(elemen, eIndex)" class="p-2.5 text-slate-400 hover:text-white hover:bg-rose-500 rounded-xl transition-colors mt-2" title="Hapus Elemen">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                  </button>
                </div>

                <!-- List TP -->
                <div class="p-6 bg-white">
                  <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 pl-10">Tujuan Pembelajaran (TP)</div>
                  
                  <div class="space-y-4">
                    <div v-for="(tp, tIndex) in elemen.tps" :key="tp.ui_id" class="flex items-start gap-4 group pl-10 relative">
                      <div class="absolute left-4 top-0 bottom-0 w-px bg-slate-200"></div>
                      <div class="absolute left-4 top-5 w-5 h-px bg-slate-200"></div>
                      
                      <div class="mt-2 text-xs font-black text-indigo-500 w-8 bg-indigo-50 rounded-md py-1 text-center border border-indigo-100">TP{{ tIndex + 1 }}</div>
                      
                      <div class="flex-1 bg-slate-50 border border-slate-200 rounded-xl p-2 focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all">
                        <textarea v-model="tp.nama_tp" @blur="autoSaveTp(tp, elemen)" rows="2" placeholder="Ketik deskripsi tujuan pembelajaran..." class="w-full text-sm font-semibold text-slate-700 border-0 bg-transparent px-3 py-2 focus:ring-0 resize-none placeholder-slate-400"></textarea>
                      </div>
                      
                      <button @click="hapusTp(tp, elemen, tIndex)" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-xl opacity-0 group-hover:opacity-100 transition-all mt-3 border border-transparent group-hover:border-rose-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                      </button>
                    </div>
                  </div>

                  <!-- Tombol Tambah TP -->
                  <button @click="tambahTp(elemen)" class="mt-5 ml-10 flex items-center text-[10px] font-black text-indigo-600 hover:text-white uppercase tracking-widest bg-indigo-50 hover:bg-indigo-600 px-4 py-2.5 rounded-xl transition-all border border-indigo-100 hover:border-indigo-600">
                    <span class="mr-2 text-sm">+</span> Tambah TP
                  </button>
                </div>
              </div>
            </div>

            <!-- Tombol Tambah Elemen -->
            <button @click="tambahElemen" class="mt-8 w-full flex items-center justify-center text-xs font-black text-slate-500 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 border-2 border-dashed border-slate-300 hover:border-slate-400 py-5 rounded-2xl transition-all uppercase tracking-widest">
              <span class="mr-2 text-lg">+</span> Tambah Elemen Pembelajaran Baru
            </button>

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

    const res = await $fetch(`http://localhost:8000/api/guru/formatif/master?${queryParams.toString()}`, {
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
    alert('Gagal memuat data master.')
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

    const res = await $fetch('http://localhost:8000/api/guru/formatif/master/elemen', {
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

    const res = await $fetch('http://localhost:8000/api/guru/formatif/master/tp', {
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
  if (!confirm('Hapus elemen ini? Semua TP di dalamnya akan ikut terhapus.')) return
  
  if (elemen.id) {
    saveStatus.value = 'saving'
    try {
      const token = useCookie('auth_token').value
      await $fetch(`http://localhost:8000/api/guru/formatif/master/elemen/${elemen.id}`, {
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
      alert('Gagal menghapus elemen dari server.')
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
      await $fetch(`http://localhost:8000/api/guru/formatif/master/tp/${tp.id}`, {
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
      alert('Gagal menghapus TP dari server.')
      return
    }
  }
  // Hapus dari UI
  elemen.tps.splice(index, 1)
}

</script>
