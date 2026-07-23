<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="xl:w-[360px] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] overflow-y-auto custom-scrollbar">
        
        <!-- Mobile Tabs (Above Blue Card) -->
        <div class="xl:hidden p-6 pb-0 shrink-0">
          <div class="flex bg-slate-100 p-1 rounded-xl">
             <button @click="activeMobileTab = 'filter'" :class="activeMobileTab === 'filter' ? 'bg-white text-sky-600 shadow-sm' : 'text-slate-500 hover:bg-slate-200'" class="flex-1 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">Filter</button>
             <button @click="activeMobileTab = 'pertemuan'" :class="activeMobileTab === 'pertemuan' ? 'bg-white text-sky-600 shadow-sm' : 'text-slate-500 hover:bg-slate-200'" class="flex-1 py-2.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">Pertemuan</button>
          </div>
        </div>

        <div class="p-4 pb-2 space-y-4" :class="{'hidden xl:block': activeMobileTab !== 'filter'}">
          <div class="bg-gradient-to-r from-sky-600 to-blue-700 rounded-2xl p-4 border border-sky-500 shadow-sm relative overflow-hidden flex items-center gap-3">
            <div class="w-8 h-8 flex items-center justify-center shrink-0 bg-white/10 rounded-lg relative z-10 text-white"><AppIcon name="document-text" class="w-5 h-5" /></div>
            <div class="relative z-10">
                <h3 class="text-xs font-black uppercase tracking-widest text-white">Absensi Kelas</h3>
                <p class="text-[9px] text-sky-100 font-semibold uppercase mt-0.5">Input Kehadiran Per Mapel</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-15 text-white pointer-events-none">
              <svg class="w-16 h-16 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            </div>
          </div>
          
          <div class="space-y-4">
            
            <!-- Kurikulum -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kurikulum</label>
              <select v-model="filter.kurikulum_id" @change="handleFilterChange('kurikulum_id')" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none" :disabled="isLoading || references.kurikulums.length === 0">
                <option v-if="references.kurikulums.length === 0" value="" disabled>Belum diseting</option>
                <option v-for="kur in references.kurikulums" :key="kur.id" :value="kur.id">{{ kur.nama_kurikulum }}</option>
              </select>
            </div>

            <!-- Periode (Titimangsa) -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Periode</label>
              <select v-model="filter.titimangsa_id" @change="handleFilterChange('titimangsa_id')" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none" :disabled="isLoading || references.periodes.length === 0">
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
              <select v-model="filter.kelas_id" @change="handleFilterChange('kelas_id')" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none" :disabled="isLoading || references.kelases.length === 0">
                <option v-if="references.kelases.length === 0" value="" disabled>Belum diseting</option>
                <option v-else value="">- Pilih Rombel -</option>
                <option v-for="kelas in references.kelases" :key="kelas.id" :value="kelas.id">{{ kelas.tingkat }} {{ kelas.nama_kelas }}</option>
              </select>
            </div>

            <!-- Mata Pelajaran -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Mata Pelajaran</label>
              <select v-model="filter.mapel_id" @change="handleFilterChange('mapel_id')" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none" :disabled="isLoading || !filter.kelas_id || references.mapels.length === 0">
                <option v-if="references.mapels.length === 0" value="" disabled>Belum diseting</option>
                <option v-else value="">- Pilih Mapel -</option>
                <option v-for="mapel in references.mapels" :key="mapel.id" :value="mapel.id">{{ mapel.nama_mapel }}</option>
              </select>
            </div>
            
            <!-- Tombol Tambah Pertemuan -->
            <button v-if="isFilterComplete" @click="openAddModal" class="w-full py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                + Tambah Pertemuan
            </button>
          </div>
        </div>

        <div class="flex-1 p-4 bg-slate-50 border-t border-slate-200" :class="{'hidden xl:block': activeMobileTab !== 'pertemuan'}">
             <div v-if="isFilterComplete" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">Daftar Pertemuan</div>
             <div v-if="isLoadingPertemuan" class="text-xs text-slate-400 text-center">Memuat pertemuan...</div>
             <div v-else-if="pertemuans.length === 0 && isFilterComplete" class="text-xs text-slate-400 text-center p-4 bg-white rounded-xl border border-slate-200 border-dashed">Belum ada pertemuan.</div>
             <div v-else class="space-y-2">
                <div v-for="(pert, index) in pertemuans" :key="pert.id" class="p-3 bg-white border border-slate-200 rounded-xl flex items-center justify-between shadow-sm">
                    <div>
                        <div class="text-xs font-black text-slate-700">Prt. {{ pertemuans.length - index }}</div>
                        <div class="text-[10px] text-slate-500 font-bold mt-0.5">{{ formatDate(pert.tanggal) }} (Jam {{ pert.jam_mulai }}-{{ pert.jam_selesai }})</div>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button @click="editPertemuan(pert)" class="w-7 h-7 flex items-center justify-center text-sky-500 hover:text-sky-600 bg-sky-50 hover:bg-sky-100 rounded-lg transition-colors"><AppIcon name="pencil" class="w-3.5 h-3.5" /></button>
                        <button @click="deletePertemuan(pert.id)" class="w-7 h-7 flex items-center justify-center text-rose-500 hover:text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors"><AppIcon name="trash" class="w-3.5 h-3.5" /></button>
                    </div>
                </div>
             </div>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            <!-- Header -->
            <div class="px-6 py-5 bg-white border-b border-slate-200 flex justify-between items-center shrink-0">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-500 text-xl">📋</div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-slate-700">Data Kehadiran Siswa</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">
                            {{ selectedPengampuName || 'Lengkapi filter terlebih dahulu' }}
                        </p>
                    </div>
                </div>
                <button v-if="isFilterComplete && pertemuans.length > 0" @click="simpanAbsensi" :disabled="isSaving" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 disabled:opacity-50 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-lg shadow-sky-500/20">
                    <span v-if="isSaving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    <span>Simpan Perubahan</span>
                </button>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-hidden bg-slate-50 p-6 flex flex-col">
                <div v-if="!isFilterComplete" class="bg-white rounded-2xl p-20 border-2 border-dashed border-slate-200 text-center h-full flex flex-col items-center justify-center">
                    <div class="text-6xl opacity-20 mb-4">👈</div>
                    <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest">Silakan pilih Kelas & Mapel</h3>
                </div>
                <div v-else-if="isLoadingSiswa" class="bg-white rounded-2xl p-20 border border-slate-200 text-center h-full flex flex-col items-center justify-center opacity-60">
                    <div class="w-8 h-8 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                    <span class="text-xs font-black text-slate-500 uppercase tracking-widest">Memuat...</span>
                </div>
                <div v-else-if="pertemuans.length === 0" class="bg-white rounded-2xl p-20 border border-slate-200 text-center h-full flex flex-col items-center justify-center">
                    <div class="text-5xl mb-4">📅</div>
                    <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest">Belum ada pertemuan</h3>
                    <p class="text-xs text-slate-500 font-bold mt-2">Klik tombol Tambah Pertemuan di sebelah kiri.</p>
                </div>
                <div v-else class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col h-full">
                    <div class="overflow-auto flex-grow custom-scrollbar">
                        <table class="w-full text-left border-collapse text-[10px] sm:text-xs">
                            <thead class="sticky top-0 z-20">
                                <tr class="bg-slate-100 text-[9px] font-black uppercase tracking-widest text-slate-500">
                                    <th class="p-3 sticky left-0 bg-slate-100 border-b border-r border-slate-200 min-w-[200px] z-30">Nama Siswa</th>
                                    <th v-for="(pert, index) in reversedPertemuans" :key="pert.id" class="p-2 border-b border-r border-slate-200 text-center min-w-[60px] group relative">
                                        <div>Prt. {{ index + 1 }}</div>
                                        <div class="text-[8px] mt-1 font-bold text-slate-400">{{ formatDateShort(pert.tanggal) }}</div>
                                        <div class="absolute inset-x-0 top-0 hidden group-hover:flex items-center justify-center space-x-1 bg-slate-100/90 py-1 border-b border-slate-200">
                                            <button @click="editPertemuan(pert)" class="text-sky-500 hover:text-sky-600 transition-colors" title="Edit Pertemuan">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </button>
                                            <button @click="deletePertemuan(pert.id)" class="text-rose-500 hover:text-rose-600 transition-colors" title="Hapus Pertemuan">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="siswa in siswas" :key="siswa.siswa_id" class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="p-3 sticky left-0 bg-white border-r border-slate-100 font-bold text-slate-700 whitespace-nowrap overflow-hidden text-ellipsis max-w-[200px] z-10">{{ siswa.nama }}</td>
                                    <td v-for="pert in reversedPertemuans" :key="pert.id" class="p-1 border-r border-slate-50 text-center min-w-[60px]">
                                        <button @click="toggleAbsen(siswa.siswa_id, pert.id)" 
                                            class="w-8 h-8 rounded-lg font-black transition-colors mx-auto flex items-center justify-center"
                                            :class="getColorClass(getAbsenValue(siswa.siswa_id, pert.id))">
                                            {{ getAbsenValue(siswa.siswa_id, pert.id) }}
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Panduan -->
            <div class="px-6 py-4 bg-white border-t border-slate-200 shrink-0 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Panduan Warna Status</h4>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[10px] font-bold text-slate-600">
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-slate-100 text-slate-400 flex items-center justify-center border border-slate-200 text-[8px]">-</span> Kosong (Hadir)</div>
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-emerald-100 text-emerald-600 flex items-center justify-center border border-emerald-200 text-[8px]">H</span> Hadir</div>
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-amber-100 text-amber-600 flex items-center justify-center border border-amber-200 text-[8px]">S</span> Sakit</div>
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-sky-100 text-sky-600 flex items-center justify-center border border-sky-200 text-[8px]">I</span> Izin</div>
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-rose-100 text-rose-600 flex items-center justify-center border border-rose-200 text-[8px]">A</span> Alpa</div>
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-md bg-indigo-100 text-indigo-600 flex items-center justify-center border border-indigo-200 text-[8px]">P</span> PKL</div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Add Pertemuan -->
    <div v-if="showAddModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl animate-fade-in-up">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">{{ formPertemuan.id ? 'Edit Pertemuan' : 'Tambah Pertemuan' }}</h3>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Tanggal Pertemuan</label>
                    <input type="date" v-model="formPertemuan.tanggal" @change="fetchLastJam" class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 font-bold text-slate-700 text-xs outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Jam Ke-Mulai</label>
                        <input type="number" min="1" max="12" v-model="formPertemuan.jam_mulai" class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 font-bold text-slate-700 text-xs outline-none text-center">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Jam Ke-Selesai</label>
                        <input type="number" min="1" max="12" v-model="formPertemuan.jam_selesai" class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 font-bold text-slate-700 text-xs outline-none text-center">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Materi Pokok (Opsional)</label>
                    <input type="text" v-model="formPertemuan.materi" placeholder="Cth: Bab 1 Pendahuluan" class="w-full px-4 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 font-bold text-slate-700 text-xs outline-none">
                </div>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button @click="showAddModal = false" class="px-5 py-2.5 text-xs font-black uppercase tracking-widest text-slate-500 hover:text-slate-700">Batal</button>
                <button @click="submitPertemuan" :disabled="isSubmitting" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 disabled:opacity-50 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-sky-500/20">
                    {{ isSubmitting ? 'Menyimpan...' : 'Simpan Pertemuan' }}
                </button>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'

definePageMeta({
    layout: 'guru',
    middleware: 'guru'
})

const isLoading = ref(true)
const activeMobileTab = ref('filter')

const swal = useSwal()
const token = useCookie('auth_token')

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
})

const isFilterComplete = computed(() => {
  return filter.value.tahun_ajaran_id && 
         filter.value.kurikulum_id && 
         filter.value.titimangsa_id && 
         filter.value.mapel_id && 
         filter.value.kelas_id
})

const selectedPengampuName = computed(() => {
    if (!isFilterComplete.value) return ''
    const kelas = references.value.kelases.find(k => k.id === filter.value.kelas_id)
    const mapel = references.value.mapels.find(m => m.id === filter.value.mapel_id)
    return `${kelas?.tingkat} ${kelas?.nama_kelas} - ${mapel?.nama_mapel}`
})

const handleFilterChange = (key) => {
    if (key === 'kelas_id') filter.value.mapel_id = ''
    pertemuans.value = []
    siswas.value = []
    fetchReferensi()
    if (isFilterComplete.value) {
        fetchPertemuan()
    }
}

const fetchReferensi = async () => {
    isLoading.value = true
    try {
        const queryParams = new URLSearchParams()
        Object.keys(filter.value).forEach(key => { if (filter.value[key]) queryParams.append(key, filter.value[key]) })
        
        const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/guru/absensi/referensi?${queryParams.toString()}`, {
            headers: { Authorization: `Bearer ${token.value}` }
        })
        
        if (res.success) {
            references.value = {
                tahunAjarans: res.tahunAjarans || [],
                kurikulums: res.kurikulums || [],
                periodes: res.periodes || [],
                kelases: res.kelases || [],
                mapels: res.mapels || [],
                is_titimangsa_aktif: res.is_titimangsa_aktif
            }
            
            // Set defaults if empty
            if (!filter.value.tahun_ajaran_id && res.tahunAjarans?.length) {
                const aktif = res.tahunAjarans.find(t => t.is_aktif)
                filter.value.tahun_ajaran_id = aktif ? aktif.id : res.tahunAjarans[0].id
            }
            if (!filter.value.kurikulum_id && res.kurikulums?.length) filter.value.kurikulum_id = res.kurikulums[0].id
            if (!filter.value.titimangsa_id && res.periodes?.length) {
                const aktif = res.periodes.find(p => p.is_aktif)
                if (aktif) filter.value.titimangsa_id = aktif.id
            }
        }
    } catch (e) {
        swal.toast('Gagal memuat filter', e.response?._data?.message, 'error')
    } finally {
        isLoading.value = false
    }
}

const pertemuans = ref([])
const siswas = ref([])
const absensiData = ref({}) // Format: { [siswa_id]: { [pertemuan_id]: 'status' } }
const isLoadingPertemuan = ref(false)
const isLoadingSiswa = ref(false)
const isSaving = ref(false)

const showAddModal = ref(false)
const isSubmitting = ref(false)
const formPertemuan = ref({
    id: null,
    tanggal: new Date().toISOString().split('T')[0],
    jam_mulai: 1,
    jam_selesai: 2,
    materi: ''
})

const isFetchingJam = ref(false)

const fetchLastJam = async () => {
    if (formPertemuan.value.id || !filter.value.kelas_id || !formPertemuan.value.tanggal) return
    isFetchingJam.value = true
    try {
        const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/guru/absensi/last-jam?kelas_id=${filter.value.kelas_id}&tanggal=${formPertemuan.value.tanggal}`, {
            headers: { Authorization: `Bearer ${token.value}` }
        })
        if (res.success) {
            formPertemuan.value.jam_mulai = res.last_jam + 1
            formPertemuan.value.jam_selesai = ''
        }
    } catch (e) {
        console.error(e)
    } finally {
        isFetchingJam.value = false
    }
}

const openAddModal = () => {
    formPertemuan.value = {
        id: null,
        tanggal: new Date().toISOString().split('T')[0],
        jam_mulai: 1,
        jam_selesai: '',
        materi: ''
    }
    showAddModal.value = true
    fetchLastJam()
}

const editPertemuan = (pert) => {
    formPertemuan.value = {
        id: pert.id,
        tanggal: pert.tanggal,
        jam_mulai: pert.jam_mulai,
        jam_selesai: pert.jam_selesai,
        materi: pert.materi || ''
    }
    showAddModal.value = true
}

// Reverse array for UI display (earliest to latest in columns)
const reversedPertemuans = computed(() => {
    return [...pertemuans.value].reverse();
})

const formatDate = (dateString) => {
    const d = new Date(dateString)
    return d.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' })
}

const formatDateShort = (dateString) => {
    const d = new Date(dateString)
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })
}

onMounted(() => {
    fetchReferensi()
})

const fetchPertemuan = async () => {
    if (!isFilterComplete.value) return
    
    isLoadingPertemuan.value = true
    isLoadingSiswa.value = true
    try {
        const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/guru/absensi/pertemuan', {
            params: {
                kelas_id: filter.value.kelas_id,
                mapel_id: filter.value.mapel_id,
                titimangsa_id: filter.value.titimangsa_id
            },
            headers: { Authorization: `Bearer ${token.value}` }
        })
        if (res.success) {
            pertemuans.value = res.data
            await fetchAllAbsensiSiswa(res.data)
        }
    } catch (e) {
        swal.toast('Gagal memuat pertemuan', '', 'error')
    } finally {
        isLoadingPertemuan.value = false
        isLoadingSiswa.value = false
    }
}

const fetchAllAbsensiSiswa = async (perts) => {
    if (perts.length === 0) {
        siswas.value = []
        absensiData.value = {}
        return
    }
    
    try {
        const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/guru/absensi/pertemuan/${perts[0].id}/siswa`, {
            headers: { Authorization: `Bearer ${token.value}` }
        })
        if (res.success) {
            siswas.value = res.data.map(s => ({
                siswa_id: s.siswa_id,
                nama: s.nama,
                nis: s.nis
            }))
        }
        
        const matrix = {}
        siswas.value.forEach(s => {
            matrix[s.siswa_id] = {}
        })
        
        const promises = perts.map(p => $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/guru/absensi/pertemuan/${p.id}/siswa`, {
            headers: { Authorization: `Bearer ${token.value}` }
        }))
        const results = await Promise.all(promises)
        
        results.forEach((res, i) => {
            const pid = perts[i].id
            res.data.forEach(s => {
                if (matrix[s.siswa_id]) {
                    matrix[s.siswa_id][pid] = s.status || 'H'
                }
            })
        })
        
        absensiData.value = matrix
        
    } catch(e) {
        console.error(e)
    }
}

const submitPertemuan = async () => {
    if (!formPertemuan.value.tanggal || !formPertemuan.value.jam_mulai || !formPertemuan.value.jam_selesai) {
        return swal.toast('Isi tanggal dan jam dengan benar', 'error')
    }
    
    isSubmitting.value = true
    try {
        const url = formPertemuan.value.id 
            ? `${import.meta.env.VITE_API_BASE_URL}/api/guru/absensi/pertemuan/${formPertemuan.value.id}`
            : import.meta.env.VITE_API_BASE_URL + '/api/guru/absensi/pertemuan'
            
        const method = formPertemuan.value.id ? 'PUT' : 'POST'

        const res = await $fetch(url, {
            method: method,
            body: {
                kelas_id: filter.value.kelas_id,
                mapel_id: filter.value.mapel_id,
                titimangsa_id: filter.value.titimangsa_id,
                ...formPertemuan.value
            },
            headers: { Authorization: `Bearer ${token.value}` }
        })
        
        if (res.success) {
            swal.toast(res.message, 'success')
            showAddModal.value = false
            fetchPertemuan()
        }
    } catch (e) {
        let msg = e.response?._data?.message || 'Terjadi kesalahan'
        if (e.response?._data?.errors) {
            msg = Object.values(e.response._data.errors)[0][0]
        }
        swal.toast(msg, 'error')
    } finally {
        isSubmitting.value = false
    }
}

const deletePertemuan = async (id) => {
    const result = await swal.fire({
        title: 'Hapus Pertemuan?',
        text: 'Semua data absensi di pertemuan ini akan ikut terhapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f43f5e',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    })
    
    if (result.isConfirmed) {
        try {
            const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/guru/absensi/pertemuan/${id}`, { 
                method: 'DELETE',
                headers: { Authorization: `Bearer ${token.value}` }
            })
            if (res.success) {
                swal.toast(res.message, 'success')
                fetchPertemuan()
            }
        } catch (e) {
            swal.toast(e.response?._data?.message || 'Gagal menghapus', 'error')
        }
    }
}

const cycleStatus = ['H', 'S', 'I', 'A', 'PKL']
const toggleAbsen = (siswa_id, pertemuan_id) => {
    let current = absensiData.value[siswa_id][pertemuan_id] || 'H'
    let currentIndex = cycleStatus.indexOf(current)
    let nextIndex = (currentIndex + 1) % cycleStatus.length
    absensiData.value[siswa_id][pertemuan_id] = cycleStatus[nextIndex]
}

const getAbsenValue = (siswa_id, pertemuan_id) => {
    return absensiData.value[siswa_id]?.[pertemuan_id] || 'H'
}

const getColorClass = (status) => {
    if (status === 'H') return 'bg-emerald-100 text-emerald-600 border border-emerald-200'
    if (status === 'S') return 'bg-amber-100 text-amber-600 border border-amber-200'
    if (status === 'I') return 'bg-sky-100 text-sky-600 border border-sky-200'
    if (status === 'A') return 'bg-rose-100 text-rose-600 border border-rose-200'
    if (status === 'PKL') return 'bg-indigo-100 text-indigo-600 border border-indigo-200'
    return 'bg-slate-100 text-slate-400 border border-slate-200'
}

const simpanAbsensi = async () => {
    isSaving.value = true
    try {
        const promises = pertemuans.value.map(pert => {
            const payload = siswas.value.map(s => ({
                siswa_id: s.siswa_id,
                status: absensiData.value[s.siswa_id][pert.id]
            }))
            return $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/guru/absensi/pertemuan/${pert.id}/simpan`, {
                method: 'POST',
                body: { absensi: payload },
                headers: { Authorization: `Bearer ${token.value}` }
            })
        })
        
        await Promise.all(promises)
        swal.toast('Tersimpan', 'Data absensi berhasil disimpan', 'success')
        
    } catch (e) {
        swal.toast('Gagal', e.response?._data?.message || 'Terjadi kesalahan saat menyimpan', 'error')
    } finally {
        isSaving.value = false
    }
}
</script>
