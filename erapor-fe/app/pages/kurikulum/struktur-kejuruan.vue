<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Struktur Kurikulum Kejuruan</h2>
        <p class="text-sm font-semibold text-slate-500 mt-1">Plotting mata pelajaran produktif berdasarkan Program atau Konsentrasi Keahlian.</p>
      </div>
    </div>

    <!-- MAIN GRID LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      
      <!-- ==============================================
           KIRI: PANEL FILTER (Col 4)
           ============================================== -->
      <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-100 flex items-center gap-4">
                <span class="text-3xl">⚙️</span>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-white">Filter Struktur</h3>
                    <p class="text-[10px] text-slate-400 font-semibold mt-0.5 uppercase tracking-widest">Pilih Unit Keahlian</p>
                </div>
            </div>
            
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Kurikulum</label>
                    <select v-model="selectedKurikulumId" @change="fetchData" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-medium text-slate-700 outline-none">
                        <option value="" disabled>-- Pilih Kurikulum --</option>
                        <option v-for="kur in kurikulums" :key="kur.id" :value="kur.id">{{ kur.nama_kurikulum }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tingkat Kelas</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button @click="tingkat = 'X'; fetchData()" :class="tingkat === 'X' ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition-all">X</button>
                        <button @click="tingkat = 'XI'; fetchData()" :class="tingkat === 'XI' ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition-all">XI</button>
                        <button @click="tingkat = 'XII'; fetchData()" :class="tingkat === 'XII' ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition-all">XII</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-5 bg-indigo-50 border border-indigo-100 rounded-2xl shadow-sm text-center">
            <p class="text-[9px] font-black text-indigo-700 uppercase leading-relaxed tracking-widest">
                Kelas X (Dasar Program Keahlian) diikat ke Program. Kelas XI dan XII diikat ke Konsentrasi Keahlian.
            </p>
        </div>

      </div>

      <!-- ==============================================
           KANAN: DAFTAR UNIT KEAHLIAN (Col 8)
           ============================================== -->
      <div class="lg:col-span-8 space-y-6">
         <!-- Loading State -->
         <div v-if="isLoading" class="bg-white rounded-2xl p-10 flex flex-col items-center justify-center opacity-60">
            <div class="w-8 h-8 border-4 border-slate-400 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Struktur Kejuruan...</span>
         </div>

         <!-- Empty State -->
         <div v-else-if="dataUnit.length === 0" class="bg-white rounded-2xl p-16 flex flex-col items-center justify-center border border-slate-200/60 shadow-sm text-center">
            <div class="text-6xl opacity-20 mb-4">🏜️</div>
            <p class="text-sm font-bold text-slate-500">Belum ada unit keahlian.</p>
            <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-2 font-bold">Pastikan master Program/Konsentrasi Keahlian sudah terisi.</p>
         </div>

         <!-- Daftar Unit -->
         <div v-else class="space-y-6">
            <div v-for="unit in dataUnit" :key="unit.id" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden group/card">
                
                <!-- Unit Header -->
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-black text-sm">🛠️</span>
                    <div>
                        <p class="text-[9px] font-black uppercase text-slate-400 tracking-widest">{{ tingkat === 'X' ? 'Program Keahlian' : 'Konsentrasi Keahlian' }}</p>
                        <h3 class="font-black text-slate-800 text-base uppercase mt-0.5">{{ unit.nama_program || unit.nama_kejuruan }}</h3>
                    </div>
                </div>

                <!-- Tabel Mapel Produktif -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-slate-100">
                                <th class="p-4 pl-6 w-12 text-center">#</th>
                                <th class="p-4">Mata Pelajaran Produktif</th>
                                <th class="p-4 text-center w-20">JP</th>
                                <th class="p-4 text-right pr-6 w-20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <tr v-if="unit.struktur_kejuruans.length === 0" class="border-b border-slate-50">
                                <td colspan="4" class="p-6 text-center text-xs font-bold text-slate-400">Belum ada mapel produktif di unit ini.</td>
                            </tr>
                            <tr v-for="(struk, idx) in unit.struktur_kejuruans" :key="struk.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors group/row">
                                <td class="p-3 pl-6 text-center text-xs font-bold text-slate-300">{{ idx + 1 }}</td>
                                <td class="p-3">
                                    <p class="font-black text-slate-700 text-xs">{{ struk.mapel?.nama_mapel }}</p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ struk.mapel?.kode_mapel }}</p>
                                </td>
                                <td class="p-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black tracking-widest bg-indigo-50 text-indigo-600 border border-indigo-100">
                                        {{ struk.jp }} JP
                                    </span>
                                </td>
                                <td class="p-3 pr-6 text-right">
                                    <button @click="confirmDeleteStruktur(struk)" class="w-7 h-7 rounded bg-white border border-slate-200 text-slate-300 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 inline-flex items-center justify-center transition-all opacity-0 group-hover/row:opacity-100" title="Hapus dari Struktur">✕</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Form Tambah Mapel Produktif -->
                <div class="p-4 bg-slate-50/50 border-t border-slate-100 flex flex-col md:flex-row items-center gap-3">
                    <div class="flex-grow flex gap-3 w-full">
                        <select v-model="formStruktur[unit.id].mapel_id" class="flex-grow px-3 py-2 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-xs font-bold text-slate-700">
                            <option value="">-- Pilih Mapel Kejuruan --</option>
                            <option v-for="mapel in mapels" :key="mapel.id" :value="mapel.id">{{ mapel.nama_mapel }}</option>
                        </select>
                        <input type="number" v-model="formStruktur[unit.id].jp" placeholder="JP" class="w-20 px-3 py-2 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-xs font-bold text-slate-700 text-center" min="1" max="50">
                    </div>
                    <button @click="addStruktur(unit)" :disabled="isSavingStruktur === unit.id || !formStruktur[unit.id].mapel_id || !formStruktur[unit.id].jp" class="w-full md:w-auto px-4 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 text-white font-bold text-xs uppercase tracking-widest rounded-xl transition-colors shadow-sm whitespace-nowrap flex items-center justify-center gap-2">
                        <span v-if="isSavingStruktur === unit.id" class="w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        <span v-else>➕ Tambah</span>
                    </button>
                </div>
            </div>
         </div>
      </div>

    </div>

    <!-- ==============================================
         MODAL KONFIRMASI HAPUS
         ============================================== -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade text-center">
            <div class="p-8">
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner border-4 border-white ring-4 ring-rose-50">⚠️</div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Hapus Data?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Anda yakin ingin menghapus mapel produktif:<br>
                    <span class="font-bold text-slate-800">{{ deleteTarget?.nama }}</span>?
                </p>
                
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isDeleteModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">Batal</button>
                    <button @click="executeDelete" class="flex-1 py-3 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        <span>Hapus</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==============================================
         MODAL ERROR
         ============================================== -->
    <div v-if="isErrorModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade text-center">
            <div class="p-8">
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner border-4 border-white ring-4 ring-rose-50">❌</div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Terjadi Kesalahan</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed font-bold bg-slate-50 p-4 rounded-xl border border-slate-100">
                    {{ errorMessage }}
                </p>
                <div class="mt-8">
                    <button @click="isErrorModalOpen = false" class="w-full py-3 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-2xl shadow-lg transition-all text-xs uppercase tracking-widest">Saya Mengerti</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div v-if="showToast" class="fixed bottom-10 left-1/2 -translate-x-1/2 bg-slate-900/95 backdrop-blur-md text-white px-6 py-4 rounded-full shadow-2xl flex items-center gap-4 z-[100] animate-slideUp">
      <div class="w-8 h-8 bg-gradient-to-br from-emerald-400 to-emerald-500 rounded-full flex items-center justify-center text-white text-sm shadow-inner shadow-white/20">✓</div>
      <p class="font-bold text-sm tracking-wide pr-2">{{ toastMessage }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'

definePageMeta({
  layout: 'kurikulum',
  middleware: 'kurikulum',
  title: 'Struktur Kurikulum Kejuruan'
})

const kurikulums = ref([])
const selectedKurikulumId = ref('')
const tingkat = ref('X')
const mapels = ref([])
const dataUnit = ref([])

const isLoading = ref(true)
const isSavingStruktur = ref(null)

// Reactive object to store form state for each unit
const formStruktur = reactive({})

// Error Modal State
const isErrorModalOpen = ref(false)
const errorMessage = ref('')
const showError = (msg) => {
    errorMessage.value = msg
    isErrorModalOpen.value = true
}

// Toast State
const showToast = ref(false)
const toastMessage = ref('')
const displayToast = (msg) => {
    toastMessage.value = msg
    showToast.value = true
    setTimeout(() => { showToast.value = false }, 3500)
}

// Delete State
const isDeleteModalOpen = ref(false)
const deleteTarget = ref({ id: null, nama: '' })

const fetchData = async () => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        const url = `http://localhost:8000/api/kurikulum/struktur-kejuruan?tingkat=${tingkat.value}` + (selectedKurikulumId.value ? `&kurikulum_id=${selectedKurikulumId.value}` : '')
        const response = await $fetch(url, {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            kurikulums.value = response.data.kurikulums
            selectedKurikulumId.value = response.data.selectedKurikulumId
            mapels.value = response.data.mapels
            dataUnit.value = response.data.dataUnit

            // Init form state untuk setiap unit
            dataUnit.value.forEach(unit => {
                if(!formStruktur[unit.id]) {
                    formStruktur[unit.id] = { mapel_id: '', jp: 4 }
                }
            })
        }
    } catch (error) {
        console.error('Failed to fetch:', error)
        showError('Gagal memuat data struktur kejuruan.')
    } finally {
        isLoading.value = false
    }
}

const addStruktur = async (unit) => {
    isSavingStruktur.value = unit.id
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch('http://localhost:8000/api/kurikulum/struktur-kejuruan', {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` },
            body: {
                kurikulum_id: selectedKurikulumId.value,
                mapel_id: formStruktur[unit.id].mapel_id,
                tingkat: tingkat.value,
                jp: formStruktur[unit.id].jp,
                program_id: tingkat.value === 'X' ? unit.id : null,
                konsentrasi_id: tingkat.value !== 'X' ? unit.id : null,
            }
        })
        if (response.success) {
            displayToast(response.message)
            formStruktur[unit.id].mapel_id = ''
            fetchData()
        }
    } catch (error) {
        console.error('Add struktur failed:', error)
        showError(error.response?._data?.message || 'Gagal menambahkan mapel produktif.')
    } finally {
        isSavingStruktur.value = null
    }
}

const confirmDeleteStruktur = (s) => {
    deleteTarget.value = { id: s.id, nama: s.mapel?.nama_mapel }
    isDeleteModalOpen.value = true
}

const executeDelete = async () => {
    if(!deleteTarget.value.id) return
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/kurikulum/struktur-kejuruan/${deleteTarget.value.id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            isDeleteModalOpen.value = false
            displayToast(response.message)
            fetchData()
        }
    } catch (error) {
        console.error('Delete failed:', error)
        showError('Gagal menghapus data.')
    }
}

onMounted(() => {
    fetchData()
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }

@keyframes slideUpFade {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-slideUpFade { animation: slideUpFade 0.3s ease-out forwards; }

@keyframes slideUp {
  from { opacity: 0; transform: translate(-50%, 20px); }
  to { opacity: 1; transform: translate(-50%, 0); }
}
.animate-slideUp { animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
