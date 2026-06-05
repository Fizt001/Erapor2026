<template>
  <div class="animate-fadeIn max-w-5xl mx-auto pb-12 mt-4 relative">
    
    <!-- Header -->
    <div class="mb-6">
      <h2 class="text-2xl font-black text-slate-800 tracking-tight">Rapor Saya</h2>
      <p class="text-sm font-semibold text-slate-500 mt-1 mb-3">Pilih periode titimangsa untuk melihat dan mencetak rapor hasil belajar Anda.</p>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-8">
      <!-- Loading State -->
      <div v-if="isLoading" class="flex flex-col items-center justify-center py-20">
        <div class="w-10 h-10 border-4 border-emerald-200 border-t-emerald-600 rounded-full animate-spin"></div>
        <p class="mt-4 text-sm font-semibold text-slate-500 animate-pulse">Memuat data periode rapor...</p>
      </div>
      
      <!-- Error State -->
      <div v-else-if="errorMessage" class="flex flex-col items-center justify-center py-20 text-center">
        <div class="text-red-500 mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
        <h3 class="text-lg font-bold text-slate-800">Gagal Memuat Data</h3>
        <p class="text-sm text-slate-500 mt-1 max-w-md">{{ errorMessage }}</p>
        <button @click="loadTitimangsas" class="mt-4 px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-bold shadow-sm hover:bg-emerald-700">Coba Lagi</button>
      </div>

      <!-- Content Selection -->
      <div v-else-if="pageData && pageData.titimangsas && pageData.titimangsas.length > 0" class="p-6">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-50 border border-slate-200 rounded-xl p-4">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">
              📄
            </div>
            <div>
              <h3 class="text-sm font-bold text-slate-700">Pilih Periode Rapor</h3>
            </div>
          </div>
          
          <div class="flex items-center gap-3 w-full sm:w-auto">
            <select 
              v-model="activeTitimangsaId"
              class="w-full sm:w-64 text-sm font-bold text-slate-700 bg-white border border-slate-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500 outline-none transition-all shadow-sm"
            >
              <option v-for="t in pageData.titimangsas" :key="t.id" :value="t.id">
                {{ t.nama_periode }} {{ !t.is_aktif ? '(Riwayat)' : '(Aktif)' }}
              </option>
            </select>

            <button 
              @click="bukaPreviewRapor"
              :disabled="isPreviewLoading"
              class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-bold transition-all shadow-sm flex items-center justify-center gap-2 disabled:opacity-70 whitespace-nowrap"
            >
              <span v-if="isPreviewLoading" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
              {{ isPreviewLoading ? 'Memuat...' : 'Lihat Rapor' }}
            </button>
          </div>
        </div>
      </div>
      
      <!-- Empty State -->
      <div v-else class="py-20 text-center">
        <div class="text-4xl mb-4">📭</div>
        <h3 class="text-lg font-bold text-slate-700">Belum Ada Rapor</h3>
        <p class="text-slate-500 text-sm mt-1">Belum ada periode rapor yang memuat nilai Anda.</p>
      </div>
    </div>

    <!-- INLINE RAPOR VIEW -->
    <div v-if="raporData && !isPreviewLoading" class="animate-fadeInUp bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden mb-12">
        <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex items-center justify-between">
            <div>
                <h3 class="text-base font-black text-slate-800">Pratinjau Nilai Rapor</h3>
                <p class="text-xs font-semibold text-slate-500">{{ raporData.nama_periode }} - Mode Baca Saja (Read-Only)</p>
            </div>
            <button @click="raporData = null" class="text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">
                Tutup
            </button>
        </div>

        <div class="p-6 md:p-12 overflow-x-auto">
            <!-- A4 Paper Content Container (Scaled nicely for web view) -->
            <div id="rapor-view-area" class="min-w-[800px] max-w-4xl mx-auto text-slate-800 font-serif text-[13px] leading-tight bg-white">
                    
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
                            <span class="border-b border-slate-900 pb-0.5 font-bold inline-block min-w-[12rem]">{{ raporData.walas?.nama_lengkap || 'Wali Kelas' }}</span><br>
                            NIP. {{ raporData.walas?.nip || '-' }}
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
</template>

<script setup>
import { ref, onMounted } from 'vue'

definePageMeta({
  layout: 'siswa',
  middleware: 'siswa',
  title: 'Rapor Saya'
})

const pageData = ref(null)
const activeTitimangsaId = ref('')
const isLoading = ref(true)
const isPreviewLoading = ref(false)
const errorMessage = ref('')

// Preview State
const showPreview = ref(false)
const raporData = ref(null)

const loadTitimangsas = async () => {
    isLoading.value = true
    errorMessage.value = ''
    try {
        const token = useCookie('auth_token').value
        
        const res = await $fetch(`http://localhost:8000/api/siswa/rapor`, {
            headers: { 'Authorization': `Bearer ${token}` }
        })

        if (res.success) {
            pageData.value = res.data
            
            if (res.data.titimangsas && res.data.titimangsas.length > 0) {
                // Pre-select first item
                activeTitimangsaId.value = res.data.titimangsas[0].id
            }
        } else {
            errorMessage.value = res.message || 'Respons API gagal tanpa pesan error.'
        }
    } catch (error) {
        console.error("Gagal memuat data titimangsa:", error)
        errorMessage.value = error.data?.message || 'Terjadi kesalahan jaringan atau server.'
    } finally {
        isLoading.value = false
    }
}

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

onMounted(() => {
    loadTitimangsas()
})
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
</style>
