<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- Header -->
    <div class="mb-8">
      <h2 class="text-2xl font-black text-slate-800 tracking-tight">Backup & Arsip Data</h2>
      <p class="text-sm font-semibold text-slate-500 mt-1">Lakukan pencadangan (backup) data sistem secara berkala untuk masing-masing peran pengguna.</p>
    </div>

    <!-- MAIN GRID LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      
      <!-- ==============================================
           KIRI: PANEL ROLE SELECTION (Col 4)
           ============================================== -->
      <div class="lg:col-span-4 space-y-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-100 flex items-center gap-4">
                <span class="text-3xl text-emerald-400">🛡️</span>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-white">Generate Backup</h3>
                    <p class="text-[10px] text-emerald-400 font-semibold uppercase mt-0.5">Pilih Konteks Data</p>
                </div>
            </div>
            
            <div class="p-6">
                <!-- Role Selection Tabs -->
                <div class="flex flex-col gap-3 mb-6 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                    <!-- Admin -->
                    <button 
                        @click="activeRole = 'admin'"
                        class="shrink-0 flex items-center p-3 rounded-2xl border-2 transition-all duration-300 text-left relative group overflow-hidden"
                        :class="activeRole === 'admin' ? 'bg-emerald-50 border-emerald-500' : 'border-slate-100 hover:border-slate-200 hover:bg-slate-50'"
                    >
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xl mr-3" :class="activeRole === 'admin' ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 grayscale opacity-70 group-hover:grayscale-0'">⚙️</div>
                        <div>
                            <p class="font-black text-xs text-slate-800">Admin</p>
                            <p class="text-[9px] uppercase font-bold text-slate-500 mt-0.5">Master Data Sistem</p>
                        </div>
                        <div v-if="activeRole === 'admin'" class="absolute right-3 text-emerald-500 font-bold">✓</div>
                    </button>
                    
                    <!-- Kurikulum -->
                    <button 
                        @click="activeRole = 'kurikulum'"
                        class="shrink-0 flex items-center p-3 rounded-2xl border-2 transition-all duration-300 text-left relative group overflow-hidden"
                        :class="activeRole === 'kurikulum' ? 'bg-blue-50 border-blue-500' : 'border-slate-100 hover:border-slate-200 hover:bg-slate-50'"
                    >
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xl mr-3" :class="activeRole === 'kurikulum' ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 grayscale opacity-70 group-hover:grayscale-0'">📚</div>
                        <div>
                            <p class="font-black text-xs text-slate-800">Kurikulum</p>
                            <p class="text-[9px] uppercase font-bold text-slate-500 mt-0.5">Struktur & Pengampu</p>
                        </div>
                        <div v-if="activeRole === 'kurikulum'" class="absolute right-3 text-blue-500 font-bold">✓</div>
                    </button>

                    <!-- Guru (Maintenance) -->
                    <button 
                        @click="activeRole = 'guru'"
                        class="shrink-0 flex items-center p-3 rounded-2xl border-2 transition-all duration-300 text-left relative group overflow-hidden"
                        :class="activeRole === 'guru' ? 'bg-slate-100 border-slate-300' : 'border-slate-100 hover:border-slate-200 hover:bg-slate-50 opacity-60'"
                    >
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xl mr-3 bg-slate-200 grayscale">👨‍🏫</div>
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="font-black text-xs text-slate-800">Guru</p>
                                <span class="text-[8px] bg-amber-100 text-amber-700 font-black px-1.5 py-0.5 rounded">MAINTENANCE</span>
                            </div>
                            <p class="text-[9px] uppercase font-bold text-slate-500 mt-0.5">Nilai & TP</p>
                        </div>
                        <div v-if="activeRole === 'guru'" class="absolute right-3 text-slate-500 font-bold">✓</div>
                    </button>

                    <!-- Wali Kelas (Maintenance) -->
                    <button 
                        @click="activeRole = 'walikelas'"
                        class="shrink-0 flex items-center p-3 rounded-2xl border-2 transition-all duration-300 text-left relative group overflow-hidden"
                        :class="activeRole === 'walikelas' ? 'bg-slate-100 border-slate-300' : 'border-slate-100 hover:border-slate-200 hover:bg-slate-50 opacity-60'"
                    >
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xl mr-3 bg-slate-200 grayscale">👨‍👩‍👧‍👦</div>
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="font-black text-xs text-slate-800">Wali Kelas</p>
                                <span class="text-[8px] bg-amber-100 text-amber-700 font-black px-1.5 py-0.5 rounded">MAINTENANCE</span>
                            </div>
                            <p class="text-[9px] uppercase font-bold text-slate-500 mt-0.5">Absensi & Ekstrakurikuler</p>
                        </div>
                        <div v-if="activeRole === 'walikelas'" class="absolute right-3 text-slate-500 font-bold">✓</div>
                    </button>

                    <!-- BK (Maintenance) -->
                    <button 
                        @click="activeRole = 'bk'"
                        class="shrink-0 flex items-center p-3 rounded-2xl border-2 transition-all duration-300 text-left relative group overflow-hidden"
                        :class="activeRole === 'bk' ? 'bg-slate-100 border-slate-300' : 'border-slate-100 hover:border-slate-200 hover:bg-slate-50 opacity-60'"
                    >
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xl mr-3 bg-slate-200 grayscale">🛡️</div>
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="font-black text-xs text-slate-800">Bimbingan Konseling</p>
                                <span class="text-[8px] bg-amber-100 text-amber-700 font-black px-1.5 py-0.5 rounded">MAINTENANCE</span>
                            </div>
                            <p class="text-[9px] uppercase font-bold text-slate-500 mt-0.5">Poin & Buku Kasus</p>
                        </div>
                        <div v-if="activeRole === 'bk'" class="absolute right-3 text-slate-500 font-bold">✓</div>
                    </button>

                    <!-- Siswa (Maintenance) -->
                    <button 
                        @click="activeRole = 'siswa'"
                        class="shrink-0 flex items-center p-3 rounded-2xl border-2 transition-all duration-300 text-left relative group overflow-hidden"
                        :class="activeRole === 'siswa' ? 'bg-slate-100 border-slate-300' : 'border-slate-100 hover:border-slate-200 hover:bg-slate-50 opacity-60'"
                    >
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xl mr-3 bg-slate-200 grayscale">🎓</div>
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="font-black text-xs text-slate-800">Siswa</p>
                                <span class="text-[8px] bg-amber-100 text-amber-700 font-black px-1.5 py-0.5 rounded">MAINTENANCE</span>
                            </div>
                            <p class="text-[9px] uppercase font-bold text-slate-500 mt-0.5">Rapor Akademik</p>
                        </div>
                        <div v-if="activeRole === 'siswa'" class="absolute right-3 text-slate-500 font-bold">✓</div>
                    </button>

                    <!-- Kepsek (Maintenance) -->
                    <button 
                        @click="activeRole = 'kepsek'"
                        class="shrink-0 flex items-center p-3 rounded-2xl border-2 transition-all duration-300 text-left relative group overflow-hidden"
                        :class="activeRole === 'kepsek' ? 'bg-slate-100 border-slate-300' : 'border-slate-100 hover:border-slate-200 hover:bg-slate-50 opacity-60'"
                    >
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xl mr-3 bg-slate-200 grayscale">🏢</div>
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="font-black text-xs text-slate-800">Kepala Sekolah</p>
                                <span class="text-[8px] bg-amber-100 text-amber-700 font-black px-1.5 py-0.5 rounded">MAINTENANCE</span>
                            </div>
                            <p class="text-[9px] uppercase font-bold text-slate-500 mt-0.5">Laporan & Evaluasi</p>
                        </div>
                        <div v-if="activeRole === 'kepsek'" class="absolute right-3 text-slate-500 font-bold">✓</div>
                    </button>
                </div>
                
                <hr class="border-slate-100 mb-6">
                
                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button 
                        @click="generateBackup('psas')" 
                        :disabled="isGenerating || isMaintenance"
                        class="w-full py-3 bg-slate-800 text-white font-bold rounded-2xl shadow-md hover:bg-slate-900 transition-all flex items-center justify-center gap-3 uppercase tracking-widest text-[10px] border border-slate-700 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span class="text-lg">📥</span> Backup PSAS (Juli - Des)
                    </button>

                    <button 
                        @click="generateBackup('psat')" 
                        :disabled="isGenerating || isMaintenance"
                        class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-2xl shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-3 uppercase tracking-widest text-[10px] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                    >
                        <span v-if="isGenerating" class="animate-spin text-lg">⏳</span>
                        <span v-else class="text-lg">📦</span> Backup PSAT (Jan - Jun)
                    </button>
                </div>

            </div>
        </div>
      </div>

      <!-- ==============================================
           KANAN: RIWAYAT BACKUP (Col 8)
           ============================================== -->
      <div class="lg:col-span-8">
         <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col min-h-[600px]">
            
            <div class="p-6 md:p-8 bg-slate-50/50 border-b border-slate-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white shadow-sm border border-slate-200 flex items-center justify-center text-2xl">🗄️</div>
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-widest text-slate-700">Riwayat Backup</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Daftar arsip yang tersedia untuk diunduh / restore</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button @click="triggerUpload" class="h-10 px-4 rounded-full bg-indigo-50 text-indigo-600 font-bold text-xs uppercase tracking-widest flex items-center gap-2 hover:bg-indigo-100 transition-colors border border-indigo-200">
                            <span>📤</span> Upload
                        </button>
                        <input type="file" ref="fileInput" class="hidden" accept=".json" @change="handleFileUpload">
                        
                        <button @click="fetchBackups" class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-slate-200 transition-colors" title="Refresh">
                            🔄
                        </button>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60">
                <div class="w-8 h-8 border-4 border-slate-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Riwayat...</span>
            </div>

            <!-- Empty State -->
            <div v-else-if="!backups || backups.length === 0" class="flex-grow flex items-center justify-center flex-col p-16 text-center">
                <div class="text-6xl opacity-20 mb-4">🏜️</div>
                <p class="text-sm font-bold text-slate-500">Belum ada file backup.</p>
                <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-2 font-bold">Gunakan panel di sebelah kiri untuk men-generate arsip.</p>
            </div>

            <!-- Table Content -->
            <div v-else class="flex-grow overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-slate-200">
                            <th class="p-5 pl-8 w-16 text-center">#</th>
                            <th class="p-5">Informasi File</th>
                            <th class="p-5">Waktu Arsip</th>
                            <th class="p-5 text-right pr-8">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr v-for="(backup, index) in backups" :key="backup.filename" class="border-b border-slate-100 hover:bg-slate-50/80 transition-colors group">
                            <td class="p-4 pl-8 text-center text-xs font-bold text-slate-300">
                                {{ index + 1 }}
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="text-3xl" :class="getRoleColorText(backup.role)">{{ getRoleIcon(backup.role) }}</div>
                                    <div>
                                        <p class="font-black text-slate-700 font-mono text-xs">{{ backup.filename }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">{{ backup.size }}</span>
                                            <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded border" :class="getRoleColorBadge(backup.role)">{{ backup.role }}</span>
                                            <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded border border-slate-200 bg-slate-800 text-white">{{ backup.mode }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <p class="text-xs font-bold text-slate-600">{{ formatDate(backup.timestamp) }}</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ formatTime(backup.timestamp) }}</p>
                            </td>
                            <td class="p-4 pr-8 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                    <button @click="confirmRestore(backup.filename)" class="w-9 h-9 rounded-lg bg-white border border-amber-200 text-amber-500 hover:text-white hover:bg-amber-500 flex items-center justify-center transition-all shadow-sm" title="Restore Data">🔄</button>
                                    <button @click="downloadBackup(backup.filename, 'json')" class="w-9 h-9 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-blue-500 hover:border-blue-200 hover:bg-blue-50 flex items-center justify-center transition-all shadow-sm" title="Download JSON">{ }</button>
                                    <button @click="downloadBackup(backup.filename, 'excel')" class="w-9 h-9 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-emerald-500 hover:border-emerald-200 hover:bg-emerald-50 flex items-center justify-center transition-all shadow-sm" title="Download Excel">📊</button>
                                    <button @click="confirmDelete(backup.filename)" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 flex items-center justify-center transition-all shadow-sm" title="Hapus">🗑️</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

         </div>
      </div>
    </div>

    <!-- ==============================================
         MODAL KONFIRMASI HAPUS & RESTORE
         ============================================== -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade text-center">
            <div class="p-8">
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner border-4 border-white ring-4 ring-rose-50">⚠️</div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Hapus Arsip?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Anda yakin ingin menghapus arsip:<br>
                    <span class="font-bold text-slate-800 font-mono text-[10px]">{{ deleteTarget }}</span>?
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

    <!-- Restore Modal -->
    <div v-if="isRestoreModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border-4 border-amber-500 overflow-hidden animate-slideUpFade text-center">
            <div class="p-8">
                <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner border-4 border-white ring-4 ring-amber-50 animate-pulse">☢️</div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">PERINGATAN KERAS!</h3>
                <p class="text-xs text-slate-600 mt-3 leading-relaxed bg-amber-50 p-4 rounded-xl border border-amber-200">
                    Aksi RESTORE akan <strong class="text-rose-600">MENGHAPUS & MENGGANTIKAN</strong> data sistem yang sedang berjalan saat ini dengan data dari:<br>
                    <span class="font-bold text-slate-800 font-mono text-[10px] mt-2 block">{{ restoreTarget }}</span>
                </p>
                <p class="text-[10px] font-bold text-rose-500 uppercase tracking-widest mt-4">Pastikan Anda sudah mem-backup data terbaru!</p>
                
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isRestoreModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">Batal</button>
                    <button @click="executeRestore" :disabled="isRestoring" class="flex-1 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-2xl shadow-lg shadow-amber-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2 disabled:opacity-50">
                        <span v-if="isRestoring" class="animate-spin text-sm">⏳</span>
                        <span v-else>Restore Data</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div v-if="showToast" class="fixed bottom-10 left-1/2 -translate-x-1/2 bg-slate-900/95 backdrop-blur-md text-white px-6 py-4 rounded-full shadow-2xl flex items-center gap-4 z-[100] animate-slideUp">
      <div class="w-8 h-8 bg-gradient-to-br from-emerald-400 to-emerald-500 rounded-full flex items-center justify-center text-white text-sm shadow-inner shadow-white/20">✓</div>
      <p class="font-bold text-sm tracking-wide pr-2">{{ toastMessage }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

definePageMeta({
  layout: 'admin',
  middleware: 'admin',
  title: 'Backup & Arsip'
})

const activeRole = ref('admin')
const backups = ref([])
const isLoading = ref(true)
const isGenerating = ref(false)

const maintenanceRoles = ['guru', 'walikelas', 'bk', 'siswa', 'kepsek']
import { computed } from 'vue'
const isMaintenance = computed(() => maintenanceRoles.includes(activeRole.value))

const isDeleteModalOpen = ref(false)
const deleteTarget = ref('')

const isRestoreModalOpen = ref(false)
const restoreTarget = ref('')
const isRestoring = ref(false)

const fileInput = ref(null)

const showToast = ref(false)
const toastMessage = ref('')

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
            displayToast(response.message)
            fetchBackups()
        }
    } catch (error) {
        console.error('Backup generation failed:', error)
        alert('Gagal men-generate backup.')
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
        alert('Gagal mengunduh file backup.')
    })
}

const triggerUpload = () => {
    fileInput.value.click()
}

const handleFileUpload = async (event) => {
    const file = event.target.files[0]
    if (!file) return
    
    if (file.type !== 'application/json' && !file.name.endsWith('.json')) {
        alert('Hanya file JSON yang diizinkan')
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
            displayToast(response.message)
            fetchBackups()
        }
    } catch (error) {
        console.error('Upload failed:', error)
        alert('Gagal mengunggah file backup.')
    } finally {
        event.target.value = '' // reset input
    }
}

const confirmRestore = (filename) => {
    restoreTarget.value = filename
    isRestoreModalOpen.value = true
}

const executeRestore = async () => {
    if(!restoreTarget.value) return
    
    isRestoring.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/admin/backup/restore/${restoreTarget.value}`, {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            isRestoreModalOpen.value = false
            displayToast('Berhasil me-restore data sistem!')
            setTimeout(() => {
                window.location.reload()
            }, 1500)
        }
    } catch (error) {
        console.error('Restore failed:', error)
        alert('Gagal me-restore data: ' + (error.data?.message || 'Terjadi kesalahan sistem.'))
    } finally {
        isRestoring.value = false
    }
}

const confirmDelete = (filename) => {
    deleteTarget.value = filename
    isDeleteModalOpen.value = true
}

const executeDelete = async () => {
    if(!deleteTarget.value) return
    
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/admin/backup/${deleteTarget.value}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            isDeleteModalOpen.value = false
            displayToast(response.message)
            fetchBackups()
        }
    } catch (error) {
        console.error('Delete failed:', error)
        alert('Gagal menghapus arsip backup.')
    }
}

const displayToast = (msg) => {
    toastMessage.value = msg
    showToast.value = true
    setTimeout(() => { showToast.value = false }, 3500)
}

onMounted(() => {
    fetchBackups()
})
</script>

<style scoped>
/* Scrollbar styling for tabs */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 20px;
}
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }

@keyframes slideUpFade {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-slideUpFade { animation: slideUpFade 0.3s ease-out forwards; }

@keyframes slideUp {
  from { opacity: 0; transform: translate(-50%, 20px); }
  to { opacity: 1; transform: translate(-50%, 0); }
}
.animate-slideUp { animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
