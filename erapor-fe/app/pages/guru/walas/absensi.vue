<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all">
        <div class="p-6 shrink-0">
          <div class="bg-gradient-to-r from-amber-600 to-amber-700 rounded-2xl p-5 border border-amber-500 shadow-sm relative overflow-hidden flex items-center gap-4">
            <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0 relative z-10">📅</div>
            <div class="relative z-10">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">Kalender Absensi</h3>
                <p class="text-[10px] text-amber-100 font-semibold uppercase mt-0.5">Rekap Bulanan Wali Kelas</p>
            </div>
          </div>
        </div>

        <div class="px-6 pb-6 bg-white shrink-0 space-y-4">
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
              <select v-model="filter.kurikulum_id" @change="handleFilterChange('kurikulum_id')" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all font-semibold text-xs text-slate-700 outline-none" :disabled="isLoading || references.kurikulums.length === 0">
                <option v-if="references.kurikulums.length === 0" value="" disabled>Belum diseting</option>
                <option v-for="kur in references.kurikulums" :key="kur.id" :value="kur.id">{{ kur.nama_kurikulum }}</option>
              </select>
            </div>

            <!-- Periode (Titimangsa) -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Periode</label>
              <select v-model="filter.titimangsa_id" @change="handleFilterChange('titimangsa_id')" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all font-semibold text-xs text-slate-700 outline-none" :disabled="isLoading || references.periodes.length === 0">
                <option v-if="references.periodes.length === 0" value="" disabled>Belum diseting</option>
                <option v-else value="">- Pilih Periode -</option>
                <option v-for="per in references.periodes" :key="per.id" :value="per.id">
                  {{ per.nama_periode_panjang || per.nama_periode }} {{ per.is_aktif ? '(Aktif)' : '(Ditutup)' }}
                </option>
              </select>
            </div>
            
            <!-- Pilih Bulan -->
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Pilih Bulan</label>
                <select v-model="selectedBulan" @change="fetchData" class="w-full px-4 py-3 bg-white border-2 border-slate-200/70 rounded-2xl focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-xs font-bold outline-none text-slate-700" :disabled="!isFilterComplete || availableMonths.length === 0">
                    <option v-if="!isFilterComplete" value="" disabled>Lengkapi filter di atas</option>
                    <option v-else-if="availableMonths.length === 0" value="" disabled>Bulan tidak tersedia</option>
                    <option v-else value="" disabled>- Pilih Bulan -</option>
                    <option v-for="m in availableMonths" :key="m.value" :value="m.value">{{ m.label }}</option>
                </select>
            </div>
            
            <div class="p-4 bg-amber-50 rounded-xl border border-amber-100 text-center">
                <div class="text-[10px] text-amber-600 font-bold leading-relaxed">
                    Data diambil berdasarkan status <b>terakhir</b> (Last Known State) di jam pelajaran terakhir per hari dari seluruh guru mata pelajaran.
                </div>
            </div>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            <!-- Header Flow -->
            <div class="px-6 py-5 bg-white border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shrink-0 z-10 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-500 text-xl">📋</div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h3 class="text-sm font-black uppercase tracking-widest text-slate-700">Kalender Kehadiran</h3>
                            <span v-if="tahunAjaran" class="bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-md text-[10px] font-black tracking-widest border border-indigo-100 uppercase">
                                TA. {{ tahunAjaran }}
                            </span>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">
                            Bulan {{ getSelectedBulanLabel() }} — {{ siswas.length }} Siswa
                        </p>
                    </div>
                </div>
            </div>

            <!-- Flow Content -->
            <div class="flex-1 overflow-hidden bg-slate-50 flex flex-col relative p-4 sm:p-6">
                <div v-if="isLoading" class="bg-white rounded-2xl p-16 flex flex-col items-center justify-center border border-slate-200/60 shadow-sm opacity-60 h-full max-h-[400px]">
                    <div class="w-8 h-8 border-4 border-amber-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Absensi...</span>
                </div>
                <div v-else class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col h-full relative">
                    <div class="overflow-auto flex-grow custom-scrollbar">
                        <table class="w-full text-left border-collapse text-[10px] sm:text-xs relative">
                            <thead class="sticky top-0 z-20">
                                <tr class="bg-slate-100 text-[9px] font-black uppercase tracking-widest text-slate-500 shadow-sm">
                                    <th class="p-3 sticky left-0 bg-slate-100 border-b border-r border-slate-200 min-w-[150px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] z-30">Siswa</th>
                                    <th v-for="d in daysInMonth" :key="d" class="p-2 border-b border-r border-slate-200 text-center min-w-[32px]">{{ d }}</th>
                                    <th class="p-2 border-b border-r border-slate-200 text-center text-amber-600 bg-amber-50 sticky right-[120px] z-30 w-10 min-w-[40px] max-w-[40px] shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.05)]">S</th>
                                    <th class="p-2 border-b border-r border-slate-200 text-center text-amber-600 bg-amber-50 sticky right-[80px] z-30 w-10 min-w-[40px] max-w-[40px]">I</th>
                                    <th class="p-2 border-b border-r border-slate-200 text-center text-rose-600 bg-rose-50 sticky right-[40px] z-30 w-10 min-w-[40px] max-w-[40px]">A</th>
                                    <th class="p-2 border-b border-slate-200 text-center text-indigo-600 bg-indigo-50 sticky right-0 z-30 w-10 min-w-[40px] max-w-[40px]">P</th>
                                </tr>
                            </thead>
                            <tbody class="font-bold text-slate-600 relative z-0">
                                <tr v-for="siswa in siswas" :key="siswa.id" class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                    <td class="p-3 sticky left-0 bg-white group-hover:bg-slate-50 border-r border-slate-100 whitespace-nowrap overflow-hidden text-ellipsis max-w-[180px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] z-10" :title="siswa.nama">
                                        {{ siswa.nama }}
                                    </td>
                                    
                                    <td v-for="d in daysInMonth" :key="d" class="p-0 border-r border-slate-50 text-center relative h-10 min-w-[32px]">
                                        <div class="w-full h-full flex items-center justify-center font-black" :class="getColorClass(siswa.absensi[d])">
                                            {{ siswa.absensi[d] || '' }}
                                        </div>
                                    </td>

                                    <td class="p-2 text-center text-amber-600 bg-amber-50/90 backdrop-blur-sm border-r border-slate-100 font-black sticky right-[120px] z-10 w-10 min-w-[40px] max-w-[40px] shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.05)]">{{ siswa.totals.S }}</td>
                                    <td class="p-2 text-center text-amber-600 bg-amber-50/90 backdrop-blur-sm border-r border-slate-100 font-black sticky right-[80px] z-10 w-10 min-w-[40px] max-w-[40px]">{{ siswa.totals.I }}</td>
                                    <td class="p-2 text-center text-rose-600 bg-rose-50/90 backdrop-blur-sm border-r border-slate-100 font-black sticky right-[40px] z-10 w-10 min-w-[40px] max-w-[40px]">{{ siswa.totals.A }}</td>
                                    <td class="p-2 text-center text-indigo-600 bg-indigo-50/90 backdrop-blur-sm sticky right-0 z-10 font-black w-10 min-w-[40px] max-w-[40px]">
                                        {{ siswa.totals.P }}
                                    </td>
                                </tr>
                                <tr v-if="siswas.length === 0">
                                    <td :colspan="daysInMonth + 5" class="p-10 text-center text-slate-400">Tidak ada data siswa.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Panduan Warna -->
            <div class="px-6 py-4 bg-white border-t border-slate-200 shrink-0 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Panduan Warna Status</h4>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[10px] font-bold text-slate-600">
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-slate-50 border border-slate-200 flex items-center justify-center text-[8px]">-</span> Hadir/Kosong</div>
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-amber-100 text-amber-600 flex items-center justify-center border border-amber-300 text-[8px]">S</span> Sakit</div>
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-sky-100 text-sky-600 flex items-center justify-center border border-sky-300 text-[8px]">I</span> Izin</div>
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-rose-100 text-rose-600 flex items-center justify-center border border-rose-300 text-[8px]">A</span> Alpa</div>
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-indigo-100 text-indigo-600 flex items-center justify-center border border-indigo-300 text-[8px]">P</span> PKL</div>
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
    layout: 'guru',
    middleware: 'guru'
})

const token = useCookie('auth_token')
const swal = useSwal()

const filter = ref({
  tahun_ajaran_id: '',
  kurikulum_id: '',
  titimangsa_id: '',
})

const references = ref({
  tahunAjarans: [],
  kurikulums: [],
  periodes: [],
  is_titimangsa_aktif: true,
})

const isFilterComplete = computed(() => {
  return filter.value.tahun_ajaran_id && 
         filter.value.kurikulum_id && 
         filter.value.titimangsa_id
})

const selectedBulan = ref('')

const availableMonths = computed(() => {
    if (!filter.value.titimangsa_id) return []
    const periode = references.value.periodes.find(p => p.id === filter.value.titimangsa_id)
    if (!periode) return []
    
    const nama = (periode.nama_periode || '').toLowerCase()
    
    if (nama.includes('ganjil')) {
        if (nama.includes('sts') || nama.includes('pts')) { // PSTS / ASTS / PTS
            return [
                { value: 7, label: 'Juli' },
                { value: 8, label: 'Agustus' },
                { value: 9, label: 'September' },
            ]
        } else { // PAS / ASAS
            return [
                { value: 10, label: 'Oktober' },
                { value: 11, label: 'November' },
                { value: 12, label: 'Desember' },
            ]
        }
    } else { // Genap
        if (nama.includes('sts') || nama.includes('pts')) {
            return [
                { value: 1, label: 'Januari' },
                { value: 2, label: 'Februari' },
                { value: 3, label: 'Maret' },
            ]
        } else {
            return [
                { value: 4, label: 'April' },
                { value: 5, label: 'Mei' },
                { value: 6, label: 'Juni' },
            ]
        }
    }
})

const isLoading = ref(false)
const siswas = ref([])
const daysInMonth = ref(31)
const tahunAjaran = ref('')

onMounted(() => {
    fetchReferensi()
})

const handleFilterChange = (key) => {
    selectedBulan.value = ''
    siswas.value = []
    fetchReferensi()
}

const fetchReferensi = async () => {
    isLoading.value = true
    try {
        const queryParams = new URLSearchParams()
        Object.keys(filter.value).forEach(key => { if (filter.value[key]) queryParams.append(key, filter.value[key]) })
        
        const res = await $fetch(`http://localhost:8000/api/guru/absensi/referensi?${queryParams.toString()}`, {
            headers: { Authorization: `Bearer ${token.value}` }
        })
        
        if (res.success) {
            references.value = {
                tahunAjarans: res.tahunAjarans || [],
                kurikulums: res.kurikulums || [],
                periodes: res.periodes || [],
                is_titimangsa_aktif: res.is_titimangsa_aktif
            }
            
            if (!filter.value.tahun_ajaran_id && res.tahunAjarans?.length) {
                const aktif = res.tahunAjarans.find(t => t.is_aktif)
                filter.value.tahun_ajaran_id = aktif ? aktif.id : res.tahunAjarans[0].id
            }
            if (!filter.value.kurikulum_id && res.kurikulums?.length) filter.value.kurikulum_id = res.kurikulums[0].id
            if (!filter.value.titimangsa_id && res.periodes?.length) {
                const aktif = res.periodes.find(p => p.is_aktif)
                filter.value.titimangsa_id = aktif ? aktif.id : res.periodes[0].id
            }
        }
    } catch (e) {
        swal.toast('Gagal memuat referensi', 'error')
    } finally {
        isLoading.value = false
    }
}

const getSelectedBulanLabel = () => {
    const m = availableMonths.value.find(x => x.value === parseInt(selectedBulan.value))
    return m ? m.label : ''
}

const fetchData = async () => {
    if (!isFilterComplete.value || !selectedBulan.value) return
    
    isLoading.value = true
    try {
        const res = await $fetch('http://localhost:8000/api/guru/walas/absensi/kalender', {
            params: { 
                bulan: selectedBulan.value,
                tahun_ajaran_id: filter.value.tahun_ajaran_id
            },
            headers: { Authorization: `Bearer ${token.value}` }
        })
        if (res.success) {
            siswas.value = res.data
            daysInMonth.value = res.days_in_month
            tahunAjaran.value = res.tahun_ajaran
        }
    } catch (e) {
        swal.toast('Gagal', e.response?._data?.message || 'Gagal memuat absensi bulanan', 'error')
    } finally {
        isLoading.value = false
    }
}

const getColorClass = (status) => {
    if (status === 'S') return 'bg-amber-100 text-amber-600'
    if (status === 'I') return 'bg-sky-100 text-sky-600'
    if (status === 'A') return 'bg-rose-100 text-rose-600'
    if (status === 'P') return 'bg-indigo-100 text-indigo-600'
    return 'bg-transparent text-transparent' // For empty/Hadir
}
</script>
