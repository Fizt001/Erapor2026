<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-1.5 flex gap-1.5 shadow-sm z-20">
        <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTabMobile = tab.id"
          :class="activeTabMobile === tab.id ? 'bg-gradient-to-br from-purple-500 to-purple-600 text-white shadow-md shadow-purple-500/20 ring-2 ring-purple-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="flex-1 rounded-lg flex flex-col items-center justify-center py-2 px-1 transition-all active:scale-95">
          <AppIcon :name="tab.icon" class="text-lg mb-0.5 transition-transform" :class="activeTabMobile === tab.id ? 'scale-110' : ''" />
          <span class="text-[10px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
        </button>
      </div>

      <!-- Panel Dock Kiri (Aksi / Tindakan) -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all', activeTabMobile === 'form' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[52px]' : '']">
        
        <div class="p-4 pb-2 shrink-0 z-10 relative">
          <div class="bg-gradient-to-r from-purple-600 to-fuchsia-600 rounded-2xl p-4 border border-purple-500 shadow-sm relative overflow-hidden flex items-center gap-3">
            <div class="w-8 h-8 flex items-center justify-center shrink-0 bg-white/10 rounded-lg relative z-10 text-white"><AppIcon name="shield-exclamation" class="w-5 h-5" /></div>
            <div class="relative z-10">
                <h3 class="text-xs font-black uppercase tracking-widest text-white">Tindakan Kepsek</h3>
                <p class="text-[10px] text-purple-100 font-semibold uppercase mt-0.5">Evaluasi & Pemanggilan</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-15 text-white pointer-events-none">
              <svg class="w-16 h-16 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm1 14h-2v-2h2v2zm0-4h-2V8h2v4z"></path></svg>
            </div>
          </div>
        </div>
        
        <div class="flex-1 overflow-y-auto custom-scrollbar p-4 pb-6 flex flex-col">
          <div v-if="!selectedItem" class="flex-1 flex flex-col items-center justify-center text-center opacity-50 p-6">
            <AppIcon name="cursor-arrow-rays" class="w-12 h-12 mb-3 text-slate-400" />
            <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Pilih kasus guru dari tabel untuk melihat detail dan melakukan tindakan</p>
          </div>
          <div v-else class="space-y-4">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
              <h4 class="font-black text-slate-800 text-sm mb-1">{{ selectedItem.guru?.name }}</h4>
              <div class="flex items-center gap-2 mb-3">
                <span class="text-[10px] font-bold text-slate-500 uppercase">Total Pelanggaran:</span>
                <span class="px-2 py-0.5 rounded font-black text-xs" :class="getCaseCount(selectedItem.guru_id) >= 3 ? 'bg-rose-100 text-rose-700' : 'bg-slate-200 text-slate-700'">{{ getCaseCount(selectedItem.guru_id) }} Kasus</span>
              </div>
              <div class="h-px bg-slate-200 w-full mb-3"></div>
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Kasus Terbaru:</p>
              <p class="text-xs text-slate-600 font-medium leading-relaxed">{{ selectedItem.kasus }}</p>
              <p class="text-[10px] text-slate-400 mt-2">{{ formatDate(selectedItem.tanggal) }}</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4">
              <h4 class="text-[11px] font-black text-slate-500 uppercase tracking-widest mb-3">Tindakan Kepala Sekolah</h4>
              <div v-if="getCaseCount(selectedItem.guru_id) >= 3">
                <p class="text-xs text-slate-600 mb-4">Guru ini telah memiliki <strong class="text-rose-600">{{ getCaseCount(selectedItem.guru_id) }} kasus</strong>. Berdasarkan kebijakan, Kepala Sekolah berhak mengeluarkan panggilan peringatan/tindak lanjut.</p>
                <button 
                  @click="panggilGuru(selectedItem.guru_id, selectedItem.guru?.name)" 
                  class="w-full py-3 bg-gradient-to-r from-rose-500 to-rose-600 text-white font-bold rounded-xl shadow-lg shadow-rose-500/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 text-xs uppercase tracking-widest"
                  :disabled="isCalling === selectedItem.guru_id"
                >
                  <span v-if="isCalling === selectedItem.guru_id" class="animate-spin"><AppIcon name="clock" class="w-5 h-5" /></span>
                  <span v-else>⚠️</span> 
                  {{ isCalling === selectedItem.guru_id ? 'Memproses...' : 'Kirim Panggilan' }}
                </button>
              </div>
              <div v-else class="text-center p-4 bg-slate-50 rounded-xl border border-slate-100">
                <p class="text-xs text-slate-500 font-medium">Belum memenuhi syarat untuk pemanggilan (minimal 3 kasus).</p>
              </div>
            </div>

            <button @click="selectedItem = null" class="w-full py-2.5 mt-2 text-slate-500 hover:bg-slate-100 font-bold rounded-xl transition-all text-xs uppercase tracking-widest border border-transparent hover:border-slate-200">
              Tutup Detail
            </button>
          </div>
        </div>
      </div>

      <!-- Panel Flow Kanan (Tabel) -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative', activeTabMobile === 'table' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[52px]' : '']">
        <div class="p-0 sm:pt-3 sm:pb-6 sm:px-6 lg:pt-3 lg:pb-8 lg:px-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-none sm:rounded-[2rem] shadow-[0_2px_15px_-3px_rgba(0,0,0,0.05)] overflow-hidden flex flex-col flex-1 relative min-h-0 border-0 sm:border sm:border-slate-200/60">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-row justify-between items-center gap-2 shrink-0 z-10 bg-white">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-2xl bg-purple-50 shadow-sm border border-purple-100 flex items-center justify-center text-xl hidden sm:flex text-purple-500"><AppIcon name="users" class="w-6 h-6" /></div>
                <div>
                    <h3 class="text-[11px] sm:text-sm font-black uppercase tracking-widest text-purple-700">Database Kasus Guru</h3>
                    <p class="text-[10px] sm:text-[10px] font-bold text-slate-400 uppercase mt-0.5">Pemantauan kasus & teguran</p>
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
                <div class="w-8 h-8 border-4 border-purple-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                <span class="text-xs font-black text-purple-500 uppercase tracking-widest">Memuat Data...</span>
            </div>

            <!-- Table Content -->
            <table v-else class="w-full text-left border-collapse min-w-full">
                <thead class="hidden sm:table-header-group sticky top-0 z-10 bg-slate-50 border-b border-slate-200 shadow-sm">
                    <tr class="text-[10px] uppercase tracking-widest font-black text-slate-500">
                        <th class="py-3 px-4 w-16 text-center">No</th>
                        <th class="py-3 px-4">Informasi Guru</th>
                        <th class="py-3 px-4 text-center">Total</th>
                        <th class="py-3 px-4">Kasus Terbaru</th>
                        <th class="py-3 px-4 text-center w-28">Status</th>
                        <th class="py-3 px-4 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="flex flex-col sm:table-row-group text-sm font-medium text-slate-700 divide-y divide-slate-100">
                    <tr v-if="!data?.data || data.data.length === 0">
                        <td colspan="6" class="p-16 text-center text-slate-400 font-bold bg-white">
                            <span class="text-4xl block mb-2 opacity-30"><AppIcon name="check-badge" class="w-6 h-6 inline" /></span>
                            Belum ada data kasus guru yang dilaporkan.
                        </td>
                    </tr>
                     <tr v-for="(item, index) in data?.data || []" :key="item.id" class="border-b border-slate-100 hover:bg-slate-50/80 transition-colors bg-white group flex flex-col sm:table-row p-4 sm:p-0 relative" :class="selectedItem?.id === item.id ? 'bg-purple-50/50' : ''">
                        <td class="px-0 py-1 sm:p-4 text-left sm:text-center text-[11px] font-bold text-slate-400 flex sm:table-cell items-center justify-between mb-2 sm:mb-0">
                            <span class="sm:hidden text-[10px] font-black uppercase tracking-widest text-slate-400">Nomor</span>
                            <span>{{ index + 1 }}</span>
                        </td>
                        <td class="px-0 py-1 sm:p-4 flex flex-col sm:table-cell mb-2 sm:mb-0">
                            <h4 class="font-black text-slate-800 text-xs sm:text-sm">{{ item.guru?.name }}</h4>
                            <p class="text-[10px] font-bold text-slate-500 uppercase mt-0.5">{{ formatDate(item.tanggal) }}</p>
                        </td>
                        <td class="px-0 py-1 sm:p-4 text-left sm:text-center flex sm:table-cell items-center justify-between mb-2 sm:mb-0">
                             <span class="sm:hidden text-[10px] font-black uppercase tracking-widest text-slate-400">Total Kasus</span>
                             <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full font-black text-[10px]"
                                :class="getCaseCount(item.guru_id) >= 3 ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600'">
                                {{ getCaseCount(item.guru_id) }}x
                              </span>
                        </td>
                        <td class="px-0 py-1 sm:p-4 text-xs text-slate-600 max-w-xs truncate flex sm:table-cell mb-2 sm:mb-0">
                            <span class="sm:hidden text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-1">Kasus Terbaru</span>
                            {{ item.kasus }}
                        </td>
                        <td class="px-0 py-1 sm:p-4 text-left sm:text-center flex sm:table-cell items-center justify-between mb-2 sm:mb-0">
                             <span class="sm:hidden text-[10px] font-black uppercase tracking-widest text-slate-400">Status</span>
                              <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest border"
                                :class="{
                                  'bg-rose-100 text-rose-700 border-rose-200': item.status === 'Terbuka',
                                  'bg-amber-100 text-amber-700 border-amber-200': item.status === 'Ditangani',
                                  'bg-emerald-100 text-emerald-700 border-emerald-200': item.status === 'Selesai'
                                }">
                                {{ item.status }}
                              </span>
                        </td>
                        <td class="px-0 pt-2 sm:p-4 text-center border-t sm:border-0 border-slate-50 mt-2 sm:mt-0 flex sm:table-cell justify-end sm:justify-center">
                            <div class="flex items-center justify-end sm:justify-center gap-2">
                                <button @click="selectData(item)" class="px-3 py-1.5 rounded-lg font-bold text-xs uppercase tracking-widest border transition-all" :class="selectedItem?.id === item.id ? 'bg-purple-600 text-white border-purple-600 shadow-md' : 'bg-white text-purple-600 border-purple-200 hover:bg-purple-50'">
                                    Tinjau
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

definePageMeta({
  layout: 'kepsek',
  middleware: 'kepsek',
  title: 'Pemantauan Kasus Guru'
})

// Responsiveness detector
const windowWidth = ref(1024) 
const isDesktop = computed(() => windowWidth.value >= 1280) // xl breakpoint

// Tabs for Mobile
const activeTabMobile = ref('table')
const mobileTabs = [
  { id: 'form', title: 'Tindakan', icon: 'shield-exclamation' },
  { id: 'table', title: 'Database Kasus', icon: 'clipboard' }
]

const data = ref({ data: [] })
const isLoading = ref(true)
const isCalling = ref(null)
const selectedItem = ref(null)

const fetchData = async () => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/kepsek/kasus-guru', { headers: { Authorization: `Bearer ${token}` } })
        data.value = res || { data: [] }
    } catch (error) {
        console.error('Failed to fetch data:', error)
    } finally {
        isLoading.value = false
    }
}

const getCaseCount = (guruId) => {
  return data.value?.guru_cases_count?.[guruId] || 0
}

const selectData = (item) => {
    selectedItem.value = item
    if (!isDesktop.value) activeTabMobile.value = 'form'
}

const panggilGuru = async (guruId, guruName) => {
  const isConfirmed = await useSwal().confirm(
    'Kirim Panggilan Resmi?',
    `Apakah Anda yakin ingin mengirim notifikasi panggilan ke dashboard ${guruName}?`,
    'warning',
    'Ya, Kirim Panggilan'
  )
  if (isConfirmed) {
    isCalling.value = guruId
    try {
      await $fetch(import.meta.env.VITE_API_BASE_URL + `/api/kepsek/kasus-guru/${guruId}/panggil`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${useCookie('auth_token').value}`
        }
      })
      useSwal().toast('Panggilan berhasil dikirim!', 'success')
      fetchData()
    } catch (err) {
      useSwal().toast('Gagal mengirim panggilan.', 'error')
    } finally {
      isCalling.value = null
    }
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
        activeTabMobile.value = 'table'
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
