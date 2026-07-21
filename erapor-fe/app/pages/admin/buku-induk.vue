<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    
<!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
        
        <!-- MOBILE VIEW TABS -->
        <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-2 grid grid-cols-2 gap-2 z-20">
            <button type="button" @click="activeTab = 'filter'" :class="activeTab === 'filter' ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 ring-2 ring-emerald-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'" class="rounded-xl flex items-center justify-center py-2 px-1 transition-all active:scale-95">
              <span class="text-lg mr-1.5 transition-transform" :class="activeTab === 'filter' ? 'scale-110' : ''">🔍</span>
              <span class="text-[10px] font-black uppercase tracking-wider">Filter Data</span>
            </button>
            <button type="button" @click="activeTab = 'preview'" :class="activeTab === 'preview' ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 ring-2 ring-emerald-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'" class="rounded-xl flex items-center justify-center py-2 px-1 transition-all active:scale-95">
              <span class="text-lg mr-1.5 transition-transform" :class="activeTab === 'preview' ? 'scale-110' : ''">📄</span>
              <span class="text-[10px] font-black uppercase tracking-wider">Preview Buku</span>
            </button>
        </div>

        <!-- Panel Dock Kiri -->
        <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all', activeTab === 'filter' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[60px]' : '']">
        <div class="p-6 shrink-0">
            <div class="bg-gradient-to-r from-teal-600 to-emerald-700 rounded-2xl p-5 border border-teal-500 shadow-sm relative overflow-hidden flex items-center gap-4">
                <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0 relative z-10">🔍</div>
                <div class="relative z-10">
                    <h3 class="text-sm font-black uppercase tracking-widest text-white">Filter Data</h3>
                    <p class="text-[10px] text-teal-100 font-semibold uppercase mt-0.5">Pilih Tahun & Kurikulum</p>
                </div>
                <div class="absolute right-0 bottom-0 opacity-20 text-white">
                  <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"></path></svg>
                </div>
            </div>
        </div>

            <div class="p-6 space-y-5 shrink-0 border-b border-slate-100">
                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Tahun Ajaran</label>
                    <select v-model="selectedTahunAjaranId" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200/70 rounded-2xl focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-bold text-slate-700 outline-none cursor-pointer appearance-none">
                        <option value="">-- Pilih Tahun --</option>
                        <option v-for="ta in tahunAjarans" :key="ta.id" :value="ta.id">{{ ta.tahun }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kurikulum</label>
                    <select v-model="selectedKurikulumId" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200/70 rounded-2xl focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-bold text-slate-700 outline-none cursor-pointer appearance-none">
                        <option value="">-- Pilih Kurikulum --</option>
                        <option v-for="k in kurikulums" :key="k.id" :value="k.id">{{ k.nama_kurikulum }}</option>
                    </select>
                </div>
            </div>

            <!-- Student List Selection -->
            <div v-if="students.length > 0" class="flex-1 flex flex-col min-h-0">
                <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <h3 class="font-black text-slate-700 text-xs uppercase tracking-widest">Daftar Siswa</h3>
                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md border border-emerald-100">{{ students.length }} Orang</span>
                </div>
                <div class="flex-1 overflow-y-auto custom-scrollbar p-3">
                    <div v-for="(siswa, index) in students" :key="siswa.id" 
                         @click="selectStudent(index)"
                         :class="currentStudentIndex === index ? 'bg-emerald-50 border-emerald-200 ring-1 ring-emerald-500/50' : 'bg-white border-transparent hover:bg-slate-50'"
                         class="p-3 mb-1.5 rounded-xl border cursor-pointer transition-all flex items-center gap-3">
                         <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-[10px] shrink-0"
                              :class="currentStudentIndex === index ? 'bg-emerald-500 text-white shadow-sm' : 'bg-slate-100 text-slate-500'">
                              {{ index + 1 }}
                         </div>
                         <div class="overflow-hidden">
                             <p class="text-xs font-bold truncate text-slate-800" :class="currentStudentIndex === index ? 'text-emerald-700' : ''">{{ siswa.nama_lengkap }}</p>
                             <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mt-0.5">NISN: {{ siswa.nisn || '-' }}</p>
                         </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Flow Kanan -->
        <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0', activeTab === 'preview' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[60px]' : '']">
          <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            
              <div v-if="(!selectedTahunAjaranId || !selectedKurikulumId) || students.length === 0" class="flex-1 flex flex-col items-center justify-center p-8 text-center bg-white">
                  <span class="text-6xl mb-6 block opacity-20">📄</span>
                  <h3 class="text-xl font-black text-slate-700 mb-2 uppercase tracking-widest">Pilih Filter</h3>
                  <p class="text-slate-500 text-sm font-semibold max-w-sm">Silakan pilih tahun ajaran dan kurikulum terlebih dahulu di panel sebelah kiri.</p>
              </div>

              <div v-else-if="currentStudent" class="flex-1 flex flex-col min-h-0 bg-slate-50/50">
                  <!-- Toolbar Header -->
                  <div class="p-4 md:px-8 md:py-5 bg-white border-b border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4 shrink-0 z-10">
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <button @click="prevStudent" :disabled="currentStudentIndex === 0" class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-500 hover:text-emerald-600 hover:border-emerald-300 hover:bg-emerald-50 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                            ⬅️
                        </button>
                        <div class="flex-1 text-center sm:text-left px-2">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-0.5">Siswa {{ currentStudentIndex + 1 }} dari {{ students.length }}</span>
                            <h3 class="text-sm font-black text-slate-800 truncate max-w-[250px]">{{ currentStudent.nama_lengkap }}</h3>
                        </div>
                        <button @click="nextStudent" :disabled="currentStudentIndex === students.length - 1" class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-500 hover:text-emerald-600 hover:border-emerald-300 hover:bg-emerald-50 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                            ➡️
                        </button>
                    </div>
                    <div class="flex items-center w-full sm:w-auto">
                        <button @click="printBukuInduk" class="w-full sm:w-auto py-2.5 px-6 bg-gradient-to-br from-slate-800 to-slate-900 text-white font-bold rounded-xl shadow-md shadow-slate-900/20 hover:-translate-y-0.5 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                            🖨️ Cetak Buku Induk
                        </button>
                    </div>
                </div>

                <!-- Printable Area -->
                <div id="printable-buku-induk" class="flex-1 overflow-auto p-4 sm:p-8 custom-scrollbar">
                    <div id="printable-area" class="min-w-[1400px] bg-white border border-slate-300 p-8 shadow-md print-container mx-auto">
                        
                        <div class="relative w-full flex justify-between items-start mb-6">
                            <div class="w-1/4"></div>
                            <div class="w-1/2 text-center uppercase tracking-wide">
                                <h2 class="text-[15px] font-bold font-serif text-black leading-tight">HASIL PRESTASI BELAJAR SISWA {{ sekolah?.nama_sekolah || "NAMA SEKOLAH" }}</h2>
                                <p class="text-[10px] font-serif text-black mt-1">{{ sekolah?.alamat || "Alamat Sekolah" }} Telp {{ sekolah?.telepon || "-" }}</p>
                            </div>
                            <div class="w-1/4 flex flex-col justify-end text-[9px] font-sans text-black gap-0.5">
                                <div class="flex"><div class="w-32">Bidang Studi Keahlian</div><div>: {{ currentKelas?.kejuruan?.program?.bidang?.nama_bidang || "-" }}</div></div>
                                <div class="flex"><div class="w-32">Program Studi Keahlian</div><div>: {{ currentKelas?.kejuruan?.program?.nama_program || "-" }}</div></div>
                                <div class="flex"><div class="w-32">Kompetensi Keahlian</div><div>: {{ currentKelas?.kejuruan?.nama_konsentrasi || "-" }}</div></div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <span class="font-bold text-[10px] font-sans text-black underline tracking-wide">KETERANGAN TENTANG DIRI SISWA</span>
                        </div>

                        <div class="flex w-full items-stretch">
                            <!-- KIRI: BIODATA (22%) -->
                            <div class="w-[22%] pr-6 text-[9px] text-black font-sans shrink-0 leading-tight">
                                <div class="flex mb-0.5"><div class="w-32">NISN</div><div>: {{ currentStudent.nisn || "-" }}</div></div>
                                <div class="flex mb-3"><div class="w-32">NIS</div><div>: {{ currentStudent.nis || "-" }}</div></div>
                                
                                <div class="flex mb-0.5"><div class="w-5">1</div><div class="w-28">Nama Siswa</div><div class="flex-1">: <span class="uppercase">{{ currentStudent.nama_lengkap || "-" }}</span></div></div>
                                <div class="flex mb-0.5"><div class="w-5">2</div><div class="w-28">Jenis Kelamin</div><div class="flex-1">: {{ currentStudent.jenis_kelamin || "-" }}</div></div>
                                <div class="flex mb-0.5"><div class="w-5">3</div><div class="w-28">Tempat/Tgl Lahir</div><div class="flex-1">: {{ currentStudent.tempat_lahir || "-" }}, {{ currentStudent.tanggal_lahir || "-" }}</div></div>
                                <div class="flex mb-0.5"><div class="w-5">4</div><div class="w-28">Warga Negara</div><div class="flex-1">: {{ currentStudent.warga_negara || "-" }}</div></div>
                                <div class="flex mb-0.5"><div class="w-5">5</div><div class="w-28">Agama</div><div class="flex-1">: {{ currentStudent.agama || "-" }}</div></div>
                                <div class="flex mb-0.5 items-start"><div class="w-5">6</div><div class="w-28 shrink-0">Alamat</div><div class="flex-1">: {{ currentStudent.alamat || "-" }}</div></div>
                                <div class="flex mb-3"><div class="w-5"></div><div class="w-28">No. Telpon</div><div class="flex-1">: {{ currentStudent.no_hp || "-" }}</div></div>
                                
                                <div class="flex mb-0.5"><div class="w-5">7</div><div class="w-28">Nama Orang Tua</div><div class="flex-1"></div></div>
                                <div class="flex mb-0.5"><div class="w-5"></div><div class="w-5">a</div><div class="w-24">Nama Ayah</div><div class="flex-1">: {{ currentStudent.nama_ayah || "-" }}</div></div>
                                <div class="flex mb-0.5"><div class="w-10"></div><div class="w-24">Tempat/Tgl Lahir</div><div class="flex-1">: {{ currentStudent.ttl_ayah || "-" }}</div></div>
                                <div class="flex mb-0.5"><div class="w-10"></div><div class="w-24">Pekerjaan</div><div class="flex-1">: {{ currentStudent.pekerjaan_ayah || "-" }}</div></div>
                                <div class="flex mb-3 items-start"><div class="w-10"></div><div class="w-24 shrink-0">Alamat Rumah</div><div class="flex-1">: {{ currentStudent.alamat || "-" }}</div></div>
                                
                                <div class="flex mb-0.5"><div class="w-5"></div><div class="w-5">b</div><div class="w-24">Nama Ibu</div><div class="flex-1">: {{ currentStudent.nama_ibu || "-" }}</div></div>
                                <div class="flex mb-0.5"><div class="w-10"></div><div class="w-24">Tempat/Tgl Lahir</div><div class="flex-1">: {{ currentStudent.ttl_ibu || "-" }}</div></div>
                                <div class="flex mb-0.5"><div class="w-10"></div><div class="w-24">Pekerjaan</div><div class="flex-1">: {{ currentStudent.pekerjaan_ibu || "-" }}</div></div>
                                <div class="flex mb-3 items-start"><div class="w-10"></div><div class="w-24 shrink-0">Alamat Rumah</div><div class="flex-1">: {{ currentStudent.alamat || "-" }}</div></div>
                                
                                <div class="flex mb-0.5"><div class="w-5"></div><div class="w-5">c</div><div class="w-24">Nama Wali</div><div class="flex-1">: {{ currentStudent.nama_wali || "-" }}</div></div>
                                <div class="flex mb-0.5"><div class="w-10"></div><div class="w-24">Pekerjaan</div><div class="flex-1">: {{ currentStudent.pekerjaan_wali || "-" }}</div></div>
                                <div class="flex mb-3 items-start"><div class="w-10"></div><div class="w-24 shrink-0">Alamat Rumah</div><div class="flex-1">: {{ currentStudent.alamat_wali || "-" }}</div></div>
                                
                                <div class="flex mb-0.5"><div class="w-5">8</div><div class="w-28">Diterima Menjadi Siswa</div><div class="flex-1"></div></div>
                                <div class="flex mb-0.5"><div class="w-5"></div><div class="w-28">Tanggal</div><div class="flex-1">: {{ currentStudent.tanggal_masuk || "-" }}</div></div>
                                <div class="flex mb-0.5 items-start"><div class="w-5"></div><div class="w-28 shrink-0">Asal sekolah</div><div class="flex-1">: {{ currentStudent.asal_sekolah || "-" }}</div></div>
                                <div class="flex mb-0.5 items-start"><div class="w-5"></div><div class="w-28 shrink-0">Alamat Sekolah</div><div class="flex-1">: {{ currentStudent.alamat_sekolah_asal || "-" }}</div></div>
                                <div class="flex mb-0.5"><div class="w-5"></div><div class="w-28">No. STTB</div><div class="flex-1">: {{ currentStudent.no_sttb_smp || "-" }}</div></div>
                                <div class="flex mb-3"><div class="w-5"></div><div class="w-28">Tgl. STTB</div><div class="flex-1">: {{ currentStudent.tgl_sttb_smp || "-" }}</div></div>

                                <div class="flex mb-0.5"><div class="w-5">9</div><div class="w-28">Meninggalkan Sekolah</div><div class="flex-1"></div></div>
                                <div class="flex mb-0.5"><div class="w-5"></div><div class="w-28">Tanggal</div><div class="flex-1">: {{ currentStudent.tanggal_keluar || "-" }}</div></div>
                                <div class="flex mb-3 items-start"><div class="w-5"></div><div class="w-28 shrink-0">Alasan</div><div class="flex-1">: {{ currentStudent.alasan_keluar || "-" }}</div></div>
                                
                                <div class="flex mb-0.5"><div class="w-5">10</div><div class="w-28">Tamat Sekolah</div><div class="flex-1"></div></div>
                                <div class="flex mb-0.5"><div class="w-5"></div><div class="w-28">No. STTB</div><div class="flex-1">: {{ currentStudent.no_sttb_smk || "-" }}</div></div>
                                <div class="flex mb-3"><div class="w-5"></div><div class="w-28">Tgl. STTB</div><div class="flex-1">: {{ currentStudent.tgl_sttb_smk || "-" }}</div></div>
                                
                                <div class="flex mb-0.5"><div class="w-5">11</div><div class="w-28">Tempat PKL</div><div class="flex-1">: {{ currentStudent.tempat_pkl || "-" }}</div></div>
                                <div class="flex mb-0.5"><div class="w-5">12</div><div class="w-28">Waktu PKL</div><div class="flex-1">: {{ currentStudent.tgl_mulai_pkl || "-" }} s/d {{ currentStudent.tgl_selesai_pkl || "-" }}</div></div>
                                <div class="flex mb-3"><div class="w-5">13</div><div class="w-28">Keterangan Lain-lain</div><div class="flex-1">: -</div></div>
                            </div>

                            <!-- KANAN: TABEL NILAI (78%) -->
                            <div class="w-[78%] border-2 border-slate-800 bg-white relative">
                                <div v-if="isLoadingNilai" class="absolute inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center z-10">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-slate-800"></div>
                                </div>
                                <div class="flex-1 p-2 flex flex-col min-h-[800px]">
                                    <table class="w-full border-collapse border border-black text-[10px] text-black">
                                        <thead>
                                            <tr class="bg-gray-100 text-center font-bold">
                                                <th rowspan="2" class="border border-black p-1.5 w-1/3">Mata Pelajaran</th>
                                                <th colspan="2" class="border border-black p-1.5">Tingkat X : {{ selectedTahunAjaran?.tahun || "-" }}</th>
                                                <th colspan="2" class="border border-black p-1.5">Tingkat XI : {{ getTahunAjaranNaik(selectedTahunAjaran?.tahun, 1) }}</th>
                                                <th colspan="2" class="border border-black p-1.5">Tingkat XII : {{ getTahunAjaranNaik(selectedTahunAjaran?.tahun, 2) }}</th>
                                            </tr>
                                            <tr class="bg-gray-100 text-center font-bold">
                                                <th class="border border-black p-1.5 w-[11%]">Nilai Semester 1</th>
                                                <th class="border border-black p-1.5 w-[11%]">Nilai Semester 2</th>
                                                <th class="border border-black p-1.5 w-[11%]">Nilai Semester 3</th>
                                                <th class="border border-black p-1.5 w-[11%]">Nilai Semester 4</th>
                                                <th class="border border-black p-1.5 w-[11%]">Nilai Semester 5</th>
                                                <th class="border border-black p-1.5 w-[11%]">Nilai Semester 6</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-for="(group, gIdx) in mapelStruktur" :key="gIdx">
                                                <tr class="bg-gray-50/50">
                                                    <td colspan="7" class="border border-black p-1.5 font-bold uppercase">{{ group.group_name }}</td>
                                                </tr>
                                                <tr v-for="(mapel, mIdx) in group.mapels" :key="mIdx">
                                                    <td class="border border-black p-1.5 pl-4">{{ mapel }}</td>
                                                    <td class="border border-black p-1.5 text-center">{{ nilaiSiswa[mapel] ? nilaiSiswa[mapel][1] : "" }}</td>
                                                    <td class="border border-black p-1.5 text-center">{{ nilaiSiswa[mapel] ? nilaiSiswa[mapel][2] : "" }}</td>
                                                    <td class="border border-black p-1.5 text-center">{{ nilaiSiswa[mapel] ? nilaiSiswa[mapel][3] : "" }}</td>
                                                    <td class="border border-black p-1.5 text-center">{{ nilaiSiswa[mapel] ? nilaiSiswa[mapel][4] : "" }}</td>
                                                    <td class="border border-black p-1.5 text-center">{{ nilaiSiswa[mapel] ? nilaiSiswa[mapel][5] : "" }}</td>
                                                    <td class="border border-black p-1.5 text-center">{{ nilaiSiswa[mapel] ? nilaiSiswa[mapel][6] : "" }}</td>
                                                </tr>
                                            </template>
                                            <tr v-if="mapelStruktur.length === 0">
                                                <td colspan="7" class="border border-black p-4 text-center text-slate-500 italic">Data struktur mata pelajaran kosong atau sedang dimuat...</td>
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
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'

definePageMeta({
  layout: 'admin',
  middleware: 'admin',
  title: 'Buku Induk'
})

const windowWidth = ref(1024)
const isDesktop = computed(() => windowWidth.value >= 1280) // xl breakpoint for new dock
const activeTab = ref('filter')

const token = useCookie('auth_token')
const tahunAjarans = ref([])
const kurikulums = ref([])
const kelasList = ref([])

const sekolah = ref(null)

const selectedTahunAjaranId = ref('')
const selectedKurikulumId = ref('')

const students = ref([])
const currentStudentIndex = ref(0)
const nilaiSiswa = ref({})
const mapelStruktur = ref([])

const isLoadingNilai = ref(false)

const selectedTahunAjaran = computed(() => {
    return tahunAjarans.value.find((t) => t.id === selectedTahunAjaranId.value)
})

const currentKelas = computed(() => {
    if (!currentStudent.value) return null
    return kelasList.value.find((k) => k.id === currentStudent.value.kelas_id)
})

const getTahunAjaranNaik = (tahunStr, addYears) => {
    if (!tahunStr) return "-"
    const parts = tahunStr.split("/")
    if (parts.length === 2) {
        return `${parseInt(parts[0]) + addYears}/${parseInt(parts[1]) + addYears}`
    }
    return tahunStr
}

const currentStudent = computed(() => {
    if (students.value.length === 0) return null
    return students.value[currentStudentIndex.value]
})

const fetchDependencies = async () => {
    try {
        const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/admin/buku-induk/dependencies', {
            headers: { Authorization: `Bearer ${token.value}` }
        })
        if (res) {
            tahunAjarans.value = res.tahun_ajarans || []
            kurikulums.value = res.kurikulums || []
            kelasList.value = res.kelas || []
            
            if (res.active_tahun_ajaran) {
                selectedTahunAjaranId.value = res.active_tahun_ajaran.id
            }
        }
        
        // Fetch sekolah profile
        const resSekolah = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/admin/sekolah', {
            headers: { Authorization: `Bearer ${token.value}` }
        })
        if (resSekolah.success) {
            sekolah.value = resSekolah.data
        }
    } catch (e) {
        console.error('Failed fetching dependencies:', e)
        useSwal().toast('Gagal memuat filter', 'error')
    }
}

const loadStudents = async () => {
    if (!selectedTahunAjaranId.value || !selectedKurikulumId.value) {
        students.value = []
        return
    }
    try {
        // Fetch data siswa
        const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/admin/buku-induk/biodata?tahun_ajaran_id=${selectedTahunAjaranId.value}&kurikulum_id=${selectedKurikulumId.value}`, {
            headers: { Authorization: `Bearer ${token.value}` }
        })
        
        students.value = res.data || res || []
        currentStudentIndex.value = 0
        
        if (!isDesktop.value && students.value.length > 0) activeTab.value = 'preview'
        
    } catch (e) {
        console.error('Failed loading students', e)
        students.value = []
    }
}

watch([selectedTahunAjaranId, selectedKurikulumId], () => {
    loadStudents()
})


const fetchNilaiSiswa = async () => {
    if (!currentStudent.value) return
    isLoadingNilai.value = true
    try {
        const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/admin/buku-induk/siswa/${currentStudent.value.id}/nilai`, {
            headers: { Authorization: `Bearer ${token.value}` }
        })
        if (res) {
            nilaiSiswa.value = res.nilai || res || {}
        }
    } catch (e) {
        console.error('Failed fetching grades', e)
        nilaiSiswa.value = {}
    } finally {
        isLoadingNilai.value = false
    }
}

watch(currentStudent, async () => {
    if (currentStudent.value) {
        fetchNilaiSiswa()
        
        // Load mapel struktur for this student's class
        try {
            const resMapel = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/admin/buku-induk/${currentStudent.value.kelas_id}/mapel-struktur`, {
                headers: { Authorization: `Bearer ${token.value}` }
            })
            if (resMapel) {
                mapelStruktur.value = resMapel.data || resMapel || []
            }
        } catch (e) {
            console.error('Failed loading mapel struktur', e)
            mapelStruktur.value = []
        }
    } else {
        nilaiSiswa.value = {}
        mapelStruktur.value = []
    }
})

const selectStudent = (index) => {
    currentStudentIndex.value = index
    if (!isDesktop.value) {
        activeTab.value = 'preview'
    }
}

const prevStudent = () => {
    if (currentStudentIndex.value > 0) {
        currentStudentIndex.value--
        document.getElementById('printable-buku-induk')?.scrollTo({ top: 0, behavior: 'smooth' })
    }
}

const nextStudent = () => {
    if (currentStudentIndex.value < students.value.length - 1) {
        currentStudentIndex.value++
        document.getElementById('printable-buku-induk')?.scrollTo({ top: 0, behavior: 'smooth' })
    }
}

const printBukuInduk = () => {
    const printContent = document.getElementById('printable-buku-induk').innerHTML
    const originalContent = document.body.innerHTML

    document.body.innerHTML = `
        <div class="print-container w-full max-w-[210mm] mx-auto p-8 bg-white text-black font-serif">
            ${printContent}
        </div>
    `
    window.print()
    document.body.innerHTML = originalContent
    window.location.reload()
}

onMounted(() => {
    windowWidth.value = window.innerWidth
    window.addEventListener('resize', () => { windowWidth.value = window.innerWidth })
    
    if (!isDesktop.value) activeTab.value = 'filter'
    fetchDependencies()
})
</script>

<style scoped>
/* Custom scrollbar for lists */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

@media print {
    body * {
        visibility: hidden;
    }
    .print-container, .print-container * {
        visibility: visible;
    }
    .print-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    table { page-break-inside:auto }
    tr    { page-break-inside:avoid; page-break-after:auto }
    thead { display:table-header-group }
    tfoot { display:table-footer-group }
}
</style>

<style>
@media print {
    @page {
        size: A4 landscape !important;
        margin: 1cm;
    }
    .print-container {
        width: 1350px !important;
        transform: scale(0.68);
        transform-origin: top left;
    }
}
</style>
