<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- Header (No Print) -->
    <div class="mb-8 no-print">
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Buku Kasus Siswa</h2>
        <p class="text-sm font-semibold text-slate-500 mt-1">Laporan komprehensif riwayat kedisiplinan dan pembinaan.</p>
    </div>

    <!-- MAIN GRID LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- ==============================================
             KIRI: PANEL FILTER (Col 3, No Print)
             ============================================== -->
        <div class="lg:col-span-3 space-y-6 lg:sticky lg:top-6 no-print">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
                <div class="p-6 bg-gradient-to-r from-rose-900 to-rose-800 border-b border-rose-100 flex items-center gap-4">
                    <span class="text-3xl">🏫</span>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-white">Filter Kelas</h3>
                    </div>
                </div>
                
                <div class="p-4 bg-slate-50 border-b border-slate-100">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
                        <input type="text" v-model="searchKelas" placeholder="Cari kelas..." class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all text-xs font-bold outline-none text-slate-700">
                    </div>
                </div>

                <!-- List Kelas -->
                <div class="max-h-[60vh] overflow-y-auto custom-scrollbar">
                    <div v-if="isLoadingKelas" class="p-8 text-center">
                        <div class="w-6 h-6 border-2 border-rose-400 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
                    </div>
                    <div v-else>
                        <button v-for="kelas in filteredKelases" :key="kelas.id" @click="selectKelas(kelas.id)"
                            class="w-full text-left px-5 py-4 border-b border-slate-100 hover:bg-rose-50 transition-colors flex items-center justify-between group"
                            :class="selectedKelasId === kelas.id ? 'bg-rose-50 border-l-4 border-l-rose-500' : 'bg-white border-l-4 border-l-transparent'">
                            <div>
                                <h4 class="text-xs font-black uppercase tracking-wider text-slate-700 group-hover:text-rose-700 transition-colors">{{ kelas.nama_kelas }}</h4>
                                <p class="text-[9px] font-bold text-slate-400 uppercase mt-1">{{ kelas.tingkat }} - {{ kelas.program_id ? 'Program' : 'Konsentrasi' }}</p>
                            </div>
                            <span v-if="selectedKelasId === kelas.id" class="text-rose-500">▶</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==============================================
             KANAN: LAPORAN BUKU KASUS (Col 9)
             ============================================== -->
        <div class="lg:col-span-9 space-y-6 w-full">
            
            <div v-if="!selectedKelasId" class="bg-white rounded-2xl p-20 text-center flex flex-col items-center justify-center border-2 border-dashed border-slate-200 shadow-sm no-print">
                <span class="text-7xl mb-6 opacity-30 grayscale">📖</span>
                <h3 class="text-sm font-black text-slate-700 uppercase tracking-widest">Buku Jurnal Kasus</h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase mt-3 tracking-widest max-w-sm">Pilih rombongan belajar untuk memuat dan mencetak buku kasus siswa.</p>
            </div>

            <div v-else-if="isLoadingSiswa" class="bg-white rounded-2xl p-16 flex flex-col items-center justify-center border border-slate-200/60 shadow-sm opacity-60 no-print">
                <div class="w-8 h-8 border-4 border-rose-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Buku Kasus...</span>
            </div>

            <div v-else class="space-y-6 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden print:border-none print:shadow-none">
                
                <!-- Print Header -->
                <div class="p-6 bg-slate-100 border-b border-slate-200 print:bg-white print:border-b-2 print:border-black flex justify-between items-center no-print-flex">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-xl shadow-sm border border-slate-200 print:hidden">🏫</div>
                        <div>
                            <h3 class="text-lg font-black text-slate-800 uppercase tracking-wide">{{ getSelectedKelasName() }}</h3>
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-0.5 print:text-black">Buku Kasus & Riwayat Siswa</p>
                        </div>
                    </div>
                    <button @click="printPage" class="px-5 py-2.5 bg-slate-800 text-white text-[10px] font-black rounded-xl hover:bg-black transition shadow-md uppercase tracking-widest flex items-center gap-2 active:scale-95 no-print">
                        <span>🖨️</span> CETAK
                    </button>
                </div>

                <div class="p-4 sm:p-6 space-y-4 print:p-0 print:space-y-8 bg-white">
                    <div v-if="filteredSiswas.length === 0" class="text-center py-12 border-2 border-dashed border-slate-200 rounded-xl no-print">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Tidak ada siswa.</p>
                    </div>
                    
                    <!-- Loop Siswa -->
                    <div v-for="siswa in filteredSiswas" :key="siswa.id" class="border border-slate-200 rounded-2xl overflow-hidden print:border-none print:break-after-page group bg-white shadow-sm print:shadow-none" :class="{ 'border-l-4 border-l-slate-300': true }">
                        
                        <!-- Header Siswa -->
                        <div class="p-4 flex justify-between items-center bg-slate-50 print:bg-white print:border-b-2 print:border-slate-900 print:mb-4 cursor-pointer hover:bg-slate-100 transition-colors no-print-interactive" @click="toggleExpanded(siswa.id)">
                            <div class="flex items-center gap-3 sm:gap-4">
                                <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-black no-print shadow-inner border border-slate-300 shrink-0">
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
                            <div class="no-print">
                                <button class="px-3 sm:px-4 py-2 bg-rose-600 text-white text-[9px] sm:text-[10px] font-black rounded-lg sm:rounded-xl hover:bg-rose-700 transition shadow-sm uppercase tracking-widest flex items-center gap-1.5 active:scale-95">
                                    <span v-if="expandedSiswa === siswa.id">✕ TUTUP</span>
                                    <span v-else>➕ BUKA</span>
                                </button>
                            </div>
                        </div>

                        <!-- Konten Buku Kasus -->
                        <div v-show="expandedSiswa === siswa.id" class="p-4 sm:p-6 bg-slate-50/50 print:p-0 print:bg-white border-t border-slate-100 print:block">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                                
                                <!-- Kiri: Riwayat Poin -->
                                <div class="print:border print:border-slate-300 print:p-5 print:rounded-xl">
                                    <h5 class="text-[10px] font-black text-rose-600 uppercase tracking-widest mb-4 flex items-center gap-2 print:text-black">
                                        <span class="w-2 h-2 bg-rose-500 rounded-full no-print"></span> Riwayat Pelanggaran
                                    </h5>
                                    <div class="space-y-3">
                                        <div v-if="!siswa.poin_logs || siswa.poin_logs.length === 0" class="p-6 text-center border-2 border-dashed border-slate-200 rounded-xl bg-white print:border-none print:text-left print:p-0">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">✨ Bersih dari pelanggaran.</p>
                                        </div>
                                        <div v-for="log in siswa.poin_logs" :key="log.id" class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm print:shadow-none print:border-b print:border-slate-200 print:rounded-none print:px-0">
                                            <div class="flex justify-between font-black text-[11px] items-start">
                                                <span class="text-slate-800 uppercase leading-tight pr-4">
                                                    {{ log.pelanggaran ? log.pelanggaran.nama_pelanggaran : 'Poin Manual / Plus' }}
                                                </span>
                                                <span v-if="log.skor_pengurang > 0" class="text-rose-600 bg-rose-50 px-2 py-0.5 rounded border border-rose-100 shrink-0 print:border-none print:text-black">-{{ log.skor_pengurang }}</span>
                                                <span v-if="log.skor_penambah > 0" class="text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100 shrink-0 print:border-none print:text-black">+{{ log.skor_penambah }}</span>
                                            </div>
                                            <p class="text-[9px] text-slate-400 mt-1.5 uppercase font-bold tracking-widest print:text-slate-600">📅 {{ formatDate(log.tanggal) }}</p>
                                            <p v-if="log.catatan" class="text-[10px] text-slate-500 italic mt-1 bg-slate-50 p-2 rounded-lg border border-slate-100 print:bg-transparent print:border-none print:p-0 print:mt-2">"{{ log.catatan }}"</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kanan: Riwayat Penanganan -->
                                <div class="print:border print:border-slate-300 print:p-5 print:rounded-xl">
                                    <h5 class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-4 flex items-center gap-2 print:text-black">
                                        <span class="w-2 h-2 bg-emerald-500 rounded-full no-print"></span> Tindak Lanjut BK
                                    </h5>
                                    <div class="space-y-3">
                                        <div v-if="!siswa.penanganans || siswa.penanganans.length === 0" class="p-6 text-center border-2 border-dashed border-slate-200 rounded-xl bg-white print:border-none print:text-left print:p-0">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tidak ada catatan penanganan.</p>
                                        </div>
                                        <div v-for="pen in siswa.penanganans" :key="pen.id" class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm print:shadow-none print:border-b print:border-slate-200 print:rounded-none print:px-0">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="text-[9px] font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100 uppercase tracking-widest print:bg-transparent print:border-none print:text-black print:px-0">{{ pen.kategori }}</span>
                                                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest print:text-slate-600">📅 {{ formatDateTime(pen.created_at) }}</span>
                                            </div>
                                            <p class="text-[10px] font-bold text-slate-700 leading-tight mt-1 print:text-black"><span class="text-rose-500 print:text-black font-black">Kasus:</span> {{ pen.deskripsi_masalah }}</p>
                                            <p class="text-[10px] text-slate-600 mt-1.5 bg-emerald-50/50 p-2 rounded-lg border border-emerald-100/50 print:border-none print:bg-transparent print:p-0 print:mt-2">
                                                <b class="text-emerald-600 print:text-black">Solusi:</b> {{ pen.tindakan_penyelesaian }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tanda Tangan Khusus Print -->
                            <div class="mt-16 hidden print:grid grid-cols-2 text-center text-[10px] font-black uppercase tracking-widest text-black">
                                <div>
                                    <p>Mengetahui,</p>
                                    <p class="mt-24 border-b border-black inline-block min-w-[150px]">Wali Kelas</p>
                                </div>
                                <div>
                                    <p>Bekasi, {{ getCurrentDate() }}</p>
                                    <p class="mt-24 border-b border-black inline-block min-w-[150px]">Guru BK</p>
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
import { ref, computed, onMounted } from 'vue'

definePageMeta({
  layout: 'bk',
  middleware: 'bk',
  title: 'Buku Kasus Siswa'
})

const isLoadingKelas = ref(true)
const isLoadingSiswa = ref(false)
const kelases = ref([])
const siswas = ref([])
const selectedKelasId = ref(null)

const searchKelas = ref('')
const expandedSiswa = ref(null) // Only one open for preview, but on print we show all using CSS

const filteredKelases = computed(() => {
    if (!searchKelas.value) return kelases.value
    const ls = searchKelas.value.toLowerCase()
    return kelases.value.filter(k => k.nama_kelas.toLowerCase().includes(ls))
})

const filteredSiswas = computed(() => {
    return siswas.value
})

const getSelectedKelasName = () => {
    if (!selectedKelasId.value) return ''
    const kelas = kelases.value.find(k => k.id === selectedKelasId.value)
    return kelas ? kelas.nama_kelas : ''
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
        }
    } catch (error) {
        console.error('Fetch error:', error)
    } finally {
        isLoadingKelas.value = false
    }
}

const selectKelas = async (id) => {
    selectedKelasId.value = id
    expandedSiswa.value = null
    isLoadingSiswa.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/bk/buku-kasus?kelas_id=${id}`, {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            siswas.value = response.data.siswas
        }
    } catch (error) {
        console.error('Fetch error:', error)
    } finally {
        isLoadingSiswa.value = false
    }
}

const toggleExpanded = (id) => {
    expandedSiswa.value = expandedSiswa.value === id ? null : id
}

const printPage = () => {
    window.print()
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
    fetchInitialData()
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }

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
