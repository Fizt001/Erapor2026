<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    
<!-- Layout 2 Panel Dock & Flow -->
    <form @submit.prevent="saveData" class="flex-1 flex overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-1.5 flex gap-1.5 z-20 shadow-sm">
        <button v-for="tab in tabs" :key="'mob-'+tab.id" type="button" @click="activeTab = tab.id"
          :class="activeTab === tab.id ? 'bg-emerald-500 text-white shadow-sm ring-1 ring-emerald-400 ring-offset-1' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'"
          class="flex-1 rounded-lg flex flex-col items-center justify-center py-2 px-0.5 transition-all active:scale-95">
          <span class="text-lg mb-0.5 transition-transform" :class="activeTab === tab.id ? 'scale-110' : ''">{{ tab.icon }}</span>
          <span class="text-[8px] font-black uppercase tracking-wider text-center leading-none">{{ tab.shortTitle }}</span>
        </button>
      </div>

      <!-- Panel Dock Kiri -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all hidden xl:flex']">
        
        <div class="p-6 shrink-0">
          <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl p-5 border border-emerald-500 shadow-sm relative overflow-hidden flex items-center gap-4">
            <div class="w-12 h-12 flex items-center justify-center text-3xl shrink-0 relative z-10">⚙️</div>
            <div class="relative z-10">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">Pengaturan Profil</h3>
                <p class="text-[10px] text-emerald-100 font-semibold uppercase mt-0.5">Navigasi Kategori</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-20 text-white">
              <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M4 5a2 2 0 012-2h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm2 0v14h12V5H6zm2 3a1 1 0 011-1h6a1 1 0 110 2H9a1 1 0 01-1-1zm0 4a1 1 0 011-1h6a1 1 0 110 2H9a1 1 0 01-1-1zm0 4a1 1 0 011-1h6a1 1 0 110 2H9a1 1 0 01-1-1z"></path></svg>
            </div>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-2 custom-scrollbar">
            <button 
                v-for="tab in tabs" :key="tab.id" 
                type="button" 
                @click="activeTab = tab.id"
                class="w-full flex items-center gap-4 p-4 rounded-2xl transition-all duration-300 text-left relative group overflow-hidden border"
                :class="activeTab === tab.id ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-white border-transparent text-slate-500 hover:bg-slate-50 hover:border-slate-200 hover:text-slate-800'"
            >
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl transition-all duration-300 shrink-0"
                    :class="activeTab === tab.id ? 'bg-white text-emerald-600 shadow-sm' : 'bg-slate-100 text-slate-400 group-hover:bg-white'">
                    {{ tab.icon }}
                </div>
                <div class="overflow-hidden">
                    <p class="font-bold text-xs uppercase tracking-widest truncate">{{ tab.title }}</p>
                    <p class="text-[10px] font-semibold text-slate-400 truncate mt-0.5">{{ tab.subtitle }}</p>
                </div>
            </button>
        </div>

        <!-- Submit Button Dock -->
        <div class="p-6 bg-slate-50 border-t border-slate-200 shrink-0">
            <button type="submit" :disabled="isSaving" class="w-full py-3.5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-bold rounded-2xl shadow-lg shadow-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/40 hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-2 uppercase tracking-widest text-xs">
                <span v-if="isSaving" class="animate-spin text-lg">⏳</span>
                <span v-else class="text-lg">💾</span>
                <span>Simpan Profil</span>
            </button>
        </div>

      </div>

      <!-- Panel Flow Kanan -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative', !isDesktop ? 'pt-[64px]' : '']">
        <div class="p-2 sm:p-6 lg:p-8 max-w-5xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-xl sm:rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            <!-- Table Header & Filters -->
            <div class="px-4 py-3 sm:px-6 sm:py-5 border-b border-slate-200 flex justify-between items-center gap-3 lg:gap-4 shrink-0 z-10 bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-xl lg:rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 shadow-sm flex items-center justify-center text-xl lg:text-2xl text-white shrink-0">
                        {{ currentTab.icon }}
                    </div>
                    <div>
                        <h3 class="text-xs lg:text-sm font-black uppercase tracking-widest text-emerald-700 leading-tight">{{ currentTab.title }}</h3>
                        <p class="text-[9px] lg:text-[10px] font-bold text-slate-400 uppercase mt-0.5 hidden sm:block">{{ currentTab.subtitle }}</p>
                    </div>
                </div>
                
                <div class="xl:hidden shrink-0">
                    <button type="submit" :disabled="isSaving" class="px-4 py-2 bg-emerald-500 text-white font-bold rounded-xl shadow-md disabled:opacity-70 flex items-center justify-center gap-1.5 text-[10px] uppercase tracking-widest transition-all">
                        <span v-if="isSaving" class="animate-spin">⏳</span>
                        <span v-else>💾</span>
                        <span class="hidden sm:inline">Simpan</span>
                        <span class="sm:hidden">Simpan</span>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar p-4 md:p-8 bg-white">
                <div class="max-w-4xl mx-auto pb-10">
                
                <!-- 1. IDENTITAS -->
                <div v-show="activeTab === 'identitas'" class="animate-fadeIn space-y-4">
                    <div class="flex flex-col md:flex-row gap-4 items-start">
                        <div class="w-full md:w-1/2 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1 truncate">Logo Sekolah</label>
                                <div class="mt-1 flex justify-center px-4 pt-4 pb-4 border-2 border-slate-300 border-dashed rounded-2xl hover:bg-slate-50 transition-colors relative group">
                                    <div class="space-y-1 text-center">
                                        <div v-if="logoPreview" class="mx-auto h-20 w-20 mb-3 relative rounded-xl overflow-hidden bg-white shadow-sm border border-slate-200">
                                            <img :src="logoPreview" alt="Logo Preview" class="h-full w-full object-contain p-2" />
                                            <button @click.prevent="clearLogo" class="absolute top-1 right-1 bg-rose-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                        <div v-else class="mx-auto h-12 w-12 text-slate-400 mb-4 text-4xl">🏢</div>
                                        <div class="flex text-sm text-slate-600 justify-center">
                                            <label for="logo-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-emerald-600 hover:text-emerald-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-emerald-500 px-2 py-1">
                                                <span v-if="!logoPreview">Upload file</span>
                                                <span v-else>Ganti file</span>
                                                <input id="logo-upload" name="logo-upload" type="file" class="sr-only" accept="image/*" @change="handleLogoUpload">
                                            </label>
                                        </div>
                                        <p class="text-[10px] text-slate-500 mt-2 font-semibold">Maks 2MB</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1 truncate">Logo Dinas</label>
                                <div class="mt-1 flex justify-center px-4 pt-4 pb-4 border-2 border-slate-300 border-dashed rounded-2xl hover:bg-slate-50 transition-colors relative group">
                                    <div class="space-y-1 text-center">
                                        <div v-if="logoKiriPreview" class="mx-auto h-20 w-20 mb-3 relative rounded-xl overflow-hidden bg-white shadow-sm border border-slate-200">
                                            <img :src="logoKiriPreview" alt="Logo Kiri Preview" class="h-full w-full object-contain p-2" />
                                            <button @click.prevent="clearLogoKiri" class="absolute top-1 right-1 bg-rose-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                        <div v-else class="mx-auto h-12 w-12 text-slate-400 mb-4 text-4xl">🏛️</div>
                                        <div class="flex text-sm text-slate-600 justify-center">
                                            <label for="logo-kiri-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-emerald-600 hover:text-emerald-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-emerald-500 px-2 py-1">
                                                <span v-if="!logoKiriPreview">Upload file</span>
                                                <span v-else>Ganti file</span>
                                                <input id="logo-kiri-upload" name="logo-kiri-upload" type="file" class="sr-only" accept="image/*" @change="handleLogoKiriUpload">
                                            </label>
                                        </div>
                                        <p class="text-[10px] text-slate-500 mt-2 font-semibold">Kop Rapor</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-full md:w-1/2 space-y-4">
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Nama Yayasan</label>
                                <input type="text" v-model="form.nama_yayasan" class="w-full px-4 py-2.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="Misal: YAYASAN TINTA EMAS INDONESIA">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Nama Sekolah Lengkap <span class="text-rose-500">*</span></label>
                                <input type="text" v-model="form.nama_sekolah" required class="w-full px-4 py-2.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="Misal: SMK TINTA EMAS INDONESIA">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">NPSN</label>
                                    <input type="text" v-model="form.npsn" class="w-full px-4 py-2.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="Nomor Pokok Sekolah Nasional">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Akreditasi</label>
                                    <input type="text" v-model="form.akreditasi" class="w-full px-4 py-2.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="Misal: TERAKREDITASI 'A'">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 1.5 VISI & MISI -->
                <div v-show="activeTab === 'visimisi'" class="animate-fadeIn space-y-6">
                    <div class="space-y-2">
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Visi Sekolah</label>
                        <textarea v-model="form.visi" rows="3" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400 italic" placeholder="Tuliskan Visi sekolah..."></textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Misi Sekolah</label>
                        <textarea v-model="form.misi" rows="6" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="Tuliskan Misi sekolah..."></textarea>
                    </div>
                </div>

                <!-- 2. PIMPINAN -->
                <div v-show="activeTab === 'pimpinan'" class="animate-fadeIn space-y-4">
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Nama Kepala Sekolah</label>
                        <input type="text" v-model="form.nama_kepsek" class="w-full px-4 py-2.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="Masukkan nama lengkap beserta gelar">
                    </div>
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">NIP Kepala Sekolah</label>
                        <input type="text" v-model="form.nip_kepsek" class="w-full px-4 py-2.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="Nomor Induk Pegawai (Opsional)">
                    </div>
                </div>

                <!-- 3. MEDIA -->
                <div v-show="activeTab === 'media'" class="animate-fadeIn space-y-4">
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Situs Web Resmi</label>
                        <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">🌐</span>
                        <input type="url" v-model="form.website" class="w-full pl-11 pr-5 py-2.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="https://www.sekolahanda.sch.id">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Alamat Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">✉️</span>
                            <input type="email" v-model="form.email" class="w-full pl-11 pr-5 py-2.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="info@sekolahanda.sch.id">
                        </div>
                        </div>
                        <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Nomor Telepon</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">📞</span>
                            <input type="text" v-model="form.telepon" class="w-full pl-11 pr-5 py-2.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="(021) 1234567">
                        </div>
                        </div>
                    </div>
                </div>

                <!-- 4. LOKASI -->
                <div v-show="activeTab === 'lokasi'" class="animate-fadeIn space-y-4">
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Alamat Lengkap</label>
                        <textarea v-model="form.alamat" rows="3" class="w-full px-4 py-2.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400 leading-relaxed custom-scrollbar" placeholder="Tuliskan nama jalan, RT/RW, dan patokan..."></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kelurahan / Desa</label>
                        <input type="text" v-model="form.kelurahan" class="w-full px-4 py-2.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="Nama Kelurahan">
                        </div>
                        <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kecamatan</label>
                        <input type="text" v-model="form.kecamatan" class="w-full px-4 py-2.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="Nama Kecamatan">
                        </div>
                        <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kabupaten / Kota</label>
                        <input type="text" v-model="form.kota" class="w-full px-4 py-2.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="Nama Kota">
                        </div>
                        <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Provinsi</label>
                        <input type="text" v-model="form.provinsi" class="w-full px-4 py-2.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="Nama Provinsi">
                        </div>
                        <div class="md:col-span-2">
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kode Pos</label>
                        <input type="text" v-model="form.kode_pos" class="w-full md:w-1/2 px-4 py-2.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="12345">
                        </div>
                    </div>
                </div>

                <!-- 5. GALERI FOTO -->
                <div v-show="activeTab === 'foto'" class="animate-fadeIn space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        
                        <!-- Foto 1 -->
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Foto 1 (Tampil di Login)</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-2xl hover:bg-slate-50 transition-colors relative group">
                                <div class="space-y-1 text-center">
                                    <div v-if="foto1Preview" class="mx-auto h-32 w-full mb-4 relative rounded-xl overflow-hidden bg-white shadow-sm border border-slate-200">
                                        <img :src="foto1Preview" alt="Foto 1 Preview" class="h-full w-full object-cover" />
                                        <button @click.prevent="clearFoto1" class="absolute top-1 right-1 bg-rose-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                    <div v-else class="mx-auto h-12 w-12 text-slate-400 mb-4 text-4xl">📸</div>
                                    <div class="flex text-sm text-slate-600 justify-center">
                                        <label for="foto1-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-emerald-600 hover:text-emerald-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-emerald-500 px-2 py-1">
                                            <span v-if="!foto1Preview">Upload file</span>
                                            <span v-else>Ganti file</span>
                                            <input id="foto1-upload" name="foto1-upload" type="file" class="sr-only" accept="image/*" @change="handleFoto1Upload">
                                        </label>
                                    </div>
                                    <p class="text-[10px] font-bold text-slate-400">PNG, JPG up to 2MB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Foto 2 -->
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Foto 2</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-2xl hover:bg-slate-50 transition-colors relative group">
                                <div class="space-y-1 text-center">
                                    <div v-if="foto2Preview" class="mx-auto h-32 w-full mb-4 relative rounded-xl overflow-hidden bg-white shadow-sm border border-slate-200">
                                        <img :src="foto2Preview" alt="Foto 2 Preview" class="h-full w-full object-cover" />
                                        <button @click.prevent="clearFoto2" class="absolute top-1 right-1 bg-rose-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                    <div v-else class="mx-auto h-12 w-12 text-slate-400 mb-4 text-4xl">🖼️</div>
                                    <div class="flex text-sm text-slate-600 justify-center">
                                        <label for="foto2-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-emerald-600 hover:text-emerald-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-emerald-500 px-2 py-1">
                                            <span v-if="!foto2Preview">Upload file</span>
                                            <span v-else>Ganti file</span>
                                            <input id="foto2-upload" name="foto2-upload" type="file" class="sr-only" accept="image/*" @change="handleFoto2Upload">
                                        </label>
                                    </div>
                                    <p class="text-[10px] font-bold text-slate-400">PNG, JPG up to 2MB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Foto 3 -->
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Foto 3</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-2xl hover:bg-slate-50 transition-colors relative group">
                                <div class="space-y-1 text-center">
                                    <div v-if="foto3Preview" class="mx-auto h-32 w-full mb-4 relative rounded-xl overflow-hidden bg-white shadow-sm border border-slate-200">
                                        <img :src="foto3Preview" alt="Foto 3 Preview" class="h-full w-full object-cover" />
                                        <button @click.prevent="clearFoto3" class="absolute top-1 right-1 bg-rose-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                    <div v-else class="mx-auto h-12 w-12 text-slate-400 mb-4 text-4xl">🖼️</div>
                                    <div class="flex text-sm text-slate-600 justify-center">
                                        <label for="foto3-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-emerald-600 hover:text-emerald-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-emerald-500 px-2 py-1">
                                            <span v-if="!foto3Preview">Upload file</span>
                                            <span v-else>Ganti file</span>
                                            <input id="foto3-upload" name="foto3-upload" type="file" class="sr-only" accept="image/*" @change="handleFoto3Upload">
                                        </label>
                                    </div>
                                    <p class="text-[10px] font-bold text-slate-400">PNG, JPG up to 2MB</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
          </div>
        </div>
      </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const runtimeConfig = useRuntimeConfig()
const apiUrl = import.meta.env.VITE_API_BASE_URL || runtimeConfig.public.apiBase || 'http://localhost:8000'

const getImageUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http') || path.startsWith('blob:')) return path
  return `${apiUrl}/${path.replace(/^\//, '')}`
}

definePageMeta({
  layout: 'admin',
  middleware: 'admin',
  title: 'Data Sekolah'
})

const windowWidth = ref(1024)
const isDesktop = computed(() => windowWidth.value >= 1280) // xl breakpoint

// === TAB MANAGEMENT ===
const activeTab = ref('identitas')
const tabs = [
  { id: 'identitas', title: 'Identitas Sekolah', shortTitle: 'Profil', subtitle: 'Informasi Dasar', icon: '🏫' },
  { id: 'visimisi', title: 'Visi & Misi', shortTitle: 'Visi', subtitle: 'Arah Tujuan', icon: '🎯' },
  { id: 'pimpinan', title: 'Pimpinan Lembaga', shortTitle: 'Kepsek', subtitle: 'Kepala Sekolah', icon: '👨‍💼' },
  { id: 'media', title: 'Media & Digital', shortTitle: 'Sosmed', subtitle: 'Kontak & Website', icon: '🌐' },
  { id: 'lokasi', title: 'Lokasi Geografis', shortTitle: 'Lokasi', subtitle: 'Alamat & Wilayah', icon: '📍' },
  { id: 'foto', title: 'Galeri Sekolah', shortTitle: 'Galeri', subtitle: 'Foto & Dokumentasi', icon: '📸' }
]

const currentTab = computed(() => tabs.find(t => t.id === activeTab.value))

// === FORM STATE ===
const form = ref({
  nama_sekolah: '',
  nama_yayasan: '',
  akreditasi: '',
  npsn: '',
  nss: '',
  website: '',
  email: '',
  telepon: '',
  nama_kepsek: '',
  nip_kepsek: '',
  alamat: '',
  kelurahan: '',
  kecamatan: '',
  kota: '',
  provinsi: '',
  kode_pos: '',
  visi: '',
  misi: ''
})

const isSaving = ref(false)

const logoFile = ref(null)
const logoPreview = ref(null)

const logoKiriFile = ref(null)
const logoKiriPreview = ref(null)

const foto1File = ref(null)
const foto1Preview = ref(null)

const foto2File = ref(null)
const foto2Preview = ref(null)

const foto3File = ref(null)
const foto3Preview = ref(null)

const handleLogoUpload = (event) => {
  const file = event.target.files[0]
  if (!file) return
  
  if (file.size > 2 * 1024 * 1024) {
    useSwal().toast('Ukuran file maksimal 2MB', 'warning')
    return
  }

  logoFile.value = file
  
  const reader = new FileReader()
  reader.onload = (e) => {
    logoPreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

const handleLogoKiriUpload = (event) => {
  const file = event.target.files[0]
  if (!file) return
  
  if (file.size > 2 * 1024 * 1024) {
    useSwal().toast('Ukuran file maksimal 2MB', 'warning')
    return
  }

  logoKiriFile.value = file
  
  const reader = new FileReader()
  reader.onload = (e) => {
    logoKiriPreview.value = e.target.result
  }
  reader.readAsDataURL(file)
}

const clearLogo = () => {
  logoFile.value = null
  logoPreview.value = null
}

const clearLogoKiri = () => {
  logoKiriFile.value = null
  logoKiriPreview.value = null
}

const handleFoto1Upload = (event) => {
  const file = event.target.files[0]
  if (!file) return
  if (file.size > 2 * 1024 * 1024) { useSwal().toast('Ukuran file maksimal 2MB', 'warning'); return }
  foto1File.value = file
  const reader = new FileReader()
  reader.onload = (e) => { foto1Preview.value = e.target.result }
  reader.readAsDataURL(file)
}

const handleFoto2Upload = (event) => {
  const file = event.target.files[0]
  if (!file) return
  if (file.size > 2 * 1024 * 1024) { useSwal().toast('Ukuran file maksimal 2MB', 'warning'); return }
  foto2File.value = file
  const reader = new FileReader()
  reader.onload = (e) => { foto2Preview.value = e.target.result }
  reader.readAsDataURL(file)
}

const handleFoto3Upload = (event) => {
  const file = event.target.files[0]
  if (!file) return
  if (file.size > 2 * 1024 * 1024) { useSwal().toast('Ukuran file maksimal 2MB', 'warning'); return }
  foto3File.value = file
  const reader = new FileReader()
  reader.onload = (e) => { foto3Preview.value = e.target.result }
  reader.readAsDataURL(file)
}

const clearFoto1 = () => { foto1File.value = null; foto1Preview.value = null }
const clearFoto2 = () => { foto2File.value = null; foto2Preview.value = null }
const clearFoto3 = () => { foto3File.value = null; foto3Preview.value = null }


// === FETCH DATA INITAL ===
const fetchSekolah = async () => {
  const tokenCookie = useCookie('auth_token')
  try {
    const response = await $fetch(apiUrl + '/api/admin/sekolah', {
      headers: { Authorization: `Bearer ${tokenCookie.value}` }
    })
    
    if (response.success && response.data) {
      Object.keys(form.value).forEach(key => {
        if (response.data[key] !== undefined && response.data[key] !== null) {
          form.value[key] = response.data[key]
        }
      })
      if (response.data.logo) {
        logoPreview.value = getImageUrl(response.data.logo)
      }
      if (response.data.logo_kiri) {
        logoKiriPreview.value = getImageUrl(response.data.logo_kiri)
      }
      if (response.data.foto_1) {
        foto1Preview.value = getImageUrl(response.data.foto_1)
      }
      if (response.data.foto_2) {
        foto2Preview.value = getImageUrl(response.data.foto_2)
      }
      if (response.data.foto_3) {
        foto3Preview.value = getImageUrl(response.data.foto_3)
      }
    }
  } catch (error) {
    console.error('Failed to fetch sekolah:', error)
  }
}

onMounted(() => {
  windowWidth.value = window.innerWidth
  window.addEventListener('resize', () => { windowWidth.value = window.innerWidth })
  fetchSekolah()
})

// === SAVE DATA ===
const saveData = async () => {
  if (isSaving.value) return
  isSaving.value = true
  
  const tokenCookie = useCookie('auth_token')
  try {
    const formData = new FormData()
    Object.keys(form.value).forEach(key => {
      formData.append(key, form.value[key] || '')
    })
    
    if (logoFile.value) {
      formData.append('logo', logoFile.value)
    }
    if (logoKiriFile.value) {
      formData.append('logo_kiri', logoKiriFile.value)
    }
    if (foto1File.value) {
      formData.append('foto_1', foto1File.value)
    }
    if (foto2File.value) {
      formData.append('foto_2', foto2File.value)
    }
    if (foto3File.value) {
      formData.append('foto_3', foto3File.value)
    }

    const { fetchSekolah: fetchGlobalSekolah } = useSekolah()
    const response = await $fetch(apiUrl + '/api/admin/sekolah', {
      method: 'POST', // POST instead of PUT for FormData handling in Laravel
      headers: { 
        Authorization: `Bearer ${tokenCookie.value}`,
      },
      body: formData
    })
    
    if (response.success) {
      // Update global state so sidebar logo updates immediately without refresh
      await fetchGlobalSekolah()
      
      useSwal().toast('Data Sekolah berhasil disimpan!', 'success')
    }
  } catch (error) {
    console.error('Failed to save sekolah:', error)
    useSwal().toast('Terjadi kesalahan saat menyimpan data.', 'error')
  } finally {
    isSaving.value = false
  }
}
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-fadeIn {
  animation: fadeIn 0.3s ease-out forwards;
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

/* Hide scrollbar for mobile tabs */
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}
.hide-scrollbar {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
</style>
