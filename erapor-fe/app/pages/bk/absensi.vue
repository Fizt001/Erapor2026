<template>
  <div class="animate-fadeIn max-w-[1400px] mx-auto pb-12 mt-4 relative px-4">
    
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Input Absensi Bulanan (HSIA)</h2>
        <p class="text-sm font-semibold text-slate-500 mt-1">Pencatatan kehadiran siswa. Klik sel untuk mengubah status absen.</p>
    </div>

    <!-- MAIN GRID LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- ==============================================
             KIRI: PANEL FILTER (Col 3)
             ============================================== -->
        <div class="lg:col-span-3 space-y-6 lg:sticky lg:top-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
                <div class="p-6 bg-gradient-to-r from-rose-900 to-rose-800 border-b border-rose-100 flex items-center gap-4">
                    <span class="text-3xl">📅</span>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-white">Setup Data</h3>
                    </div>
                </div>
                
                <div class="p-4 bg-slate-50 border-b border-slate-100 space-y-4">
                    <!-- Pilih Periode -->
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Periode Penilaian</label>
                        <select v-model="selectedPeriode" class="w-full px-4 py-2 bg-slate-100 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all text-xs font-bold outline-none text-slate-700">
                            <option v-for="p in periodeOptions" :key="p.id" :value="p.id">
                                {{ p.label }} {{ activePeriodeBackend.includes(p.id) ? '(Aktif)' : '(Ditutup)' }}
                            </option>
                        </select>
                    </div>

                    <!-- Pilih Bulan -->
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Bulan</label>
                        <select v-model="selectedBulan" @change="fetchSiswas" class="w-full px-4 py-2 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all text-xs font-bold outline-none text-slate-700">
                            <option v-for="m in availableMonths" :key="m.value" :value="m.value">{{ m.label }}</option>
                        </select>
                    </div>

                    <!-- Cari Kelas -->
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Kelas</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
                            <input type="text" v-model="searchKelas" placeholder="Cari kelas..." class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all text-xs font-bold outline-none text-slate-700">
                        </div>
                    </div>
                </div>

                <!-- List Kelas -->
                <div class="max-h-[50vh] overflow-y-auto custom-scrollbar">
                    <div v-if="isLoadingKelas" class="p-8 text-center">
                        <div class="w-6 h-6 border-2 border-rose-400 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
                    </div>
                    <div v-else>
                        <button v-for="kelas in filteredKelases" :key="kelas.id" @click="selectKelas(kelas.id)"
                            class="w-full text-left px-5 py-4 border-b border-slate-100 hover:bg-rose-50 transition-colors flex items-center justify-between group"
                            :class="selectedKelasId === kelas.id ? 'bg-rose-50 border-l-4 border-l-rose-500' : 'bg-white border-l-4 border-l-transparent'">
                            <div>
                                <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 group-hover:text-rose-700 transition-colors">{{ kelas.nama_kelas }}</h4>
                            </div>
                            <span v-if="selectedKelasId === kelas.id" class="text-rose-500">▶</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Keterangan Status -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
                <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-3 text-center">Panduan Warna</h4>
                <div class="grid grid-cols-2 gap-2 text-[10px] font-bold">
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-slate-100 border border-slate-300"></span> Hadir (H)</div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-amber-100 border border-amber-300"></span> Sakit (S)</div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-sky-100 border border-sky-300"></span> Izin (I)</div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-rose-100 border border-rose-300"></span> Alpa (A)</div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-emerald-100 border border-emerald-300"></span> Libur (L)</div>
                    <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-indigo-100 border border-indigo-300"></span> PKL (P)</div>
                </div>
            </div>
        </div>

        <!-- ==============================================
             KANAN: GRID ABSENSI (Col 9)
             ============================================== -->
        <div class="lg:col-span-9 w-full">
            
            <div v-if="!selectedKelasId" class="bg-white rounded-2xl p-20 flex flex-col items-center justify-center border-2 border-dashed border-slate-200 shadow-sm text-center">
                <div class="text-6xl opacity-30 grayscale mb-4">👈</div>
                <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest">Absensi Kelas</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase mt-3 tracking-widest">Silakan pilih bulan dan kelas terlebih dahulu.</p>
            </div>

            <div v-else-if="isLoadingSiswa" class="bg-white rounded-2xl p-16 flex flex-col items-center justify-center border border-slate-200/60 shadow-sm opacity-60">
                <div class="w-8 h-8 border-4 border-rose-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Absensi...</span>
            </div>

            <div v-else-if="!activePeriodeBackend.includes(selectedPeriode)" class="bg-white rounded-2xl p-20 flex flex-col items-center justify-center border-2 border-dashed border-slate-300 shadow-sm text-center bg-slate-50/50">
                <div class="text-6xl mb-6">🔒</div>
                <h3 class="text-lg font-black text-slate-800 uppercase tracking-widest">Periode Terkunci</h3>
                <p class="text-xs font-bold text-slate-500 mt-3 tracking-wide max-w-sm leading-relaxed">
                    Periode <span class="text-rose-600 font-black">{{ selectedPeriode }}</span> saat ini berstatus nonaktif atau belum dibuka oleh bagian Kurikulum. Input data dibekukan sementara.
                </p>
            </div>

            <div v-else class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col relative h-[70vh]">
                
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center shrink-0">
                    <div>
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-wide">{{ getSelectedKelasName() }}</h3>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-0.5">Bulan {{ getSelectedBulanLabel() }} - {{ siswas.length }} Siswa</p>
                    </div>
                </div>

                <div class="overflow-auto flex-grow custom-scrollbar">
                    <table class="w-full text-left border-collapse text-[10px] sm:text-xs relative">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-slate-100 text-[9px] font-black uppercase tracking-widest text-slate-500 shadow-sm">
                                <th class="p-3 sticky left-0 bg-slate-100 border-b border-r border-slate-200 min-w-[150px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] z-20">Siswa</th>
                                <th v-for="d in daysInMonth" :key="d" class="p-2 border-b border-r border-slate-200 text-center min-w-[32px]">{{ d }}</th>
                                <th class="p-2 border-b border-r border-slate-200 text-center text-slate-500 bg-slate-100 w-10 min-w-[40px] max-w-[40px]">H</th>
                                <th class="p-2 border-b border-r border-slate-200 text-center text-amber-600 bg-amber-50 sticky right-[80px] z-20 w-10 min-w-[40px] max-w-[40px] shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.05)]">S</th>
                                <th class="p-2 border-b border-r border-slate-200 text-center text-sky-600 bg-sky-50 sticky right-[40px] z-20 w-10 min-w-[40px] max-w-[40px]">I</th>
                                <th class="p-2 border-b border-slate-200 text-center text-rose-600 bg-rose-50 sticky right-0 z-20 w-10 min-w-[40px] max-w-[40px]">A</th>
                            </tr>
                        </thead>
                        <tbody class="font-bold text-slate-600">
                            <tr v-for="(siswa, idx) in siswas" :key="siswa.id" class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                <td class="p-3 sticky left-0 bg-white group-hover:bg-slate-50 border-r border-slate-100 whitespace-nowrap overflow-hidden text-ellipsis max-w-[180px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] z-10" :title="siswa.nama">
                                    {{ siswa.nama }}
                                </td>
                                
                                <td v-for="d in daysInMonth" :key="d" class="p-0 border-r border-slate-50 text-center relative h-10 min-w-[32px]">
                                    <button @click="toggleAbsen(idx, d)" class="w-full h-full flex items-center justify-center font-black transition-colors focus:outline-none" :class="getColorClass(getAbsenValue(siswa, d))">
                                        {{ getAbsenValue(siswa, d) }}
                                    </button>
                                </td>

                                <td class="p-2 text-center text-slate-500 bg-slate-50/50 border-r border-slate-100 font-black w-10 min-w-[40px] max-w-[40px]">{{ countStatus(siswa, 'H') }}</td>
                                <td class="p-2 text-center text-amber-600 bg-amber-50/50 border-r border-slate-100 font-black sticky right-[80px] z-10 w-10 min-w-[40px] max-w-[40px] shadow-[-2px_0_5px_-2px_rgba(0,0,0,0.05)]">{{ countStatus(siswa, 'S') }}</td>
                                <td class="p-2 text-center text-sky-600 bg-sky-50/50 border-r border-slate-100 font-black sticky right-[40px] z-10 w-10 min-w-[40px] max-w-[40px]">{{ countStatus(siswa, 'I') }}</td>
                                <td class="p-2 text-center text-rose-600 bg-rose-50/50 sticky right-0 z-10 font-black w-10 min-w-[40px] max-w-[40px]">
                                    {{ countStatus(siswa, 'A') }}
                                </td>
                            </tr>
                            <tr v-if="siswas.length === 0">
                                <td :colspan="daysInMonth + 2" class="p-10 text-center text-slate-400">Tidak ada siswa.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    
    <!-- Custom Toast Notification -->
    <div v-if="toast.show" class="fixed bottom-6 right-6 z-50 animate-fadeIn" style="animation-duration: 0.2s;">
        <div class="bg-rose-600 text-white px-6 py-4 rounded-xl shadow-xl shadow-rose-900/20 flex items-center gap-3">
            <span class="text-xl">⚠️</span>
            <div>
                <h4 class="text-xs font-black uppercase tracking-widest">{{ toast.title }}</h4>
                <p class="text-xs font-semibold opacity-90">{{ toast.message }}</p>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'

definePageMeta({
  layout: 'bk',
  middleware: 'bk',
  title: 'Absensi Siswa'
})

const isLoadingKelas = ref(true)
const isLoadingSiswa = ref(false)
const kelases = ref([])
const siswas = ref([])
const selectedKelasId = ref(null)
const selectedBulan = ref(new Date().getMonth() + 1)
const searchKelas = ref('')

const activeTahunAjaran = ref('2025/2026')
const activePeriodeBackend = ref([])
const selectedPeriode = ref('PSTS Ganjil')

const toast = ref({
    show: false,
    title: '',
    message: ''
})

const showToast = (title, message) => {
    toast.value = { show: true, title, message }
    setTimeout(() => {
        toast.value.show = false
    }, 3000)
}

const daysInMonth = computed(() => {
    const period = periodeOptions.find(p => p.id === selectedPeriode.value);
    if (!period) return 31;
    
    const parts = activeTahunAjaran.value.split('/');
    const tahunAwal = parts[0] || new Date().getFullYear();
    const tahunAkhir = parts[1] || (parseInt(tahunAwal) + 1);
    
    const yearToUse = period.useYear === 'awal' ? tahunAwal : tahunAkhir;
    
    // new Date(year, month, 0) returns the last day of the previous month.
    // So if selectedBulan is 2 (Februari), new Date(2025, 2, 0) returns Feb 28.
    return new Date(yearToUse, selectedBulan.value, 0).getDate();
})

const filteredKelases = computed(() => {
    if (!searchKelas.value) return kelases.value
    const ls = searchKelas.value.toLowerCase()
    return kelases.value.filter(k => k.nama_kelas.toLowerCase().includes(ls))
})

const getSelectedKelasName = () => {
    if (!selectedKelasId.value) return ''
    const kelas = kelases.value.find(k => k.id === selectedKelasId.value)
    return kelas ? kelas.nama_kelas : ''
}

const periodeOptions = [
    { id: 'PSTS Ganjil', label: 'PSTS Ganjil', months: [7, 8, 9], useYear: 'awal' },
    { id: 'PSAS', label: 'PSAS', months: [10, 11, 12], useYear: 'awal' },
    { id: 'PSTS Genap', label: 'PSTS Genap', months: [1, 2, 3], useYear: 'akhir' },
    { id: 'PSAT', label: 'PSAT', months: [4, 5, 6], useYear: 'akhir' }
]

const availableMonths = computed(() => {
    const period = periodeOptions.find(p => p.id === selectedPeriode.value);
    if (!period) return [];
    
    const parts = activeTahunAjaran.value.split('/');
    const tahunAwal = parts[0] || new Date().getFullYear();
    const tahunAkhir = parts[1] || (parseInt(tahunAwal) + 1);
    
    const yearToUse = period.useYear === 'awal' ? tahunAwal : tahunAkhir;
    
    return period.months.map(m => {
        return {
            value: m,
            label: `${getNamaBulanBase(m)} ${yearToUse}`
        }
    })
})

const getNamaBulanBase = (b) => {
    const names = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
    return names[b - 1]
}

const getSelectedBulanLabel = () => {
    const found = availableMonths.value.find(m => m.value === selectedBulan.value)
    return found ? found.label : getNamaBulanBase(selectedBulan.value)
}

watch(selectedPeriode, () => {
    const months = availableMonths.value;
    if (months.length > 0) {
        const isCurrentValid = months.some(m => m.value === selectedBulan.value);
        if (!isCurrentValid) {
            selectedBulan.value = months[0].value;
            fetchSiswas();
        }
    }
})

const fetchInitialData = async () => {
    isLoadingKelas.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch('http://localhost:8000/api/bk/absensi', {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            kelases.value = response.data.kelases
            if (response.data.tahun_ajaran) {
                activeTahunAjaran.value = response.data.tahun_ajaran
                activePeriodeBackend.value = response.data.periode_aktif || []
                
                // Coba cocokan selectedBulan default ke periode yang benar
                const currentMonth = new Date().getMonth() + 1;
                const foundPeriod = periodeOptions.find(p => p.months.includes(currentMonth));
                if (foundPeriod) {
                    selectedPeriode.value = foundPeriod.id;
                    selectedBulan.value = currentMonth;
                }
            }
        }
    } catch (error) {
        console.error('Fetch error:', error)
    } finally {
        isLoadingKelas.value = false
    }
}

const fetchSiswas = async () => {
    if(!selectedKelasId.value) return;
    isLoadingSiswa.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/bk/absensi?kelas_id=${selectedKelasId.value}&bulan=${selectedBulan.value}`, {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            siswas.value = response.data.siswas.map(s => {
                return {
                    ...s,
                    absensi_data: s.absensi || {} // Object holding tgl_1, tgl_2
                }
            })
        }
    } catch (error) {
        console.error('Fetch error:', error)
    } finally {
        isLoadingSiswa.value = false
    }
}

const selectKelas = (id) => {
    selectedKelasId.value = id
    fetchSiswas()
}

const getAbsenValue = (siswa, day) => {
    return siswa.absensi_data[`tgl_${day}`] || 'H'
}

const countStatus = (siswa, status) => {
    let count = 0;
    for(let i = 1; i <= daysInMonth.value; i++) {
        if (getAbsenValue(siswa, i) === status) count++;
    }
    return count;
}

const getColorClass = (val) => {
    switch(val) {
        case 'H': return 'text-slate-300 hover:bg-slate-100' // Sangat soft
        case 'S': return 'bg-amber-100 text-amber-700 hover:bg-amber-200'
        case 'I': return 'bg-sky-100 text-sky-700 hover:bg-sky-200'
        case 'A': return 'bg-rose-100 text-rose-700 hover:bg-rose-200'
        case 'L': return 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'
        case 'P': return 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200'
        default: return 'text-slate-300 hover:bg-slate-100'
    }
}

const toggleAbsen = async (sIdx, tgl) => {
    // Validasi instan di Frontend
    if (!activePeriodeBackend.value || activePeriodeBackend.value.length === 0) {
        showToast('Akses Ditolak', 'Tidak ada Titimangsa (Periode) yang sedang aktif.');
        return;
    }
    if (!activePeriodeBackend.value.includes(selectedPeriode.value)) {
        showToast('Periode Terkunci', `Hanya periode aktif (${activePeriodeBackend.value.join(', ')}) yang bisa diinput.`);
        return;
    }

    const order = ['H', 'S', 'I', 'A', 'L', 'P']
    const curr = getAbsenValue(siswas.value[sIdx], tgl)
    const next = order[(order.indexOf(curr) + 1) % order.length]
    
    // Update UI instantly
    siswas.value[sIdx].absensi_data[`tgl_${tgl}`] = next
    
    // Send API async without blocking
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch('http://localhost:8000/api/bk/absensi/update', {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` },
            body: {
                siswa_id: siswas.value[sIdx].id,
                bulan: selectedBulan.value,
                periode: selectedPeriode.value,
                tanggal: tgl,
                status: next
            }
        })
        
        if (!response.success) {
            throw new Error(response.message || 'Gagal menyimpan absensi');
        }
    } catch (e) {
        console.error('Failed to save absensi', e)
        // Revert on fail
        siswas.value[sIdx].absensi_data[`tgl_${tgl}`] = curr;
        showToast('Gagal', e.data?.message || e.message || 'Gagal menyimpan absensi');
    }
}

onMounted(() => {
    fetchInitialData()
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }
</style>
