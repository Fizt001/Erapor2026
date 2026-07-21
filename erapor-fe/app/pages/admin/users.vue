<template>
  <div class="absolute inset-0 flex flex-col min-h-0 bg-slate-50 z-10">
    
<!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-2 grid grid-cols-3 gap-2 z-20">
        <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTab = tab.id"
          :class="activeTab === tab.id ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 ring-2 ring-emerald-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="rounded-xl flex flex-col items-center justify-center py-2 px-1 transition-all active:scale-95">
          <span class="text-lg mb-0.5 transition-transform" :class="activeTab === tab.id ? 'scale-110' : ''">{{ tab.icon }}</span>
          <span class="text-[9px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
        </button>
      </div>

      <!-- Panel Dock Kiri -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all', (activeTab === 'form' || activeTab === 'import') || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[60px]' : '']">
        
        <!-- Desktop Tabs (Toggle Form vs Import) -->
        <div class="hidden xl:flex bg-slate-50/50 border-b border-slate-200 p-2 shrink-0">
            <button @click="activeTab = 'form'" :class="activeTab === 'form' ? 'bg-white text-emerald-700 shadow-sm border border-slate-200' : 'text-slate-500 hover:bg-slate-100 border border-transparent'" class="flex-1 py-2.5 rounded-xl font-bold text-[10px] uppercase tracking-widest transition-all">
                👤 Registrasi
            </button>
            <button @click="activeTab = 'import'" :class="activeTab === 'import' ? 'bg-white text-emerald-700 shadow-sm border border-slate-200' : 'text-slate-500 hover:bg-slate-100 border border-transparent'" class="flex-1 py-2.5 rounded-xl font-bold text-[10px] uppercase tracking-widest transition-all ml-2">
                🚀 Import
            </button>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <!-- Panel Form Tambah -->
            <div v-show="activeTab === 'form'" class="animate-fadeIn">
                <div class="p-6 shrink-0">
                  <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl p-5 border border-emerald-500 shadow-sm relative overflow-hidden flex items-center gap-4">
                    <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0 relative z-10">👤</div>
                    <div class="relative z-10">
                        <h3 class="text-sm font-black uppercase tracking-widest text-white">Akun Baru</h3>
                        <p class="text-[10px] text-emerald-100 font-semibold uppercase mt-0.5">Tambah Pengguna Manual</p>
                    </div>
                    <div class="absolute right-0 bottom-0 opacity-20 text-white">
                      <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg>
                    </div>
                  </div>
                </div>
                <div class="px-6 pb-6">
                    <form @submit.prevent="saveUser" class="space-y-4">
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

                        <div class="pt-4 border-t border-slate-100">
                            <button type="submit" :disabled="isSaving" class="w-full py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-2xl shadow-lg shadow-emerald-500/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                                <span v-if="isSaving" class="animate-spin">⏳</span>
                                <span v-else>➕</span> 
                                Tambah Pengguna
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Panel Import -->
            <div v-show="activeTab === 'import'" class="animate-fadeIn">
                <div class="p-6 shrink-0">
                  <div class="bg-gradient-to-r from-indigo-600 to-blue-700 rounded-2xl p-5 border border-indigo-500 shadow-sm relative overflow-hidden flex items-center gap-4">
                    <span class="text-2xl w-10 h-10 flex items-center justify-center relative z-10">🚀</span>
                    <div class="relative z-10">
                        <h3 class="text-sm font-black uppercase tracking-widest text-white">Import Massal</h3>
                        <p class="text-[10px] text-indigo-100 font-semibold uppercase mt-0.5">Via File CSV</p>
                    </div>
                    <div class="absolute right-0 bottom-0 opacity-20 text-white">
                      <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"></path></svg>
                    </div>
                  </div>
                </div>
                <div class="px-6 pb-6 text-center space-y-5">
                    
                    <p class="text-[11px] text-slate-500 font-medium leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-200">
                        Gunakan template resmi kami agar struktur kolom sesuai dengan database sistem. Format: <span class="font-bold text-slate-700">.csv</span>
                    </p>

                    <!-- Tombol Download -->
                    <button @click="downloadTemplate" type="button" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl font-bold text-[10px] uppercase tracking-widest transition-colors shadow-inner border border-slate-200">
                        📥 Download Template CSV
                    </button>

                    <!-- Area Upload -->
                    <form @submit.prevent="uploadImportFile" class="space-y-6 relative pt-4 border-t border-slate-100">
                        <div class="border-2 border-dashed border-emerald-300 bg-emerald-50/50 rounded-2xl p-6 transition-all hover:bg-emerald-50 group relative cursor-pointer min-h-[140px] flex flex-col items-center justify-center">
                            <input type="file" ref="fileInput" @change="handleFileChange" accept=".csv" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="pointer-events-none text-center">
                                <span class="text-4xl block mb-2 group-hover:scale-110 transition-transform text-emerald-400">📄</span>
                                <p class="text-xs font-bold text-emerald-700">Pilih file CSV</p>
                                <p class="text-[10px] font-semibold text-emerald-600/70 mt-1" v-if="!selectedFile">atau drag & drop ke area ini</p>
                                <div v-else class="mt-2 bg-emerald-200/50 py-1.5 px-3 rounded-lg flex items-center justify-center gap-2">
                                    <span class="text-[10px] font-black text-emerald-800 truncate max-w-[150px]">{{ selectedFile.name }}</span>
                                    <span class="text-[9px] text-emerald-600 font-bold shrink-0">({{ (selectedFile.size / 1024).toFixed(1) }} KB)</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" :disabled="isSaving || !selectedFile" class="w-full py-4 bg-gradient-to-r from-emerald-500 to-emerald-700 text-white font-bold rounded-2xl shadow-lg shadow-emerald-500/30 hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:transform-none flex items-center justify-center gap-3">
                            <span v-if="isSaving" class="animate-spin text-lg">⏳</span>
                            <span v-else class="text-lg">🚀</span> 
                            <span class="uppercase tracking-widest text-[11px]">Mulai Import</span>
                        </button>
                    </form>

                </div>
            </div>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative', activeTab === 'table' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[60px]' : '']">
        <div class="p-2 sm:p-6 lg:p-8 max-w-5xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-xl sm:rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            <!-- Table Header & Filters -->
            <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white shrink-0 z-10">
            <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto">
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-white shadow-sm border border-slate-200 flex items-center justify-center text-sm sm:text-xl hidden sm:flex">📋</div>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-sm font-black uppercase tracking-widest text-emerald-700">Database</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Total: {{ pagination.total }} Akun</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <select v-model="roleFilter" @change="fetchUsers(1)" class="w-full sm:w-40 py-2 px-4 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-emerald-500 text-[11px] font-black uppercase tracking-wider text-slate-600 cursor-pointer shadow-sm">
                    <option value="">Semua Akses</option>
                    <option value="admin">Admin</option>
                    <option value="kepsek">Kepsek</option>
                    <option value="kurikulum">Kurikulum</option>
                    <option value="guru">Guru</option>
                    <option value="bk">BK</option>
                    <option value="siswa">Siswa</option>
                </select>
                <!-- Search Input -->
                <div class="relative w-full sm:w-auto">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">🔍</span>
                    <input type="text" v-model="searchQuery" @input="debouncedFetch" placeholder="Cari nama/email..." class="w-full sm:w-48 pl-9 pr-4 py-2 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-emerald-500 text-xs font-bold transition-all shadow-sm">
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60">
            <div class="w-8 h-8 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</span>
        </div>

        <!-- Table Data -->
        <div v-else class="flex-1 overflow-y-auto overflow-x-auto custom-scrollbar relative bg-white">
            <table class="w-full text-left border-collapse min-w-[600px] whitespace-nowrap">
                <thead class="sticky top-0 z-10 bg-slate-50 border-b border-slate-200 shadow-sm">
                    <tr class="text-[9px] uppercase tracking-widest font-black text-slate-500">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Pengguna</th>
                        <th class="py-3 px-4 w-32">Hak Akses</th>
                        <th class="py-3 px-4 w-24 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-xs font-medium text-slate-700 divide-y divide-slate-100">
                    <tr v-if="users.length === 0">
                        <td colspan="4" class="p-16 text-center text-slate-400 font-bold bg-white">
                            <span class="text-4xl block mb-2 opacity-30">📋</span>
                            Tidak ada data pengguna ditemukan.
                        </td>
                    </tr>
                    <tr v-for="(u, index) in users" :key="u.id" class="hover:bg-slate-50/80 transition-colors bg-white group">
                        <td class="p-4 text-center text-slate-400 font-bold">{{ (pagination.currentPage - 1) * 15 + index + 1 }}</td>
                        <td class="p-4">
                            <div class="font-black text-slate-800 text-[13px]">{{ u.name }}</div>
                            <div class="text-[10px] font-bold text-slate-400 mt-0.5">{{ u.email }}</div>
                        </td>
                        <td class="p-4">
                            <span class="px-3 py-1.5 rounded-md text-[9px] font-black uppercase tracking-widest border" :class="getRoleBadgeClass(u.role)">
                                {{ u.role }}
                            </span>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center justify-center gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                <button @click="resetPassword(u)" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:border-amber-200 hover:bg-amber-50 hover:text-amber-600 flex items-center justify-center transition-colors shadow-sm" title="Reset Password">🔑</button>
                                <button @click="openEditModal(u)" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-600 flex items-center justify-center transition-colors shadow-sm" title="Edit">✏️</button>
                                <button @click="confirmDelete(u)" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 flex items-center justify-center transition-colors shadow-sm" title="Hapus">🗑️</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 sm:p-6 bg-slate-50 border-t border-slate-200 flex items-center justify-between shrink-0" v-if="pagination.lastPage > 1">
            <span class="hidden sm:inline-block text-[10px] font-black uppercase text-slate-400 tracking-widest">Halaman {{ pagination.currentPage }} dari {{ pagination.lastPage }}</span>
            <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-end">
                <button @click="fetchUsers(pagination.currentPage - 1)" :disabled="pagination.currentPage === 1" class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 disabled:opacity-50 shadow-sm transition-all">Prev</button>
                <span class="sm:hidden text-[10px] font-black uppercase text-slate-400 tracking-widest">{{ pagination.currentPage }} / {{ pagination.lastPage }}</span>
                <button @click="fetchUsers(pagination.currentPage + 1)" :disabled="pagination.currentPage === pagination.lastPage" class="px-4 py-2.5 bg-emerald-50 border border-emerald-100 rounded-xl text-[10px] font-black uppercase tracking-widest text-emerald-700 hover:bg-emerald-100 disabled:opacity-50 shadow-sm transition-all">Next</button>
            </div>
        </div>
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
                            <select v-model="editForm.role" class="w-full px-4 py-2.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-bold text-slate-700 appearance-none">
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

                    <button type="submit" :disabled="isSaving" class="w-full mt-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-2xl shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 text-[11px] uppercase tracking-widest">
                        <span v-if="isSaving" class="animate-spin text-sm">⏳</span>
                        <span v-else class="text-sm">💾</span>
                        {{ isSaving ? 'Memproses...' : 'Simpan Perubahan' }}
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

    <!-- ==============================================
         MODAL KONFIRMASI RESET PASSWORD (Overlay)
         ============================================== -->
    <div v-if="isResetModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade text-center">
            <div class="p-8">
                <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner border-4 border-white ring-4 ring-amber-50">
                    🔑
                </div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Reset Password?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Password akun <span class="font-bold text-amber-600">{{ userToReset?.name }}</span> akan direset menjadi <span class="font-bold text-slate-800 bg-slate-100 px-1 py-0.5 rounded">12345678</span>.
                </p>
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isResetModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">
                        Batal
                    </button>
                    <button @click="executeReset" :disabled="isSaving" class="flex-1 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-2xl shadow-lg shadow-amber-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        <span v-if="isSaving" class="animate-spin text-base">⏳</span>
                        <span v-else>Ya, Reset</span>
                    </button>
                </div>
            </div>
      </div>

    </div>

    <!-- Footer Info -->
    <div class="bg-slate-100 border-t border-slate-200 text-center py-3 shrink-0 print:hidden z-20">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Created by <span class="text-emerald-600">SMK-Yatindo</span></p>
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
const isDesktop = computed(() => windowWidth.value >= 1280) // xl breakpoint

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

// Import state
const fileInput = ref(null)
const selectedFile = ref(null)

const downloadTemplate = async () => {
    const token = useCookie('auth_token').value
    try {
        const blob = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/admin/users/template', {
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
        useSwal().fire('Error', 'Gagal mengunduh template CSV.', 'error')
    }
}

const handleFileChange = (e) => {
    const file = e.target.files[0]
    if (file && (file.type === 'text/csv' || file.name.endsWith('.csv') || file.type === 'application/vnd.ms-excel')) {
        selectedFile.value = file
    } else if (file) {
        useSwal().fire('Gagal!', 'Pastikan Anda menyimpan Excel tersebut dengan format Save As -> CSV (Comma delimited).', 'error')
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
        const response = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/admin/users/import', {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` },
            body: formData
        })
        
        if (response.success) {
            useSwal().toast(response.message)
            selectedFile.value = null
            if (fileInput.value) fileInput.value.value = ''
            fetchUsers(1)
            if (!isDesktop.value) activeTab.value = 'table'
        }
    } catch (error) {
        console.error('Import failed:', error)
        useSwal().fire('Import Gagal', error.data?.message || 'Terjadi kesalahan saat mengimpor file. Cek format kolom Anda.', 'error')
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
const isResetModalOpen = ref(false)
const userToDelete = ref(null)
const userToReset = ref(null)

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

        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/admin/users?${queryParams.toString()}`, {
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
        const response = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/admin/users', {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` },
            body: form.value
        })
        
        if (response.success) {
            useSwal().toast('Pengguna berhasil ditambahkan!')
            // Reset Form
            form.value = { name: '', email: '', role: 'guru', password: '', is_pengampu_umum: true, is_pengampu_kejuruan: false }
            // Fetch updated list
            fetchUsers(1)
            // Redirect back to table view on mobile
            if (!isDesktop.value) activeTab.value = 'table'
        }
    } catch (error) {
        console.error('Failed to save user:', error)
        useSwal().toast('Gagal menambahkan pengguna. Email mungkin sudah terdaftar.', 'error')
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
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/admin/users/${editForm.value.id}`, {
            method: 'PUT',
            headers: { Authorization: `Bearer ${token}` },
            body: editForm.value
        })
        
        if (response.success) {
            isEditModalOpen.value = false
            useSwal().toast('Data pengguna berhasil diperbarui!')
            fetchUsers(pagination.value.currentPage)
        }
    } catch (error) {
        console.error('Failed to update user:', error)
        useSwal().toast('Gagal memperbarui pengguna.', 'error')
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
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/admin/users/${userToDelete.value.id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        
        if (response.success) {
            isDeleteModalOpen.value = false
            useSwal().toast('Pengguna telah dihapus secara permanen.')
            fetchUsers(pagination.value.currentPage)
        }
    } catch (error) {
        console.error('Failed to delete user:', error)
        useSwal().toast('Gagal menghapus pengguna (Mungkin Anda menghapus akun sendiri).', 'error')
    } finally {
        isSaving.value = false
    }
}

// Reset Password Modal
const resetPassword = (user) => {
    userToReset.value = user
    isResetModalOpen.value = true
}

const executeReset = async () => {
    if (!userToReset.value) return
    isSaving.value = true
    
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/admin/users/${userToReset.value.id}/reset-password`, {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` }
        })
        
        if (response.success) {
            isResetModalOpen.value = false
            useSwal().toast('Password berhasil direset menjadi 12345678!', 'success')
        }
    } catch (error) {
        console.error('Failed to reset password:', error)
        useSwal().toast('Gagal mereset password.', 'error')
    } finally {
        isSaving.value = false
    }
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
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #94a3b8;
}
</style>
