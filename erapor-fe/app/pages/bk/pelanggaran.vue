<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Master Pelanggaran</h2>
        <p class="text-sm font-semibold text-slate-500 mt-1">Kelola data referensi jenis pelanggaran dan bobot poin kedisiplinan.</p>
      </div>
      
      <button @click="openModal()" class="bg-rose-600 hover:bg-rose-700 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-rose-500/30 transition-all flex items-center gap-2">
        <span>➕</span> Tambah Pelanggaran
      </button>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 mb-6 flex flex-col sm:flex-row gap-4 justify-between items-center">
        <!-- Tab Jenis -->
        <div class="flex bg-slate-100 p-1.5 rounded-xl w-full sm:w-auto">
            <button v-for="j in ['Semua', 'Ringan', 'Sedang', 'Berat']" :key="j" @click="filterJenis = j; fetchPelanggarans()"
                class="flex-1 sm:flex-none px-4 py-2 text-[11px] font-black uppercase tracking-widest rounded-lg transition-all duration-300"
                :class="filterJenis === j ? 'bg-white text-rose-600 shadow-sm' : 'text-slate-400 hover:text-slate-600'">
                {{ j }}
            </button>
        </div>

        <!-- Search -->
        <div class="relative w-full sm:w-64">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
            <input type="text" v-model="searchQuery" @input="debounceSearch" placeholder="Cari pelanggaran..." 
                class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all text-xs font-bold outline-none text-slate-700">
        </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="bg-white rounded-2xl p-16 flex flex-col items-center justify-center border border-slate-200/60 shadow-sm opacity-60">
        <div class="w-8 h-8 border-4 border-rose-400 border-t-transparent rounded-full animate-spin mb-4"></div>
        <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</span>
    </div>

    <!-- Empty State -->
    <div v-else-if="pelanggarans.length === 0" class="bg-white rounded-2xl p-16 flex flex-col items-center justify-center border border-slate-200/60 shadow-sm text-center">
        <div class="text-6xl opacity-20 mb-4">📋</div>
        <p class="text-sm font-bold text-slate-500">Belum ada data pelanggaran.</p>
        <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-2 font-bold">Klik tombol Tambah untuk membuat referensi baru.</p>
    </div>

    <!-- Data Table -->
    <div v-else class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">
                        <th class="p-4 pl-6 w-16">No</th>
                        <th class="p-4">Nama Pelanggaran</th>
                        <th class="p-4 w-32">Kategori</th>
                        <th class="p-4 text-center w-24">Poin</th>
                        <th class="p-4 text-right pr-6 w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <tr v-for="(p, index) in pelanggarans" :key="p.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors group">
                        <td class="p-4 pl-6 text-xs font-bold text-slate-400">{{ (currentPage - 1) * 15 + index + 1 }}</td>
                        <td class="p-4">
                            <p class="font-bold text-slate-800 text-[13px] leading-tight">{{ p.nama_pelanggaran }}</p>
                        </td>
                        <td class="p-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-black tracking-widest uppercase border"
                                :class="{
                                    'bg-amber-50 text-amber-600 border-amber-200': p.jenis === 'Ringan',
                                    'bg-orange-50 text-orange-600 border-orange-200': p.jenis === 'Sedang',
                                    'bg-rose-50 text-rose-600 border-rose-200': p.jenis === 'Berat'
                                }">
                                {{ p.jenis }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <span class="font-black text-slate-700 text-lg">{{ p.bobot }}</span>
                        </td>
                        <td class="p-4 pr-6 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="openModal(p)" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 flex items-center justify-center transition-all shadow-sm" title="Edit">✏️</button>
                                <button @click="confirmDelete(p)" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50 flex items-center justify-center transition-all shadow-sm" title="Hapus">🗑️</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div v-if="totalPages > 1" class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
            <span class="text-xs font-bold text-slate-400">Halaman {{ currentPage }} dari {{ totalPages }}</span>
            <div class="flex gap-1">
                <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-600 hover:bg-slate-100 disabled:opacity-50 disabled:cursor-not-allowed">Prev</button>
                <button @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-600 hover:bg-slate-100 disabled:opacity-50 disabled:cursor-not-allowed">Next</button>
            </div>
        </div>
    </div>

    <!-- ==============================================
         MODAL FORM
         ============================================== -->
    <div v-if="isModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade">
            <!-- Header -->
            <div class="px-6 py-5 bg-gradient-to-r from-rose-50 to-white border-b border-rose-100 flex items-center justify-between">
                <h3 class="text-lg font-black text-rose-900 tracking-tight flex items-center gap-2">
                    <span>{{ editId ? '✏️ Edit' : '➕ Tambah' }}</span> Pelanggaran
                </h3>
                <button @click="closeModal" class="text-slate-400 hover:text-rose-500 transition-colors bg-white w-8 h-8 rounded-full flex items-center justify-center border border-slate-200 shadow-sm">✕</button>
            </div>

            <!-- Body -->
            <form @submit.prevent="saveData" class="p-6 space-y-5">
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Nama Pelanggaran</label>
                    <textarea v-model="form.nama_pelanggaran" required rows="2" placeholder="Contoh: Terlambat datang ke sekolah..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all text-sm font-semibold text-slate-700 outline-none resize-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Kategori</label>
                        <select v-model="form.jenis" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all text-sm font-semibold text-slate-700 outline-none">
                            <option value="Ringan">Ringan</option>
                            <option value="Sedang">Sedang</option>
                            <option value="Berat">Berat</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Bobot Poin</label>
                        <input type="number" v-model.number="form.bobot" required min="1" max="1000" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all text-sm font-black text-slate-700 text-center outline-none">
                    </div>
                </div>

                <!-- Footer -->
                <div class="pt-4 mt-6 border-t border-slate-100 flex gap-3">
                    <button type="button" @click="closeModal" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all text-xs uppercase tracking-widest">Batal</button>
                    <button type="submit" :disabled="isSaving" class="flex-1 py-3 bg-rose-600 hover:bg-rose-700 disabled:bg-rose-400 text-white font-bold rounded-xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        <span v-if="isSaving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        <span v-else>Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==============================================
         MODAL DELETE
         ============================================== -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade text-center">
            <div class="p-8">
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner border-4 border-white ring-4 ring-rose-50">🗑️</div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Hapus Referensi?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Yakin ingin menghapus pelanggaran <span class="font-bold text-slate-800">"{{ deleteTarget?.nama_pelanggaran }}"</span>?
                </p>
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isDeleteModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all text-xs uppercase tracking-widest">Batal</button>
                    <button @click="executeDelete" class="flex-1 py-3 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast -->
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
  layout: 'bk',
  middleware: 'bk',
  title: 'Master Pelanggaran'
})

const isLoading = ref(true)
const pelanggarans = ref([])
const currentPage = ref(1)
const totalPages = ref(1)
const filterJenis = ref('Semua')
const searchQuery = ref('')
let searchTimeout = null

const fetchPelanggarans = async (page = 1) => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        let url = `http://localhost:8000/api/bk/pelanggaran?page=${page}&jenis=${filterJenis.value}`
        if (searchQuery.value) url += `&search=${searchQuery.value}`
        
        const response = await $fetch(url, {
            headers: { Authorization: `Bearer ${token}` }
        })
        
        if (response.success) {
            pelanggarans.value = response.data.data
            currentPage.value = response.data.current_page
            totalPages.value = response.data.last_page
        }
    } catch (error) {
        console.error('Failed to fetch:', error)
        displayToast('Gagal mengambil data.', 'error')
    } finally {
        isLoading.value = false
    }
}

const changePage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        fetchPelanggarans(page)
    }
}

const debounceSearch = () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        fetchPelanggarans(1)
    }, 500)
}

// FORM MODAL
const isModalOpen = ref(false)
const isSaving = ref(false)
const editId = ref(null)
const form = ref({ nama_pelanggaran: '', bobot: 5, jenis: 'Ringan' })

const openModal = (data = null) => {
    if (data) {
        editId.value = data.id
        form.value = { nama_pelanggaran: data.nama_pelanggaran, bobot: data.bobot, jenis: data.jenis }
    } else {
        editId.value = null
        form.value = { nama_pelanggaran: '', bobot: 5, jenis: 'Ringan' }
    }
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
}

const saveData = async () => {
    isSaving.value = true
    const token = useCookie('auth_token').value
    try {
        const url = editId.value 
            ? `http://localhost:8000/api/bk/pelanggaran/${editId.value}`
            : `http://localhost:8000/api/bk/pelanggaran`
        
        const method = editId.value ? 'PUT' : 'POST'
        
        const response = await $fetch(url, {
            method,
            headers: { Authorization: `Bearer ${token}` },
            body: form.value
        })
        
        if (response.success) {
            closeModal()
            displayToast(response.message, 'success')
            fetchPelanggarans(currentPage.value)
        }
    } catch (error) {
        displayToast(error.response?._data?.message || 'Gagal menyimpan.', 'error')
    } finally {
        isSaving.value = false
    }
}

// DELETE MODAL
const isDeleteModalOpen = ref(false)
const deleteTarget = ref(null)

const confirmDelete = (p) => {
    deleteTarget.value = p
    isDeleteModalOpen.value = true
}

const executeDelete = async () => {
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/bk/pelanggaran/${deleteTarget.value.id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            isDeleteModalOpen.value = false
            displayToast(response.message, 'success')
            fetchPelanggarans(currentPage.value)
        }
    } catch (error) {
        isDeleteModalOpen.value = false
        displayToast(error.response?._data?.message || 'Gagal menghapus.', 'error')
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
    fetchPelanggarans()
})
</script>

<style scoped>
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }
@keyframes slideUpFade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-slideUpFade { animation: slideUpFade 0.3s ease-out forwards; }
@keyframes slideUp { from { opacity: 0; transform: translate(-50%, 20px); } to { opacity: 1; transform: translate(-50%, 0); } }
.animate-slideUp { animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
