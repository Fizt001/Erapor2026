<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    
<!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-2 grid grid-cols-2 gap-2 z-20">
        <button type="button" @click="activeTab = 'form'" :class="activeTab === 'form' ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 ring-2 ring-emerald-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'" class="rounded-xl flex items-center justify-center py-2 px-1 transition-all active:scale-95">
          <span class="text-lg mr-1.5 transition-transform" :class="activeTab === 'form' ? 'scale-110' : ''"><AppIcon name="book" /></span>
          <span class="text-[10px] font-black uppercase tracking-wider">Entry Kurikulum</span>
        </button>
        <button type="button" @click="activeTab = 'table'" :class="activeTab === 'table' ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 ring-2 ring-emerald-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'" class="rounded-xl flex items-center justify-center py-2 px-1 transition-all active:scale-95">
          <span class="text-lg mr-1.5 transition-transform" :class="activeTab === 'table' ? 'scale-110' : ''"><AppIcon name="clipboard" /></span>
          <span class="text-[10px] font-black uppercase tracking-wider">Database</span>
        </button>
      </div>

      <!-- Panel Dock Kiri -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all', activeTab === 'form' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[60px]' : '']">
        
        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <div class="animate-fadeIn">
                <div class="p-6 shrink-0">
                  <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl p-5 border border-emerald-500 shadow-sm relative overflow-hidden flex items-center gap-4">
                    <div class="w-12 h-12 flex items-center justify-center text-3xl shrink-0 relative z-10"><AppIcon name="book" /></div>
                    <div class="relative z-10">
                        <h3 class="text-sm font-black uppercase tracking-widest text-white">Data Kurikulum</h3>
                        <p class="text-[10px] text-emerald-100 font-semibold uppercase mt-0.5">{{ isEditing ? 'Mode Update' : 'Mode Tambah Baru' }}</p>
                    </div>
                    <div class="absolute right-0 bottom-0 opacity-20 text-white">
                      <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z"></path></svg>
                    </div>
                  </div>
                </div>
                
                <div class="px-6 pb-6">
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
                            <button v-if="isEditing" type="button" @click="resetForm" class="flex-1 py-3.5 bg-slate-100 text-slate-600 font-bold rounded-2xl hover:bg-slate-200 transition-all uppercase tracking-widest text-xs">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSaving" class="flex-1 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-2xl shadow-lg shadow-emerald-500/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 uppercase tracking-widest text-xs">
                                <span v-if="isSaving" class="animate-spin text-lg"><AppIcon name="clock" /></span>
                                <span v-else class="text-lg"><AppIcon name="save" /></span> {{ isEditing ? 'Simpan' : 'Tambah Kurikulum' }}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative', activeTab === 'table' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[60px]' : '']">
        <div class="p-2 sm:pt-3 sm:pb-6 sm:px-6 lg:pt-3 lg:pb-8 lg:px-8 max-w-5xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-2xl sm:rounded-[2rem] shadow-[0_2px_15px_-3px_rgba(0,0,0,0.05)] overflow-hidden flex flex-col flex-1 relative min-h-0">
            <!-- Table Header & Filters -->
            <div class="px-6 py-5 border-b border-slate-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shrink-0 z-10 bg-white">
            <div class="flex items-center gap-4 w-full sm:w-auto">
                <div class="w-12 h-12 rounded-2xl bg-slate-50 shadow-sm border border-slate-200 flex items-center justify-center text-2xl hidden sm:flex"><AppIcon name="clipboard" /></div>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-emerald-700">Database Kurikulum</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Daftar Kurikulum Pembelajaran</p>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60">
            <div class="w-8 h-8 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</span>
        </div>

        <!-- Table Content -->
        <div v-else class="flex-1 overflow-y-auto overflow-x-auto custom-scrollbar relative bg-white">
            <div v-if="!kurikulumData || kurikulumData.length === 0" class="text-center py-16 flex flex-col items-center justify-center h-full min-h-[400px]">
                <div class="text-6xl opacity-30 mb-4 block"><AppIcon name="empty-state" /></div>
                <h3 class="text-lg font-black text-slate-700 mb-2 uppercase tracking-widest">Data Kosong</h3>
                <p class="text-slate-500 text-sm font-semibold max-w-sm">Data kurikulum belum tersedia.</p>
            </div>

            <!-- Desktop Table -->
            <table v-else class="w-full text-left border-collapse min-w-full">
                <thead class="hidden sm:table-header-group sticky top-0 z-10 bg-slate-50 border-b border-slate-200 shadow-sm">
                    <tr class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-500">
                        <th class="p-4 w-16 text-center">#</th>
                        <th class="p-4">Nama Kurikulum</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-xs flex flex-col sm:table-row-group">
                    <tr v-for="(k, index) in kurikulumData" :key="k.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors group bg-white flex flex-col sm:table-row p-4 sm:p-0 relative">
                        <td class="px-0 py-1 sm:p-4 text-left sm:text-center text-[10px] font-bold text-slate-400 flex sm:table-cell items-center justify-between">
                            <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-slate-400">Nomor</span>
                            <span>{{ index + 1 }}</span>
                        </td>
                        <td class="px-0 py-1 sm:p-4 flex sm:table-cell items-center justify-between border-b sm:border-0 border-slate-50/50 pb-3 sm:pb-4 mb-2 sm:mb-0">
                            <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-slate-400">Nama Kurikulum</span>
                            <div class="text-right sm:text-left">
                                <p class="font-black text-slate-800 text-[13px]">{{ k.nama_kurikulum }}</p>
                                <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mt-0.5">{{ k.singkatan }}</p>
                            </div>
                        </td>
                        <td class="px-0 pt-2 sm:p-4 text-center">
                            <div class="flex items-center justify-center gap-3 opacity-100 xl:opacity-0 xl:group-hover:opacity-100 transition-opacity">
                                <button @click="editKurikulum(k)" class="w-10 h-10 sm:w-8 sm:h-8 rounded-xl sm:rounded-lg bg-white border border-slate-100 text-slate-400 hover:text-indigo-500 hover:border-indigo-200 hover:bg-indigo-50 flex items-center justify-center transition-all shadow-sm" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                <button @click="confirmDelete(k.id, k.nama_kurikulum)" class="w-10 h-10 sm:w-8 sm:h-8 rounded-xl sm:rounded-lg bg-white border border-slate-100 text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 flex items-center justify-center transition-all shadow-sm" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
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
                    <AppIcon name="exclamation-triangle" />️
                </div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Hapus Kurikulum?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Anda yakin ingin menghapus:<br>
                    <span class="font-bold text-slate-800">{{ deleteTarget.name }}</span>?
                </p>
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isDeleteModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">
                        Batal
                    </button>
                    <button @click="executeDelete" :disabled="isSaving" class="flex-1 py-3 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        <span v-if="isSaving" class="animate-spin text-base"><AppIcon name="clock" /></span>
                        <span v-else>Hapus</span>
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

// === API CALLS ===

const fetchKurikulum = async () => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/admin/kurikulum`, {
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
        ? `${import.meta.env.VITE_API_BASE_URL}/api/admin/kurikulum/${form.value.id}`
        : `${import.meta.env.VITE_API_BASE_URL}/api/admin/kurikulum`
    
    try {
        const response = await $fetch(url, {
            method: method,
            headers: { Authorization: `Bearer ${token}` },
            body: form.value
        })
        if (response.success) {
            useSwal().toast(response.message)
            resetForm()
            fetchKurikulum()
            if (!isDesktop.value) activeTab.value = 'table'
        }
    } catch (error) {
        console.error('Save failed:', error)
        useSwal().toast('Gagal menyimpan kurikulum. Periksa kembali form Anda.', 'error')
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
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/admin/kurikulum/${deleteTarget.value.id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            isDeleteModalOpen.value = false
            useSwal().toast(response.message)
            fetchKurikulum()
        }
    } catch (error) {
        console.error('Delete failed:', error)
        useSwal().toast('Gagal menghapus data kurikulum.', 'error')
    } finally {
        isSaving.value = false
    }
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
</style>
