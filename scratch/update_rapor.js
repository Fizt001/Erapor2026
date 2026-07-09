const fs = require('fs');
const file = 'erapor-fe/app/pages/guru/walas/rapor.vue';
let content = fs.readFileSync(file, 'utf8');

const startMarker = '<!-- KOP SURAT (Hanya di Halaman Pertama) -->';
const endMarker = '                </div>\r\n            </div>\r\n        </div>\r\n    </div>';
// we'll replace everything between startMarker and the end of #rapor-print-area 

const newChunk = `<!-- KOP SURAT (Hanya di Halaman Pertama) -->
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
                    <div v-if="raporData?.nama_periode && raporData.nama_periode.includes('PSTS')" class="text-center font-bold text-[16px] mb-8 uppercase underline" style="font-family: 'Times New Roman', Times, serif;">
                        PENILAIAN SUMATIF TENGAH SEMESTER
                    </div>
                    <template v-else>
                        <div class="text-center font-bold text-[16px] mb-2 uppercase underline" style="font-family: 'Times New Roman', Times, serif;">
                            PENILAIAN SUMATIF AKHIR SEMESTER
                        </div>
                        <!-- DOUBLE BORDER TOP -->
                        <div class="border-t-[3px] border-slate-900 border-b border-slate-900 h-1 mb-4 w-full"></div>
                    </template>

                    <!-- MASTER TABLE UNTUK 3 HALAMAN -->
                    <table class="w-full">
                        <!-- THEAD: KOP RAPOR (Berulang di setiap halaman cetak) -->
                        <thead class="print:table-header-group">
                            <tr>
                                <td class="pb-2">
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
                                                    <td class="py-0">{{ (raporData?.nama_periode && raporData.nama_periode.includes('PSTS')) ? raporData.kelas_full : raporData.kelas }}</td>
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
                                                
                                                <template v-if="!(raporData?.nama_periode && raporData.nama_periode.includes('PSTS'))">
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
                                    <div v-if="!(raporData?.nama_periode && raporData.nama_periode.includes('PSTS'))" class="border-t-[3px] border-slate-900 border-b border-slate-900 h-1 mb-2 w-full mt-2"></div>
                                </td>
                            </tr>
                        </thead>

                        <!-- HALAMAN 1: MAPEL UMUM -->
                        <tbody>
                            <tr>
                                <td>
                                    <div class="font-bold text-[13px] mb-2" :class="{'mt-4': raporData?.nama_periode && raporData.nama_periode.includes('PSTS')}" style="font-family: 'Times New Roman', Times, serif;">
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
                                                <tr v-for="(mapel, mIndex) in grup.mapels" :key="mapel.id" class="h-[60px]">
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
                                </td>
                            </tr>
                        </tbody>
                    </table>`;

const startIdx = content.indexOf(startMarker);
const endIdx = content.indexOf('<div v-if="!(raporData?.nama_periode && raporData.nama_periode.includes(\'PSTS\'))" class="text-center mt-8 text-[11px] font-sans">');

if (startIdx !== -1 && endIdx !== -1) {
    // find the closing div of the endIdx element
    // wait, it's easier to use indexOf up to the end of #rapor-print-area
    const endOfContainer = content.indexOf('                </div>', endIdx);
    
    // we want to replace from startMarker up to endOfContainer
    const pre = content.substring(0, startIdx);
    const post = content.substring(endOfContainer);
    
    fs.writeFileSync(file, pre + newChunk + '\n' + post);
    console.log('Replacement successful');
} else {
    console.log('Markers not found', startIdx, endIdx);
}
