<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-2 grid grid-cols-2 gap-2 z-20">
        <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTabMobile = tab.id"
          :class="activeTabMobile === tab.id ? 'bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-md shadow-indigo-500/20 ring-2 ring-indigo-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="rounded-xl flex flex-col items-center justify-center py-2 px-1 transition-all active:scale-95">
          <span class="text-lg mb-0.5 transition-transform" :class="activeTabMobile === tab.id ? 'scale-110' : ''">{{ tab.icon }}</span>
          <span class="text-[9px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
        </button>
      </div>

      <!-- Panel Dock Kiri -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all', activeTabMobile === 'kurikulum' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[60px]' : '']">
        
        <div class="p-6 shrink-0 z-10 relative">
          <div class="bg-gradient-to-r from-indigo-600 to-blue-700 rounded-2xl p-5 border border-indigo-500 shadow-sm relative overflow-hidden flex items-center gap-4">
            <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0 relative z-10">📚</div>
            <div class="relative z-10">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">Kurikulum</h3>
                <p class="text-[10px] text-indigo-100 font-semibold uppercase mt-0.5">Pilih kurikulum</p>
            </div>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto px-6 pb-6 space-y-2 custom-scrollbar">
            <div v-if="isLoadingKurikulum" class="text-center p-8 opacity-50 flex flex-col items-center justify-center">
              <span class="animate-spin text-2xl inline-block mb-2 text-indigo-500">↻</span>
              <p class="text-[10px] font-bold uppercase tracking-widest text-indigo-500">Memuat data...</p>
            </div>
            
            <div v-else-if="kurikulums.length === 0" class="text-center p-8 text-slate-400 text-sm">
              Belum ada data kurikulum.
            </div>
            
            <div v-else class="space-y-2">
              <button 
                v-for="kur in kurikulums" :key="kur.id"
                @click="selectKurikulum(kur)"
                class="w-full text-left px-4 py-3 rounded-xl border transition-all duration-200 group flex items-center justify-between"
                :class="selectedKurikulum?.id === kur.id ? 'bg-indigo-50 border-indigo-200 shadow-sm' : 'bg-white border-slate-200 hover:border-indigo-300 hover:bg-slate-50'"
              >
                <div>
                  <h4 class="font-bold text-sm group-hover:text-indigo-700" :class="selectedKurikulum?.id === kur.id ? 'text-indigo-700' : 'text-slate-800'">{{ kur.nama_kurikulum }}</h4>
                  <p class="text-[10px] text-slate-500 uppercase tracking-widest mt-1">Status: Aktif</p>
                </div>
                <div v-if="hasTemplate(kur.id)" class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">
                  ✓
                </div>
              </button>
            </div>
        </div>
      </div>

      <!-- Main Content Flow -->
      <div class="flex-1 flex flex-col min-w-0 bg-slate-50 relative h-full" :class="activeTabMobile === 'flow' || isDesktop ? 'flex' : 'hidden'">
            
            <!-- Header Flow -->
            <div class="p-4 bg-white border-b border-slate-200 flex justify-between items-center gap-4 shrink-0 z-10 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 shadow-sm border border-indigo-100 flex items-center justify-center text-xl hidden sm:flex text-indigo-500">📝</div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-indigo-700">Template Deskripsi</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">
                            {{ selectedKurikulum ? `Kurikulum: ${selectedKurikulum.nama_kurikulum}` : 'Pilih kurikulum di panel kiri' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto custom-scrollbar relative">
                
                <!-- Loading State -->
                <div v-if="isLoadingKurikulum" class="flex flex-col items-center justify-center h-full">
                    <div class="w-10 h-10 border-4 border-indigo-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</span>
                </div>

                <!-- Alert Belum Lengkap -->
                <div v-else-if="!selectedKurikulum" class="flex flex-col items-center justify-center h-full opacity-80 p-8 text-center relative overflow-hidden bg-gradient-to-b from-transparent to-slate-50/50">
                    <div class="w-24 h-24 mb-6 rounded-full bg-white flex items-center justify-center border border-slate-200 shadow-sm relative z-10">
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
                <div v-else class="p-6 md:p-8 max-w-4xl mx-auto space-y-6">
                    
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
                        class="w-full px-4 py-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-medium text-slate-700 outline-none resize-none shadow-sm"
                        placeholder="Contoh: Peserta didik sangat menguasai materi [NAMA_TP] pada saat kegiatan pembelajaran di sekolah."
                        ></textarea>
                        <p class="text-[10px] text-slate-500 mt-2">Teks ini akan muncul dengan menyisipkan nilai TP siswa yang tertinggi.</p>
                    </div>

                    <!-- Teks Ekstrakurikuler -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Template Deskripsi Ekstrakurikuler</label>
                        <textarea 
                        v-model="form.teks_ekskul" 
                        rows="4"
                        class="w-full px-4 py-3 bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm font-medium text-slate-700 outline-none resize-none shadow-sm"
                        placeholder="Contoh: Sangat baik dalam mengikuti kegiatan [NAMA_EKSKUL], dan memperoleh nilai [NILAI]"
                        ></textarea>
                        <p class="text-[10px] text-slate-500 mt-2">Gunakan tag <code class="font-bold border px-1 rounded bg-slate-100">[NAMA_EKSKUL]</code> dan <code class="font-bold border px-1 rounded bg-slate-100">[NILAI]</code>.</p>
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
import { ref, reactive, onMounted, computed } from 'vue'

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
const activeTahunAjaran = ref(null)

const form = reactive({
  teks_tertinggi: '',
  teks_ekskul: ''
})

// Responsiveness
const windowWidth = ref(1024)
const isDesktop = computed(() => windowWidth.value >= 1280)
const activeTabMobile = ref('kurikulum')
const mobileTabs = [
  { id: 'kurikulum', title: 'Kurikulum', icon: '📚' },
  { id: 'flow', title: 'Template', icon: '📝' }
]

const tokenCookie = useCookie('auth_token')

const fetchData = async () => {
  isLoadingKurikulum.value = true
  try {
    // Parallel Fetching using Promise.all
    const [dataRef, pengampuResp, tplData] = await Promise.all([
        $fetch('http://localhost:8000/api/kurikulum/titimangsa', {
            headers: { 'Authorization': `Bearer ${tokenCookie.value}` }
        }),
        $fetch('http://localhost:8000/api/kurikulum/pengampu', {
            headers: { 'Authorization': `Bearer ${tokenCookie.value}` }
        }),
        $fetch('http://localhost:8000/api/kurikulum/deskripsi-template', {
            headers: { 'Authorization': `Bearer ${tokenCookie.value}` }
        })
    ])
    
    if (dataRef.success) {
      kurikulums.value = dataRef.data.kurikulums
    }

    if (pengampuResp.success && pengampuResp.tahun_ajarans && pengampuResp.active_tahun_ajaran_id) {
        activeTahunAjaran.value = pengampuResp.tahun_ajarans.find(t => t.id === pengampuResp.active_tahun_ajaran_id)
    }

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
    form.teks_ekskul = existing.teks_ekskul || ''
  } else {
    // Default suggestion
    form.teks_tertinggi = 'Peserta didik sangat menguasai materi [NAMA_TP] pada saat kegiatan pembelajaran di sekolah.'
    form.teks_ekskul = 'Sangat baik dalam mengikuti kegiatan [NAMA_EKSKUL], dan memperoleh nilai [NILAI]'
  }
  
  if (!isDesktop.value) {
      activeTabMobile.value = 'flow'
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
        teks_ekskul: form.teks_ekskul,
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
  windowWidth.value = window.innerWidth
  window.addEventListener('resize', () => { windowWidth.value = window.innerWidth })
  fetchData()
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 20px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #94a3b8;
}
</style>
