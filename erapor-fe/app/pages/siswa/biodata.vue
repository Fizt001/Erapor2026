<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all hidden xl:flex">
        
        <div class="p-6 shrink-0 relative z-10">
          <div class="bg-gradient-to-r from-indigo-600 to-violet-700 rounded-2xl p-5 border border-indigo-500 shadow-sm relative overflow-hidden flex items-center gap-4">
            <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0 relative z-10">🧑‍🎓</div>
            <div class="relative z-10">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">Biodata Diri</h3>
                <p class="text-[10px] text-indigo-100 font-semibold uppercase mt-0.5">Identitas Siswa</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-20 text-white">
              <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg>
            </div>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-2 custom-scrollbar">
            <button 
                @click="activeTab = 'view'"
                type="button" 
                class="w-full flex items-center gap-4 p-4 rounded-2xl transition-all duration-300 text-left relative group overflow-hidden border"
                :class="activeTab === 'view' ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'bg-white border-slate-100 text-slate-600 hover:bg-slate-50 hover:border-slate-200'"
            >
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl transition-all duration-300 shrink-0 shadow-sm"
                     :class="activeTab === 'view' ? 'bg-white text-indigo-600' : 'bg-slate-100 text-slate-400 group-hover:bg-white group-hover:text-indigo-500'">
                    📄
                </div>
                <div class="overflow-hidden">
                    <p class="font-bold text-xs uppercase tracking-widest truncate">Lihat Biodata</p>
                    <p class="text-[10px] font-semibold truncate mt-0.5" :class="activeTab === 'view' ? 'text-indigo-400' : 'text-slate-400'">Tampilkan Data Diri</p>
                </div>
            </button>
            
            <button 
                @click="activeTab = 'edit'"
                type="button" 
                class="w-full flex items-center gap-4 p-4 rounded-2xl transition-all duration-300 text-left relative group overflow-hidden border"
                :class="activeTab === 'edit' ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'bg-white border-slate-100 text-slate-600 hover:bg-slate-50 hover:border-slate-200'"
            >
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl transition-all duration-300 shrink-0 shadow-sm"
                     :class="activeTab === 'edit' ? 'bg-white text-indigo-600' : 'bg-slate-100 text-slate-400 group-hover:bg-white group-hover:text-indigo-500'">
                    ✍️
                </div>
                <div class="overflow-hidden">
                    <p class="font-bold text-xs uppercase tracking-widest truncate">Edit Biodata</p>
                    <p class="text-[10px] font-semibold truncate mt-0.5" :class="activeTab === 'edit' ? 'text-indigo-400' : 'text-slate-400'">Perbarui Informasi</p>
                </div>
            </button>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0">
        <div class="p-6 lg:p-8 max-w-5xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            
            <!-- Header -->
            <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between sticky top-0 bg-white/80 backdrop-blur-xl z-20 gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-400 to-indigo-600 shadow-sm flex items-center justify-center text-2xl text-white">
                        {{ activeTab === 'view' ? '📄' : '✍️' }}
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-indigo-700">{{ activeTab === 'view' ? 'Informasi Biodata' : 'Perbarui Biodata' }}</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Kelengkapan Administrasi Sekolah</p>
                    </div>
                </div>
                
                <!-- Tab Switcher (Mobile Only) -->
                <div class="flex xl:hidden items-center bg-slate-100 p-1 rounded-xl">
                  <button @click="activeTab = 'view'" :class="['px-4 py-2 text-xs font-bold rounded-lg transition-all', activeTab === 'view' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500']">Lihat</button>
                  <button @click="activeTab = 'edit'" :class="['px-4 py-2 text-xs font-bold rounded-lg transition-all', activeTab === 'edit' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500']">Edit</button>
                </div>
            </div>

            <!-- Loading -->
            <div v-if="isLoading" class="flex-1 flex justify-center items-center">
              <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
            </div>

            <div v-else-if="formData" class="flex-1 overflow-y-auto custom-scrollbar p-6 md:p-10 relative z-0">
              <!-- TAB: VIEW BIODATA -->
              <div v-if="activeTab === 'view'" class="space-y-8 animate-fadeIn">
          <!-- Data Diri Siswa (Read-only) -->
          <div>
            <h4 class="text-base font-black text-slate-700 border-b pb-2 mb-4 border-indigo-100 flex items-center">
              <span class="bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded text-xs mr-2">I</span> Data Diri Siswa
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
              <div class="flex justify-between border-b border-slate-100 pb-2">
                <span class="text-sm font-bold text-slate-500">Nama Lengkap</span>
                <span class="text-sm font-bold text-slate-800">{{ formData.nama_lengkap || '-' }}</span>
              </div>
              <div class="flex justify-between border-b border-slate-100 pb-2">
                <span class="text-sm font-bold text-slate-500">NIS / NISN</span>
                <span class="text-sm font-bold text-slate-800">{{ formData.nis || '-' }} / {{ formData.nisn || '-' }}</span>
              </div>
              <div class="flex justify-between border-b border-slate-100 pb-2">
                <span class="text-sm font-bold text-slate-500">Jenis Kelamin</span>
                <span class="text-sm font-bold text-slate-800">{{ formData.jenis_kelamin === 'L' ? 'Laki-laki' : (formData.jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</span>
              </div>
              <div class="flex justify-between border-b border-slate-100 pb-2">
                <span class="text-sm font-bold text-slate-500">Tempat, Tanggal Lahir</span>
                <span class="text-sm font-bold text-slate-800">{{ formData.tempat_lahir || '-' }}, {{ formData.tanggal_lahir || '-' }}</span>
              </div>
              <div class="flex justify-between border-b border-slate-100 pb-2">
                <span class="text-sm font-bold text-slate-500">Agama</span>
                <span class="text-sm font-bold text-slate-800">{{ formData.agama || '-' }}</span>
              </div>
              <div class="flex justify-between border-b border-slate-100 pb-2">
                <span class="text-sm font-bold text-slate-500">No. HP / WA</span>
                <span class="text-sm font-bold text-slate-800">{{ formData.no_hp || '-' }}</span>
              </div>
              <div class="flex justify-between border-b border-slate-100 pb-2">
                <span class="text-sm font-bold text-slate-500">Anak Ke</span>
                <span class="text-sm font-bold text-slate-800">{{ formData.anak_ke || '-' }}</span>
              </div>
              <div class="flex justify-between border-b border-slate-100 pb-2">
                <span class="text-sm font-bold text-slate-500">Status Keluarga</span>
                <span class="text-sm font-bold text-slate-800">{{ formData.status_keluarga || '-' }}</span>
              </div>
              <div class="col-span-full pt-2">
                <span class="block text-sm font-bold text-slate-500 mb-1">Alamat Lengkap</span>
                <p class="text-sm font-bold text-slate-800 bg-slate-50 p-3 rounded-lg border border-slate-100">{{ formData.alamat || '-' }}</p>
              </div>
            </div>
          </div>

          <!-- Data Orang Tua (Read-only) -->
          <div>
            <h4 class="text-base font-black text-slate-700 border-b pb-2 mb-4 border-indigo-100 flex items-center">
              <span class="bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded text-xs mr-2">II</span> Data Orang Tua / Wali
            </h4>
            <div class="grid grid-cols-1 lg:grid-cols-3 md:grid-cols-3 gap-6">
              <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                <h5 class="text-sm font-black text-blue-800 mb-3 border-b border-blue-200 pb-1">Identitas Ayah</h5>
                <div class="space-y-2">
                  <div class="flex justify-between">
                    <span class="text-xs font-bold text-slate-500">Nama</span>
                    <span class="text-sm font-bold text-slate-800">{{ formData.nama_ayah || '-' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-xs font-bold text-slate-500">Pekerjaan</span>
                    <span class="text-sm font-bold text-slate-800">{{ formData.pekerjaan_ayah || '-' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-xs font-bold text-slate-500">No. HP</span>
                    <span class="text-sm font-bold text-slate-800">{{ formData.no_hp_ayah || '-' }}</span>
                  </div>
                </div>
              </div>

              <div class="bg-pink-50/50 p-4 rounded-xl border border-pink-100">
                <h5 class="text-sm font-black text-pink-800 mb-3 border-b border-pink-200 pb-1">Identitas Ibu</h5>
                <div class="space-y-2">
                  <div class="flex justify-between">
                    <span class="text-xs font-bold text-slate-500">Nama</span>
                    <span class="text-sm font-bold text-slate-800">{{ formData.nama_ibu || '-' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-xs font-bold text-slate-500">Pekerjaan</span>
                    <span class="text-sm font-bold text-slate-800">{{ formData.pekerjaan_ibu || '-' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-xs font-bold text-slate-500">No. HP</span>
                    <span class="text-sm font-bold text-slate-800">{{ formData.no_hp_ibu || '-' }}</span>
                  </div>
                </div>
              </div>

              <div class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100">
                <h5 class="text-sm font-black text-indigo-800 mb-3 border-b border-indigo-200 pb-1">Identitas Wali</h5>
                <div class="space-y-2">
                  <div class="flex justify-between">
                    <span class="text-xs font-bold text-slate-500">Nama</span>
                    <span class="text-sm font-bold text-slate-800">{{ formData.nama_wali || '-' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-xs font-bold text-slate-500">Pekerjaan</span>
                    <span class="text-sm font-bold text-slate-800">{{ formData.pekerjaan_wali || '-' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-xs font-bold text-slate-500">No. HP</span>
                    <span class="text-sm font-bold text-slate-800">{{ formData.no_hp_wali || '-' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-xs font-bold text-slate-500">Alamat</span>
                    <span class="text-sm font-bold text-slate-800 text-right">{{ formData.alamat_wali || '-' }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Data PKL (Read-only) -->
          <div>
            <h4 class="text-base font-black text-slate-700 border-b pb-2 mb-4 border-indigo-100 flex items-center">
              <span class="bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded text-xs mr-2">III</span> Data Praktik Kerja Lapangan (PKL)
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 bg-slate-50 p-6 rounded-xl border border-slate-200">
              <div class="flex justify-between border-b border-slate-200 pb-2">
                <span class="text-sm font-bold text-slate-500">Tempat PKL</span>
                <span class="text-sm font-bold text-slate-800">{{ formData.tempat_pkl || '-' }}</span>
              </div>
              <div class="flex justify-between border-b border-slate-200 pb-2">
                <span class="text-sm font-bold text-slate-500">Alamat PKL</span>
                <span class="text-sm font-bold text-slate-800">{{ formData.alamat_pkl || '-' }}</span>
              </div>
              <div class="flex justify-between border-b border-slate-200 pb-2">
                <span class="text-sm font-bold text-slate-500">Tanggal Mulai</span>
                <span class="text-sm font-bold text-slate-800">{{ formData.tgl_mulai_pkl || '-' }}</span>
              </div>
              <div class="flex justify-between border-b border-slate-200 pb-2">
                <span class="text-sm font-bold text-slate-500">Tanggal Selesai</span>
                <span class="text-sm font-bold text-slate-800">{{ formData.tgl_selesai_pkl || '-' }}</span>
              </div>
            </div>
          </div>
        </div>
              
              <!-- TAB: EDIT BIODATA -->
              <form v-else-if="activeTab === 'edit'" @submit.prevent="saveBiodata" class="space-y-8 animate-fadeIn">
          
          <!-- Bagian I: Data Diri -->
          <div>
            <h4 class="text-base font-black text-slate-700 border-b pb-2 mb-4 border-indigo-100 flex items-center">
              <span class="bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded text-xs mr-2">I</span> Edit Data Diri Siswa
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              
              <div class="col-span-full md:col-span-2 lg:col-span-3">
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Nama Lengkap</label>
                <input type="text" v-model="formData.nama_lengkap" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm font-semibold bg-white" placeholder="Sesuai Ijazah SMP">
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">NIS (Nomor Induk Siswa)</label>
                <input type="text" v-model="formData.nis" disabled class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-100 text-slate-500 font-semibold outline-none transition-all text-sm cursor-not-allowed">
                <p class="text-[10px] font-bold text-slate-400 mt-1">*NIS tidak dapat diubah</p>
              </div>
              
              <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">NISN</label>
                <input type="text" v-model="formData.nisn" disabled class="w-full px-4 py-2.5 rounded-lg border border-slate-200 bg-slate-100 text-slate-500 font-semibold outline-none transition-all text-sm cursor-not-allowed">
                <p class="text-[10px] font-bold text-slate-400 mt-1">*NISN tidak dapat diubah</p>
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Jenis Kelamin</label>
                <select v-model="formData.jenis_kelamin" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm font-semibold bg-white">
                  <option value="">-- Pilih --</option>
                  <option value="L">Laki-laki (L)</option>
                  <option value="P">Perempuan (P)</option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Tempat Lahir</label>
                <input type="text" v-model="formData.tempat_lahir" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm font-semibold bg-white" placeholder="Contoh: Jakarta">
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Tanggal Lahir</label>
                <input type="date" v-model="formData.tanggal_lahir" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm font-semibold bg-white">
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Agama</label>
                <select v-model="formData.agama" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm font-semibold bg-white">
                  <option value="Islam">Islam</option>
                  <option value="Kristen">Kristen</option>
                  <option value="Katolik">Katolik</option>
                  <option value="Hindu">Hindu</option>
                  <option value="Buddha">Buddha</option>
                  <option value="Konghucu">Konghucu</option>
                </select>
              </div>
              
              <div class="col-span-full">
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Alamat Lengkap</label>
                <textarea v-model="formData.alamat" rows="2" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm font-semibold bg-white" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan"></textarea>
              </div>
              
              <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">No. HP / WA</label>
                <input type="text" v-model="formData.no_hp" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm font-semibold bg-white" placeholder="Contoh: 08123456789">
              </div>
              
              <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Anak Ke</label>
                <input type="number" v-model="formData.anak_ke" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm font-semibold bg-white">
              </div>
              
              <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Status Keluarga</label>
                <select v-model="formData.status_keluarga" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all text-sm font-semibold bg-white">
                  <option value="">-- Pilih --</option>
                  <option value="Anak Kandung">Anak Kandung</option>
                  <option value="Anak Tiri">Anak Tiri</option>
                  <option value="Anak Angkat">Anak Angkat</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Bagian II: Data Orang Tua -->
          <div>
            <h4 class="text-base font-black text-slate-700 border-b pb-2 mb-4 border-indigo-100 flex items-center mt-6">
              <span class="bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded text-xs mr-2">II</span> Edit Data Orang Tua / Wali
            </h4>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 md:grid-cols-3 gap-6">
              <!-- Ayah -->
              <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 space-y-4">
                <h5 class="font-bold text-slate-700 text-sm mb-2 border-b border-slate-200 pb-2">Identitas Ayah</h5>
                
                <div>
                  <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Nama Ayah</label>
                  <input type="text" v-model="formData.nama_ayah" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-indigo-500 outline-none font-semibold text-sm bg-white">
                </div>
                
                <div>
                  <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Pekerjaan</label>
                  <input type="text" v-model="formData.pekerjaan_ayah" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-indigo-500 outline-none font-semibold text-sm bg-white">
                </div>
                
                <div>
                  <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">No. HP</label>
                  <input type="text" v-model="formData.no_hp_ayah" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-indigo-500 outline-none font-semibold text-sm bg-white">
                </div>
              </div>

              <!-- Ibu -->
              <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 space-y-4">
                <h5 class="font-bold text-slate-700 text-sm mb-2 border-b border-slate-200 pb-2">Identitas Ibu</h5>
                
                <div>
                  <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Nama Ibu</label>
                  <input type="text" v-model="formData.nama_ibu" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-indigo-500 outline-none font-semibold text-sm bg-white">
                </div>
                
                <div>
                  <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Pekerjaan</label>
                  <input type="text" v-model="formData.pekerjaan_ibu" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-indigo-500 outline-none font-semibold text-sm bg-white">
                </div>
                
                <div>
                  <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">No. HP</label>
                  <input type="text" v-model="formData.no_hp_ibu" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-indigo-500 outline-none font-semibold text-sm bg-white">
                </div>
              </div>

              <!-- Wali -->
              <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 space-y-4">
                <h5 class="font-bold text-slate-700 text-sm mb-2 border-b border-slate-200 pb-2">Identitas Wali</h5>
                
                <div>
                  <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Nama Wali</label>
                  <input type="text" v-model="formData.nama_wali" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-indigo-500 outline-none font-semibold text-sm bg-white">
                </div>
                
                <div>
                  <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Pekerjaan</label>
                  <input type="text" v-model="formData.pekerjaan_wali" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-indigo-500 outline-none font-semibold text-sm bg-white">
                </div>
                
                <div>
                  <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">No. HP</label>
                  <input type="text" v-model="formData.no_hp_wali" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-indigo-500 outline-none font-semibold text-sm bg-white">
                </div>

                <div>
                  <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Alamat Wali</label>
                  <textarea v-model="formData.alamat_wali" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-indigo-500 outline-none font-semibold text-sm bg-white"></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- Bagian III: Data PKL -->
          <div>
            <h4 class="text-base font-black text-slate-700 border-b pb-2 mb-4 border-indigo-100 flex items-center mt-6">
              <span class="bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded text-xs mr-2">III</span> Edit Data PKL
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-xl border border-slate-200">
              <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Tempat / Mitra PKL</label>
                <input type="text" v-model="formData.tempat_pkl" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 outline-none font-semibold text-sm bg-white" placeholder="Contoh: PT. ABC">
              </div>
              
              <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Alamat PKL</label>
                <input type="text" v-model="formData.alamat_pkl" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 outline-none font-semibold text-sm bg-white">
              </div>
              
              <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Tanggal Mulai PKL</label>
                <input type="date" v-model="formData.tgl_mulai_pkl" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 outline-none font-semibold text-sm bg-white">
              </div>
              
              <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Tanggal Selesai PKL</label>
                <input type="date" v-model="formData.tgl_selesai_pkl" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-indigo-500 outline-none font-semibold text-sm bg-white">
              </div>
            </div>
          </div>
          
          <!-- Submit Button at bottom -->
          <div class="pt-6 border-t border-slate-200 flex justify-end">
            <button 
              type="submit"
              :disabled="isSaving"
              class="px-8 py-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition-colors shadow-sm disabled:opacity-50 flex items-center"
            >
              <span v-if="isSaving" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
              {{ isSaving ? 'Menyimpan...' : 'Simpan Semua Biodata' }}
            </button>
          </div>
        </form>
            </div>
          </div>
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
import { ref, watch } from 'vue'

definePageMeta({
  layout: 'siswa',
  middleware: 'siswa',
  title: 'Biodata Diri'
})

const activeTab = ref('view') // 'view' or 'edit'
const formData = ref(null)
const isSaving = ref(false)

const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('success')

const displayToast = (msg, type = 'success') => {
    useSwal().fire({
        title: msg,
        icon: type === 'error' ? 'error' : 'success',
        confirmButtonText: 'OK',
        confirmButtonColor: '#4f46e5'
    })
}

const tokenCookie = useCookie('auth_token')
const { data: response, pending: isLoading } = await useFetch('http://localhost:8000/api/siswa/biodata', {
  headers: {
    'Authorization': `Bearer ${tokenCookie.value}`
  }
})

watch(response, (newVal) => {
  if (newVal?.success && newVal?.data) {
    formData.value = JSON.parse(JSON.stringify(newVal.data))
  }
}, { immediate: true })

const saveBiodata = async () => {
  if (!formData.value) return
  
  isSaving.value = true
  
  try {
    const res = await $fetch(`http://localhost:8000/api/siswa/biodata`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${tokenCookie.value}`,
        'Content-Type': 'application/json'
      },
      body: formData.value
    })

    if (res.success) {
      displayToast(res.message || 'Biodata berhasil diperbarui!', 'success')
      
      // Update profile cookie to reflect name changes
      const profile = useCookie('user_profile')
      if (profile.value) {
        let profData = typeof profile.value === 'string' ? JSON.parse(profile.value) : profile.value
        profData.name = formData.value.nama_lengkap
        profile.value = JSON.stringify(profData)
      }
      
      // Update local useFetch cache so the view updates
      if (response.value && response.value.data) {
          response.value.data = JSON.parse(JSON.stringify(formData.value))
      }

      // Auto switch back to view mode and scroll top
      activeTab.value = 'view'
      window.scrollTo({ top: 0, behavior: 'smooth' })
    } else {
      displayToast(res.message || 'Gagal menyimpan biodata.', 'error')
    }
  } catch (error) {
    console.error('Error saving biodata:', error)
    displayToast(error.data?.message || 'Terjadi kesalahan jaringan atau server.', 'error')
  } finally {
    isSaving.value = false
  }
}
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
  animation: fadeIn 0.3s ease-out forwards;
}

@keyframes slideUp {
  from { opacity: 0; transform: translate(-50%, 20px); }
  to { opacity: 1; transform: translate(-50%, 0); }
}
.animate-slideUp { animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
