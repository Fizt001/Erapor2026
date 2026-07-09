<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    
<!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-2 grid grid-cols-2 gap-2 z-20">
        <button type="button" @click="activeTab = 'form'" :class="activeTab === 'form' ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 ring-2 ring-emerald-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'" class="rounded-xl flex items-center justify-center py-2 px-1 transition-all active:scale-95">
          <span class="text-lg mr-1.5 transition-transform" :class="activeTab === 'form' ? 'scale-110' : ''">📝</span>
          <span class="text-[10px] font-black uppercase tracking-wider">Entry Ref</span>
        </button>
        <button type="button" @click="activeTab = 'table'" :class="activeTab === 'table' ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 ring-2 ring-emerald-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'" class="rounded-xl flex items-center justify-center py-2 px-1 transition-all active:scale-95">
          <span class="text-lg mr-1.5 transition-transform" :class="activeTab === 'table' ? 'scale-110' : ''">🗄️</span>
          <span class="text-[10px] font-black uppercase tracking-wider">Database</span>
        </button>
      </div>

      <!-- Panel Dock Kiri -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all overflow-y-auto custom-scrollbar', activeTab === 'form' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[60px]' : '']">
        
        <div class="p-6 space-y-6 animate-fadeIn">
          <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl p-5 border border-emerald-500 shadow-sm relative overflow-hidden shrink-0">
            <div class="relative z-10">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">Data Referensi</h3>
                <p class="text-[10px] text-emerald-100 font-semibold uppercase mt-0.5">{{ isEditing ? 'Mode Update' : 'Mode Tambah Baru' }}</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-20 text-white">
              <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M4 5a2 2 0 012-2h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm2 0v14h12V5H6zm2 3a1 1 0 011-1h6a1 1 0 110 2H9a1 1 0 01-1-1zm0 4a1 1 0 011-1h6a1 1 0 110 2H9a1 1 0 01-1-1zm0 4a1 1 0 011-1h6a1 1 0 110 2H9a1 1 0 01-1-1z"></path></svg>
            </div>
          </div>

          <form @submit.prevent="saveData" class="space-y-5">
              <div>
                  <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Jenis Referensi</label>
                  <input type="text" v-model="formData.jenis" required placeholder="Contoh: kategori_mapel" list="jenis-list" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200/70 rounded-2xl focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-bold text-slate-700 outline-none">
                  <datalist id="jenis-list">
                      <option v-for="j in uniqueJenisList" :key="j" :value="j">{{ formatJenis(j) }}</option>
                  </datalist>
                  <p class="text-[10px] font-bold text-slate-400 mt-1 ml-1">Kecil & underscore (tanpa spasi).</p>
              </div>
              <div>
                  <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kode / Value</label>
                  <input type="text" v-model="formData.kode" required placeholder="Contoh: umum, A, B" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200/70 rounded-2xl focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-bold text-slate-700 outline-none">
              </div>
              <div>
                  <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Nama Tampilan</label>
                  <input type="text" v-model="formData.nama" required placeholder="Contoh: Muatan Umum" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200/70 rounded-2xl focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-bold text-slate-700 outline-none">
              </div>
              <div>
                  <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Keterangan / Nama Panjang</label>
                  <input type="text" v-model="formData.keterangan" placeholder="Contoh: Penilaian Sumatif Akhir Semester" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200/70 rounded-2xl focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-bold text-slate-700 outline-none">
                  <p class="text-[10px] font-bold text-slate-400 mt-1 ml-1">Opsional, digunakan untuk kepanjangan kode.</p>
              </div>
              <div class="pt-4 border-t border-slate-100 flex gap-3">
                  <button v-if="isEditing" type="button" @click="resetForm" class="flex-1 py-3.5 bg-rose-50 text-rose-600 font-bold rounded-2xl hover:bg-rose-100 transition-all uppercase tracking-widest text-xs border border-rose-200">
                      Batal
                  </button>
                  <button type="submit" :disabled="isSaving" class="flex-[2] py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-2xl shadow-lg shadow-emerald-500/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 uppercase tracking-widest text-xs">
                      <span v-if="isSaving" class="animate-spin text-lg">⏳</span>
                      <span v-else class="text-lg">💾</span> {{ isEditing ? 'Simpan' : 'Tambah' }}
                  </button>
              </div>
          </form>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative', activeTab === 'table' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[60px]' : '']">
        <div class="p-6 lg:p-8 max-w-5xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            <!-- Table Header & Filters -->
            <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-white shrink-0 z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-lg border border-emerald-100">📋</div>
                    <div>
                        <h3 class="text-[13px] font-black leading-none uppercase tracking-wide text-slate-800">Database Master</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Daftar Referensi Sistem</p>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60">
                <div class="w-8 h-8 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</span>
            </div>

            <div v-else class="flex-1 overflow-y-auto custom-scrollbar p-6 space-y-6 bg-slate-50/30">
                <!-- Group Data -->
                <div v-for="(group, jenis) in groupedData" :key="jenis" class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col min-h-[100px]">
                    <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-black text-slate-700 text-sm uppercase tracking-widest">{{ formatJenis(jenis) }}</h3>
                        <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md border border-emerald-100">{{ group.length }} Item</span>
                    </div>
                    
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-white text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-slate-100">
                                    <th class="p-4 pl-6 w-16 text-center">#</th>
                                    <th class="p-4">Kode / Value</th>
                                    <th class="p-4">Nama (Display) & Keterangan</th>
                                    <th class="p-4 text-right pr-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-xs">
                                <tr v-for="(item, index) in group" :key="item.id" class="border-b border-slate-50 hover:bg-emerald-50/30 transition-colors group">
                                    <td class="p-4 pl-6 text-center text-[10px] font-bold text-slate-400">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="p-4">
                                        <span class="text-[10px] font-black uppercase tracking-widest px-2 py-1.5 rounded bg-slate-100 text-slate-600 border border-slate-200">{{ item.kode }}</span>
                                    </td>
                                    <td class="p-4">
                                        <p class="font-bold text-slate-800 text-[13px]">{{ item.nama }}</p>
                                        <p v-if="item.keterangan" class="text-[10px] text-slate-500 mt-0.5">{{ item.keterangan }}</p>
                                    </td>
                                    <td class="p-4 pr-6 text-right">
                                        <div class="flex items-center justify-end gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                            <button @click="editData(item)" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-emerald-500 hover:bg-emerald-50 hover:border-emerald-200 flex items-center justify-center transition-all shadow-sm" title="Edit">✏️</button>
                                            <button @click="confirmDelete(item)" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:bg-rose-50 hover:border-rose-200 flex items-center justify-center transition-all shadow-sm" title="Hapus">🗑️</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="group.length === 0">
                                    <td colspan="4" class="py-8 text-center text-slate-400 text-xs font-bold italic">Belum ada data untuk jenis ini.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div v-if="Object.keys(groupedData).length === 0" class="bg-white p-16 rounded-2xl border border-slate-200/60 shadow-sm text-center flex flex-col items-center justify-center h-full min-h-[400px]">
                    <span class="text-6xl mb-4 block opacity-30">🗄️</span>
                    <h3 class="text-lg font-black text-slate-700 mb-2 uppercase tracking-widest">Master Database Kosong</h3>
                    <p class="text-slate-500 text-sm font-semibold max-w-sm">Belum ada referensi yang ditambahkan. Tambahkan data referensi di panel sebelah kiri.</p>
                </div>
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
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Hapus Referensi?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Anda yakin ingin menghapus referensi <span class="font-bold text-rose-600">{{ itemToDelete?.nama }}</span> secara permanen? Data yang telah dihapus tidak dapat dikembalikan.
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

definePageMeta({
  layout: 'admin',
  middleware: 'admin',
  title: 'Master Database'
})

const windowWidth = ref(1024)
const isDesktop = computed(() => windowWidth.value >= 1280)
const activeTab = ref('table')

const tokenCookie = useCookie('auth_token')
const referensis = ref([])
const isLoading = ref(true)
const isSaving = ref(false)
const isEditing = ref(false)

const isDeleteModalOpen = ref(false)
const itemToDelete = ref(null)
const isDeleting = ref(false)

const formData = ref({
    id: null,
    jenis: '',
    kode: '',
    nama: '',
    keterangan: ''
})

const groupedData = computed(() => {
    const groups = {}
    
    referensis.value.forEach(item => {
        if (!groups[item.jenis]) {
            groups[item.jenis] = []
        }
        groups[item.jenis].push(item)
    })
    
    const orderedGroups = {}
    Object.keys(groups).sort().forEach(key => {
        orderedGroups[key] = groups[key]
    })
    
    return orderedGroups
})

const uniqueJenisList = computed(() => {
    return Object.keys(groupedData.value)
})

const formatJenis = (jenis) => {
    if (!jenis) return ''
    return jenis.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')
}

const fetchData = async () => {
    isLoading.value = true
    try {
        const response = await $fetch('http://localhost:8000/api/admin/referensi', {
            headers: { 'Authorization': `Bearer ${tokenCookie.value}` }
        })
        referensis.value = response.data || []
    } catch (error) {
        console.error('Error fetching referensi:', error)
    } finally {
        isLoading.value = false
    }
}

const resetForm = () => {
    isEditing.value = false
    formData.value = { id: null, jenis: '', kode: '', nama: '', keterangan: '' }
}

const editData = (item) => {
    isEditing.value = true
    formData.value = { ...item }
    activeTab.value = 'form'
}

const displayToast = (msg, type = 'success') => {
    useSwal().toast(msg, type)
}

const saveData = async () => {
    if (!formData.value.jenis || !formData.value.kode || !formData.value.nama) return

    isSaving.value = true
    try {
        const url = isEditing.value 
            ? `http://localhost:8000/api/admin/referensi/${formData.value.id}`
            : 'http://localhost:8000/api/admin/referensi'
            
        const response = await $fetch(url, {
            method: isEditing.value ? 'PUT' : 'POST',
            headers: { 
                'Authorization': `Bearer ${tokenCookie.value}`,
                'Content-Type': 'application/json'
            },
            body: formData.value
        })
        
        displayToast(response.message || 'Data berhasil disimpan', 'success')
        await fetchData()
        resetForm()
        if (!isDesktop.value) activeTab.value = 'table'
    } catch (error) {
        console.error('Error saving:', error)
        displayToast('Gagal menyimpan data referensi.', 'error')
    } finally {
        isSaving.value = false
    }
}

const confirmDelete = (item) => {
    itemToDelete.value = item
    isDeleteModalOpen.value = true
}

const executeDelete = async () => {
    if (!itemToDelete.value) return
    isDeleting.value = true
    try {
        const response = await $fetch(`http://localhost:8000/api/admin/referensi/${itemToDelete.value.id}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${tokenCookie.value}` }
        })
        useSwal().toast(response.message || 'Data berhasil dihapus')
        isDeleteModalOpen.value = false
        await fetchData()
    } catch (error) {
        console.error('Error deleting:', error)
        useSwal().toast('Gagal menghapus data referensi.', 'error')
    } finally {
        isDeleting.value = false
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
    fetchData()
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
