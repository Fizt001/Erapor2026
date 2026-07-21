<template>
  <div class="animate-fadeIn pb-12 mt-4 relative">
    
    <!-- Header -->
    <div class="mb-6">
      <h2 class="text-2xl font-black text-slate-800 tracking-tight">Portofolio Saya</h2>
      <p class="text-slate-500 text-sm font-medium mt-1">Galeri prestasi dan rekam jejak kegiatan ekstrakurikuler.</p>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex flex-col items-center justify-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-amber-500 mb-4"></div>
      <p class="text-slate-500 font-medium">Memuat portofolio...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="errorMessage" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-10 flex flex-col items-center text-center">
      <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center text-2xl mb-4">
        ⚠️
      </div>
      <h3 class="text-lg font-black text-slate-800 mb-2">Gagal Memuat Data</h3>
      <p class="text-slate-500 text-sm max-w-md mx-auto">{{ errorMessage }}</p>
      <button @click="fetchData" class="mt-6 px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-lg transition-colors shadow-sm">
        Coba Lagi
      </button>
    </div>

    <div v-else class="space-y-8">
      
      <!-- Prestasi Section -->
      <div>
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl shadow-sm">
            🏆
          </div>
          <div>
            <h3 class="text-lg font-black text-slate-800">Galeri Prestasi</h3>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">ALL TIME</p>
          </div>
        </div>

        <div v-if="portofolioData.prestasi.length === 0" class="bg-white border border-slate-200 border-dashed rounded-2xl p-10 text-center">
          <span class="text-4xl block mb-2 opacity-50 grayscale">🥇</span>
          <p class="text-slate-500 font-medium text-sm">Belum ada catatan prestasi yang divalidasi sekolah.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
          <div v-for="item in portofolioData.prestasi" :key="item.id" class="bg-gradient-to-b from-amber-50 to-white rounded-2xl shadow-sm border border-amber-100/50 p-6 flex flex-col relative overflow-hidden group hover:shadow-md hover:border-amber-200 transition-all">
            <div class="absolute top-0 right-0 w-32 h-32 bg-amber-400/10 rounded-full -mr-10 -mt-10 blur-2xl pointer-events-none group-hover:bg-amber-400/20 transition-all"></div>
            
            <div class="flex items-start justify-between mb-4 relative z-10">
              <span class="text-4xl drop-shadow-sm">🥇</span>
              <span class="text-[10px] font-bold text-amber-700 bg-amber-100/50 px-2 py-1 rounded border border-amber-200/50">{{ item.tahun }}</span>
            </div>
            
            <h4 class="text-base font-black text-slate-800 mb-1 relative z-10 line-clamp-2" :title="item.nama_prestasi">{{ item.nama_prestasi }}</h4>
            <p class="text-xs font-bold text-slate-500 mb-4 relative z-10">{{ item.tingkat }} • {{ item.jenis_prestasi }}</p>
            
            <div class="mt-auto pt-4 border-t border-amber-100/50 relative z-10">
              <p class="text-xs text-slate-500"><span class="font-medium">Oleh:</span> {{ item.penyelenggara }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Ekskul Section -->
      <div>
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl shadow-sm">
            🏃‍♂️
          </div>
          <div>
            <h3 class="text-lg font-black text-slate-800">Ekstrakurikuler</h3>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">TAHUN INI</p>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50/50 text-[10px] uppercase tracking-wider text-slate-500 border-b border-slate-200">
                  <th class="px-6 py-4 font-bold w-12 text-center">#</th>
                  <th class="px-6 py-4 font-bold">Nama Ekstrakurikuler</th>
                  <th class="px-6 py-4 font-bold text-center">Periode</th>
                  <th class="px-6 py-4 font-black text-emerald-600 text-center">Nilai/Predikat</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="portofolioData.ekskul.length === 0">
                  <td colspan="4" class="px-6 py-8 text-center text-slate-500 text-sm">Tidak terdaftar di ekskul manapun pada tahun ini.</td>
                </tr>
                <tr v-for="(item, index) in portofolioData.ekskul" :key="item.id" class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                  <td class="px-6 py-4 text-sm text-slate-400 font-medium text-center">{{ index + 1 }}</td>
                  <td class="px-6 py-4">
                    <p class="text-[14px] font-bold text-slate-800">{{ item.nama_ekskul }}</p>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded">{{ item.periode }}</span>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <span class="text-sm font-black text-emerald-600 bg-emerald-50 px-3 py-1 rounded-md border border-emerald-100">{{ item.nilai || '-' }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

definePageMeta({
  layout: 'siswa',
  title: 'Portofolio Saya',
  middleware: 'siswa'
})

const isLoading = ref(true)
const errorMessage = ref('')
const portofolioData = ref(null)

const fetchData = async () => {
    isLoading.value = true
    errorMessage.value = ''
    
    try {
        const tokenCookie = useCookie('auth_token')
        const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/siswa/portofolio', {
            headers: {
                'Authorization': `Bearer ${tokenCookie.value}`,
                'Accept': 'application/json'
            }
        })
        
        if (res.success && res.data) {
            portofolioData.value = res.data
        } else {
            errorMessage.value = res.message || 'Gagal mengambil data'
        }
    } catch (error) {
        console.error('Fetch error:', error)
        errorMessage.value = error.response?._data?.message || 'Terjadi kesalahan jaringan atau server.'
    } finally {
        isLoading.value = false
    }
}

onMounted(() => {
    fetchData()
})
</script>

<style scoped>
.animate-fadeIn {
  animation: fadeIn 0.3s ease-out forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
