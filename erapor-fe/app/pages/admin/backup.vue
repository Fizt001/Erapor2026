<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    
<!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-2 grid grid-cols-2 gap-2 z-20">
        <button type="button" @click="activeTab = 'generate'" :class="activeTab === 'generate' ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 ring-2 ring-emerald-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'" class="rounded-xl flex flex-col items-center justify-center py-2 px-1 transition-all active:scale-95">
          <span class="text-lg mb-0.5 transition-transform" :class="activeTab === 'generate' ? 'scale-110' : ''">🛡️</span>
          <span class="text-[10px] font-black uppercase tracking-wider text-center leading-none">Generate</span>
        </button>
        <button type="button" @click="activeTab = 'riwayat'" :class="activeTab === 'riwayat' ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 ring-2 ring-emerald-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'" class="rounded-xl flex flex-col items-center justify-center py-2 px-1 transition-all active:scale-95">
          <span class="text-lg mb-0.5 transition-transform" :class="activeTab === 'riwayat' ? 'scale-110' : ''">📋</span>
          <span class="text-[10px] font-black uppercase tracking-wider text-center leading-none">Riwayat</span>
        </button>
      </div>

      <!-- Panel Dock Kiri -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all', activeTab === 'generate' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[60px]' : '']">
        
        <div class="p-6 shrink-0">
          <div class="bg-gradient-to-r from-teal-600 to-emerald-700 rounded-2xl p-5 border border-teal-500 shadow-sm relative overflow-hidden flex items-center gap-4">
            <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0 relative z-10">🛡️</div>
            <div class="relative z-10">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">Generate Backup</h3>
                <p class="text-[10px] text-teal-100 font-semibold uppercase mt-0.5">Pilih Konteks Data</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-20 text-white">
              <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 11v2h-2v2h2v2h-2v2h-2v-2h-2v2h-2v-2H9v2H7v-2H5v-2h2v-2H5v-2h2V9H5V7h2V5h2v2h2V5h2v2h2V5h2v2h2v2h-2v2h2zm-2-2h-2v2h2V9zm-4 4h-2v2h2v-2zm-4-4H7v2h2V9z"></path></svg>
            </div>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar p-6">
            <!-- Role Selection Tabs -->
            <div class="grid grid-cols-2 gap-3 mb-3">
                <!-- Admin -->
                <button 
                    @click="activeRole = 'admin'"
                    class="shrink-0 flex flex-col items-center justify-center p-3 rounded-2xl border-2 transition-all duration-300 text-center relative group overflow-hidden"
                    :class="activeRole === 'admin' ? 'bg-emerald-50 border-emerald-500' : 'border-slate-100 hover:border-slate-200 hover:bg-slate-50'"
                >
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xl mb-1.5" :class="activeRole === 'admin' ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 grayscale opacity-70 group-hover:grayscale-0'">⚙️</div>
                    <p class="font-black text-[11px] text-slate-800">Admin</p>
                    <p class="text-[8px] uppercase font-bold text-slate-500 leading-tight">Master Data</p>
                    <div v-if="activeRole === 'admin'" class="absolute top-2 right-2.5 text-emerald-500 font-bold text-xs">✓</div>
                </button>
                
                <!-- Kurikulum -->
                <button 
                    @click="activeRole = 'kurikulum'"
                    class="shrink-0 flex flex-col items-center justify-center p-3 rounded-2xl border-2 transition-all duration-300 text-center relative group overflow-hidden"
                    :class="activeRole === 'kurikulum' ? 'bg-blue-50 border-blue-500' : 'border-slate-100 hover:border-slate-200 hover:bg-slate-50'"
                >
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xl mb-1.5" :class="activeRole === 'kurikulum' ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 grayscale opacity-70 group-hover:grayscale-0'">📚</div>
                    <p class="font-black text-[11px] text-slate-800">Kurikulum</p>
                    <p class="text-[8px] uppercase font-bold text-slate-500 leading-tight">Struktur & Pengampu</p>
                    <div v-if="activeRole === 'kurikulum'" class="absolute top-2 right-2.5 text-blue-500 font-bold text-xs">✓</div>
                </button>

                <!-- Guru -->
                <button 
                    @click="activeRole = 'guru'"
                    class="shrink-0 flex flex-col items-center justify-center p-3 rounded-2xl border-2 transition-all duration-300 text-center relative group overflow-hidden"
                    :class="activeRole === 'guru' ? 'bg-amber-50 border-amber-500' : 'border-slate-100 hover:border-slate-200 hover:bg-slate-50'"
                >
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xl mb-1.5" :class="activeRole === 'guru' ? 'bg-amber-100 text-amber-600' : 'bg-slate-100 grayscale opacity-70 group-hover:grayscale-0'">👨‍🏫</div>
                    <p class="font-black text-[11px] text-slate-800">Guru</p>
                    <p class="text-[8px] uppercase font-bold text-slate-500 leading-tight">Nilai & TP</p>
                    <div v-if="activeRole === 'guru'" class="absolute top-2 right-2.5 text-amber-500 font-bold text-xs">✓</div>
                </button>

                <!-- Wali Kelas -->
                <button 
                    @click="activeRole = 'walikelas'"
                    class="shrink-0 flex flex-col items-center justify-center p-3 rounded-2xl border-2 transition-all duration-300 text-center relative group overflow-hidden"
                    :class="activeRole === 'walikelas' ? 'bg-purple-50 border-purple-500' : 'border-slate-100 hover:border-slate-200 hover:bg-slate-50'"
                >
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xl mb-1.5" :class="activeRole === 'walikelas' ? 'bg-purple-100 text-purple-600' : 'bg-slate-100 grayscale opacity-70 group-hover:grayscale-0'">👨‍👩‍👧‍👦</div>
                    <p class="font-black text-[11px] text-slate-800">Wali Kelas</p>
                    <p class="text-[8px] uppercase font-bold text-slate-500 leading-tight">Absensi & Ekskul</p>
                    <div v-if="activeRole === 'walikelas'" class="absolute top-2 right-2.5 text-purple-500 font-bold text-xs">✓</div>
                </button>

                <!-- BK -->
                <button 
                    @click="activeRole = 'bk'"
                    class="shrink-0 flex flex-col items-center justify-center p-3 rounded-2xl border-2 transition-all duration-300 text-center relative group overflow-hidden"
                    :class="activeRole === 'bk' ? 'bg-rose-50 border-rose-500' : 'border-slate-100 hover:border-slate-200 hover:bg-slate-50'"
                >
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xl mb-1.5" :class="activeRole === 'bk' ? 'bg-rose-100 text-rose-600' : 'bg-slate-100 grayscale opacity-70 group-hover:grayscale-0'">🛡️</div>
                    <p class="font-black text-[11px] text-slate-800">Konseling</p>
                    <p class="text-[8px] uppercase font-bold text-slate-500 leading-tight">Poin & Kasus</p>
                    <div v-if="activeRole === 'bk'" class="absolute top-2 right-2.5 text-rose-500 font-bold text-xs">✓</div>
                </button>

                <!-- Siswa -->
                <button 
                    @click="activeRole = 'siswa'"
                    class="shrink-0 flex flex-col items-center justify-center p-3 rounded-2xl border-2 transition-all duration-300 text-center relative group overflow-hidden"
                    :class="activeRole === 'siswa' ? 'bg-indigo-50 border-indigo-500' : 'border-slate-100 hover:border-slate-200 hover:bg-slate-50'"
                >
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xl mb-1.5" :class="activeRole === 'siswa' ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-100 grayscale opacity-70 group-hover:grayscale-0'">🎓</div>
                    <p class="font-black text-[11px] text-slate-800">Siswa</p>
                    <p class="text-[8px] uppercase font-bold text-slate-500 leading-tight">Rapor Akademik</p>
                    <div v-if="activeRole === 'siswa'" class="absolute top-2 right-2.5 text-indigo-500 font-bold text-xs">✓</div>
                </button>
            </div>

                <!-- Kepsek -->
                <button 
                    @click="activeRole = 'kepsek'"
                    class="w-full shrink-0 flex items-center p-3 rounded-2xl border-2 transition-all duration-300 text-left relative group overflow-hidden mb-6"
                    :class="activeRole === 'kepsek' ? 'bg-teal-50 border-teal-500' : 'border-slate-100 hover:border-slate-200 hover:bg-slate-50'"
                >
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xl mr-3" :class="activeRole === 'kepsek' ? 'bg-teal-100 text-teal-600' : 'bg-slate-100 grayscale opacity-70 group-hover:grayscale-0'">🏢</div>
                    <div>
                        <p class="font-black text-xs text-slate-800">Kepala Sekolah</p>
                        <p class="text-[9px] uppercase font-bold text-slate-500 mt-0.5">Laporan & Evaluasi</p>
                    </div>
                    <div v-if="activeRole === 'kepsek'" class="absolute right-3 text-teal-500 font-bold">✓</div>
                </button>
            
            <hr class="border-slate-100 mb-6">
            
            <!-- Action Buttons -->
            <div class="space-y-3 pb-8">
                <button 
                    @click="generateBackup('psas')" 
                    :disabled="isGenerating || isMaintenance"
                    class="w-full py-3 bg-slate-800 text-white font-bold rounded-2xl shadow-md hover:bg-slate-900 transition-all flex items-center justify-center gap-3 uppercase tracking-widest text-[10px] border border-slate-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span class="text-lg">📥</span> Backup Semester Ganjil
                </button>

                <button 
                    @click="generateBackup('psat')" 
                    :disabled="isGenerating || isMaintenance"
                    class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-2xl shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-3 uppercase tracking-widest text-[10px] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                >
                    <span v-if="isGenerating" class="animate-spin text-lg">⏳</span>
                    <span v-else class="text-lg">📦</span> Backup Semester Genap
                </button>
            </div>

        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0', activeTab === 'riwayat' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[60px]' : '']">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            
            <div class="p-6 bg-white border-b border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shrink-0 z-10">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 shadow-sm border border-slate-200 flex items-center justify-center text-2xl hidden sm:flex">📋</div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-emerald-700">Riwayat Backup</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Daftar arsip yang tersedia</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button @click="triggerUpload" class="h-10 px-4 rounded-xl bg-indigo-50 text-indigo-600 font-bold text-[10px] uppercase tracking-widest flex items-center gap-2 hover:bg-indigo-100 transition-colors border border-indigo-200">
                        <span>📤</span> Upload
                    </button>
                    <input type="file" ref="fileInput" class="hidden" accept=".json" @change="handleFileUpload">
                    
                    <button @click="fetchBackups" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-slate-200 transition-colors border border-slate-200" title="Refresh">
                        🔄
                    </button>
                </div>
            </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60">
            <div class="w-8 h-8 border-4 border-slate-400 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Riwayat...</span>
        </div>

        <!-- Empty State -->
        <div v-else-if="!backups || backups.length === 0" class="flex-grow flex items-center justify-center flex-col p-16 text-center bg-white m-6 rounded-2xl border border-slate-200">
            <div class="text-6xl opacity-20 mb-4">🏜️</div>
            <h3 class="text-lg font-black text-slate-700 mb-2 uppercase tracking-widest">Kosong</h3>
            <p class="text-sm font-bold text-slate-500">Belum ada file backup.</p>
            <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-2 font-bold">Gunakan panel di sebelah kiri untuk men-generate arsip.</p>
        </div>

        <!-- Table Content -->
        <div v-else class="flex-1 overflow-y-auto overflow-x-auto custom-scrollbar relative bg-white">
            <table class="w-full text-left border-collapse min-w-[600px] whitespace-nowrap">
                <thead class="sticky top-0 z-10 bg-slate-50 border-b border-slate-200 shadow-sm">
                    <tr class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-500">
                        <th class="p-4 w-16 text-center">#</th>
                        <th class="p-4">Informasi File</th>
                        <th class="p-4">Waktu Arsip</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-xs">
                    <tr v-for="(backup, index) in backups" :key="backup.filename" class="border-b border-slate-100 hover:bg-slate-50/80 transition-colors group bg-white">
                        <td class="p-4 text-center text-xs font-bold text-slate-300">
                            {{ index + 1 }}
                        </td>
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="text-3xl" :class="getRoleColorText(backup.role)">{{ getRoleIcon(backup.role) }}</div>
                                <div>
                                    <p class="font-black text-slate-800 font-mono text-xs">{{ backup.filename }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">{{ backup.size }}</span>
                                        <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded border" :class="getRoleColorBadge(backup.role)">{{ backup.role }}</span>
                                        <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded border border-slate-200 bg-slate-800 text-white">{{ backup.mode }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            <p class="text-[11px] font-bold text-slate-700">{{ formatDate(backup.timestamp) }}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ formatTime(backup.timestamp) }}</p>
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-2 opacity-100 xl:opacity-0 xl:group-hover:opacity-100 transition-opacity">
                                <button @click="confirmRestore(backup.filename)" class="w-8 h-8 rounded-lg bg-white border border-amber-200 text-amber-500 hover:text-white hover:bg-amber-500 flex items-center justify-center transition-all shadow-sm" title="Restore Data">🔄</button>
                                <button @click="downloadBackup(backup.filename, 'json')" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-blue-500 hover:border-blue-200 hover:bg-blue-50 flex items-center justify-center transition-all shadow-sm" title="Download JSON">{ }</button>
                                <button @click="downloadBackup(backup.filename, 'excel')" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-emerald-500 hover:border-emerald-200 hover:bg-emerald-50 flex items-center justify-center transition-all shadow-sm" title="Download Excel">📊</button>
                                <button @click="confirmDelete(backup.filename)" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 flex items-center justify-center transition-all shadow-sm" title="Hapus">🗑️</button>
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

    <!-- ==============================================
         MODAL KONFIRMASI HAPUS (Overlay)
         ============================================== -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade text-center">
            <div class="p-8">
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner border-4 border-white ring-4 ring-rose-50">
                    ⚠️
                </div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Hapus Arsip?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Anda yakin ingin menghapus arsip:<br>
                    <span class="font-bold text-slate-800 font-mono bg-slate-100 px-1 py-0.5 rounded">{{ deleteTarget }}</span>?
                </p>
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isDeleteModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">
                        Batal
                    </button>
                    <button @click="executeDelete" :disabled="isDeleting" class="flex-1 py-3 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        <span v-if="isDeleting" class="animate-spin text-base">⏳</span>
                        <span v-else>Hapus</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==============================================
         MODAL KONFIRMASI RESTORE (Overlay)
         ============================================== -->
    <div v-if="isRestoreModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade text-center">
            <div class="p-8">
                <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner border-4 border-white ring-4 ring-amber-50">
                    ⚠️
                </div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight uppercase">Peringatan Keras!</h3>
                <p class="text-xs text-slate-600 mt-3 leading-relaxed">
                    Aksi RESTORE akan <strong class="text-rose-600">MENGHAPUS & MENGGANTIKAN</strong> data sistem yang sedang berjalan saat ini dengan data dari:<br><br>
                    <span class="font-bold text-slate-800 font-mono text-[10px] bg-slate-100 p-2 rounded block">{{ restoreTarget }}</span><br>
                    <span class="text-xs text-rose-500 font-bold">Pastikan Anda sudah mem-backup data terbaru!</span>
                </p>
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isRestoreModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">
                        Batal
                    </button>
                    <button @click="executeRestore" :disabled="isRestoring" class="flex-1 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-2xl shadow-lg shadow-amber-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        <span v-if="isRestoring" class="animate-spin text-base">⏳</span>
                        <span v-else>Ya, Restore Data</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'

definePageMeta({
  layout: 'admin',
  middleware: 'admin',
  title: 'Backup & Arsip'
})

const windowWidth = ref(1024) 
const isDesktop = computed(() => windowWidth.value >= 1280) // xl breakpoint

const activeTab = ref('generate')
const activeRole = ref('admin')
const backups = ref([])
const isLoading = ref(true)
const isGenerating = ref(false)

const maintenanceRoles = []
const isMaintenance = computed(() => maintenanceRoles.includes(activeRole.value))

const fileInput = ref(null)

const isDeleteModalOpen = ref(false)
const isDeleting = ref(false)
const deleteTarget = ref('')

const isRestoreModalOpen = ref(false)
const isRestoring = ref(false)
const restoreTarget = ref('')

// Helpers
const getRoleIcon = (role) => {
    if(role.toLowerCase() === 'admin') return '⚙️'
    if(role.toLowerCase() === 'guru') return '👨‍🏫'
    if(role.toLowerCase() === 'walikelas') return '👨‍👩‍👧‍👦'
    return '📄'
}

const getRoleColorText = (role) => {
    if(role.toLowerCase() === 'admin') return 'text-emerald-500'
    if(role.toLowerCase() === 'kurikulum') return 'text-blue-500'
    return 'text-slate-500'
}

const getRoleColorBadge = (role) => {
    if(role.toLowerCase() === 'admin') return 'bg-emerald-50 border-emerald-200 text-emerald-700'
    if(role.toLowerCase() === 'kurikulum') return 'bg-blue-50 border-blue-200 text-blue-700'
    return 'bg-slate-50 border-slate-200 text-slate-700'
}

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' }
    return new Date(dateString).toLocaleDateString('id-ID', options)
}

const formatTime = (dateString) => {
    const options = { hour: '2-digit', minute: '2-digit' }
    return new Date(dateString).toLocaleTimeString('id-ID', options) + ' WIB'
}

// API Calls
const fetchBackups = async () => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/admin/backup/list`, {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            backups.value = response.data
        }
    } catch (error) {
        console.error('Failed to fetch backups:', error)
    } finally {
        isLoading.value = false
    }
}

const generateBackup = async (mode) => {
    isGenerating.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/admin/backup/generate`, {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` },
            body: {
                role: activeRole.value,
                mode: mode
            }
        })
        if (response.success) {
            useSwal().toast(response.message)
            fetchBackups()
            if (!isDesktop.value) activeTab.value = 'riwayat'
        }
    } catch (error) {
        console.error('Backup generation failed:', error)
        useSwal().toast('Gagal men-generate backup.', 'error')
    } finally {
        isGenerating.value = false
    }
}

const downloadBackup = (filename, format = 'json') => {
    const token = useCookie('auth_token').value
    
    $fetch(`http://localhost:8000/api/admin/backup/download/${filename}?format=${format}`, {
        headers: { Authorization: `Bearer ${token}` },
        responseType: 'blob'
    }).then(blob => {
        const url = window.URL.createObjectURL(blob)
        const a = document.createElement('a')
        a.style.display = 'none'
        a.href = url
        // if excel, filename becomes .zip
        let downloadFilename = filename;
        if(format === 'excel') {
            downloadFilename = filename.replace('.json', '.zip');
        }
        a.download = downloadFilename
        document.body.appendChild(a)
        a.click()
        window.URL.revokeObjectURL(url)
    }).catch(err => {
        console.error('Download failed:', err)
        useSwal().toast('Gagal mengunduh file backup.', 'error')
    })
}

const triggerUpload = () => {
    fileInput.value.click()
}

const handleFileUpload = async (event) => {
    const file = event.target.files[0]
    if (!file) return
    
    if (file.type !== 'application/json' && !file.name.endsWith('.json')) {
        useSwal().toast('Hanya file JSON yang diizinkan', 'error')
        return
    }

    const formData = new FormData()
    formData.append('file', file)

    const token = useCookie('auth_token').value
    try {
        const response = await $fetch('http://localhost:8000/api/admin/backup/upload', {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` },
            body: formData
        })
        if (response.success) {
            useSwal().toast(response.message)
            fetchBackups()
        }
    } catch (error) {
        console.error('Upload failed:', error)
        useSwal().toast('Gagal mengunggah file backup.', 'error')
    } finally {
        event.target.value = '' // reset input
    }
}

const confirmRestore = (filename) => {
    restoreTarget.value = filename
    isRestoreModalOpen.value = true
}

const executeRestore = async () => {
    if (!restoreTarget.value) return
    isRestoring.value = true
    isRestoreModalOpen.value = false // Tutup modal saat loading
    const filename = restoreTarget.value
    const token = useCookie('auth_token').value
    try {
        useSwal().fire({
            title: 'Me-restore Data...',
            html: 'Mohon tunggu, jangan tutup halaman ini.',
            allowOutsideClick: false,
            didOpen: () => {
                useSwal().showLoading()
            }
        })
        const response = await $fetch(`http://localhost:8000/api/admin/backup/restore/${filename}`, {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            useSwal().fire('Berhasil!', 'Berhasil me-restore data sistem!', 'success').then(() => {
                window.location.reload()
            })
        }
    } catch (error) {
        console.error('Restore failed:', error)
        useSwal().fire('Gagal', 'Gagal me-restore data: ' + (error.data?.message || 'Terjadi kesalahan sistem.'), 'error')
    } finally {
        isRestoring.value = false
    }
}

const confirmDelete = (filename) => {
    deleteTarget.value = filename
    isDeleteModalOpen.value = true
}

const executeDelete = async () => {
    if (!deleteTarget.value) return
    isDeleting.value = true
    const filename = deleteTarget.value
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/admin/backup/${filename}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            isDeleteModalOpen.value = false
            useSwal().toast(response.message)
            fetchBackups()
        }
    } catch (error) {
        console.error('Delete failed:', error)
        useSwal().toast('Gagal menghapus arsip backup.', 'error')
    } finally {
        isDeleting.value = false
    }
}

onMounted(() => {
    windowWidth.value = window.innerWidth
    window.addEventListener('resize', () => { windowWidth.value = window.innerWidth })
    if (isDesktop.value) {
        activeTab.value = 'generate'
    } else {
        activeTab.value = 'riwayat'
    }
    fetchBackups()
})
</script>

<style scoped>
/* Scrollbar styling */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #94a3b8;
}
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }
</style>
