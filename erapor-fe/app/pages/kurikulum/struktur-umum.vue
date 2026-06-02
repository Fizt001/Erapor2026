<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Struktur Kurikulum Umum</h2>
        <p class="text-sm font-semibold text-slate-500 mt-1">Plotting mata pelajaran muatan umum ke dalam kelompok dan tingkat.</p>
      </div>
      
      <div class="bg-white p-2 rounded-2xl shadow-sm border border-slate-200">
        <select v-model="selectedKurikulumId" @change="fetchData" class="pl-4 pr-10 py-2 rounded-xl bg-slate-50 border-none font-bold text-sm text-slate-700 focus:ring-0 cursor-pointer w-full md:w-64">
            <option value="" disabled>-- Pilih Kurikulum --</option>
            <option v-for="kur in kurikulums" :key="kur.id" :value="kur.id">{{ kur.nama_kurikulum }}</option>
        </select>
      </div>
    </div>

    <!-- No Tabs, direct 3-column view -->

    <!-- Loading State -->
    <div v-if="isLoading" class="bg-white rounded-2xl p-10 flex flex-col items-center justify-center opacity-60">
        <div class="w-8 h-8 border-4 border-emerald-400 border-t-transparent rounded-full animate-spin mb-4"></div>
        <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Struktur...</span>
    </div>

    <!-- Empty State for Mapels in this Kurikulum -->
    <div v-else-if="activeKelompoks.length === 0" class="bg-white rounded-2xl p-16 flex flex-col items-center justify-center border border-slate-200/60 shadow-sm text-center">
        <div class="text-6xl opacity-20 mb-4">📭</div>
        <p class="text-sm font-bold text-slate-500">Belum ada Mata Pelajaran Umum.</p>
        <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-2 font-bold">Silakan tambahkan Mata Pelajaran Umum untuk Kurikulum ini di menu Mata Pelajaran.</p>
    </div>

    <!-- Daftar Kelompok & Mapel Berdasarkan Tingkat (3 Kolom) -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Loop untuk Kelas X, XI, XII -->
        <div v-for="tingkat in ['X', 'XI', 'XII']" :key="tingkat" class="space-y-4">
            <h3 class="font-black text-slate-800 text-base uppercase pb-2 border-b-2 border-emerald-500 inline-block mb-2">Kelas {{ tingkat }}</h3>

            <div v-for="kelompok in activeKelompoks" :key="kelompok.kode" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden group/card">
                
                <!-- Kelompok Header -->
                <div class="px-4 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between group-hover/card:bg-slate-100/50 transition-colors">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded bg-emerald-100 text-emerald-600 flex items-center justify-center font-black text-xs">📁</span>
                        <h4 class="font-black text-slate-800 text-xs uppercase">{{ kelompok.nama }}</h4>
                    </div>
                </div>

                <!-- Tabel Mapel dalam Kelompok -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white text-[8px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-slate-100">
                                <th class="p-3 pl-4">Mapel</th>
                                <th class="p-3 text-center w-16">JP</th>
                                <th class="p-3 text-right pr-4 w-12">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs">
                            <tr v-if="filteredStrukturs(kelompok.kode, tingkat).length === 0" class="border-b border-slate-50">
                                <td colspan="3" class="p-4 text-center text-[10px] font-bold text-slate-400">Belum ada mapel.</td>
                            </tr>
                            <tr v-for="struk in filteredStrukturs(kelompok.kode, tingkat)" :key="struk.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors group">
                                <td class="p-3 pl-4">
                                    <p class="font-black text-slate-700">{{ struk.mapel?.nama_mapel }}</p>
                                </td>
                                <td class="p-3 text-center">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-black tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        {{ struk.jp }}
                                    </span>
                                </td>
                                <td class="p-3 pr-4 text-right">
                                    <button @click="confirmDeleteStruktur(struk)" class="w-6 h-6 rounded bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:bg-rose-50 inline-flex items-center justify-center transition-all opacity-100 lg:opacity-0 lg:group-hover:opacity-100 shadow-sm" title="Hapus">✕</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Form Tambah Mapel ke Kelompok -->
                <div class="p-3 bg-slate-50/50 border-t border-slate-100 flex gap-2">
                    <select v-model="formStruktur[tingkat][kelompok.kode].mapel_id" class="flex-grow px-2 py-1.5 rounded-lg border border-slate-200 bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-[10px] font-bold text-slate-700 outline-none w-full">
                        <option value="">Mapel...</option>
                        <option v-for="mapel in availableMapels(kelompok.kode, tingkat)" :key="mapel.id" :value="mapel.id">{{ mapel.nama_mapel }}</option>
                    </select>
                    <input type="number" v-model="formStruktur[tingkat][kelompok.kode].jp" placeholder="JP" class="w-12 px-2 py-1.5 rounded-lg border border-slate-200 bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-[10px] font-bold text-slate-700 text-center outline-none" min="1" max="20">
                    <button @click="addStruktur(kelompok.kode, tingkat)" :disabled="isSavingStruktur === `${tingkat}-${kelompok.kode}` || !formStruktur[tingkat][kelompok.kode].mapel_id || !formStruktur[tingkat][kelompok.kode].jp" class="w-8 flex-shrink-0 bg-emerald-600 hover:bg-emerald-700 disabled:bg-emerald-400 text-white rounded-lg transition-colors shadow-sm shadow-emerald-200 flex items-center justify-center">
                        <span v-if="isSavingStruktur === `${tingkat}-${kelompok.kode}`" class="w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        <span v-else class="text-xs">➕</span>
                    </button>
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
                    Anda yakin ingin menghapus mapel:<br>
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

    <!-- Toast Notification -->
    <div v-if="showToast" class="fixed bottom-10 left-1/2 -translate-x-1/2 bg-slate-900/95 backdrop-blur-md text-white px-6 py-4 rounded-full shadow-2xl flex items-center gap-4 z-[100] animate-slideUp">
      <div :class="toastType === 'success' ? 'from-emerald-400 to-emerald-500' : 'from-rose-400 to-rose-500'" class="w-8 h-8 bg-gradient-to-br rounded-full flex items-center justify-center text-white text-sm shadow-inner shadow-white/20">
          {{ toastType === 'success' ? '✓' : '✕' }}
      </div>
      <p class="font-bold text-sm tracking-wide pr-2">{{ toastMessage }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'

definePageMeta({
  layout: 'kurikulum',
  middleware: 'kurikulum',
  title: 'Struktur Kurikulum Umum'
})

const kurikulums = ref([])
const selectedKurikulumId = ref('')
const strukturs = ref([])
const mapels = ref([])
const referensi_kelompok = ref([])

const activeKelompoks = computed(() => {
    if (!referensi_kelompok.value) return []
    // Hanya tampilkan kelompok jika ada mapel yang tersedia ATAU sudah ada mapel yang diplot
    return referensi_kelompok.value.filter(k => {
        const hasAvailableMapels = mapels.value.some(m => m.kelompok === k.kode)
        const hasAssignedStrukturs = strukturs.value.some(s => s.mapel?.kelompok === k.kode)
        return hasAvailableMapels || hasAssignedStrukturs
    })
})

// activeTingkat is removed
const isLoading = ref(true)
const isSavingStruktur = ref(null)

const formStruktur = reactive({})

// Toast State
const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('success') // 'success' or 'error'
const displayToast = (msg, type = 'success') => {
    toastMessage.value = msg
    toastType.value = type
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
        const queryParams = selectedKurikulumId.value ? `?kurikulum_id=${selectedKurikulumId.value}&t=${Date.now()}` : `?t=${Date.now()}`;
        const url = `http://localhost:8000/api/kurikulum/struktur-umum` + queryParams;
        const response = await $fetch(url, {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response && response.success) {
            kurikulums.value = response.data?.kurikulums || []
            selectedKurikulumId.value = response.data?.selectedKurikulumId || ''
            strukturs.value = response.data?.strukturs || []
            mapels.value = response.data?.mapels || []
            referensi_kelompok.value = response.data?.referensi_kelompok || []

            // Init form state untuk setiap tingkat dan kelompok referensi
            ;['X', 'XI', 'XII'].forEach(t => {
                if (!formStruktur[t]) formStruktur[t] = {}
                ;(referensi_kelompok.value || []).forEach(k => {
                    if (k && k.kode) {
                        if(!formStruktur[t][k.kode]) {
                            formStruktur[t][k.kode] = { mapel_id: '', jp: 2 }
                        }
                    }
                })
            })
        }
    } catch (error) {
        console.error('Failed to fetch:', error)
        let errMsg = error.message || 'Error tidak diketahui';
        if (error.response) {
            errMsg = `API Error: ${error.response.status} - ${typeof error.response._data === 'string' ? 'HTML Response' : JSON.stringify(error.response._data)}`;
        }
        displayToast(errMsg, 'error')
    } finally {
        isLoading.value = false
    }
}

// Filter struktur berdasarkan tingkat aktif dan kelompok kode
const filteredStrukturs = (kelompok_kode, tingkat) => {
    return strukturs.value.filter(s => s.tingkat === tingkat && s.mapel?.kelompok === kelompok_kode)
}

// Filter mapel yang tersedia untuk ditambah (belum ada di tingkat ini, dan sesuai kelompok)
const availableMapels = (kelompok_kode, tingkat) => {
    const existingMapelIds = filteredStrukturs(kelompok_kode, tingkat).map(s => s.mapel_id)
    return mapels.value.filter(m => m.kelompok === kelompok_kode && !existingMapelIds.includes(m.id))
}

const addStruktur = async (kelompok_kode, tingkat) => {
    if(!selectedKurikulumId.value) return
    isSavingStruktur.value = `${tingkat}-${kelompok_kode}`
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch('http://localhost:8000/api/kurikulum/struktur-umum', {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` },
            body: {
                kurikulum_id: selectedKurikulumId.value,
                mapel_id: formStruktur[tingkat][kelompok_kode].mapel_id,
                tingkat: tingkat,
                jp: formStruktur[tingkat][kelompok_kode].jp
            }
        })
        if (response.success) {
            displayToast(response.message)
            formStruktur[tingkat][kelompok_kode].mapel_id = ''
            fetchData()
        }
    } catch (error) {
        console.error('Add struktur failed:', error)
        let errMsg = error.message || 'Error tidak diketahui';
        if (error.response) {
            errMsg = `API Error: ${error.response.status} - ${typeof error.response._data === 'string' ? 'HTML Response' : JSON.stringify(error.response._data)}`;
        }
        displayToast(errMsg, 'error')
    } finally {
        isSavingStruktur.value = null
    }
}

const confirmDeleteStruktur = (s) => {
    deleteTarget.value = { id: s.id, nama: `${s.mapel?.nama_mapel} dari kelas ${s.tingkat}` }
    isDeleteModalOpen.value = true
}

const executeDelete = async () => {
    if(!deleteTarget.value.id) return
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/kurikulum/struktur-umum/${deleteTarget.value.id}`, {
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
        displayToast('Gagal menghapus data.', 'error')
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
