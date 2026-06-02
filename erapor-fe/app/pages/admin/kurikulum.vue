<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- MOBILE VIEW TABS -->
    <div class="xl:hidden mb-8 mt-2">
      <div class="grid grid-cols-2 gap-3">
        <button type="button" @click="activeTab = 'form'" :class="activeTab === 'form' ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 ring-2 ring-emerald-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'" class="rounded-2xl flex flex-col items-center justify-center py-5 transition-all active:scale-95">
          <span class="text-3xl mb-2 transition-transform" :class="activeTab === 'form' ? 'scale-110' : ''">📚</span>
          <span class="text-[10px] font-black uppercase tracking-wider">Entry Kurikulum</span>
        </button>
        <button type="button" @click="activeTab = 'table'" :class="activeTab === 'table' ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 ring-2 ring-emerald-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'" class="rounded-2xl flex flex-col items-center justify-center py-5 transition-all active:scale-95">
          <span class="text-3xl mb-2 transition-transform" :class="activeTab === 'table' ? 'scale-110' : ''">📋</span>
          <span class="text-[10px] font-black uppercase tracking-wider">Database</span>
        </button>
      </div>
    </div>

    <!-- MAIN GRID LAYOUT -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-8 items-start">
      
      <!-- ==============================================
           KIRI: PANEL ENTRY (Form) - xl:col-span-1
           ============================================== -->
      <div class="xl:col-span-1 space-y-6 xl:sticky xl:top-6" v-show="isDesktop || activeTab === 'form'">
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden animate-slideUpFade">
            <div class="p-6 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-100 flex items-center gap-4">
                <span class="text-3xl">📚</span>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-white">{{ isEditing ? 'Update Kurikulum' : 'Entry Kurikulum' }}</h3>
                    <p class="text-[10px] text-emerald-400 font-semibold uppercase mt-0.5">Master Data</p>
                </div>
            </div>
            
            <div class="p-6 md:p-8">
                <form @submit.prevent="saveKurikulum" class="space-y-5">
                    
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Nama Kurikulum</label>
                        <input type="text" v-model="form.nama_kurikulum" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-bold text-slate-800" placeholder="Misal: Kurikulum Merdeka">
                    </div>

                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Singkatan</label>
                        <input type="text" v-model="form.singkatan" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-bold text-slate-800" placeholder="Misal: K-Merdeka">
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex gap-3">
                         <button v-if="isEditing" type="button" @click="resetForm" class="px-4 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSaving" class="flex-1 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-2xl shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 uppercase tracking-widest text-xs">
                            <span v-if="isSaving" class="animate-spin text-lg">⏳</span>
                            <span v-else class="text-lg">💾</span> {{ isEditing ? 'Update' : 'Simpan' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>

      </div>

      <!-- ==============================================
           KANAN: DATABASE TABLE - xl:col-span-3
           ============================================== -->
      <div class="xl:col-span-3" v-show="isDesktop || activeTab === 'table'">
         <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col min-h-[500px]">
            
            <!-- Table Header & Filters -->
            <div class="p-6 md:p-8 bg-slate-50/50 border-b border-slate-100">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-white shadow-sm border border-slate-200 flex items-center justify-center text-2xl hidden sm:flex">📋</div>
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-widest text-emerald-700">Database Kurikulum</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Daftar Kurikulum Pembelajaran</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60">
                <div class="w-8 h-8 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</span>
            </div>

            <!-- Table Content -->
            <div v-else class="flex-grow flex flex-col">
                <div v-if="!kurikulumData || kurikulumData.length === 0" class="text-center py-16 m-auto">
                    <div class="text-6xl opacity-30 mb-4">🌵</div>
                    <p class="text-sm font-bold text-slate-500">Data kurikulum masih kosong.</p>
                </div>

                <!-- Desktop Table -->
                <div v-else class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-slate-200">
                                <th class="p-5 pl-8 w-16 text-center">#</th>
                                <th class="p-5">Nama Kurikulum</th>
                                <th class="p-5 text-right pr-8">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <tr v-for="(k, index) in kurikulumData" :key="k.id" class="border-b border-slate-100 hover:bg-slate-50/80 transition-colors group">
                                <td class="p-4 pl-8 text-center text-xs font-bold text-slate-300">
                                    {{ index + 1 }}
                                </td>
                                <td class="p-4">
                                    <p class="font-black text-slate-700">{{ k.nama_kurikulum }}</p>
                                    <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mt-0.5">{{ k.singkatan }}</p>
                                </td>
                                <td class="p-4 pr-8 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                        <button @click="editKurikulum(k)" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-emerald-500 hover:border-emerald-200 hover:bg-emerald-50 flex items-center justify-center transition-all shadow-sm" title="Edit">✏️</button>
                                        <button @click="confirmDelete(k.id, k.nama_kurikulum)" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 flex items-center justify-center transition-all shadow-sm" title="Hapus">🗑️</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div v-if="kurikulumData && kurikulumData.length > 0" class="md:hidden p-4 space-y-4 bg-slate-50">
                    <div v-for="(k, index) in kurikulumData" :key="'mob-'+k.id" class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 relative overflow-hidden">
                        <div class="absolute right-4 top-4 flex items-center gap-2 z-10">
                            <button @click="editKurikulum(k)" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-emerald-500 hover:bg-emerald-50 flex items-center justify-center shadow-sm" title="Edit">✏️</button>
                            <button @click="confirmDelete(k.id, k.nama_kurikulum)" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:bg-rose-50 flex items-center justify-center shadow-sm" title="Hapus">🗑️</button>
                        </div>
                        <div class="mb-4 pr-16">
                            <h4 class="font-black text-slate-800 text-lg leading-tight">{{ k.nama_kurikulum }}</h4>
                            <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mt-1">{{ k.singkatan }}</p>
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
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Hapus Kurikulum?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Anda yakin ingin menghapus:<br>
                    <span class="font-bold text-slate-800">{{ deleteTarget.name }}</span>?
                </p>
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
  title: 'Master Kurikulum'
})

const windowWidth = ref(1024) 
const isDesktop = computed(() => windowWidth.value >= 1280) // xl breakpoint

// Mobile Tab State
const activeTab = ref('table')

// Main State
const kurikulumData = ref([])
const isLoading = ref(true)
const isSaving = ref(false)

// Form State
const isEditing = ref(false)
const form = ref({
    id: null,
    nama_kurikulum: '',
    singkatan: ''
})

// Delete State
const isDeleteModalOpen = ref(false)
const deleteTarget = ref({ id: null, name: '' })

// Toast State
const showToast = ref(false)
const toastMessage = ref('')

// === API CALLS ===

const fetchKurikulum = async () => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/admin/kurikulum`, {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            kurikulumData.value = response.data
        }
    } catch (error) {
        console.error('Failed to fetch kurikulum:', error)
    } finally {
        isLoading.value = false
    }
}

const saveKurikulum = async () => {
    isSaving.value = true
    const token = useCookie('auth_token').value
    const method = isEditing.value ? 'PUT' : 'POST'
    const url = isEditing.value 
        ? `http://localhost:8000/api/admin/kurikulum/${form.value.id}`
        : `http://localhost:8000/api/admin/kurikulum`
    
    try {
        const response = await $fetch(url, {
            method: method,
            headers: { Authorization: `Bearer ${token}` },
            body: form.value
        })
        if (response.success) {
            displayToast(response.message)
            resetForm()
            fetchKurikulum()
            if (!isDesktop.value) activeTab.value = 'table'
        }
    } catch (error) {
        console.error('Save failed:', error)
        alert('Gagal menyimpan kurikulum. Periksa kembali form Anda.')
    } finally {
        isSaving.value = false
    }
}

const editKurikulum = (k) => {
    isEditing.value = true
    form.value = {
        id: k.id,
        nama_kurikulum: k.nama_kurikulum,
        singkatan: k.singkatan
    }
    activeTab.value = 'form'
    window.scrollTo({ top: 0, behavior: 'smooth' })
}

const resetForm = () => {
    isEditing.value = false
    form.value = {
        id: null,
        nama_kurikulum: '',
        singkatan: ''
    }
}

const confirmDelete = (id, name) => {
    deleteTarget.value = { id, name }
    isDeleteModalOpen.value = true
}

const executeDelete = async () => {
    if (!deleteTarget.value.id) return
    isSaving.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/admin/kurikulum/${deleteTarget.value.id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            isDeleteModalOpen.value = false
            displayToast(response.message)
            fetchKurikulum()
        }
    } catch (error) {
        console.error('Delete failed:', error)
        alert('Gagal menghapus data kurikulum.')
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
        activeTab.value = 'form' 
    } else {
        activeTab.value = 'table' 
    }

    fetchKurikulum()
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

@keyframes slideUp {
  from { opacity: 0; transform: translate(-50%, 20px); }
  to { opacity: 1; transform: translate(-50%, 0); }
}
.animate-slideUp { animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
