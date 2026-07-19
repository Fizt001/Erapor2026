<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    
    <!-- Superadmin Empty State -->
    <div v-if="isSuperadminWithoutImpersonation" class="flex-1 flex flex-col items-center justify-center text-center py-20 bg-white">
      <div class="text-amber-500 mb-6 bg-amber-50 p-5 rounded-full border border-amber-100 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
      </div>
      <h3 class="text-2xl font-black text-slate-800 tracking-tight">Menunggu Pilihan Siswa</h3>
      <p class="text-base text-slate-500 mt-2 max-w-md">Anda sedang dalam Mode Superadmin. Silakan pilih kelas dan nama siswa dari bilah menu di <strong class="text-amber-600 font-bold">pojok kanan atas</strong> untuk melihat Rapor Siswa.</p>
    </div>

    <!-- Loading State Awal -->
    <div v-else-if="isLoading" class="flex-1 flex flex-col items-center justify-center">
      <div class="w-10 h-10 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
      <p class="mt-4 text-sm font-semibold text-slate-500 animate-pulse">Memuat data periode rapor...</p>
    </div>
    
    <!-- Error State Awal -->
    <div v-else-if="errorMessage" class="flex-1 flex flex-col items-center justify-center text-center">
      <div class="text-red-500 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>
      <h3 class="text-lg font-bold text-slate-800">Gagal Memuat Data</h3>
      <p class="text-sm text-slate-500 mt-1 max-w-md">{{ errorMessage }}</p>
      <button @click="loadTitimangsas" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold shadow-sm hover:bg-indigo-700">Coba Lagi</button>
    </div>
    
    <!-- Empty State Awal -->
    <div v-else-if="!pageData?.titimangsas?.length" class="flex-1 flex flex-col items-center justify-center text-center">
      <div class="text-5xl mb-4">📭</div>
      <h3 class="text-lg font-bold text-slate-700">Belum Ada Rapor</h3>
      <p class="text-slate-500 text-sm mt-1">Belum ada periode rapor yang memuat nilai Anda.</p>
    </div>

    <!-- Layout 2 Panel Dock & Flow -->
    <div v-else class="flex-1 flex overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all hidden xl:flex">
        
        <div class="p-6 shrink-0 relative z-10">
          <div class="bg-gradient-to-r from-indigo-600 to-violet-700 rounded-2xl p-5 border border-indigo-500 shadow-sm relative overflow-hidden flex items-center gap-4">
            <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0 relative z-10">📄</div>
            <div class="relative z-10">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">Rapor Saya</h3>
                <p class="text-[10px] text-indigo-100 font-semibold uppercase mt-0.5">Pilih Periode Rapor</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-20 text-white">
              <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg>
            </div>
          </div>
        </div>

        
        <div class="p-4 shrink-0 relative z-10 border-b border-slate-100 bg-white">
          <div class="flex items-center gap-1 bg-slate-100 p-1.5 rounded-xl border border-slate-200/60 shadow-inner">
            <button @click="pindahTingkat('X')" :class="activeTingkat === 'X' ? 'bg-white shadow-sm text-indigo-700 ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'" class="flex-1 py-2 rounded-lg text-xs font-black uppercase tracking-wider transition-all duration-300">Kelas X</button>
            <button @click="pindahTingkat('XI')" :class="activeTingkat === 'XI' ? 'bg-white shadow-sm text-indigo-700 ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'" class="flex-1 py-2 rounded-lg text-xs font-black uppercase tracking-wider transition-all duration-300">Kelas XI</button>
            <button @click="pindahTingkat('XII')" :class="activeTingkat === 'XII' ? 'bg-white shadow-sm text-indigo-700 ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'" class="flex-1 py-2 rounded-lg text-xs font-black uppercase tracking-wider transition-all duration-300">Kelas XII</button>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-2 custom-scrollbar">

            <button 
                v-for="t in filteredTitimangsas" 
                :key="t.id"
                @click="pilihTitimangsa(t.id)"
                type="button" 
                class="w-full flex items-center gap-4 p-4 rounded-2xl transition-all duration-300 text-left relative group overflow-hidden border"
                :class="activeTitimangsaId === t.id ? 'bg-indigo-50 border-indigo-200 text-indigo-700 shadow-sm' : 'bg-white border-slate-100 text-slate-600 hover:bg-slate-50 hover:border-slate-200'"
            >
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl transition-all duration-300 shrink-0 shadow-sm"
                     :class="activeTitimangsaId === t.id ? 'bg-white text-indigo-600' : 'bg-slate-100 text-slate-400 group-hover:bg-white group-hover:text-indigo-500'">
                    📋
                </div>
                <div class="overflow-hidden flex-1">
                    <p class="font-bold text-xs uppercase tracking-widest truncate">{{ t.nama_periode_panjang || t.nama_periode }}</p>
                    <p class="text-[10px] font-semibold truncate mt-0.5" :class="activeTitimangsaId === t.id ? 'text-indigo-400' : 'text-slate-400'">{{ !t.is_aktif ? 'Riwayat Rapor' : 'Periode Aktif' }}</p>
                </div>
                <div v-if="activeTitimangsaId === t.id" class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></div>
            </button>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            
            <!-- Header Flow -->
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white/80 backdrop-blur-xl z-20 gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-400 to-indigo-600 shadow-sm flex items-center justify-center text-2xl text-white">
                        🔍
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-indigo-700">Preview Rapor</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">{{ raporData?.nama_periode_panjang || raporData?.nama_periode || 'Pilih Periode di Samping' }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <!-- Mobile Selector -->
                    <div class="xl:hidden w-48">
                        <select 
                        v-model="activeTitimangsaId"
                        @change="bukaPreviewRapor"
                        class="w-full text-xs font-bold text-slate-700 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 outline-none"
                        >
                        <option v-for="t in filteredTitimangsas" :key="t.id" :value="t.id">
                            {{ t.nama_periode_panjang || t.nama_periode }}
                        </option>
                        </select>
                    </div>

                    <!-- Tombol Tutup (Hanya Muncul Jika Ada Preview) -->
                    <button v-if="raporData" @click="raporData = null" class="flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-red-50 text-slate-500 hover:text-red-600 rounded-xl text-xs font-bold transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span class="hidden sm:inline">Tutup Preview</span>
                    </button>
                </div>
            </div>

            <!-- Loading Preview -->
            <div v-if="isPreviewLoading" class="flex-1 flex flex-col justify-center items-center">
              <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
              <p class="text-xs font-bold text-slate-500 mt-4 animate-pulse">Menarik data rapor...</p>
            </div>

            <!-- Empty Preview / Not In Class Yet -->
            <div v-else-if="filteredTitimangsas.length === 0" class="flex-1 flex flex-col justify-center items-center text-center p-8 bg-gradient-to-br from-indigo-50/50 via-white to-white m-6 rounded-3xl border border-indigo-100/50 shadow-sm relative overflow-hidden">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.03]"></div>
                <div class="w-32 h-32 mb-6 relative z-10">
                    <div class="absolute inset-0 bg-indigo-200 rounded-full animate-ping opacity-20 duration-1000"></div>
                    <div class="w-full h-full bg-gradient-to-br from-indigo-100 to-indigo-50 rounded-full flex items-center justify-center text-6xl shadow-inner border-4 border-white relative z-10 transform hover:scale-105 transition-transform">
                        ⏳
                    </div>
                    <div class="absolute -top-2 -right-2 bg-white p-2 rounded-xl shadow-md rotate-12 text-2xl z-20 animate-bounce">🔒</div>
                </div>
                <h3 class="text-2xl font-black text-indigo-900 mb-3 tracking-tight relative z-10">Sabar Dulu Ya! 🚀</h3>
                <p class="text-sm font-semibold text-slate-500 max-w-sm leading-relaxed relative z-10">
                    Saat ini kamu masih berada di <span class="text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded shadow-sm border border-indigo-100">Kelas {{ pageData?.current_tingkat || 'X' }}</span>.<br>
                    Data Rapor untuk <span class="text-indigo-600 font-bold underline decoration-indigo-200 underline-offset-4">Kelas {{ activeTingkat }}</span> belum tersedia.
                </p>
                <div class="mt-8 px-6 py-3 bg-white rounded-2xl border border-slate-100 shadow-sm inline-flex items-center gap-3 relative z-10">
                    <span class="text-xl">✨</span>
                    <span class="text-slate-400 text-xs italic font-bold tracking-wide">"Tetap semangat belajar untuk menuju jenjang ini!"</span>
                </div>
            </div>

            <!-- Select Period State -->
            <div v-else-if="!raporData" class="flex-1 flex flex-col justify-center items-center text-center p-8">
              <div class="text-4xl mb-3 opacity-50 animate-bounce">👆</div>
              <p class="text-sm font-bold text-slate-500">Pilih periode rapor di panel sebelah kiri untuk melihat nilainya.</p>
            </div>

            <!-- Rapor Content -->
            <div v-else class="flex-1 overflow-y-auto custom-scrollbar bg-slate-100 p-4 md:p-8 relative z-0">

        <div class="p-6 md:p-12 overflow-x-auto">
            <!-- A4 Paper Content Container (Scaled nicely for web view) -->
            <div id="rapor-view-area" class="min-w-[800px] max-w-4xl mx-auto text-slate-800 font-serif text-[13px] leading-tight bg-white">
                    
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
                                            <span class="border-b border-slate-900 pb-0.5 font-bold inline-block min-w-[12rem]">{{ raporData.walas?.nama_lengkap }}</span><br>
                                            NIP. {{ raporData.walas?.nip || '-' }}
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
      </div>
    </div>
    
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'

definePageMeta({
  layout: 'siswa',
  middleware: 'siswa',
  title: 'Rapor Saya'
})

const tokenCookie = useCookie('auth_token')
const userProfile = useCookie('user_profile')

const { data: response, pending: isLoading, error } = await useFetch('http://localhost:8000/api/siswa/rapor', {
  headers: { 'Authorization': `Bearer ${tokenCookie.value}` }
})

const pageData = computed(() => response.value?.data || null)
const errorMessage = computed(() => error.value?.message || (!response.value?.success && response.value?.message ? response.value?.message : ''))

const isSuperadminWithoutImpersonation = computed(() => {
  let role = null;
  if (typeof userProfile.value === 'string') {
    try {
      role = JSON.parse(userProfile.value)?.role
    } catch (e) {
      role = null;
    }
  } else {
    role = userProfile.value?.role
  }
  return role === 'superadmin' && !!errorMessage.value
})

const activeTingkat = ref('X')
const activeTitimangsaId = ref('')
const isPreviewLoading = ref(false)

const filteredTitimangsas = computed(() => {
    if (!pageData.value || !pageData.value.titimangsas) return [];
    return pageData.value.titimangsas.filter(t => t.tingkat === activeTingkat.value);
})

// Preview State
const showPreview = ref(false)
const raporData = ref(null)

watch(pageData, (newData) => {
  if (newData) {
      if (newData.current_tingkat) {
          activeTingkat.value = newData.current_tingkat;
      }
      
      if (newData.titimangsas && newData.titimangsas.length > 0) {
          const firstMatch = newData.titimangsas.find(t => t.tingkat === activeTingkat.value);
          if (firstMatch) {
              activeTitimangsaId.value = firstMatch.id;
          }
      }
  }
}, { immediate: true })

const bukaPreviewRapor = async () => {
    if (!activeTitimangsaId.value) return;
    
    raporData.value = null
    isPreviewLoading.value = true
    try {
        const token = useCookie('auth_token').value
        
        const res = await $fetch(`http://localhost:8000/api/siswa/rapor/cetak`, {
            params: {
                titimangsa_id: activeTitimangsaId.value
            },
            headers: { 'Authorization': `Bearer ${token}` }
        })

        if (res.success) {
            raporData.value = res.data
        } else {
            const { $swal } = useNuxtApp()
            $swal.fire({
                title: 'Gagal',
                text: res.message || 'Gagal memuat rapor',
                icon: 'error'
            })
        }
    } catch (error) {
        console.error("Gagal memuat rapor:", error)
        const { $swal } = useNuxtApp()
        $swal.fire({
            title: 'Gagal',
            text: error.data?.message || 'Terjadi kesalahan saat menarik data rapor.',
            icon: 'error',
            confirmButtonColor: '#10b981'
        })
    } finally {
        isPreviewLoading.value = false
    }
}

watch(activeTitimangsaId, (newId) => {
  if (newId) {
      bukaPreviewRapor();
  }
}, { immediate: true })



const pindahTingkat = (tingkat) => {
    activeTingkat.value = tingkat;
    const firstMatch = filteredTitimangsas.value[0];
    if (firstMatch) {
        activeTitimangsaId.value = firstMatch.id;
        bukaPreviewRapor();
    } else {
        activeTitimangsaId.value = '';
        raporData.value = null;
    }
}

const pilihTitimangsa = (id) => {

    activeTitimangsaId.value = id;
    bukaPreviewRapor();
}


</script>

<style scoped>
/* Animasi */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fadeInUp {
    animation: fadeInUp 0.4s ease-out forwards;
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
