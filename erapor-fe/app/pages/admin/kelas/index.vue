<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    
<!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-2 grid grid-cols-2 gap-2 z-20">
        <button type="button" @click="activeTab = 'form'" :class="activeTab === 'form' ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 ring-2 ring-emerald-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'" class="rounded-xl flex items-center justify-center py-2 px-1 transition-all active:scale-95">
          <span class="text-lg mr-1.5 transition-transform" :class="activeTab === 'form' ? 'scale-110' : ''">🏫</span>
          <span class="text-[10px] font-black uppercase tracking-wider">Entry Kelas</span>
        </button>
        <button type="button" @click="activeTab = 'table'" :class="activeTab === 'table' ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 ring-2 ring-emerald-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'" class="rounded-xl flex items-center justify-center py-2 px-1 transition-all active:scale-95">
          <span class="text-lg mr-1.5 transition-transform" :class="activeTab === 'table' ? 'scale-110' : ''">📋</span>
          <span class="text-[10px] font-black uppercase tracking-wider">Database</span>
        </button>
      </div>

      <!-- Panel Dock Kiri -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all', activeTab === 'form' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[60px]' : '']">
        
        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <div class="animate-fadeIn">
                <div class="p-6 shrink-0">
                  <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl p-5 border border-emerald-500 shadow-sm relative overflow-hidden flex items-center gap-4">
                    <div class="w-12 h-12 flex items-center justify-center text-3xl shrink-0 relative z-10">🏫</div>
                    <div class="relative z-10">
                        <h3 class="text-sm font-black uppercase tracking-widest text-white">Data Kelas</h3>
                        <p class="text-[10px] text-emerald-100 font-semibold uppercase mt-0.5">{{ isEditing ? 'Mode Update' : 'Mode Tambah Baru' }}</p>
                    </div>
                    <div class="absolute right-0 bottom-0 opacity-20 text-white">
                      <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"></path></svg>
                    </div>
                  </div>
                </div>
                
                <div class="px-6 pb-6">
                    <form @submit.prevent="saveKelas" class="space-y-5">
                        
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Konsentrasi Keahlian (Jurusan)</label>
                            <select v-model="kelasForm.kejuruan_id" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-bold text-slate-700 cursor-pointer appearance-none">
                                <option value="" disabled>-- Pilih Konsentrasi --</option>
                                <option v-for="k in dependencies.kejuruans" :key="k.id" :value="k.id">
                                    {{ k.kode_konsentrasi }} - {{ k.nama_konsentrasi }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kurikulum</label>
                            <select v-model="kelasForm.kurikulum_id" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-bold text-slate-700 cursor-pointer appearance-none">
                                <option value="" disabled>-- Pilih Kurikulum --</option>
                                <option v-for="k in dependencies.kurikulums" :key="k.id" :value="k.id">
                                    {{ k.singkatan }} ({{ k.nama_kurikulum }})
                                </option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Tingkat</label>
                                <select v-model="kelasForm.tingkat" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-bold text-slate-700 cursor-pointer text-center text-lg appearance-none">
                                    <option value="" disabled>-</option>
                                    <option value="X">X</option>
                                    <option value="XI">XI</option>
                                    <option value="XII">XII</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Nama Spesifik</label>
                                <input type="text" v-model="kelasForm.nama_kelas" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-black uppercase text-center text-slate-800" placeholder="Misal: TAV 1">
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex gap-3">
                            <button v-if="isEditing" type="button" @click="resetForm" class="flex-1 py-3.5 bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-100 font-bold rounded-2xl transition-all uppercase tracking-widest text-xs">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSaving" class="flex-1 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-2xl shadow-lg shadow-emerald-500/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 uppercase tracking-widest text-xs">
                                <span v-if="isSaving" class="animate-spin text-lg">⏳</span>
                                <span v-else class="text-lg">💾</span> {{ isEditing ? 'Simpan Perubahan' : 'Tambah Kelas' }}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative', activeTab === 'table' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[60px]' : '']">
        <div class="p-2 sm:p-6 lg:p-8 max-w-5xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-2xl sm:rounded-[2rem] shadow-[0_2px_15px_-3px_rgba(0,0,0,0.05)] overflow-hidden flex flex-col flex-1 relative min-h-0">
            <!-- Table Header & Filters -->
            <div class="px-6 py-5 border-b border-slate-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shrink-0 z-10 bg-white">
            <div class="flex items-center gap-4 w-full sm:w-auto">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 shadow-sm flex items-center justify-center text-2xl text-white hidden sm:flex">📋</div>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-sm font-black uppercase tracking-widest text-emerald-700">Database Rombel</h3>
                </div>
            </div>
            
            <div class="flex flex-row gap-2 w-full sm:w-auto">
                <select v-model="tingkatFilter" @change="fetchKelas(1)" class="w-1/2 sm:w-40 py-2 px-3 sm:px-4 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-emerald-500 text-[9px] sm:text-[11px] font-black uppercase tracking-wider text-slate-600 cursor-pointer shadow-sm appearance-none">
                    <option value="">Semua Tingkat</option>
                    <option value="X">Kelas X</option>
                    <option value="XI">Kelas XI</option>
                    <option value="XII">Kelas XII</option>
                </select>
                <div class="relative w-1/2 sm:w-auto">
                    <span class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center text-slate-400">🔍</span>
                    <input type="text" v-model="searchQuery" @input="debouncedFetch" placeholder="Cari Kelas..." class="w-full sm:w-48 pl-8 sm:pl-9 pr-3 sm:pr-4 py-2 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-emerald-500 text-[10px] sm:text-xs font-bold transition-all shadow-sm placeholder-slate-400">
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60">
            <div class="w-8 h-8 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</span>
        </div>

        <!-- Table Content -->
        <div v-else class="flex-1 overflow-y-auto overflow-x-auto custom-scrollbar relative bg-white">
            <div v-if="!kelasData.data || kelasData.data.length === 0" class="text-center py-16 flex flex-col items-center justify-center h-full min-h-[400px]">
                <div class="text-6xl opacity-30 mb-4 block">🌵</div>
                <h3 class="text-lg font-black text-slate-700 mb-2 uppercase tracking-widest">Data Kosong</h3>
                <p class="text-slate-500 text-sm font-semibold max-w-sm">Data kelas tidak ditemukan.</p>
                <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-2 font-bold" v-if="searchQuery || tingkatFilter">Coba ubah filter pencarian Anda.</p>
            </div>

            <!-- Desktop Table -->
            <table v-else class="w-full text-left border-collapse min-w-full">
                <thead class="hidden sm:table-header-group sticky top-0 z-10 bg-slate-50 border-b border-slate-200 shadow-sm">
                    <tr class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-500">
                        <th class="p-4 w-16 text-center">#</th>
                        <th class="p-4">Nama Kelas</th>
                        <th class="p-4">Tingkat</th>

                        <th class="p-4">Wali Kelas</th>
                        <th class="p-4 text-center w-48">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-xs flex flex-col sm:table-row-group">
                    <tr v-for="(k, index) in kelasData.data" :key="k.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors group bg-white flex flex-col sm:table-row p-4 sm:p-0">
                        <td class="px-0 py-1 sm:p-4 text-left sm:text-center text-[10px] font-bold text-slate-400 flex sm:table-cell items-center justify-between">
                            <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-slate-400">Nomor</span>
                            <span>{{ (kelasData.current_page - 1) * kelasData.per_page + index + 1 }}</span>
                        </td>
                        <td class="px-0 py-1 sm:p-4 flex sm:table-cell items-center justify-between border-b sm:border-0 border-slate-50 pb-2 sm:pb-4 mb-1 sm:mb-0">
                            <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-slate-400">Nama Kelas</span>
                            <span class="font-black text-emerald-700 text-sm tracking-wide">{{ k.nama_kelas }}</span>
                        </td>
                        <td class="px-0 py-1 sm:p-4 flex sm:table-cell items-center justify-between border-b sm:border-0 border-slate-50 pb-2 sm:pb-4 mb-1 sm:mb-0">
                            <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-slate-400">Tingkat</span>
                            <span class="inline-flex px-2.5 py-1 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                {{ k.tingkat }}
                            </span>
                        </td>

                        <td class="px-0 py-1 sm:p-4 flex sm:table-cell items-center justify-between border-b sm:border-0 border-slate-50 pb-3 sm:pb-4 mb-2 sm:mb-0">
                            <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-slate-400">Wali Kelas</span>
                            <div class="flex items-center gap-2" v-if="k.wali_kelas?.guru">
                                <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-[10px] font-bold shrink-0">
                                    {{ k.wali_kelas.guru.name.substring(0, 2).toUpperCase() }}
                                </div>
                                <span class="font-bold text-slate-700 text-[11px] truncate max-w-[150px]">{{ k.wali_kelas.guru.name }}</span>
                            </div>
                            <span v-else class="text-[10px] font-bold text-slate-400 italic">Belum Diatur</span>
                        </td>
                        <td class="px-0 pt-2 sm:p-4 text-center">
                            <div class="flex items-center justify-center gap-2 opacity-100 xl:opacity-0 xl:group-hover:opacity-100 transition-opacity">
                                <NuxtLink :to="`/admin/kelas/${k.id}/siswa`" class="px-3 h-10 sm:h-8 rounded-xl sm:rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-600 hover:text-emerald-700 hover:bg-emerald-100 flex items-center justify-center transition-all shadow-sm font-bold text-[10px] uppercase tracking-wider" title="Anggota Rombel">👥 Anggota</NuxtLink>
                                <button @click="editKelas(k)" class="w-10 h-10 sm:w-8 sm:h-8 rounded-xl sm:rounded-lg bg-white border border-slate-100 text-slate-400 hover:text-indigo-500 hover:border-indigo-200 hover:bg-indigo-50 flex items-center justify-center transition-all shadow-sm" title="Edit">\n                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>\n                                </button>
                                <button @click="confirmDelete(k.id, k.nama_kelas)" class="w-10 h-10 sm:w-8 sm:h-8 rounded-xl sm:rounded-lg bg-white border border-slate-100 text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 flex items-center justify-center transition-all shadow-sm" title="Hapus">\n                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>\n                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="kelasData.last_page > 1" class="p-4 sm:p-6 bg-slate-50 border-t border-slate-200 flex items-center justify-between shrink-0">
            <span class="hidden sm:inline-block text-[10px] font-black uppercase text-slate-400 tracking-widest">Halaman {{ kelasData.current_page }} dari {{ kelasData.last_page }}</span>
            <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-end">
                <button @click="fetchKelas(kelasData.current_page - 1)" :disabled="kelasData.current_page === 1" class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 disabled:opacity-50 shadow-sm transition-all">Prev</button>
                <span class="sm:hidden text-[10px] font-black uppercase text-slate-400 tracking-widest">{{ kelasData.current_page }} / {{ kelasData.last_page }}</span>
                <button @click="fetchKelas(kelasData.current_page + 1)" :disabled="kelasData.current_page === kelasData.last_page" class="px-4 py-2.5 bg-emerald-50 border border-emerald-100 rounded-xl text-[10px] font-black uppercase tracking-widest text-emerald-700 hover:bg-emerald-100 disabled:opacity-50 shadow-sm transition-all">Next</button>
            </div>
        </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ==============================================
         MODAL KONFIRMASI HAPUS (Overlay)
         ============================================== -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade text-center">
            <div class="p-8">
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner border-4 border-white ring-4 ring-rose-50">
                    ⚠️
                </div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Hapus Kelas?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Anda yakin ingin menghapus kelas:<br>
                    <span class="font-bold text-slate-800">{{ deleteTarget.name }}</span>?
                </p>
                <p class="text-[10px] font-bold text-rose-500 mt-3 p-2 bg-rose-50 rounded-lg">AWAS: Menghapus kelas mungkin berdampak pada data siswa atau penilaian yang terkait dengan kelas ini!</p>
                
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isDeleteModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">
                        Batal
                    </button>
                    <button @click="executeDelete" :disabled="isDeleting" class="flex-1 py-3 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        <span v-if="isDeleting" class="animate-spin text-base">⏳</span>
                        <span v-else>Ya, Hapus</span>
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
  layout: 'admin',
  middleware: 'admin',
  title: 'Master Kelas'
})

const windowWidth = ref(1024) 
const isDesktop = computed(() => windowWidth.value >= 1280) // xl breakpoint

// Mobile Tab State
const activeTab = ref('table')

// Main State
const dependencies = ref({ kejuruans: [], kurikulums: [], tahunAjarans: [] })
const kelasData = ref({})
const isLoading = ref(true)
const isSaving = ref(false)

// Delete State
const isDeleteModalOpen = ref(false)
const isDeleting = ref(false)
const deleteTarget = ref({ id: null, name: '' })

// Form State
const isEditing = ref(false)
const kelasForm = ref({
    id: null,
    tahun_ajaran_id: '',
    kejuruan_id: '',
    kurikulum_id: '',
    tingkat: '',
    nama_kelas: ''
})

// Filter & Search State
const searchQuery = ref('')
const tingkatFilter = ref('')
let searchTimeout = null

// Navigation
const navigateToSiswa = (id) => {
    navigateTo(`/admin/kelas/${id}/siswa`)
}

// === API CALLS ===

const fetchDependencies = async () => {
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/admin/kelas/dependencies', {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            dependencies.value = {
                kejuruans: response.kejuruans,
                kurikulums: response.kurikulums,
                tahunAjarans: response.tahunAjarans
            }
        }
    } catch (error) {
        console.error('Failed to fetch dependencies:', error)
    }
}

const fetchKelas = async (page = 1) => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/admin/kelas?page=${page}`, {
            headers: { Authorization: `Bearer ${token}` },
            query: {
                search: searchQuery.value,
                tingkat: tingkatFilter.value
            }
        })
        if (response.success) {
            kelasData.value = response.data
        }
    } catch (error) {
        console.error('Failed to fetch kelas:', error)
    } finally {
        isLoading.value = false
    }
}

const debouncedFetch = () => {
    if (searchTimeout) clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        fetchKelas(1)
    }, 500)
}

const saveKelas = async () => {
    if (!kelasForm.value.tahun_ajaran_id) {
        const activeTA = dependencies.value.tahunAjarans?.find(ta => ta.is_aktif);
        if (activeTA) {
            kelasForm.value.tahun_ajaran_id = activeTA.id;
        } else {
            useSwal().toast('Tidak ada Tahun Ajaran aktif.', 'error');
            return;
        }
    }

    isSaving.value = true
    const token = useCookie('auth_token').value
    const method = isEditing.value ? 'PUT' : 'POST'
    const url = isEditing.value 
        ? `${import.meta.env.VITE_API_BASE_URL}/api/admin/kelas/${kelasForm.value.id}`
        : `${import.meta.env.VITE_API_BASE_URL}/api/admin/kelas`
    
    try {
        const response = await $fetch(url, {
            method: method,
            headers: { Authorization: `Bearer ${token}` },
            body: kelasForm.value
        })
        if (response.success) {
            useSwal().toast(response.message)
            resetForm()
            fetchKelas(kelasData.value.current_page || 1)
            if (!isDesktop.value) activeTab.value = 'table'
        }
    } catch (error) {
        console.error('Save failed:', error)
        useSwal().toast('Gagal menyimpan kelas. Periksa kembali form Anda.', 'error')
    } finally {
        isSaving.value = false
    }
}

const editKelas = (k) => {
    isEditing.value = true
    kelasForm.value = {
        id: k.id,
        tahun_ajaran_id: k.tahun_ajaran_id,
        kejuruan_id: k.kejuruan_id,
        kurikulum_id: k.kurikulum_id,
        tingkat: k.tingkat,
        nama_kelas: k.nama_kelas
    }
    activeTab.value = 'form'
}

const resetForm = () => {
    isEditing.value = false
    kelasForm.value = {
        id: null,
        tahun_ajaran_id: '',
        kejuruan_id: '',
        kurikulum_id: '',
        tingkat: '',
        nama_kelas: ''
    }
}

const confirmDelete = (id, name) => {
    deleteTarget.value = { id, name }
    isDeleteModalOpen.value = true
}

const executeDelete = async () => {
    if (!deleteTarget.value.id) return
    isDeleting.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/admin/kelas/${deleteTarget.value.id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            isDeleteModalOpen.value = false
            useSwal().toast(response.message)
            fetchKelas(kelasData.value.current_page || 1)
        }
    } catch (error) {
        console.error('Delete failed:', error)
        useSwal().toast('Gagal menghapus data kelas.', 'error')
    } finally {
        isDeleting.value = false
    }
}

onMounted(async () => {
    windowWidth.value = window.innerWidth
    window.addEventListener('resize', () => { windowWidth.value = window.innerWidth })
    
    if (isDesktop.value) {
        activeTab.value = 'form' 
    } else {
        activeTab.value = 'table' 
    }

    await fetchDependencies()
    fetchKelas()
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }

.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #94a3b8;
}
</style>
