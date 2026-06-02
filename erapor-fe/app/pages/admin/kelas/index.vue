<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- MOBILE VIEW TABS -->
    <div class="xl:hidden mb-8 mt-2">
      <div class="grid grid-cols-2 gap-3">
        <button type="button" @click="activeTab = 'form'" :class="activeTab === 'form' ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 ring-2 ring-emerald-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'" class="rounded-2xl flex flex-col items-center justify-center py-5 transition-all active:scale-95">
          <span class="text-3xl mb-2 transition-transform" :class="activeTab === 'form' ? 'scale-110' : ''">🏫</span>
          <span class="text-[10px] font-black uppercase tracking-wider">Entry Kelas</span>
        </button>
        <button type="button" @click="activeTab = 'table'" :class="activeTab === 'table' ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 ring-2 ring-emerald-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'" class="rounded-2xl flex flex-col items-center justify-center py-5 transition-all active:scale-95">
          <span class="text-3xl mb-2 transition-transform" :class="activeTab === 'table' ? 'scale-110' : ''">📋</span>
          <span class="text-[10px] font-black uppercase tracking-wider">Database</span>
        </button>
      </div>
    </div>

    <!-- MAIN GRID LAYOUT -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-8 items-start">
      
      <!-- ==============================================
           KIRI: PANEL ENTRY (Form) - xl:col-span-1
           ============================================== -->
      <div class="xl:col-span-1 space-y-6 xl:sticky xl:top-6" v-show="isDesktop || activeTab === 'form'">
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden animate-slideUpFade">
            <div class="p-6 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-100 flex items-center gap-4">
                <span class="text-3xl">🏫</span>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-white">{{ isEditing ? 'Update Kelas' : 'Entry Kelas' }}</h3>
                    <p class="text-[10px] text-emerald-400 font-semibold uppercase mt-0.5">Rombongan Belajar</p>
                </div>
            </div>
            
            <div class="p-6 md:p-8">
                <form @submit.prevent="saveKelas" class="space-y-5">
                    
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Konsentrasi Keahlian (Jurusan)</label>
                        <select v-model="kelasForm.kejuruan_id" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-bold text-slate-700 cursor-pointer">
                            <option value="" disabled>-- Pilih Konsentrasi --</option>
                            <option v-for="k in dependencies.kejuruans" :key="k.id" :value="k.id">
                                {{ k.kode_konsentrasi }} - {{ k.nama_konsentrasi }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kurikulum</label>
                        <select v-model="kelasForm.kurikulum_id" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-bold text-slate-700 cursor-pointer">
                            <option value="" disabled>-- Pilih Kurikulum --</option>
                            <option v-for="k in dependencies.kurikulums" :key="k.id" :value="k.id">
                                {{ k.singkatan }} ({{ k.nama_kurikulum }})
                            </option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Tingkat</label>
                            <select v-model="kelasForm.tingkat" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-bold text-slate-700 cursor-pointer text-center text-lg">
                                <option value="" disabled>-</option>
                                <option value="X">X</option>
                                <option value="XI">XI</option>
                                <option value="XII">XII</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Nama Spesifik</label>
                            <input type="text" v-model="kelasForm.nama_kelas" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-black uppercase text-center text-slate-800" placeholder="Misal: X TKJ 1">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex gap-3">
                         <button v-if="isEditing" type="button" @click="resetForm" class="px-4 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSaving" class="flex-1 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-2xl shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 uppercase tracking-widest text-xs">
                            <span v-if="isSaving" class="animate-spin text-lg">⏳</span>
                            <span v-else class="text-lg">💾</span> {{ isEditing ? 'Update' : 'Simpan' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>

      </div>

      <!-- ==============================================
           KANAN: DATABASE TABLE - xl:col-span-3
           ============================================== -->
      <div class="xl:col-span-3" v-show="isDesktop || activeTab === 'table'">
         <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col min-h-[500px]">
            
            <!-- Table Header & Filters -->
            <div class="p-6 md:p-8 bg-slate-50/50 border-b border-slate-100">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white shadow-sm border border-slate-200 flex items-center justify-center text-2xl hidden sm:flex">📋</div>
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-widest text-emerald-700">Database Rombel</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">List Kelas Satuan Pendidikan</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                        <select v-model="tingkatFilter" @change="fetchKelas(1)" class="w-full sm:w-auto px-4 py-2.5 rounded-2xl border border-slate-200 bg-white focus:ring-2 focus:ring-emerald-500/20 text-xs font-bold text-slate-600 uppercase tracking-widest cursor-pointer shadow-sm">
                            <option value="">Semua Tingkat</option>
                            <option value="X">Kelas X</option>
                            <option value="XI">Kelas XI</option>
                            <option value="XII">Kelas XII</option>
                        </select>
                        <div class="relative w-full sm:w-64">
                            <span class="absolute inset-y-0 left-4 flex items-center text-slate-400">🔍</span>
                            <input type="text" v-model="searchQuery" @input="debouncedFetch" placeholder="Cari Kelas..." class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all text-xs font-semibold bg-white shadow-sm placeholder-slate-300">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60">
                <div class="w-8 h-8 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</span>
            </div>

            <!-- Table Content -->
            <div v-else class="flex-grow flex flex-col">
                <div v-if="!kelasData.data || kelasData.data.length === 0" class="text-center py-16 m-auto">
                    <div class="text-6xl opacity-30 mb-4">🌵</div>
                    <p class="text-sm font-bold text-slate-500">Data kelas tidak ditemukan.</p>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-2 font-bold" v-if="searchQuery || tingkatFilter">Coba ubah filter pencarian Anda.</p>
                </div>

                <!-- Desktop Table -->
                <div v-else class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-slate-200">
                                <th class="p-5 pl-8 w-16 text-center">#</th>
                                <th class="p-5">Nama Kelas</th>
                                <th class="p-5">Konsentrasi</th>
                                <th class="p-5">Kurikulum</th>
                                <th class="p-5 text-right pr-8">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <tr v-for="(k, index) in kelasData.data" :key="k.id" class="border-b border-slate-100 hover:bg-slate-50/80 transition-colors group">
                                <td class="p-4 pl-8 text-center text-xs font-bold text-slate-300">
                                    {{ (kelasData.current_page - 1) * kelasData.per_page + index + 1 }}
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 font-black flex items-center justify-center border border-emerald-100">
                                            {{ k.tingkat }}
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-700">{{ k.nama_kelas }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <p class="text-xs font-bold text-slate-600">{{ k.kejuruan?.nama_konsentrasi || '-' }}</p>
                                    <p class="text-[9px] font-black uppercase text-emerald-500 tracking-widest mt-0.5">{{ k.kejuruan?.kode_konsentrasi || '-' }}</p>
                                </td>
                                <td class="p-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[9px] font-black uppercase tracking-widest bg-slate-100 text-slate-500 border border-slate-200">
                                        {{ k.kurikulum?.singkatan || '-' }}
                                    </span>
                                </td>
                                <td class="p-4 pr-8 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                        <button @click="navigateToSiswa(k.id)" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-indigo-500 hover:border-indigo-200 hover:bg-indigo-50 flex items-center justify-center transition-all shadow-sm" title="Kelola Siswa">🧑‍🎓</button>
                                        <button @click="editKelas(k)" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-emerald-500 hover:border-emerald-200 hover:bg-emerald-50 flex items-center justify-center transition-all shadow-sm" title="Edit">✏️</button>
                                        <button @click="confirmDelete(k.id, k.nama_kelas)" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 flex items-center justify-center transition-all shadow-sm" title="Hapus">🗑️</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div v-if="kelasData.data && kelasData.data.length > 0" class="md:hidden p-4 space-y-4 bg-slate-50">
                    <div v-for="k in kelasData.data" :key="'mob-'+k.id" class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 relative overflow-hidden">
                        <div class="absolute right-4 top-4 flex items-center gap-2 z-10">
                            <button @click="editKelas(k)" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-emerald-500 hover:bg-emerald-50 flex items-center justify-center shadow-sm" title="Edit">✏️</button>
                            <button @click="confirmDelete(k.id, k.nama_kelas)" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:bg-rose-50 flex items-center justify-center shadow-sm" title="Hapus">🗑️</button>
                        </div>
                        <div class="flex items-center gap-4 mb-4 border-b border-slate-100 pb-4 pr-16">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 font-black flex items-center justify-center text-lg border border-emerald-100">
                                {{ k.tingkat }}
                            </div>
                            <div>
                                <h4 class="font-black text-slate-800 text-lg leading-tight">{{ k.nama_kelas }}</h4>
                                <span class="inline-block px-2 py-0.5 mt-1 text-[9px] font-black uppercase tracking-widest bg-slate-100 text-slate-500 rounded border border-slate-200">
                                    {{ k.kurikulum?.singkatan || '-' }}
                                </span>
                            </div>
                        </div>
                        <div class="mb-5">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Konsentrasi</p>
                            <p class="text-xs font-bold text-slate-700">{{ k.kejuruan?.nama_konsentrasi || '-' }}</p>
                        </div>
                        <div class="mt-4 flex flex-col gap-2 pt-4 border-t border-slate-100">
                            <NuxtLink :to="`/admin/kelas/${k.id}/siswa`" class="w-full py-2.5 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 hover:bg-indigo-100 hover:text-indigo-700 text-[10px] uppercase tracking-widest font-bold flex items-center justify-center transition-all">
                                👥 Kelola Siswa
                            </NuxtLink>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="kelasData.last_page > 1" class="p-6 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-4 mt-auto">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        Halaman <span class="text-slate-700">{{ kelasData.current_page }}</span> dari <span class="text-slate-700">{{ kelasData.last_page }}</span>
                    </p>
                    <div class="flex gap-2">
                        <button @click="fetchKelas(kelasData.current_page - 1)" :disabled="kelasData.current_page === 1" class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 disabled:opacity-50 transition-all uppercase tracking-widest">Prev</button>
                        <button @click="fetchKelas(kelasData.current_page + 1)" :disabled="kelasData.current_page === kelasData.last_page" class="px-4 py-2 rounded-lg bg-emerald-50 border border-emerald-100 text-emerald-700 font-bold text-xs hover:bg-emerald-100 disabled:opacity-50 transition-all uppercase tracking-widest">Next</button>
                    </div>
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
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Hapus Kelas?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Anda yakin ingin menghapus kelas:<br>
                    <span class="font-bold text-slate-800">{{ deleteTarget.name }}</span>?
                </p>
                <p class="text-[10px] font-bold text-rose-500 mt-2 p-2 bg-rose-50 rounded-lg">AWAS: Menghapus kelas mungkin berdampak pada data siswa atau penilaian yang terkait dengan kelas ini!</p>
                
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isDeleteModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">Batal</button>
                    <button @click="executeDelete" :disabled="isSaving" class="flex-1 py-3 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        <span v-if="isSaving" class="animate-spin text-base">⏳</span>
                        <span v-else>Hapus</span>
                    </button>
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
const dependencies = ref({ kejuruans: [], kurikulums: [] })
const kelasData = ref({})
const isLoading = ref(true)
const isSaving = ref(false)

// Form State
const isEditing = ref(false)
const kelasForm = ref({
    id: null,
    kejuruan_id: '',
    kurikulum_id: '',
    tingkat: '',
    nama_kelas: ''
})

// Filter & Search State
const searchQuery = ref('')
const tingkatFilter = ref('')
let searchTimeout = null

// Delete State
const isDeleteModalOpen = ref(false)
const deleteTarget = ref({ id: null, name: '' })

// Toast State
const showToast = ref(false)
const toastMessage = ref('')

// Navigation
const navigateToSiswa = (id) => {
    navigateTo(`/admin/kelas/${id}/siswa`)
}

// === API CALLS ===

const fetchDependencies = async () => {
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch('http://localhost:8000/api/admin/kelas/dependencies', {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            dependencies.value = {
                kejuruans: response.kejuruans,
                kurikulums: response.kurikulums
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
        const response = await $fetch(`http://localhost:8000/api/admin/kelas?page=${page}`, {
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
    isSaving.value = true
    const token = useCookie('auth_token').value
    const method = isEditing.value ? 'PUT' : 'POST'
    const url = isEditing.value 
        ? `http://localhost:8000/api/admin/kelas/${kelasForm.value.id}`
        : `http://localhost:8000/api/admin/kelas`
    
    try {
        const response = await $fetch(url, {
            method: method,
            headers: { Authorization: `Bearer ${token}` },
            body: kelasForm.value
        })
        if (response.success) {
            displayToast(response.message)
            resetForm()
            fetchKelas(kelasData.value.current_page || 1)
            if (!isDesktop.value) activeTab.value = 'table'
        }
    } catch (error) {
        console.error('Save failed:', error)
        alert('Gagal menyimpan kelas. Periksa kembali form Anda.')
    } finally {
        isSaving.value = false
    }
}

const editKelas = (k) => {
    isEditing.value = true
    kelasForm.value = {
        id: k.id,
        kejuruan_id: k.kejuruan_id,
        kurikulum_id: k.kurikulum_id,
        tingkat: k.tingkat,
        nama_kelas: k.nama_kelas
    }
    activeTab.value = 'form'
    window.scrollTo({ top: 0, behavior: 'smooth' })
}

const resetForm = () => {
    isEditing.value = false
    kelasForm.value = {
        id: null,
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
    isSaving.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/admin/kelas/${deleteTarget.value.id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            isDeleteModalOpen.value = false
            displayToast(response.message)
            fetchKelas(kelasData.value.current_page || 1)
        }
    } catch (error) {
        console.error('Delete failed:', error)
        alert('Gagal menghapus data kelas.')
    } finally {
        isSaving.value = false
    }
}

const displayToast = (msg) => {
    toastMessage.value = msg
    showToast.value = true
    setTimeout(() => { showToast.value = false }, 3500)
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
