<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-2 grid grid-cols-2 gap-2 z-20">
        <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTabMobile = tab.id"
          :class="activeTabMobile === tab.id ? 'bg-gradient-to-br from-rose-500 to-rose-600 text-white shadow-md shadow-rose-500/20 ring-2 ring-rose-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="rounded-xl flex flex-col items-center justify-center py-2 px-1 transition-all active:scale-95">
          <span class="text-lg mb-0.5 transition-transform" :class="activeTabMobile === tab.id ? 'scale-110' : ''">{{ tab.icon }}</span>
          <span class="text-[9px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
        </button>
      </div>

      <!-- Panel Dock Kiri (Filter Kelas) -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all', activeTabMobile === 'dock' || isDesktop ? 'flex' : 'hidden xl:flex', !isDesktop ? 'pt-[60px]' : '']">
        
        <div class="p-6 shrink-0">
          <div class="bg-gradient-to-r from-rose-600 to-rose-700 rounded-2xl p-5 border border-rose-500 shadow-sm relative overflow-hidden flex items-center gap-4">
            <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0 relative z-10">🏫</div>
            <div class="relative z-10">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">Filter Kelas</h3>
                <p class="text-[10px] text-rose-100 font-semibold uppercase mt-0.5">Pilih Rombel Siswa</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-20 text-white">
              <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"></path></svg>
            </div>
          </div>
        </div>

        <div class="px-6 pb-6 bg-white space-y-4 shrink-0">
            <!-- Pilih Kurikulum -->
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Pilih Kurikulum</label>
                <select v-model="selectedKurikulum" class="w-full px-3 py-2.5 bg-white border-2 border-slate-200/70 rounded-xl focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all text-xs font-bold outline-none text-slate-700" :disabled="masterKurikulum.length === 0">
                    <option v-if="masterKurikulum.length === 0" value="" disabled>Belum diseting</option>
                    <option v-for="k in masterKurikulum" :key="k.id" :value="k.id">
                        {{ k.nama_kurikulum }}
                    </option>
                </select>
            </div>

            <!-- Pilih Periode -->
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Periode Penilaian</label>
                <select v-model="selectedPeriode" @change="fetchSiswas" class="w-full px-3 py-2.5 bg-white border-2 border-slate-200/70 rounded-xl focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all text-xs font-bold outline-none text-slate-700" :disabled="periodeOptions.length === 0">
                    <option v-if="periodeOptions.length === 0" value="" disabled>Belum diseting</option>
                    <option v-for="p in periodeOptions" :key="p.id" :value="p.id">
                        {{ p.label }} {{ activePeriodeBackend.includes(p.id) ? '(Aktif)' : '(Ditutup)' }}
                    </option>
                </select>
            </div>

            <!-- Cari Kelas -->
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Cari Kelas</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
                    <input type="text" v-model="searchKelas" placeholder="Cari nama kelas..." class="w-full pl-9 pr-3 py-2.5 bg-white border-2 border-slate-200/70 rounded-xl focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all text-xs font-bold outline-none text-slate-700">
                </div>
            </div>
        </div>

        <!-- List Kelas -->
        <div class="flex-1 overflow-y-auto custom-scrollbar bg-white">
            <div v-if="isLoadingKelas" class="p-8 text-center flex flex-col items-center justify-center h-full opacity-60">
                <div class="w-8 h-8 border-4 border-rose-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Kelas...</span>
            </div>
            <div v-else>
                <button v-for="kelas in filteredKelases" :key="kelas.id" @click="selectKelas(kelas.id)"
                    class="w-full text-left px-5 py-4 border-b border-slate-100 hover:bg-rose-50 transition-colors flex items-center justify-between group"
                    :class="selectedKelasId === kelas.id ? 'bg-rose-50 border-l-4 border-l-rose-500' : 'bg-white border-l-4 border-l-transparent'">
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-wider transition-colors" :class="selectedKelasId === kelas.id ? 'text-rose-700' : 'text-slate-700 group-hover:text-rose-700'">
                            {{ kelas.tingkat }} {{ kelas.nama_kelas }}
                        </h4>
                        <p class="text-[9px] font-bold text-slate-400 uppercase mt-1">{{ kelas.siswas_count || 0 }} Siswa Aktif</p>
                    </div>
                    <span v-if="selectedKelasId === kelas.id" class="text-rose-500 text-lg">▶</span>
                </button>
                <div v-if="filteredKelases.length === 0" class="p-8 text-center text-slate-400 text-xs font-bold">
                    Tidak ada kelas ditemukan.
                </div>
            </div>
        </div>
      </div>

      <!-- Panel Flow Kanan (Daftar Siswa & Poin) -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative', activeTabMobile === 'flow' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[60px]' : '']">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            <!-- Header Flow -->
            <div class="px-6 py-5 bg-white border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shrink-0 z-10 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-500 hidden sm:flex text-xl">👥</div>
                <div>
                    <div class="flex items-center gap-3">
                        <h3 class="text-sm font-black uppercase tracking-widest text-slate-700">Daftar Siswa</h3>
                        <span v-if="activeTahunAjaran" class="bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-md text-[10px] font-black tracking-widest border border-indigo-100 uppercase">
                            TA. {{ activeTahunAjaran }}
                        </span>
                    </div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">
                        {{ selectedKelasInfo ? `${selectedKelasInfo.tingkat} ${selectedKelasInfo.nama_kelas} - Total: ${siswas.length} Siswa` : 'Pilih kelas terlebih dahulu' }}
                    </p>
                </div>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-64" v-if="selectedKelasId && !isLoadingSiswa">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
                    <input type="text" v-model="searchSiswa" placeholder="Cari nama/NIS siswa..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all text-xs font-bold outline-none text-slate-700">
                </div>
            </div>
        </div>

        <!-- Flow Content -->
        <div class="flex-1 overflow-auto bg-slate-50 custom-scrollbar p-4 sm:p-6 relative">
            <div v-if="!selectedKelasId" class="bg-white rounded-2xl p-20 flex flex-col items-center justify-center border-2 border-dashed border-slate-200 shadow-sm text-center h-full max-h-[400px]">
                <div class="text-6xl opacity-20 mb-4">👈</div>
                <p class="text-sm font-bold text-slate-500">Silakan pilih kelas di panel kiri.</p>
            </div>

            <div v-else-if="isLoadingSiswa" class="bg-white rounded-2xl p-16 flex flex-col items-center justify-center border border-slate-200/60 shadow-sm opacity-60 h-full max-h-[400px]">
                <div class="w-8 h-8 border-4 border-rose-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Siswa...</span>
            </div>

            <div v-else-if="!activePeriodeBackend.includes(selectedPeriode)" class="bg-white rounded-2xl p-20 flex flex-col items-center justify-center border-2 border-dashed border-slate-300 shadow-sm text-center bg-slate-50/50 h-full max-h-[400px]">
                <div class="text-6xl mb-6">🔒</div>
                <h3 class="text-lg font-black text-slate-800 uppercase tracking-widest">Periode Terkunci</h3>
                <p class="text-xs font-bold text-slate-500 mt-3 tracking-wide max-w-sm leading-relaxed">
                    Periode <span class="text-rose-600 font-black">{{ selectedPeriode }}</span> saat ini berstatus nonaktif atau belum dibuka oleh bagian Kurikulum. Input data dibekukan sementara.
                </p>
            </div>

            <div v-else class="space-y-4 max-w-5xl mx-auto">
                <div v-if="filteredSiswas.length === 0" class="text-center p-10 text-slate-400 text-sm font-bold">
                    Tidak ada data siswa ditemukan.
                </div>

                <!-- Kartu Siswa (Accordion) -->
                <div v-for="siswa in filteredSiswas" :key="selectedKelas + '-' + selectedBulan + '-' + activeTab + '-' + siswa.id" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden transition-all duration-300">
                    <div class="px-5 py-4 bg-slate-50 flex flex-col sm:flex-row items-center justify-between gap-4 cursor-pointer hover:bg-white transition-colors group" @click="toggleExpanded(siswa.id)">
                        <div class="flex items-center gap-4 w-full sm:w-auto">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-black flex-shrink-0 text-white shadow-sm" :class="siswa.sisa_poin < 50 ? 'bg-rose-500' : 'bg-slate-300'">
                                {{ siswa.nama.charAt(0) }}
                            </div>
                            <div>
                                <h4 class="font-black text-slate-800 text-sm uppercase group-hover:text-rose-600 transition-colors">{{ siswa.nama }}</h4>
                                <p class="text-[10px] font-bold text-slate-500 tracking-widest uppercase mt-0.5">NIS: {{ siswa.nis }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-6 w-full sm:w-auto justify-between sm:justify-end">
                            <div class="text-right bg-white px-4 py-1.5 rounded-xl border border-slate-200 shadow-sm">
                                <p class="text-[8px] font-black uppercase tracking-widest text-slate-400 mb-0.5">Sisa Poin</p>
                                <p class="text-lg font-black leading-none" :class="siswa.sisa_poin < 50 ? 'text-rose-600' : 'text-emerald-600'">{{ siswa.sisa_poin }}</p>
                            </div>
                            <button class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-100 text-slate-400 group-hover:bg-rose-100 group-hover:text-rose-600 transition-all">
                                <span class="transform transition-transform duration-300" :class="{ 'rotate-180': expandedSiswa === siswa.id }">▼</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Area Expanded (Riwayat Poin) -->
                    <div v-if="expandedSiswa === siswa.id" class="p-5 border-t border-slate-100 animate-slideUpFade bg-white">
                        <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-4">
                            <h5 class="text-[11px] font-black text-slate-500 uppercase tracking-widest">Riwayat Kedisiplinan</h5>
                            <button @click.stop="openModal(siswa)" class="bg-gradient-to-r from-rose-500 to-rose-600 hover:-translate-y-0.5 text-white px-4 py-2 rounded-xl font-bold shadow-lg shadow-rose-500/30 transition-all text-[10px] uppercase tracking-widest flex items-center gap-2">
                                <span>➕</span> Input Poin
                            </button>
                        </div>
                        
                        <div v-if="siswa.poin_logs && siswa.poin_logs.length > 0" class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 text-[9px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-200">
                                        <th class="p-3 pl-4 w-32">Tanggal</th>
                                        <th class="p-3 w-48">Jenis / Kategori</th>
                                        <th class="p-3 min-w-[200px]">Keterangan</th>
                                        <th class="p-3 text-center w-24">Poin</th>
                                        <th class="p-3 text-right pr-4 w-20">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-xs font-bold text-slate-600">
                                    <tr v-for="log in siswa.poin_logs" :key="log.id" class="border-b border-slate-50 hover:bg-slate-50/50 group">
                                        <td class="p-3 pl-4">{{ formatDate(log.tanggal) }}</td>
                                        <td class="p-3">
                                            <div v-if="log.pelanggaran_id" class="flex items-center gap-2">
                                                <span class="w-6 h-6 rounded-md bg-rose-50 flex items-center justify-center text-xs">🚨</span>
                                                <span class="text-rose-700 line-clamp-2" :title="log.pelanggaran?.nama_pelanggaran">{{ log.pelanggaran?.nama_pelanggaran }}</span>
                                            </div>
                                            <div v-else class="flex items-center gap-2">
                                                <span class="w-6 h-6 rounded-md bg-emerald-50 flex items-center justify-center text-xs">🌟</span>
                                                <span class="text-emerald-700">Penghargaan</span>
                                            </div>
                                        </td>
                                        <td class="p-3 text-[10px] font-semibold text-slate-500">{{ log.catatan || '-' }}</td>
                                        <td class="p-3 text-center">
                                            <span v-if="log.skor_pengurang > 0" class="inline-block text-[10px] font-black text-rose-600 bg-rose-50 border border-rose-200 px-2 py-1 rounded-md">-{{ log.skor_pengurang }}</span>
                                            <span v-if="log.skor_penambah > 0" class="inline-block text-[10px] font-black text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-1 rounded-md">+{{ log.skor_penambah }}</span>
                                        </td>
                                        <td class="p-3 pr-4 text-right">
                                            <button @click.stop="confirmDeletePoin(log.id, siswa.id)" class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50 flex items-center justify-center transition-all shadow-sm opacity-0 group-hover:opacity-100 mx-auto" title="Hapus">🗑️</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center py-8 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            <span class="text-3xl opacity-30 block mb-3">✨</span>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Belum ada catatan untuk siswa ini.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ==============================================
         MODAL FORM INPUT POIN
         ============================================== -->
    <div v-if="isModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade">
            <!-- Header -->
            <div class="px-6 py-5 bg-gradient-to-r from-rose-50 to-white border-b border-rose-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-rose-900 tracking-tight flex items-center gap-2">
                        <span>➕</span> Input Poin Siswa
                    </h3>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1 ml-7">{{ targetSiswa?.nama }} ({{ targetSiswa?.nis }})</p>
                </div>
                <button @click="closeModal" class="text-slate-400 hover:text-rose-500 transition-colors bg-white w-8 h-8 rounded-full flex items-center justify-center border border-slate-200 shadow-sm">✕</button>
            </div>

            <!-- Body -->
            <form @submit.prevent="savePoin" class="p-6 space-y-5">
                <!-- Pilihan Jenis -->
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 ml-1">Jenis Pencatatan</label>
                    <div class="flex gap-4">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" v-model="form.jenis_input" value="Pelanggaran" class="peer hidden">
                            <div class="p-4 rounded-2xl border-2 peer-checked:border-rose-500 peer-checked:bg-rose-50 peer-checked:shadow-inner text-center transition-all bg-white border-slate-200">
                                <span class="text-2xl block mb-2">🚨</span>
                                <span class="text-[10px] font-black uppercase tracking-widest peer-checked:text-rose-700 text-slate-500">Pelanggaran</span>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" v-model="form.jenis_input" value="Penghargaan" class="peer hidden">
                            <div class="p-4 rounded-2xl border-2 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:shadow-inner text-center transition-all bg-white border-slate-200">
                                <span class="text-2xl block mb-2">🌟</span>
                                <span class="text-[10px] font-black uppercase tracking-widest peer-checked:text-emerald-700 text-slate-500">Penghargaan</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Tanggal</label>
                        <input type="date" v-model="form.tanggal" required class="w-full px-4 py-3 bg-white border-2 border-slate-200/70 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all text-xs font-bold text-slate-700 outline-none">
                    </div>
                    <div v-if="form.jenis_input === 'Penghargaan'">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Poin Tambahan (+)</label>
                        <input type="number" v-model.number="form.skor_penambah" required min="1" max="100" class="w-full px-4 py-3 bg-white border-2 border-slate-200/70 rounded-2xl focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-black text-emerald-600 outline-none text-center">
                    </div>
                </div>

                <div v-if="form.jenis_input === 'Pelanggaran'">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Jenis Pelanggaran</label>
                    <select v-model="form.pelanggaran_id" required class="w-full px-4 py-3 bg-white border-2 border-slate-200/70 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all text-xs font-bold text-slate-700 outline-none">
                        <option value="" disabled>-- Pilih Jenis Pelanggaran --</option>
                        <option v-for="p in pelanggarans" :key="p.id" :value="p.id">
                            {{ p.nama_pelanggaran }} (Min {{ p.bobot }} Poin) - {{ p.jenis }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Catatan Tambahan (Opsional)</label>
                    <textarea v-model="form.keterangan" rows="2" placeholder="Tuliskan keterangan detail kejadian..." class="w-full px-4 py-3 bg-white border-2 border-slate-200/70 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all text-xs font-semibold text-slate-700 outline-none resize-none"></textarea>
                </div>

                <!-- Footer -->
                <div class="pt-4 mt-6 border-t border-slate-100 flex gap-3">
                    <button type="button" @click="closeModal" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">Batal</button>
                    <button type="submit" :disabled="isSaving" class="flex-1 py-3 bg-gradient-to-r from-rose-500 to-rose-600 hover:-translate-y-0.5 disabled:bg-rose-400 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        <span v-if="isSaving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        <span v-else>Simpan Poin</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==============================================
         MODAL DELETE (STANDAR)
         ============================================== -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade text-center">
            <div class="p-8">
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner border-4 border-white ring-4 ring-rose-50">⚠️</div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Hapus Catatan?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Yakin ingin menghapus catatan poin kedisiplinan ini? Data poin siswa akan dihitung ulang secara otomatis.
                </p>
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isDeleteModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">Batal</button>
                    <button @click="executeDeletePoin" class="flex-1 py-3 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        <span>Hapus</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'

definePageMeta({
  layout: 'bk',
  middleware: 'bk',
  title: 'Input Poin Siswa'
})

// UI State
const windowWidth = ref(1024)
const isDesktop = computed(() => windowWidth.value >= 1024) // lg breakpoint
const activeTabMobile = ref('dock')
const mobileTabs = [
  { id: 'dock', title: 'Pilih Kelas', icon: '🏫' },
  { id: 'flow', title: 'Data Siswa', icon: '👥' }
]

const isLoadingKelas = ref(true)
const isLoadingSiswa = ref(false)
const kelases = ref([])
const siswas = ref([])
const pelanggarans = ref([])
const selectedKelasId = ref(null)

const searchKelas = ref('')
const searchSiswa = ref('')
const expandedSiswa = ref(null)

const activePeriodeBackend = ref([])
const selectedPeriode = ref('')
const selectedKurikulum = ref('')
const masterKurikulum = ref([])
const masterTitimangsas = ref([])
const activeTahunAjaran = ref('')

const periodeOptions = computed(() => {
    if (!selectedKurikulum.value) return []
    return masterTitimangsas.value
        .filter(t => t.kurikulum_id == selectedKurikulum.value)
        .map(t => ({
            id: t.nama_periode,
            label: t.nama_periode
        }))
})

watch(selectedKurikulum, () => {
    if (periodeOptions.value.length > 0) {
        if (!periodeOptions.value.find(p => p.id === selectedPeriode.value)) {
            selectedPeriode.value = periodeOptions.value[0].id
            // Refetch siswas automatically since period changed
            if (selectedKelasId.value) {
                fetchSiswas()
            }
        }
    } else {
        selectedPeriode.value = ''
    }
})

const filteredKelases = computed(() => {
    if (!searchKelas.value) return kelases.value
    const ls = searchKelas.value.toLowerCase()
    return kelases.value.filter(k => 
        k.nama_kelas.toLowerCase().includes(ls) || 
        (k.tingkat + ' ' + k.nama_kelas).toLowerCase().includes(ls)
    )
})

const filteredSiswas = computed(() => {
    if (!searchSiswa.value) return siswas.value
    const ls = searchSiswa.value.toLowerCase()
    return siswas.value.filter(s => s.nama.toLowerCase().includes(ls) || s.nis.includes(ls))
})

const selectedKelasInfo = computed(() => {
    if (!selectedKelasId.value) return null
    return kelases.value.find(k => k.id === selectedKelasId.value)
})

const fetchInitialData = async () => {
    isLoadingKelas.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/bk/poin', {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            kelases.value = response.data.kelases
            pelanggarans.value = response.data.pelanggarans
            
            if (response.data.tahun_aktif) {
                activeTahunAjaran.value = response.data.tahun_aktif.tahun
            }

            if (response.data.master_kurikulum) {
                masterKurikulum.value = response.data.master_kurikulum
                if (masterKurikulum.value.length > 0) {
                    selectedKurikulum.value = masterKurikulum.value[0].id
                }
            }

            if (response.data.titimangsas) {
                masterTitimangsas.value = response.data.titimangsas
            }

            if (response.data.periode_aktif) {
                activePeriodeBackend.value = response.data.periode_aktif
                // Auto-select first active period if available
                if (activePeriodeBackend.value.length > 0 && !activePeriodeBackend.value.includes(selectedPeriode.value)) {
                    selectedPeriode.value = activePeriodeBackend.value[0];
                }
            }
        }
    } catch (error) {
        console.error('Fetch error:', error)
        useSwal().toast('Gagal memuat data kelas.', 'error')
    } finally {
        isLoadingKelas.value = false
    }
}

const selectKelas = async (id) => {
    selectedKelasId.value = id
    expandedSiswa.value = null
    isLoadingSiswa.value = true
    if (!isDesktop.value) activeTabMobile.value = 'flow'
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/bk/poin?kelas_id=${id}&periode=${selectedPeriode.value}`, {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            siswas.value = response.data.siswas
        }
    } catch (error) {
        console.error('Fetch error:', error)
        useSwal().toast('Gagal memuat data siswa.', 'error')
    } finally {
        isLoadingSiswa.value = false
    }
}

const fetchSiswas = () => {
    if (selectedKelasId.value) {
        selectKelas(selectedKelasId.value);
    }
}

const toggleExpanded = (id) => {
    expandedSiswa.value = expandedSiswa.value === id ? null : id
}

// FORMAT TANGGAL
const formatDate = (dateStr) => {
    if (!dateStr) return '-'
    const d = new Date(dateStr)
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

// MODAL FORM
const isModalOpen = ref(false)
const isSaving = ref(false)
const targetSiswa = ref(null)

const form = ref({
    siswa_id: '',
    jenis_input: 'Pelanggaran',
    pelanggaran_id: '',
    skor_penambah: 5,
    tanggal: new Date().toISOString().split('T')[0],
    keterangan: ''
})

const openModal = (siswa) => {
    targetSiswa.value = siswa
    form.value = {
        siswa_id: siswa.id,
        jenis_input: 'Pelanggaran',
        pelanggaran_id: '',
        skor_penambah: 5,
        tanggal: new Date().toISOString().split('T')[0],
        keterangan: ''
    }
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
    targetSiswa.value = null
}

const savePoin = async () => {
    isSaving.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/bk/poin', {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` },
            body: {
                ...form.value,
                periode: selectedPeriode.value
            }
        })
        
        if (response.success) {
            closeModal()
            useSwal().toast(response.message, 'success')
            // Refresh siswa data
            selectKelas(selectedKelasId.value)
        }
    } catch (error) {
        useSwal().toast(error.response?._data?.message || 'Gagal menyimpan data.', 'error')
    } finally {
        isSaving.value = false
    }
}

// DELETE POIN
const isDeleteModalOpen = ref(false)
const deleteTargetId = ref(null)

const confirmDeletePoin = (poinId, siswaId) => {
    deleteTargetId.value = poinId
    isDeleteModalOpen.value = true
}

const executeDeletePoin = async () => {
    if (!deleteTargetId.value) return
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/bk/poin/${deleteTargetId.value}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            isDeleteModalOpen.value = false
            useSwal().toast(response.message, 'success')
            selectKelas(selectedKelasId.value)
        }
    } catch (error) {
        isDeleteModalOpen.value = false
        useSwal().toast('Gagal menghapus data.', 'error')
    }
}

onMounted(() => {
    windowWidth.value = window.innerWidth
    window.addEventListener('resize', () => { windowWidth.value = window.innerWidth })
    fetchInitialData()
})
</script>
