<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Tindak Lanjut & Penanganan Kasus</h2>
        <p class="text-sm font-semibold text-slate-500 mt-1">Kelola pemberian Surat Peringatan (SP) dan pembinaan kedisiplinan siswa.</p>
    </div>

    <!-- MAIN GRID LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- ==============================================
             KIRI: PANEL FILTER (Col 3)
             ============================================== -->
        <div class="lg:col-span-3 space-y-6 lg:sticky lg:top-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
                <div class="p-6 bg-gradient-to-r from-rose-900 to-rose-800 border-b border-rose-100 flex items-center gap-4">
                    <span class="text-3xl">🏫</span>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-white">Filter Kelas</h3>
                    </div>
                </div>
                
                <div class="p-4 bg-slate-50 border-b border-slate-100">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
                        <input type="text" v-model="searchKelas" placeholder="Cari kelas..." class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all text-xs font-bold outline-none text-slate-700">
                    </div>
                </div>

                <!-- List Kelas -->
                <div class="max-h-[60vh] overflow-y-auto custom-scrollbar">
                    <div v-if="isLoadingKelas" class="p-8 text-center">
                        <div class="w-6 h-6 border-2 border-rose-400 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
                    </div>
                    <div v-else>
                        <button v-for="kelas in filteredKelases" :key="kelas.id" @click="selectKelas(kelas.id)"
                            class="w-full text-left px-5 py-4 border-b border-slate-100 hover:bg-rose-50 transition-colors flex items-center justify-between group"
                            :class="selectedKelasId === kelas.id ? 'bg-rose-50 border-l-4 border-l-rose-500' : 'bg-white border-l-4 border-l-transparent'">
                            <div>
                                <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 group-hover:text-rose-700 transition-colors">{{ kelas.nama_kelas }}</h4>
                                <p class="text-[9px] font-bold text-slate-400 uppercase mt-1">{{ kelas.tingkat }} - {{ kelas.program_id ? 'Program' : 'Konsentrasi' }}</p>
                            </div>
                            <span v-if="selectedKelasId === kelas.id" class="text-rose-500">▶</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==============================================
             KANAN: DAFTAR SISWA (Col 9)
             ============================================== -->
        <div class="lg:col-span-9 space-y-6">
            
            <div v-if="!selectedKelasId" class="bg-white rounded-2xl p-16 flex flex-col items-center justify-center border border-slate-200/60 shadow-sm text-center">
                <div class="text-6xl opacity-20 mb-4">👈</div>
                <p class="text-sm font-bold text-slate-500">Silakan pilih kelas terlebih dahulu.</p>
            </div>

            <div v-else-if="isLoadingSiswa" class="bg-white rounded-2xl p-16 flex flex-col items-center justify-center border border-slate-200/60 shadow-sm opacity-60">
                <div class="w-8 h-8 border-4 border-rose-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Siswa...</span>
            </div>

            <div v-else class="space-y-4">
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex justify-between items-center mb-6">
                    <h3 class="font-black text-slate-700 uppercase tracking-widest text-xs">Daftar Siswa ({{ siswas.length }})</h3>
                    <div class="relative w-64">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
                        <input type="text" v-model="searchSiswa" placeholder="Cari nama siswa..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all text-xs font-bold outline-none text-slate-700">
                    </div>
                </div>

                <!-- Kartu Siswa -->
                <div v-for="siswa in filteredSiswas" :key="siswa.id" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-5 py-4 bg-slate-50 flex flex-col sm:flex-row items-center justify-between gap-4 cursor-pointer hover:bg-slate-100 transition-colors" @click="toggleExpanded(siswa.id)">
                        <div class="flex items-center gap-4 w-full sm:w-auto">
                            <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center font-black text-slate-500 flex-shrink-0">
                                {{ siswa.nama.charAt(0) }}
                            </div>
                            <div>
                                <h4 class="font-black text-slate-800 text-sm uppercase">{{ siswa.nama }}</h4>
                                <p class="text-[10px] font-bold text-slate-500 tracking-widest uppercase mt-0.5">NIS: {{ siswa.nis }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-6 w-full sm:w-auto justify-between sm:justify-end">
                            <div class="text-right">
                                <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Status Kedisiplinan</p>
                                <div class="mt-1 flex items-center gap-2 justify-end">
                                    <span v-if="siswa.penanganans && siswa.penanganans.some(p => p.status === 'Proses')" class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                                    <span class="text-xs font-black" :class="siswa.sisa_poin < 50 ? 'text-rose-600' : 'text-emerald-600'">
                                        Sisa Poin: {{ siswa.sisa_poin }}
                                    </span>
                                </div>
                            </div>
                            <span class="text-slate-400 transform transition-transform" :class="{ 'rotate-180': expandedSiswa === siswa.id }">▼</span>
                        </div>
                    </div>
                    
                    <!-- Area Expanded (Riwayat Kasus) -->
                    <div v-if="expandedSiswa === siswa.id" class="p-5 border-t border-slate-100 animate-slideUpFade">
                        <div class="flex justify-between items-center mb-4">
                            <h5 class="text-xs font-black text-slate-700 uppercase tracking-widest">Riwayat Penanganan / SP</h5>
                            <button @click="openModal(siswa)" class="bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-xl font-bold shadow-md shadow-rose-500/30 transition-all text-[10px] uppercase tracking-widest flex items-center gap-2">
                                <span>➕</span> Catat Kasus
                            </button>
                        </div>
                        
                        <div v-if="siswa.penanganans && siswa.penanganans.length > 0" class="space-y-4">
                            <div v-for="kasus in siswa.penanganans" :key="kasus.id" class="bg-white border rounded-xl overflow-hidden shadow-sm" :class="kasus.status === 'Proses' ? 'border-rose-200' : 'border-slate-200'">
                                <div class="px-4 py-3 border-b flex justify-between items-center" :class="kasus.status === 'Proses' ? 'bg-rose-50 border-rose-100' : 'bg-slate-50 border-slate-100'">
                                    <div class="flex items-center gap-3">
                                        <span class="text-[10px] font-black text-white px-2 py-1 rounded" :class="getKategoriColor(kasus.kategori)">{{ kasus.kategori }}</span>
                                        <span class="text-[10px] font-bold text-slate-500">{{ formatDate(kasus.created_at) }}</span>
                                    </div>
                                    <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full" :class="kasus.status === 'Proses' ? 'bg-rose-100 text-rose-600' : 'bg-emerald-100 text-emerald-600'">
                                        {{ kasus.status }}
                                    </span>
                                </div>
                                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                    <div>
                                        <p class="font-black text-[10px] text-slate-400 uppercase tracking-widest mb-1">Masalah / Kasus</p>
                                        <p class="text-slate-700 font-medium">{{ kasus.deskripsi_masalah }}</p>
                                    </div>
                                    <div>
                                        <p class="font-black text-[10px] text-slate-400 uppercase tracking-widest mb-1">Tindakan / Solusi</p>
                                        <p class="text-slate-700 font-medium">{{ kasus.tindakan_penyelesaian }}</p>
                                    </div>
                                </div>
                                <div class="px-4 py-2 bg-slate-50 border-t border-slate-100 flex justify-between items-center">
                                    <p class="text-[9px] text-slate-400 font-bold uppercase">Petugas: {{ kasus.guru?.name || 'BK' }}</p>
                                    <div class="flex gap-2">
                                        <button @click="openModal(siswa, kasus)" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 transition-colors uppercase tracking-widest">Edit</button>
                                        <button @click="confirmDelete(kasus.id)" class="text-[10px] font-bold text-rose-600 hover:text-rose-800 transition-colors uppercase tracking-widest ml-2">Hapus</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-6 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            <span class="text-2xl opacity-30 block mb-2">✨</span>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Belum ada catatan SP / Penanganan.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ==============================================
         MODAL FORM PENANGANAN
         ============================================== -->
    <div v-if="isModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade">
            <!-- Header -->
            <div class="px-6 py-5 bg-gradient-to-r from-rose-900 to-rose-800 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-white tracking-widest uppercase">{{ editId ? 'Edit' : 'Catat' }} Penanganan</h3>
                    <p class="text-[10px] text-rose-200 uppercase mt-0.5">{{ targetSiswa?.nama }} ({{ targetSiswa?.nis }})</p>
                </div>
                <button @click="closeModal" class="text-rose-200 hover:text-white transition-colors bg-white/10 w-8 h-8 rounded-full flex items-center justify-center">✕</button>
            </div>

            <!-- Body -->
            <form @submit.prevent="saveData" class="p-6 space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Kategori (SP)</label>
                        <select v-model="form.kategori" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all text-xs font-bold text-slate-700 outline-none">
                            <option value="Bimbingan Walas">Bimbingan Walas</option>
                            <option value="SP1">Surat Peringatan 1</option>
                            <option value="SP2">Surat Peringatan 2</option>
                            <option value="SP3">Surat Peringatan 3</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Status Penanganan</label>
                        <select v-model="form.status" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all text-xs font-bold text-slate-700 outline-none" :class="form.status === 'Proses' ? 'text-rose-600' : 'text-emerald-600'">
                            <option value="Proses">Sedang Diproses</option>
                            <option value="Selesai">Kasus Selesai</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Deskripsi Masalah / Alasan SP</label>
                    <textarea v-model="form.deskripsi_masalah" required rows="2" placeholder="Jelaskan secara singkat akar permasalahan..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all text-xs font-semibold text-slate-700 outline-none resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Tindakan / Solusi yang Diberikan</label>
                    <textarea v-model="form.tindakan_penyelesaian" required rows="2" placeholder="Contoh: Pemanggilan Orang Tua, Skorsing..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all text-xs font-semibold text-slate-700 outline-none resize-none"></textarea>
                </div>

                <!-- Footer -->
                <div class="pt-4 mt-6 border-t border-slate-100 flex gap-3">
                    <button type="button" @click="closeModal" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all text-xs uppercase tracking-widest">Batal</button>
                    <button type="submit" :disabled="isSaving" class="flex-1 py-3 bg-rose-600 hover:bg-rose-700 disabled:bg-rose-400 text-white font-bold rounded-xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        <span v-if="isSaving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        <span v-else>Simpan Data</span>
                    </button>
                </div>
            </form>
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
import { ref, computed, onMounted } from 'vue'

definePageMeta({
  layout: 'bk',
  middleware: 'bk',
  title: 'Penanganan Kasus'
})

const isLoadingKelas = ref(true)
const isLoadingSiswa = ref(false)
const kelases = ref([])
const siswas = ref([])
const selectedKelasId = ref(null)

const searchKelas = ref('')
const searchSiswa = ref('')
const expandedSiswa = ref(null)

const filteredKelases = computed(() => {
    if (!searchKelas.value) return kelases.value
    const ls = searchKelas.value.toLowerCase()
    return kelases.value.filter(k => k.nama_kelas.toLowerCase().includes(ls))
})

const filteredSiswas = computed(() => {
    if (!searchSiswa.value) return siswas.value
    const ls = searchSiswa.value.toLowerCase()
    return siswas.value.filter(s => s.nama.toLowerCase().includes(ls) || s.nis.includes(ls))
})

const fetchInitialData = async () => {
    isLoadingKelas.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch('http://localhost:8000/api/bk/penanganan', {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            kelases.value = response.data.kelases
        }
    } catch (error) {
        console.error('Fetch error:', error)
        displayToast('Gagal memuat data kelas.', 'error')
    } finally {
        isLoadingKelas.value = false
    }
}

const selectKelas = async (id) => {
    selectedKelasId.value = id
    expandedSiswa.value = null
    isLoadingSiswa.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/bk/penanganan?kelas_id=${id}`, {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            siswas.value = response.data.siswas
        }
    } catch (error) {
        console.error('Fetch error:', error)
        displayToast('Gagal memuat data siswa.', 'error')
    } finally {
        isLoadingSiswa.value = false
    }
}

const toggleExpanded = (id) => {
    expandedSiswa.value = expandedSiswa.value === id ? null : id
}

// FORMAT TANGGAL
const formatDate = (dateStr) => {
    if (!dateStr) return '-'
    const d = new Date(dateStr)
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

const getKategoriColor = (kat) => {
    switch (kat) {
        case 'SP1': return 'bg-amber-500'
        case 'SP2': return 'bg-orange-500'
        case 'SP3': return 'bg-rose-600'
        default: return 'bg-indigo-500'
    }
}

// MODAL FORM
const isModalOpen = ref(false)
const isSaving = ref(false)
const editId = ref(null)
const targetSiswa = ref(null)

const form = ref({
    siswa_id: '',
    kategori: 'Bimbingan Walas',
    deskripsi_masalah: '',
    tindakan_penyelesaian: '',
    status: 'Proses'
})

const openModal = (siswa, kasus = null) => {
    targetSiswa.value = siswa
    if (kasus) {
        editId.value = kasus.id
        form.value = {
            siswa_id: siswa.id,
            kategori: kasus.kategori,
            deskripsi_masalah: kasus.deskripsi_masalah,
            tindakan_penyelesaian: kasus.tindakan_penyelesaian,
            status: kasus.status
        }
    } else {
        editId.value = null
        form.value = {
            siswa_id: siswa.id,
            kategori: 'Bimbingan Walas',
            deskripsi_masalah: '',
            tindakan_penyelesaian: '',
            status: 'Proses'
        }
    }
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
    editId.value = null
    targetSiswa.value = null
}

const saveData = async () => {
    isSaving.value = true
    const token = useCookie('auth_token').value
    try {
        const url = editId.value 
            ? `http://localhost:8000/api/bk/penanganan/${editId.value}`
            : `http://localhost:8000/api/bk/penanganan`
            
        const method = editId.value ? 'PUT' : 'POST'
        
        const response = await $fetch(url, {
            method,
            headers: { Authorization: `Bearer ${token}` },
            body: form.value
        })
        
        if (response.success) {
            closeModal()
            displayToast(response.message, 'success')
            // Refresh siswa data
            selectKelas(selectedKelasId.value)
        }
    } catch (error) {
        displayToast(error.response?._data?.message || 'Gagal menyimpan data.', 'error')
    } finally {
        isSaving.value = false
    }
}

// DELETE PENANGANAN
const confirmDelete = async (id) => {
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/bk/penanganan/${id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            displayToast(response.message, 'success')
            selectKelas(selectedKelasId.value)
        }
    } catch (error) {
        displayToast('Gagal menghapus data.', 'error')
    }
}

// TOAST
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
    fetchInitialData()
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }
@keyframes slideUpFade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-slideUpFade { animation: slideUpFade 0.3s ease-out forwards; }
@keyframes slideUp { from { opacity: 0; transform: translate(-50%, 20px); } to { opacity: 1; transform: translate(-50%, 0); } }
.animate-slideUp { animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
