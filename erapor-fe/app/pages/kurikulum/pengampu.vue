<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-center gap-4">
      <div>
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Teaching Assignment</h2>
        <p class="text-[11px] text-slate-500 uppercase mt-1 font-bold tracking-widest">Plotting Pengampu {{ activeKategori === 'umum' ? 'Unit Umum' : 'Unit Kejuruan' }}</p>
      </div>

      <!-- Kategori Switcher -->
      <div class="flex bg-slate-100/80 p-1.5 rounded-2xl border border-slate-200 shadow-inner w-full md:w-auto">
        <button @click="activeKategori = 'umum'; fetchPengampu()" 
            class="flex-1 md:flex-none text-center px-8 py-2.5 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all duration-300"
            :class="activeKategori === 'umum' ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-slate-200/50' : 'text-slate-400 hover:text-slate-600'">
            Unit Umum
        </button>
        <button @click="activeKategori = 'kejuruan'; fetchPengampu()" 
            class="flex-1 md:flex-none text-center px-8 py-2.5 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all duration-300"
            :class="activeKategori === 'kejuruan' ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-slate-200/50' : 'text-slate-400 hover:text-slate-600'">
            Unit Kejuruan
        </button>
      </div>
    </div>

    <!-- MAIN GRID LAYOUT -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 items-start flex-grow mb-10">
      
      <!-- ==============================================
           KIRI: FILTER (Col 1)
           ============================================== -->
      <div class="xl:col-span-1 xl:sticky xl:top-6 z-20">
        <div class="bg-indigo-600 rounded-2xl shadow-lg overflow-hidden transition-all hover:shadow-xl border border-indigo-500">
            <div class="p-6">
                <h3 class="text-xs font-black uppercase tracking-widest text-indigo-100 mb-6 flex items-center gap-2">
                    <span>🎛️</span> Filter Plotting
                </h3>
                
                <div class="space-y-5">
                    <div>
                        <label class="block text-[10px] font-black text-indigo-200 uppercase tracking-widest mb-2">Kurikulum</label>
                        <select v-model="selectedKurikulumId" @change="fetchPengampu" class="w-full px-4 py-3 bg-indigo-700/50 border border-indigo-500 rounded-xl focus:ring-2 focus:ring-white/20 focus:border-white transition-all text-xs font-bold text-white outline-none">
                            <option value="">-- Pilih Kurikulum --</option>
                            <option v-for="kur in listKurikulum" :key="kur.id" :value="kur.id">{{ kur.nama_kurikulum }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-indigo-200 uppercase tracking-widest mb-2">Tingkat</label>
                        <select v-model="selectedTingkat" @change="fetchPengampu" class="w-full px-4 py-3 bg-indigo-700/50 border border-indigo-500 rounded-xl focus:ring-2 focus:ring-white/20 focus:border-white transition-all text-xs font-bold text-white outline-none">
                            <option value="X">Kelas X</option>
                            <option value="XI">Kelas XI</option>
                            <option value="XII">Kelas XII</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- Sync Button -->
            <button @click="fetchPengampu" class="w-full p-4 bg-indigo-700 hover:bg-indigo-800 transition-colors flex items-center justify-center gap-2 border-t border-indigo-500/50 group">
                <span class="text-xs font-black uppercase tracking-widest text-indigo-100 group-hover:text-white">Refresh Data</span>
                <span class="text-indigo-300 group-hover:rotate-180 transition-transform duration-500">🔄</span>
            </button>
        </div>
      </div>

      <!-- ==============================================
           KANAN: GRID MAPEL (Col 3)
           ============================================== -->
      <div class="xl:col-span-3">
        
        <!-- Loading State -->
        <div v-if="isLoading" class="flex flex-col items-center justify-center h-[500px] bg-white border border-slate-200 rounded-2xl shadow-sm">
            <div class="w-8 h-8 border-4 border-indigo-400 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</span>
        </div>

        <!-- Empty State (No Filter) -->
        <div v-else-if="!selectedKurikulumId" class="flex flex-col items-center justify-center h-[500px] bg-white border border-slate-200 rounded-2xl shadow-sm">
            <span class="text-6xl mb-4 grayscale opacity-50">📑</span>
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest text-center">Silakan Pilih Kurikulum Terlebih Dahulu</h3>
        </div>

        <!-- Empty State (No Data) -->
        <div v-else-if="strukturs.length === 0 || kelases.length === 0" class="flex flex-col items-center justify-center h-[500px] bg-white border border-slate-200 rounded-2xl shadow-sm">
            <span class="text-6xl mb-4 grayscale opacity-50 transition-all">📭</span>
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Struktur/Kelas Belum Tersedia</h3>
        </div>

        <!-- Mapel Cards Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-3 gap-6 items-start">
            
            <div v-for="struktur in strukturs" :key="struktur.id" class="bg-white border border-slate-200 rounded-2xl shadow-sm flex flex-col h-[500px] overflow-hidden hover:shadow-md transition-all">
                
                <!-- Card Header -->
                <div class="p-4 bg-slate-50/80 border-b border-slate-100 shrink-0">
                    <div class="flex justify-between items-start mb-2">
                        <div class="text-[10px] font-black tracking-widest text-indigo-500 uppercase">{{ struktur.mapel?.kode_mapel }}</div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 text-[9px] font-black text-indigo-700 bg-white border border-indigo-100 rounded-md shadow-sm">
                                {{ struktur.jp }} JP
                            </span>
                        </div>
                    </div>
                    <h3 class="text-xs font-black text-slate-800 leading-tight uppercase truncate" :title="struktur.mapel?.nama_mapel">{{ struktur.mapel?.nama_mapel }}</h3>
                    
                    <div v-if="activeKategori === 'kejuruan'" class="mt-2">
                        <span class="text-[8px] font-black px-1.5 py-0.5 bg-rose-50 text-rose-600 border border-rose-100 rounded uppercase tracking-tighter shadow-sm inline-block truncate max-w-full">
                            {{ selectedTingkat === 'X' ? struktur.program?.nama_program : struktur.konsentrasi?.nama_konsentrasi }}
                        </span>
                    </div>
                </div>

                <!-- Card Body (Classes List) -->
                <div class="flex-1 overflow-y-auto p-3 space-y-4 bg-slate-50/30 custom-scrollbar">
                    
                    <div v-for="kelas in getRelevantClasses(struktur)" :key="kelas.id" 
                         class="border rounded-xl p-3 bg-white transition-all"
                         :class="getSisaJp(struktur, kelas.id) <= 0 ? 'border-emerald-200' : 'border-slate-200 hover:border-indigo-300'">
                        
                        <!-- Kelas Header -->
                        <div class="flex justify-between items-center mb-3 border-b border-slate-100 pb-2">
                            <h4 class="text-[11px] font-black text-slate-700 uppercase">{{ kelas.nama_kelas }}</h4>
                            <span class="text-[8px] font-black px-1.5 py-0.5 rounded uppercase tracking-widest"
                                  :class="getSisaJp(struktur, kelas.id) <= 0 ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' : 'text-rose-600 bg-rose-50 border border-rose-100 animate-pulse'">
                                {{ getSisaJp(struktur, kelas.id) <= 0 ? '✓ Tuntas' : 'Sisa ' + getSisaJp(struktur, kelas.id) + ' JP' }}
                            </span>
                        </div>

                        <!-- Existing Pengampus for this class -->
                        <div class="space-y-1.5 mb-2">
                            <div v-for="p in getPengampus(struktur, kelas.id)" :key="p.id" class="flex justify-between items-center bg-slate-50 border border-slate-100 p-2 rounded-lg text-[10px] group hover:border-indigo-100 transition-colors">
                                <div class="flex flex-col truncate pr-2">
                                    <span class="font-black text-slate-800 uppercase truncate" :title="p.guru?.name">{{ p.guru?.name }}</span>
                                    <span class="text-[8px] text-indigo-600 font-black tracking-widest">{{ p.jp }} JAM</span>
                                </div>
                                <button @click="confirmDelete(p.id)" class="text-slate-300 hover:text-rose-600 p-1.5 bg-white rounded-md border border-slate-100 shadow-sm transition-colors active:scale-90 shrink-0" title="Hapus Guru">
                                    <svg viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Add Pengampu Form -->
                        <div v-if="getSisaJp(struktur, kelas.id) > 0" class="flex items-center gap-1.5 pt-2 border-t border-dashed border-slate-200">
                            <!-- Inline V-Model variables -->
                            <select v-model="formAssign[struktur.id + '_' + kelas.id].guru" class="flex-1 py-1.5 px-2 text-[9px] font-bold rounded-lg border-slate-200 bg-slate-50 cursor-pointer min-w-0 focus:ring-2 focus:ring-indigo-500 outline-none border">
                                <option value="">+ Guru</option>
                                <option v-for="g in gurus" :key="g.id" :value="g.id">{{ g.name }}</option>
                            </select>
                            <input type="number" v-model.number="formAssign[struktur.id + '_' + kelas.id].jp" :max="getSisaJp(struktur, kelas.id)" min="1" class="w-10 py-1.5 text-[10px] font-black text-center rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none appearance-none bg-white">
                            <button @click="savePengampu(struktur, kelas.id)" class="bg-indigo-600 text-white w-7 h-7 flex items-center justify-center rounded-lg hover:bg-indigo-700 transition shadow-sm shrink-0 active:scale-90" title="Simpan" :disabled="isSaving[struktur.id + '_' + kelas.id]">
                                <span v-if="isSaving[struktur.id + '_' + kelas.id]" class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            </button>
                        </div>
                    </div>
                    
                    <div v-if="getRelevantClasses(struktur).length === 0" class="flex flex-col items-center justify-center py-6 opacity-30 italic">
                        <span class="text-2xl mb-1">🏫</span>
                        <p class="text-[9px] font-black uppercase text-center">Belum ada kelas terkait</p>
                    </div>

                </div>
            </div>
            
        </div>
      </div>
    </div>

    <!-- Modal Delete -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade text-center">
            <div class="p-8">
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner border-4 border-white ring-4 ring-rose-50">🗑️</div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Hapus Pengampu?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Anda yakin ingin menghapus tugas mengajar guru ini dari kelas tersebut?
                </p>
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isDeleteModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all text-xs uppercase tracking-widest">Batal</button>
                    <button @click="executeDelete" class="flex-1 py-3 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
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
import { ref, onMounted } from 'vue'

definePageMeta({
  layout: 'kurikulum',
  middleware: 'kurikulum',
  title: 'Plot Guru Mapel'
})

const isLoading = ref(false)
const listKurikulum = ref([])
const titimangsas = ref([])
const strukturs = ref([])
const kelases = ref([])
const gurus = ref([])

const activeKategori = ref('umum')
const selectedKurikulumId = ref('')
const selectedTingkat = ref('X')

const formAssign = ref({}) // Format: { "strukturId_kelasId": { guru: "", jp: 0 } }
const isSaving = ref({})

const fetchInitial = async () => {
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch('http://localhost:8000/api/admin/kurikulum', {
            headers: { Authorization: `Bearer ${token}` }
        })
        listKurikulum.value = response.data || []
        if (listKurikulum.value.length > 0) {
            selectedKurikulumId.value = listKurikulum.value[0].id
            fetchPengampu()
        }
    } catch (error) {
        console.error('Failed to fetch kurikulum:', error)
    }
}

const fetchPengampu = async () => {
    if (!selectedKurikulumId.value || !selectedTingkat.value) return
    
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/kurikulum/pengampu?kurikulum_id=${selectedKurikulumId.value}&tingkat=${selectedTingkat.value}&kategori=${activeKategori.value}`, {
            headers: { Authorization: `Bearer ${token}` }
        })
        titimangsas.value = response.data?.titimangsas || []
        strukturs.value = response.data?.strukturs || []
        kelases.value = response.data?.kelases || []
        gurus.value = response.gurus || []
        
        // Inisialisasi state form per struktur per kelas
        strukturs.value.forEach(s => {
            getRelevantClasses(s).forEach(k => {
                const key = `${s.id}_${k.id}`
                if (!formAssign.value[key]) {
                    formAssign.value[key] = { guru: '', jp: getSisaJp(s, k.id) }
                } else {
                    formAssign.value[key].jp = getSisaJp(s, k.id)
                }
            })
        })
    } catch (error) {
        console.error('Failed to fetch pengampu:', error)
        displayToast('Gagal memuat data.', 'error')
    } finally {
        isLoading.value = false
    }
}

const getRelevantClasses = (struktur) => {
    if (activeKategori.value === 'umum') {
        return kelases.value
    } else {
        // Kejuruan
        if (selectedTingkat.value === 'X') {
            return kelases.value.filter(k => k.kejuruan?.program_id === struktur.program_id)
        } else {
            return kelases.value.filter(k => k.kejuruan_id === struktur.konsentrasi_id)
        }
    }
}

const getPengampus = (struktur, kelasId) => {
    if (!struktur.pengampus) return []
    return struktur.pengampus.filter(p => p.kelas_id === kelasId)
}

const getSisaJp = (struktur, kelasId) => {
    const ps = getPengampus(struktur, kelasId)
    const usedJp = ps.reduce((sum, p) => sum + Number(p.jp), 0)
    return Math.max(0, struktur.jp - usedJp)
}

const savePengampu = async (struktur, kelasId) => {
    const key = `${struktur.id}_${kelasId}`
    const formData = formAssign.value[key]
    
    if (!formData.guru) {
        displayToast('Pilih guru terlebih dahulu!', 'error')
        return
    }
    if (!formData.jp || formData.jp <= 0) {
        displayToast('Jumlah Jam Pelajaran harus valid!', 'error')
        return
    }

    isSaving.value[key] = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch('http://localhost:8000/api/kurikulum/pengampu', {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` },
            body: {
                kelas_id: kelasId,
                struktur_id: struktur.id,
                jenis: activeKategori.value,
                guru_id: formData.guru,
                jp: formData.jp
            }
        })
        if (response.success) {
            displayToast('Guru berhasil diplot.', 'success')
            // Reset form for this cell
            formData.guru = ''
            await fetchPengampu() // refresh
        }
    } catch (error) {
        console.error('Save failed:', error)
        displayToast('Gagal menambahkan plot guru.', 'error')
    } finally {
        isSaving.value[key] = false
    }
}

const isDeleteModalOpen = ref(false)
const deleteTargetId = ref(null)

const confirmDelete = (pengampuId) => {
    deleteTargetId.value = pengampuId
    isDeleteModalOpen.value = true
}

const executeDelete = async () => {
    if (!deleteTargetId.value) return
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/kurikulum/pengampu/${deleteTargetId.value}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            isDeleteModalOpen.value = false
            displayToast('Tugas mengajar berhasil dihapus.', 'success')
            fetchPengampu()
        }
    } catch (error) {
        console.error('Delete failed:', error)
        displayToast('Gagal menghapus plotting.', 'error')
    }
}

const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('success')

const displayToast = (msg, type = 'success') => {
    toastMessage.value = msg
    toastType.value = type
    showToast.value = true
    setTimeout(() => { showToast.value = false }, 3500)
}

onMounted(() => {
    fetchInitial()
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

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
</style>
