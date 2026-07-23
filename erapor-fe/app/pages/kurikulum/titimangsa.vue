<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-2 grid grid-cols-2 gap-2 z-20">
        <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTab = tab.id"
          :class="activeTab === tab.id ? 'bg-gradient-to-br from-amber-500 to-amber-600 text-white shadow-md shadow-amber-500/20 ring-2 ring-amber-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="rounded-xl flex items-center justify-center py-2 px-1 transition-all active:scale-95">
          <span class="text-lg mr-1.5 transition-transform" :class="activeTab === tab.id ? 'scale-110' : ''"><AppIcon :name="tab.icon" /></span>
          <span class="text-[10px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
        </button>
      </div>

      <!-- Panel Dock Kiri (Form) -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all', activeTab === 'form' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[60px]' : '']">
        
        <div class="p-6 shrink-0 z-10 relative">
          <div class="bg-gradient-to-r from-amber-600 to-orange-600 rounded-2xl p-5 border border-amber-500 shadow-sm relative overflow-hidden flex items-center gap-4">
            <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0 relative z-10"><AppIcon name="clock" class="w-6 h-6" /></div>
            <div class="relative z-10">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">{{ isEdit ? 'Edit Periode' : 'Periode Baru' }}</h3>
                <p class="text-[10px] text-amber-100 font-semibold uppercase mt-0.5">{{ isEdit ? 'Perbarui Data' : 'Tambah Data Manual' }}</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-20 text-white">
              <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path></svg>
            </div>
          </div>
        </div>
        
        <div class="flex-1 overflow-y-auto custom-scrollbar p-5">
            <form @submit.prevent="saveData" class="space-y-4">
                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kurikulum</label>
                    <select v-model="form.kurikulum_id" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-bold text-slate-700 outline-none">
                        <option value="" disabled>-- Pilih Kurikulum --</option>
                        <option v-for="k in kurikulums" :key="k.id" :value="k.id">{{ k.nama_kurikulum }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Nama Periode</label>
                    <select v-model="form.nama_periode" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-bold text-slate-700 outline-none">
                        <option value="" disabled>-- Pilih Nama Periode --</option>
                        <option v-for="rp in referensiPeriode" :key="rp.id" :value="rp.nama">{{ rp.nama }}</option>
                    </select>
                    <p v-if="referensiPeriode.length === 0" class="text-[10px] text-rose-500 mt-1 font-medium">Data kosong. Tambahkan dulu di Master Database -> Nama Periode.</p>
                </div>

                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Target Tingkat</label>
                    <div class="flex flex-wrap gap-3 bg-slate-50 p-3 border-2 border-slate-200/70 rounded-2xl">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" v-model="form.target_tingkat_arr" value="X" class="w-4 h-4 text-amber-600 rounded border-slate-300 focus:ring-amber-500">
                            <span class="text-xs font-bold text-slate-700">Kelas X</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" v-model="form.target_tingkat_arr" value="XI" class="w-4 h-4 text-amber-600 rounded border-slate-300 focus:ring-amber-500">
                            <span class="text-xs font-bold text-slate-700">Kelas XI</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" v-model="form.target_tingkat_arr" value="XII" class="w-4 h-4 text-amber-600 rounded border-slate-300 focus:ring-amber-500">
                            <span class="text-xs font-bold text-slate-700">Kelas XII</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Tempat Cetak</label>
                        <input v-model="form.tempat_cetak" type="text" required placeholder="Misal: Bekasi" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-bold text-slate-700 outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Tanggal Cetak</label>
                        <input v-model="form.tanggal_cetak" type="date" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-bold text-slate-700 outline-none">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex gap-3">
                    <button v-if="isEdit" type="button" @click="resetForm" class="flex-1 py-3 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest border border-rose-200">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSaving" class="flex-[2] py-3 bg-gradient-to-r from-amber-500 to-amber-600 text-white font-bold rounded-2xl shadow-lg shadow-amber-500/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                        <span v-if="isSaving" class="animate-spin"><AppIcon name="clock" class="w-6 h-6" /></span>
                        <span v-else class="text-lg"><AppIcon name="save" /></span> 
                        {{ isEdit ? 'Simpan' : 'Tambah' }}
                    </button>
                </div>
            </form>
        </div>
      </div>

      <!-- Panel Flow Kanan (Tabel) -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative', activeTab === 'table' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[60px]' : '']">
        <div class="p-2 sm:pt-3 sm:pb-6 sm:px-6 lg:pt-3 lg:pb-8 lg:px-8 max-w-5xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-2xl sm:rounded-[2rem] shadow-[0_2px_15px_-3px_rgba(0,0,0,0.05)] border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            <!-- Header Flow -->
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shrink-0 z-10 bg-white">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 shadow-sm border border-amber-100 flex items-center justify-center text-xl hidden sm:flex text-amber-500"><AppIcon name="calendar" class="w-6 h-6" /></div>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-amber-700">Database Titimangsa</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Plotting Periode & Tanggal Rapor</p>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="flex-1 overflow-y-auto overflow-x-hidden custom-scrollbar relative bg-white">
            <table class="w-full text-left border-collapse min-w-full">
                <thead class="hidden sm:table-header-group sticky top-0 z-10 bg-slate-50 border-b border-slate-200 shadow-sm">
                    <tr class="text-[9px] uppercase tracking-widest font-black text-slate-500">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Kurikulum</th>
                        <th class="py-3 px-4">Periode & Kelas</th>
                        <th class="py-3 px-4">Cetak Rapor</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-xs font-medium text-slate-700 flex flex-col sm:table-row-group">
                    <tr v-if="titimangsas.length === 0" class="flex sm:table-row">
                        <td colspan="6" class="p-16 w-full text-center text-slate-400 font-bold bg-white block sm:table-cell">
                            <span class="text-4xl block mb-2 opacity-30"><AppIcon name="calendar" class="w-6 h-6 inline-block" /></span>
                            Belum ada data titimangsa.
                        </td>
                    </tr>
                    <tr v-for="(item, index) in titimangsas" :key="item.id" class="border-b border-slate-100 hover:bg-slate-50/80 transition-colors bg-white group flex flex-col sm:table-row p-4 sm:p-0 relative">
                        <td class="px-0 py-1 sm:p-4 text-left sm:text-center text-[10px] sm:text-xs font-bold text-slate-400 flex sm:table-cell items-center justify-between">
                            <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-slate-400">Nomor</span>
                            <span>{{ index + 1 }}</span>
                        </td>
                        <td class="px-0 py-2 sm:p-4 flex sm:table-cell items-center justify-between border-b sm:border-0 border-slate-50">
                            <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-slate-400">Kurikulum</span>
                            <span class="px-2 py-1 bg-slate-100 text-slate-600 font-bold rounded text-[10px] uppercase tracking-wider border border-slate-200">{{ item.kurikulum?.nama_kurikulum }}</span>
                        </td>
                        <td class="px-0 py-2 sm:p-4 flex sm:table-cell items-center justify-between border-b sm:border-0 border-slate-50">
                            <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-slate-400">Periode & Kelas</span>
                            <div class="text-right sm:text-left">
                                <div class="font-black text-slate-800">{{ item.nama_periode }}</div>
                                <div class="text-[10px] font-bold text-amber-500 uppercase tracking-widest mt-0.5">Tingkat: {{ item.target_tingkat }}</div>
                            </div>
                        </td>
                        <td class="px-0 py-2 sm:p-4 flex sm:table-cell items-center justify-between border-b sm:border-0 border-slate-50">
                            <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-slate-400">Cetak Rapor</span>
                            <div class="text-right sm:text-left">
                                <div class="font-bold text-slate-700">{{ item.tempat_cetak }}</div>
                                <div class="text-[10px] font-medium text-slate-500 mt-0.5">{{ item.tanggal_cetak }}</div>
                            </div>
                        </td>
                        <td class="px-0 py-2 sm:p-4 flex sm:table-cell items-center justify-between border-b sm:border-0 border-slate-50 sm:text-center">
                            <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-slate-400">Status</span>
                            <button @click="toggleStatus(item.id)" class="px-3 py-1 text-[9px] uppercase tracking-widest font-black rounded-full transition-colors border"
                                :class="item.is_aktif ? 'bg-emerald-50 text-emerald-600 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-50 text-slate-500 border-slate-200 hover:bg-slate-100'">
                                {{ item.is_aktif ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </td>
                        <td class="px-0 pt-3 sm:p-4 text-center">
                            <div class="flex items-center justify-center sm:justify-center gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                <button @click="editData(item)" class="w-10 h-10 sm:w-8 sm:h-8 rounded-xl sm:rounded-lg bg-white border border-slate-200 text-slate-400 hover:border-amber-200 hover:bg-amber-50 hover:text-amber-600 flex items-center justify-center transition-colors shadow-sm" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg></button>
                                <button @click="confirmDelete(item)" class="w-10 h-10 sm:w-8 sm:h-8 rounded-xl sm:rounded-lg bg-white border border-slate-200 text-slate-400 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 flex items-center justify-center transition-colors shadow-sm" title="Hapus"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
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

    <!-- Konfirmasi Hapus -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade text-center">
            <div class="p-8">
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner border-4 border-white ring-4 ring-rose-50">
                    <AppIcon name="exclamation-triangle" class="w-5 h-5 inline-block mr-1" />
                </div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Hapus Periode?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Anda yakin ingin menghapus periode <span class="font-bold text-rose-600">{{ deleteTarget?.nama_periode }}</span> secara permanen?
                </p>
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isDeleteModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">
                        Batal
                    </button>
                    <button @click="executeDelete" class="flex-1 py-3 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAutoSave } from '~/composables/useAutoSave'

definePageMeta({
  layout: 'kurikulum',
  middleware: 'kurikulum',
  title: 'Periode & Titimangsa'
})

// Responsiveness detector
const windowWidth = ref(1024) 
const isDesktop = computed(() => windowWidth.value >= 1280) // xl breakpoint

// Tabs for Mobile
const activeTab = ref('table')
const mobileTabs = [
  { id: 'form', title: 'Form Data', icon: 'document-text' },
  { id: 'table', title: 'Database', icon: 'clipboard' }
]

const titimangsas = ref([])
const kurikulums = ref([])
const referensiPeriode = ref([])
const tahunAktif = ref(null)

const isEdit = ref(false)
const isSaving = ref(false)
const isDeleteModalOpen = ref(false)
const deleteTarget = ref(null)

const form = ref({
  id: null,
  kurikulum_id: '',
  nama_periode: '',
  target_tingkat_arr: ['X', 'XI', 'XII'],
  tempat_cetak: 'Bekasi',
  tanggal_cetak: ''
})

const fetchData = async () => {
  try {
    const token = useCookie('auth_token').value
    const response = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/kurikulum/titimangsa', {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    if (response.success) {
      titimangsas.value = response.data.titimangsas
      kurikulums.value = response.data.kurikulums
      referensiPeriode.value = response.data.referensi_periode || []
      tahunAktif.value = response.data.tahun_aktif
    }
  } catch (error) {
    console.error('Error fetching titimangsa:', error)
  }
}

const editData = (item) => {
  isEdit.value = true
  form.value = { ...item, target_tingkat_arr: item.target_tingkat ? item.target_tingkat.split(',').map(s => s.trim()) : [] }
  if (!isDesktop.value) activeTab.value = 'form'
}

const resetForm = () => {
  isEdit.value = false
  form.value = {
    id: null,
    kurikulum_id: kurikulums.value.length > 0 ? kurikulums.value[0].id : '',
    nama_periode: '',
    target_tingkat_arr: ['X', 'XI', 'XII'],
    tempat_cetak: 'Bekasi',
    tanggal_cetak: ''
  }
}

const saveData = async () => {
  if (form.value.target_tingkat_arr.length === 0) {
      useSwal().toast("Pilih minimal satu tingkat (X, XI, atau XII)!", "error")
      return
  }

  isSaving.value = true
  try {
    const token = useCookie('auth_token').value
    const url = isEdit.value 
      ? `${import.meta.env.VITE_API_BASE_URL}/api/kurikulum/titimangsa/${form.value.id}`
      : import.meta.env.VITE_API_BASE_URL + '/api/kurikulum/titimangsa'
    
    const method = isEdit.value ? 'PUT' : 'POST'
    
    // Sort and join
    const sortedTingkat = [...form.value.target_tingkat_arr].sort((a,b) => {
        const order = { 'X': 1, 'XI': 2, 'XII': 3 }
        return order[a] - order[b]
    })
    const payload = {
        ...form.value,
        target_tingkat: sortedTingkat.join(',')
    }
    
    const response = await $fetch(url, {
      method,
      headers: { Authorization: `Bearer ${token}` },
      body: payload
    })
    
    if (response.success) {
      useSwal().toast(response.message, 'success')
      resetForm()
      fetchData()
      if (!isDesktop.value) activeTab.value = 'table'
    }
  } catch (error) {
    console.error('Error saving data:', error)
    useSwal().toast(error.response?._data?.message || 'Gagal menyimpan periode.', 'error')
  } finally {
    isSaving.value = false
  }
}

const toggleStatus = async (id) => {
  try {
    const token = useCookie('auth_token').value
    const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/kurikulum/titimangsa/${id}/toggle`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}` }
    })
    
    if (response.success) {
      fetchData()
    }
  } catch (error) {
    console.error('Error toggling status:', error)
  }
}

const confirmDelete = (item) => {
  deleteTarget.value = item
  isDeleteModalOpen.value = true
}

const executeDelete = async () => {
  if (!deleteTarget.value) return
  isSaving.value = true
  try {
    const token = useCookie('auth_token').value
    const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/kurikulum/titimangsa/${deleteTarget.value.id}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${token}` }
    })
    if (response.success) {
        isDeleteModalOpen.value = false
        useSwal().toast('Periode berhasil dihapus.', 'success')
        fetchData()
    }
  } catch (error) {
    console.error('Error deleting data:', error)
    useSwal().toast('Gagal menghapus data.', 'error')
  } finally {
    isSaving.value = false
  }
}

const { registerAutoSave, unregisterAutoSave } = useAutoSave()

onUnmounted(() => {
    unregisterAutoSave()
})

onMounted(() => {
  windowWidth.value = window.innerWidth
  window.addEventListener('resize', () => { windowWidth.value = window.innerWidth })
  
  if (isDesktop.value) {
      activeTab.value = 'form'
  } else {
      activeTab.value = 'table'
  }

  registerAutoSave(async () => {
      if (form.value.nama_periode && form.value.kurikulum_id) {
          await saveData()
      }
  })

  fetchData()
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-fadeIn {
  animation: fadeIn 0.3s ease-out forwards;
}

@keyframes slideUpFade {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-slideUpFade {
  animation: slideUpFade 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

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
