<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Cetak Rapor & Leger</h2>
        <p class="text-sm font-semibold text-slate-500 mt-1 mb-3">Pilih siswa untuk mencetak buku rapor individu.</p>

        <!-- Compact Info Badges -->
        <div v-if="pageData" class="flex flex-wrap items-center gap-2 animate-fadeIn">
            <div class="px-3 py-1.5 bg-white shadow-sm rounded-lg flex items-center gap-2 border border-slate-200">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Kelas:</span>
                <span class="text-xs font-bold text-slate-700">{{ pageData.kelas?.tingkat }} {{ pageData.kelas?.nama_kelas }}</span>
            </div>
            <div class="px-3 py-1.5 bg-white shadow-sm rounded-lg flex items-center gap-2 border border-slate-200">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Tahun:</span>
                <span class="text-xs font-bold text-slate-700">{{ pageData.tahun_ajaran?.tahun }}</span>
            </div>
        </div>
      </div>
    </div>

    <!-- Main Card -->
    <div v-if="pageData" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
      <!-- Toolbar -->
      <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-4">
        
        <!-- Filter Titimangsa -->
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
            <svg xmlns="http://www.w3.org/-2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <div>
            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-0.5">Periode Titimangsa</label>
            <select 
              v-model="activeTitimangsaId"
              class="w-48 text-sm font-bold text-slate-700 bg-white border border-slate-200 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-indigo-500 outline-none"
            >
              <option v-for="t in pageData.titimangsas" :key="t.id" :value="t.id">
                {{ t.nama_periode }} {{ !t.is_aktif ? '(Ditutup)' : '' }}
              </option>
            </select>
          </div>
        </div>

        <!-- Switch Leger / Rapor -->
        <div class="bg-slate-200 p-1 rounded-lg flex items-center">
            <button 
                @click="mode = 'rapor'"
                :class="mode === 'rapor' ? 'bg-white text-indigo-700 shadow-sm font-bold' : 'text-slate-500 font-medium hover:text-slate-700'"
                class="px-4 py-1.5 rounded-md text-xs transition-all duration-200"
            >
                Rapor Individu
            </button>
            <button 
                @click="mode = 'leger'"
                :class="mode === 'leger' ? 'bg-white text-indigo-700 shadow-sm font-bold' : 'text-slate-500 font-medium hover:text-slate-700'"
                class="px-4 py-1.5 rounded-md text-xs transition-all duration-200"
            >
                Tabel Leger
            </button>
        </div>
      </div>

      <!-- Content Rapor -->
      <div v-if="mode === 'rapor'">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50 border-b border-slate-200">
                <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-16 text-center">No</th>
                <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-32">NISN / NIS</th>
                <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Siswa</th>
                <th class="py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider w-32 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr 
                v-for="(siswa, index) in pageData.siswas" 
                :key="siswa.id"
                class="hover:bg-slate-50/50 transition-colors group"
              >
                <td class="py-3 px-4 text-sm font-medium text-slate-400 text-center">{{ index + 1 }}</td>
                <td class="py-3 px-4 text-sm font-semibold text-slate-600">{{ siswa.nisn }} / {{ siswa.nis }}</td>
                <td class="py-3 px-4 text-sm font-bold text-slate-800">{{ siswa.user?.name }}</td>
                <td class="py-3 px-4 text-center">
                  <button 
                    @click="bukaPreviewRapor(siswa)"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-lg text-xs font-bold transition-all shadow-sm opacity-0 group-hover:opacity-100"
                  >
                    <svg xmlns="http://www.w3.org/-2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Preview
                  </button>
                </td>
              </tr>
              <tr v-if="pageData.siswas.length === 0">
                <td colspan="4" class="py-12 text-center text-slate-400 font-medium">Tidak ada data siswa di kelas ini.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Content Leger -->
      <div v-else class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-slate-700">Tabel Leger Kelas</h3>
            <button @click="printLeger" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold transition-colors shadow-sm flex items-center gap-2" :disabled="!legerData || isLoadingLeger">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Leger
            </button>
        </div>

        <div v-if="isLoadingLeger" class="flex flex-col items-center justify-center py-20">
            <div class="w-10 h-10 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
            <p class="mt-4 text-sm font-semibold text-slate-500 animate-pulse">Memuat data leger...</p>
        </div>
        
        <div v-else-if="legerData" class="overflow-x-auto border border-slate-200 rounded-xl bg-white shadow-sm" id="leger-print-area">
            <div class="hidden print:block mb-6 w-full text-center">
                <h2 class="text-xl font-black uppercase text-black border-b-[3px] border-black inline-block pb-1 mb-2 tracking-widest">LEGER NILAI PESERTA DIDIK</h2>
                <div class="flex justify-center gap-12 text-[11px] font-black text-black uppercase tracking-widest mt-1">
                    <p>KELAS: {{ legerData.kelas?.nama_kelas || '-' }}</p>
                    <p>PERIODE: {{ legerData.titimangsa?.nama_periode || '-' }}</p>
                </div>
            </div>

            <table class="w-full text-left border-collapse min-w-[1200px] print:min-w-0 table-fixed">
                <thead class="sticky top-0 z-20 print:static">
                    <tr class="text-[10px] font-bold uppercase tracking-widest text-slate-700 bg-slate-100 print:text-[8px] print:text-black">
                        <th rowspan="2" class="w-[20%] py-2 px-4 border border-slate-300 align-middle text-center sticky left-0 bg-slate-100 z-30 print:static print:bg-transparent print:border-black">NAMA SISWA</th>
                        <template v-for="(grup, key) in legerData.dataTabel" :key="key">
                            <th v-if="grup.items.length > 0" :colspan="grup.items.length" class="py-2 px-2 border border-slate-300 text-center bg-indigo-50 text-indigo-700 print:bg-transparent print:border-black print:text-black">
                                {{ grup.nama_kelompok }}
                            </th>
                        </template>
                        <th colspan="3" class="py-2 px-2 border border-slate-300 text-center bg-slate-800 text-white print:bg-slate-300 print:text-black print:border-black">REKAPITULASI</th>
                    </tr>
                    <tr class="text-[9px] font-bold uppercase print:text-[7px] print:text-black">
                        <template v-for="(grup, key) in legerData.dataTabel" :key="key">
                            <template v-for="item in grup.items" :key="item.id">
                                <th class="py-2 px-1 border border-slate-300 bg-indigo-50/50 text-indigo-800 align-bottom h-40 print:h-32 print:bg-transparent print:border-black print:text-black text-center">
                                    <div class="whitespace-normal text-left mx-auto vertical-text leading-[1.1] w-full">
                                        {{ item.nama_mapel }}
                                    </div>
                                </th>
                            </template>
                        </template>
                        <th class="w-[5%] py-2 px-1 border border-slate-300 text-center align-middle bg-slate-200 print:bg-transparent print:border-black print:text-black">JML</th>
                        <th class="w-[5%] py-2 px-1 border border-slate-300 text-center align-middle bg-slate-200 print:bg-transparent print:border-black print:text-black">RATA</th>
                        <th class="w-[5%] py-2 px-1 border border-slate-300 text-center align-middle bg-slate-200 print:bg-transparent print:border-black print:text-black">RANK</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="siswa in legerData.siswas" :key="siswa.id" class="hover:bg-indigo-50/30 print:break-inside-avoid">
                        <td class="py-1.5 px-4 text-xs font-bold text-slate-700 border border-slate-300 sticky left-0 bg-white shadow-[1px_0_0_0_#cbd5e1] print:static print:bg-transparent print:border-black print:shadow-none print:text-[9px] print:text-black">
                            {{ siswa.user?.name || '-' }}
                        </td>
                        <template v-for="(grup, key) in legerData.dataTabel" :key="key">
                            <template v-for="item in grup.items" :key="item.id">
                                <td class="py-1.5 px-1 border border-slate-300 text-center text-[11px] text-slate-600 font-medium print:border-black print:text-[8px] print:text-black">
                                    {{ legerData.nilaiMatriks[siswa.id] && legerData.nilaiMatriks[siswa.id][item.id] !== undefined ? legerData.nilaiMatriks[siswa.id][item.id] : '-' }}
                                </td>
                            </template>
                        </template>
                        <td class="py-1.5 px-2 border border-slate-300 text-center text-[11px] font-bold text-slate-800 bg-slate-50 print:bg-transparent print:border-black print:text-[9px] print:text-black">
                            {{ legerData.rekapSiswa[siswa.id]?.jumlah || '-' }}
                        </td>
                        <td class="py-1.5 px-2 border border-slate-300 text-center text-[11px] font-bold text-indigo-700 bg-indigo-50/50 print:bg-transparent print:border-black print:text-[9px] print:text-black">
                            {{ legerData.rekapSiswa[siswa.id]?.rata || '-' }}
                        </td>
                        <td class="py-1.5 px-2 border border-slate-300 text-center text-[11px] font-bold text-rose-600 bg-rose-50/50 print:bg-transparent print:border-black print:text-[9px] print:text-black">
                            {{ legerData.rekapSiswa[siswa.id]?.rank || '-' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else class="py-12 text-center text-slate-500">
            Pilih periode untuk memuat data leger.
        </div>
      </div>

    </div>

    <!-- Loading State -->
    <div v-else-if="isLoading" class="flex flex-col items-center justify-center py-20">
      <div class="w-10 h-10 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
      <p class="mt-4 text-sm font-semibold text-slate-500 animate-pulse">Memuat data kelas...</p>
    </div>

    <!-- Error State -->
    <div v-else class="flex flex-col items-center justify-center py-20 text-center">
      <div class="text-red-500 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>
      <h3 class="text-lg font-bold text-slate-800">Gagal Memuat Data</h3>
      <p class="text-sm text-slate-500 mt-1 max-w-md">{{ errorMessage }}</p>
      <button @click="loadData" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold shadow-sm hover:bg-indigo-700">Coba Lagi</button>
    </div>

    <!-- PREVIEW RAPOR MODAL -->
    <div v-if="showPreview && raporData" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-hidden">
        <div class="bg-slate-200 w-full max-w-5xl h-[95vh] rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-fadeInUp">
            
            <!-- Modal Header -->
            <div class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between shrink-0">
                <div>
                    <h3 class="text-lg font-black text-slate-800">Preview Rapor Siswa</h3>
                    <p class="text-xs font-semibold text-slate-500">{{ raporData.siswa?.nama }} - {{ raporData.kelas }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="printRapor" class="px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700 rounded-lg text-sm font-bold shadow-sm transition-colors flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/-2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak PDF
                    </button>
                    <button @click="showPreview = false" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/-2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body (A4 Paper View Container) -->
            <div class="flex-1 overflow-y-auto p-4 md:p-8 bg-slate-200 print-bg-white print-p-0 print-overflow-visible">
                <!-- A4 Paper Content -->
                <div id="rapor-print-area" class="bg-white mx-auto shadow-lg max-w-[210mm] min-h-[297mm] p-8 md:p-12 print-shadow-none print-w-full print-max-w-none print-min-h-0 text-slate-800 font-serif text-[12px] leading-tight">
                    
                    <!-- TITLE RAPOR -->
                    <div v-if="raporData?.nama_periode && raporData.nama_periode.includes('PSTS')" class="text-center font-bold text-[16px] mb-8 uppercase underline" style="font-family: 'Times New Roman', Times, serif;">
                        PENILAIAN SUMATIF TENGAH SEMESTER
                    </div>
                    <template v-else>
                        <div class="text-center font-bold text-lg mb-2 uppercase underline" style="font-family: 'Times New Roman', Times, serif;">
                            PENILAIAN SUMATIF AKHIR SEMESTER
                        </div>
                        <!-- DOUBLE BORDER TOP -->
                        <div class="border-t-2 border-slate-900 border-b border-slate-900 h-1 mb-4 w-full"></div>
                    </template>

                    <!-- KOP RAPOR -->
                    <div class="mb-4">
                        <table class="w-full text-[12px]" style="font-family: 'Times New Roman', Times, serif;">
                            <tbody>
                                <tr>
                                    <td class="w-36 py-0.5">Nama Peserta Didik</td>
                                    <td class="w-2 py-0.5">:</td>
                                    <td class="font-bold py-0.5">{{ raporData.siswa?.nama }}</td>
                                    
                                    <td class="w-28 py-0.5 pl-4">Kelas</td>
                                    <td class="w-2 py-0.5">:</td>
                                    <td class="py-0.5">{{ (raporData?.nama_periode && raporData.nama_periode.includes('PSTS')) ? raporData.kelas_full : raporData.kelas }}</td>
                                </tr>
                                <tr>
                                    <td class="py-0.5">NIS / NISN</td>
                                    <td class="py-0.5">:</td>
                                    <td class="py-0.5">{{ raporData.siswa?.nis }} / {{ raporData.siswa?.nisn }}</td>
                                    
                                    <td class="py-0.5 pl-4">Fase</td>
                                    <td class="py-0.5">:</td>
                                    <td class="py-0.5">{{ raporData.siswa?.fase }}</td>
                                </tr>
                                <tr>
                                    <td class="py-0.5">Sekolah</td>
                                    <td class="py-0.5">:</td>
                                    <td class="py-0.5 uppercase">{{ raporData.sekolah?.nama }}</td>
                                    
                                    <td class="py-0.5 pl-4">Semester</td>
                                    <td class="py-0.5">:</td>
                                    <td class="py-0.5">{{ raporData.semester }}</td>
                                </tr>
                                <tr>
                                    <td class="py-0.5 align-top">Alamat</td>
                                    <td class="py-0.5 align-top">:</td>
                                    <td class="py-0.5 align-top pr-4">
                                        {{ raporData.sekolah?.alamat_line1 }}<br>
                                        {{ raporData.sekolah?.alamat_line2 }}
                                    </td>
                                    
                                    <td class="py-0.5 pl-4 align-top">Tahun Pelajaran</td>
                                    <td class="py-0.5 align-top">:</td>
                                    <td class="py-0.5 align-top">{{ raporData.tahun_ajaran }}</td>
                                </tr>
                                <!-- Spacer -->
                                <tr><td colspan="6" class="h-4"></td></tr>
                                
                                <template v-if="!(raporData?.nama_periode && raporData.nama_periode.includes('PSTS'))">
                                    <tr v-if="raporData.keahlian?.bidang && raporData.keahlian?.bidang !== '-'">
                                        <td class="py-0.5">Bidang Keahlian</td>
                                        <td class="py-0.5">:</td>
                                        <td class="py-0.5 italic" colspan="4">{{ raporData.keahlian?.bidang }}</td>
                                    </tr>
                                    <tr v-if="raporData.keahlian?.program && raporData.keahlian?.program !== '-'">
                                        <td class="py-0.5">Program Keahlian</td>
                                        <td class="py-0.5">:</td>
                                        <td class="py-0.5 italic" colspan="4">{{ raporData.keahlian?.program }}</td>
                                    </tr>
                                    <tr v-if="raporData.keahlian?.konsentrasi && raporData.keahlian?.konsentrasi !== '-'">
                                        <td class="py-0.5">Konsentrasi Keahlian</td>
                                        <td class="py-0.5">:</td>
                                        <td class="py-0.5 italic" colspan="4">{{ raporData.keahlian?.konsentrasi }}</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- DOUBLE BORDER BOTTOM -->
                    <div v-if="!(raporData?.nama_periode && raporData.nama_periode.includes('PSTS'))" class="border-t-2 border-slate-900 border-b border-slate-900 h-1 mb-4 w-full"></div>

                    <div class="font-bold text-[13px] mb-2" :class="{'mt-6': raporData?.nama_periode && raporData.nama_periode.includes('PSTS')}" style="font-family: 'Times New Roman', Times, serif;">
                        I. <span class="ml-2">INTRAKURIKULER</span>
                    </div>

                    <!-- TABEL NILAI -->
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
                            <!-- Loop Kelompok Mapel -->
                            <template v-for="(grup, gIndex) in raporData.rapor_mapel" :key="gIndex">
                                <tr>
                                    <td colspan="4" class="border border-slate-900 py-1 px-2 font-bold bg-[#e2e2e2] text-[12px]">
                                        {{ grup.kelompok }}
                                    </td>
                                </tr>
                                <!-- Loop Mapel -->
                                <tr v-for="(mapel, mIndex) in grup.mapels" :key="mapel.id" class="h-[60px]">
                                    <td class="border border-slate-800 py-2 px-2 text-center align-middle">{{ mIndex + 1 }}</td>
                                    <td class="border border-slate-800 py-2 px-2 font-semibold align-middle">{{ mapel.nama_mapel }}</td>
                                    <td class="border border-slate-800 py-2 px-2 text-center font-bold align-middle text-sm">{{ mapel.nilai }}</td>
                                    <td class="border border-slate-800 py-2 px-2 text-[10px] text-justify align-middle leading-tight">
                                        {{ mapel.deskripsi }}
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="border border-slate-900 py-1.5 px-2 text-center font-bold text-[12px] bg-[#e2e2e2]">JUMLAH</td>
                                <td colspan="2" class="border border-slate-900 py-1.5 px-2 text-center font-bold text-[13px] bg-[#e2e2e2]">{{ raporData.statistik?.jumlah }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="border border-slate-900 py-1.5 px-2 text-center font-bold text-[12px] bg-[#e2e2e2]">RATA-RATA</td>
                                <td colspan="2" class="border border-slate-900 py-1.5 px-2 text-center font-bold text-[13px] bg-[#e2e2e2]">{{ raporData.statistik?.rata_rata }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="border border-slate-900 py-1.5 px-2 text-center font-bold text-[12px] bg-[#e2e2e2]">PERINGKAT</td>
                                <td colspan="2" class="border border-slate-900 py-1.5 px-2 text-center font-bold italic text-[13px] bg-[#e2e2e2]">{{ raporData.statistik?.peringkat }}</td>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- KOKURIKULER (Hanya untuk PSAS/PSAT) -->
                    <template v-if="!(raporData?.nama_periode && raporData.nama_periode.includes('PSTS'))">
                        <div class="font-bold text-[13px] mb-2" style="font-family: 'Times New Roman', Times, serif;">
                            II. <span class="ml-2">KOKURIKULER</span>
                        </div>
                        <div class="border border-slate-900 py-2 px-4 mb-6 text-[12px] italic text-justify leading-relaxed" style="font-family: 'Times New Roman', Times, serif; min-height: 60px;">
                            {{ raporData.kokurikuler || '-' }}
                        </div>
                    </template>

                    <!-- EKSTRAKURIKULER -->
                    <div class="font-bold text-[13px] mb-2" style="font-family: 'Times New Roman', Times, serif;">
                        {{ (raporData?.nama_periode && raporData.nama_periode.includes('PSTS')) ? 'II.' : 'III.' }} <span class="ml-2">EKSTRA KURIKULER</span>
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
                                <td colspan="3" class="border border-slate-900 py-1 px-2 font-bold bg-[#e2e2e2] text-[13px]">Wajib</td>
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
                                <td colspan="3" class="border border-slate-900 py-1 px-2 font-bold bg-[#e2e2e2] text-[13px]">Pilihan</td>
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
                        {{ (raporData?.nama_periode && raporData.nama_periode.includes('PSTS')) ? 'III.' : 'IV.' }} <span class="ml-2">KETIDAKHADIRAN & POIN</span>
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
                    <div v-if="!(raporData?.nama_periode && raporData.nama_periode.includes('PSTS'))" class="text-center mt-8 text-[11px] font-sans">
                        Mengetahui,<br>
                        Kepala Sekolah<br><br><br><br>
                        <span class="border-b border-slate-800 pb-0.5 font-bold inline-block w-48">{{ raporData.sekolah?.kepsek }}</span><br>
                        NIP. {{ raporData.sekolah?.nip_kepsek }}
                    </div>

                </div>
            </div>
        </div>
    </div>
    
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'

definePageMeta({
  layout: 'guru',
  middleware: 'guru'
})

const pageData = ref(null)
const mode = ref('rapor') // 'leger' | 'rapor'
const activeTitimangsaId = ref('')
const isLoading = ref(true)
const errorMessage = ref('')

// Preview State
const showPreview = ref(false)
const raporData = ref(null)

// Leger State
const legerData = ref(null)
const isLoadingLeger = ref(false)

const config = useRuntimeConfig()

const loadData = async () => {
    isLoading.value = true
    errorMessage.value = ''
    try {
        const token = useCookie('auth_token').value
        
        const res = await $fetch(`http://localhost:8000/api/guru/walas/cetak`, {
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
        
        const res = await $fetch(`http://localhost:8000/api/guru/walas/cetak/rapor`, {
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
        const { $swal } = useNuxtApp()
        $swal.fire({
            title: 'Gagal',
            text: 'Terjadi kesalahan saat menarik data rapor.',
            icon: 'error',
            confirmButtonColor: '#ef4444'
        })
        showPreview.value = false
    }
}

const printRapor = () => {
    const printContent = document.getElementById('rapor-print-area').innerHTML;
    const originalContent = document.body.innerHTML;

    // Apply specific print styles temporarily
    const style = document.createElement('style');
    style.innerHTML = `
        @media print {
            body * { visibility: hidden; position: static !important; transform: none !important; }
            #rapor-print-area, #rapor-print-area * { visibility: visible; }
            #rapor-print-area { 
                position: absolute !important; left: 0 !important; top: 0 !important; right: 0 !important;
                width: 100% !important; max-width: none !important; 
                margin: 0 !important; padding: 0 !important; box-shadow: none !important;
            }
            @page { size: A4; margin: 15mm; }
        }
    `;
    document.head.appendChild(style);

    window.print();

    // Clean up
    document.head.removeChild(style);
}

const printLeger = () => {
    const style = document.createElement('style');
    style.innerHTML = `
        @media print {
            body * { visibility: hidden; position: static !important; transform: none !important; }
            #leger-print-area, #leger-print-area * { visibility: visible; }
            #leger-print-area { 
                position: absolute !important; left: 0 !important; top: 0 !important; right: 0 !important;
                width: 100% !important; 
                max-width: none !important; 
                margin: 0 !important; 
                padding: 0 !important; 
                border: none !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                overflow: visible !important;
            }
            #leger-print-area table {
                width: 100% !important;
                min-width: 100% !important;
                max-width: none !important;
            }
            @page { size: A4 landscape; margin: 10mm; }
            .vertical-text { 
                white-space: normal !important; 
                word-wrap: break-word !important; 
                transform: rotate(180deg) !important; 
            }
        }
    `;
    document.head.appendChild(style);
    window.print();
    document.head.removeChild(style);
}

const loadLegerData = async () => {
    isLoadingLeger.value = true
    try {
        const token = useCookie('auth_token').value
        
        const res = await $fetch(`http://localhost:8000/api/guru/walas/cetak/leger`, {
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
        const { $swal } = useNuxtApp()
        $swal.fire({
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
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px) scale(0.98); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
.animate-fadeInUp {
    animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

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
</style>
