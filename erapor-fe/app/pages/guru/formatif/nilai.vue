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
              <p class="text-[10px] text-sky-100 font-semibold mt-0.5">Tentukan parameter Nilai Formatif</p>
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
              <select :value="filter.kurikulum_id" @change="handleFilterChange('kurikulum_id', $event)" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none" :disabled="isLoading || references.kurikulums.length === 0">
                <option v-if="references.kurikulums.length === 0" value="" disabled>Belum diseting</option>
                <option v-for="kur in references.kurikulums" :key="kur.id" :value="kur.id">{{ kur.nama_kurikulum }}</option>
              </select>
            </div>

            <!-- Periode (Titimangsa) -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Periode</label>
              <select :value="filter.titimangsa_id" @change="handleFilterChange('titimangsa_id', $event)" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none" :disabled="isLoading || references.periodes.length === 0">
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
              <select :value="filter.kelas_id" @change="handleFilterChange('kelas_id', $event)" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none" :disabled="isLoading || references.kelases.length === 0">
                <option v-if="references.kelases.length === 0" value="" disabled>Belum diseting</option>
                <option v-else value="">- Pilih Rombel -</option>
                <option v-for="kelas in references.kelases" :key="kelas.id" :value="kelas.id">{{ kelas.tingkat }} {{ kelas.nama_kelas }}</option>
              </select>
            </div>

            <!-- Mata Pelajaran -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Mata Pelajaran</label>
              <select :value="filter.mapel_id" @change="handleFilterChange('mapel_id', $event)" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none" :disabled="isLoading || !filter.kelas_id || references.mapels.length === 0">
                <option v-if="references.mapels.length === 0" value="" disabled>Belum diseting</option>
                <option v-else value="">- Pilih Mapel -</option>
                <option v-for="mapel in references.mapels" :key="mapel.id" :value="mapel.id">{{ mapel.nama_mapel }}</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative">
        <div class="p-6 lg:p-8 mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            
            <!-- Header Card Kanan -->
            <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-white shrink-0 z-10">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center text-lg border border-sky-100">📋</div>
                <div>
                  <h3 class="text-[13px] font-black leading-none uppercase tracking-wide text-slate-800">Matriks Nilai Formatif</h3>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Sistem otomatis menyimpan ketikan anda</p>
                </div>
              </div>
              
              <div class="flex items-center space-x-4" v-if="isFilterComplete && references.is_titimangsa_aktif">
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
                  <span class="text-4xl animate-bounce" style="animation-duration: 3s;">📝</span>
              </div>
              <h3 class="font-black text-slate-700 text-xl tracking-tight relative z-10 mb-2">Pilih Filter Data</h3>
              <p class="text-sm font-semibold text-slate-500 max-w-md relative z-10 leading-relaxed">
                  Matriks penilaian masih kosong. Silakan lengkapi parameter filter di panel sebelah kiri untuk memuat data siswa.
              </p>
              <div class="mt-8 flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest relative z-10">
                  <span class="animate-pulse text-lg">👈</span> Arahkan perhatian ke panel kiri
              </div>
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
              <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Memuat Matriks...</span>
            </div>

            <!-- List Workspace Area (MATRIKS) -->
            <div v-else class="flex-grow flex flex-col relative bg-slate-50/30 overflow-hidden">
              <div class="flex-1 overflow-auto custom-scrollbar relative">
                <table class="w-full text-left border-collapse bg-white table-fixed">
                  <thead class="sticky top-0 z-20 shadow-sm">
                    <tr class="bg-slate-100 border-b border-slate-200">
                      <th class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500 sticky left-0 bg-slate-100 w-[200px] z-30 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">Nama Siswa</th>
                      <th v-for="(tp, index) in tps" :key="tp.id" class="py-2 px-1 text-center w-[100px] border-r border-slate-200 bg-slate-50">
                        <span class="text-[10px] font-black text-sky-600 line-clamp-3 leading-tight px-1" :title="tp.nama_tp">{{ tp.nama_tp }}</span>
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    <tr v-for="siswa in siswas" :key="siswa.id" class="hover:bg-sky-50/30 transition-colors">
                      <td class="py-2 px-4 sticky left-0 bg-white border-r border-slate-100 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.02)] z-10 group-hover:bg-sky-50/10">
                        <h4 class="text-[11px] font-black text-slate-700 truncate">{{ siswa.nama }}</h4>
                        <p class="text-[9px] font-bold text-slate-400">{{ siswa.nis }}</p>
                      </td>
                      <td v-for="tp in tps" :key="tp.id" class="p-0 border-r border-slate-100 text-center h-12 relative group">
                        <input type="text" v-model="getNilai(siswa.id, tp.id).nilai" @input="markAsUnsaved(siswa.id, tp.id)" :disabled="!references.is_titimangsa_aktif"
                          class="w-full h-full border-none p-0 text-center font-black text-xs transition-all focus:ring-inset focus:ring-2 focus:ring-sky-500 bg-transparent text-slate-700 focus:bg-white hover:bg-slate-50/50"
                          placeholder="-">
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
import { ref, computed, onMounted } from 'vue'
import { useCookie, useNuxtApp } from '#app'
import { onBeforeRouteLeave } from 'vue-router'

definePageMeta({
  layout: 'guru',
  title: 'Input Asessmen Formatif',
  middleware: 'guru'
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
  kkm: null
})

const siswas = ref([])
const tps = ref([])
const nilaiMatrix = ref([])
let debounceTimer = null

const isFilterComplete = computed(() => {
  return filter.value.tahun_ajaran_id && 
         filter.value.kurikulum_id && 
         filter.value.titimangsa_id && 
         filter.value.mapel_id && 
         filter.value.kelas_id
})

const handleFilterChange = async (key, event) => {
    const newVal = event.target.value
    if (hasUnsavedChanges.value) {
        const { $swal } = useNuxtApp()
        const result = await $swal.fire({
            title: 'Ada Nilai Belum Tersimpan!',
            text: 'Simpan perubahan sebelum pindah?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Abaikan'
        })

        if (result.isConfirmed) {
            clearTimeout(debounceTimer)
            await executeManualSave()
            filter.value[key] = newVal
            if (key === 'kelas_id') filter.value.mapel_id = ''
            fetchData()
        } else if (result.dismiss === $swal.DismissReason.cancel) {
            hasUnsavedChanges.value = false
            filter.value[key] = newVal
            if (key === 'kelas_id') filter.value.mapel_id = ''
            fetchData()
        } else {
            event.target.value = filter.value[key]
        }
    } else {
        filter.value[key] = newVal
        if (key === 'kelas_id') filter.value.mapel_id = ''
        fetchData()
    }
}

const fetchData = async () => {
  isLoading.value = true
  try {
    const token = useCookie('auth_token').value
    const queryParams = new URLSearchParams()
    Object.keys(filter.value).forEach(key => { if (filter.value[key]) queryParams.append(key, filter.value[key]) })

    const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/guru/formatif/nilai?${queryParams.toString()}`, {
      headers: { Authorization: `Bearer ${token}` }
    })

    if (res.success) {
      references.value.tahunAjarans = res.data.tahun_ajarans || []
      references.value.kurikulums = res.data.kurikulums || []
      references.value.periodes = res.data.periodes || []
      references.value.mapels = res.data.mapels || []
      references.value.kelases = res.data.kelases || []
      references.value.is_titimangsa_aktif = res.data.selections.is_titimangsa_aktif
      references.value.kkm = res.data.selections.kkm

      if (!filter.value.tahun_ajaran_id) filter.value.tahun_ajaran_id = res.data.selections.tahun_ajaran_id
      if (!filter.value.kurikulum_id) filter.value.kurikulum_id = res.data.selections.kurikulum_id
      if (!filter.value.titimangsa_id) filter.value.titimangsa_id = res.data.selections.titimangsa_id

      siswas.value = res.data.siswas || []
      tps.value = res.data.tps || []
      
      const serverNilai = res.data.nilai || []
      nilaiMatrix.value = siswas.value.flatMap(s => tps.value.map(t => {
        const n = serverNilai.find(x => x.siswa_id === s.id && x.formatif_tp_id === t.id)
        return { siswa_id: s.id, tp_id: t.id, nilai: n ? n.nilai : '' }
      }))
    }
  } catch (e) { console.error(e) } finally { isLoading.value = false }
}

onMounted(() => fetchData())

const getNilai = (siswaId, tpId) => {
  return nilaiMatrix.value.find(n => n.siswa_id === siswaId && n.tp_id === tpId) || { nilai: '' }
}

const markAsUnsaved = (siswaId, tpId) => {
    hasUnsavedChanges.value = true
    const cell = getNilai(siswaId, tpId)
    cell.nilai = cell.nilai.toString().replace(/[^0-9.,]/g, '').replace(',', '.')
    if (parseFloat(cell.nilai) > 100) cell.nilai = '100'

    // Debounce Auto-Save Logic
    saveStatus.value = 'saving'
    clearTimeout(debounceTimer)
    debounceTimer = setTimeout(() => {
        executeManualSave()
    }, 1000)
}

const executeManualSave = async () => {
  saveStatus.value = 'saving'
  try {
    const token = useCookie('auth_token').value
    const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/guru/formatif/nilai/store', {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}` },
      body: { ...filter.value, data: nilaiMatrix.value }
    })

    if (res.success) {
      saveStatus.value = 'saved'
      hasUnsavedChanges.value = false
      setTimeout(() => { if(saveStatus.value === 'saved') saveStatus.value = 'idle' }, 2000)
    }
  } catch (e) {
    saveStatus.value = 'idle'
    const { $swal } = useNuxtApp()
    $swal.fire({ icon: 'error', text: 'Gagal', toast: true, position: 'top-end' })
  }
}

onBeforeRouteLeave((to, from, next) => {
    if (hasUnsavedChanges.value) {
        useSwal().fire({
            title: 'Yakin ingin keluar?',
            text: 'Perubahan belum disimpan. Yakin ingin keluar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Tinggalkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) next()
            else next(false)
        })
    } else next()
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #94a3b8; }
</style>
