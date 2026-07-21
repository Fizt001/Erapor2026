<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <div class="flex-1 flex overflow-hidden relative">
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            <!-- Header -->
            <div class="px-6 py-5 bg-white border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shrink-0 z-10 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-500 text-xl hidden sm:flex">🤝</div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h3 class="text-sm font-black uppercase tracking-widest text-slate-700">Tindak Lanjut Walas</h3>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Daftar kasus atau eskalasi sistem yang butuh bimbingan Wali Kelas</p>
                    </div>
                </div>
                
                <div class="relative w-full sm:w-64">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
                    <input type="text" v-model="searchQuery" placeholder="Cari nama/NIS..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-xs font-bold outline-none text-slate-700">
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-auto bg-slate-50 custom-scrollbar p-4 sm:p-6 relative">
                <div v-if="pending" class="bg-white rounded-2xl p-16 flex flex-col items-center justify-center border border-slate-200/60 shadow-sm opacity-60 h-full max-h-[400px]">
                    <div class="w-8 h-8 border-4 border-indigo-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</span>
                </div>

                <div v-else-if="filteredSiswas.length === 0" class="bg-white rounded-2xl p-16 flex flex-col items-center justify-center border-2 border-dashed border-slate-200 shadow-sm text-center h-full max-h-[400px]">
                    <div class="text-6xl opacity-20 mb-4">✨</div>
                    <p class="text-sm font-bold text-slate-500">Bagus! Tidak ada kasus eskalasi yang butuh penanganan saat ini.</p>
                </div>

                <div v-else class="space-y-4 max-w-5xl mx-auto">
                    <!-- Kartu Siswa -->
                    <div v-for="siswa in filteredSiswas" :key="siswa.id" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden transition-all duration-300">
                        <div class="px-5 py-4 bg-slate-50 flex flex-col sm:flex-row items-center justify-between gap-4 cursor-pointer hover:bg-white transition-colors group" @click="toggleExpanded(siswa.id)">
                            <div class="flex items-center gap-4 w-full sm:w-auto">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-black flex-shrink-0 text-white shadow-sm bg-indigo-500">
                                    {{ siswa.nama.charAt(0) }}
                                </div>
                                <div>
                                    <h4 class="font-black text-slate-800 text-sm uppercase group-hover:text-indigo-600 transition-colors">{{ siswa.nama }}</h4>
                                    <p class="text-[10px] font-bold text-slate-500 tracking-widest uppercase mt-0.5">NIS: {{ siswa.nis }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-6 w-full sm:w-auto justify-between sm:justify-end">
                                <div class="text-right bg-white px-4 py-1.5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3">
                                    <div>
                                        <p class="text-[8px] font-black uppercase tracking-widest text-slate-400 mb-0.5">Status Penanganan</p>
                                        <p class="text-xs font-black leading-none text-rose-600">{{ siswa.penanganans.filter(p => p.status === 'Proses' && p.kategori === 'Bimbingan Walas').length }} Kasus Aktif</p>
                                    </div>
                                    <span v-if="siswa.penanganans.some(p => p.status === 'Proses' && p.kategori === 'Bimbingan Walas')" class="w-3 h-3 rounded-full bg-rose-500 animate-pulse shadow-lg shadow-rose-500/50"></span>
                                </div>
                                <button class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-100 text-slate-400 group-hover:bg-indigo-100 group-hover:text-indigo-600 transition-all">
                                    <span class="transform transition-transform duration-300" :class="{ 'rotate-180': expandedSiswa === siswa.id }">▼</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Area Expanded -->
                        <div v-if="expandedSiswa === siswa.id" class="p-5 border-t border-slate-100 animate-slideUpFade bg-white">
                            <h5 class="text-[11px] font-black text-slate-500 uppercase tracking-widest mb-4 border-b border-slate-100 pb-4">Riwayat Kasus Eskalasi</h5>
                            
                            <div class="space-y-4">
                                <div v-for="kasus in siswa.penanganans" :key="kasus.id" class="bg-white border rounded-xl overflow-hidden shadow-sm transition-all hover:shadow-md" :class="kasus.status === 'Proses' ? 'border-rose-200' : 'border-slate-200'">
                                    <div class="px-4 py-3 border-b flex justify-between items-center" :class="kasus.status === 'Proses' ? 'bg-rose-50 border-rose-100' : 'bg-slate-50 border-slate-100'">
                                        <div class="flex items-center gap-3">
                                            <span class="text-[10px] font-black text-white px-2 py-1 rounded-md shadow-sm bg-indigo-600">{{ kasus.kategori }}</span>
                                            <span class="text-[10px] font-bold text-slate-500">{{ formatDate(kasus.created_at) }}</span>
                                        </div>
                                        <span class="text-[9px] font-black uppercase tracking-widest px-3 py-1 rounded-full shadow-sm" :class="kasus.status === 'Proses' ? 'bg-rose-500 text-white animate-pulse' : 'bg-emerald-100 text-emerald-700 border border-emerald-200'">
                                            {{ kasus.status }}
                                        </span>
                                    </div>
                                    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                                        <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                                            <p class="font-black text-[10px] text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5"><span class="text-rose-500">🚨</span> Masalah / Peringatan</p>
                                            <p class="text-slate-700 font-semibold leading-relaxed">{{ kasus.deskripsi_masalah }}</p>
                                        </div>
                                        <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                                            <p class="font-black text-[10px] text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5"><span class="text-indigo-500">💡</span> Tindakan / Solusi</p>
                                            <p class="text-slate-700 font-semibold leading-relaxed">{{ kasus.tindakan_penyelesaian || 'Belum ada tindakan.' }}</p>
                                        </div>
                                    </div>
                                    <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-100 flex justify-between items-center group/actions">
                                        <p class="text-[9px] text-slate-400 font-black uppercase tracking-widest flex items-center gap-1.5">
                                            <span>👤</span> Pelapor: {{ kasus.guru?.name || 'Sistem' }}
                                        </p>
                                        <button v-if="kasus.kategori === 'Bimbingan Walas'" @click.stop="openModal(siswa, kasus)" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-[10px] font-bold text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 hover:border-indigo-200 transition-all uppercase tracking-widest flex items-center gap-1 shadow-sm">
                                            ✏️ {{ kasus.status === 'Proses' ? 'Tindak Lanjut' : 'Edit Tindakan' }}
                                        </button>
                                        <div v-else class="px-3 py-1.5 rounded-lg bg-rose-50 border border-rose-100 text-[9px] font-black text-rose-600 uppercase tracking-widest flex items-center gap-1">
                                            🛡️ Ditangani oleh BK
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Tindak Lanjut -->
    <div v-if="isModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade">
            <!-- Header Modal -->
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-slate-800">Tindak Lanjut Walas</h3>
                    <p class="text-[10px] font-bold text-slate-500 mt-0.5">Siswa: {{ selectedSiswa?.nama }}</p>
                </div>
                <button @click="closeModal" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-200 text-slate-400 hover:text-slate-600 transition-colors">
                    ✖
                </button>
            </div>

            <!-- Body Modal -->
            <div class="p-6 space-y-4">
                <div class="bg-rose-50 p-4 rounded-xl border border-rose-100">
                    <label class="block text-[10px] font-black text-rose-800 uppercase tracking-widest mb-1">Masalah (Eskalasi)</label>
                    <p class="text-xs font-semibold text-rose-900">{{ form.deskripsi_masalah }}</p>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Tindakan Penyelesaian / Solusi</label>
                    <textarea v-model="form.tindakan_penyelesaian" rows="4" placeholder="Jelaskan tindakan yang telah diambil wali kelas..." 
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-xs font-semibold outline-none resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Status Penanganan</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" @click="form.status = 'Proses'" :class="form.status === 'Proses' ? 'bg-rose-100 border-rose-300 text-rose-700 ring-2 ring-rose-500/20' : 'bg-white border-slate-200 text-slate-500 hover:bg-slate-50'" class="px-4 py-3 rounded-xl border font-bold text-xs transition-all uppercase tracking-widest flex flex-col items-center gap-1">
                            <span class="text-lg">⏳</span>
                            Masih Proses
                        </button>
                        <button type="button" @click="form.status = 'Selesai'" :class="form.status === 'Selesai' ? 'bg-emerald-100 border-emerald-300 text-emerald-700 ring-2 ring-emerald-500/20' : 'bg-white border-slate-200 text-slate-500 hover:bg-slate-50'" class="px-4 py-3 rounded-xl border font-bold text-xs transition-all uppercase tracking-widest flex flex-col items-center gap-1">
                            <span class="text-lg">✅</span>
                            Selesai
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                <button @click="closeModal" class="px-5 py-2.5 rounded-xl font-bold text-[10px] uppercase tracking-widest text-slate-500 hover:bg-slate-200 transition-colors">
                    Batal
                </button>
                <button @click="saveTindakLanjut" :disabled="isSubmitting" class="px-5 py-2.5 rounded-xl font-bold text-[10px] uppercase tracking-widest bg-indigo-600 hover:bg-indigo-700 text-white shadow-md hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                    <span v-if="isSubmitting" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    Simpan Tindakan
                </button>
            </div>
        </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCookie } from '#app'

definePageMeta({
    layout: 'guru',
    title: 'Bimbingan Wali Kelas'
})

const token = useCookie('auth_token')
const siswas = ref([])
const pending = ref(true)
const searchQuery = ref('')
const expandedSiswa = ref(null)

const isModalOpen = ref(false)
const selectedSiswa = ref(null)
const selectedKasus = ref(null)
const isSubmitting = ref(false)

const form = ref({
    deskripsi_masalah: '',
    tindakan_penyelesaian: '',
    status: 'Proses'
})

const filteredSiswas = computed(() => {
    if (!siswas.value) return []
    // Filter only those who have penanganans
    let items = siswas.value.filter(s => s.penanganans && s.penanganans.length > 0)
    
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase()
        items = items.filter(s => 
            s.nama.toLowerCase().includes(q) || 
            (s.nis && s.nis.toLowerCase().includes(q))
        )
    }
    
    // Sort to show those with 'Proses' status first (only for Bimbingan Walas)
    items.sort((a, b) => {
        const aProses = a.penanganans.some(p => p.status === 'Proses' && p.kategori === 'Bimbingan Walas') ? 1 : 0
        const bProses = b.penanganans.some(p => p.status === 'Proses' && p.kategori === 'Bimbingan Walas') ? 1 : 0
        return bProses - aProses
    })
    
    return items
})

const toggleExpanded = (id) => {
    expandedSiswa.value = expandedSiswa.value === id ? null : id
}

const formatDate = (dateString) => {
    if (!dateString) return '-'
    const d = new Date(dateString)
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

const fetchData = async () => {
    pending.value = true
    try {
        const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/guru/walas/bimbingan', {
            headers: { Authorization: `Bearer ${token.value}` }
        })
        if (res.success) {
            siswas.value = res.data.siswas
        }
    } catch (e) {
        console.error(e)
    } finally {
        pending.value = false
    }
}

const openModal = (siswa, kasus) => {
    selectedSiswa.value = siswa
    selectedKasus.value = kasus
    form.value = {
        deskripsi_masalah: kasus.deskripsi_masalah,
        tindakan_penyelesaian: kasus.tindakan_penyelesaian || '',
        status: kasus.status || 'Proses'
    }
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
    selectedSiswa.value = null
    selectedKasus.value = null
}

const saveTindakLanjut = async () => {
    if (!form.value.tindakan_penyelesaian) {
        return useSwal().toast('Isi tindakan penyelesaian terlebih dahulu', 'warning')
    }
    
    isSubmitting.value = true
    try {
        const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/guru/walas/bimbingan/${selectedKasus.value.id}`, {
            method: 'PUT',
            body: {
                tindakan_penyelesaian: form.value.tindakan_penyelesaian,
                status: form.value.status
            },
            headers: { Authorization: `Bearer ${token.value}` }
        })
        if (res.success) {
            useSwal().toast('Tersimpan', 'Data berhasil diperbarui', 'success')
            fetchData()
            closeModal()
        }
    } catch (e) {
        useSwal().toast('Gagal', e.response?._data?.message || 'Terjadi kesalahan', 'error')
    } finally {
        isSubmitting.value = false
    }
}

onMounted(() => {
    fetchData()
})
</script>
