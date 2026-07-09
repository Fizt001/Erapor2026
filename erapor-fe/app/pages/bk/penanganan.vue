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

        <div class="px-6 pb-6 bg-white shrink-0">
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

      <!-- Panel Flow Kanan (Daftar Siswa & Penanganan) -->
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
            
            <div class="relative w-full sm:w-64" v-if="selectedKelasId && !isLoadingSiswa">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
                <input type="text" v-model="searchSiswa" placeholder="Cari nama/NIS siswa..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all text-xs font-bold outline-none text-slate-700">
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
                            <div class="text-right bg-white px-4 py-1.5 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3">
                                <div>
                                    <p class="text-[8px] font-black uppercase tracking-widest text-slate-400 mb-0.5">Status Kedisiplinan</p>
                                    <p class="text-xs font-black leading-none" :class="siswa.sisa_poin < 50 ? 'text-rose-600' : 'text-emerald-600'">Sisa Poin: {{ siswa.sisa_poin }}</p>
                                </div>
                                <span v-if="siswa.penanganans && siswa.penanganans.some(p => p.status === 'Proses' || p.status === 'belum_diproses')" class="w-3 h-3 rounded-full bg-rose-500 animate-pulse shadow-lg shadow-rose-500/50" title="Ada Kasus Diproses"></span>
                            </div>
                            <button class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-100 text-slate-400 group-hover:bg-rose-100 group-hover:text-rose-600 transition-all">
                                <span class="transform transition-transform duration-300" :class="{ 'rotate-180': expandedSiswa === siswa.id }">▼</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Area Expanded (Riwayat Kasus) -->
                    <div v-if="expandedSiswa === siswa.id" class="p-5 border-t border-slate-100 animate-slideUpFade bg-white">
                        <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-4">
                            <h5 class="text-[11px] font-black text-slate-500 uppercase tracking-widest">Riwayat Penanganan / SP</h5>
                            <button @click.stop="openModal(siswa)" class="bg-gradient-to-r from-rose-500 to-rose-600 hover:-translate-y-0.5 text-white px-4 py-2 rounded-xl font-bold shadow-lg shadow-rose-500/30 transition-all text-[10px] uppercase tracking-widest flex items-center gap-2">
                                <span>➕</span> Catat Kasus
                            </button>
                        </div>
                        
                        <div v-if="siswa.penanganans && siswa.penanganans.length > 0" class="space-y-4">
                            <div v-for="kasus in siswa.penanganans" :key="kasus.id" class="bg-white border rounded-xl overflow-hidden shadow-sm transition-all hover:shadow-md" :class="kasus.status === 'Proses' ? 'border-rose-200' : 'border-slate-200'">
                                <div class="px-4 py-3 border-b flex justify-between items-center" :class="(kasus.status === 'Proses' || kasus.status === 'belum_diproses') ? 'bg-rose-50 border-rose-100' : 'bg-slate-50 border-slate-100'">
                                    <div class="flex items-center gap-3">
                                        <span class="text-[10px] font-black text-white px-2 py-1 rounded-md shadow-sm" :class="getKategoriColor(kasus.kategori)">{{ kasus.kategori }}</span>
                                        <span class="text-[10px] font-bold text-slate-500">{{ formatDate(kasus.created_at) }}</span>
                                    </div>
                                    <span class="text-[9px] font-black uppercase tracking-widest px-3 py-1 rounded-full shadow-sm" :class="kasus.status === 'belum_diproses' ? 'bg-amber-500 text-white animate-pulse' : (kasus.status === 'Proses' ? 'bg-rose-500 text-white animate-pulse' : 'bg-emerald-100 text-emerald-700 border border-emerald-200')">
                                        {{ kasus.status === 'belum_diproses' ? 'Belum Diproses' : kasus.status }}
                                    </span>
                                </div>
                                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                                    <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                                        <p class="font-black text-[10px] text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5"><span class="text-rose-500">🚨</span> Masalah / Alasan</p>
                                        <p class="text-slate-700 font-semibold leading-relaxed">{{ kasus.deskripsi_masalah }}</p>
                                    </div>
                                    <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                                        <p class="font-black text-[10px] text-slate-400 uppercase tracking-widest mb-1.5 flex items-center gap-1.5"><span class="text-indigo-500">💡</span> Tindakan / Solusi</p>
                                        <p class="text-slate-700 font-semibold leading-relaxed">{{ kasus.tindakan_penyelesaian }}</p>
                                    </div>
                                </div>
                                <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-100 flex justify-between items-center group/actions">
                                    <p class="text-[9px] text-slate-400 font-black uppercase tracking-widest flex items-center gap-1.5">
                                        <span>👤</span> Petugas: {{ kasus.guru?.name || 'BK' }}
                                    </p>
                                    <div class="flex gap-2">
                                        <button @click.stop="openModal(siswa, kasus)" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-[10px] font-bold text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 hover:border-indigo-200 transition-all uppercase tracking-widest flex items-center gap-1 shadow-sm">
                                            ✏️ Edit
                                        </button>
                                        <button @click.stop="confirmDelete(kasus.id)" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-[10px] font-bold text-rose-600 hover:text-rose-700 hover:bg-rose-50 hover:border-rose-200 transition-all uppercase tracking-widest flex items-center gap-1 shadow-sm opacity-0 group-hover/actions:opacity-100">
                                            🗑️ Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            <span class="text-3xl opacity-30 block mb-3">✨</span>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Belum ada catatan SP / Penanganan.</p>
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
         MODAL FORM PENANGANAN
         ============================================== -->
    <div v-if="isModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade">
            <!-- Header -->
            <div class="px-6 py-5 bg-gradient-to-r from-rose-50 to-white border-b border-rose-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-rose-900 tracking-tight flex items-center gap-2">
                        <span>{{ editId ? '✏️' : '➕' }}</span> {{ editId ? 'Edit' : 'Catat' }} Penanganan
                    </h3>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1 ml-8">{{ targetSiswa?.nama }} ({{ targetSiswa?.nis }})</p>
                </div>
                <button @click="closeModal" class="text-slate-400 hover:text-rose-500 transition-colors bg-white w-8 h-8 rounded-full flex items-center justify-center border border-slate-200 shadow-sm">✕</button>
            </div>

            <!-- Body -->
            <form @submit.prevent="saveData" class="p-6 space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Kategori (SP)</label>
                        <select v-model="form.kategori" required class="w-full px-4 py-3 bg-white border-2 border-slate-200/70 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all text-xs font-bold text-slate-700 outline-none">
                            <option value="" disabled>-- Pilih Kategori --</option>
                            <option v-for="kat in kategoriList" :key="kat.id" :value="kat.nama">{{ kat.nama }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Status Penanganan</label>
                        <select v-model="form.status" required class="w-full px-4 py-3 bg-white border-2 border-slate-200/70 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all text-xs font-bold outline-none" :class="form.status === 'Proses' || form.status === 'belum_diproses' ? 'text-rose-600' : 'text-emerald-600'">
                            <option value="belum_diproses">Belum Diproses (Eskalasi Otomatis)</option>
                            <option value="Proses">Sedang Diproses</option>
                            <option value="Selesai">Kasus Selesai</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Deskripsi Masalah / Alasan SP</label>
                    <textarea v-model="form.deskripsi_masalah" required rows="2" placeholder="Jelaskan secara singkat akar permasalahan..." class="w-full px-4 py-3 bg-white border-2 border-slate-200/70 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all text-xs font-semibold text-slate-700 outline-none resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Tindakan / Solusi yang Diberikan</label>
                    <textarea v-model="form.tindakan_penyelesaian" required rows="2" placeholder="Contoh: Pemanggilan Orang Tua, Skorsing..." class="w-full px-4 py-3 bg-white border-2 border-slate-200/70 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all text-xs font-semibold text-slate-700 outline-none resize-none"></textarea>
                </div>

                <!-- Footer -->
                <div class="pt-4 mt-6 border-t border-slate-100 flex gap-3">
                    <button type="button" @click="closeModal" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">Batal</button>
                    <button type="submit" :disabled="isSaving" class="flex-1 py-3 bg-gradient-to-r from-rose-500 to-rose-600 hover:-translate-y-0.5 disabled:bg-rose-400 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        <span v-if="isSaving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        <span v-else>Simpan Data</span>
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
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Hapus Kasus?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Yakin ingin menghapus catatan penanganan ini secara permanen? Data tidak dapat dikembalikan.
                </p>
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isDeleteModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">Batal</button>
                    <button @click="executeDelete" class="flex-1 py-3 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        <span>Hapus</span>
                    </button>
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
  title: 'Penanganan Kasus'
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
const kategoriList = ref([])
const selectedKelasId = ref(null)
const activeTahunAjaran = ref('')

const searchKelas = ref('')
const searchSiswa = ref('')
const expandedSiswa = ref(null)

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
        const response = await $fetch('http://localhost:8000/api/bk/penanganan', {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            kelases.value = response.data.kelases
            kategoriList.value = response.data.kategoriList || []
            if (response.data.tahun_aktif) {
                activeTahunAjaran.value = response.data.tahun_aktif.tahun
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
        const response = await $fetch(`http://localhost:8000/api/bk/penanganan?kelas_id=${id}`, {
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

const toggleExpanded = (id) => {
    expandedSiswa.value = expandedSiswa.value === id ? null : id
}

// FORMAT TANGGAL
const formatDate = (dateStr) => {
    if (!dateStr) return '-'
    const d = new Date(dateStr)
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

const getKategoriColor = (kat) => {
    if (!kat) return 'bg-indigo-500'
    if (kat.includes('Eskalasi')) return 'bg-amber-600'
    switch (kat) {
        case 'SP1 BK': return 'bg-amber-500'
        case 'SP2 BK': return 'bg-orange-500'
        case 'SP3 Kurikulum': return 'bg-rose-600'
        case 'SP1': return 'bg-amber-500'
        case 'SP2': return 'bg-orange-500'
        case 'SP3': return 'bg-rose-600'
        default: return 'bg-indigo-500'
    }
}

// MODAL FORM
const isModalOpen = ref(false)
const isSaving = ref(false)
const editId = ref(null)
const targetSiswa = ref(null)

const form = ref({
    siswa_id: '',
    kategori: '',
    deskripsi_masalah: '',
    tindakan_penyelesaian: '',
    status: 'Proses'
})

const openModal = (siswa, kasus = null) => {
    targetSiswa.value = siswa
    if (kasus) {
        editId.value = kasus.id
        form.value = {
            siswa_id: siswa.id,
            kategori: kasus.kategori,
            deskripsi_masalah: kasus.deskripsi_masalah,
            tindakan_penyelesaian: kasus.tindakan_penyelesaian,
            status: kasus.status
        }
    } else {
        editId.value = null
        form.value = {
            siswa_id: siswa.id,
            kategori: '',
            deskripsi_masalah: '',
            tindakan_penyelesaian: '',
            status: 'Proses'
        }
    }
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
    editId.value = null
    targetSiswa.value = null
}

const saveData = async () => {
    isSaving.value = true
    const token = useCookie('auth_token').value
    try {
        const url = editId.value 
            ? `http://localhost:8000/api/bk/penanganan/${editId.value}`
            : `http://localhost:8000/api/bk/penanganan`
            
        const method = editId.value ? 'PUT' : 'POST'
        
        const response = await $fetch(url, {
            method,
            headers: { Authorization: `Bearer ${token}` },
            body: form.value
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

// DELETE PENANGANAN
const isDeleteModalOpen = ref(false)
const deleteTargetId = ref(null)

const confirmDelete = (id) => {
    deleteTargetId.value = id
    isDeleteModalOpen.value = true
}

const executeDelete = async () => {
    if (!deleteTargetId.value) return
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/bk/penanganan/${deleteTargetId.value}`, {
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
