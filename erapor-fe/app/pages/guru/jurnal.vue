<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex flex-col xl:flex-row overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-1.5 flex gap-1.5 z-20 shadow-sm">
        <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTabMobile = tab.id"
          :class="activeTabMobile === tab.id ? 'bg-gradient-to-br from-sky-500 to-sky-600 text-white shadow-md shadow-sky-500/20 ring-2 ring-sky-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="flex-1 rounded-lg flex flex-col items-center justify-center py-1.5 px-0.5 transition-all active:scale-95">
          <AppIcon :name="tab.icon" class="text-lg mb-0.5 transition-transform" :class="activeTabMobile === tab.id ? 'scale-110' : ''" />
          <span class="text-[8px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
        </button>
      </div>

      <!-- Panel Dock Kiri -->
      <div class="w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] overflow-y-auto custom-scrollbar transition-all pt-[60px] xl:pt-0" :class="activeTabMobile === 'dock' ? 'block' : 'hidden xl:flex'">
        
        <div class="p-4 pb-2 space-y-4">
          <!-- Widget Title -->
          <div class="bg-gradient-to-r from-sky-600 to-blue-700 rounded-2xl p-4 border border-sky-500 shadow-sm relative overflow-hidden flex items-center gap-3">
            <div class="w-8 h-8 flex items-center justify-center shrink-0 bg-white/10 rounded-lg relative z-10 text-white"><AppIcon name="book-open" class="w-5 h-5" /></div>
            <div class="relative z-10">
                <h3 class="text-xs font-black uppercase tracking-widest text-white">Jurnal Mengajar</h3>
                <p class="text-[9px] text-sky-100 font-semibold uppercase mt-0.5">Rekapitulasi Absensi</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-15 text-white pointer-events-none">
              <svg class="w-16 h-16 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            </div>
          </div>
          
          <!-- Semester Tabs in Dock -->
          <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pilih Semester</label>
              <div class="bg-slate-100 p-1.5 rounded-xl flex shadow-inner">
                  <button @click="activeSemester = 'ganjil'" class="flex-1 py-2 text-xs font-black uppercase tracking-widest rounded-lg transition-all" :class="activeSemester === 'ganjil' ? 'bg-white text-sky-600 shadow-sm ring-1 ring-slate-200/50' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-200/50'">Ganjil</button>
                  <button @click="activeSemester = 'genap'" class="flex-1 py-2 text-xs font-black uppercase tracking-widest rounded-lg transition-all" :class="activeSemester === 'genap' ? 'bg-white text-sky-600 shadow-sm ring-1 ring-slate-200/50' : 'text-slate-400 hover:text-slate-600 hover:bg-slate-200/50'">Genap</button>
              </div>
          </div>
        </div>

        <div class="flex-1 p-4 bg-slate-50 border-t border-slate-200 overflow-y-auto custom-scrollbar flex flex-col relative">
          <!-- TOTAL SEMESTER CARD -->
          <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3 sticky top-0 bg-slate-50 z-10 py-1">Akumulasi {{ activeSemester === 'ganjil' ? 'Ganjil' : 'Genap' }}</div>
          
          <div v-if="isLoading" class="flex-1 flex flex-col items-center justify-center text-slate-400 py-10">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-sky-500 mb-3"></div>
            <span class="text-[10px] font-black uppercase tracking-widest">Memuat...</span>
          </div>
          <div v-else-if="!jurnalData" class="flex-1 flex flex-col items-center justify-center py-10 opacity-50">
             <AppIcon name="exclamation-circle" class="w-10 h-10 text-slate-400 mb-2" />
             <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Gagal memuat data</p>
          </div>
          <div v-else-if="Object.keys(activeData.total || {}).length === 0" class="flex-1 flex flex-col items-center justify-center py-10 opacity-50">
            <AppIcon name="folder-open" class="w-10 h-10 text-slate-400 mb-2" />
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Belum ada rekap</p>
          </div>
          <div v-else class="space-y-2">
            <div v-for="(pertemuans, mapelKelas) in activeData.total" :key="mapelKelas" class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col hover:border-sky-200 transition-colors">
              <div class="flex items-start justify-between mb-2">
                <span class="text-[11px] font-bold text-slate-700 leading-tight pr-2">{{ mapelKelas }}</span>
                <span class="bg-sky-100 text-sky-700 text-[10px] font-black px-2 py-0.5 rounded shadow-sm shrink-0">
                  {{ pertemuans.length }}x
                </span>
              </div>
              <button @click="showDetail('Semester ' + (activeSemester === 'ganjil' ? 'Ganjil' : 'Genap'), mapelKelas, pertemuans)" class="w-full py-1.5 bg-slate-50 hover:bg-sky-50 text-slate-500 hover:text-sky-600 rounded-lg text-[9px] font-black uppercase tracking-widest transition-colors flex items-center justify-center">
                <AppIcon name="eye" class="w-3.5 h-3.5 mr-1" /> Rincian
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative transition-all pt-[60px] xl:pt-0" :class="activeTabMobile === 'flow' ? 'flex' : 'hidden xl:flex'">
        <div class="p-2 lg:p-6 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          
          <div v-if="isLoading" class="flex-grow flex flex-col items-center justify-center p-20 opacity-60 bg-white rounded-3xl shadow-sm border border-slate-200/60">
            <div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Memuat Data...</span>
          </div>

          <div v-else-if="jurnalData" class="flex-1 flex flex-col bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden min-h-0 relative">
            <div class="px-6 py-5 border-b border-slate-200 bg-white shrink-0 z-10 flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center border border-sky-100"><AppIcon name="calendar" class="w-5 h-5" /></div>
                <div>
                  <h3 class="text-[13px] font-black leading-none uppercase tracking-wide text-slate-800">Rincian Bulanan</h3>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Daftar Pertemuan Per Bulan</p>
                </div>
              </div>
            </div>

            <!-- List Bulanan -->
            <div class="flex-1 overflow-y-auto custom-scrollbar p-6 bg-slate-50/50">
              <div class="space-y-6">
                <div v-for="bulan in activeBulanList" :key="bulan.code" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                  <div class="bg-slate-50 border-b border-slate-100 px-5 py-4 flex items-center justify-between">
                    <h3 class="font-black text-slate-700 text-[11px] uppercase tracking-widest flex items-center">
                      <AppIcon name="calendar" class="w-4 h-4 mr-2 text-sky-500" /> {{ bulan.name }}
                    </h3>
                    <span class="bg-white border border-slate-200 text-slate-500 text-[9px] px-2.5 py-0.5 rounded font-bold shadow-sm uppercase tracking-widest">
                      {{ countKeys(activeData.bulanan[bulan.code]) }} Kelas/Mapel
                    </span>
                  </div>
                  
                  <div class="p-0">
                    <div v-if="Object.keys(activeData.bulanan[bulan.code] || {}).length === 0" class="text-center py-8 text-slate-400 bg-white">
                      <div class="flex justify-center mb-2 opacity-50 grayscale text-slate-400"><AppIcon name="moon" class="w-6 h-6" /></div>
                      <p class="text-[10px] font-black uppercase tracking-widest">Tidak ada pertemuan</p>
                    </div>
                    
                    <div v-else class="overflow-x-auto custom-scrollbar">
                      <table class="w-full text-left border-collapse min-w-[500px]">
                        <thead>
                          <tr class="bg-white text-[9px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">
                            <th class="px-5 py-3 w-[60px] text-center border-r border-slate-100">No</th>
                            <th class="px-5 py-3 border-r border-slate-100">Rombongan Belajar / Mapel</th>
                            <th class="px-5 py-3 w-[120px] text-center border-r border-slate-100">Total</th>
                            <th class="px-5 py-3 w-[100px] text-center">Aksi</th>
                          </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                          <tr v-for="(pertemuans, mapelKelas, idx) in activeData.bulanan[bulan.code]" :key="mapelKelas" class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3 text-center border-r border-slate-100 text-[11px] font-bold text-slate-400">{{ idx + 1 }}</td>
                            <td class="px-5 py-3 border-r border-slate-100">
                              <p class="text-[11px] font-bold text-slate-700">{{ mapelKelas }}</p>
                            </td>
                            <td class="px-5 py-3 text-center border-r border-slate-100">
                              <span class="inline-flex items-center justify-center bg-sky-100 text-sky-700 text-[10px] font-black px-2 py-0.5 rounded shadow-sm">
                                {{ pertemuans.length }}x
                              </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                              <button @click="showDetail(bulan.name, mapelKelas, pertemuans)" class="text-sky-500 hover:text-sky-600 hover:bg-sky-50 transition-colors px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest" title="Lihat Rincian Tanggal">
                                Rincian
                              </button>
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
    </div>

    <!-- MODAL DETAIL -->
    <div v-if="detailModal.show" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="detailModal.show = false"></div>
      <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-4xl relative z-10 flex flex-col max-h-[85vh] overflow-hidden transform transition-all">
        <div class="bg-slate-50 border-b border-slate-200 px-5 py-4 flex items-center justify-between">
          <div>
            <h3 class="font-black text-slate-800 text-sm flex items-center uppercase tracking-widest"><AppIcon name="magnifying-glass" class="w-4 h-4 mr-2 text-slate-600" /> Rincian Pertemuan</h3>
            <p class="text-[10px] text-slate-500 font-bold mt-0.5 uppercase tracking-widest">{{ detailModal.mapelKelas }} • {{ detailModal.bulan }}</p>
          </div>
          <button @click="detailModal.show = false" class="text-slate-400 hover:text-rose-500 transition-colors p-1 bg-white rounded-lg border border-slate-200 hover:border-rose-200">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <div class="p-5 overflow-y-auto custom-scrollbar flex-1 bg-slate-50/50">
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <div v-for="(item, idx) in detailModal.pertemuans" :key="idx" class="bg-white border border-slate-200/60 p-3 rounded-xl shadow-sm hover:border-sky-300 transition-colors group relative overflow-hidden">
              <div class="absolute left-0 top-0 bottom-0 w-1 bg-sky-400 opacity-0 group-hover:opacity-100 transition-opacity"></div>
              <div class="flex items-start justify-between">
                <div class="min-w-0 pr-2">
                  <div class="flex items-center text-xs font-bold text-slate-700 mb-1 truncate">
                    <AppIcon name="calendar-days" class="w-4 h-4 mr-1.5 text-sky-600" /> {{ formatDate(item.tanggal) }}
                  </div>
                  <div class="text-[10px] text-slate-500 flex items-center font-medium">
                    <AppIcon name="clock" class="w-3.5 h-3.5 mr-1.5 opacity-60" /> {{ item.jam }}
                  </div>
                </div>
                <div class="text-[9px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-black border border-slate-200 whitespace-nowrap shrink-0 uppercase tracking-widest">
                  Pert. #{{ idx + 1 }}
                </div>
              </div>
              <div class="mt-2 pt-2 border-t border-slate-100 text-[10px] text-slate-600 leading-relaxed font-medium">
                <span class="font-black text-slate-400 mr-1 uppercase tracking-widest">Materi:</span> <br/> {{ item.materi || '-' }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

definePageMeta({ layout: "guru", middleware: "guru", title: 'Jurnal Mengajar' })

const tokenCookie = useCookie('auth_token')
const activeSemester = ref('ganjil')
const detailModal = ref({ show: false, bulan: '', mapelKelas: '', pertemuans: [] })

const activeTabMobile = ref('dock')
const mobileTabs = [
  { id: 'dock', title: 'Ringkasan', icon: 'document-text' },
  { id: 'flow', title: 'Data Bulanan', icon: 'table-cells' }
]

const { data: response, pending: isLoading } = await useFetch(import.meta.env.VITE_API_BASE_URL + '/api/guru/jurnal-mengajar', {
  headers: {
    'Authorization': `Bearer ${tokenCookie.value}`,
    'Accept': 'application/json'
  }
})

const jurnalData = computed(() => response.value?.data || null)

const bulanGanjil = [
  { code: '07', name: 'Juli' },
  { code: '08', name: 'Agustus' },
  { code: '09', name: 'September' },
  { code: '10', name: 'Oktober' },
  { code: '11', name: 'November' },
  { code: '12', name: 'Desember' }
]

const bulanGenap = [
  { code: '01', name: 'Januari' },
  { code: '02', name: 'Februari' },
  { code: '03', name: 'Maret' },
  { code: '04', name: 'April' },
  { code: '05', name: 'Mei' },
  { code: '06', name: 'Juni' }
]

const activeBulanList = computed(() => activeSemester.value === 'ganjil' ? bulanGanjil : bulanGenap)
const activeData = computed(() => {
  if (!jurnalData.value) return { bulanan: {}, total: {} };
  return jurnalData.value[activeSemester.value] || { bulanan: {}, total: {} }
})

function countKeys(obj) {
  if (!obj) return 0;
  return Object.keys(obj).length;
}

function showDetail(bulanName, mapelKelas, pertemuans) {
  detailModal.value = {
    show: true,
    bulan: bulanName,
    mapelKelas: mapelKelas,
    pertemuans: pertemuans
  }
}

function formatDate(dateStr) {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
}
</script>
