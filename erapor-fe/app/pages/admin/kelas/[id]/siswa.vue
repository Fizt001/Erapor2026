<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex flex-col xl:flex-row overflow-hidden relative">
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-2 grid grid-cols-2 gap-2 z-20">
        <button type="button" @click="activeTab = 'table'"
          :class="activeTab === 'table' ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 ring-2 ring-emerald-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="rounded-xl flex flex-col items-center justify-center py-2 px-1 transition-all active:scale-95">
          <span class="text-lg mb-0.5 transition-transform" :class="activeTab === 'table' ? 'scale-110' : ''"><AppIcon name="user" />‍<AppIcon name="academic-cap" /></span>
          <span class="text-[9px] font-black uppercase tracking-wider text-center leading-none">Anggota Kelas</span>
        </button>
        <button type="button" @click="activeTab = 'form'"
          :class="activeTab === 'form' ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 ring-2 ring-emerald-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="rounded-xl flex flex-col items-center justify-center py-2 px-1 transition-all active:scale-95">
          <span class="text-lg mb-0.5 transition-transform" :class="activeTab === 'form' ? 'scale-110' : ''"><AppIcon name="inbox" /></span>
          <span class="text-[9px] font-black uppercase tracking-wider text-center leading-none">Tarik User</span>
        </button>
      </div>

      <!-- Panel Dock Kiri -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all', activeTab === 'form' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[60px]' : '']">
        
        <div class="p-4 pb-2 shrink-0 z-10 relative">
          <div class="bg-gradient-to-r from-teal-600 to-emerald-700 rounded-2xl p-4 border border-teal-500 shadow-sm relative overflow-hidden flex items-center gap-3">
            <div class="w-8 h-8 flex items-center justify-center shrink-0 bg-white/10 rounded-lg relative z-10 text-white"><AppIcon name="inbox" class="w-5 h-5" /></div>
            <div class="relative z-10">
                <h3 class="text-xs font-black uppercase tracking-widest text-white">Tarik User</h3>
                <p class="text-[9px] text-teal-100 font-semibold uppercase mt-0.5">Tambah Siswa Ke Kelas</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-15 text-white pointer-events-none">
              <svg class="w-16 h-16 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path></svg>
            </div>
          </div>
        </div>

        <div class="p-4 border-b border-slate-100 bg-slate-50/50 shrink-0">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 text-lg"><AppIcon name="magnifying-glass" /></span>
                <input type="text" v-model="searchKandidat" placeholder="Cari nama siswa..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border-2 border-slate-200/70 bg-white focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-xs font-bold text-slate-800 outline-none placeholder-slate-400">
            </div>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar bg-slate-50">
            <div v-if="filteredKandidat.length === 0" class="text-center py-8">
                <div class="w-12 h-12 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-3 text-xl opacity-50"><AppIcon name="inbox" /></div>
                <p class="text-xs font-bold text-slate-500">Tidak ada kandidat.</p>
            </div>
            <div v-else class="p-3 space-y-2">
                <div v-for="u in filteredKandidat" :key="u.id" class="p-3 rounded-xl border-2 transition-all duration-200 cursor-pointer shadow-sm" :class="selectedUsers.includes(u.id) ? 'bg-emerald-50 border-emerald-200 shadow-emerald-500/10' : 'border-slate-200/60 bg-white hover:border-slate-300'" @click.self="toggleUserSelection(u.id)">
                    <div class="flex items-start gap-2.5 pointer-events-none">
                        <input type="checkbox" :value="u.id" v-model="selectedUsers" class="w-4 h-4 text-emerald-600 rounded border-slate-300 pointer-events-auto focus:ring-emerald-500 focus:ring-2 mt-0.5 shrink-0 transition-all">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-black text-slate-800 truncate" :class="{'text-emerald-700': selectedUsers.includes(u.id)}">{{ u.name }}</p>
                            <p class="text-[9px] font-bold text-slate-400 mt-0.5 truncate uppercase tracking-wider">{{ u.email || u.username || '-' }}</p>
                            <div class="mt-2 pointer-events-auto">
                                <input type="text" v-model="bulkNis[u.id]" @input="autoCheck(u.id)" placeholder="NIS (WAJIB)" class="w-full px-2.5 py-1.5 rounded-lg border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-[11px] font-bold text-slate-800 outline-none uppercase tracking-wider placeholder-slate-400">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-slate-50 border-t border-slate-200 shrink-0">
            <button @click="assignBulkUser" :disabled="isSaving || selectedUsers.length === 0" class="w-full py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-2xl shadow-lg shadow-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/40 hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-2 uppercase tracking-widest text-xs">
                <span v-if="isSaving" class="animate-spin text-lg"><AppIcon name="clock" /></span>
                <span v-else class="text-lg"><AppIcon name="save" /></span>
                <span>Tambahkan ({{ selectedUsers.length }})</span>
            </button>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0', !isDesktop && activeTab === 'form' ? 'hidden' : 'flex', !isDesktop ? 'pt-[60px]' : '']">
        <div class="p-2 sm:pt-3 sm:pb-6 sm:px-6 lg:pt-3 lg:pb-8 lg:px-8 max-w-5xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-2xl sm:rounded-[2rem] shadow-[0_2px_15px_-3px_rgba(0,0,0,0.05)] overflow-hidden flex flex-col flex-1 relative min-h-0">
            
            <!-- Table Header & Filters -->
            <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shrink-0 z-10 sticky top-0 bg-white/80 backdrop-blur-xl">
                <div class="flex items-center gap-4 w-full sm:w-auto">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 shadow-sm flex items-center justify-center text-2xl text-white hidden sm:flex"><AppIcon name="user" />‍<AppIcon name="academic-cap" /></div>
                <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-emerald-700">
                            {{ kelas.tingkat ? `${kelas.tingkat} ${kelas.nama_kelas}` : 'Anggota Kelas' }}
                        </h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">
                            Tahun Ajaran: {{ kelas.tahun_ajaran?.tahun || '-' }} | Total: {{ students.filter(s => s.status_siswa === 'aktif').length }} Aktif
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <div class="relative">
                            <input type="checkbox" v-model="showInactive" @change="fetchData" class="sr-only">
                            <div class="block bg-slate-200 w-10 h-6 rounded-full transition-colors" :class="showInactive ? 'bg-emerald-500' : ''"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform" :class="showInactive ? 'transform translate-x-4' : ''"></div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Tampilkan Non-Aktif</span>
                    </label>
                    <button @click="navigateTo('/admin/kelas')" class="w-full sm:w-auto px-6 py-2.5 bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-800 font-bold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 text-[10px] uppercase tracking-widest">
                        <span><AppIcon name="arrow-left" /></span>
                        <span>Kembali</span>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar relative z-0 flex flex-col h-full">
                
                <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60">
                    <div class="w-8 h-8 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                    <span class="text-xs font-bold text-slate-500">Memuat Data...</span>
                </div>
                
                <div v-else class="flex-grow flex flex-col">
                    <div v-if="students.length === 0" class="text-center py-20 m-auto">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl opacity-50">🪹</div>
                        <p class="text-sm font-bold text-slate-600">Belum ada siswa di kelas ini.</p>
                        <p class="text-xs text-slate-400 mt-1">Pilih dan tambahkan siswa dari panel di sebelah kiri.</p>
                    </div>
                    
                    <div v-else class="overflow-x-auto flex-1">
                        <table class="w-full text-left border-collapse min-w-full">
                            <thead class="hidden sm:table-header-group">
                                <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-200">
                                    <th class="py-4 px-4 pl-6 w-16 text-center">#</th>
                                    <th class="py-4 px-4">Nama Siswa / Akun</th>
                                    <th class="py-4 px-4 text-center">NIS</th>
                                    <th class="py-4 px-4 text-center">Status</th>
                                    <th class="py-4 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm flex flex-col sm:table-row-group">
                                <tr v-for="(s, index) in paginatedStudents" :key="s.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors group bg-white flex flex-col sm:table-row p-4 sm:p-0 relative">
                                    <td class="px-0 py-1 sm:p-4 sm:pl-6 text-left sm:text-center text-xs font-bold text-slate-400 flex sm:table-cell items-center justify-between">
                                        <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-slate-400">Nomor</span>
                                        <span>{{ (currentPage - 1) * itemsPerPage + index + 1 }}</span>
                                    </td>
                                    <td class="px-0 py-1 sm:p-4 flex sm:table-cell items-center justify-between border-b sm:border-0 border-slate-50 pb-2 sm:pb-4 mb-1 sm:mb-0">
                                        <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-slate-400">Nama Siswa</span>
                                        <p class="font-black text-slate-800 text-[13px]">{{ s.user?.name || 'Unknown' }}</p>
                                    </td>
                                    <td class="px-0 py-1 sm:p-4 flex sm:table-cell items-center justify-between border-b sm:border-0 border-slate-50 pb-2 sm:pb-4 mb-1 sm:mb-0 sm:text-center">
                                        <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-slate-400">NIS</span>
                                        <span class="inline-flex items-center px-2.5 py-1.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-black uppercase tracking-widest border border-slate-200">
                                            {{ s.nis }}
                                        </span>
                                    </td>
                                    <td class="px-0 py-1 sm:p-4 flex sm:table-cell items-center justify-between border-b sm:border-0 border-slate-50 pb-3 sm:pb-4 mb-2 sm:mb-0 sm:text-center">
                                        <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-slate-400">Status</span>
                                        <div class="flex flex-col items-end sm:items-center justify-center">
                                            <span :class="s.status_siswa === 'aktif' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-rose-100 text-rose-700 border-rose-200'" class="inline-flex items-center px-2.5 py-1.5 rounded-md text-[11px] font-black uppercase tracking-widest border">
                                                {{ s.status_siswa === 'pindah_sekolah' ? 'PINDAH SEKOLAH' : (s.status_siswa || 'aktif') }}
                                            </span>
                                            <span v-if="s.status_siswa !== 'aktif' && s.alasan_keluar" class="text-[9px] text-rose-500 font-bold mt-1 max-w-[120px] truncate" :title="s.alasan_keluar">{{ s.alasan_keluar }}</span>
                                        </div>
                                    </td>
                                    <td class="px-0 pt-2 sm:p-4 sm:pr-6 text-center">
                                        <div class="flex items-center justify-center sm:justify-end gap-3 opacity-100 xl:opacity-0 xl:group-hover:opacity-100 transition-opacity">
                                            <button v-if="s.status_siswa === 'aktif'" @click="openMutasiModal(s)" class="w-10 h-10 sm:w-8 sm:h-8 rounded-xl sm:rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-amber-500 hover:border-amber-200 hover:bg-amber-50 flex items-center justify-center transition-all shadow-sm" title="Proses Mutasi"><AppIcon name="arrow-path" /></button>
                                            <button @click="openEditNisModal(s)" class="w-10 h-10 sm:w-8 sm:h-8 rounded-xl sm:rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-emerald-500 hover:border-emerald-200 hover:bg-emerald-50 flex items-center justify-center transition-all shadow-sm" title="Edit NIS">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                            <button @click="confirmDelete(s.id, s.user?.name)" class="w-10 h-10 sm:w-8 sm:h-8 rounded-xl sm:rounded-lg bg-white border border-slate-100 text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 flex items-center justify-center transition-all shadow-sm" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="totalPages > 1" class="p-4 sm:p-6 bg-slate-50 border-t border-slate-200 flex items-center justify-between shrink-0 mt-auto">
                        <span class="hidden sm:inline-block text-[10px] font-black uppercase text-slate-400 tracking-widest">Halaman {{ currentPage }} dari {{ totalPages }}</span>
                        <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-end">
                            <button @click="prevPage" :disabled="currentPage === 1" class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 disabled:opacity-50 shadow-sm transition-all">Prev</button>
                            <span class="sm:hidden text-[10px] font-black uppercase text-slate-400 tracking-widest">{{ currentPage }} / {{ totalPages }}</span>
                            <button @click="nextPage" :disabled="currentPage === totalPages" class="px-4 py-2.5 bg-emerald-50 border border-emerald-100 rounded-xl text-[10px] font-black uppercase tracking-widest text-emerald-700 hover:bg-emerald-100 disabled:opacity-50 shadow-sm transition-all">Next</button>
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL EDIT NIS -->
    <div v-if="isEditModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-800">Edit NIS Siswa</h3>
                    <button @click="isEditModalOpen = false" class="text-slate-400 hover:text-rose-500 w-6 h-6 flex items-center justify-center rounded-md hover:bg-rose-50 transition-colors"><AppIcon name="x-mark" /></button>
                </div>
                <form @submit.prevent="executeEditNis" class="space-y-4">
                    <div>
                        <p class="text-xs font-medium text-slate-500 mb-1">Nama Siswa</p>
                        <p class="text-sm font-bold text-slate-800 mb-4">{{ editForm.name }}</p>
                        <label class="text-xs font-medium text-slate-600 mb-1 block">NIS</label>
                        <input type="text" v-model="editForm.nis" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-bold text-slate-800 outline-none mb-4">
                        <label class="text-xs font-medium text-slate-600 mb-1 block">Status Siswa (Bypass)</label>
                        <select v-model="editForm.status_siswa" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-bold text-slate-800 outline-none">
                            <option value="aktif">Aktif</option>
                            <option value="lulus">Lulus</option>
                            <option value="pindah_sekolah">Pindah Sekolah</option>
                            <option value="keluar">Keluar</option>
                            <option value="dikeluarkan">Dikeluarkan</option>
                        </select>
                        <p class="text-[9px] text-slate-400 mt-2 font-bold leading-relaxed">
                            Gunakan tombol Mutasi (<AppIcon name="arrow-path" />) untuk mencatat alasan dan tanggal keluar secara detail.
                        </p>
                    </div>
                    <button type="submit" :disabled="isSaving" class="w-full py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl shadow-sm hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-100 transition-all flex items-center justify-center gap-2">
                        <span v-if="isSaving" class="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span>
                        <span>Simpan Perubahan</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL PROSES MUTASI -->
    <div v-if="isMutasiModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-amber-700 flex items-center gap-2">
                        <span><AppIcon name="arrow-path" /></span>
                        Proses Mutasi Siswa
                    </h3>
                    <button @click="isMutasiModalOpen = false" class="text-slate-400 hover:text-rose-500 w-6 h-6 flex items-center justify-center rounded-md hover:bg-rose-50 transition-colors"><AppIcon name="x-mark" /></button>
                </div>
                
                <form @submit.prevent="executeMutasi" class="space-y-4">
                    <div>
                        <p class="text-xs font-medium text-slate-500 mb-1">Nama Siswa</p>
                        <p class="text-sm font-black text-slate-800 mb-4">{{ mutasiForm.name }}</p>
                        
                        <label class="text-xs font-medium text-slate-600 mb-1 block">Jenis Mutasi</label>
                        <select v-model="mutasiForm.jenis_mutasi" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all text-sm font-bold text-slate-800 outline-none mb-4">
                            <option value="pindah_sekolah">⛔ Pindah Sekolah</option>
                            <option value="pindah_kelas">🔄 Pindah Kelas (Internal)</option>
                            <option value="keluar">⛔ Keluar</option>
                            <option value="dikeluarkan">⚠️ Dikeluarkan</option>
                        </select>

                        <template v-if="mutasiForm.jenis_mutasi === 'pindah_kelas'">
                            <label class="text-xs font-medium text-slate-600 mb-1 block">Pilih Kelas Tujuan</label>
                            <select v-model="mutasiForm.kelas_tujuan_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all text-sm font-bold text-slate-800 outline-none mb-4">
                                <option value="">-- Pilih Kelas --</option>
                                <option v-for="k in semua_kelas.filter(k => k.id !== parseInt(kelasId))" :key="k.id" :value="k.id">
                                    {{ k.tingkat }} {{ k.nama_kelas }}
                                </option>
                            </select>
                        </template>

                        <template v-else>
                            <label class="text-xs font-medium text-slate-600 mb-1 block">Tanggal Mutasi / Keluar</label>
                            <input type="date" v-model="mutasiForm.tanggal_mutasi" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all text-sm font-bold text-slate-800 outline-none mb-4">
                            
                            <label class="text-xs font-medium text-slate-600 mb-1 block">Sekolah Tujuan / Alasan Detail</label>
                            <textarea v-model="mutasiForm.alasan" required rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all text-sm font-bold text-slate-800 outline-none" placeholder="Masukkan nama sekolah tujuan atau alasan lengkap..."></textarea>
                        </template>
                    </div>

                    <button type="submit" :disabled="isSaving" class="w-full py-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-xs uppercase tracking-widest font-black rounded-xl shadow-lg shadow-amber-500/30 hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                        <span v-if="isSaving" class="animate-spin text-base"><AppIcon name="clock" /></span>
                        <span>Proses Mutasi & Arsipkan</span>
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
                    <AppIcon name="exclamation-triangle" />️
                </div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Keluarkan Siswa?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Anda yakin ingin mengeluarkan:<br>
                    <span class="font-bold text-slate-800">{{ deleteTarget.name }}</span><br>
                    dari kelas ini?
                </p>
                <p class="text-[10px] font-bold text-rose-500 mt-3 p-2 bg-rose-50 rounded-lg">Peringatan: Seluruh history biodata siswa ini akan ikut terhapus.</p>
                
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isDeleteModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">
                        Batal
                    </button>
                    <button @click="executeDelete" :disabled="isDeleting" class="flex-1 py-3 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        <span v-if="isDeleting" class="animate-spin text-base"><AppIcon name="clock" /></span>
                        <span v-else>Keluarkan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
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

const activeTab = ref('table')

const kelas = ref({})
const students = ref([])
const usersAvailable = ref([])
const semua_kelas = ref([]) // Untuk opsi mutasi pindah kelas

const isLoading = ref(true)
const isSaving = ref(false)

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

const searchKandidat = ref('')
const selectedUsers = ref([])
const bulkNis = ref({})

const isDeleteModalOpen = ref(false)
const isDeleting = ref(false)
const deleteTarget = ref({ id: null, name: '' })

const toggleUserSelection = (id) => {
    if (selectedUsers.value.includes(id)) {
        selectedUsers.value = selectedUsers.value.filter(uid => uid !== id)
    } else {
        selectedUsers.value.push(id)
    }
}

const filteredKandidat = computed(() => {
    if (!searchKandidat.value) return usersAvailable.value
    const s = searchKandidat.value.toLowerCase()
    return usersAvailable.value.filter(u => u.name.toLowerCase().includes(s) || (u.email && u.email.toLowerCase().includes(s)) || (u.username && u.username.toLowerCase().includes(s)))
})

const isEditModalOpen = ref(false)
const showInactive = ref(false)
const editForm = ref({
    id: null,
    name: '',
    nis: '',
    status_siswa: 'aktif'
})

const isMutasiModalOpen = ref(false)
const mutasiForm = ref({
    siswa_id: null,
    name: '',
    jenis_mutasi: 'pindah_sekolah',
    kelas_tujuan_id: '',
    tanggal_mutasi: '',
    alasan: ''
})

const fetchData = async () => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/admin/kelas/${kelasId}/siswa?show_inactive=${showInactive.value ? '1' : '0'}`, {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            kelas.value = response.kelas
            usersAvailable.value = response.users_available
            students.value = response.students
            semua_kelas.value = response.semua_kelas || []
            
            if (currentPage.value > Math.ceil(students.value.length / itemsPerPage) && students.value.length > 0) {
                currentPage.value = Math.max(1, Math.ceil(students.value.length / itemsPerPage))
            } else if (students.value.length === 0) {
                currentPage.value = 1
            }
        }
    } catch (error) {
        console.error('Failed to fetch data:', error)
        useSwal().toast('Gagal memuat data rombel.', 'error')
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
    
    const payloadSiswa = []
    for (const uid of selectedUsers.value) {
        if (!bulkNis.value[uid]) {
            useSwal().toast('Ada siswa yang dicentang namun NIS-nya masih kosong!', 'warning')
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
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/admin/kelas/${kelasId}/siswa`, {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` },
            body: { siswa: payloadSiswa }
        })
        if (response.success) {
            useSwal().toast(response.message, 'success')
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
        useSwal().toast(msg, 'error')
    } finally {
        isSaving.value = false
    }
}

const openEditNisModal = (s) => {
    editForm.value = {
        id: s.id,
        name: s.user?.name || 'Unknown',
        nis: s.nis,
        status_siswa: s.status_siswa || 'aktif'
    }
    isEditModalOpen.value = true
}

const executeEditNis = async () => {
    isSaving.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/admin/siswa/${editForm.value.id}`, {
            method: 'PUT',
            headers: { Authorization: `Bearer ${token}` },
            body: { 
                nis: editForm.value.nis, 
                kelas_id: kelasId, 
                status_siswa: editForm.value.status_siswa 
            }
        })
        if (response.success) {
            isEditModalOpen.value = false
            useSwal().toast(response.message, 'success')
            await fetchData()
        }
    } catch (error) {
        console.error('Update NIS failed:', error)
        useSwal().toast('Gagal memperbarui NIS. Pastikan NIS unik.', 'error')
    } finally {
        isSaving.value = false
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
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/admin/siswa/${deleteTarget.value.id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            isDeleteModalOpen.value = false
            useSwal().toast(response.message, 'success')
            await fetchData()
        }
    } catch (error) {
        console.error('Delete failed:', error)
        useSwal().toast('Gagal mengeluarkan siswa.', 'error')
    } finally {
        isDeleting.value = false
    }
}

const openMutasiModal = (s) => {
    mutasiForm.value = {
        siswa_id: s.id,
        name: s.user?.name || '',
        jenis_mutasi: 'pindah_sekolah',
        kelas_tujuan_id: '',
        tanggal_mutasi: new Date().toISOString().split('T')[0],
        alasan: ''
    }
    isMutasiModalOpen.value = true
}

const executeMutasi = async () => {
    isSaving.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/admin/mutasi/proses`, {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` },
            body: mutasiForm.value
        })

        if (response.success) {
            useSwal().toast('Berhasil!', response.message, 'success')
            isMutasiModalOpen.value = false
            await fetchData()
        }
    } catch (error) {
        let msg = error.response?._data?.message || 'Terjadi kesalahan'
        if (error.response?._data?.errors) {
            msg = Object.values(error.response._data.errors)[0][0]
        }
        useSwal().toast(msg, 'error')
    } finally {
        isSaving.value = false
    }
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
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
