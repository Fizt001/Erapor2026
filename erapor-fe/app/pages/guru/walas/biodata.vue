<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex flex-col xl:flex-row overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="xl:w-[360px] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] overflow-y-auto custom-scrollbar">
        <div class="p-6 space-y-6">
          <div class="bg-gradient-to-r from-sky-600 to-blue-700 rounded-2xl p-5 border border-sky-500 shadow-sm relative overflow-hidden">
            <div class="relative z-10">
              <h3 class="text-sm font-black uppercase tracking-widest text-white">Biodata Siswa</h3>
              <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Kelengkapan Profil Rapor Siswa</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-10">
              <svg class="w-24 h-24 transform translate-x-6 translate-y-6" fill="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
          </div>
          
          <div class="space-y-4">
            <!-- Search / Filter -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pencarian Siswa</label>
              <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 pointer-events-none">🔍</span>
                  <input type="text" v-model="searchQuery" placeholder="Cari nama atau NISN..." 
                    class="w-full pl-10 pr-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none">
              </div>
            </div>

            <div class="bg-teal-50 border border-teal-100 p-4 rounded-2xl">
              <div class="flex items-start mb-2">
                <span class="text-teal-500 mr-2 text-sm leading-none mt-0.5">💡</span>
                <div class="text-[11px] text-teal-800 font-semibold leading-relaxed">
                  Lengkapi data profil siswa hingga mencapai 100% untuk cetak cover buku rapor.
                </div>
              </div>
              <div class="space-y-1.5 mt-3 border-t border-teal-200/50 pt-3">
                  <div class="flex items-center text-[10px] font-bold text-teal-700">
                      <div class="w-2 h-2 rounded-full bg-teal-500 mr-2"></div> Lengkap (100%)
                  </div>
                  <div class="flex items-center text-[10px] font-bold text-amber-700">
                      <div class="w-2 h-2 rounded-full bg-amber-400 mr-2"></div> Sebagian (> 50%)
                  </div>
                  <div class="flex items-center text-[10px] font-bold text-rose-700">
                      <div class="w-2 h-2 rounded-full bg-rose-500 mr-2"></div> Kurang (< 50%)
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            
            <!-- Header Card Kanan -->
            <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-white shrink-0 z-10">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-lg border border-indigo-100">👥</div>
                <div>
                  <h3 class="text-[13px] font-black leading-none uppercase tracking-wide text-slate-800">Daftar Siswa Perwalian</h3>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ filteredStudents.length }} Siswa Aktif</p>
                </div>
              </div>
              
              <div class="flex items-center space-x-4 shrink-0 mt-4 md:mt-0">
                 <button @click="refreshData" class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 transition-all active:scale-95" :disabled="pending">
                  <span :class="{'animate-spin': pending}">🔄</span> <span>REFRESH</span>
                </button>
              </div>
            </div>

            <!-- Loading Indicator -->
            <div v-if="pending" class="flex-grow flex flex-col items-center justify-center p-20 opacity-60">
              <div class="w-8 h-8 border-4 border-indigo-400 border-t-transparent rounded-full animate-spin mb-4"></div>
              <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Memuat Data Siswa...</span>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="flex-grow flex flex-col items-center justify-center p-16 text-center">
              <div class="text-rose-500 text-4xl mb-4">⚠️</div>
              <h3 class="text-rose-800 font-black mb-1">Gagal Memuat Data</h3>
              <p class="text-rose-600 text-sm font-semibold">{{ error.message || 'Terjadi kesalahan pada server.' }}</p>
              <button @click="refreshData" class="mt-4 px-4 py-2 bg-rose-100 hover:bg-rose-200 text-rose-700 text-[10px] font-black uppercase tracking-widest rounded-lg transition-colors">
                Coba Lagi
              </button>
            </div>

            <!-- List Workspace Area -->
            <div v-else class="flex-grow flex flex-col relative bg-slate-50/30 overflow-hidden">
              <div class="flex-1 overflow-auto custom-scrollbar relative pb-10">
                <table class="w-full text-left border-collapse bg-white">
                  <thead class="sticky top-0 z-20 shadow-sm">
                    <tr class="bg-slate-100 border-b border-slate-200">
                      <th class="py-3 px-4 text-center text-[10px] font-black uppercase tracking-widest text-slate-500 w-[60px] border-r border-slate-200">No</th>
                      <th class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500 border-r border-slate-200 min-w-[250px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] sticky left-0 bg-slate-100 z-30">Nama Siswa & NISN</th>
                      <th class="py-3 px-4 text-center text-[10px] font-black uppercase tracking-widest text-slate-500 w-[80px] border-r border-slate-200">L/P</th>
                      <th class="py-3 px-4 text-center text-[10px] font-black uppercase tracking-widest text-slate-500 min-w-[200px] border-r border-slate-200">Status Kelengkapan</th>
                      <th class="py-3 px-4 text-center text-[10px] font-black uppercase tracking-widest text-slate-500 w-[120px] bg-slate-50">Aksi</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    <tr v-for="(siswa, index) in filteredStudents" :key="activeTab + '-' + siswa.id" class="hover:bg-slate-50/80 transition-colors group">
                      <td class="py-3 px-4 text-center text-[11px] font-bold text-slate-400 border-r border-slate-100">{{ index + 1 }}</td>
                      <td class="py-3 px-4 border-r border-slate-100 sticky left-0 bg-white group-hover:bg-slate-50/90 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.02)] z-10">
                        <div class="flex items-center gap-2">
                            <div class="text-[12px] font-black uppercase tracking-wide" :class="siswa.tanggal_keluar ? 'text-rose-600 line-through' : 'text-slate-700'">{{ siswa.nama_lengkap }}</div>
                            <span v-if="siswa.tanggal_keluar" class="px-1.5 py-0.5 bg-rose-100 text-rose-700 text-[8px] font-black uppercase tracking-widest rounded">Keluar: {{ siswa.tanggal_keluar }}</span>
                        </div>
                        <div class="text-[10px] text-slate-400 font-bold tracking-widest mt-0.5">{{ siswa.nisn || 'NISN -' }} | {{ siswa.nis || 'NIS -' }}</div>
                      </td>
                      <td class="py-3 px-4 text-center border-r border-slate-100">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-md text-[11px] font-black" 
                          :class="siswa.jenis_kelamin === 'L' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700'">
                          {{ siswa.jenis_kelamin }}
                        </span>
                      </td>
                      
                      <!-- Progress Bar -->
                      <td class="py-3 px-4 border-r border-slate-100">
                        <div class="flex items-center space-x-3 w-full">
                            <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200/50">
                                <div class="h-full rounded-full transition-all duration-500" 
                                     :class="getProgressBarColor(siswa.persentase_lengkap)"
                                     :style="{ width: `${siswa.persentase_lengkap}%` }">
                                </div>
                            </div>
                            <span class="text-[11px] font-black w-10 text-right tracking-widest" :class="getProgressTextColor(siswa.persentase_lengkap)">
                                {{ siswa.persentase_lengkap }}%
                            </span>
                        </div>
                      </td>

                      <!-- Aksi -->
                      <td class="py-3 px-4 text-center bg-slate-50/30">
                        <div class="flex items-center justify-center gap-2">
                            <button 
                                @click="openEditModal(siswa)"
                                class="flex-1 px-2 py-2 bg-white border border-indigo-200 text-indigo-600 hover:bg-indigo-50 font-black rounded-lg transition-all text-[9px] uppercase tracking-widest shadow-sm hover:shadow active:scale-95 inline-flex items-center justify-center"
                            >
                                ✏️ Edit
                            </button>
                        </div>
                      </td>
                    </tr>
                    <tr v-if="filteredStudents.length === 0 && !pending">
                      <td colspan="5" class="py-12 text-center">
                        <div class="text-slate-300 text-4xl mb-3">🔍</div>
                        <div class="text-slate-500 font-bold text-sm">Tidak ada siswa yang sesuai.</div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Modal -->
    <div v-if="isModalOpen" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4 sm:p-6">
        <div class="bg-white w-full max-w-4xl rounded-3xl shadow-2xl flex flex-col max-h-full sm:max-h-[90vh] overflow-hidden border border-slate-200/50 transform transition-all scale-100">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-slate-50 shrink-0">
                <div>
                    <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest">Edit Biodata Siswa</h2>
                    <p class="text-[11px] text-slate-500 font-bold tracking-widest mt-1">{{ editForm.nama_lengkap }}</p>
                </div>
                <button @click="closeModal" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-200 text-slate-500 hover:bg-slate-300 hover:text-slate-700 transition-colors font-bold">&times;</button>
            </div>
            
            <!-- Tabs -->
            <div class="flex border-b border-slate-200 px-2 pt-2 bg-slate-50 overflow-x-auto custom-scrollbar shrink-0">
                <button @click="activeTab = 'identitas'" :class="activeTab === 'identitas' ? 'border-indigo-500 text-indigo-600 bg-white' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-100/50'" class="px-5 py-3 border-b-2 font-black text-[11px] uppercase tracking-widest transition-all whitespace-nowrap rounded-t-lg mx-1">Identitas</button>
                <button @click="activeTab = 'ortu'" :class="activeTab === 'ortu' ? 'border-indigo-500 text-indigo-600 bg-white' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-100/50'" class="px-5 py-3 border-b-2 font-black text-[11px] uppercase tracking-widest transition-all whitespace-nowrap rounded-t-lg mx-1">Orang Tua & Wali</button>
                <button @click="activeTab = 'sekolah'" :class="activeTab === 'sekolah' ? 'border-indigo-500 text-indigo-600 bg-white' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-100/50'" class="px-5 py-3 border-b-2 font-black text-[11px] uppercase tracking-widest transition-all whitespace-nowrap rounded-t-lg mx-1">Sekolah Asal & SMK</button>
                <button @click="activeTab = 'pkl'" :class="activeTab === 'pkl' ? 'border-indigo-500 text-indigo-600 bg-white' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-100/50'" class="px-5 py-3 border-b-2 font-black text-[11px] uppercase tracking-widest transition-all whitespace-nowrap rounded-t-lg mx-1">Data PKL</button>
            </div>

            <!-- Body -->
            <div class="p-6 overflow-y-auto flex-1 custom-scrollbar bg-white">
                <form @submit.prevent="submitEdit">
                    <!-- Tab Identitas -->
                    <div v-show="activeTab === 'identitas'" class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Nama Lengkap</label>
                            <input v-model="editForm.nama_lengkap" type="text" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">NIS</label>
                                <input v-model="editForm.nis" type="text" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">NISN</label>
                                <input v-model="editForm.nisn" type="text" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Tempat Lahir</label>
                            <input v-model="editForm.tempat_lahir" type="text" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Tanggal Lahir</label>
                            <input v-model="editForm.tanggal_lahir" type="date" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Jenis Kelamin</label>
                            <select v-model="editForm.jenis_kelamin" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Agama</label>
                            <select v-model="editForm.agama" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                                <option value="" disabled>-- Pilih Agama --</option>
                                <option v-for="agama in agamaOptions" :key="agama.kode" :value="agama.nama">{{ agama.nama }}</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Anak Ke / Status Keluarga</label>
                            <div class="grid grid-cols-2 gap-4">
                                <input v-model="editForm.anak_ke" type="number" placeholder="Anak Ke" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                                <input v-model="editForm.status_keluarga" type="text" placeholder="Status (Anak Kandung)" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Warga Negara</label>
                            <input v-model="editForm.warga_negara" type="text" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">No HP Siswa</label>
                            <input v-model="editForm.no_hp" type="text" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                        </div>
                        <div class="sm:col-span-2 space-y-1.5">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Alamat Lengkap Siswa</label>
                            <textarea v-model="editForm.alamat" rows="2" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Tab Ortu & Wali -->
                    <div v-show="activeTab === 'ortu'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Data Ayah -->
                        <div class="space-y-4 p-5 border-2 border-slate-100 rounded-2xl bg-slate-50/50 relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-3 opacity-10 text-4xl">👨</div>
                            <h3 class="text-xs font-black text-indigo-700 uppercase tracking-widest border-b-2 border-indigo-100 pb-2 mb-4">Data Ayah</h3>
                            <div class="space-y-1.5 relative z-10">
                                <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest ml-1">Nama Ayah</label>
                                <input v-model="editForm.nama_ayah" type="text" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-2 focus:ring-indigo-500 outline-none">
                            </div>
                            <div class="space-y-1.5 relative z-10">
                                <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest ml-1">TTL Ayah</label>
                                <input v-model="editForm.ttl_ayah" type="text" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-2 focus:ring-indigo-500 outline-none">
                            </div>
                            <div class="space-y-1.5 relative z-10">
                                <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest ml-1">Pekerjaan Ayah</label>
                                <input v-model="editForm.pekerjaan_ayah" type="text" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-2 focus:ring-indigo-500 outline-none">
                            </div>
                            <div class="space-y-1.5 relative z-10">
                                <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest ml-1">No HP Ayah</label>
                                <input v-model="editForm.no_hp_ayah" type="text" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-2 focus:ring-indigo-500 outline-none">
                            </div>
                        </div>

                        <!-- Data Ibu -->
                        <div class="space-y-4 p-5 border-2 border-slate-100 rounded-2xl bg-slate-50/50 relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-3 opacity-10 text-4xl">👩</div>
                            <h3 class="text-xs font-black text-pink-700 uppercase tracking-widest border-b-2 border-pink-100 pb-2 mb-4">Data Ibu</h3>
                            <div class="space-y-1.5 relative z-10">
                                <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest ml-1">Nama Ibu</label>
                                <input v-model="editForm.nama_ibu" type="text" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-2 focus:ring-pink-500 outline-none">
                            </div>
                            <div class="space-y-1.5 relative z-10">
                                <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest ml-1">TTL Ibu</label>
                                <input v-model="editForm.ttl_ibu" type="text" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-2 focus:ring-pink-500 outline-none">
                            </div>
                            <div class="space-y-1.5 relative z-10">
                                <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest ml-1">Pekerjaan Ibu</label>
                                <input v-model="editForm.pekerjaan_ibu" type="text" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-2 focus:ring-pink-500 outline-none">
                            </div>
                            <div class="space-y-1.5 relative z-10">
                                <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest ml-1">No HP Ibu</label>
                                <input v-model="editForm.no_hp_ibu" type="text" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-2 focus:ring-pink-500 outline-none">
                            </div>
                        </div>

                        <!-- Data Wali -->
                        <div class="space-y-4 p-5 border-2 border-slate-100 rounded-2xl bg-slate-50/50 relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-3 opacity-10 text-4xl">🧑‍🦳</div>
                            <h3 class="text-xs font-black text-amber-700 uppercase tracking-widest border-b-2 border-amber-100 pb-2 mb-4">Data Wali</h3>
                            <div class="space-y-1.5 relative z-10">
                                <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest ml-1">Nama Wali</label>
                                <input v-model="editForm.nama_wali" type="text" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-2 focus:ring-amber-500 outline-none">
                            </div>
                            <div class="space-y-1.5 relative z-10">
                                <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest ml-1">Pekerjaan Wali</label>
                                <input v-model="editForm.pekerjaan_wali" type="text" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-2 focus:ring-amber-500 outline-none">
                            </div>
                            <div class="space-y-1.5 relative z-10">
                                <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest ml-1">No HP Wali</label>
                                <input v-model="editForm.no_hp_wali" type="text" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-2 focus:ring-amber-500 outline-none">
                            </div>
                            <div class="space-y-1.5 relative z-10">
                                <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest ml-1">Alamat Wali</label>
                                <textarea v-model="editForm.alamat_wali" rows="1" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-2 focus:ring-amber-500 outline-none resize-none"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Sekolah & SMK -->
                    <div v-show="activeTab === 'sekolah'" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Sekolah Asal -->
                        <div class="space-y-5">
                            <h3 class="text-xs font-black text-teal-700 uppercase tracking-widest border-b-2 border-teal-100 pb-2">Sekolah Asal (SMP/MTs)</h3>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Asal Sekolah</label>
                                <input v-model="editForm.asal_sekolah" type="text" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 outline-none transition-all">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Alamat Sekolah Asal</label>
                                <textarea v-model="editForm.alamat_sekolah_asal" rows="2" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 outline-none transition-all resize-none"></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">No STTB SMP</label>
                                    <input v-model="editForm.no_sttb_smp" type="text" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 outline-none transition-all">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Tgl STTB SMP</label>
                                    <input v-model="editForm.tgl_sttb_smp" type="date" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 outline-none transition-all">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Data SMK -->
                        <div class="space-y-5">
                            <h3 class="text-xs font-black text-sky-700 uppercase tracking-widest border-b-2 border-sky-100 pb-2">Data Masuk / Keluar SMK</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Tanggal Masuk</label>
                                    <input v-model="editForm.tanggal_masuk" type="date" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none transition-all">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Kelas Masuk</label>
                                    <input v-model="editForm.kelas_masuk" type="text" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none transition-all">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Tanggal Keluar</label>
                                    <input v-model="editForm.tanggal_keluar" type="date" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none transition-all">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Alasan Keluar</label>
                                    <input v-model="editForm.alasan_keluar" type="text" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none transition-all">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">No STTB SMK</label>
                                    <input v-model="editForm.no_sttb_smk" type="text" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none transition-all">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Tgl STTB SMK</label>
                                    <input v-model="editForm.tgl_sttb_smk" type="date" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab PKL -->
                    <div v-show="activeTab === 'pkl'" class="max-w-2xl mx-auto space-y-5">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Tempat PKL</label>
                            <input v-model="editForm.tempat_pkl" type="text" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Mulai PKL</label>
                                <input v-model="editForm.tgl_mulai_pkl" type="date" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Selesai PKL</label>
                                <input v-model="editForm.tgl_selesai_pkl" type="date" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all">
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Alamat PKL</label>
                            <textarea v-model="editForm.alamat_pkl" rows="3" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all resize-none"></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 rounded-b-3xl flex justify-end gap-3 shrink-0">
                <button @click="closeModal" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-xl text-[11px] font-black uppercase tracking-widest transition-colors shadow-sm">Batal</button>
                <button 
                    @click="submitEdit" 
                    :disabled="isSaving"
                    class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[11px] font-black uppercase tracking-widest shadow-md hover:shadow-lg transition-all active:scale-95 flex items-center disabled:opacity-50"
                >
                    <span v-if="isSaving" class="mr-2 animate-spin">⏳</span>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'

definePageMeta({
  layout: 'guru',
  middleware: 'guru',
  title: 'Biodata Siswa'
})

const token = useCookie('auth_token')
const pending = ref(true)
const error = ref(null)
const students = ref([])
const agamaOptions = ref([])
const searchQuery = ref('')

// Form Modal State
const isModalOpen = ref(false)
const isSaving = ref(false)
const activeTab = ref('identitas')
const editForm = ref({})

const filteredStudents = computed(() => {
    if (!searchQuery.value) return students.value
    const q = searchQuery.value.toLowerCase()
    return students.value.filter(s => 
        (s.nama_lengkap && s.nama_lengkap.toLowerCase().includes(q)) || 
        (s.nisn && s.nisn.toLowerCase().includes(q)) ||
        (s.nis && s.nis.toLowerCase().includes(q))
    )
})

const getProgressBarColor = (percentage) => {
    if (percentage === 100) return 'bg-teal-500'
    if (percentage >= 50) return 'bg-amber-400'
    return 'bg-rose-500'
}
const getProgressTextColor = (percentage) => {
    if (percentage === 100) return 'text-teal-600'
    if (percentage >= 50) return 'text-amber-600'
    return 'text-rose-600'
}

const fetchData = async () => {
  pending.value = true
  error.value = null
  try {
    const res = await $fetch('http://localhost:8000/api/guru/walas/biodata', {
      headers: { Authorization: `Bearer ${token.value}` }
    })
    
    if (res.success) {
      students.value = res.data
    } else {
      throw new Error(res.message || 'Gagal memuat data')
    }
  } catch (err) {
    error.value = err
  } finally {
    pending.value = false
  }
}

const refreshData = () => {
  fetchData()
}

const openEditModal = (siswa) => {
    editForm.value = { ...siswa }
    activeTab.value = 'identitas'
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
}

const submitEdit = async () => {
    isSaving.value = true
    try {
        const payload = { ...editForm.value }
        
        const res = await $fetch(`http://localhost:8000/api/guru/walas/biodata/${payload.id}`, {
            method: 'PUT',
            headers: { Authorization: `Bearer ${token.value}` },
            body: payload
        })

        if (res.success) {
            const idx = students.value.findIndex(s => s.id === payload.id)
            if (idx !== -1) {
                fetchData() // Refresh data to get updated percentages
            }
            closeModal()
            useNuxtApp().$swal.fire({
                title: 'Berhasil!',
                text: res.message || 'Biodata berhasil diperbarui!',
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })
        } else {
            throw new Error(res.message)
        }
    } catch (err) {
        useNuxtApp().$swal.fire({
            title: 'Gagal!',
            text: err.data?.message || err.message || 'Terjadi kesalahan saat menyimpan data.',
            icon: 'error',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        })
    } finally {
        isSaving.value = false
    }
}

const fetchAgamaOptions = async () => {
    try {
        const res = await $fetch('http://localhost:8000/api/admin/referensi?jenis=Kategori%20Agama', {
            headers: { Authorization: `Bearer ${token.value}` }
        })
        if (res.success) {
            agamaOptions.value = res.data
        }
    } catch (err) {
        console.error('Gagal memuat daftar agama:', err)
    }
}

onMounted(() => {
  fetchData()
  fetchAgamaOptions()
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #94a3b8; }
</style>
