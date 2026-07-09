<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50 relative animate-fadeIn print:block print:h-auto print:overflow-visible">
    
    <!-- Mobile Tabs -->
    <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-2 grid grid-cols-2 gap-2 z-20 no-print">
      <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTabMobile = tab.id"
        :class="activeTabMobile === tab.id ? 'bg-gradient-to-br from-rose-500 to-rose-600 text-white shadow-md shadow-rose-500/20 ring-2 ring-rose-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
        class="rounded-xl flex flex-col items-center justify-center py-2 px-1 transition-all active:scale-95">
        <span class="text-lg mb-0.5 transition-transform" :class="activeTabMobile === tab.id ? 'scale-110' : ''">{{ tab.icon }}</span>
        <span class="text-[9px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
      </button>
    </div>

    <div class="flex-1 flex overflow-hidden relative print:block print:h-auto print:overflow-visible">
      <!-- Panel Dock Kiri (Filter Kelas) -->
      <div v-show="!printMode" :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all no-print', activeTabMobile === 'dock' || isDesktop ? 'flex' : 'hidden xl:flex', !isDesktop ? 'pt-[60px]' : '']">
        
        <div class="p-6 shrink-0 no-print">
          <div class="bg-gradient-to-r from-rose-600 to-rose-700 rounded-2xl p-5 border border-rose-500 shadow-sm relative overflow-hidden flex items-center gap-4">
            <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0 relative z-10">🏫</div>
            <div class="relative z-10">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">Filter Kelas</h3>
                <p class="text-[10px] text-rose-100 font-semibold uppercase mt-0.5">Buku Kasus Siswa</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-20 text-white">
              <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"></path></svg>
            </div>
          </div>
        </div>

        <div class="px-6 pb-6 bg-white shrink-0 no-print">
            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Cari Kelas</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
                <input type="text" v-model="searchKelas" placeholder="Cari nama kelas..." class="w-full pl-9 pr-3 py-2.5 bg-white border-2 border-slate-200/70 rounded-xl focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all text-xs font-bold outline-none text-slate-700">
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

      <!-- Panel Flow Kanan (Buku Kasus) -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative print:block print:bg-white print:h-auto print:overflow-visible', activeTabMobile === 'flow' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[60px]' : '']">
        <div class="p-6 lg:p-8 max-w-5xl mx-auto w-full h-full flex flex-col relative z-0 print:p-0 print:block">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0 print:border-none print:shadow-none print:rounded-none">
            <!-- Header Flow -->
            <div class="px-6 py-5 bg-white border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shrink-0 z-10 shadow-sm print:hidden">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-500 hidden sm:flex text-xl">📖</div>
                <div>
                    <div class="flex items-center gap-3">
                        <h3 class="text-sm font-black uppercase tracking-widest text-slate-700">Buku Jurnal Kasus</h3>
                        <span v-if="tahunAktif?.tahun" class="bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-md text-[10px] font-black tracking-widest border border-indigo-100 uppercase no-print">
                            TA. {{ tahunAktif.tahun }}
                        </span>
                    </div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">
                        {{ selectedKelasId ? getSelectedKelasName() : 'Riwayat Kedisiplinan' }}
                    </p>
                </div>
            </div>
            <div class="flex gap-2">
            </div>
        </div>

        <div class="flex-1 overflow-auto bg-slate-50 p-4 sm:p-6 custom-scrollbar print:p-0 print:bg-white print:block print:h-auto print:overflow-visible">
            
            <div v-if="!selectedKelasId" class="bg-white rounded-2xl p-20 text-center flex flex-col items-center justify-center border-2 border-dashed border-slate-200 shadow-sm h-full max-h-[400px] no-print">
                <span class="text-7xl mb-6 opacity-30 grayscale">📖</span>
                <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest">Buku Jurnal Kasus</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase mt-3 tracking-widest max-w-sm">Pilih rombongan belajar di panel kiri untuk memuat dan mencetak buku kasus siswa.</p>
            </div>

            <div v-else-if="isLoadingSiswa" class="bg-white rounded-2xl p-16 flex flex-col items-center justify-center border border-slate-200/60 shadow-sm opacity-60 h-full max-h-[400px] no-print">
                <div class="w-8 h-8 border-4 border-rose-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Buku Kasus...</span>
            </div>

            <div v-else class="space-y-6 bg-transparent print:border-none print:shadow-none">
                
                <div class="space-y-4 print:p-0 print:space-y-0 print:bg-white">
                    <div v-if="filteredSiswas.length === 0" class="text-center py-12 border-2 border-dashed border-slate-200 rounded-xl no-print bg-white">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Tidak ada siswa.</p>
                    </div>
                    
                    <!-- Loop Siswa -->
                    <div v-for="siswa in filteredSiswas" :key="selectedKelas + '-' + selectedBulan + '-' + activeTab + '-' + siswa.id" 
                        v-show="printSiswaId === null || printSiswaId === siswa.id"
                        class="border border-slate-200 rounded-2xl overflow-hidden print:border-none print:break-after-page group bg-white shadow-sm print:shadow-none border-l-4 border-l-slate-300 print:overflow-visible">
                        
                        <!-- Header Print (Repeated per page) -->
                        <div class="hidden print:block mb-6 text-center border-b-2 border-slate-900 pb-4">
                            <h2 class="text-xl font-black text-black uppercase">Buku Kasus & Riwayat Siswa</h2>
                            <p class="text-xs font-bold text-black mt-1 uppercase">
                                {{ getSelectedKelasName() }} <span v-if="getTargetTitimangsaForSiswa(siswa.id)">- {{ getSemesterName(getTargetTitimangsaForSiswa(siswa.id)) }} {{ getTargetTitimangsaForSiswa(siswa.id)?.tahun_ajaran?.tahun }} ({{ getTargetTitimangsaForSiswa(siswa.id)?.nama_periode }})</span>
                            </p>
                        </div>

                        <!-- Header Siswa -->
                        <div class="p-4 flex justify-between items-center bg-white print:bg-white print:border-b-2 print:border-slate-900 print:mb-4 cursor-pointer hover:bg-slate-50 transition-colors no-print-interactive" @click="toggleExpanded(siswa.id)">
                            <div class="flex items-center gap-3 sm:gap-4">
                                <div class="w-10 h-10 rounded-full bg-rose-50 flex items-center justify-center text-rose-500 font-black no-print border border-rose-100 shrink-0">
                                    👤
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-slate-800 uppercase">{{ siswa.nama }}</h4>
                                    <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                        <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest px-2 py-0.5 rounded border border-slate-200 bg-white shadow-sm print:border-none print:shadow-none print:p-0 print:text-black">
                                            SISA POIN: <b :class="siswa.sisa_poin < 50 ? 'text-rose-600 print:text-black' : 'text-emerald-600 print:text-black'">{{ siswa.sisa_poin }}</b>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="no-print flex flex-col sm:flex-row gap-2 items-end sm:items-center">
                                <select v-model="printTitimangsaId[siswa.id]" class="px-3 py-2 bg-slate-100 border border-slate-200 text-slate-700 text-[10px] font-bold rounded-lg sm:rounded-xl outline-none focus:border-rose-500 transition cursor-pointer max-w-[200px]">
                                    <option value="" disabled>- Pilih Periode -</option>
                                    <option v-for="t in titimangsas" :key="t.id" :value="t.id">
                                        TA. {{ t.tahun_ajaran ? t.tahun_ajaran.tahun : '' }} - {{ t.nama_periode }}
                                    </option>
                                </select>
                                <button @click.stop="printSiswa(siswa.id)" class="px-3 sm:px-4 py-2 bg-slate-800 text-white text-[9px] sm:text-[10px] font-black rounded-lg sm:rounded-xl hover:bg-black transition shadow-sm uppercase tracking-widest flex items-center gap-1.5 active:scale-95">
                                    🖨️ CETAK
                                </button>
                                <button class="px-3 sm:px-4 py-2 bg-rose-600 text-white text-[9px] sm:text-[10px] font-black rounded-lg sm:rounded-xl hover:bg-rose-700 transition shadow-sm uppercase tracking-widest flex items-center gap-1.5 active:scale-95">
                                    <span v-if="expandedSiswa === siswa.id">✕ TUTUP</span>
                                    <span v-else>➕ BUKA</span>
                                </button>
                            </div>
                        </div>

                        <!-- Konten Buku Kasus -->
                        <div v-show="expandedSiswa === siswa.id" class="p-4 sm:p-6 bg-slate-50/50 print:p-0 print:bg-white border-t border-slate-100 print:block">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-4 print:gap-2">
                                
                                <!-- Kiri: Riwayat Poin -->
                                <div class="print:border print:border-slate-300 print:p-2 print:rounded-lg">
                                    <h5 class="text-[10px] font-black text-rose-600 uppercase tracking-widest mb-3 flex items-center gap-2 print:text-black print:mb-1 print:text-[8px]">
                                        <span class="w-2 h-2 bg-rose-500 rounded-full no-print"></span> Riwayat Pelanggaran
                                    </h5>
                                    <div class="space-y-2 print:space-y-0">
                                        <div v-if="!printTitimangsaId[siswa.id]" class="p-4 text-center border-2 border-dashed border-slate-200 rounded-xl bg-white print:hidden">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pilih periode untuk melihat data.</p>
                                        </div>
                                        <div v-else-if="!getFilteredLogs(siswa.id, siswa.poin_logs) || getFilteredLogs(siswa.id, siswa.poin_logs).length === 0" class="p-4 text-center border-2 border-dashed border-slate-200 rounded-xl bg-white print:border-none print:text-left print:p-0">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest print:text-[8px]">✨ Bersih dari pelanggaran.</p>
                                        </div>
                                        <div v-else v-for="log in getFilteredLogs(siswa.id, siswa.poin_logs)" :key="log.id" class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm print:shadow-none print:border-b print:border-slate-300 print:rounded-none print:px-0 print:py-1">
                                            <div class="flex justify-between font-black text-[11px] items-start print:text-[9px]">
                                                <span class="text-slate-800 uppercase leading-tight pr-4">
                                                    {{ log.pelanggaran ? log.pelanggaran.nama_pelanggaran : 'Poin Manual / Plus' }}
                                                </span>
                                                <span v-if="log.skor_pengurang > 0" class="text-rose-600 bg-rose-50 px-2 py-0.5 rounded border border-rose-100 shrink-0 print:border-none print:text-black print:px-2">-{{ log.skor_pengurang }}</span>
                                                <span v-if="log.skor_penambah > 0" class="text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100 shrink-0 print:border-none print:text-black print:px-2">+{{ log.skor_penambah }}</span>
                                            </div>
                                            <p class="text-[9px] text-slate-400 mt-1.5 uppercase font-bold tracking-widest print:text-slate-600 print:text-[7px] print:mt-0.5">
                                                📅 {{ formatDate(log.tanggal) }}
                                                <span v-if="log.tahun_ajaran" class="ml-2 text-indigo-600 bg-indigo-50 border border-indigo-100 px-1.5 py-0.5 rounded print:bg-transparent print:border-none print:text-black print:p-0 print:ml-1">
                                                    TA. {{ log.tahun_ajaran.tahun }} <span v-if="log.titimangsa">({{ log.titimangsa.nama_periode }})</span>
                                                </span>
                                            </p>
                                            <p v-if="log.catatan" class="text-[10px] text-slate-500 italic mt-1 bg-slate-50 p-2 rounded-lg border border-slate-100 print:bg-transparent print:border-none print:p-0 print:mt-0.5 print:text-[8px]">"{{ log.catatan }}"</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kanan: Riwayat Penanganan -->
                                <div class="print:border print:border-slate-300 print:p-2 print:rounded-lg">
                                    <h5 class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-3 flex items-center gap-2 print:text-black print:mb-1 print:text-[8px]">
                                        <span class="w-2 h-2 bg-emerald-500 rounded-full no-print"></span> Tindak Lanjut BK
                                    </h5>
                                    <div class="space-y-2 print:space-y-0">
                                        <div v-if="!printTitimangsaId[siswa.id]" class="p-4 text-center border-2 border-dashed border-slate-200 rounded-xl bg-white print:hidden">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pilih periode untuk melihat data.</p>
                                        </div>
                                        <div v-else-if="!getFilteredPenanganans(siswa.id, siswa.penanganans) || getFilteredPenanganans(siswa.id, siswa.penanganans).length === 0" class="p-4 text-center border-2 border-dashed border-slate-200 rounded-xl bg-white print:border-none print:text-left print:p-0">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest print:text-[8px]">Tidak ada catatan penanganan.</p>
                                        </div>
                                        <div v-else v-for="pen in getFilteredPenanganans(siswa.id, siswa.penanganans)" :key="pen.id" class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm print:shadow-none print:border-b print:border-slate-300 print:rounded-none print:px-0 print:py-1">
                                            <div class="flex justify-between items-center mb-2 print:mb-0.5">
                                                <span class="text-[9px] font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100 uppercase tracking-widest print:bg-transparent print:border-none print:text-black print:px-0 print:text-[8px]">{{ pen.kategori }}</span>
                                                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest print:text-slate-600 print:text-[7px]">
                                                    📅 {{ formatDateTime(pen.created_at) }}
                                                    <span v-if="pen.tahun_ajaran" class="ml-2 text-indigo-600 bg-indigo-50 border border-indigo-100 px-1.5 py-0.5 rounded print:bg-transparent print:border-none print:text-black print:p-0 print:ml-1">
                                                        TA. {{ pen.tahun_ajaran.tahun }}
                                                    </span>
                                                </span>
                                            </div>
                                            <p class="text-[10px] font-bold text-slate-700 leading-tight mt-1 print:text-black print:mt-0 print:text-[8px]"><span class="text-rose-500 print:text-black font-black">Kasus:</span> {{ pen.deskripsi_masalah }}</p>
                                            <p class="text-[10px] text-slate-600 mt-1.5 bg-emerald-50/50 p-2 rounded-lg border border-emerald-100/50 print:border-none print:bg-transparent print:p-0 print:mt-0.5 print:text-[8px]">
                                                <b class="text-emerald-600 print:text-black">Solusi:</b> {{ pen.tindakan_penyelesaian }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tanda Tangan Khusus Print -->
                            <div class="mt-8 hidden print:grid grid-cols-2 text-center text-[10px] font-black uppercase tracking-widest text-black">
                                <div>
                                    <p>Mengetahui,</p>
                                    <div class="mt-16 flex flex-col items-center">
                                        <p class="w-48 border-b border-black font-black pb-1 truncate">{{ getSelectedKelasWaliName() || '...................................' }}</p>
                                        <p class="mt-1 font-bold">Wali Kelas</p>
                                    </div>
                                </div>
                                <div>
                                    <p>Bekasi, {{ getCurrentDate() }}</p>
                                    <div class="mt-16 flex flex-col items-center">
                                        <p class="w-48 border-b border-black font-black pb-1 truncate">{{ userProfile?.name || 'Guru BK' }}</p>
                                        <p class="mt-1 font-bold">Guru BK</p>
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
  </div>
</div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'

definePageMeta({
  layout: 'bk',
  middleware: 'bk',
  title: 'Buku Kasus Siswa'
})

// UI State
const windowWidth = ref(1024)
const isDesktop = computed(() => windowWidth.value >= 1024) // lg breakpoint
const activeTabMobile = ref('dock')

const userCookie = useCookie('user_profile')
const userProfile = computed(() => {
  if (!userCookie.value) return null
  return typeof userCookie.value === 'string' ? JSON.parse(userCookie.value) : userCookie.value
})

const mobileTabs = [
  { id: 'dock', title: 'Filter', icon: '🏫' },
  { id: 'flow', title: 'Buku Kasus', icon: '📖' }
]

const isLoadingKelas = ref(true)
const isLoadingSiswa = ref(false)
const kelases = ref([])
const siswas = ref([])
const selectedKelasId = ref(null)

const titimangsaAktif = ref(null)
const tahunAktif = ref(null)
const titimangsas = ref([])

const searchKelas = ref('')
const expandedSiswa = ref(null) // Only one open for preview, but on print we show all using CSS

const printSiswaId = ref(null)
const printTitimangsaId = ref({})

onMounted(() => {
    window.addEventListener('afterprint', () => {
        printSiswaId.value = null
    })
})

const printSiswa = async (id) => {
    if (!printTitimangsaId.value[id]) {
        useSwal().toast('Pilih periode laporan terlebih dahulu', 'warning')
        return
    }
    printSiswaId.value = id
    expandedSiswa.value = id
    await nextTick()
    setTimeout(() => {
        window.print()
    }, 150)
}

const getTargetTitimangsaForSiswa = (siswaId) => {
    const targetId = printTitimangsaId.value[siswaId]
    if (!targetId) return null
    return titimangsas.value.find(t => t.id === targetId)
}

const getSemesterByMonth = (month) => {
    // 0 = Jan, 11 = Dec. Ganjil: Jul-Dec (6-11), Genap: Jan-Jun (0-5)
    return (month >= 6 && month <= 11) ? 1 : 2;
}

const getSemesterName = (target) => {
    if (!target) return '';
    const date = target.tanggal_cetak ? new Date(target.tanggal_cetak) : new Date();
    return getSemesterByMonth(date.getMonth()) === 1 ? 'SEMESTER GANJIL' : 'SEMESTER GENAP';
}

const getFilteredLogs = (siswaId, logs) => {
    if (!logs) return []
    const target = getTargetTitimangsaForSiswa(siswaId)
    if (!target) return []
    
    const targetDate = target.tanggal_cetak ? new Date(target.tanggal_cetak) : new Date();
    const targetSemester = getSemesterByMonth(targetDate.getMonth());
    
    return logs.filter(l => {
        if (l.tahun_ajaran_id !== target.tahun_ajaran_id) return false;
        const logDate = l.tanggal ? new Date(l.tanggal) : new Date(l.created_at);
        return getSemesterByMonth(logDate.getMonth()) === targetSemester;
    })
}

const getFilteredPenanganans = (siswaId, penanganans) => {
    if (!penanganans) return []
    const target = getTargetTitimangsaForSiswa(siswaId)
    if (!target) return []
    
    const targetDate = target.tanggal_cetak ? new Date(target.tanggal_cetak) : new Date();
    const targetSemester = getSemesterByMonth(targetDate.getMonth());
    
    return penanganans.filter(p => {
        if (p.tahun_ajaran_id !== target.tahun_ajaran_id) return false;
        const pDate = p.created_at ? new Date(p.created_at) : new Date();
        return getSemesterByMonth(pDate.getMonth()) === targetSemester;
    })
}

const filteredKelases = computed(() => {
    if (!searchKelas.value) return kelases.value
    const ls = searchKelas.value.toLowerCase()
    return kelases.value.filter(k => 
        k.nama_kelas.toLowerCase().includes(ls) || 
        (k.tingkat + ' ' + k.nama_kelas).toLowerCase().includes(ls)
    )
})

const filteredSiswas = computed(() => {
    return siswas.value
})

const getSelectedKelasName = () => {
    if (!selectedKelasId.value) return ''
    const kelas = kelases.value.find(k => k.id === selectedKelasId.value)
    return kelas ? `${kelas.tingkat} ${kelas.nama_kelas}` : ''
}

const getSelectedKelasWaliName = () => {
    if (!selectedKelasId.value) return ''
    const kelas = kelases.value.find(k => k.id === selectedKelasId.value)
    return kelas?.wali_kelas?.guru?.name || ''
}

const fetchInitialData = async () => {
    isLoadingKelas.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch('http://localhost:8000/api/bk/buku-kasus', {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            kelases.value = response.data.kelases
            titimangsaAktif.value = response.data.titimangsa_aktif
            tahunAktif.value = response.data.tahun_aktif
            titimangsas.value = response.data.titimangsas
        }
    } catch (error) {
        console.error('Fetch error:', error)
        useSwal().toast('Gagal memuat kelas', 'error')
    } finally {
        isLoadingKelas.value = false
    }
}

const selectKelas = async (id) => {
    selectedKelasId.value = id
    if (!isDesktop.value) activeTabMobile.value = 'flow'
    expandedSiswa.value = null
    isLoadingSiswa.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/bk/buku-kasus?kelas_id=${id}`, {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            siswas.value = response.data.siswas
            titimangsaAktif.value = response.data.titimangsa_aktif
            tahunAktif.value = response.data.tahun_aktif
            titimangsas.value = response.data.titimangsas
            
            siswas.value.forEach(s => {
                printTitimangsaId.value[s.id] = ''
            })
        }
    } catch (error) {
        console.error('Fetch error:', error)
        useSwal().toast('Gagal memuat buku kasus siswa', 'error')
    } finally {
        isLoadingSiswa.value = false
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
const formatDateTime = (dateStr) => {
    if (!dateStr) return '-'
    const d = new Date(dateStr)
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' })
}

const getCurrentDate = () => {
    return new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

onMounted(() => {
    windowWidth.value = window.innerWidth
    window.addEventListener('resize', () => { windowWidth.value = window.innerWidth })
    fetchInitialData()
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.animate-fadeIn { animation: fadeIn 0.4s ease-out forwards; }

/* PRINT STYLES */
@media print {
    .no-print { display: none !important; }
    .no-print-flex { display: none !important; }
    .no-print-interactive { pointer-events: none !important; }
    
    body { background-color: white !important; padding: 0; margin: 0; color: black; }
    main { background-color: white !important; }
    
    .print\:break-after-page { page-break-after: always; }
    .print\:hidden { display: none !important; }
    .print\:block { display: block !important; }
    
    .shadow-sm, .shadow-md, .shadow-lg, .shadow-inner, .shadow-xl, .shadow-2xl { box-shadow: none !important; }
    .bg-slate-50, .bg-rose-50, .bg-emerald-50, .bg-slate-100 { background-color: transparent !important; }
    
    * { 
        -webkit-print-color-adjust: exact !important; 
        print-color-adjust: exact !important; 
    }
}
</style>
