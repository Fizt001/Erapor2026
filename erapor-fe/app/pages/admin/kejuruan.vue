<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- MOBILE VIEW TABS -->
    <div class="lg:hidden mb-8 mt-2">
      <div class="grid grid-cols-4 gap-2">
        <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTab = tab.id"
          :class="activeTab === tab.id ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 ring-2 ring-emerald-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="rounded-2xl flex flex-col items-center justify-center py-4 px-1 transition-all active:scale-95">
          <span class="text-2xl mb-1.5 transition-transform" :class="activeTab === tab.id ? 'scale-110' : ''">{{ tab.icon }}</span>
          <span class="text-[9px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
        </button>
      </div>
    </div>

    <!-- MAIN GRID LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      
      <!-- ==============================================
           KIRI: PANEL KENDALI (Forms) - lg:col-span-4
           ============================================== -->
      <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-6" v-show="isDesktop || (activeTab === 'bidang' || activeTab === 'program' || activeTab === 'konsentrasi')">
        
        <!-- Desktop Tabs (Toggle Form) -->
        <div class="hidden lg:flex bg-white rounded-2xl shadow-sm border border-slate-200/60 p-1.5">
            <button @click="activeTab = 'bidang'" :class="activeTab === 'bidang' ? 'bg-emerald-50 text-emerald-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50'" class="flex-1 py-2.5 rounded-2xl font-bold text-xs uppercase tracking-widest transition-all">
                🛠️ Bidang
            </button>
            <button @click="activeTab = 'program'" :class="activeTab === 'program' ? 'bg-emerald-50 text-emerald-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50'" class="flex-1 py-2.5 rounded-2xl font-bold text-xs uppercase tracking-widest transition-all">
                🎓 Program
            </button>
            <button @click="activeTab = 'konsentrasi'" :class="activeTab === 'konsentrasi' ? 'bg-emerald-50 text-emerald-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50'" class="flex-1 py-2.5 rounded-2xl font-bold text-xs uppercase tracking-widest transition-all">
                🎯 Fokus
            </button>
        </div>

        <!-- Panel Form Bidang -->
        <div v-show="activeTab === 'bidang'" class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden animate-slideUpFade">
            <div class="p-6 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-100 flex items-center gap-4">
                <span class="text-3xl">🛠️</span>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-white">Bidang Keahlian</h3>
                    <p class="text-[10px] text-emerald-400 font-semibold uppercase mt-0.5">Tingkatan 1 (Tertinggi)</p>
                </div>
            </div>
            <div class="p-6">
                <form @submit.prevent="saveBidang" class="space-y-5">
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Nama Bidang</label>
                        <input type="text" v-model="bidangForm.nama_bidang" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800" placeholder="Misal: Teknologi Informasi">
                    </div>
                    <button type="submit" :disabled="isSaving" class="w-full py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-2xl shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 uppercase tracking-widest text-xs">
                        <span v-if="isSaving" class="animate-spin text-lg">⏳</span>
                        <span v-else class="text-lg">💾</span> Simpan Bidang
                    </button>
                </form>
            </div>
        </div>

        <!-- Panel Form Program -->
        <div v-show="activeTab === 'program'" class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden animate-slideUpFade">
            <div class="p-6 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-100 flex items-center gap-4">
                <span class="text-3xl">🎓</span>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-white">Program Keahlian</h3>
                    <p class="text-[10px] text-emerald-400 font-semibold uppercase mt-0.5">Tingkatan 2 (Menengah)</p>
                </div>
            </div>
            <div class="p-6">
                <form @submit.prevent="saveProgram" class="space-y-5">
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pilih Induk (Bidang)</label>
                        <select v-model="programForm.bidang_id" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-bold text-slate-700 cursor-pointer">
                            <option value="" disabled>-- Pilih Bidang Keahlian --</option>
                            <option v-for="b in treeData" :key="b.id" :value="b.id">{{ b.nama_bidang }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Nama Program</label>
                        <input type="text" v-model="programForm.nama_program" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800" placeholder="Misal: Teknik Komputer dan Informatika">
                    </div>
                    <button type="submit" :disabled="isSaving" class="w-full py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-2xl shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 uppercase tracking-widest text-xs">
                        <span v-if="isSaving" class="animate-spin text-lg">⏳</span>
                        <span v-else class="text-lg">💾</span> Simpan Program
                    </button>
                </form>
            </div>
        </div>

        <!-- Panel Form Konsentrasi -->
        <div v-show="activeTab === 'konsentrasi'" class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden animate-slideUpFade">
            <div class="p-6 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-100 flex items-center gap-4">
                <span class="text-3xl">🎯</span>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-white">Konsentrasi Keahlian</h3>
                    <p class="text-[10px] text-emerald-400 font-semibold uppercase mt-0.5">Tingkatan 3 (Kejuruan/Jurusan)</p>
                </div>
            </div>
            <div class="p-6">
                <form @submit.prevent="saveKejuruan" class="space-y-5">
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pilih Induk (Program)</label>
                        <select v-model="kejuruanForm.program_id" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-bold text-slate-700 cursor-pointer">
                            <option value="" disabled>-- Pilih Program Keahlian --</option>
                            <option v-for="p in flatProgramList" :key="p.id" :value="p.id">{{ p.nama_program }}</option>
                        </select>
                    </div>
                    <div class="space-y-5">
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kode Konsentrasi</label>
                            <input type="text" v-model="kejuruanForm.kode_konsentrasi" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-black uppercase text-slate-800" placeholder="Contoh: TKJ">
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Nama Lengkap Konsentrasi</label>
                            <input type="text" v-model="kejuruanForm.nama_konsentrasi" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800" placeholder="Misal: Teknik Komputer dan Jaringan">
                        </div>
                    </div>
                    <button type="submit" :disabled="isSaving" class="w-full py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-2xl shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 uppercase tracking-widest text-xs">
                        <span v-if="isSaving" class="animate-spin text-lg">⏳</span>
                        <span v-else class="text-lg">💾</span> Simpan Konsentrasi
                    </button>
                </form>
            </div>
        </div>

      </div>

      <!-- ==============================================
           KANAN: DATABASE TREE VIEW - lg:col-span-8
           ============================================== -->
      <div class="lg:col-span-8" v-show="isDesktop || activeTab === 'table'">
         <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col min-h-[500px]">
            
            <div class="p-6 md:p-8 bg-slate-50/50 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white shadow-sm border border-slate-200 flex items-center justify-center text-2xl hidden sm:flex">📋</div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-emerald-700">Database Kejuruan</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Struktur Kurikulum Hierarkis</p>
                    </div>
                </div>
                <div class="flex gap-2">
                     <button @click="expandAll" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-100 transition-all">Buka Semua</button>
                     <button @click="collapseAll" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-100 transition-all">Tutup Semua</button>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60">
                <div class="w-8 h-8 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Struktur...</span>
            </div>

            <!-- Tree View Data -->
            <div v-else class="flex-grow p-4 md:p-6 bg-slate-50">
                
                <div v-if="treeData.length === 0" class="text-center py-10">
                    <div class="text-5xl opacity-50 mb-4">🌵</div>
                    <p class="text-sm font-bold text-slate-500">Belum ada struktur kejuruan.<br>Mulai dengan menambahkan Bidang Keahlian.</p>
                </div>

                <!-- LOOP 1: BIDANG -->
                <div v-for="b in treeData" :key="'b-'+b.id" class="mb-4 bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden animate-slideUpFade">
                    <!-- Bidang Header -->
                    <div class="flex items-center justify-between p-4 cursor-pointer hover:bg-slate-50 transition-colors" @click="toggleNode('b-'+b.id)">
                        <div class="flex items-center gap-4 flex-1">
                            <span class="w-6 h-6 flex items-center justify-center rounded-md bg-slate-100 text-slate-500 transition-transform" :class="isNodeExpanded('b-'+b.id) ? 'rotate-90' : ''">▶</span>
                            <div>
                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 bg-slate-100 px-2 py-0.5 rounded mr-2">BIDANG</span>
                                <span class="text-sm font-bold text-slate-800">{{ b.nama_bidang }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                            <button @click.stop="openEdit('bidang', b)" class="w-8 h-8 flex items-center justify-center rounded-xl text-emerald-600 hover:bg-emerald-50" title="Edit Bidang">✏️</button>
                            <button @click.stop="confirmDelete('bidang', b.id, b.nama_bidang)" class="w-8 h-8 flex items-center justify-center rounded-xl text-rose-500 hover:bg-rose-50" title="Hapus Bidang">🗑️</button>
                        </div>
                    </div>

                    <!-- Programs Container (Collapsible) -->
                    <div v-show="isNodeExpanded('b-'+b.id)" class="border-t border-slate-100 bg-slate-50/30 p-4 pl-12 space-y-3 relative">
                        <!-- Line Guide -->
                        <div class="absolute left-7 top-0 bottom-6 w-px bg-slate-200"></div>
                        
                        <div v-if="!b.programs || b.programs.length === 0" class="text-[10px] font-bold text-slate-400 italic">Belum ada program keahlian.</div>

                        <!-- LOOP 2: PROGRAM -->
                        <div v-for="p in b.programs" :key="'p-'+p.id" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden relative group">
                            <!-- Program Header -->
                            <div class="flex items-center justify-between p-3 cursor-pointer hover:bg-slate-50 transition-colors" @click="toggleNode('p-'+p.id)">
                                <div class="flex items-center gap-3 flex-1">
                                    <span class="w-5 h-5 flex items-center justify-center rounded bg-slate-100 text-slate-400 transition-transform text-[10px]" :class="isNodeExpanded('p-'+p.id) ? 'rotate-90' : ''">▶</span>
                                    <div>
                                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 bg-slate-100 px-2 py-0.5 rounded mr-2">PROGRAM</span>
                                        <span class="text-xs font-bold text-slate-700">{{ p.nama_program }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                    <button @click.stop="openEdit('program', p)" class="w-7 h-7 flex items-center justify-center rounded-xl text-emerald-600 hover:bg-emerald-50 text-xs" title="Edit Program">✏️</button>
                                    <button @click.stop="confirmDelete('program', p.id, p.nama_program)" class="w-7 h-7 flex items-center justify-center rounded-xl text-rose-500 hover:bg-rose-50 text-xs" title="Hapus Program">🗑️</button>
                                </div>
                            </div>

                            <!-- Kejuruans Container (Collapsible) -->
                            <div v-show="isNodeExpanded('p-'+p.id)" class="border-t border-slate-100 bg-slate-50 p-3 pl-10 space-y-2 relative">
                                <div class="absolute left-5 top-0 bottom-4 w-px bg-slate-200"></div>
                                
                                <div v-if="!p.kejuruans || p.kejuruans.length === 0" class="text-[10px] font-bold text-slate-400 italic">Belum ada konsentrasi.</div>

                                <!-- LOOP 3: KONSENTRASI / KEJURUAN -->
                                <div v-for="k in p.kejuruans" :key="'k-'+k.id" class="flex items-center justify-between p-2.5 bg-white rounded-lg border border-slate-200 hover:shadow-sm transition-shadow group/k">
                                    <div class="flex items-center gap-3">
                                        <span class="text-lg">🎯</span>
                                        <div>
                                            <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded uppercase tracking-widest mr-2 border border-emerald-100">{{ k.kode_konsentrasi }}</span>
                                            <span class="text-xs font-bold text-slate-700">{{ k.nama_konsentrasi }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1 opacity-100 sm:opacity-0 sm:group-hover/k:opacity-100 transition-opacity">
                                        <button @click="openEdit('konsentrasi', k)" class="w-7 h-7 flex items-center justify-center rounded bg-slate-50 text-emerald-600 hover:bg-emerald-100 text-xs">✏️</button>
                                        <button @click="confirmDelete('konsentrasi', k.id, k.nama_konsentrasi)" class="w-7 h-7 flex items-center justify-center rounded bg-slate-50 text-rose-500 hover:bg-rose-100 text-xs">🗑️</button>
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

    <!-- ==============================================
         MODAL KONFIRMASI HAPUS
         ============================================== -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade text-center">
            <div class="p-8">
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner border-4 border-white ring-4 ring-rose-50">⚠️</div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Hapus Data?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Anda yakin ingin menghapus <span class="uppercase font-black text-rose-600">{{ deleteTarget.type }}</span>:<br>
                    <span class="font-bold text-slate-800">{{ deleteTarget.name }}</span>?
                </p>
                <p v-if="deleteTarget.type === 'bidang'" class="text-[10px] font-bold text-rose-500 mt-2 p-2 bg-rose-50 rounded-lg">AWAS: Menghapus Bidang akan menghapus SEMUA Program dan Konsentrasi di bawahnya!</p>
                <p v-if="deleteTarget.type === 'program'" class="text-[10px] font-bold text-rose-500 mt-2 p-2 bg-rose-50 rounded-lg">AWAS: Menghapus Program akan menghapus SEMUA Konsentrasi di bawahnya!</p>
                
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isDeleteModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">Batal</button>
                    <button @click="executeDelete" :disabled="isSaving" class="flex-1 py-3 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        <span v-if="isSaving" class="animate-spin text-base">⏳</span>
                        <span v-else>Hapus</span>
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
import { ref, computed, onMounted } from 'vue'

definePageMeta({
  layout: 'admin',
  middleware: 'admin',
  title: 'Master Kejuruan'
})

const windowWidth = ref(1024) 
const isDesktop = computed(() => windowWidth.value >= 1024)

// Tabs for Mobile
const activeTab = ref('table')
const mobileTabs = [
  { id: 'bidang', title: 'Bidang', icon: '🛠️' },
  { id: 'program', title: 'Program', icon: '🎓' },
  { id: 'konsentrasi', title: 'Fokus', icon: '🎯' },
  { id: 'table', title: 'Database', icon: '📋' }
]

// Tree Data State
const treeData = ref([])
const isLoading = ref(true)
const expandedNodes = ref(new Set())

// Forms State
const bidangForm = ref({ id: null, nama_bidang: '' })
const programForm = ref({ id: null, bidang_id: '', nama_program: '' })
const kejuruanForm = ref({ id: null, program_id: '', kode_konsentrasi: '', nama_konsentrasi: '' })
const isSaving = ref(false)

// Delete State
const isDeleteModalOpen = ref(false)
const deleteTarget = ref({ type: '', id: null, name: '' })

// Toast State
const showToast = ref(false)
const toastMessage = ref('')

// Computed Helpers for Select Dropdowns
const flatProgramList = computed(() => {
    let programs = []
    treeData.value.forEach(b => {
        if (b.programs) {
            b.programs.forEach(p => {
                programs.push({
                    id: p.id,
                    nama_program: `${b.nama_bidang} - ${p.nama_program}`
                })
            })
        }
    })
    return programs
})

// === TREE ACCORDION LOGIC ===
const toggleNode = (nodeId) => {
    const newSet = new Set(expandedNodes.value)
    if (newSet.has(nodeId)) {
        newSet.delete(nodeId)
    } else {
        newSet.add(nodeId)
    }
    expandedNodes.value = newSet
}
const isNodeExpanded = (nodeId) => expandedNodes.value.has(nodeId)
const expandAll = () => {
    const newSet = new Set()
    treeData.value.forEach(b => {
        newSet.add('b-'+b.id)
        if(b.programs) b.programs.forEach(p => newSet.add('p-'+p.id))
    })
    expandedNodes.value = newSet
}
const collapseAll = () => {
    expandedNodes.value = new Set()
}

// === API CALLS ===
const fetchTreeData = async () => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch('http://localhost:8000/api/admin/kejuruan/data', {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            treeData.value = response.data
            // Auto expand if only few items
            if (treeData.value.length === 1) expandAll()
        }
    } catch (error) {
        console.error('Failed to fetch tree:', error)
    } finally {
        isLoading.value = false
    }
}

const saveBidang = async () => {
    await submitForm('bidang', bidangForm.value)
    bidangForm.value = { id: null, nama_bidang: '' }
}

const saveProgram = async () => {
    await submitForm('program', programForm.value)
    programForm.value = { id: null, bidang_id: '', nama_program: '' }
}

const saveKejuruan = async () => {
    await submitForm('konsentrasi', kejuruanForm.value)
    kejuruanForm.value = { id: null, program_id: '', kode_konsentrasi: '', nama_konsentrasi: '' }
}

const submitForm = async (type, payload) => {
    isSaving.value = true
    const token = useCookie('auth_token').value
    const isUpdate = payload.id !== null
    const method = isUpdate ? 'PUT' : 'POST'
    const url = isUpdate 
        ? `http://localhost:8000/api/admin/kejuruan/${type}/${payload.id}`
        : `http://localhost:8000/api/admin/kejuruan/${type}`
    
    try {
        const response = await $fetch(url, {
            method: method,
            headers: { Authorization: `Bearer ${token}` },
            body: payload
        })
        if (response.success) {
            displayToast(response.message)
            fetchTreeData()
            if (!isDesktop.value) activeTab.value = 'table'
        }
    } catch (error) {
        console.error(`Save ${type} failed:`, error)
        alert(`Gagal menyimpan data ${type}.`)
    } finally {
        isSaving.value = false
    }
}

const openEdit = (type, item) => {
    if (type === 'bidang') {
        bidangForm.value = { id: item.id, nama_bidang: item.nama_bidang }
        activeTab.value = 'bidang'
    } else if (type === 'program') {
        programForm.value = { id: item.id, bidang_id: item.bidang_id, nama_program: item.nama_program }
        activeTab.value = 'program'
    } else if (type === 'konsentrasi') {
        kejuruanForm.value = { id: item.id, program_id: item.program_id, kode_konsentrasi: item.kode_konsentrasi, nama_konsentrasi: item.nama_konsentrasi }
        activeTab.value = 'konsentrasi'
    }
    window.scrollTo({ top: 0, behavior: 'smooth' })
}

const confirmDelete = (type, id, name) => {
    deleteTarget.value = { type, id, name }
    isDeleteModalOpen.value = true
}

const executeDelete = async () => {
    if (!deleteTarget.value.id) return
    isSaving.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/admin/kejuruan/${deleteTarget.value.type}/${deleteTarget.value.id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            isDeleteModalOpen.value = false
            displayToast(response.message)
            fetchTreeData()
        }
    } catch (error) {
        console.error('Delete failed:', error)
        alert('Gagal menghapus data.')
    } finally {
        isSaving.value = false
    }
}

const displayToast = (msg) => {
    toastMessage.value = msg
    showToast.value = true
    setTimeout(() => { showToast.value = false }, 3500)
}

onMounted(() => {
    windowWidth.value = window.innerWidth
    window.addEventListener('resize', () => { windowWidth.value = window.innerWidth })
    
    if (isDesktop.value) {
        activeTab.value = 'bidang' 
    } else {
        activeTab.value = 'table' 
    }

    fetchTreeData()
})
</script>

<style scoped>
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

.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
</style>
