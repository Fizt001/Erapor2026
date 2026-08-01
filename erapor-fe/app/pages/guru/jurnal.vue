<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center">
          <AppIcon name="book-open" class="w-8 h-8 mr-3 text-slate-700" /> Jurnal Mengajar
        </h1>
        <p class="text-sm text-slate-500 mt-1 font-medium">Rekapitulasi jumlah absensi pertemuan Anda per bulan.</p>
      </div>
    </div>

    <!-- SEMESTER TABS -->
    <div class="bg-white p-1 rounded-xl shadow-sm border border-slate-200/60 flex w-full md:inline-flex mb-2">
      <button 
        @click="activeSemester = 'ganjil'" 
        class="flex-1 md:flex-none px-6 py-2 rounded-lg text-sm font-bold transition-all duration-200"
        :class="activeSemester === 'ganjil' ? 'bg-sky-500 text-white shadow-md' : 'text-slate-500 hover:bg-slate-50'"
      >
        Semester Ganjil
      </button>
      <button 
        @click="activeSemester = 'genap'" 
        class="flex-1 md:flex-none px-6 py-2 rounded-lg text-sm font-bold transition-all duration-200"
        :class="activeSemester === 'genap' ? 'bg-sky-500 text-white shadow-md' : 'text-slate-500 hover:bg-slate-50'"
      >
        Semester Genap
      </button>
    </div>

    <div v-if="isLoading" class="flex flex-col items-center justify-center h-64 text-slate-400">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-sky-500 mb-4"></div>
      <p class="text-sm font-bold">Memuat Jurnal Mengajar...</p>
    </div>

    <div v-else-if="!jurnalData" class="bg-white p-10 rounded-2xl border border-slate-200/60 shadow-sm text-center">
        <p class="text-slate-500 font-bold">Gagal memuat data jurnal mengajar.</p>
    </div>

    <div v-else class="space-y-6">
      <!-- LIST BULANAN -->
      <div class="space-y-4">
        <!-- Bulan Items -->
        <div v-for="bulan in activeBulanList" :key="bulan.code" class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden flex flex-col hover:border-sky-300 transition-colors">
          <div class="bg-slate-50 border-b border-slate-100 px-5 py-4 flex items-center justify-between">
            <h3 class="font-bold text-slate-700 text-sm flex items-center">
              <AppIcon name="calendar" class="w-5 h-5 mr-2 text-sky-500" /> {{ bulan.name }}
            </h3>
            <span class="bg-white border border-slate-200 text-slate-500 text-[10px] px-3 py-1 rounded-full font-bold shadow-sm">
              {{ countKeys(activeData.bulanan[bulan.code]) }} Kelas/Mapel
            </span>
          </div>
          
          <div class="p-0">
            <div v-if="Object.keys(activeData.bulanan[bulan.code] || {}).length === 0" class="text-center py-8 text-slate-400 bg-slate-50/50">
              <div class="flex justify-center mb-2 opacity-50 grayscale text-slate-400"><AppIcon name="moon" class="w-8 h-8" /></div>
              <p class="text-xs font-bold">Tidak ada pertemuan</p>
            </div>
            
            <div v-else class="overflow-x-auto">
              <table class="w-full text-left border-collapse">
                <thead>
                  <tr class="bg-slate-50/50 text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">
                    <th class="px-5 py-3">Rombongan Belajar / Mapel</th>
                    <th class="px-5 py-3 w-32 text-center">Total Pertemuan</th>
                    <th class="px-5 py-3 w-24 text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-for="(pertemuans, mapelKelas) in activeData.bulanan[bulan.code]" :key="mapelKelas" class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-3">
                      <p class="text-xs font-bold text-slate-700">{{ mapelKelas }}</p>
                    </td>
                    <td class="px-5 py-3 text-center">
                      <span class="inline-flex items-center justify-center bg-sky-100 text-sky-700 text-xs font-black px-2.5 py-1 rounded-md min-w-[2.5rem] shadow-sm">
                        {{ pertemuans.length }}x
                      </span>
                    </td>
                    <td class="px-5 py-3 text-center">
                      <button @click="showDetail(bulan.name, mapelKelas, pertemuans)" class="text-sky-500 hover:text-sky-600 hover:bg-sky-50 transition-colors p-1.5 rounded-lg border border-transparent hover:border-sky-200" title="Lihat Rincian Tanggal">
                        <AppIcon name="eye" class="w-4 h-4" />
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- TOTAL SEMESTER CARD -->
      <div class="bg-sky-50 rounded-2xl shadow-sm border border-sky-100 overflow-hidden relative mt-2">
        <div class="absolute top-0 right-0 p-4 opacity-[0.05]">
          <svg class="w-24 h-24 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
        </div>
        <div class="p-4 relative z-10">
          <div class="flex items-center justify-between mb-3">
            <div>
              <h2 class="text-sm font-black text-sky-800 flex items-center uppercase tracking-widest">
                <AppIcon name="chart-bar" class="w-5 h-5 mr-2 text-sky-500" /> Total Semester {{ activeSemester === 'ganjil' ? 'Ganjil' : 'Genap' }}
              </h2>
              <p class="text-sky-600/70 text-[10px] font-bold mt-0.5">Akumulasi seluruh pertemuan selama 1 semester.</p>
            </div>
          </div>
          
          <div v-if="Object.keys(activeData.total || {}).length === 0" class="text-sky-500 text-xs font-bold italic">
            Belum ada rekapitulasi semester ini.
          </div>
          <div v-else class="overflow-x-auto mt-4 bg-white rounded-xl border border-sky-100">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-sky-50/50 text-[10px] font-black uppercase tracking-widest text-sky-600 border-b border-sky-100">
                  <th class="px-4 py-2">Rombongan Belajar / Mapel</th>
                  <th class="px-4 py-2 w-32 text-center">Total Pertemuan</th>
                  <th class="px-4 py-2 w-24 text-center">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-sky-50">
                <tr v-for="(pertemuans, mapelKelas) in activeData.total" :key="mapelKelas" class="hover:bg-sky-50 transition-colors">
                  <td class="px-4 py-2 text-xs font-bold text-slate-700">{{ mapelKelas }}</td>
                  <td class="px-4 py-2 text-center">
                    <span class="bg-sky-100 text-sky-700 text-[10px] font-black px-2 py-1 rounded-lg min-w-[2rem] shadow-sm">
                      {{ pertemuans.length }}x
                    </span>
                  </td>
                  <td class="px-4 py-2 text-center">
                    <button @click="showDetail('Semester ' + (activeSemester === 'ganjil' ? 'Ganjil' : 'Genap'), mapelKelas, pertemuans)" class="text-sky-500 hover:text-sky-600 hover:bg-sky-100 transition-colors p-1 rounded-lg border border-transparent hover:border-sky-200" title="Lihat Rincian Tanggal">
                      <AppIcon name="eye" class="w-4 h-4" />
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
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
            <h3 class="font-black text-slate-800 text-base flex items-center"><AppIcon name="magnifying-glass" class="w-5 h-5 mr-2 text-slate-600" /> Rincian Pertemuan</h3>
            <p class="text-[11px] text-slate-500 font-medium mt-0.5">{{ detailModal.mapelKelas }} • {{ detailModal.bulan }}</p>
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
                <div class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-bold border border-slate-200 whitespace-nowrap shrink-0">
                  Pertemuan #{{ idx + 1 }}
                </div>
              </div>
              <div class="mt-2 pt-2 border-t border-slate-100 text-[11px] text-slate-600">
                <span class="font-bold text-slate-500 mr-1">Materi:</span> {{ item.materi || '-' }}
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
