<template>
  <div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
      <!-- Header -->
      <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h3 class="text-lg font-black text-slate-800">Biodata Diri</h3>
          <p class="text-sm font-semibold text-slate-500 mt-1">Lengkapi biodata kamu untuk keperluan administrasi sekolah.</p>
        </div>
        
        <!-- Tab Switcher -->
        <div class="flex items-center bg-slate-200 p-1 rounded-lg">
          <button 
            @click="activeTab = 'view'"
            :class="[
              'px-4 py-2 text-sm font-bold rounded-md transition-all',
              activeTab === 'view' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'
            ]"
          >
            Lihat Biodata
          </button>
          <button 
            @click="activeTab = 'edit'"
            :class="[
              'px-4 py-2 text-sm font-bold rounded-md transition-all',
              activeTab === 'edit' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'
            ]"
          >
            Edit Biodata
          </button>
        </div>
      </div>
      
      <!-- Loading -->
      <div v-if="isLoading" class="p-12 flex justify-center">
        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-emerald-600"></div>
      </div>
      
      <div v-else-if="formData" class="p-6">

        <!-- TAB: VIEW BIODATA -->
        <div v-if="activeTab === 'view'" class="space-y-8 animate-fadeIn">
          <!-- Data Diri Siswa (Read-only) -->
          <div>
            <h4 class="text-base font-black text-slate-700 border-b pb-2 mb-4 border-emerald-100 flex items-center">
              <span class="bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded text-xs mr-2">I</span> Data Diri Siswa
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
            <h4 class="text-base font-black text-slate-700 border-b pb-2 mb-4 border-emerald-100 flex items-center">
              <span class="bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded text-xs mr-2">II</span> Data Orang Tua / Wali
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

              <div class="bg-emerald-50/50 p-4 rounded-xl border border-emerald-100">
                <h5 class="text-sm font-black text-emerald-800 mb-3 border-b border-emerald-200 pb-1">Identitas Wali</h5>
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
            <h4 class="text-base font-black text-slate-700 border-b pb-2 mb-4 border-emerald-100 flex items-center">
              <span class="bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded text-xs mr-2">III</span> Data Praktik Kerja Lapangan (PKL)
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
            <h4 class="text-base font-black text-slate-700 border-b pb-2 mb-4 border-emerald-100 flex items-center">
              <span class="bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded text-xs mr-2">I</span> Edit Data Diri Siswa
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              
              <div class="col-span-full md:col-span-2 lg:col-span-3">
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Nama Lengkap</label>
                <input type="text" v-model="formData.nama_lengkap" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm font-semibold bg-white" placeholder="Sesuai Ijazah SMP">
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
                <select v-model="formData.jenis_kelamin" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm font-semibold bg-white">
                  <option value="">-- Pilih --</option>
                  <option value="L">Laki-laki (L)</option>
                  <option value="P">Perempuan (P)</option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Tempat Lahir</label>
                <input type="text" v-model="formData.tempat_lahir" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm font-semibold bg-white" placeholder="Contoh: Jakarta">
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Tanggal Lahir</label>
                <input type="date" v-model="formData.tanggal_lahir" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm font-semibold bg-white">
              </div>

              <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Agama</label>
                <select v-model="formData.agama" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm font-semibold bg-white">
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
                <textarea v-model="formData.alamat" rows="2" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm font-semibold bg-white" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan"></textarea>
              </div>
              
              <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">No. HP / WA</label>
                <input type="text" v-model="formData.no_hp" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm font-semibold bg-white" placeholder="Contoh: 08123456789">
              </div>
              
              <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Anak Ke</label>
                <input type="number" v-model="formData.anak_ke" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm font-semibold bg-white">
              </div>
              
              <div>
                <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Status Keluarga</label>
                <select v-model="formData.status_keluarga" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all text-sm font-semibold bg-white">
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
            <h4 class="text-base font-black text-slate-700 border-b pb-2 mb-4 border-emerald-100 flex items-center mt-6">
              <span class="bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded text-xs mr-2">II</span> Edit Data Orang Tua / Wali
            </h4>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 md:grid-cols-3 gap-6">
              <!-- Ayah -->
              <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 space-y-4">
                <h5 class="font-bold text-slate-700 text-sm mb-2 border-b border-slate-200 pb-2">Identitas Ayah</h5>
                
                <div>
                  <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Nama Ayah</label>
                  <input type="text" v-model="formData.nama_ayah" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-emerald-500 outline-none font-semibold text-sm bg-white">
                </div>
                
                <div>
                  <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Pekerjaan</label>
                  <input type="text" v-model="formData.pekerjaan_ayah" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-emerald-500 outline-none font-semibold text-sm bg-white">
                </div>
                
                <div>
                  <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">No. HP</label>
                  <input type="text" v-model="formData.no_hp_ayah" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-emerald-500 outline-none font-semibold text-sm bg-white">
                </div>
              </div>

              <!-- Ibu -->
              <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 space-y-4">
                <h5 class="font-bold text-slate-700 text-sm mb-2 border-b border-slate-200 pb-2">Identitas Ibu</h5>
                
                <div>
                  <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Nama Ibu</label>
                  <input type="text" v-model="formData.nama_ibu" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-emerald-500 outline-none font-semibold text-sm bg-white">
                </div>
                
                <div>
                  <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Pekerjaan</label>
                  <input type="text" v-model="formData.pekerjaan_ibu" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-emerald-500 outline-none font-semibold text-sm bg-white">
                </div>
                
                <div>
                  <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">No. HP</label>
                  <input type="text" v-model="formData.no_hp_ibu" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-emerald-500 outline-none font-semibold text-sm bg-white">
                </div>
              </div>

              <!-- Wali -->
              <div class="bg-slate-50 p-5 rounded-xl border border-slate-200 space-y-4">
                <h5 class="font-bold text-slate-700 text-sm mb-2 border-b border-slate-200 pb-2">Identitas Wali</h5>
                
                <div>
                  <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Nama Wali</label>
                  <input type="text" v-model="formData.nama_wali" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-emerald-500 outline-none font-semibold text-sm bg-white">
                </div>
                
                <div>
                  <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Pekerjaan</label>
                  <input type="text" v-model="formData.pekerjaan_wali" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-emerald-500 outline-none font-semibold text-sm bg-white">
                </div>
                
                <div>
                  <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">No. HP</label>
                  <input type="text" v-model="formData.no_hp_wali" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-emerald-500 outline-none font-semibold text-sm bg-white">
                </div>

                <div>
                  <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Alamat Wali</label>
                  <textarea v-model="formData.alamat_wali" rows="2" class="w-full px-3 py-2 rounded-lg border border-slate-300 focus:border-emerald-500 outline-none font-semibold text-sm bg-white"></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- Bagian III: Data PKL -->
          <div>
            <h4 class="text-base font-black text-slate-700 border-b pb-2 mb-4 border-emerald-100 flex items-center mt-6">
              <span class="bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded text-xs mr-2">III</span> Edit Data PKL
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-xl border border-slate-200">
              <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Tempat / Mitra PKL</label>
                <input type="text" v-model="formData.tempat_pkl" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-emerald-500 outline-none font-semibold text-sm bg-white" placeholder="Contoh: PT. ABC">
              </div>
              
              <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Alamat PKL</label>
                <input type="text" v-model="formData.alamat_pkl" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-emerald-500 outline-none font-semibold text-sm bg-white">
              </div>
              
              <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Tanggal Mulai PKL</label>
                <input type="date" v-model="formData.tgl_mulai_pkl" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-emerald-500 outline-none font-semibold text-sm bg-white">
              </div>
              
              <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wider">Tanggal Selesai PKL</label>
                <input type="date" v-model="formData.tgl_selesai_pkl" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-emerald-500 outline-none font-semibold text-sm bg-white">
              </div>
            </div>
          </div>
          
          <!-- Submit Button at bottom -->
          <div class="pt-6 border-t border-slate-200 flex justify-end">
            <button 
              type="submit"
              :disabled="isSaving"
              class="px-8 py-3 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 transition-colors shadow-sm disabled:opacity-50 flex items-center"
            >
              <span v-if="isSaving" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
              {{ isSaving ? 'Menyimpan...' : 'Simpan Semua Biodata' }}
            </button>
          </div>
        </form>
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
  layout: 'siswa',
  middleware: 'siswa',
  title: 'Biodata Diri'
})

const activeTab = ref('view') // 'view' or 'edit'
const formData = ref(null)
const isLoading = ref(true)
const isSaving = ref(false)

const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('success')

const displayToast = (msg, type = 'success') => {
    toastMessage.value = msg
    toastType.value = type
    showToast.value = true
    setTimeout(() => { showToast.value = false }, 3500)
}

const fetchBiodata = async () => {
  isLoading.value = true
  try {
    const token = useCookie('auth_token').value
    const res = await $fetch('http://localhost:8000/api/siswa/biodata', {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })

    if (res.success) {
      formData.value = res.data
    }
  } catch (error) {
    console.error('Error fetching biodata:', error)
    displayToast('Gagal memuat biodata.', 'error')
  } finally {
    isLoading.value = false
  }
}

const saveBiodata = async () => {
  if (!formData.value) return
  
  isSaving.value = true
  
  try {
    const token = useCookie('auth_token').value
    const res = await $fetch(`http://localhost:8000/api/siswa/biodata`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
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

onMounted(() => {
  fetchBiodata()
})
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
