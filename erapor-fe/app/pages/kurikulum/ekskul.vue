<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Master Ekstrakurikuler</h2>
        <p class="text-sm font-semibold text-slate-500 mt-1">Kelola data ekstrakurikuler sekolah dan pembina.</p>
      </div>
    </div>

    <!-- MAIN GRID LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      
      <!-- ==============================================
           KIRI: FORM EKSKUL (Col 4)
           ============================================== -->
      <div class="lg:col-span-4 space-y-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-100">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">{{ isEditing ? 'Edit Ekstrakurikuler' : 'Tambah Ekstrakurikuler' }}</h3>
                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Lengkapi form di bawah ini.</p>
            </div>
            
            <div class="p-6">
                <form @submit.prevent="saveData" class="space-y-5">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Ekstrakurikuler</label>
                        <input type="text" v-model="formData.nama_ekskul" required placeholder="Contoh: Pramuka, Paskibra, PMR" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-bold text-slate-800 placeholder:text-slate-400 outline-none placeholder:font-normal">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Pembina (Opsional)</label>
                        <input type="text" v-model="formData.nama_pembina" placeholder="Masukkan nama pembina" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-bold text-slate-800 placeholder:text-slate-400 outline-none placeholder:font-normal">
                    </div>
                    
                    <div class="pt-4 flex gap-3">
                        <button v-if="isEditing" type="button" @click="resetForm" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all text-xs uppercase tracking-widest">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSaving" class="flex-[2] py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                            <span v-if="isSaving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                            <span>{{ isEditing ? 'Simpan Perubahan' : 'Tambah Ekskul' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
      </div>

      <!-- ==============================================
           KANAN: TABEL EKSKUL (Col 8)
           ============================================== -->
      <div class="lg:col-span-8">
         <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col min-h-[600px]">
            
            <div class="p-6 md:p-8 bg-slate-50/50 border-b border-slate-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white shadow-sm border border-slate-200 flex items-center justify-center text-2xl">🏃</div>
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-widest text-slate-700">Daftar Ekstrakurikuler</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Semua data ekstrakurikuler</p>
                        </div>
                    </div>
                    <button @click="fetchData" class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-slate-200 transition-colors" title="Refresh">
                        🔄
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60">
                <div class="w-8 h-8 border-4 border-slate-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</span>
            </div>

            <!-- Empty State -->
            <div v-else-if="!ekskuls || ekskuls.length === 0" class="flex-grow flex items-center justify-center flex-col p-16 text-center">
                <div class="text-6xl opacity-20 mb-4">🏜️</div>
                <p class="text-sm font-bold text-slate-500">Belum ada data Ekstrakurikuler.</p>
                <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-2 font-bold">Silakan tambahkan melalui form di samping.</p>
            </div>

            <!-- Table Content -->
            <div v-else class="flex-grow overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-slate-200">
                            <th class="p-5 pl-8 w-16 text-center">#</th>
                            <th class="p-5">Informasi Ekskul</th>
                            <th class="p-5 text-right pr-8">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr v-for="(item, index) in ekskuls" :key="item.id" class="border-b border-slate-100 hover:bg-slate-50/80 transition-colors group">
                            <td class="p-4 pl-8 text-center text-xs font-bold text-slate-300">
                                {{ index + 1 }}
                            </td>
                            <td class="p-4">
                                <p class="font-black text-slate-800 text-sm">{{ item.nama_ekskul }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">Pembina: {{ item.nama_pembina || '-' }}</span>
                                </div>
                            </td>
                            <td class="p-4 pr-8 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                    <button @click="editData(item)" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-indigo-500 hover:border-indigo-200 hover:bg-indigo-50 flex items-center justify-center transition-all shadow-sm" title="Edit">✏️</button>
                                    <button @click="confirmDelete(item)" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 flex items-center justify-center transition-all shadow-sm" title="Hapus">🗑️</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

         </div>
      </div>
    </div>

    <!-- Modal Delete -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade text-center">
            <div class="p-8">
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner border-4 border-white ring-4 ring-rose-50">⚠️</div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Hapus Ekstrakurikuler?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Anda yakin ingin menghapus ekskul:<br>
                    <span class="font-bold text-slate-800">{{ deleteTarget?.nama_ekskul }}</span>?
                </p>
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isDeleteModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all text-xs uppercase tracking-widest">Batal</button>
                    <button @click="executeDelete" class="flex-1 py-3 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        <span>Hapus</span>
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
  layout: 'kurikulum',
  middleware: 'kurikulum',
  title: 'Master Ekstrakurikuler'
})

const ekskuls = ref([])
const isLoading = ref(true)
const isSaving = ref(false)

const isEditing = ref(false)
const formData = ref({
    id: null,
    nama_ekskul: '',
    nama_pembina: ''
})

const isDeleteModalOpen = ref(false)
const deleteTarget = ref(null)

const showToast = ref(false)
const toastMessage = ref('')

const fetchData = async () => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/kurikulum/ekskul`, {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            ekskuls.value = response.data
        }
    } catch (error) {
        console.error('Failed to fetch ekskul:', error)
    } finally {
        isLoading.value = false
    }
}

const saveData = async () => {
    isSaving.value = true
    const token = useCookie('auth_token').value
    const url = isEditing.value 
        ? `http://localhost:8000/api/kurikulum/ekskul/${formData.value.id}` 
        : `http://localhost:8000/api/kurikulum/ekskul`
    const method = isEditing.value ? 'PUT' : 'POST'

    try {
        const response = await $fetch(url, {
            method: method,
            headers: { Authorization: `Bearer ${token}` },
            body: formData.value
        })
        
        if (response.success) {
            displayToast(response.message)
            resetForm()
            fetchData()
        }
    } catch (error) {
        console.error('Save error:', error)
        alert('Gagal menyimpan data ekstrakurikuler.')
    } finally {
        isSaving.value = false
    }
}

const editData = (item) => {
    isEditing.value = true
    formData.value = {
        id: item.id,
        nama_ekskul: item.nama_ekskul,
        nama_pembina: item.nama_pembina
    }
}

const resetForm = () => {
    isEditing.value = false
    formData.value = {
        id: null,
        nama_ekskul: '',
        nama_pembina: ''
    }
}

const confirmDelete = (item) => {
    deleteTarget.value = item
    isDeleteModalOpen.value = true
}

const executeDelete = async () => {
    if(!deleteTarget.value) return
    
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/kurikulum/ekskul/${deleteTarget.value.id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            isDeleteModalOpen.value = false
            displayToast(response.message)
            fetchData()
        }
    } catch (error) {
        console.error('Delete failed:', error)
        alert('Gagal menghapus data.')
    }
}

const displayToast = (msg) => {
    toastMessage.value = msg
    showToast.value = true
    setTimeout(() => { showToast.value = false }, 3500)
}

onMounted(() => {
    fetchData()
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
