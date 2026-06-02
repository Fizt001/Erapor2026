<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- HEADER & BACK BUTTON -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <NuxtLink to="/admin/kelas" class="inline-flex items-center gap-3 px-6 py-3 bg-slate-800 text-white rounded-2xl hover:bg-black transition-all shadow-lg active:scale-95 group w-fit">
            <span class="group-hover:-translate-x-1 transition-transform">⬅️</span>
            <span class="text-[10px] font-black uppercase tracking-widest">Kembali ke Master Kelas</span>
        </NuxtLink>
        
        <div class="text-left md:text-right">
            <h2 v-if="isLoading" class="w-48 h-6 bg-slate-200 animate-pulse rounded ml-auto"></h2>
            <h2 v-else class="text-sm font-black uppercase tracking-[0.3em] text-emerald-700 drop-shadow-sm">
                {{ kelas?.nama_kelas || 'Memuat...' }}
            </h2>
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">Penyusunan Anggota Rombel</p>
        </div>
    </div>

    <!-- MOBILE VIEW TABS -->
    <div class="xl:hidden mb-8 mt-2">
      <div class="grid grid-cols-2 gap-3">
        <button type="button" @click="activeTab = 'form'" :class="activeTab === 'form' ? 'bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-md shadow-indigo-500/20 ring-2 ring-indigo-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'" class="rounded-2xl flex flex-col items-center justify-center py-5 transition-all active:scale-95">
          <span class="text-3xl mb-2 transition-transform" :class="activeTab === 'form' ? 'scale-110' : ''">📥</span>
          <span class="text-[10px] font-black uppercase tracking-wider">Tarik User</span>
        </button>
        <button type="button" @click="activeTab = 'table'" :class="activeTab === 'table' ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 ring-2 ring-emerald-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'" class="rounded-2xl flex flex-col items-center justify-center py-5 transition-all active:scale-95">
          <span class="text-3xl mb-2 transition-transform" :class="activeTab === 'table' ? 'scale-110' : ''">📋</span>
          <span class="text-[10px] font-black uppercase tracking-wider">Daftar Anggota</span>
        </button>
      </div>
    </div>

    <!-- MAIN GRID LAYOUT -->
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-8 items-start">
      
      <!-- ==============================================
           KIRI: PANEL TARIK USER - xl:col-span-2 (40%)
           ============================================== -->
      <div class="xl:col-span-2 space-y-6 xl:sticky xl:top-6" v-show="isDesktop || activeTab === 'form'">
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden animate-slideUpFade">
            <div class="p-6 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-100 flex items-center gap-4">
                <span class="text-3xl">📥</span>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-white">Tarik User</h3>
                    <p class="text-[10px] text-indigo-400 font-semibold uppercase mt-0.5">Assign ke Kelas Ini</p>
                </div>
            </div>
            
            <div class="p-6 md:p-8">
                <div class="mb-4">
                    <input type="text" v-model="searchKandidat" placeholder="Cari nama siswa..." class="w-full px-4 py-2.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-xs font-bold text-slate-800 outline-none">
                </div>
                
                <div class="space-y-2 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                    <div v-if="filteredKandidat.length === 0" class="text-center py-4 text-xs font-bold text-rose-500">
                        Tidak ada kandidat ditemukan.
                    </div>
                    <div v-for="u in filteredKandidat" :key="u.id" class="flex items-center gap-2 p-2.5 rounded-2xl border border-slate-100 bg-white hover:bg-slate-50 transition-colors" :class="{'ring-2 ring-indigo-500 ring-offset-1': selectedUsers.includes(u.id)}">
                        <input type="checkbox" :value="u.id" v-model="selectedUsers" class="w-4 h-4 text-indigo-600 rounded border-slate-300 cursor-pointer focus:ring-indigo-500 focus:ring-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-black text-slate-700 truncate">{{ u.name }}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5 truncate">{{ u.email || u.username || '-' }}</p>
                        </div>
                        <input type="text" v-model="bulkNis[u.id]" @input="autoCheck(u.id)" placeholder="NIS..." class="w-24 px-2 py-2 rounded-xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-[11px] font-black text-slate-800 text-center outline-none">
                    </div>
                </div>

                <div class="pt-4 mt-4 border-t border-slate-100">
                    <button @click="assignBulkUser" :disabled="isSaving || selectedUsers.length === 0" class="w-full py-3.5 bg-gradient-to-r from-indigo-500 to-blue-600 text-white font-bold rounded-2xl shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 uppercase tracking-widest text-xs disabled:opacity-50 disabled:cursor-not-allowed">
                        <span v-if="isSaving" class="animate-spin text-lg">⏳</span>
                        <span v-else class="text-lg">➕</span> Tambahkan Terpilih ({{ selectedUsers.length }})
                    </button>
                </div>
            </div>
        </div>

        <div class="p-5 bg-emerald-50 border border-emerald-100 rounded-2xl text-center shadow-sm">
            <p class="text-[9px] font-black text-emerald-700 uppercase leading-relaxed tracking-widest">
                💡 Siswa yang sudah memiliki kelas tidak akan muncul dalam daftar pilihan kandidat di atas.
            </p>
        </div>

      </div>

      <!-- ==============================================
           KANAN: DATABASE SISWA DI KELAS - xl:col-span-3 (60%)
           ============================================== -->
      <div class="xl:col-span-3" v-show="isDesktop || activeTab === 'table'">
         <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col min-h-[500px]">
            
            <!-- Table Header & Filters -->
            <div class="p-6 bg-white border-b border-slate-100">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div class="flex items-center gap-4">
                        <span class="text-3xl">🧑‍🎓</span>
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-widest text-emerald-700">Daftar Anggota Kelas</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Total Siswa Aktif</p>
                        </div>
                    </div>
                    <span class="px-5 py-2 bg-white border border-slate-200 rounded-full text-[10px] font-black text-emerald-600 shadow-sm uppercase tracking-widest">
                        {{ students.length }} SISWA
                    </span>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60">
                <div class="w-8 h-8 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</span>
            </div>

            <!-- Table Content -->
            <div v-else class="flex-grow flex flex-col">
                <div v-if="students.length === 0" class="text-center py-20 m-auto">
                    <div class="text-6xl opacity-30 mb-4">🪹</div>
                    <p class="text-sm font-bold text-slate-500">Belum ada siswa di kelas ini.</p>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-2 font-bold">Gunakan form di sebelah kiri untuk menarik user.</p>
                </div>

                <!-- Desktop Table -->
                <div v-else class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-slate-200">
                                <th class="py-3 px-4 pl-6 w-16 text-center">#</th>
                                <th class="py-3 px-4">Nama Siswa / Akun</th>
                                <th class="py-3 px-4 text-center">NIS</th>
                                <th class="py-3 px-4 text-right pr-6">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <tr v-for="(s, index) in paginatedStudents" :key="s.id" class="border-b border-slate-100 hover:bg-slate-50/80 transition-colors group">
                                <td class="py-2.5 px-4 pl-6 text-center text-xs font-bold text-slate-300">
                                    {{ (currentPage - 1) * 10 + index + 1 }}
                                </td>
                                <td class="py-2.5 px-4">
                                    <p class="font-black text-slate-700 text-xs">{{ s.user?.name || 'Unknown' }}</p>
                                </td>
                                <td class="py-2.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-emerald-50 text-emerald-700 text-[10px] font-black tracking-widest border border-emerald-100">
                                        {{ s.nis }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-4 pr-6 text-right">
                                    <div class="flex items-center justify-end gap-1.5 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                        <button @click="openEditNisModal(s)" class="w-7 h-7 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-indigo-500 hover:border-indigo-200 hover:bg-indigo-50 flex items-center justify-center transition-all shadow-sm" title="Edit NIS">✏️</button>
                                        <button @click="confirmDelete(s.id, s.user?.name)" class="w-7 h-7 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 flex items-center justify-center transition-all shadow-sm" title="Hapus">🗑️</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div v-if="students.length > 0" class="md:hidden p-4 space-y-4 bg-slate-50">
                    <div v-for="s in paginatedStudents" :key="'mob-'+s.id" class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 relative overflow-hidden">
                        <div class="absolute right-4 top-4 flex items-center gap-2 z-10">
                            <button @click="openEditNisModal(s)" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-indigo-500 hover:bg-indigo-50 flex items-center justify-center shadow-sm" title="Edit NIS">✏️</button>
                            <button @click="confirmDelete(s.id, s.user?.name)" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:bg-rose-50 flex items-center justify-center shadow-sm" title="Hapus">🗑️</button>
                        </div>
                        <div class="mb-4 pr-16">
                            <h4 class="font-black text-slate-800 text-lg leading-tight">{{ s.user?.name }}</h4>
                        </div>
                        <div class="mb-1 flex items-center gap-2">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">NIS:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded bg-emerald-50 text-emerald-700 text-xs font-black tracking-widest border border-emerald-100">
                                {{ s.nis }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="totalPages > 1" class="p-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-4 mt-auto">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">
                        Halaman {{ currentPage }} dari {{ totalPages }}
                    </p>
                    <div class="flex items-center gap-2">
                        <button @click="prevPage" :disabled="currentPage === 1" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-emerald-600 hover:border-emerald-200 hover:bg-emerald-50 disabled:opacity-50 transition-all shadow-sm">
                            &laquo; Prev
                        </button>
                        <button @click="nextPage" :disabled="currentPage === totalPages" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-emerald-600 hover:border-emerald-200 hover:bg-emerald-50 disabled:opacity-50 transition-all shadow-sm">
                            Next &raquo;
                        </button>
                    </div>
                </div>

            </div>
         </div>
      </div>
    </div>

    <!-- ==============================================
         MODAL EDIT NIS
         ============================================== -->
    <div v-if="isEditModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade">
            <div class="p-8">
                <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-4">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Koreksi NIS</h3>
                    <button @click="isEditModalOpen = false" class="text-slate-400 hover:text-rose-500 bg-slate-50 w-8 h-8 rounded-full flex items-center justify-center">✕</button>
                </div>
                <form @submit.prevent="executeEditNis" class="space-y-6">
                    <div>
                        <p class="text-center text-xs font-black text-indigo-600 uppercase mb-4">{{ editForm.name }}</p>
                        <input type="text" v-model="editForm.nis" required class="w-full py-4 text-center text-lg font-black text-slate-800 rounded-2xl border border-slate-200 bg-slate-50 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 tracking-[0.3em] transition-all">
                    </div>
                    <button type="submit" :disabled="isSaving" class="w-full py-4 text-[10px] font-black text-white bg-indigo-600 hover:bg-indigo-700 rounded-2xl shadow-lg uppercase tracking-widest flex items-center justify-center gap-2">
                        <span v-if="isSaving" class="animate-spin text-base">⏳</span>
                        <span v-else>💾 Simpan Perubahan</span>
                    </button>
                </form>
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
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Keluarkan Siswa?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Anda yakin ingin mengeluarkan:<br>
                    <span class="font-bold text-slate-800">{{ deleteTarget.name }}</span><br>
                    dari kelas ini?
                </p>
                <div class="mt-4 p-3 bg-rose-50 border border-rose-100 rounded-2xl">
                    <p class="text-[9px] font-black text-rose-600 uppercase tracking-widest leading-relaxed">
                        PERINGATAN:<br>Siswa akan dikembalikan ke daftar kandidat kosong. Seluruh history biodata di tabel siswa akan ikut terhapus!
                    </p>
                </div>
                
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isDeleteModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">Batal</button>
                    <button @click="executeDelete" :disabled="isSaving" class="flex-1 py-3 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        <span v-if="isSaving" class="animate-spin text-base">⏳</span>
                        <span v-else>Keluarkan</span>
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
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'

definePageMeta({
  layout: 'admin',
  middleware: 'admin',
  title: 'Kelola Siswa'
})

const route = useRoute()
const kelasId = route.params.id

const windowWidth = ref(1024) 
const isDesktop = computed(() => windowWidth.value >= 1280) // xl breakpoint

// Mobile Tab State
const activeTab = ref('table')

// Main State
const kelas = ref({})
const usersAvailable = ref([])
const students = ref([])
const isLoading = ref(true)
const isSaving = ref(false)

// Pagination State
const currentPage = ref(1)
const itemsPerPage = 10

const totalPages = computed(() => {
    return Math.ceil(students.value.length / itemsPerPage)
})

const paginatedStudents = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage
    return students.value.slice(start, start + itemsPerPage)
})

const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }
const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }

// Bulk Form State
const searchKandidat = ref('')
const selectedUsers = ref([])
const bulkNis = ref({})

const filteredKandidat = computed(() => {
    if (!searchKandidat.value) return usersAvailable.value
    const s = searchKandidat.value.toLowerCase()
    return usersAvailable.value.filter(u => u.name.toLowerCase().includes(s) || (u.email && u.email.toLowerCase().includes(s)) || (u.username && u.username.toLowerCase().includes(s)))
})

// Edit NIS State
const isEditModalOpen = ref(false)
const editForm = ref({
    id: null,
    name: '',
    nis: ''
})

// Delete State
const isDeleteModalOpen = ref(false)
const deleteTarget = ref({ id: null, name: '' })

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

// === API CALLS ===

const fetchData = async () => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/admin/kelas/${kelasId}/siswa`, {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            kelas.value = response.kelas
            usersAvailable.value = response.users_available
            students.value = response.students
            
            // Auto reset pagination if current page exceeds total pages after refetch
            if (currentPage.value > Math.ceil(students.value.length / itemsPerPage)) {
                currentPage.value = Math.max(1, Math.ceil(students.value.length / itemsPerPage))
            }
        }
    } catch (error) {
        console.error('Failed to fetch data:', error)
        showError('Gagal memuat data rombel.')
    } finally {
        isLoading.value = false
    }
}

const autoCheck = (id) => {
    if (bulkNis.value[id] && !selectedUsers.value.includes(id)) {
        selectedUsers.value.push(id)
    } else if (!bulkNis.value[id] && selectedUsers.value.includes(id)) {
        selectedUsers.value = selectedUsers.value.filter(uid => uid !== id)
    }
}

const assignBulkUser = async () => {
    if(selectedUsers.value.length === 0) return
    
    // Validasi NIS kosong
    const payloadSiswa = []
    for (const uid of selectedUsers.value) {
        if (!bulkNis.value[uid]) {
            showError('Ada siswa yang dicentang namun NIS-nya masih kosong!')
            return
        }
        payloadSiswa.push({
            user_id: uid,
            nis: bulkNis.value[uid]
        })
    }

    isSaving.value = true
    const token = useCookie('auth_token').value
    
    try {
        const response = await $fetch(`http://localhost:8000/api/admin/kelas/${kelasId}/siswa`, {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` },
            body: { siswa: payloadSiswa }
        })
        if (response.success) {
            displayToast(response.message)
            selectedUsers.value = []
            bulkNis.value = {}
            searchKandidat.value = ''
            await fetchData()
            if (!isDesktop.value) activeTab.value = 'table'
        }
    } catch (error) {
        console.error('Save failed:', error)
        let msg = 'Gagal mendaftarkan siswa secara massal.'
        if (error.response && error.response._data && error.response._data.message) {
            msg = error.response._data.message
        }
        showError(msg)
    } finally {
        isSaving.value = false
    }
}

const openEditNisModal = (s) => {
    editForm.value = {
        id: s.id,
        name: s.user?.name || 'Unknown',
        nis: s.nis
    }
    isEditModalOpen.value = true
}

const executeEditNis = async () => {
    isSaving.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/admin/siswa/${editForm.value.id}`, {
            method: 'PUT',
            headers: { Authorization: `Bearer ${token}` },
            body: { nis: editForm.value.nis }
        })
        if (response.success) {
            isEditModalOpen.value = false
            displayToast(response.message)
            await fetchData()
        }
    } catch (error) {
        console.error('Update NIS failed:', error)
        showError('Gagal memperbarui NIS. Pastikan NIS tidak bentrok dengan siswa lain.')
    } finally {
        isSaving.value = false
    }
}

const confirmDelete = (id, name) => {
    deleteTarget.value = { id, name: name || 'Siswa' }
    isDeleteModalOpen.value = true
}

const executeDelete = async () => {
    if (!deleteTarget.value.id) return
    isSaving.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/admin/siswa/${deleteTarget.value.id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            isDeleteModalOpen.value = false
            displayToast(response.message)
            await fetchData()
        }
    } catch (error) {
        console.error('Delete failed:', error)
        showError('Gagal mengeluarkan siswa.')
    } finally {
        isSaving.value = false
    }
}

const displayToast = (msg) => {
    toastMessage.value = msg
    showToast.value = true
    setTimeout(() => { showToast.value = false }, 3500)
}

onMounted(() => {
    windowWidth.value = window.innerWidth
    window.addEventListener('resize', () => { windowWidth.value = window.innerWidth })
    
    if (isDesktop.value) {
        activeTab.value = 'form' 
    } else {
        activeTab.value = 'table' 
    }

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

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
