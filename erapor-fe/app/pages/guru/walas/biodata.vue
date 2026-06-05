<template>
  <div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Biodata Siswa</h1>
        <p class="text-slate-500 text-sm mt-1">
          Kelengkapan Data Profil & Validasi Rapor Siswa.
        </p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="pending" class="flex flex-col items-center justify-center py-20 bg-white rounded-2xl shadow-sm border border-slate-100">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-teal-500 mb-4"></div>
      <p class="text-slate-500 font-medium">Memuat data siswa...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 p-6 rounded-2xl border border-red-100 flex items-start">
      <div class="text-red-500 text-xl mr-4 mt-0.5">⚠️</div>
      <div>
        <h3 class="text-red-800 font-bold mb-1">Gagal Memuat Data</h3>
        <p class="text-red-600 text-sm">{{ error.message || 'Terjadi kesalahan pada server.' }}</p>
        <button @click="refreshData" class="mt-3 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 text-sm font-medium rounded-lg transition-colors">
          Coba Lagi
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
      <!-- Info Alert -->
      <div class="bg-teal-50 border-b border-teal-100 p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between">
        <div class="flex items-start">
          <span class="text-teal-500 mr-3 text-lg">💡</span>
          <div class="text-sm text-teal-800">
            <strong>Informasi:</strong> Lengkapi data profil siswa hingga mencapai 100%. Data ini sangat penting untuk kelengkapan cetak cover buku rapor.
          </div>
        </div>
        
        <!-- Indikator Informasi -->
        <div class="mt-4 sm:mt-0 flex space-x-4 text-xs font-semibold text-slate-600">
            <span class="flex items-center"><div class="w-3 h-3 rounded-full bg-teal-500 mr-1.5"></div> 100% (Lengkap)</span>
            <span class="flex items-center"><div class="w-3 h-3 rounded-full bg-amber-400 mr-1.5"></div> > 50%</span>
            <span class="flex items-center"><div class="w-3 h-3 rounded-full bg-rose-500 mr-1.5"></div> < 50%</span>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-[12px] uppercase tracking-wider">
              <th class="p-4 font-bold w-12 text-center">No</th>
              <th class="p-4 font-bold min-w-[200px]">Nama Siswa & NISN</th>
              <th class="p-4 font-bold text-center w-20">L/P</th>
              <th class="p-4 font-bold text-center min-w-[150px]">Status Kelengkapan</th>
              <th class="p-4 font-bold text-center w-28">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-sm">
            <tr v-for="(siswa, index) in students" :key="siswa.id" class="hover:bg-slate-50 transition-colors">
              <td class="p-4 text-center text-slate-400 font-medium">{{ index + 1 }}</td>
              <td class="p-4">
                <div class="font-bold text-slate-800">{{ siswa.nama_lengkap }}</div>
                <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ siswa.nisn || 'NISN Kosong' }} | {{ siswa.nis || 'NIS Kosong' }}</div>
              </td>
              <td class="p-4 text-center">
                <span class="px-2 py-1 rounded text-xs font-bold" 
                  :class="siswa.jenis_kelamin === 'L' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700'">
                  {{ siswa.jenis_kelamin }}
                </span>
              </td>
              
              <!-- Progress Bar -->
              <td class="p-4">
                <div class="flex items-center space-x-2">
                    <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                        <div class="h-2.5 rounded-full" 
                             :class="getProgressBarColor(siswa.persentase_lengkap)"
                             :style="{ width: `${siswa.persentase_lengkap}%` }">
                        </div>
                    </div>
                    <span class="text-xs font-bold w-10 text-right" :class="getProgressTextColor(siswa.persentase_lengkap)">
                        {{ siswa.persentase_lengkap }}%
                    </span>
                </div>
              </td>

              <!-- Aksi -->
              <td class="p-4 text-center">
                <button 
                    @click="openEditModal(siswa)"
                    class="px-3 py-1.5 bg-sky-50 text-sky-600 hover:bg-sky-100 font-bold rounded-lg transition-colors text-xs flex items-center justify-center w-full"
                >
                    <span class="mr-1">✏️</span> Edit
                </button>
              </td>
            </tr>
            <tr v-if="students.length === 0">
              <td colspan="5" class="p-8 text-center text-slate-500">
                Belum ada data siswa di kelas ini.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="p-4 bg-slate-50 border-t border-slate-200 text-sm text-slate-500 text-center">
        Total {{ students.length }} siswa
      </div>
    </div>

    <!-- Edit Modal -->
    <div v-if="isModalOpen" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-4xl rounded-2xl shadow-2xl flex flex-col max-h-[90vh]">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50 rounded-t-2xl">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Edit Biodata Siswa</h2>
                    <p class="text-xs text-slate-500 mt-1">{{ editForm.nama_lengkap }}</p>
                </div>
                <button @click="closeModal" class="text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
            </div>
            
            <!-- Tabs -->
            <div class="flex border-b border-slate-200 px-6 pt-4 bg-slate-50 overflow-x-auto whitespace-nowrap">
                <button @click="activeTab = 'identitas'" :class="activeTab === 'identitas' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-4 py-2 border-b-2 font-bold text-sm transition-colors">Identitas</button>
                <button @click="activeTab = 'ortu'" :class="activeTab === 'ortu' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-4 py-2 border-b-2 font-bold text-sm transition-colors">Orang Tua & Wali</button>
                <button @click="activeTab = 'sekolah'" :class="activeTab === 'sekolah' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-4 py-2 border-b-2 font-bold text-sm transition-colors">Sekolah Asal & SMK</button>
                <button @click="activeTab = 'pkl'" :class="activeTab === 'pkl' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-4 py-2 border-b-2 font-bold text-sm transition-colors">Data PKL</button>
            </div>

            <!-- Body -->
            <div class="p-6 overflow-y-auto flex-1">
                <form @submit.prevent="submitEdit">
                    <!-- Tab Identitas -->
                    <div v-show="activeTab === 'identitas'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-600 uppercase">Nama Lengkap</label>
                            <input v-model="editForm.nama_lengkap" type="text" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-slate-600 uppercase">NIS</label>
                                <input v-model="editForm.nis" type="text" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-slate-600 uppercase">NISN</label>
                                <input v-model="editForm.nisn" type="text" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-600 uppercase">Tempat Lahir</label>
                            <input v-model="editForm.tempat_lahir" type="text" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-600 uppercase">Tanggal Lahir</label>
                            <input v-model="editForm.tanggal_lahir" type="date" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-600 uppercase">Jenis Kelamin</label>
                            <select v-model="editForm.jenis_kelamin" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-600 uppercase">Agama</label>
                            <input v-model="editForm.agama" type="text" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-600 uppercase">Anak Ke / Status Keluarga</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input v-model="editForm.anak_ke" type="number" placeholder="Anak Ke" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                                <input v-model="editForm.status_keluarga" type="text" placeholder="Status (mis: Anak Kandung)" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-600 uppercase">Warga Negara</label>
                            <input v-model="editForm.warga_negara" type="text" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-600 uppercase">No HP Siswa</label>
                            <input v-model="editForm.no_hp" type="text" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>
                        <div class="sm:col-span-2 space-y-1">
                            <label class="text-xs font-semibold text-slate-600 uppercase">Alamat Lengkap Siswa</label>
                            <textarea v-model="editForm.alamat" rows="2" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none"></textarea>
                        </div>
                    </div>

                    <!-- Tab Ortu & Wali -->
                    <div v-show="activeTab === 'ortu'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4">
                        <!-- Data Ayah -->
                        <div class="space-y-4 p-4 border border-slate-200 rounded-xl bg-slate-50/50">
                            <h3 class="text-sm font-bold text-slate-800 border-b pb-2 mb-4">Data Ayah</h3>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-500 uppercase">Nama Ayah</label>
                                <input v-model="editForm.nama_ayah" type="text" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-sm">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-500 uppercase">TTL Ayah</label>
                                <input v-model="editForm.ttl_ayah" type="text" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-sm">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-500 uppercase">Pekerjaan Ayah</label>
                                <input v-model="editForm.pekerjaan_ayah" type="text" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-sm">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-500 uppercase">No HP Ayah</label>
                                <input v-model="editForm.no_hp_ayah" type="text" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-sm">
                            </div>
                        </div>

                        <!-- Data Ibu -->
                        <div class="space-y-4 p-4 border border-slate-200 rounded-xl bg-slate-50/50">
                            <h3 class="text-sm font-bold text-slate-800 border-b pb-2 mb-4">Data Ibu</h3>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-500 uppercase">Nama Ibu</label>
                                <input v-model="editForm.nama_ibu" type="text" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-sm">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-500 uppercase">TTL Ibu</label>
                                <input v-model="editForm.ttl_ibu" type="text" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-sm">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-500 uppercase">Pekerjaan Ibu</label>
                                <input v-model="editForm.pekerjaan_ibu" type="text" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-sm">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-500 uppercase">No HP Ibu</label>
                                <input v-model="editForm.no_hp_ibu" type="text" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-sm">
                            </div>
                        </div>

                        <!-- Data Wali -->
                        <div class="space-y-4 p-4 border border-slate-200 rounded-xl bg-slate-50/50">
                            <h3 class="text-sm font-bold text-slate-800 border-b pb-2 mb-4">Data Wali</h3>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-500 uppercase">Nama Wali</label>
                                <input v-model="editForm.nama_wali" type="text" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-sm">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-500 uppercase">Pekerjaan Wali</label>
                                <input v-model="editForm.pekerjaan_wali" type="text" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-sm">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-500 uppercase">No HP Wali</label>
                                <input v-model="editForm.no_hp_wali" type="text" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-sm">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-500 uppercase">Alamat Wali</label>
                                <textarea v-model="editForm.alamat_wali" rows="1" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-sm"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Sekolah & SMK -->
                    <div v-show="activeTab === 'sekolah'" class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <!-- Sekolah Asal -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-bold text-teal-700 uppercase tracking-widest border-b-2 border-teal-100 pb-1">Sekolah Asal (SMP/MTs)</h3>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-600 uppercase">Asal Sekolah</label>
                                <input v-model="editForm.asal_sekolah" type="text" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-600 uppercase">Alamat Sekolah Asal</label>
                                <textarea v-model="editForm.alamat_sekolah_asal" rows="2" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm"></textarea>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-600 uppercase">No STTB SMP</label>
                                <input v-model="editForm.no_sttb_smp" type="text" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-600 uppercase">Tanggal STTB SMP</label>
                                <input v-model="editForm.tgl_sttb_smp" type="date" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                        </div>
                        
                        <!-- Data SMK -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-bold text-amber-600 uppercase tracking-widest border-b-2 border-amber-100 pb-1">Data SMK Saat Ini / Keluar</h3>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="space-y-1">
                                    <label class="text-[11px] font-semibold text-slate-600 uppercase">Tanggal Masuk</label>
                                    <input v-model="editForm.tanggal_masuk" type="date" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] font-semibold text-slate-600 uppercase">Kelas Masuk</label>
                                    <input v-model="editForm.kelas_masuk" type="text" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm">
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-600 uppercase">Tanggal Keluar / Pindah</label>
                                <input v-model="editForm.tanggal_keluar" type="date" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-600 uppercase">Alasan Keluar / Pindah</label>
                                <textarea v-model="editForm.alasan_keluar" rows="2" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm"></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="space-y-1">
                                    <label class="text-[11px] font-semibold text-slate-600 uppercase">No STTB SMK</label>
                                    <input v-model="editForm.no_sttb_smk" type="text" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[11px] font-semibold text-slate-600 uppercase">Tanggal STTB SMK</label>
                                    <input v-model="editForm.tgl_sttb_smk" type="date" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab PKL -->
                    <div v-show="activeTab === 'pkl'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-600 uppercase">Tempat PKL</label>
                            <input v-model="editForm.tempat_pkl" type="text" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-600 uppercase">Mulai PKL</label>
                                <input v-model="editForm.tgl_mulai_pkl" type="date" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[11px] font-semibold text-slate-600 uppercase">Selesai PKL</label>
                                <input v-model="editForm.tgl_selesai_pkl" type="date" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                        </div>
                        <div class="sm:col-span-2 space-y-1">
                            <label class="text-xs font-semibold text-slate-600 uppercase">Alamat PKL</label>
                            <textarea v-model="editForm.alamat_pkl" rows="2" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm"></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 rounded-b-2xl flex justify-end gap-3">
                <button @click="closeModal" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg text-sm font-bold transition-colors">Batal</button>
                <button 
                    @click="submitEdit" 
                    :disabled="isSaving"
                    class="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-bold shadow-sm transition-colors flex items-center disabled:opacity-50"
                >
                    <span v-if="isSaving" class="mr-2 animate-spin">⏳</span>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
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
  layout: 'guru',
  middleware: 'guru',
  title: 'Biodata Siswa'
})

const token = useCookie('auth_token')
const pending = ref(true)
const error = ref(null)
const students = ref([])

// Form Modal State
const isModalOpen = ref(false)
const isSaving = ref(false)
const activeTab = ref('identitas')
const editForm = ref({})

const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('success')

const displayToast = (msg, type = 'success') => {
    toastMessage.value = msg
    toastType.value = type
    showToast.value = true
    setTimeout(() => { showToast.value = false }, 3500)
}

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
    // Clone properties
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
            // Update in local state
            const idx = students.value.findIndex(s => s.id === payload.id)
            if (idx !== -1) {
                // Re-calculate completion internally or just re-fetch
                fetchData()
            }
            closeModal()
            displayToast(res.message || 'Biodata berhasil diperbarui!', 'success')
        } else {
            throw new Error(res.message)
        }
    } catch (err) {
        displayToast(err.data?.message || err.message || 'Terjadi kesalahan saat menyimpan data.', 'error')
    } finally {
        isSaving.value = false
    }
}

onMounted(() => {
  fetchData()
})
</script>

<style scoped>
@keyframes slideUp {
  from { opacity: 0; transform: translate(-50%, 20px); }
  to { opacity: 1; transform: translate(-50%, 0); }
}
.animate-slideUp { animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
