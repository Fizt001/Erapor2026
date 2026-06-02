<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- MAIN GRID LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-8">
      
      <!-- ==============================================
           KIRI: DAFTAR KURIKULUM (33%)
           ============================================== -->
      <div class="lg:col-span-4 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col min-h-[500px]">
          <div class="p-6 bg-slate-900 border-b border-slate-700">
            <h3 class="text-sm font-black uppercase tracking-widest text-white">Daftar Kurikulum</h3>
            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Pilih kurikulum untuk mengatur deskripsi</p>
          </div>
          
          <div class="p-4 flex-grow overflow-y-auto">
            <div v-if="isLoadingKurikulum" class="text-center p-8 opacity-50">
              <span class="animate-spin text-2xl inline-block mb-2">↻</span>
              <p class="text-xs font-bold uppercase tracking-widest">Memuat data...</p>
            </div>
            
            <div v-else-if="kurikulums.length === 0" class="text-center p-8 text-slate-400 text-sm">
              Belum ada data kurikulum.
            </div>
            
            <div v-else class="space-y-2">
              <button 
                v-for="kur in kurikulums" :key="kur.id"
                @click="selectKurikulum(kur)"
                class="w-full text-left px-4 py-3 rounded-xl border transition-all duration-200 group flex items-center justify-between"
                :class="selectedKurikulum?.id === kur.id ? 'bg-indigo-50 border-indigo-200' : 'bg-white border-slate-200 hover:border-indigo-300 hover:bg-slate-50'"
              >
                <div>
                  <h4 class="font-bold text-sm text-slate-800 group-hover:text-indigo-700">{{ kur.nama_kurikulum }}</h4>
                  <p class="text-[10px] text-slate-500 uppercase tracking-widest mt-1">Status: Aktif</p>
                </div>
                <div v-if="hasTemplate(kur.id)" class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">
                  ✓
                </div>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- ==============================================
           KANAN: WORKSPACE AREA (67%)
           ============================================== -->
      <div class="lg:col-span-8">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col min-h-[500px]">
          
          <!-- Header Card Kanan -->
          <div class="px-6 py-4 bg-sky-600 border-b border-sky-700 flex justify-between items-center text-white shrink-0 shadow-sm z-10">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-lg shadow-inner">📝</div>
              <div>
                <h3 class="text-sm font-black leading-none uppercase tracking-wide">Template Deskripsi Capaian</h3>
                <p class="text-[10px] font-bold text-sky-100 uppercase tracking-widest mt-1">
                  {{ selectedKurikulum ? `Kurikulum: ${selectedKurikulum.nama_kurikulum}` : 'Pilih kurikulum terlebih dahulu' }}
                </p>
              </div>
            </div>
          </div>

          <!-- Alert Belum Lengkap -->
          <div v-if="!selectedKurikulum" class="flex-grow flex flex-col items-center justify-center p-16 text-center relative overflow-hidden bg-gradient-to-b from-transparent to-slate-50/50">
            <!-- Background Decoration -->
            <div class="absolute inset-0 flex items-center justify-center opacity-[0.02] pointer-events-none">
                <svg class="w-96 h-96" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zM7 10h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z"/></svg>
            </div>
            
            <div class="w-24 h-24 mb-6 rounded-full bg-indigo-50 flex items-center justify-center border-8 border-white shadow-sm relative z-10">
                <span class="text-4xl animate-bounce" style="animation-duration: 3s;">📋</span>
            </div>
            
            <h3 class="font-black text-slate-700 text-xl tracking-tight relative z-10 mb-2">Pilih Kurikulum</h3>
            <p class="text-sm font-semibold text-slate-500 max-w-md relative z-10 leading-relaxed">
                Silakan pilih kurikulum di panel sebelah kiri untuk mengatur template teks deskripsi capaian kompetensi siswa pada rapor.
            </p>
            
            <div class="mt-8 flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest relative z-10">
                <span class="animate-pulse text-lg">👈</span> Arahkan perhatian ke panel kiri
            </div>
          </div>

          <!-- Form Area -->
          <div v-else class="flex-grow flex flex-col relative bg-slate-50/50 p-6 md:p-8">
            <div class="space-y-6">
              
              <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
                <h4 class="text-xs font-bold text-blue-800 uppercase tracking-wider mb-2 flex items-center gap-2">
                  <span>💡</span> Petunjuk Penggunaan Tag
                </h4>
                <p class="text-[13px] text-blue-700 leading-relaxed">
                  Gunakan tag <code class="bg-white px-1.5 py-0.5 rounded border border-blue-300 font-bold">[NAMA_TP]</code> di dalam teks. Sistem akan secara otomatis mengganti tag tersebut dengan nama Tujuan Pembelajaran (TP) yang sesungguhnya saat meng-*generate* nilai siswa di akhir semester.
                </p>
              </div>

              <!-- Teks Tertinggi -->
              <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Template Deskripsi Capaian (TP Tertinggi)</label>
                <textarea 
                  v-model="form.teks_tertinggi" 
                  rows="4"
                  class="w-full px-4 py-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-medium text-slate-700 outline-none resize-none"
                  placeholder="Contoh: Peserta didik sangat menguasai materi [NAMA_TP] pada saat kegiatan pembelajaran di sekolah."
                ></textarea>
                <p class="text-[10px] text-slate-500 mt-2">Teks ini akan muncul dengan menyisipkan nilai TP siswa yang tertinggi.</p>
              </div>

            </div>

            <!-- Footer Action -->
            <div class="mt-8 pt-6 border-t border-slate-200 flex justify-end">
              <button 
                @click="saveTemplate" 
                class="flex items-center space-x-2 text-[12px] font-black uppercase tracking-widest px-6 py-3 bg-indigo-600 text-white rounded-xl shadow-md hover:bg-indigo-700 hover:shadow-lg transition-all active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed"
                :disabled="isSaving"
              >
                <span v-if="isSaving" class="animate-spin text-lg">↻</span>
                <span v-else class="text-lg">💾</span>
                <span>{{ isSaving ? 'Menyimpan...' : 'Simpan Template' }}</span>
              </button>
            </div>
          </div>

        </div>
      </div>
      
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'

definePageMeta({
  layout: 'kurikulum',
  middleware: 'kurikulum',
  title: 'Master Deskripsi Capaian'
})

const kurikulums = ref([])
const templates = ref([])
const isLoadingKurikulum = ref(true)
const selectedKurikulum = ref(null)
const isSaving = ref(false)

const form = reactive({
  teks_tertinggi: ''
})

const tokenCookie = useCookie('auth_token')

const fetchData = async () => {
  isLoadingKurikulum.value = true
  try {
    // Ambil Data Kurikulum dari endpoint titimangsa (karena endpoint ini mengembalikan data kurikulum juga)
    const dataRef = await $fetch('http://localhost:8000/api/kurikulum/titimangsa', {
      headers: { 'Authorization': `Bearer ${tokenCookie.value}` }
    })
    
    if (dataRef.success) {
      kurikulums.value = dataRef.data.kurikulums
    }

    const tplData = await $fetch('http://localhost:8000/api/kurikulum/deskripsi-template', {
      headers: { 'Authorization': `Bearer ${tokenCookie.value}` }
    })

    if (tplData.status === 'success') {
      templates.value = tplData.data
    }
  } catch (error) {
    console.error('Error fetching data:', error)
  } finally {
    isLoadingKurikulum.value = false
  }
}

const hasTemplate = (kurId) => {
  return templates.value.some(t => t.kurikulum_id === kurId && t.teks_tertinggi)
}

const selectKurikulum = (kur) => {
  selectedKurikulum.value = kur
  
  const existing = templates.value.find(t => t.kurikulum_id === kur.id)
  if (existing) {
    form.teks_tertinggi = existing.teks_tertinggi || ''
  } else {
    // Default suggestion
    form.teks_tertinggi = 'Peserta didik sangat menguasai materi [NAMA_TP] pada saat kegiatan pembelajaran di sekolah.'
  }
}

const saveTemplate = async () => {
  if (!selectedKurikulum.value) return
  
  isSaving.value = true
  const { $swal } = useNuxtApp()
  
  try {
    const res = await $fetch('http://localhost:8000/api/kurikulum/deskripsi-template', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${tokenCookie.value}`,
        'Content-Type': 'application/json'
      },
      body: {
        kurikulum_id: selectedKurikulum.value.id,
        teks_tertinggi: form.teks_tertinggi,
        teks_terendah: '', // Send empty or default since it's required by controller
        is_active: true
      }
    })
    
    if (res.status === 'success') {
      $swal.fire({
        title: 'Berhasil!',
        text: 'Template deskripsi berhasil disimpan.',
        icon: 'success',
        confirmButtonColor: '#4f46e5'
      })
      await fetchData()
      // Reselect to update form state from latest fetched
      selectKurikulum(selectedKurikulum.value)
    }
  } catch (error) {
    console.error('Error saving template:', error)
    $swal.fire({
        title: 'Gagal!',
        text: 'Gagal menyimpan template. Silakan coba lagi.',
        icon: 'error',
        confirmButtonColor: '#ef4444'
    })
  } finally {
    isSaving.value = false
  }
}

onMounted(() => {
  fetchData()
})
</script>
