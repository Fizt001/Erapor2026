<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-1.5 flex gap-1.5 shadow-sm z-20">
        <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTabMobile = tab.id"
          :class="activeTabMobile === tab.id ? 'bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/20 ring-2 ring-blue-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="flex-1 rounded-lg flex flex-col items-center justify-center py-2 px-1 transition-all active:scale-95">
          <AppIcon :name="tab.icon" class="text-lg mb-0.5 transition-transform" :class="activeTabMobile === tab.id ? 'scale-110' : ''" />
          <span class="text-[10px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
        </button>
      </div>

      <!-- Panel Dock Kiri (Form) -->
      <div :class="['w-full xl:w-[380px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all', activeTabMobile === 'form' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[52px]' : '']">
        
        <div class="p-4 pb-2 shrink-0 z-10 relative">
          <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-4 border border-blue-500 shadow-sm relative overflow-hidden flex items-center gap-3">
            <div class="w-8 h-8 flex items-center justify-center shrink-0 bg-white/10 rounded-lg relative z-10 text-white"><AppIcon name="clipboard-document-check" class="w-5 h-5" /></div>
            <div class="relative z-10">
                <h3 class="text-xs font-black uppercase tracking-widest text-white">{{ isEditing ? 'Update / Evaluasi' : 'Jadwal Baru' }}</h3>
                <p class="text-[10px] text-blue-100 font-semibold uppercase mt-0.5">{{ isEditing ? 'Berikan Penilaian' : 'Buat Supervisi Guru' }}</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-15 text-white pointer-events-none">
              <svg class="w-16 h-16 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"></path></svg>
            </div>
          </div>
        </div>
        
        <div class="flex-1 overflow-y-auto custom-scrollbar p-4 pb-6">
            <form @submit.prevent="saveData" class="space-y-4">
                
                <div v-if="!isEditing">
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pilih Guru</label>
                    <select v-model="formData.guru_id" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all text-sm font-bold text-slate-800 outline-none appearance-none">
                        <option value="">-- Pilih Guru --</option>
                        <option v-for="g in gurus?.data || []" :key="g.id" :value="g.id">{{ g.name }}</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Tanggal</label>
                        <input type="date" v-model="formData.tanggal" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all text-sm font-bold text-slate-800 outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Waktu (Jam)</label>
                        <input type="time" v-model="formData.waktu" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all text-sm font-bold text-slate-800 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Persiapan / Keterangan</label>
                    <textarea v-model="formData.keterangan" rows="2" required placeholder="Contoh: RPP, Silabus, dan Media..." class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all text-sm font-bold text-slate-800 placeholder:text-slate-400 outline-none resize-none"></textarea>
                </div>
                
                <template v-if="isEditing">
                  <div class="pt-2">
                    <div class="flex items-center gap-2 mb-3">
                      <div class="h-px bg-slate-200 flex-1"></div>
                      <span class="text-[9px] font-black uppercase tracking-widest text-blue-500 bg-blue-50 px-2 py-1 rounded-lg">Hasil Supervisi</span>
                      <div class="h-px bg-slate-200 flex-1"></div>
                    </div>
                    
                    <div class="space-y-4">
                      <div>
                          <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Evaluasi / Penilaian</label>
                          <textarea v-model="formData.evaluasi" rows="2" placeholder="Tuliskan evaluasi di sini..." class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all text-sm font-bold text-slate-800 placeholder:text-slate-400 outline-none resize-none"></textarea>
                      </div>

                      <div>
                          <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Tindak Lanjut</label>
                          <textarea v-model="formData.tindak_lanjut" rows="2" placeholder="Catatan tindak lanjut..." class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all text-sm font-bold text-slate-800 placeholder:text-slate-400 outline-none resize-none"></textarea>
                      </div>

                      <div>
                          <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Status</label>
                          <select v-model="formData.status" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all text-sm font-bold text-slate-800 outline-none appearance-none">
                              <option value="Terjadwal">Terjadwal</option>
                              <option value="Selesai">Selesai</option>
                              <option value="Dibatalkan">Dibatalkan</option>
                          </select>
                      </div>
                    </div>
                  </div>
                </template>

                <div class="pt-4 border-t border-slate-100 flex gap-3">
                    <button v-if="isEditing" type="button" @click="resetForm" class="flex-1 py-3 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest border border-rose-200">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSaving" class="flex-[2] py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-bold rounded-2xl shadow-lg shadow-blue-500/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 text-xs uppercase tracking-widest">
                        <span v-if="isSaving" class="animate-spin"><AppIcon name="clock" class="w-6 h-6" /></span>
                        <span v-else>{{ isEditing ? '💾' : '➕' }}</span> 
                        {{ isEditing ? 'Simpan' : 'Tambah' }}
                    </button>
                </div>
            </form>
        </div>
      </div>

      <!-- Panel Flow Kanan (Tabel) -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative', activeTabMobile === 'table' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[52px]' : '']">
        <div class="p-0 sm:pt-3 sm:pb-6 sm:px-6 lg:pt-3 lg:pb-8 lg:px-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-none sm:rounded-[2rem] shadow-[0_2px_15px_-3px_rgba(0,0,0,0.05)] overflow-hidden flex flex-col flex-1 relative min-h-0 border-0 sm:border sm:border-slate-200/60">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-row justify-between items-center gap-2 shrink-0 z-10 bg-white">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-2xl bg-blue-50 shadow-sm border border-blue-100 flex items-center justify-center text-xl hidden sm:flex text-blue-500"><AppIcon name="clipboard-document-list" class="w-6 h-6" /></div>
                <div>
                    <h3 class="text-[11px] sm:text-sm font-black uppercase tracking-widest text-blue-700">Jadwal & Riwayat Supervisi</h3>
                    <p class="text-[10px] sm:text-[10px] font-bold text-slate-400 uppercase mt-0.5">Pantau kinerja guru</p>
                </div>
            </div>
            <button @click="fetchData" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-slate-200 hover:text-slate-700 font-bold transition-colors shrink-0" title="Refresh">
                <AppIcon name="arrow-path" class="w-5 h-5" />
            </button>
        </div>

        <!-- Table Container -->
        <div class="flex-1 overflow-y-auto custom-scrollbar relative bg-white">
            <!-- Loading State -->
            <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60">
                <div class="w-8 h-8 border-4 border-blue-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                <span class="text-xs font-black text-blue-500 uppercase tracking-widest">Memuat Data...</span>
            </div>

            <!-- Table Content -->
            <table v-else class="w-full text-left border-collapse min-w-full">
                <thead class="hidden sm:table-header-group sticky top-0 z-10 bg-slate-50 border-b border-slate-200 shadow-sm">
                    <tr class="text-[10px] uppercase tracking-widest font-black text-slate-500">
                        <th class="py-3 px-4 w-16 text-center">No</th>
                        <th class="py-3 px-4">Informasi Jadwal</th>
                        <th class="py-3 px-4">Evaluasi / Keterangan</th>
                        <th class="py-3 px-4 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="flex flex-col sm:table-row-group text-sm font-medium text-slate-700 divide-y divide-slate-100">
                    <tr v-if="!supervisiList || supervisiList.length === 0">
                        <td colspan="4" class="p-16 text-center text-slate-400 font-bold bg-white">
                            <span class="text-4xl block mb-2 opacity-30"><AppIcon name="calendar-days" class="w-6 h-6" /></span>
                            Belum ada jadwal supervisi.
                        </td>
                    </tr>
                     <tr v-for="(item, index) in supervisiList" :key="item.id" class="border-b border-slate-100 hover:bg-slate-50/80 transition-colors bg-white group flex flex-col sm:table-row p-4 sm:p-0 relative">
                        <td class="px-0 py-1 sm:p-4 text-left sm:text-center text-[11px] font-bold text-slate-400 flex sm:table-cell items-center justify-between mb-2 sm:mb-0">
                            <span class="sm:hidden text-[10px] font-black uppercase tracking-widest text-slate-400">Nomor</span>
                            <span>{{ index + 1 }}</span>
                        </td>
                        <td class="px-0 py-1 sm:p-4 flex flex-col sm:table-cell mb-2 sm:mb-0 w-full gap-1 align-top">
                            <div class="flex items-start justify-between w-full">
                                <div>
                                    <h4 class="font-black text-slate-800 text-xs sm:text-sm">{{ item.guru?.name }}</h4>
                                    <div class="flex items-center gap-2 mt-1">
                                      <span class="text-[10px] font-bold text-slate-500 uppercase"><AppIcon name="calendar" class="w-3 h-3 inline pb-0.5"/> {{ formatDate(item.tanggal) }}</span>
                                      <span class="text-[10px] font-bold text-slate-500"><AppIcon name="clock" class="w-3 h-3 inline pb-0.5"/> {{ item.waktu }}</span>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest border"
                                  :class="{
                                      'bg-blue-100 text-blue-700 border-blue-200': item.status === 'Terjadwal',
                                      'bg-emerald-100 text-emerald-700 border-emerald-200': item.status === 'Selesai',
                                      'bg-slate-100 text-slate-700 border-slate-200': item.status === 'Dibatalkan'
                                  }">
                                  {{ item.status }}
                                </span>
                            </div>
                        </td>
                        <td class="px-0 py-1 sm:p-4 flex flex-col sm:table-cell mb-2 sm:mb-0 w-full gap-2 align-top">
                             <div class="text-[10px] text-slate-600 mb-1">
                                <span class="font-bold text-slate-400 uppercase tracking-widest block mb-0.5 text-[9px]">Persiapan:</span> 
                                {{ item.keterangan || '-' }}
                            </div>
                            <div v-if="item.evaluasi" class="mt-2 text-[10px] text-slate-600 bg-blue-50/50 p-2 rounded-lg border border-blue-100">
                                <span class="font-bold text-blue-400 uppercase tracking-widest block mb-0.5 text-[9px]">Evaluasi:</span> 
                                {{ item.evaluasi }}
                            </div>
                            <div v-if="item.tindak_lanjut" class="mt-1 text-[10px] text-slate-500 border-l-2 border-emerald-200 pl-2">
                                <span class="font-bold text-emerald-400 uppercase tracking-widest block mb-0.5 text-[9px]">Tindak Lanjut:</span>
                                {{ item.tindak_lanjut }}
                            </div>
                        </td>
                        <td class="px-0 pt-2 sm:p-4 text-center border-t sm:border-0 border-slate-50 mt-2 sm:mt-0 flex sm:table-cell justify-end sm:justify-center w-full sm:w-24 align-middle">
                            <div class="flex items-center justify-end sm:justify-center gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity w-full">
                                <button @click="editData(item)" class="w-8 h-8 rounded-xl sm:rounded-lg bg-white border border-slate-200 text-slate-400 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-600 flex items-center justify-center transition-all shadow-sm" :title="item.status === 'Selesai' ? 'Lihat' : 'Edit / Evaluasi'"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg></button>
                                <button @click="confirmDelete(item)" class="w-8 h-8 rounded-xl sm:rounded-lg bg-white border border-slate-200 text-slate-400 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 flex items-center justify-center transition-all shadow-sm" title="Hapus"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
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

     <!-- Delete Modal -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade text-center">
            <div class="p-8">
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner border-4 border-white ring-4 ring-rose-50"><AppIcon name="exclamation-triangle" class="w-6 h-6" /></div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Hapus Jadwal?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Anda yakin ingin menghapus jadwal supervisi untuk:<br>
                    <span class="font-bold text-rose-600">{{ deleteTarget?.guru?.name }}</span>?
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

definePageMeta({
  layout: 'kepsek',
  middleware: 'kepsek',
  title: 'Supervisi Guru'
})

// Responsiveness detector
const windowWidth = ref(1024) 
const isDesktop = computed(() => windowWidth.value >= 1280) // xl breakpoint

// Tabs for Mobile
const activeTabMobile = ref('table')
const mobileTabs = [
  { id: 'form', title: 'Form Jadwal', icon: 'document-text' },
  { id: 'table', title: 'Data Supervisi', icon: 'clipboard' }
]

const supervisiList = ref([])
const gurus = ref({ data: [] })
const isLoading = ref(true)
const isSaving = ref(false)

const isEditing = ref(false)
const formData = ref({
    id: null,
    guru_id: '',
    tanggal: new Date().toISOString().split('T')[0],
    waktu: '08:00',
    keterangan: '',
    evaluasi: '',
    tindak_lanjut: '',
    status: 'Terjadwal'
})

const isDeleteModalOpen = ref(false)
const deleteTarget = ref(null)

const fetchData = async () => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        const [supervisiRes, gurusRes] = await Promise.all([
            $fetch('/api/kepsek/supervisi', { headers: { Authorization: `Bearer ${token}` } }),
            $fetch('/api/kepsek/supervisi/gurus', { headers: { Authorization: `Bearer ${token}` } })
        ])
        
        supervisiList.value = supervisiRes?.data || []
        gurus.value = gurusRes || { data: [] }
    } catch (error) {
        console.error('Failed to fetch data:', error)
    } finally {
        isLoading.value = false
    }
}

const saveData = async () => {
    isSaving.value = true
    const token = useCookie('auth_token').value
    const url = isEditing.value 
        ? `/api/kepsek/supervisi/${formData.value.id}` 
        : `/api/kepsek/supervisi`
    const method = isEditing.value ? 'PUT' : 'POST'

    try {
        await $fetch(url, {
            method: method,
            headers: { Authorization: `Bearer ${token}` },
            body: formData.value
        })
        
        useSwal().toast('Data supervisi berhasil disimpan!', 'success')
        resetForm()
        fetchData()
        if (!isDesktop.value) activeTabMobile.value = 'table'
    } catch (error) {
        console.error('Save error:', error)
        useSwal().toast(error.response?._data?.message || 'Gagal menyimpan data.', 'error')
    } finally {
        isSaving.value = false
    }
}

const editData = (item) => {
    isEditing.value = true
    formData.value = {
        id: item.id,
        guru_id: item.guru_id,
        tanggal: item.tanggal,
        waktu: item.waktu || '08:00',
        keterangan: item.keterangan || '',
        evaluasi: item.evaluasi || '',
        tindak_lanjut: item.tindak_lanjut || '',
        status: item.status
    }
    if (!isDesktop.value) activeTabMobile.value = 'form'
}

const resetForm = () => {
    isEditing.value = false
    formData.value = {
        id: null,
        guru_id: '',
        tanggal: new Date().toISOString().split('T')[0],
        waktu: '08:00',
        keterangan: '',
        evaluasi: '',
        tindak_lanjut: '',
        status: 'Terjadwal'
    }
}

const confirmDelete = (item) => {
    deleteTarget.value = item
    isDeleteModalOpen.value = true
}

const executeDelete = async () => {
    if(!deleteTarget.value) return
    isSaving.value = true
    const token = useCookie('auth_token').value
    try {
        await $fetch(`/api/kepsek/supervisi/${deleteTarget.value.id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        isDeleteModalOpen.value = false
        useSwal().toast('Jadwal berhasil dihapus!', 'success')
        fetchData()
    } catch (error) {
        console.error('Delete failed:', error)
        useSwal().toast('Gagal menghapus data.', 'error')
    } finally {
        isSaving.value = false
    }
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const d = new Date(dateString)
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}

onMounted(() => {
    windowWidth.value = window.innerWidth
    window.addEventListener('resize', () => { windowWidth.value = window.innerWidth })
    
    if (isDesktop.value) {
        activeTabMobile.value = 'form'
    } else {
        activeTabMobile.value = 'table'
    }

    fetchData()
})

onUnmounted(() => {
    window.removeEventListener('resize', () => {})
})
</script>

<style scoped>
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

@keyframes slideUpFade {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-slideUpFade { animation: slideUpFade 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
</style>
