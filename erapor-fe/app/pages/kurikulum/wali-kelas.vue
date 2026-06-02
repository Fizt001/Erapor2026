<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Wali Kelas</h2>
        <p class="text-sm font-semibold text-slate-500 mt-1">Tugaskan guru sebagai wali kelas.</p>
      </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col min-h-[500px]">
        <div class="p-6 md:p-8 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white shadow-sm border border-slate-200 flex items-center justify-center text-2xl">👩‍🏫</div>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-slate-700">Daftar Wali Kelas</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Filter berdasarkan tingkat</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <select v-model="filterTingkat" class="px-4 py-2 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-xs font-bold text-slate-700 outline-none">
                    <option value="">Semua Tingkat</option>
                    <option value="X">Kelas X</option>
                    <option value="XI">Kelas XI</option>
                    <option value="XII">Kelas XII</option>
                </select>
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

        <div v-else class="flex-grow p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="kelas in filteredKelas" :key="kelas.id" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 hover:shadow-md transition-shadow group flex flex-col h-full relative overflow-hidden">
                    
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-slate-50 rounded-full group-hover:bg-indigo-50 transition-colors -z-0"></div>

                    <div class="relative z-10 flex-grow">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="font-black text-lg text-slate-800">{{ kelas.nama_kelas }}</h4>
                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 bg-slate-100 px-2 py-0.5 rounded border border-slate-200 mt-1 inline-block">{{ kelas.kurikulum?.nama_kurikulum }}</span>
                            </div>
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shadow-sm border border-slate-100"
                                :class="kelas.wali_kelas ? 'bg-indigo-50 text-indigo-500' : 'bg-slate-50 text-slate-300'">
                                {{ kelas.wali_kelas ? '✅' : '❓' }}
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Guru Wali Kelas</label>
                            
                            <!-- Assign Form -->
                            <div class="flex gap-2">
                                <select :value="kelas.wali_kelas?.guru_id || ''" @change="e => saveWaliKelas(kelas.id, e.target.value)" class="flex-grow px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 outline-none focus:border-indigo-500">
                                    <option value="">-- Belum ada Wali Kelas --</option>
                                    <option v-for="g in gurus" :key="g.id" :value="g.id">{{ g.name }}</option>
                                </select>
                                
                                <button v-if="kelas.wali_kelas" @click="confirmDelete(kelas.id)" class="w-10 h-10 flex-shrink-0 bg-rose-50 hover:bg-rose-100 text-rose-500 rounded-xl flex items-center justify-center transition-colors border border-rose-100 shadow-sm" title="Hapus Jabatan Wali Kelas">
                                    ✕
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Empty Class State -->
            <div v-if="filteredKelas.length === 0" class="flex items-center justify-center flex-col p-16 text-center">
                <div class="text-6xl opacity-20 mb-4">🏫</div>
                <p class="text-sm font-bold text-slate-500">Tidak ada kelas yang ditemukan.</p>
            </div>
        </div>
    </div>

    <!-- Modal Delete -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade text-center">
            <div class="p-8">
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner border-4 border-white ring-4 ring-rose-50">🗑️</div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Lepas Wali Kelas?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Anda yakin ingin mencabut tugas guru ini sebagai wali kelas?
                </p>
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isDeleteModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all text-xs uppercase tracking-widest">Batal</button>
                    <button @click="executeDelete" class="flex-1 py-3 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        <span>Lepas Jabatan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div v-if="showToast" class="fixed bottom-10 left-1/2 -translate-x-1/2 bg-slate-900/95 backdrop-blur-md text-white px-6 py-4 rounded-full shadow-2xl flex items-center gap-4 z-[100] animate-slideUp">
      <div :class="toastType === 'success' ? 'from-emerald-400 to-emerald-500' : 'from-rose-400 to-rose-500'" class="w-8 h-8 bg-gradient-to-br rounded-full flex items-center justify-center text-white text-sm shadow-inner shadow-white/20">
          {{ toastType === 'success' ? '✓' : '✕' }}
      </div>
      <p class="font-bold text-sm tracking-wide pr-2">{{ toastMessage }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

definePageMeta({
  layout: 'kurikulum',
  middleware: 'kurikulum',
  title: 'Wali Kelas'
})

const isLoading = ref(true)
const listKelas = ref([])
const gurus = ref([])
const filterTingkat = ref('')

const filteredKelas = computed(() => {
    if (!filterTingkat.value) return listKelas.value
    return listKelas.value.filter(k => k.tingkat === filterTingkat.value)
})

const fetchData = async () => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch('http://localhost:8000/api/kurikulum/wali-kelas', {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            listKelas.value = response.data || []
            gurus.value = response.gurus || []
        }
    } catch (error) {
        console.error('Failed to fetch data:', error)
        displayToast('Gagal memuat daftar wali kelas.', 'error')
    } finally {
        isLoading.value = false
    }
}

const saveWaliKelas = async (kelas_id, guru_id) => {
    if (!guru_id) return
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch('http://localhost:8000/api/kurikulum/wali-kelas', {
            method: 'POST',
            headers: { Authorization: `Bearer ${token}` },
            body: { kelas_id, guru_id }
        })
        if (response.success) {
            displayToast('Wali Kelas berhasil ditugaskan.', 'success')
            fetchData()
        }
    } catch (error) {
        console.error('Save failed:', error)
        displayToast('Gagal menyimpan penugasan.', 'error')
    }
}

const isDeleteModalOpen = ref(false)
const deleteTargetKelasId = ref(null)

const confirmDelete = (kelasId) => {
    deleteTargetKelasId.value = kelasId
    isDeleteModalOpen.value = true
}

const executeDelete = async () => {
    if (!deleteTargetKelasId.value) return
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/kurikulum/wali-kelas/${deleteTargetKelasId.value}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            isDeleteModalOpen.value = false
            displayToast('Wali Kelas berhasil dilepas.', 'success')
            fetchData()
        }
    } catch (error) {
        console.error('Delete failed:', error)
        displayToast('Gagal menghapus penugasan.', 'error')
    }
}

const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('success')

const displayToast = (msg, type = 'success') => {
    toastMessage.value = msg
    toastType.value = type
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
