<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex flex-col xl:flex-row overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="xl:w-[360px] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] overflow-y-auto custom-scrollbar">
        <div class="p-6 space-y-6">
          <div class="bg-gradient-to-r from-sky-600 to-blue-700 rounded-2xl p-5 border border-sky-500 shadow-sm relative overflow-hidden">
            <div class="relative z-10">
              <h3 class="text-sm font-black uppercase tracking-widest text-white">Cetak Rapor & Leger</h3>
              <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Cetak buku rapor individu dan leger kelas</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-10">
              <svg class="w-24 h-24 transform translate-x-6 translate-y-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
          </div>
          
          <div class="space-y-4" v-if="pageData">
            <!-- Informasi Kelas & Tahun -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-3">
              <div class="flex justify-between items-center pb-2 border-b border-slate-200">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Kelas</span>
                <span class="text-[11px] font-bold text-slate-700">{{ pageData.kelas?.tingkat }} {{ pageData.kelas?.nama_kelas }}</span>
              </div>
              <div class="flex justify-between items-center pb-2 border-b border-slate-200">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Tahun Ajaran</span>
                <span class="text-[11px] font-bold text-slate-700">{{ pageData.tahun_ajaran?.tahun }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-1">Periode</span>
                <select 
                  v-model="activeTitimangsaId"
                  class="text-[11px] font-bold text-indigo-700 bg-transparent border-none p-0 focus:ring-0 cursor-pointer outline-none text-right max-w-[150px]"
                  :disabled="!pageData?.titimangsas?.length"
                >
                  <option v-if="!pageData?.titimangsas?.length" value="" disabled>Belum diseting</option>
                  <option v-for="t in pageData.titimangsas" :key="t.id" :value="t.id">
                    {{ t.nama_periode_panjang || t.nama_periode }} {{ !t.is_aktif ? '(Ditutup)' : '' }}
                  </option>
                </select>
              </div>
            </div>

            <!-- Mode Selector -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Mode Tampilan</label>
              <div class="bg-slate-100 p-1 rounded-xl flex items-center shadow-inner">
                  <button 
                      @click="mode = 'rapor'"
                      :class="mode === 'rapor' ? 'bg-white text-indigo-700 shadow-sm font-black' : 'text-slate-500 font-bold hover:text-slate-700'"
                      class="flex-1 px-4 py-2 rounded-lg text-[10px] uppercase tracking-widest transition-all duration-200"
                  >
                      Rapor Individu
                  </button>
                  <button 
                      @click="mode = 'leger'"
                      :class="mode === 'leger' ? 'bg-white text-indigo-700 shadow-sm font-black' : 'text-slate-500 font-bold hover:text-slate-700'"
                      class="flex-1 px-4 py-2 rounded-lg text-[10px] uppercase tracking-widest transition-all duration-200"
                  >
                      Tabel Leger
                  </button>
              </div>
            </div>

            <!-- Search / Filter (Hanya untuk Rapor) -->
            <div v-show="mode === 'rapor'">
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pencarian Siswa</label>
              <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 pointer-events-none">🔍</span>
                  <input type="text" v-model="searchQuery" placeholder="Cari nama siswa..." 
                    class="w-full pl-10 pr-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-semibold text-xs text-slate-700 outline-none">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          
          <!-- Loading State -->
          <div v-if="isLoading" class="flex-grow flex flex-col items-center justify-center p-20 opacity-60 bg-white rounded-3xl shadow-sm border border-slate-200/60">
            <div class="w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Memuat Data Kelas...</span>
          </div>

          <!-- Error State -->
          <div v-else-if="errorMessage" class="flex-grow flex flex-col items-center justify-center p-16 text-center bg-white rounded-3xl shadow-sm border border-slate-200/60">
            <div class="text-rose-500 text-4xl mb-4">⚠️</div>
            <h3 class="text-rose-800 font-black mb-1">Gagal Memuat Data</h3>
            <p class="text-rose-600 text-sm font-semibold max-w-md">{{ errorMessage }}</p>
            <button @click="loadData" class="mt-4 px-4 py-2 bg-rose-100 hover:bg-rose-200 text-rose-700 text-[10px] font-black uppercase tracking-widest rounded-lg transition-colors">
              Coba Lagi
            </button>
          </div>

          <!-- Main Content Container -->
          <div v-else-if="pageData" class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            
            <!-- HEADER KANAN -->
            <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-white shrink-0 z-10">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-lg border border-indigo-100">
                  <span v-if="mode === 'rapor'">📄</span>
                  <span v-else>📊</span>
                </div>
                <div>
                  <h3 class="text-[13px] font-black leading-none uppercase tracking-wide text-slate-800">
                    {{ mode === 'rapor' ? 'Daftar Siswa' : 'Tabel Leger Kelas' }}
                  </h3>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                    {{ mode === 'rapor' ? `${filteredStudents.length} Siswa Ditemukan` : (legerData ? 'Siap Dicetak' : 'Memuat Leger...') }}
                  </p>
                </div>
              </div>
              <div v-if="mode === 'leger' && legerData" class="flex items-center space-x-4 shrink-0 mt-4 md:mt-0">
                 <button @click="printLeger" class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest px-4 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-md transition-all active:scale-95" :disabled="isLoadingLeger">
                  <span>🖨️ CETAK LEGER</span>
                </button>
              </div>
            </div>

            <!-- CONTENT: RAPOR -->
            <div v-if="mode === 'rapor'" class="flex-1 overflow-auto custom-scrollbar relative bg-slate-50/30">
              <table class="w-full text-left border-collapse bg-white">
                <thead class="sticky top-0 z-20 shadow-sm">
                  <tr class="bg-slate-100 border-b border-slate-200">
                    <th class="py-3 px-4 text-center text-[10px] font-black uppercase tracking-widest text-slate-500 w-[60px] border-r border-slate-200">No</th>
                    <th class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500 border-r border-slate-200 min-w-[200px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] sticky left-0 bg-slate-100 z-30">Nama Siswa</th>
                    <th class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500 min-w-[150px] border-r border-slate-200">NISN / NIS</th>
                    <th class="py-3 px-4 text-center text-[10px] font-black uppercase tracking-widest text-slate-500 w-32 bg-slate-50">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-for="(siswa, index) in filteredStudents" :key="activeTab + '-' + siswa.id" class="hover:bg-slate-50/80 transition-colors group">
                    <td class="py-3 px-4 text-center text-[11px] font-bold text-slate-400 border-r border-slate-100">{{ index + 1 }}</td>
                    <td class="py-3 px-4 border-r border-slate-100 sticky left-0 bg-white group-hover:bg-slate-50/90 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.02)] z-10">
                      <div class="text-[12px] font-black text-slate-700 uppercase tracking-wide">{{ siswa.user?.name || siswa.nama_lengkap }}</div>
                    </td>
                    <td class="py-3 px-4 border-r border-slate-100">
                      <div class="text-[11px] font-bold text-slate-600">{{ siswa.nisn || '-' }}</div>
                      <div class="text-[9px] font-bold text-slate-400 tracking-widest uppercase mt-0.5">{{ siswa.nis || '-' }}</div>
                    </td>
                    <td class="py-3 px-4 text-center bg-slate-50/30">
                      <button 
                        @click="bukaPreviewRapor(siswa)"
                        class="px-3 py-1.5 bg-white border border-indigo-200 text-indigo-600 hover:bg-indigo-50 font-black rounded-lg transition-all text-[10px] uppercase tracking-widest shadow-sm hover:shadow active:scale-95 inline-flex items-center justify-center w-full"
                      >
                        👁️ Preview
                      </button>
                    </td>
                  </tr>
                  <tr v-if="filteredStudents.length === 0">
                    <td colspan="4" class="py-12 text-center text-slate-500 bg-slate-50/50">
                      <div class="text-3xl mb-3 opacity-50">🔍</div>
                      <div class="text-xs font-bold">Tidak ada siswa yang sesuai.</div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- CONTENT: LEGER -->
            <div v-else class="flex-1 flex flex-col min-h-0 bg-slate-50/30">
              <div v-if="isLoadingLeger" class="flex-grow flex flex-col items-center justify-center p-20 opacity-60">
                  <div class="w-10 h-10 border-4 border-indigo-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                  <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Memuat Tabel Leger...</span>
              </div>
              <div v-else-if="legerData" class="flex-1 overflow-auto custom-scrollbar relative p-4" id="leger-print-area">
                  
                  <div class="hidden print:block mb-6 w-full text-center">
                      <h2 class="text-xl font-black uppercase text-black border-b-[3px] border-black inline-block pb-1 mb-2 tracking-widest">LEGER NILAI PESERTA DIDIK</h2>
                      <div class="flex justify-center gap-12 text-[11px] font-black text-black uppercase tracking-widest mt-1">
                          <p>KELAS: {{ legerData.kelas?.nama_kelas || '-' }}</p>
                          <p>PERIODE: {{ legerData.titimangsa?.nama_periode_panjang || legerData.titimangsa?.nama_periode || '-' }}</p>
                      </div>
                  </div>

                  <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-x-auto custom-scrollbar print:overflow-visible print:border-none print:shadow-none print:rounded-none">
                      <table class="w-full text-left border-collapse min-w-[1200px] print:min-w-0 table-fixed">
                          <thead class="sticky top-0 z-20 print:static">
                              <tr class="text-[10px] font-black uppercase tracking-widest text-slate-500 bg-slate-100 print:text-[8px] print:text-black border-b border-slate-200">
                                  <th rowspan="2" class="w-[20%] py-3 px-4 border-r border-slate-200 align-middle text-center sticky left-0 bg-slate-100 z-30 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] print:static print:bg-transparent print:border-black print:shadow-none">NAMA SISWA</th>
                                  <template v-for="(grup, key) in legerData.dataTabel" :key="key">
                                      <th v-if="grup.items.length > 0" :colspan="grup.items.length" class="py-2 px-2 border-r border-slate-200 text-center bg-indigo-50/50 text-indigo-700 print:bg-transparent print:border-black print:text-black">
                                          {{ grup.nama_kelompok }}
                                      </th>
                                  </template>
                                  <th colspan="3" class="py-2 px-2 text-center bg-slate-700 text-white print:bg-slate-300 print:text-black print:border-black">REKAPITULASI</th>
                              </tr>
                              <tr class="text-[9px] font-bold uppercase print:text-[7px] print:text-black border-b border-slate-200 bg-slate-50">
                                  <template v-for="(grup, key) in legerData.dataTabel" :key="key">
                                      <template v-for="item in grup.items" :key="item.id">
                                          <th class="py-2 px-1 border-r border-slate-200 text-slate-600 align-bottom h-40 print:h-32 print:bg-transparent print:border-black print:text-black text-center">
                                              <div class="whitespace-normal text-left mx-auto vertical-text leading-tight w-full">
                                                  {{ item.nama_mapel }}
                                              </div>
                                          </th>
                                      </template>
                                  </template>
                                  <th class="w-[5%] py-2 px-1 border-r border-slate-200 text-center align-middle bg-slate-200/50 print:bg-transparent print:border-black print:text-black">JML</th>
                                  <th class="w-[5%] py-2 px-1 border-r border-slate-200 text-center align-middle bg-slate-200/50 print:bg-transparent print:border-black print:text-black">RATA</th>
                                  <th class="w-[5%] py-2 px-1 text-center align-middle bg-slate-200/50 print:bg-transparent print:border-black print:text-black">RANK</th>
                              </tr>
                          </thead>
                          <tbody class="divide-y divide-slate-100">
                              <tr v-for="siswa in legerData.siswas" :key="activeTab + '-' + siswa.id" class="hover:bg-indigo-50/30 print:break-inside-avoid">
                                  <td class="py-2 px-4 text-[11px] font-black text-slate-700 uppercase border-r border-slate-100 sticky left-0 bg-white shadow-[2px_0_5px_-2px_rgba(0,0,0,0.02)] print:static print:bg-transparent print:border-black print:shadow-none print:text-[9px] print:text-black">
                                      {{ siswa.user?.name || '-' }}
                                  </td>
                                  <template v-for="(grup, key) in legerData.dataTabel" :key="key">
                                      <template v-for="item in grup.items" :key="item.id">
                                          <td class="py-2 px-1 border-r border-slate-100 text-center text-[11px] text-slate-600 font-bold print:border-black print:text-[8px] print:text-black">
                                              {{ legerData.nilaiMatriks[siswa.id] && legerData.nilaiMatriks[siswa.id][item.id] !== undefined ? legerData.nilaiMatriks[siswa.id][item.id] : '-' }}
                                          </td>
                                      </template>
                                  </template>
                                  <td class="py-2 px-2 border-r border-slate-100 text-center text-[11px] font-black text-slate-800 bg-slate-50/50 print:bg-transparent print:border-black print:text-[9px] print:text-black">
                                      {{ legerData.rekapSiswa[siswa.id]?.jumlah || '-' }}
                                  </td>
                                  <td class="py-2 px-2 border-r border-slate-100 text-center text-[11px] font-black text-indigo-600 bg-indigo-50/30 print:bg-transparent print:border-black print:text-[9px] print:text-black">
                                      {{ legerData.rekapSiswa[siswa.id]?.rata || '-' }}
                                  </td>
                                  <td class="py-2 px-2 text-center text-[11px] font-black text-rose-600 bg-rose-50/30 print:bg-transparent print:border-black print:text-[9px] print:text-black">
                                      {{ legerData.rekapSiswa[siswa.id]?.rank || '-' }}
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
    </div>

    <!-- PREVIEW RAPOR MODAL -->
    <div v-if="showPreview && raporData" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-hidden">
        <div class="bg-slate-200 w-full max-w-5xl h-[95vh] rounded-3xl shadow-2xl flex flex-col overflow-hidden animate-scaleIn border border-slate-300/50">
            
            <!-- Modal Header -->
            <div class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between shrink-0">
                <div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Preview Rapor Siswa</h3>
                    <p class="text-[11px] font-bold text-slate-500 tracking-widest mt-0.5 uppercase">{{ raporData.siswa?.nama }} <span class="mx-1">•</span> {{ raporData.kelas }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <button @click="printRapor" class="px-5 py-2.5 bg-indigo-600 text-white hover:bg-indigo-700 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-md hover:shadow-lg transition-all active:scale-95 flex items-center gap-2">
                        <span>🖨️</span> CETAK PDF
                    </button>
                    <button @click="showPreview = false" class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-700 transition-colors font-bold text-lg">&times;</button>
                </div>
            </div>

            <!-- Modal Body (A4/F4 Paper View Container) -->
            <div class="flex-1 overflow-y-auto p-4 md:p-8 bg-slate-100 print-bg-white print-p-0 print-overflow-visible custom-scrollbar">
                <!-- Paper Content -->
                <div id="rapor-print-area" class="bg-white mx-auto shadow-xl border border-slate-200 max-w-[210mm] min-h-[330mm] p-8 md:p-12 print-shadow-none print-w-full print-max-w-none print-min-h-0 text-slate-800 font-serif text-[12px] leading-tight">
                    
                    <!-- KOP SURAT (Hanya di Halaman Pertama) -->
                    <div class="flex items-center justify-between pb-2 mb-1" style="font-family: 'Times New Roman', Times, serif;">
                        <!-- Logo Kiri -->
                        <div class="w-[110px] h-[110px] flex-shrink-0 flex items-center justify-center">
                            <img v-if="raporData.sekolah?.logo_kiri" :src="raporData.sekolah.logo_kiri" alt="Logo Dinas" class="max-w-full max-h-full object-contain">
                            <div v-else class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center text-[9px] text-slate-400 border border-dashed border-slate-300">Logo<br>Kiri</div>
                        </div>
                        
                        <!-- Text Tengah -->
                        <div class="flex-1 text-center px-2 flex flex-col justify-center overflow-hidden">
                            <h2 v-if="raporData.sekolah?.nama_yayasan" class="text-[15px] font-black uppercase m-0 leading-tight block" style="transform: scaleX(1.05); font-weight: 900; -webkit-text-stroke: 0.5px black;">{{ raporData.sekolah.nama_yayasan }}</h2>
                            
                            <h1 class="text-[25px] font-black uppercase m-0 leading-none mt-1 block" style="transform: scaleX(1.15); font-weight: 900; -webkit-text-stroke: 1.2px black; letter-spacing: 0.02em;">{{ raporData.sekolah?.nama || 'SMK TINTA EMAS INDONESIA' }}</h1>
                            
                            <h3 v-if="raporData.sekolah?.akreditasi" class="text-[14px] font-bold uppercase m-0 leading-tight mt-1 block" style="transform: scaleX(1.05); font-weight: 800; -webkit-text-stroke: 0.3px black;">{{ raporData.sekolah.akreditasi }}</h3>
                            
                            <p class="text-[12px] mt-1.5 mb-0 leading-snug font-medium block" style="transform: scaleX(1.02);">
                                {{ raporData.sekolah?.alamat || '-' }} Kel./Kec. {{ raporData.sekolah?.kecamatan || '-' }} {{ raporData.sekolah?.kota || '-' }} {{ raporData.sekolah?.kode_pos || '' }}<br>
                                website : {{ raporData.sekolah?.website || '-' }} Telp. {{ raporData.sekolah?.telepon || '-' }}<br>
                                NPSN:{{ raporData.sekolah?.npsn || '-' }}
                            </p>
                        </div>
                        
                        <!-- Logo Kanan -->
                        <div class="w-[110px] h-[110px] flex-shrink-0 flex items-center justify-center">
                            <img v-if="raporData.sekolah?.logo" :src="raporData.sekolah.logo" alt="Logo Sekolah" class="max-w-full max-h-full object-contain">
                            <div v-else class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center text-[9px] text-slate-400 border border-dashed border-slate-300">Logo<br>Kanan</div>
                        </div>
                    </div>
                    <!-- Garis Ganda Kop Surat -->
                    <div class="w-full border-t-[3px] border-black mb-[1px]"></div>
                    <div class="w-full border-t border-black mb-6"></div>

                    <!-- TITLE RAPOR -->
                    <div class="text-center font-bold text-[16px] mb-8 uppercase underline" style="font-family: 'Times New Roman', Times, serif;">
                        {{ raporData?.nama_periode_panjang || raporData?.nama_periode || 'PENILAIAN SUMATIF AKHIR SEMESTER' }}
                    </div>

                    <!-- MASTER TABLE UNTUK 3 HALAMAN -->
                    <table class="w-full">
                        <!-- THEAD: KOP RAPOR (Berulang di setiap halaman cetak) -->
                        <thead class="print:table-header-group">
                            <tr>
                                <td class="pb-2 print:pt-8">
                                    <!-- KOP RAPOR -->
                                    <div class="mb-1">
                                        <table class="w-full text-[12px] leading-tight" style="font-family: 'Times New Roman', Times, serif;">
                                            <tbody>
                                                <tr>
                                                    <td class="w-36 py-0">Nama Peserta Didik</td>
                                                    <td class="w-2 py-0">:</td>
                                                    <td class="font-bold py-0">{{ raporData.siswa?.nama }}</td>
                                                    
                                                    <td class="w-28 py-0 pl-4">Kelas</td>
                                                    <td class="w-2 py-0">:</td>
                                                    <td class="py-0">{{ (raporData?.is_tengah_semester) ? raporData.kelas_full : raporData.kelas }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="py-0">NIS / NISN</td>
                                                    <td class="py-0">:</td>
                                                    <td class="py-0">{{ raporData.siswa?.nis }} / {{ raporData.siswa?.nisn }}</td>
                                                    
                                                    <td class="py-0 pl-4">Fase</td>
                                                    <td class="py-0">:</td>
                                                    <td class="py-0">{{ raporData.siswa?.fase }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="py-0">Sekolah</td>
                                                    <td class="py-0">:</td>
                                                    <td class="py-0 uppercase">{{ raporData.sekolah?.nama }}</td>
                                                    
                                                    <td class="py-0 pl-4">Semester</td>
                                                    <td class="py-0">:</td>
                                                    <td class="py-0">{{ raporData.semester }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="py-0 align-top">Alamat</td>
                                                    <td class="py-0 align-top">:</td>
                                                    <td class="py-0 align-top pr-4">
                                                        {{ raporData.sekolah?.alamat_line1 }}<br>
                                                        {{ raporData.sekolah?.alamat_line2 }}
                                                    </td>
                                                    
                                                    <td class="py-0 pl-4 align-top">Tahun Pelajaran</td>
                                                    <td class="py-0 align-top">:</td>
                                                    <td class="py-0 align-top">{{ raporData.tahun_ajaran }}</td>
                                                </tr>
                                                <!-- Spacer -->
                                                <tr><td colspan="6" class="h-2"></td></tr>
                                                
                                                <template v-if="!(raporData?.is_tengah_semester)">
                                                    <tr v-if="raporData.keahlian?.bidang && raporData.keahlian?.bidang !== '-'">
                                                        <td class="py-0">Bidang Keahlian</td>
                                                        <td class="py-0">:</td>
                                                        <td class="py-0 italic" colspan="4">{{ raporData.keahlian?.bidang }}</td>
                                                    </tr>
                                                    <tr v-if="raporData.keahlian?.program && raporData.keahlian?.program !== '-'">
                                                        <td class="py-0">Program Keahlian</td>
                                                        <td class="py-0">:</td>
                                                        <td class="py-0 italic" colspan="4">{{ raporData.keahlian?.program }}</td>
                                                    </tr>
                                                    <tr v-if="raporData.keahlian?.konsentrasi && raporData.keahlian?.konsentrasi !== '-'">
                                                        <td class="py-0">Konsentrasi Keahlian</td>
                                                        <td class="py-0">:</td>
                                                        <td class="py-0 italic" colspan="4">{{ raporData.keahlian?.konsentrasi }}</td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- DOUBLE BORDER BOTTOM UNTUK KOP RAPOR -->
                                    <div v-if="!(raporData?.is_tengah_semester)" class="border-t-[3px] border-slate-900 border-b border-slate-900 h-1 mb-2 w-full mt-2"></div>
                                </td>
                            </tr>
                        </thead>

                        <!-- HALAMAN 1: MAPEL UMUM -->
                        <tbody>
                            <tr>
                                <td>
                                    <div class="font-bold text-[13px] mb-2" :class="{'mt-4': raporData?.is_tengah_semester}" style="font-family: 'Times New Roman', Times, serif;">
                                        I. <span class="ml-2">INTRAKURIKULER</span>
                                    </div>

                                    <table class="w-full border-collapse border border-slate-900 mb-6" style="font-family: 'Times New Roman', Times, serif;">
                                        <thead>
                                            <tr class="bg-[#92d050]">
                                                <th class="border border-slate-900 py-1.5 px-2 w-10 text-center font-bold text-[12px]">No.</th>
                                                <th class="border border-slate-900 py-1.5 px-2 font-bold text-[13px]">Mata Pelajaran</th>
                                                <th class="border border-slate-900 py-1.5 px-2 w-20 text-center font-bold text-[13px]">Nilai</th>
                                                <th class="border border-slate-900 py-1.5 px-2 w-[45%] font-bold text-[13px]">Deskripsi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Loop Kelompok Mapel UMUM saja -->
                                            <template v-for="(grup, gIndex) in raporData.rapor_mapel?.filter(g => g.kelompok.toLowerCase().includes('umum') || g.kelompok.toLowerCase().includes('nasional') || g.kelompok.toLowerCase().includes('kewilayahan') || g.kelompok.toLowerCase().includes('kelompok a'))" :key="'u'+gIndex">
                                                <tr>
                                                    <td colspan="4" class="border border-slate-900 py-1 px-2 font-bold bg-[#e7e6e6] text-[12px]">
                                                        {{ grup.kelompok }}
                                                    </td>
                                                </tr>
                                                <tr v-for="(mapel, mIndex) in grup.mapels" :key="mapel.id" class="h-[75px]">
                                                    <td class="border border-slate-800 py-2 px-2 text-center align-middle">{{ mIndex + 1 }}</td>
                                                    <td class="border border-slate-800 py-2 px-2 font-semibold align-middle">{{ mapel.nama_mapel }}</td>
                                                    <td class="border border-slate-800 py-2 px-2 text-center font-bold align-middle text-sm">{{ mapel.nilai }}</td>
                                                    <td class="border border-slate-800 py-2 px-2 text-[10px] text-justify align-middle leading-tight">
                                                        {{ mapel.deskripsi }}
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>

                        <!-- HALAMAN 2: MAPEL KEJURUAN, DLL -->
                        <tbody style="page-break-before: always; break-before: page;">
                            <tr>
                                <td class="pt-4">
                                    <table class="w-full border-collapse border border-slate-900 mb-6" style="font-family: 'Times New Roman', Times, serif;">
                                        <thead>
                                            <tr class="bg-[#92d050]">
                                                <th class="border border-slate-900 py-1.5 px-2 w-10 text-center font-bold text-[12px]">No.</th>
                                                <th class="border border-slate-900 py-1.5 px-2 font-bold text-[13px]">Mata Pelajaran</th>
                                                <th class="border border-slate-900 py-1.5 px-2 w-20 text-center font-bold text-[13px]">Nilai</th>
                                                <th class="border border-slate-900 py-1.5 px-2 w-[45%] font-bold text-[13px]">Deskripsi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Loop Kelompok Mapel KEJURUAN DLL -->
                                            <template v-for="(grup, gIndex) in raporData.rapor_mapel?.filter(g => !(g.kelompok.toLowerCase().includes('umum') || g.kelompok.toLowerCase().includes('nasional') || g.kelompok.toLowerCase().includes('kewilayahan') || g.kelompok.toLowerCase().includes('kelompok a')))" :key="'k'+gIndex">
                                                <tr>
                                                    <td colspan="4" class="border border-slate-900 py-1 px-2 font-bold bg-[#e7e6e6] text-[12px]">
                                                        {{ grup.kelompok }}
                                                    </td>
                                                </tr>
                                                <tr v-for="(mapel, mIndex) in grup.mapels" :key="mapel.id" class="h-[75px]">
                                                    <td class="border border-slate-800 py-2 px-2 text-center align-middle">{{ mIndex + 1 }}</td>
                                                    <td class="border border-slate-800 py-2 px-2 font-semibold align-middle">{{ mapel.nama_mapel }}</td>
                                                    <td class="border border-slate-800 py-2 px-2 text-center font-bold align-middle text-sm">{{ mapel.nilai }}</td>
                                                    <td class="border border-slate-800 py-2 px-2 text-[10px] text-justify align-middle leading-tight">
                                                        {{ mapel.deskripsi }}
                                                    </td>
                                                </tr>
                                            </template>
                                            
                                            <!-- STATISTIK (Hanya di akhir tabel mapel) -->
                                            <tr>
                                                <td colspan="2" class="border border-slate-900 py-1.5 px-2 text-center font-bold text-[12px] bg-[#e7e6e6]">JUMLAH</td>
                                                <td colspan="2" class="border border-slate-900 py-1.5 px-2 text-center font-bold text-[13px] bg-[#e7e6e6]">{{ raporData.statistik?.jumlah }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="border border-slate-900 py-1.5 px-2 text-center font-bold text-[12px] bg-[#e7e6e6]">RATA-RATA</td>
                                                <td colspan="2" class="border border-slate-900 py-1.5 px-2 text-center font-bold text-[13px] bg-[#e7e6e6]">{{ raporData.statistik?.rata_rata }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="border border-slate-900 py-1.5 px-2 text-center font-bold text-[12px] bg-[#e7e6e6]">PERINGKAT</td>
                                                <td colspan="2" class="border border-slate-900 py-1.5 px-2 text-center font-bold italic text-[13px]">{{ raporData.statistik?.peringkat }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>

                        <!-- HALAMAN 3: KOKURIKULER DLL -->
                        <tbody style="page-break-before: always; break-before: page;">
                            <tr>
                                <td class="pt-4">
                                    <div class="h-2"></div>
                                    <!-- KOKURIKULER (Hanya untuk PSAS/PSAT) -->
                                    <template v-if="!(raporData?.is_tengah_semester)">
                                        <div class="font-bold text-[13px] mb-2" style="font-family: 'Times New Roman', Times, serif;">
                                            II. <span class="ml-2">KOKURIKULER</span>
                                        </div>
                                        <div class="border border-slate-900 py-2 px-4 mb-6 text-[12px] italic text-justify leading-relaxed" style="font-family: 'Times New Roman', Times, serif; min-height: 100px;">
                                            {{ raporData.kokurikuler || '-' }}
                                        </div>
                                    </template>

                                    <!-- EKSTRAKURIKULER -->
                                    <div class="font-bold text-[13px] mb-2" style="font-family: 'Times New Roman', Times, serif;">
                                        {{ (raporData?.is_tengah_semester) ? 'II.' : 'III.' }} <span class="ml-2">EKSTRA KURIKULER</span>
                                    </div>
                                    <table class="w-full border-collapse border border-slate-900 mb-6" style="font-family: 'Times New Roman', Times, serif;">
                                        <thead>
                                            <tr class="bg-[#92d050]">
                                                <th class="border border-slate-900 py-1.5 px-2 w-10 text-center font-bold text-[12px]">No.</th>
                                                <th class="border border-slate-900 py-1.5 px-2 w-1/3 text-center font-bold text-[13px]">Ekstrakurikuler</th>
                                                <th class="border border-slate-900 py-1.5 px-2 font-bold text-[13px]">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Wajib -->
                                            <tr>
                                                <td colspan="3" class="border border-slate-900 py-1 px-2 font-bold bg-[#e7e6e6] text-[13px]">Wajib</td>
                                            </tr>
                                            <tr v-for="(eks, i) in raporData.ekskuls?.wajib" :key="'w'+i">
                                                <td class="border border-slate-900 py-1.5 px-2 text-center align-top">{{ i + 1 }}</td>
                                                <td class="border border-slate-900 py-1.5 px-2 align-top">{{ eks.nama }}</td>
                                                <td class="border border-slate-900 py-1 px-2 text-[12px] text-justify align-top leading-tight">{{ eks.deskripsi }}</td>
                                            </tr>
                                            <tr v-if="!raporData.ekskuls?.wajib?.length">
                                                <td colspan="3" class="border border-slate-900 py-1.5 px-2 text-center italic text-slate-500">- Tidak ada data ekskul wajib -</td>
                                            </tr>

                                            <!-- Pilihan -->
                                            <tr>
                                                <td colspan="3" class="border border-slate-900 py-1 px-2 font-bold bg-[#e7e6e6] text-[13px]">Pilihan</td>
                                            </tr>
                                            <tr v-for="(eks, i) in raporData.ekskuls?.pilihan" :key="'p'+i">
                                                <td class="border border-slate-900 py-1.5 px-2 text-center align-top">{{ (raporData.ekskuls?.wajib?.length || 0) + i + 1 }}</td>
                                                <td class="border border-slate-900 py-1.5 px-2 align-top">{{ eks.nama }}</td>
                                                <td class="border border-slate-900 py-1 px-2 text-[12px] text-justify align-top leading-tight">{{ eks.deskripsi }}</td>
                                            </tr>
                                            <tr v-if="!raporData.ekskuls?.pilihan?.length">
                                                <td colspan="3" class="border border-slate-900 py-1.5 px-2 text-center italic text-slate-500">- Tidak ada data ekskul pilihan -</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- KETIDAKHADIRAN & POIN -->
                                    <div class="font-bold text-[13px] mb-2" style="font-family: 'Times New Roman', Times, serif;">
                                        {{ (raporData?.is_tengah_semester) ? 'III.' : 'IV.' }} <span class="ml-2">KETIDAKHADIRAN & POIN</span>
                                    </div>
                                    <table class="w-full border-collapse border border-slate-900 mb-8" style="font-family: 'Times New Roman', Times, serif;">
                                        <thead>
                                            <tr class="bg-[#92d050]">
                                                <th class="border border-slate-900 py-1.5 px-2 w-10 text-center font-bold text-[12px]">No.</th>
                                                <th class="border border-slate-900 py-1.5 px-2 w-1/3 text-center font-bold text-[13px]" colspan="2">Ketidakhadiran</th>
                                                <th class="border border-slate-900 py-1.5 px-2 w-16 text-center font-bold text-[13px]">Poin</th>
                                                <th class="border border-slate-900 py-1.5 px-2 text-center font-bold text-[13px]">Catatan Wali Kelas</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="border border-slate-900 py-1.5 px-2 text-center align-middle">1</td>
                                                <td class="border border-slate-900 py-1.5 px-2">Sakit</td>
                                                <td class="border border-slate-900 py-1.5 px-2 text-center">{{ raporData.absensi?.s }} hari</td>
                                                <td rowspan="3" class="border border-slate-900 py-1.5 px-2 text-center font-bold text-[16px] align-middle">{{ raporData.poin }}</td>
                                                <td rowspan="3" class="border border-slate-900 py-1 px-2 text-[12px] text-justify align-top leading-tight">{{ raporData.catatan || '- Tidak ada catatan -' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="border border-slate-900 py-1.5 px-2 text-center align-middle">2</td>
                                                <td class="border border-slate-900 py-1.5 px-2">Izin</td>
                                                <td class="border border-slate-900 py-1.5 px-2 text-center">{{ raporData.absensi?.i }} hari</td>
                                            </tr>
                                            <tr>
                                                <td class="border border-slate-900 py-1.5 px-2 text-center align-middle">3</td>
                                                <td class="border border-slate-900 py-1.5 px-2">Tanpa Keterangan</td>
                                                <td class="border border-slate-900 py-1.5 px-2 text-center">{{ raporData.absensi?.a }} hari</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- TTD -->
                                    <div class="flex justify-between mt-12 text-[11px] font-sans px-8" style="font-family: 'Times New Roman', Times, serif;">
                                        <div class="text-center mt-4">
                                            Mengetahui,<br>
                                            Orang Tua / Wali<br><br><br><br>
                                            <span class="border-b border-slate-900 pb-0.5 inline-block w-40"></span>
                                        </div>
                                        <div class="text-left">
                                            {{ raporData.sekolah?.alamat_line2 ? raporData.sekolah.alamat_line2.split('-')[0].trim() : 'Bekasi' }}, {{ raporData.tanggal_cetak }}<br>
                                            Wali Kelas<br><br><br><br>
                                            <span class="border-b border-slate-900 pb-0.5 font-bold inline-block min-w-[12rem]">{{ pageData.walas?.guru?.biodata?.nama_lengkap || pageData.walas?.guru?.name }}</span><br>
                                            NIP. {{ pageData.walas?.guru?.biodata?.nip || '-' }}
                                        </div>
                                    </div>
                                    <div v-if="!(raporData?.is_tengah_semester)" class="text-center mt-8 text-[11px] font-sans">
                                        Mengetahui,<br>
                                        Kepala Sekolah<br><br><br><br>
                                        <span class="border-b border-slate-800 pb-0.5 font-bold inline-block w-48">{{ raporData.sekolah?.kepsek }}</span><br>
                                        NIP. {{ raporData.sekolah?.nip_kepsek }}
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'

definePageMeta({
  layout: 'guru',
  middleware: 'guru'
})

const pageData = ref(null)
const mode = ref('rapor') // 'leger' | 'rapor'
const activeTitimangsaId = ref('')
const isLoading = ref(true)
const errorMessage = ref('')
const searchQuery = ref('')

// Preview State
const showPreview = ref(false)
const raporData = ref(null)

// Leger State
const legerData = ref(null)
const isLoadingLeger = ref(false)

const filteredStudents = computed(() => {
    if (!pageData.value || !pageData.value.siswas) return []
    if (!searchQuery.value) return pageData.value.siswas
    const q = searchQuery.value.toLowerCase()
    return pageData.value.siswas.filter(s => 
        (s.user?.name && s.user.name.toLowerCase().includes(q)) || 
        (s.nama_lengkap && s.nama_lengkap.toLowerCase().includes(q))
    )
})

const loadData = async () => {
    isLoading.value = true
    errorMessage.value = ''
    try {
        const token = useCookie('auth_token').value
        
        const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/guru/walas/cetak`, {
            headers: { 'Authorization': `Bearer ${token}` }
        })

        if (res.success) {
            pageData.value = res.data
            
            // Set default active titimangsa
            if (res.data.titimangsas && res.data.titimangsas.length > 0) {
                const active = res.data.titimangsas.find(t => t.is_aktif)
                activeTitimangsaId.value = active ? active.id : res.data.titimangsas[res.data.titimangsas.length - 1].id
            }
        } else {
            errorMessage.value = res.message || 'Respons API gagal tanpa pesan error.'
        }
    } catch (error) {
        console.error("Gagal memuat data cetak:", error)
        errorMessage.value = error.message || 'Terjadi kesalahan jaringan atau server.'
    } finally {
        isLoading.value = false
    }
}

const bukaPreviewRapor = async (siswa) => {
    raporData.value = null
    showPreview.value = true
    try {
        const token = useCookie('auth_token').value
        
        const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/guru/walas/cetak/rapor`, {
            params: {
                siswa_id: siswa.id,
                titimangsa_id: activeTitimangsaId.value
            },
            headers: { 'Authorization': `Bearer ${token}` }
        })

        if (res.success) {
            raporData.value = res.data
        }
    } catch (error) {
        console.error("Gagal memuat rapor:", error)
        useNuxtApp().$swal.fire({
            title: 'Gagal',
            text: 'Terjadi kesalahan saat menarik data rapor.',
            icon: 'error',
            confirmButtonColor: '#ef4444'
        })
        showPreview.value = false
    }
}

const printRapor = () => {
    const originalArea = document.getElementById('rapor-print-area');
    const clone = originalArea.cloneNode(true);
    clone.id = 'temp-rapor-print-area';
    clone.style.margin = '0';
    clone.style.boxShadow = 'none';
    clone.style.maxWidth = '100%';
    
    const printContainer = document.createElement('div');
    printContainer.id = 'temp-print-container';
    printContainer.appendChild(clone);
    document.body.appendChild(printContainer);

    const isPSTS = raporData.value?.is_tengah_semester;
    const pageSize = isPSTS ? '215.9mm 330.2mm' : 'A4';

    const style = document.createElement('style');
    style.innerHTML = `
        @media print {
            body > *:not(#temp-print-container) { display: none !important; }
            #temp-print-container { display: block !important; position: absolute; left: 0; top: 0; width: 100%; background: white; }
            @page { size: ${pageSize}; margin: 15mm; }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
        }
    `;
    document.head.appendChild(style);

    window.print();

    document.body.removeChild(printContainer);
    document.head.removeChild(style);
}

const printLeger = () => {
    const originalArea = document.getElementById('leger-print-area');
    const clone = originalArea.cloneNode(true);
    clone.id = 'temp-leger-print-area';
    clone.style.margin = '0';
    clone.style.boxShadow = 'none';
    clone.style.maxWidth = '100%';
    
    const printContainer = document.createElement('div');
    printContainer.id = 'temp-print-container';
    printContainer.appendChild(clone);
    document.body.appendChild(printContainer);

    const style = document.createElement('style');
    style.innerHTML = `
        @media print {
            body > *:not(#temp-print-container) { display: none !important; }
            #temp-print-container { display: block !important; position: absolute; left: 0; top: 0; width: 100%; background: white; }
            @page { size: A4 landscape; margin: 10mm; }
            .vertical-text { 
                white-space: normal !important; 
                word-wrap: break-word !important; 
                transform: rotate(180deg) !important; 
            }
            #temp-leger-print-area table {
                width: 100% !important;
                min-width: 100% !important;
                max-width: none !important;
            }
        }
    `;
    document.head.appendChild(style);

    window.print();

    document.body.removeChild(printContainer);
    document.head.removeChild(style);
}

const loadLegerData = async () => {
    isLoadingLeger.value = true
    try {
        const token = useCookie('auth_token').value
        
        const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/guru/walas/cetak/leger`, {
            params: {
                titimangsa_id: activeTitimangsaId.value
            },
            headers: { 'Authorization': `Bearer ${token}` }
        })

        if (res.success) {
            legerData.value = res.data
        }
    } catch (error) {
        console.error("Gagal memuat data leger:", error)
        useNuxtApp().$swal.fire({
            title: 'Gagal',
            text: 'Terjadi kesalahan saat menarik data Leger.',
            icon: 'error',
            confirmButtonColor: '#ef4444'
        })
    } finally {
        isLoadingLeger.value = false
    }
}

watch(activeTitimangsaId, () => {
    if (mode.value === 'leger') {
        loadLegerData()
    }
})

watch(mode, (newMode) => {
    if (newMode === 'leger' && !legerData.value) {
        loadLegerData()
    }
})

onMounted(() => {
    loadData()
})
</script>

<style>
/* Animasi */
@keyframes scaleIn {
    from { opacity: 0; transform: scale(0.95) translateY(-10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
.animate-scaleIn {
    animation: scaleIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Scrollbar */
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #94a3b8; }

/* Penyesuaian agar tampilan modal mirip kertas di monitor, tapi print-friendly saat dicetak */
.print-bg-white { background-color: white; }
.print-p-0 { padding: 0; }
.print-shadow-none { box-shadow: none !important; }
.print-w-full { width: 100% !important; }
.print-max-w-none { max-width: none !important; }
.print-min-h-0 { min-height: 0 !important; }

/* Custom class for vertical header text in Leger */
.vertical-text {
    writing-mode: vertical-rl;
    transform: rotate(180deg);
}

@media print {
    @page {
        size: A4 portrait;
        margin: 0; /* Mengunci Margin menjadi Tidak Ada secara default */
    }
    
    body {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>
