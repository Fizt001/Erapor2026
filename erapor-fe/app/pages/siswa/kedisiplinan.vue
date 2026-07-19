<template>
  <div class="animate-fadeIn pb-12 mt-4 relative">
    
    <!-- Header -->
    <div class="mb-6">
      <h2 class="text-2xl font-black text-slate-800 tracking-tight">Catatan Kedisiplinan</h2>
      <p class="text-slate-500 text-sm font-medium mt-1">Rekam jejak pelanggaran dan penghargaan selama tahun ajaran aktif.</p>
    </div>

    <!-- Superadmin Empty State -->
    <div v-if="isSuperadminWithoutImpersonation" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 flex flex-col items-center text-center">
      <div class="w-20 h-20 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mb-6 shadow-inner border border-amber-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
      </div>
      <h3 class="text-2xl font-black text-slate-800 mb-2">Menunggu Pilihan Siswa</h3>
      <p class="text-slate-500 text-base max-w-md mx-auto">Anda sedang dalam Mode Superadmin. Silakan pilih kelas dan nama siswa dari bilah menu di <strong class="text-amber-600 font-bold">pojok kanan atas</strong> untuk melihat Catatan Kedisiplinan.</p>
    </div>

    <!-- Loading State -->
    <div v-else-if="isLoading" class="flex flex-col items-center justify-center py-20">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-emerald-500 mb-4"></div>
      <p class="text-slate-500 font-medium">Memuat catatan kedisiplinan...</p>
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

    <div v-else-if="kedisiplinanData" class="space-y-6">
      
      <!-- Top Card: Sisa Poin -->
      <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl shadow-lg p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>
        <div class="absolute bottom-0 left-10 -mb-10 w-32 h-32 bg-white opacity-10 rounded-full blur-xl"></div>
        
        <div class="relative z-10 text-white">
          <h3 class="text-lg font-bold text-emerald-100 mb-1">Total Poin Kedisiplinan</h3>
          <p class="text-sm text-emerald-50/80 max-w-md">Tahun Ajaran {{ kedisiplinanData.tahun_ajaran }}. Poin dasar adalah 100. Poin akan berkurang jika melakukan pelanggaran, dan bertambah jika mendapat penghargaan.</p>
        </div>
        
        <div class="relative z-10 bg-white/20 backdrop-blur-md rounded-2xl p-4 min-w-[140px] text-center border border-white/20 shadow-inner">
          <span class="block text-4xl font-black text-white drop-shadow-md">{{ kedisiplinanData.sisa_poin }}</span>
          <span class="text-xs font-bold text-emerald-50 uppercase tracking-wider mt-1 block">Poin Tersisa</span>
        </div>
      </div>

      <!-- Timeline History -->
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <h3 class="text-base font-black text-slate-800">Riwayat Catatan</h3>
            <p class="text-xs font-medium text-slate-500 mt-1">Daftar riwayat kedisiplinan (terbaru di atas).</p>
          </div>
        </div>
        
        <div class="p-6">
          <div v-if="kedisiplinanData.history.length === 0" class="py-10 flex flex-col items-center justify-center text-center">
            <span class="text-4xl mb-3">✨</span>
            <h4 class="text-base font-bold text-slate-700 mb-1">Bersih Tanpa Catatan!</h4>
            <p class="text-sm text-slate-500">Kamu belum memiliki riwayat pelanggaran maupun penghargaan pada tahun ajaran ini. Pertahankan!</p>
          </div>
          
          <div v-else class="relative border-l-2 border-slate-100 ml-3 md:ml-4 py-2 space-y-8">
            <div v-for="item in kedisiplinanData.history" :key="item.id" class="relative pl-6 sm:pl-8 group">
              <!-- Timeline Dot -->
              <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full border-2 border-white shadow-sm transition-transform group-hover:scale-125"
                   :class="item.skor_pengurang > 0 ? 'bg-rose-500' : 'bg-emerald-500'"></div>
              
              <div class="bg-slate-50/50 hover:bg-slate-50 border border-slate-100 hover:border-slate-200 rounded-xl p-4 transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                  <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-white px-2 py-1 rounded shadow-sm"
                          :class="item.skor_pengurang > 0 ? 'bg-rose-500' : 'bg-emerald-500'">
                      {{ item.jenis }}
                    </span>
                    <span class="text-xs font-bold text-slate-400">{{ formatDate(item.tanggal) }}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-500 bg-white border border-slate-200 px-2 py-1 rounded">
                      Periode: {{ item.periode }}
                    </span>
                  </div>
                </div>
                
                <h4 class="text-[15px] font-black text-slate-800 mb-1">{{ item.deskripsi }}</h4>
                <p v-if="item.catatan" class="text-sm text-slate-500 mb-3 italic">"{{ item.catatan }}"</p>
                
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-100">
                  <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">
                      {{ item.guru.charAt(0).toUpperCase() }}
                    </div>
                    <span class="text-xs font-medium text-slate-500">Dicatat oleh: <span class="font-bold text-slate-700">{{ item.guru }}</span></span>
                  </div>
                  
                  <div class="text-sm font-black" :class="item.skor_pengurang > 0 ? 'text-rose-600' : 'text-emerald-600'">
                    <span v-if="item.skor_pengurang > 0">- {{ item.skor_pengurang }} Poin</span>
                    <span v-else>+ {{ item.skor_penambah }} Poin</span>
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
import { ref, onMounted } from 'vue'

definePageMeta({
  layout: 'siswa',
  title: 'Catatan Kedisiplinan',
  middleware: 'siswa'
})

const isLoading = ref(true)
const errorMessage = ref('')
const kedisiplinanData = ref(null)

const userProfile = useCookie('user_profile')
const isSuperadminWithoutImpersonation = computed(() => {
  let role = null;
  if (typeof userProfile.value === 'string') {
    try {
      role = JSON.parse(userProfile.value)?.role
    } catch (e) {
      role = null;
    }
  } else {
    role = userProfile.value?.role
  }
  return role === 'superadmin' && !!errorMessage.value
})

const fetchData = async () => {
    isLoading.value = true
    errorMessage.value = ''
    
    try {
        const tokenCookie = useCookie('auth_token')
        const res = await $fetch('http://localhost:8000/api/siswa/kedisiplinan', {
            headers: {
                'Authorization': `Bearer ${tokenCookie.value}`,
                'Accept': 'application/json'
            }
        })
        
        if (res.success && res.data) {
            kedisiplinanData.value = res.data
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

const formatDate = (dateString) => {
    if (!dateString) return '-'
    const date = new Date(dateString)
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    }).format(date)
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
