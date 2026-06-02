<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- MOBILE VIEW TABS -->
    <div class="lg:hidden mb-8 mt-2">
      <div class="grid grid-cols-3 gap-2">
        <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTab = tab.id"
          :class="activeTab === tab.id ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 ring-2 ring-emerald-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="rounded-2xl flex flex-col items-center justify-center py-4 px-1 transition-all active:scale-95">
          <span class="text-2xl mb-1.5 transition-transform" :class="activeTab === tab.id ? 'scale-110' : ''">{{ tab.icon }}</span>
          <span class="text-[9px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
        </button>
      </div>
    </div>

    <!-- MAIN GRID LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      
      <!-- ==============================================
           KIRI: PANEL KENDALI (Form & Import) - lg:col-span-4
           Muncul jika layar besar, ATAU jika di mobile memilih form/import
           ============================================== -->
      <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-6" v-show="isDesktop || (activeTab === 'form' || activeTab === 'import')">
        
        <!-- Desktop Tabs (Toggle Form vs Import) -->
        <div class="hidden lg:flex bg-white rounded-2xl shadow-sm border border-slate-200/60 p-1.5">
            <button @click="activeTab = 'form'" :class="activeTab === 'form' ? 'bg-emerald-50 text-emerald-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50'" class="flex-1 py-2.5 rounded-2xl font-bold text-xs uppercase tracking-widest transition-all">
                👤 Registrasi
            </button>
            <button @click="activeTab = 'import'" :class="activeTab === 'import' ? 'bg-emerald-50 text-emerald-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50'" class="flex-1 py-2.5 rounded-2xl font-bold text-xs uppercase tracking-widest transition-all">
                🚀 Import
            </button>
        </div>

        <!-- Panel Form Tambah -->
        <div v-show="activeTab === 'form'" class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden animate-slideUpFade">
            <div class="p-6 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-100 flex items-center gap-4">
                <span class="text-3xl">👤</span>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-white">Akun Baru</h3>
                    <p class="text-[10px] text-slate-400 font-semibold uppercase mt-0.5">Tambah Pengguna Manual</p>
                </div>
            </div>
            <div class="p-6 space-y-5">
                <form @submit.prevent="saveUser" class="space-y-5">
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Nama Lengkap</label>
                        <input type="text" v-model="form.name" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800" placeholder="Misal: John Doe, S.Pd.">
                    </div>
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Email Login</label>
                        <input type="email" v-model="form.email" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800" placeholder="guru@erapor.id">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Role Akses</label>
                            <select v-model="form.role" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-bold text-slate-700 cursor-pointer appearance-none">
                                <option value="guru">Guru</option>
                                <option value="siswa">Siswa</option>
                                <option value="admin">Admin</option>
                                <option value="kepsek">Kepsek</option>
                                <option value="kurikulum">Kurikulum</option>
                                <option value="bk">BK</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Password</label>
                            <input type="password" v-model="form.password" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800" placeholder="Min. 6 karakter">
                        </div>
                    </div>

                    <!-- Opsi Khusus Guru -->
                    <div v-if="form.role === 'guru'" class="p-4 bg-emerald-50/50 border border-emerald-100 rounded-2xl space-y-3 animate-fadeIn">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" v-model="form.is_pengampu_umum" class="rounded border-emerald-300 text-emerald-600 w-5 h-5 focus:ring-emerald-500">
                            <span class="text-xs font-black text-emerald-800 uppercase tracking-widest">Unit Umum (Normatif)</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" v-model="form.is_pengampu_kejuruan" class="rounded border-emerald-300 text-emerald-600 w-5 h-5 focus:ring-emerald-500">
                            <span class="text-xs font-black text-emerald-800 uppercase tracking-widest">Unit Kejuruan (Produktif)</span>
                        </label>
                    </div>

                    <button type="submit" :disabled="isSaving" class="w-full py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-2xl shadow-lg shadow-emerald-500/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                        <span v-if="isSaving" class="animate-spin">⏳</span>
                        <span v-else>➕</span> 
                        Tambah Pengguna
                    </button>
                </form>
            </div>
        </div>

        <!-- Panel Import -->
        <div v-show="activeTab === 'import'" class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden animate-slideUpFade">
            <div class="p-6 bg-gradient-to-r from-emerald-600 to-emerald-800 border-b border-emerald-500 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span class="text-3xl">🚀</span>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-white">Import Massal</h3>
                        <p class="text-[10px] text-emerald-100 font-semibold uppercase mt-0.5">Via File CSV / Excel</p>
                    </div>
                </div>
            </div>
            <div class="p-8 text-center">
                
                <p class="text-xs text-slate-500 font-medium mb-6 leading-relaxed">
                    Gunakan template resmi kami agar struktur kolom sesuai dengan database sistem. <br class="hidden lg:block"> Format file yang didukung: <span class="font-bold text-slate-700">.csv</span>
                </p>

                <!-- Tombol Download -->
                <button @click="downloadTemplate" type="button" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-bold text-xs uppercase tracking-widest transition-colors mb-8 shadow-inner border border-slate-200">
                    📥 Download Template CSV
                </button>

                <!-- Area Upload -->
                <form @submit.prevent="uploadImportFile" class="space-y-6 relative">
                    <div class="border-2 border-dashed border-emerald-300 bg-emerald-50/50 rounded-2xl p-8 transition-all hover:bg-emerald-50 group relative">
                        <input type="file" ref="fileInput" @change="handleFileChange" accept=".csv" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="pointer-events-none">
                            <span class="text-5xl block mb-3 group-hover:scale-110 transition-transform text-emerald-400">📄</span>
                            <p class="text-sm font-bold text-emerald-700">Pilih file CSV</p>
                            <p class="text-xs font-semibold text-emerald-600/70 mt-1" v-if="!selectedFile">atau drag & drop ke area ini</p>
                            <p class="text-xs font-black text-emerald-800 mt-2 bg-emerald-200/50 py-1 px-3 rounded-lg inline-block" v-else>
                                {{ selectedFile.name }} ({{ (selectedFile.size / 1024).toFixed(1) }} KB)
                            </p>
                        </div>
                    </div>

                    <button type="submit" :disabled="isSaving || !selectedFile" class="w-full py-4 bg-gradient-to-r from-emerald-500 to-emerald-700 text-white font-bold rounded-2xl shadow-lg shadow-emerald-500/30 hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:transform-none flex items-center justify-center gap-3">
                        <span v-if="isSaving" class="animate-spin text-xl">⏳</span>
                        <span v-else class="text-xl">🚀</span> 
                        <span class="uppercase tracking-widest text-xs">Mulai Import Data</span>
                    </button>
                </form>

            </div>
        </div>

      </div>

      <!-- ==============================================
           KANAN: DATABASE TABLE - lg:col-span-8
           Muncul jika layar besar, ATAU jika di mobile memilih table
           ============================================== -->
      <div class="lg:col-span-8" v-show="isDesktop || activeTab === 'table'">
         <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col min-h-[500px]">
            
            <!-- Table Header & Filters -->
            <div class="p-6 bg-slate-50/50 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-4 w-full sm:w-auto">
                    <div class="w-12 h-12 rounded-2xl bg-white shadow-sm border border-slate-200 flex items-center justify-center text-2xl hidden sm:flex">📋</div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-emerald-700">Database</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Total: {{ pagination.total }} Akun</p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <!-- Search Input -->
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">🔍</span>
                        <input type="text" v-model="searchQuery" @input="debouncedFetch" placeholder="Cari nama/email..." class="w-full sm:w-48 pl-9 pr-4 py-2.5 rounded-2xl border border-slate-200 bg-white focus:ring-2 focus:ring-emerald-500 text-xs font-bold transition-all shadow-sm">
                    </div>
                    <!-- Role Filter -->
                    <select v-model="roleFilter" @change="fetchUsers(1)" class="w-full sm:w-40 py-2.5 px-4 rounded-2xl border border-slate-200 bg-white focus:ring-2 focus:ring-emerald-500 text-xs font-black uppercase tracking-wider text-slate-600 cursor-pointer shadow-sm">
                        <option value="">Semua Akses</option>
                        <option value="admin">Admin</option>
                        <option value="kepsek">Kepsek</option>
                        <option value="kurikulum">Kurikulum</option>
                        <option value="guru">Guru</option>
                        <option value="bk">BK</option>
                        <option value="siswa">Siswa</option>
                    </select>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60">
                <div class="w-8 h-8 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</span>
            </div>

            <!-- Table Data -->
            <div v-else class="flex-grow overflow-x-auto custom-scrollbar relative">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="bg-slate-100 text-[9px] uppercase tracking-widest font-black text-slate-500">
                            <th class="p-4 border-b border-slate-200 w-12 text-center">No</th>
                            <th class="p-4 border-b border-slate-200">Pengguna</th>
                            <th class="p-4 border-b border-slate-200 w-32">Hak Akses</th>
                            <th class="p-4 border-b border-slate-200 w-24 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs font-medium text-slate-700 divide-y divide-slate-100">
                        <tr v-if="users.length === 0">
                            <td colspan="4" class="p-10 text-center text-slate-400 font-bold">Tidak ada data pengguna ditemukan.</td>
                        </tr>
                        <tr v-for="(u, index) in users" :key="u.id" class="hover:bg-slate-50/80 transition-colors">
                            <td class="p-4 text-center text-slate-400">{{ (pagination.currentPage - 1) * 15 + index + 1 }}</td>
                            <td class="p-4">
                                <div class="font-bold text-slate-800 text-sm">{{ u.name }}</div>
                                <div class="text-[10px] font-bold text-slate-400 mt-0.5">{{ u.email }}</div>
                            </td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border" :class="getRoleBadgeClass(u.role)">
                                    {{ u.role }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="openEditModal(u)" class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-100 hover:text-emerald-700 flex items-center justify-center transition-colors" title="Edit">✏️</button>
                                    <button @click="confirmDelete(u)" class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 hover:text-rose-700 flex items-center justify-center transition-colors" title="Hapus">🗑️</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between" v-if="pagination.lastPage > 1">
                <button @click="fetchUsers(pagination.currentPage - 1)" :disabled="pagination.currentPage === 1" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-100 disabled:opacity-50 transition-all">Previous</button>
                <span class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Halaman {{ pagination.currentPage }} dari {{ pagination.lastPage }}</span>
                <button @click="fetchUsers(pagination.currentPage + 1)" :disabled="pagination.currentPage === pagination.lastPage" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-100 disabled:opacity-50 transition-all">Next</button>
            </div>
         </div>
      </div>
    </div>

    <!-- ==============================================
         MODAL EDIT (Overlay)
         ============================================== -->
    <div v-if="isEditModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade">
            
            <div class="p-6 bg-gradient-to-r from-slate-900 to-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-white uppercase tracking-widest">Update Akun</h3>
                    <p class="text-[10px] font-bold text-emerald-400 mt-0.5 uppercase tracking-wider">{{ editForm.name }}</p>
                </div>
                <button @click="isEditModalOpen = false" class="w-8 h-8 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition-colors">✕</button>
            </div>

            <div class="p-6">
                <form @submit.prevent="updateUser" class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Nama Lengkap</label>
                        <input type="text" v-model="editForm.name" required class="w-full px-4 py-2.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800">
                    </div>
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Email Login</label>
                        <input type="email" v-model="editForm.email" required class="w-full px-4 py-2.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Role Akses</label>
                            <select v-model="editForm.role" class="w-full px-4 py-2.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-bold text-slate-700">
                                <option value="guru">Guru</option>
                                <option value="siswa">Siswa</option>
                                <option value="admin">Admin</option>
                                <option value="kepsek">Kepsek</option>
                                <option value="kurikulum">Kurikulum</option>
                                <option value="bk">BK</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Password Baru</label>
                            <input type="password" v-model="editForm.password" class="w-full px-4 py-2.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800" placeholder="Kosongkan jika tdk diubah">
                        </div>
                    </div>

                    <!-- Opsi Khusus Guru (Edit) -->
                    <div v-if="editForm.role === 'guru'" class="p-3 bg-emerald-50/50 border border-emerald-100 rounded-2xl space-y-2">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" v-model="editForm.is_pengampu_umum" class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-[11px] font-black text-emerald-800 uppercase tracking-widest">Unit Umum (Normatif)</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" v-model="editForm.is_pengampu_kejuruan" class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-[11px] font-black text-emerald-800 uppercase tracking-widest">Unit Kejuruan (Produktif)</span>
                        </label>
                    </div>

                    <button type="submit" :disabled="isSaving" class="w-full mt-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-2xl shadow-lg hover:-translate-y-0.5 transition-all">
                        {{ isSaving ? '⏳ Memproses...' : '💾 Simpan Perubahan' }}
                    </button>
                </form>
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
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Hapus Pengguna?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Anda yakin ingin menghapus akun <span class="font-bold text-rose-600">{{ userToDelete?.name }}</span> secara permanen? Data yang telah dihapus tidak dapat dikembalikan.
                </p>
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isDeleteModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">
                        Batal
                    </button>
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
import { ref, onMounted, computed } from 'vue'

definePageMeta({
  layout: 'admin',
  middleware: 'admin',
  title: 'Manajemen Pengguna'
})

// Responsiveness detector (Simulated for Vue SSR safety by relying on CSS hiding mostly, but this ref is helpful for conditional renders if needed)
const windowWidth = ref(1024) 
const isDesktop = computed(() => windowWidth.value >= 1024)

// Tabs for Mobile
const activeTab = ref('table')
const mobileTabs = [
  { id: 'form', title: 'Tambah', icon: '👤' },
  { id: 'import', title: 'Import', icon: '🚀' },
  { id: 'table', title: 'Database', icon: '📋' }
]

// State management
const users = ref([])
const isLoading = ref(true)
const isSaving = ref(false)
const showToast = ref(false)
const toastMessage = ref('')

// Import state
const fileInput = ref(null)
const selectedFile = ref(null)

const downloadTemplate = async () => {
    const token = useCookie('auth_token').value
    try {
        const blob = await $fetch('http://localhost:8000/api/admin/users/template', {
            headers: { Authorization: `Bearer ${token}` },
            responseType: 'blob'
        })
        
        const url = window.URL.createObjectURL(blob)
        const a = document.createElement('a')
        a.href = url
        a.download = 'template_import_user.csv'
        document.body.appendChild(a)
        a.click()
        window.URL.revokeObjectURL(url)
        a.remove()
    } catch (error) {
        console.error('Download failed:', error)
        alert('Gagal mengunduh template CSV.')
    }
}

const handleFileChange = (e) => {
    const file = e.target.files[0]
    if (file && (file.type === 'text/csv' || file.name.endsWith('.csv') || file.type === 'application/vnd.ms-excel')) {
        selectedFile.value = file
    } else if (file) {
        alert('Gagal! Pastikan Anda menyimpan Excel tersebut dengan format Save As -> CSV (Comma delimited).')
        e.target.value = ''
        selectedFile.value = null
    }
}

const uploadImportFile = async () => {
    if (!selectedFile.value) return
    isSaving.value = true
    const token = useCookie('auth_token').value

    const formData = new FormData()
    formData.append('file', selectedFile.value)

    try {
        const response = await $fetch('http://localhost:8000/api/admin/users/import', {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` },
            body: formData
        })
        
        if (response.success) {
            displayToast(response.message)
            selectedFile.value = null
            if (fileInput.value) fileInput.value.value = ''
            fetchUsers(1)
            if (!isDesktop.value) activeTab.value = 'table'
        }
    } catch (error) {
        console.error('Import failed:', error)
        alert(error.data?.message || 'Terjadi kesalahan saat mengimpor file. Cek format kolom Anda.')
    } finally {
        isSaving.value = false
    }
}

const searchQuery = ref('')
const roleFilter = ref('')
const pagination = ref({
    currentPage: 1,
    lastPage: 1,
    total: 0
})

const form = ref({
    name: '',
    email: '',
    role: 'guru',
    password: '',
    is_pengampu_umum: true,
    is_pengampu_kejuruan: false
})

const isEditModalOpen = ref(false)
const isDeleteModalOpen = ref(false)
const userToDelete = ref(null)

const editForm = ref({
    id: null,
    name: '',
    email: '',
    role: '',
    password: '',
    is_pengampu_umum: false,
    is_pengampu_kejuruan: false
})

let debounceTimer = null
const debouncedFetch = () => {
    clearTimeout(debounceTimer)
    debounceTimer = setTimeout(() => {
        fetchUsers(1)
    }, 500)
}

// Fetch Users API
const fetchUsers = async (page = 1) => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        const queryParams = new URLSearchParams({ page })
        if (roleFilter.value) queryParams.append('role', roleFilter.value)
        if (searchQuery.value) queryParams.append('search', searchQuery.value)

        const response = await $fetch(`http://localhost:8000/api/admin/users?${queryParams.toString()}`, {
            headers: { Authorization: `Bearer ${token}` }
        })

        if (response.success) {
            users.value = response.data.data
            pagination.value = {
                currentPage: response.data.current_page,
                lastPage: response.data.last_page,
                total: response.data.total
            }
        }
    } catch (error) {
        console.error('Failed to fetch users:', error)
    } finally {
        isLoading.value = false
    }
}

// Create User API
const saveUser = async () => {
    isSaving.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch('http://localhost:8000/api/admin/users', {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` },
            body: form.value
        })
        
        if (response.success) {
            displayToast('Pengguna berhasil ditambahkan!')
            // Reset Form
            form.value = { name: '', email: '', role: 'guru', password: '', is_pengampu_umum: true, is_pengampu_kejuruan: false }
            // Fetch updated list
            fetchUsers(1)
            // Redirect back to table view on mobile
            if (!isDesktop.value) activeTab.value = 'table'
        }
    } catch (error) {
        console.error('Failed to save user:', error)
        alert('Gagal menambahkan pengguna. Email mungkin sudah terdaftar.')
    } finally {
        isSaving.value = false
    }
}

// Open Edit Modal
const openEditModal = (user) => {
    editForm.value = {
        id: user.id,
        name: user.name,
        email: user.email,
        role: user.role,
        password: '',
        is_pengampu_umum: Boolean(user.is_pengampu_umum),
        is_pengampu_kejuruan: Boolean(user.is_pengampu_kejuruan)
    }
    isEditModalOpen.value = true
}

// Update User API
const updateUser = async () => {
    isSaving.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/admin/users/${editForm.value.id}`, {
            method: 'PUT',
            headers: { Authorization: `Bearer ${token}` },
            body: editForm.value
        })
        
        if (response.success) {
            isEditModalOpen.value = false
            displayToast('Data pengguna berhasil diperbarui!')
            fetchUsers(pagination.value.currentPage)
        }
    } catch (error) {
        console.error('Failed to update user:', error)
        alert('Gagal memperbarui pengguna.')
    } finally {
        isSaving.value = false
    }
}

// Confirm Delete
const confirmDelete = (user) => {
    userToDelete.value = user
    isDeleteModalOpen.value = true
}

// Execute Delete API
const executeDelete = async () => {
    if (!userToDelete.value) return
    isSaving.value = true
    
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/admin/users/${userToDelete.value.id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        
        if (response.success) {
            isDeleteModalOpen.value = false
            displayToast('Pengguna telah dihapus secara permanen.')
            fetchUsers(pagination.value.currentPage)
        }
    } catch (error) {
        console.error('Failed to delete user:', error)
        alert('Gagal menghapus pengguna (Mungkin Anda menghapus akun sendiri).')
    } finally {
        isSaving.value = false
    }
}

const displayToast = (msg) => {
    toastMessage.value = msg
    showToast.value = true
    setTimeout(() => { showToast.value = false }, 3500)
}

const getRoleBadgeClass = (role) => {
    const badges = {
        admin: 'bg-rose-50 text-rose-600 border-rose-200',
        kepsek: 'bg-purple-50 text-purple-600 border-purple-200',
        kurikulum: 'bg-blue-50 text-blue-600 border-blue-200',
        guru: 'bg-emerald-50 text-emerald-600 border-emerald-200',
        bk: 'bg-amber-50 text-amber-600 border-amber-200',
        siswa: 'bg-slate-100 text-slate-500 border-slate-200'
    }
    return badges[role] || badges['siswa']
}

onMounted(() => {
    windowWidth.value = window.innerWidth
    window.addEventListener('resize', () => { windowWidth.value = window.innerWidth })
    
    // Set default tab logic based on screen
    if (isDesktop.value) {
        activeTab.value = 'form' // Desktop left panel default
    } else {
        activeTab.value = 'table' // Mobile default view
    }

    fetchUsers()
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-fadeIn {
  animation: fadeIn 0.3s ease-out forwards;
}

@keyframes slideUpFade {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-slideUpFade {
  animation: slideUpFade 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

@keyframes slideUp {
  from { opacity: 0; transform: translate(-50%, 50px); }
  to { opacity: 1; transform: translate(-50%, 0); }
}
.animate-slideUp {
  animation: slideUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

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
</style>
