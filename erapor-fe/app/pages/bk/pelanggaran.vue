<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-2 grid grid-cols-2 gap-2 z-20">
        <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTabMobile = tab.id"
          :class="activeTabMobile === tab.id ? 'bg-gradient-to-br from-rose-500 to-rose-600 text-white shadow-md shadow-rose-500/20 ring-2 ring-rose-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="rounded-xl flex flex-col items-center justify-center py-2 px-1 transition-all active:scale-95">
          <span class="text-lg mb-0.5 transition-transform" :class="activeTabMobile === tab.id ? 'scale-110' : ''">{{ tab.icon }}</span>
          <span class="text-[9px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
        </button>
      </div>

      <!-- Panel Dock Kiri (Filter & Form) -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all', activeTabMobile === 'form' || isDesktop ? 'flex' : 'hidden xl:flex', !isDesktop ? 'pt-[60px]' : '']">
        
        <div class="p-6 shrink-0">
          <div class="bg-gradient-to-r from-rose-600 to-rose-700 rounded-2xl p-5 border border-rose-500 shadow-sm relative overflow-hidden flex items-center gap-4">
            <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0 relative z-10">📋</div>
            <div class="relative z-10">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">Master Pelanggaran</h3>
                <p class="text-[10px] text-rose-100 font-semibold uppercase mt-0.5">Kelola referensi poin</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-20 text-white">
              <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"></path></svg>
            </div>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar px-6 pb-6 space-y-6">
            
            <!-- Pencarian & Filter -->
            <div class="space-y-3">
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Pencarian</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
                        <input type="text" v-model="searchQuery" @input="debounceSearch" placeholder="Cari pelanggaran..." 
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border-2 border-slate-200/70 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all text-xs font-bold outline-none text-slate-700">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Filter Kategori</label>
                    <div class="flex bg-slate-100 p-1 rounded-xl">
                        <button v-for="j in ['Semua', 'Ringan', 'Sedang', 'Berat']" :key="j" @click="filterJenis = j; fetchPelanggarans()"
                            class="flex-1 px-2 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-lg transition-all duration-300"
                            :class="filterJenis === j ? 'bg-white text-rose-600 shadow-sm' : 'text-slate-400 hover:text-slate-600'">
                            {{ j }}
                        </button>
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            <!-- Form Edit/Tambah -->
            <div class="bg-rose-50/50 p-4 rounded-2xl border border-rose-100 relative">
                <div v-if="editId" class="absolute -top-3 left-4 bg-amber-100 text-amber-700 border border-amber-200 text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full flex items-center gap-1 shadow-sm">
                    <span class="animate-pulse">✏️</span> Mode Edit
                </div>

                <h4 class="text-xs font-black text-rose-800 uppercase tracking-wider mb-4 flex items-center gap-2 mt-2">
                  <span>{{ editId ? '✏️' : '➕' }}</span> {{ editId ? 'Edit Referensi' : 'Tambah Referensi' }}
                </h4>
                
                <form @submit.prevent="saveData" class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Nama Pelanggaran</label>
                        <textarea v-model="form.nama_pelanggaran" required rows="3" placeholder="Contoh: Terlambat datang..." class="w-full px-4 py-3 bg-white border-2 border-slate-200/70 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all text-xs font-semibold text-slate-700 outline-none resize-none"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Kategori</label>
                            <select v-model="form.jenis" required class="w-full px-3 py-2.5 bg-white border-2 border-slate-200/70 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all text-xs font-semibold text-slate-700 outline-none">
                                <option value="Ringan">Ringan</option>
                                <option value="Sedang">Sedang</option>
                                <option value="Berat">Berat</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Bobot Poin</label>
                            <input type="number" v-model.number="form.bobot" required min="1" max="1000" class="w-full px-3 py-2.5 bg-white border-2 border-slate-200/70 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all text-xs font-black text-slate-700 text-center outline-none">
                        </div>
                    </div>

                    <div class="pt-2 flex gap-2">
                        <button v-if="editId" type="button" @click="resetForm" class="flex-1 py-3 bg-white border border-rose-200 hover:bg-rose-50 hover:text-rose-700 text-rose-500 font-bold rounded-2xl transition-all text-[10px] uppercase tracking-widest shadow-sm">Batal</button>
                        <button type="submit" :disabled="isSaving" class="flex-[2] py-3 bg-gradient-to-r from-rose-500 to-rose-600 hover:-translate-y-0.5 disabled:bg-rose-400 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/30 transition-all text-[10px] uppercase tracking-widest flex items-center justify-center gap-2">
                            <span v-if="isSaving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                            <span v-else>{{ editId ? 'Update Data' : 'Simpan Data' }}</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
      </div>

      <!-- Panel Flow Kanan (Data Table) -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative', activeTabMobile === 'table' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[60px]' : '']">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            <!-- Header Flow -->
            <div class="px-6 py-5 bg-white border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shrink-0 z-10 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-500 hidden sm:flex">📊</div>
                <div>
                    <div class="flex items-center gap-3">
                        <h3 class="text-sm font-black uppercase tracking-widest text-slate-700">Data Pelanggaran</h3>
                        <span v-if="activeTahunAjaran" class="bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-md text-[10px] font-black tracking-widest border border-indigo-100 uppercase">
                            TA. {{ activeTahunAjaran }}
                        </span>
                    </div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Total: {{ totalItems }} Referensi</p>
                </div>
            </div>
            
            <!-- Refresh Button -->
            <button @click="fetchPelanggarans(currentPage)" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50 transition-all shadow-sm active:scale-95">
                <span :class="isLoading ? 'animate-spin' : ''">↻</span>
            </button>
        </div>

        <!-- Flow Content (Table) -->
        <div class="flex-1 overflow-auto bg-slate-50 custom-scrollbar relative">
            <div v-if="isLoading" class="absolute inset-0 z-10 bg-white/50 backdrop-blur-sm flex items-center justify-center">
                <div class="w-10 h-10 border-4 border-rose-500 border-t-transparent rounded-full animate-spin"></div>
            </div>

            <div v-if="pelanggarans.length === 0 && !isLoading" class="flex flex-col items-center justify-center h-full p-8 text-center opacity-60">
                <div class="text-6xl mb-4">📭</div>
                <p class="text-sm font-bold text-slate-600">Tidak ada data.</p>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-2">Coba sesuaikan filter atau tambah baru</p>
            </div>

            <table v-else class="w-full text-left border-collapse">
                <thead class="sticky top-0 z-20 bg-slate-100/90 backdrop-blur-md shadow-sm border-b border-slate-200">
                    <tr class="text-[9px] font-black uppercase tracking-widest text-slate-400">
                        <th class="p-4 pl-6 w-16">No</th>
                        <th class="p-4">Nama Pelanggaran</th>
                        <th class="p-4 w-32">Kategori</th>
                        <th class="p-4 text-center w-24">Poin</th>
                        <th class="p-4 text-right pr-6 w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <tr v-for="(p, index) in pelanggarans" :key="p.id" class="border-b border-slate-200/50 hover:bg-white transition-colors group bg-slate-50/30">
                        <td class="p-4 pl-6 text-xs font-bold text-slate-400">{{ (currentPage - 1) * 15 + index + 1 }}</td>
                        <td class="p-4">
                            <p class="font-bold text-slate-800 text-[12px] leading-relaxed max-w-lg">{{ p.nama_pelanggaran }}</p>
                        </td>
                        <td class="p-4">
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-[9px] font-black tracking-widest uppercase border"
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
                            <div class="flex items-center justify-end gap-1.5 opacity-100 lg:opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="editPelanggaran(p)" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-amber-500 hover:border-amber-200 hover:bg-amber-50 flex items-center justify-center transition-all shadow-sm" title="Edit">✏️</button>
                                <button @click="confirmDelete(p)" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50 flex items-center justify-center transition-all shadow-sm" title="Hapus">🗑️</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Flow -->
        <div class="p-4 bg-white border-t border-slate-200 shadow-sm z-10 shrink-0 flex items-center justify-between" v-if="totalPages > 1">
            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Hal {{ currentPage }} / {{ totalPages }}</span>
            <div class="flex gap-2">
                <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm transition-all active:scale-95">Prev</button>
                <button @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm transition-all active:scale-95">Next</button>
            </div>
        </div>
        </div>
      </div>
    </div>
  </div>


    <!-- ==============================================
         MODAL DELETE (TETAP PERLU UNTUK KONFIRMASI)
         ============================================== -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade text-center">
            <div class="p-8">
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner border-4 border-white ring-4 ring-rose-50">⚠️</div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Hapus Referensi?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Yakin ingin menghapus pelanggaran <span class="font-bold text-slate-800">"{{ deleteTarget?.nama_pelanggaran }}"</span>?
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

definePageMeta({
  layout: 'bk',
  middleware: 'bk',
  title: 'Master Pelanggaran'
})

// UI State
const windowWidth = ref(1024)
const isDesktop = computed(() => windowWidth.value >= 1280)
const activeTabMobile = ref('table')
const mobileTabs = [
  { id: 'form', title: 'Input', icon: '📋' },
  { id: 'table', title: 'Data', icon: '📊' }
]

const isLoading = ref(true)
const pelanggarans = ref([])
const currentPage = ref(1)
const totalPages = ref(1)
const totalItems = ref(0)
const filterJenis = ref('Semua')
const searchQuery = ref('')
const activeTahunAjaran = ref('')
let searchTimeout = null

const tokenCookie = useCookie('auth_token')

const fetchPelanggarans = async (page = 1) => {
    isLoading.value = true
    try {
        let url = `${import.meta.env.VITE_API_BASE_URL}/api/bk/pelanggaran?page=${page}&jenis=${filterJenis.value}`
        if (searchQuery.value) url += `&search=${searchQuery.value}`
        
        const response = await $fetch(url, {
            headers: { Authorization: `Bearer ${tokenCookie.value}` }
        })
        
        if (response.success) {
            pelanggarans.value = response.data.data
            currentPage.value = response.data.current_page
            totalPages.value = response.data.last_page
            totalItems.value = response.data.total
            if (response.tahun_aktif) {
                activeTahunAjaran.value = response.tahun_aktif.tahun
            }
        }
    } catch (error) {
        console.error('Failed to fetch:', error)
        useSwal().toast('Gagal mengambil data.', 'error')
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

// FORM DOCK STATE
const isSaving = ref(false)
const editId = ref(null)
const form = ref({ nama_pelanggaran: '', bobot: 5, jenis: 'Ringan' })

const editPelanggaran = (data) => {
    editId.value = data.id
    form.value = { nama_pelanggaran: data.nama_pelanggaran, bobot: data.bobot, jenis: data.jenis }
    activeTabMobile.value = 'form' // Pindah tab di mobile
}

const resetForm = () => {
    editId.value = null
    form.value = { nama_pelanggaran: '', bobot: 5, jenis: 'Ringan' }
}

const saveData = async () => {
    isSaving.value = true
    try {
        const url = editId.value 
            ? `${import.meta.env.VITE_API_BASE_URL}/api/bk/pelanggaran/${editId.value}`
            : `${import.meta.env.VITE_API_BASE_URL}/api/bk/pelanggaran`
        
        const method = editId.value ? 'PUT' : 'POST'
        
        const response = await $fetch(url, {
            method,
            headers: { Authorization: `Bearer ${tokenCookie.value}` },
            body: form.value
        })
        
        if (response.success) {
            resetForm()
            useSwal().toast(response.message, 'success')
            fetchPelanggarans(currentPage.value)
            if(!isDesktop.value) activeTabMobile.value = 'table'
        }
    } catch (error) {
        useSwal().toast(error.response?._data?.message || 'Gagal menyimpan.', 'error')
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
    try {
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/bk/pelanggaran/${deleteTarget.value.id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${tokenCookie.value}` }
        })
        if (response.success) {
            isDeleteModalOpen.value = false
            useSwal().toast(response.message, 'success')
            // if we are editing the deleted item, reset the form
            if (editId.value === deleteTarget.value.id) resetForm()
            fetchPelanggarans(currentPage.value)
        }
    } catch (error) {
        isDeleteModalOpen.value = false
        useSwal().toast(error.response?._data?.message || 'Gagal menghapus.', 'error')
    }
}

onMounted(() => {
    windowWidth.value = window.innerWidth
    window.addEventListener('resize', () => { windowWidth.value = window.innerWidth })
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
