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

            <!-- Periode (Titimangsa) -->
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
                <option v-for="kelas in references.kelases" :key="kelas.id" :value="kelas.id">{{ kelas.tingkat }} {{ kelas.nama_kelas }}</option>
              </select>
            </div>

            <!-- Mata Pelajaran -->
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Mata Pelajaran</label>
              <select :value="filter.mapel_id" @change="handleFilterChange('mapel_id', $event)" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-medium text-slate-700 outline-none" :disabled="isLoading || references.mapels.length === 0">
                <option value="">-- Pilih Mapel --</option>
                <option v-for="mapel in references.mapels" :key="mapel.id" :value="mapel.id">{{ mapel.nama_mapel }}</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- KANAN: WORKSPACE AREA (75%) -->
      <div class="lg:col-span-9">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col min-h-[400px]">
          
          <!-- Header Card Kanan -->
          <div class="px-6 py-4 bg-sky-600 border-b border-sky-700 flex justify-between items-center text-white shrink-0 shadow-sm z-10">
            <h3 class="text-sm font-black uppercase tracking-wide">Matriks Nilai Formatif</h3>
            
            <div class="flex items-center space-x-4" v-if="isFilterComplete && references.is_titimangsa_aktif">
              <button @click="executeManualSave" class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest px-4 py-2.5 bg-indigo-500 text-white rounded-lg shadow hover:bg-indigo-700 transition-all active:scale-95" :disabled="saveStatus === 'saving'">
                <span>{{ saveStatus === 'saving' ? 'Menyimpan...' : 'SIMPAN DATA' }}</span>
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

          <!-- List Workspace Area (MATRIKS) -->
          <div v-else class="flex-grow flex flex-col relative bg-slate-50/50">
            <div class="flex-1 overflow-x-auto custom-scrollbar relative">
              <table class="w-full text-left border-collapse bg-white table-fixed">
                <thead class="sticky top-0 z-20 shadow-sm">
                  <tr class="bg-slate-100 border-b border-slate-200">
                    <th class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500 sticky left-0 bg-slate-100 w-[200px]">Nama Siswa</th>
                    <th v-for="(tp, index) in tps" :key="tp.id" class="py-2 px-1 text-center w-[100px] border-r">
                      <span class="text-[10px] font-black text-sky-600">TP{{ index + 1 }}</span>
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-for="siswa in siswas" :key="siswa.id" class="hover:bg-sky-50/20">
                    <td class="py-2 px-4 sticky left-0 bg-white border-r">
                      <h4 class="text-[11px] font-black text-slate-700 truncate">{{ siswa.nama }}</h4>
                      <p class="text-[9px] font-bold text-slate-400">{{ siswa.nis }}</p>
                    </td>
                    <td v-for="tp in tps" :key="tp.id" class="p-0 border-r text-center h-12 relative group">
                      <input type="text" v-model="getNilai(siswa.id, tp.id).nilai" @input="markAsUnsaved(siswa.id, tp.id)" :disabled="!references.is_titimangsa_aktif"
                        class="w-full h-full border-none p-0 text-center font-black text-xs transition-all focus:ring-inset focus:ring-2 focus:ring-indigo-500 bg-transparent text-slate-700 focus:bg-white hover:bg-slate-50"
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

    const res = await $fetch(`http://localhost:8000/api/guru/formatif/nilai?${queryParams.toString()}`, {
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
}

const executeManualSave = async () => {
  saveStatus.value = 'saving'
  try {
    const token = useCookie('auth_token').value
    const res = await $fetch('http://localhost:8000/api/guru/formatif/nilai/store', {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}` },
      body: { ...filter.value, data: nilaiMatrix.value }
    })

    if (res.success) {
      saveStatus.value = 'idle'
      hasUnsavedChanges.value = false
      const { $swal } = useNuxtApp()
      $swal.fire({ icon: 'success', text: 'Berhasil disimpan', toast: true, position: 'top-end' })
    }
  } catch (e) {
    saveStatus.value = 'idle'
    const { $swal } = useNuxtApp()
    $swal.fire({ icon: 'error', text: 'Gagal' })
  }
}

onBeforeRouteLeave((to, from, next) => {
    if (hasUnsavedChanges.value) {
        if (confirm('Perubahan belum disimpan. Yakin ingin keluar?')) next()
        else next(false)
    } else next()
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #94a3b8; }
</style>
