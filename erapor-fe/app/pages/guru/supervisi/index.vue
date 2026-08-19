<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- MOBILE TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-1.5 flex gap-1.5 shadow-sm z-20">
        <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTabMobile = tab.id"
          :class="activeTabMobile === tab.id ? 'bg-gradient-to-br from-sky-500 to-blue-600 text-white shadow-md ring-2 ring-sky-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="flex-1 rounded-lg flex flex-col items-center justify-center py-2 px-1 transition-all active:scale-95">
          <AppIcon :name="tab.icon" class="text-lg mb-0.5" />
          <span class="text-[10px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
        </button>
      </div>

      <!-- PANEL KIRI: Daftar Jadwal -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all', activeTabMobile === 'list' || isDesktop ? 'flex' : 'hidden xl:flex', !isDesktop ? 'pt-[52px]' : '']">
        
        <div class="p-4 pb-2 shrink-0">
          <div class="bg-gradient-to-r from-sky-600 to-blue-700 rounded-2xl p-4 border border-sky-500 shadow-sm relative overflow-hidden flex items-center gap-3">
            <div class="w-8 h-8 flex items-center justify-center shrink-0 bg-white/10 rounded-lg relative z-10 text-white">
              <AppIcon name="calendar" class="w-5 h-5" />
            </div>
            <div class="relative z-10">
              <h3 class="text-xs font-black uppercase tracking-widest text-white">Jadwal Supervisi</h3>
              <p class="text-[10px] text-sky-100 font-semibold uppercase mt-0.5">Dari Kepala Sekolah</p>
            </div>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar p-0">
          <div v-if="pending" class="flex flex-col items-center justify-center h-full opacity-60 py-12">
            <div class="w-8 h-8 border-4 border-sky-400 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-[10px] font-black text-sky-500 uppercase tracking-widest">Memuat...</span>
          </div>
          <div v-else-if="!jadwalList || jadwalList.length === 0" class="flex flex-col items-center justify-center h-full opacity-50 p-6 text-center">
            <AppIcon name="calendar" class="w-12 h-12 text-slate-300 mb-3" />
            <p class="text-sm font-bold text-slate-500">Belum ada jadwal supervisi</p>
            <p class="text-xs text-slate-400 mt-1">Anda belum menerima jadwal dari Kepala Sekolah.</p>
          </div>
          <div v-else class="p-3 space-y-2">
            <button v-for="item in jadwalList" :key="item.id" type="button"
              @click="selectItem(item)"
              :class="selectedId === item.id ? 'ring-2 ring-sky-500 bg-sky-50 border-sky-200' : 'border-slate-200 bg-white hover:bg-sky-50 hover:border-sky-200'"
              class="w-full text-left p-3 rounded-xl border transition-all cursor-pointer">
              <div class="flex items-center justify-between gap-2 mb-1.5">
                <p class="text-xs font-black text-slate-800">{{ formatDate(item.tanggal) }}</p>
                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider shrink-0"
                  :class="{
                    'bg-sky-100 text-sky-700': item.status === 'Terjadwal',
                    'bg-emerald-100 text-emerald-700': item.status === 'Selesai',
                    'bg-slate-100 text-slate-500': item.status === 'Dibatalkan'
                  }">{{ item.status }}</span>
              </div>
              <p class="text-[11px] text-slate-500 font-medium">Jam: {{ item.waktu || '-' }}</p>
            </button>
          </div>
        </div>
      </div>

      <!-- PANEL KANAN: Detail -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative', activeTabMobile === 'detail' || isDesktop ? 'flex' : 'hidden xl:flex', !isDesktop ? 'pt-[52px]' : '']">
        <div class="p-0 sm:pt-3 sm:pb-6 sm:px-6 lg:pt-3 lg:pb-8 lg:px-8 max-w-5xl mx-auto w-full h-full flex flex-col">
          <div class="bg-white rounded-none sm:rounded-[2rem] shadow-sm overflow-hidden flex flex-col flex-1 relative min-h-0 border-0 sm:border sm:border-slate-200/60">
            
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between gap-2 shrink-0 bg-white">
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-500 hidden sm:flex">
                  <AppIcon name="information-circle" class="w-6 h-6" />
                </div>
                <div>
                  <h3 class="text-[11px] sm:text-sm font-black uppercase tracking-widest text-sky-700">Detail Supervisi</h3>
                  <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">
                    {{ selectedItem ? formatDate(selectedItem.tanggal) : 'Pilih jadwal dari panel kiri' }}
                  </p>
                </div>
              </div>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar p-6">
              <!-- Empty state -->
              <div v-if="!selectedItem" class="flex flex-col items-center justify-center h-full opacity-40 text-center py-20">
                <AppIcon name="calendar" class="w-16 h-16 text-slate-400 mb-4" />
                <p class="text-lg font-black text-slate-700">Pilih Jadwal</p>
                <p class="text-sm text-slate-500 mt-1">Pilih jadwal supervisi di panel kiri untuk melihat detailnya.</p>
              </div>

              <!-- Detail content -->
              <div v-else class="space-y-5">
                <!-- Info Jadwal -->
                <div class="bg-sky-50 border border-sky-200 rounded-2xl p-5">
                  <h4 class="text-[11px] font-black uppercase tracking-widest text-sky-700 mb-4 flex items-center gap-2">
                    <AppIcon name="calendar" class="w-4 h-4" /> Informasi Jadwal
                  </h4>
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <p class="text-[10px] font-black uppercase tracking-wider text-slate-500 mb-1">Tanggal</p>
                      <p class="text-sm font-bold text-slate-800">{{ formatDate(selectedItem.tanggal) }}</p>
                    </div>
                    <div>
                      <p class="text-[10px] font-black uppercase tracking-wider text-slate-500 mb-1">Waktu</p>
                      <p class="text-sm font-bold text-slate-800">{{ selectedItem.waktu || '-' }}</p>
                    </div>
                    <div class="col-span-2">
                      <p class="text-[10px] font-black uppercase tracking-wider text-slate-500 mb-1">Status</p>
                      <span class="inline-flex px-3 py-1 rounded-lg text-xs font-black uppercase tracking-wider"
                        :class="{
                          'bg-sky-100 text-sky-700': selectedItem.status === 'Terjadwal',
                          'bg-emerald-100 text-emerald-700': selectedItem.status === 'Selesai',
                          'bg-slate-100 text-slate-500': selectedItem.status === 'Dibatalkan'
                        }">{{ selectedItem.status }}</span>
                    </div>
                  </div>
                </div>

                <!-- Persiapan/Keterangan -->
                <div class="bg-white border border-slate-200 rounded-2xl p-5">
                  <h4 class="text-[11px] font-black uppercase tracking-widest text-slate-600 mb-3 flex items-center gap-2">
                    <AppIcon name="information-circle" class="w-4 h-4" /> Persiapan / Keterangan
                  </h4>
                  <p class="text-sm text-slate-700 whitespace-pre-wrap leading-relaxed">{{ selectedItem.keterangan || 'Menunggu arahan dari Kepala Sekolah.' }}</p>
                </div>

                <!-- Evaluasi & Tindak Lanjut (hanya jika Selesai) -->
                <div v-if="selectedItem.status === 'Selesai'" class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5">
                  <h4 class="text-[11px] font-black uppercase tracking-widest text-emerald-700 mb-4 flex items-center gap-2">
                    <AppIcon name="check-badge" class="w-4 h-4" /> Evaluasi & Tindak Lanjut
                  </h4>
                  <div class="space-y-4">
                    <div>
                      <p class="text-[10px] font-black uppercase tracking-wider text-slate-500 mb-1">Evaluasi Kepsek</p>
                      <p class="text-sm text-slate-700 whitespace-pre-wrap leading-relaxed">{{ selectedItem.evaluasi || '-' }}</p>
                    </div>
                    <div>
                      <p class="text-[10px] font-black uppercase tracking-wider text-slate-500 mb-1">Tindak Lanjut</p>
                      <p class="text-sm text-slate-700 whitespace-pre-wrap leading-relaxed">{{ selectedItem.tindak_lanjut || '-' }}</p>
                    </div>
                  </div>
                </div>

                <!-- Pesan jika masih Terjadwal -->
                <div v-if="selectedItem.status === 'Terjadwal'" class="bg-amber-50 border border-amber-200 rounded-2xl p-5 flex items-start gap-3">
                  <AppIcon name="information-circle" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" />
                  <div>
                    <p class="text-sm font-black text-amber-800">Persiapkan diri Anda</p>
                    <p class="text-xs text-amber-700 mt-1">Supervisi dijadwalkan pada <strong>{{ formatDate(selectedItem.tanggal) }}</strong> jam <strong>{{ selectedItem.waktu }}</strong>. Pastikan RPP dan perangkat pembelajaran sudah siap.</p>
                  </div>
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
import { ref, computed, onMounted, onUnmounted } from 'vue'

definePageMeta({ middleware: 'guru', layout: 'guru', title: 'Jadwal Supervisi' })

const windowWidth = ref(1024)
const isDesktop = computed(() => windowWidth.value >= 1280)
const activeTabMobile = ref('list')
const mobileTabs = [
  { id: 'list', title: 'Daftar Jadwal', icon: 'calendar' },
  { id: 'detail', title: 'Detail', icon: 'information-circle' }
]
onMounted(() => {
  windowWidth.value = window.innerWidth
  window.addEventListener('resize', () => windowWidth.value = window.innerWidth)
})
onUnmounted(() => {
  window.removeEventListener('resize', () => windowWidth.value = window.innerWidth)
})

const tokenCookie = useCookie('auth_token')
const jadwalList = ref([])
const pending = ref(true)
const selectedId = ref(null)
const selectedItem = ref(null)

const selectItem = (item) => {
  selectedId.value = item.id
  selectedItem.value = item
  if (!isDesktop.value) activeTabMobile.value = 'detail'
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' })
}

onMounted(async () => {
  try {
    const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/guru/supervisi', {
      headers: { Authorization: `Bearer ${tokenCookie.value}` }
    })
    jadwalList.value = res?.data || []
    if (jadwalList.value.length > 0) {
      selectedItem.value = jadwalList.value[0]
      selectedId.value = jadwalList.value[0].id
    }
  } catch (e) {
    console.error(e)
  } finally {
    pending.value = false
  }
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
