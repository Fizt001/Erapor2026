<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    
<!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-2 grid grid-cols-2 gap-2 z-20">
        <button type="button" @click="activeTab = 'form'" :class="activeTab === 'form' ? 'bg-gradient-to-br from-rose-500 to-rose-600 text-white shadow-md shadow-rose-500/20 ring-2 ring-rose-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'" class="rounded-xl flex items-center justify-center py-2 px-1 transition-all active:scale-95">
          <span class="text-lg mr-1.5 transition-transform" :class="activeTab === 'form' ? 'scale-110' : ''">📝</span>
          <span class="text-[10px] font-black uppercase tracking-wider">Entry SP</span>
        </button>
        <button type="button" @click="activeTab = 'table'" :class="activeTab === 'table' ? 'bg-gradient-to-br from-rose-500 to-rose-600 text-white shadow-md shadow-rose-500/20 ring-2 ring-rose-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'" class="rounded-xl flex items-center justify-center py-2 px-1 transition-all active:scale-95">
          <span class="text-lg mr-1.5 transition-transform" :class="activeTab === 'table' ? 'scale-110' : ''">🗄️</span>
          <span class="text-[10px] font-black uppercase tracking-wider">Database</span>
        </button>
      </div>

      <!-- Panel Dock Kiri -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all overflow-y-auto custom-scrollbar', activeTab === 'form' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[60px]' : '']">
        
        <div class="p-6 space-y-6 animate-fadeIn">
          <div class="bg-gradient-to-br from-rose-500 to-rose-700 rounded-2xl p-5 border border-rose-400 shadow-sm relative overflow-hidden shrink-0">
            <div class="relative z-10">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">Kategori Penanganan</h3>
                <p class="text-[10px] text-rose-100 font-semibold uppercase mt-0.5">{{ isEditing ? 'Mode Update Kategori' : 'Mode Tambah Kategori' }}</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-20 text-white">
              <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M4 5a2 2 0 012-2h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm2 0v14h12V5H6zm2 3a1 1 0 011-1h6a1 1 0 110 2H9a1 1 0 01-1-1zm0 4a1 1 0 011-1h6a1 1 0 110 2H9a1 1 0 01-1-1zm0 4a1 1 0 011-1h6a1 1 0 110 2H9a1 1 0 01-1-1z"></path></svg>
            </div>
          </div>

          <form @submit.prevent="saveData" class="space-y-5">
              <div>
                  <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Jenis Referensi</label>
                  <input type="text" value="kategori_penanganan" disabled class="w-full px-4 py-3 bg-slate-100 border-2 border-slate-200/70 rounded-2xl text-sm font-bold text-slate-500 cursor-not-allowed">
                  <p class="text-[10px] font-bold text-slate-400 mt-1 ml-1">Terkunci secara sistem untuk BK.</p>
              </div>
              <div>
                  <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Nama Tampilan Kategori</label>
                  <input type="text" v-model="formData.nama" @input="generateKode" required placeholder="Contoh: Surat Peringatan 1" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200/70 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all text-sm font-bold text-slate-700 outline-none">
              </div>
              <div>
                  <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kode Internal</label>
                  <input type="text" v-model="formData.kode" required placeholder="contoh_kode" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200/70 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all text-sm font-bold text-slate-700 outline-none">
                  <p class="text-[10px] font-bold text-slate-400 mt-1 ml-1">Otomatis dihasilkan, bisa diedit.</p>
              </div>
              <div>
                  <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Keterangan / Detail</label>
                  <input type="text" v-model="formData.keterangan" placeholder="Keterangan opsional" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200/70 rounded-2xl focus:bg-white focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all text-sm font-bold text-slate-700 outline-none">
              </div>
              <div class="pt-4 border-t border-slate-100 flex gap-3">
                  <button v-if="isEditing" type="button" @click="resetForm" class="flex-1 py-3.5 bg-slate-100 text-slate-600 font-bold rounded-2xl hover:bg-slate-200 transition-all uppercase tracking-widest text-xs">
                      Batal
                  </button>
                  <button type="submit" :disabled="isSaving" class="flex-[2] py-3.5 bg-gradient-to-r from-rose-500 to-rose-600 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 uppercase tracking-widest text-xs">
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
            <!-- Table Header -->
            <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-white shrink-0 z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center text-lg border border-rose-100">📋</div>
                    <div>
                        <h3 class="text-[13px] font-black leading-none uppercase tracking-wide text-slate-800">Master Kategori Penanganan</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Daftar kategori yang muncul di dropdown BK</p>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60">
                <div class="w-8 h-8 border-4 border-rose-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</span>
            </div>

            <div v-else class="flex-1 overflow-y-auto custom-scrollbar p-6 bg-slate-50/30">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col min-h-[100px]">
                    <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-black text-slate-700 text-sm uppercase tracking-widest">Kategori Penanganan</h3>
                        <span class="text-[10px] font-black uppercase tracking-widest text-rose-600 bg-rose-50 px-2 py-1 rounded-md border border-rose-100">{{ filteredData.length }} Item</span>
                    </div>
                    
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-white text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-slate-100">
                                    <th class="px-6 py-4">Tampilan</th>
                                    <th class="px-6 py-4">Kode Internal</th>
                                    <th class="px-6 py-4">Keterangan</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-xs font-semibold text-slate-600">
                                <tr v-for="item in filteredData" :key="item.id" class="border-b border-slate-50 hover:bg-slate-50/80 transition-colors group">
                                    <td class="px-6 py-3.5 font-bold text-slate-800">{{ item.nama }}</td>
                                    <td class="px-6 py-3.5 font-mono text-[10px] text-slate-500">{{ item.kode }}</td>
                                    <td class="px-6 py-3.5 text-slate-400">{{ item.keterangan || '-' }}</td>
                                    <td class="px-6 py-3.5 text-right opacity-0 group-hover:opacity-100 transition-opacity">
                                        <div class="flex items-center justify-end gap-1">
                                            <button @click="editData(item)" class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center hover:bg-sky-100 transition-colors" title="Edit">
                                                ✏️
                                            </button>
                                            <button @click="deleteData(item.id)" class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-100 transition-colors" title="Hapus">
                                                🗑️
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredData.length === 0">
                                    <td colspan="4" class="px-6 py-10 text-center">
                                        <div class="text-3xl mb-2 opacity-30">📭</div>
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Belum ada kategori yang ditambahkan.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
import { ref, onMounted, computed, onUnmounted } from 'vue'

definePageMeta({
  layout: 'bk',
  middleware: 'bk',
  title: 'Master Kategori'
})

const isLoading = ref(true)
const referensiList = ref([])
const formData = ref({
    jenis: 'kategori_penanganan',
    kode: '',
    nama: '',
    keterangan: ''
})

const isEditing = ref(false)
const editId = ref(null)
const isSaving = ref(false)

// Mobile responsive logic
const activeTab = ref('table')
const isDesktop = ref(true)

const checkWindowSize = () => {
    isDesktop.value = window.innerWidth >= 1280
}

onMounted(() => {
    checkWindowSize()
    window.addEventListener('resize', checkWindowSize)
    fetchData()
})

onUnmounted(() => {
    window.removeEventListener('resize', checkWindowSize)
})

const filteredData = computed(() => {
    return referensiList.value.filter(item => item.jenis === 'kategori_penanganan')
})

const fetchData = async () => {
    isLoading.value = true
    try {
        const token = useCookie('auth_token').value
        const { data } = await useFetch('http://localhost:8000/api/bk/referensi', {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (data.value && data.value.success) {
            referensiList.value = data.value.data
        }
    } catch (error) {
        console.error('Error fetching data:', error)
    } finally {
        isLoading.value = false
    }
}

const generateKode = () => {
    if (!isEditing.value && formData.value.nama) {
        formData.value.kode = formData.value.nama
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '_')
            .replace(/(^_|_$)/g, '')
    }
}

const resetForm = () => {
    formData.value = {
        jenis: 'kategori_penanganan',
        kode: '',
        nama: '',
        keterangan: ''
    }
    isEditing.value = false
    editId.value = null
    activeTab.value = 'table'
}

const editData = (item) => {
    formData.value = {
        jenis: item.jenis,
        kode: item.kode,
        nama: item.nama,
        keterangan: item.keterangan || ''
    }
    isEditing.value = true
    editId.value = item.id
    activeTab.value = 'form'
}

const saveData = async () => {
    isSaving.value = true
    try {
        const url = isEditing.value 
            ? `http://localhost:8000/api/bk/referensi/${editId.value}`
            : 'http://localhost:8000/api/bk/referensi'
        
        const method = isEditing.value ? 'PUT' : 'POST'
        const token = useCookie('auth_token').value
        
        const { data, error } = await useFetch(url, {
            method,
            headers: { Authorization: `Bearer ${token}` },
            body: formData.value
        })

        if (error.value) throw error.value

        if (data.value && data.value.success) {
            await fetchData()
            resetForm()
        }
    } catch (error) {
        console.error('Error saving data:', error)
        alert('Gagal menyimpan referensi. Pastikan kode belum terpakai.')
    } finally {
        isSaving.value = false
    }
}

const deleteData = async (id) => {
    if (!confirm('Anda yakin ingin menghapus referensi ini? Kategori yang sudah terpakai oleh siswa tidak boleh dihapus.')) return
    
    try {
        const token = useCookie('auth_token').value
        const { data, error } = await useFetch(`http://localhost:8000/api/bk/referensi/${id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })

        if (error.value) throw error.value

        if (data.value && data.value.success) {
            await fetchData()
        }
    } catch (error) {
        console.error('Error deleting data:', error)
        alert('Gagal menghapus data referensi.')
    }
}
</script>
