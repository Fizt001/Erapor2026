<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <div class="flex-1 flex flex-col xl:flex-row overflow-hidden relative">
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-2 grid grid-cols-2 gap-2 z-20">
        <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTabMobile = tab.id"
          :class="activeTabMobile === tab.id ? 'bg-gradient-to-br from-rose-500 to-rose-600 text-white shadow-md shadow-rose-500/20 ring-2 ring-rose-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="rounded-xl flex items-center justify-center py-2 px-1 transition-all active:scale-95">
          <AppIcon :name="tab.icon" class="w-6 h-6 mb-0.5 transition-transform" :class="activeTabMobile === tab.id ? 'scale-110' : ''" />
          <span class="text-[10px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
        </button>
      </div>

      <!-- Panel Dock Kiri -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] overflow-y-auto custom-scrollbar transition-all', activeTabMobile === 'info' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[60px]' : '']">
        <div class="p-6 space-y-6">
          <div class="p-6 shrink-0 z-10 relative -mx-6 -mt-6 mb-6">
            <div class="bg-gradient-to-br from-rose-500 to-rose-700 rounded-2xl p-5 border border-rose-400 shadow-sm relative overflow-hidden flex flex-col gap-2">
              <div class="relative z-10">
                <h2 class="text-lg font-extrabold mb-1 text-white uppercase tracking-wider">Penanganan Kasus</h2>
                <p class="text-rose-100 text-xs leading-relaxed">
                  Evaluasi akhir tingkat Kurikulum
                </p>
              </div>
              <div class="absolute right-0 bottom-0 opacity-20 text-white">
                <svg class="w-24 h-24 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
              </div>
            </div>
          </div>
          <div class="bg-rose-50 p-5 rounded-2xl border border-rose-100">
            <h3 class="text-[10px] font-black text-rose-800 uppercase tracking-widest mb-3 flex items-center gap-2">
              <span><AppIcon name="pin" class="w-5 h-5 inline-block mr-1" /></span> Informasi Eskalasi
            </h3>
            <p class="text-xs text-rose-700 leading-relaxed font-medium">
              Halaman ini menampilkan eskalasi sistem untuk pelanggaran berat akademik (Alpa beruntun). 
              <br><br>
              <strong>SP2 (10x Alpa):</strong> Sekadar mengetahui (ditangani BK).<br>
              <strong>SP3 (15x Alpa):</strong> Wajib memberikan keputusan akhir (ACC).
            </p>
          </div>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative', activeTabMobile === 'cases' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[60px]' : '']">
        <div class="p-2 sm:pt-3 sm:pb-6 sm:px-6 lg:pt-3 lg:pb-8 lg:px-8 max-w-5xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-white z-20">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-rose-100 shadow-inner flex items-center justify-center text-xl border border-rose-200 text-rose-600"><AppIcon name="shield-check" class="w-6 h-6" /></div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-slate-800">Daftar Eskalasi Akademik</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Menampilkan kasus SP2 dan SP3</p>
                    </div>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto custom-scrollbar p-6">
                <div v-if="isLoading" class="flex justify-center py-12">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-rose-600"></div>
                </div>
                <div v-else-if="kasusList.length === 0" class="text-center py-12 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50">
                    <div class="text-4xl mb-3 opacity-50"><AppIcon name="sparkles" class="w-5 h-5 inline-block mr-1" /></div>
                    <p class="text-sm font-bold text-slate-600 uppercase tracking-widest mb-1">Bersih!</p>
                    <p class="text-xs font-medium text-slate-400">Tidak ada kasus SP2 atau SP3 saat ini.</p>
                </div>
                <div v-else class="space-y-4">
                    <div v-for="kasus in kasusList" :key="kasus.id" class="border border-slate-200 rounded-2xl overflow-hidden bg-white hover:border-slate-300 transition-colors shadow-sm">
                        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 font-bold flex items-center justify-center border border-rose-200 uppercase text-xs">
                                    {{ kasus.siswa.nama.substring(0, 2) }}
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-slate-800 uppercase">{{ kasus.siswa.nama }}</h4>
                                    <p class="text-[10px] font-bold text-slate-400 mt-0.5">{{ kasus.siswa.kelas }} • NIS: {{ kasus.siswa.nis }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border"
                                      :class="kasus.kategori === 'SP3' ? 'bg-rose-100 text-rose-700 border-rose-200' : 'bg-orange-100 text-orange-700 border-orange-200'">
                                    {{ kasus.kategori }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border"
                                      :class="kasus.status === 'Selesai' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200'">
                                    {{ kasus.status }}
                                </span>
                            </div>
                        </div>
                        <div class="p-5">
                            <div class="mb-4">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Masalah Akademik</p>
                                <p class="text-sm font-medium text-slate-700 leading-relaxed">{{ kasus.deskripsi_masalah }}</p>
                            </div>
                            <div class="mb-4" v-if="kasus.tindakan_penyelesaian">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Riwayat Tindakan</p>
                                <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs text-slate-600 whitespace-pre-wrap font-mono">{{ kasus.tindakan_penyelesaian }}</div>
                            </div>
                            <div class="flex justify-end pt-4 border-t border-slate-100">
                                <button v-if="kasus.kategori === 'SP3' && (!kasus.tindakan_penyelesaian || !kasus.tindakan_penyelesaian.includes('ACC Kurikulum'))" 
                                        @click="openModal(kasus)"
                                        class="px-5 py-2.5 bg-rose-600 text-white text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-rose-700 transition-colors shadow-sm flex items-center gap-2">
                                    <span><AppIcon name="scale" class="w-5 h-5 inline-block mr-1" /></span> Berikan ACC Kurikulum
                                </button>
                                <div v-else-if="kasus.kategori === 'SP2' && kasus.status === 'Proses'" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-pulse"></span>
                                    Sedang ditangani oleh BK
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal ACC -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="closeModal"></div>
      <div class="bg-white rounded-3xl shadow-xl w-full max-w-lg overflow-hidden relative z-10 flex flex-col max-h-[90vh]">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-rose-50/50">
          <div>
            <h3 class="text-sm font-black text-rose-800 uppercase tracking-widest">Eksekusi SP3</h3>
            <p class="text-[10px] font-bold text-rose-600 uppercase mt-1">Persetujuan Akhir Kurikulum</p>
          </div>
          <button @click="closeModal" class="w-8 h-8 flex items-center justify-center rounded-xl bg-white text-slate-400 hover:bg-rose-100 hover:text-rose-600 transition-colors border border-slate-200">
            ✕
          </button>
        </div>
        <div class="p-6 overflow-y-auto custom-scrollbar">
          <form @submit.prevent="submitAcc" class="space-y-5">
            <div>
              <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Keputusan Akhir Waka Kurikulum <span class="text-rose-500">*</span></label>
              <div class="grid grid-cols-2 gap-3">
                <label class="cursor-pointer">
                    <input type="radio" v-model="form.keputusan" value="Keluarkan" class="peer sr-only" required>
                    <div class="p-4 rounded-xl border-2 border-slate-100 peer-checked:border-rose-500 peer-checked:bg-rose-50 text-center transition-all">
                        <span class="block text-xl mb-1"><AppIcon name="no-symbol" class="w-5 h-5 inline-block mr-1" /></span>
                        <span class="block text-[11px] font-black text-slate-700 peer-checked:text-rose-700 uppercase">Keluarkan</span>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" v-model="form.keputusan" value="Pertahankan" class="peer sr-only" required>
                    <div class="p-4 rounded-xl border-2 border-slate-100 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 text-center transition-all">
                        <span class="block text-xl mb-1"><AppIcon name="shield-check" class="w-6 h-6" /></span>
                        <span class="block text-[11px] font-black text-slate-700 peer-checked:text-emerald-700 uppercase">Pertahankan</span>
                    </div>
                </label>
              </div>
            </div>
            
            <div>
              <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Catatan Tambahan <span class="text-rose-500">*</span></label>
              <textarea v-model="form.catatan_kurikulum" rows="4" 
                        class="w-full text-sm p-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all resize-none"
                        placeholder="Misal: Siswa telah diberikan pembinaan terakhir dengan orang tua..." required></textarea>
            </div>
          </form>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end gap-3 shrink-0">
          <button type="button" @click="closeModal" class="px-5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-200 bg-slate-100 rounded-xl transition-colors">Batal</button>
          <button @click="submitAcc" :disabled="isSubmitting || !form.keputusan || !form.catatan_kurikulum" 
                  class="px-6 py-2.5 text-xs font-black uppercase tracking-widest text-white rounded-xl transition-all shadow-sm flex items-center justify-center min-w-[120px]"
                  :class="(isSubmitting || !form.keputusan || !form.catatan_kurikulum) ? 'bg-rose-400 cursor-not-allowed' : 'bg-rose-600 hover:bg-rose-700 shadow-rose-500/30'">
            <span v-if="isSubmitting" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            <span v-else>Simpan Eksekusi</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'

definePageMeta({
    layout: 'kurikulum',
    middleware: 'kurikulum',
    title: 'Penanganan Kasus'
})

const isLoading = ref(true)
const kasusList = ref<any[]>([])
const showModal = ref(false)
const isSubmitting = ref(false)
const selectedKasus = ref<any>(null)

// Responsiveness detector
const windowWidth = ref(1024)
const isDesktop = computed(() => windowWidth.value >= 1280) // xl breakpoint
const activeTabMobile = ref('info')
const mobileTabs = [
  { id: 'info', title: 'Informasi', icon: 'clipboard-document-list' },
  { id: 'cases', title: 'Daftar Kasus', icon: 'shield-check' }
]

const handleResize = () => {
    windowWidth.value = window.innerWidth
}

const form = ref({
    keputusan: '',
    catatan_kurikulum: ''
})

const fetchKasus = async () => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        const data = await $fetch<any>(import.meta.env.VITE_API_BASE_URL + '/api/kurikulum/penanganan', {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (data?.success) {
            kasusList.value = data.data
        }
    } catch (error) {
        console.error('Error fetching kasus:', error)
    } finally {
        isLoading.value = false
    }
}

const openModal = (kasus: any) => {
    selectedKasus.value = kasus
    form.value = {
        keputusan: '',
        catatan_kurikulum: ''
    }
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    selectedKasus.value = null
}

const submitAcc = async () => {
    if (!form.value.keputusan || !form.value.catatan_kurikulum) return

    isSubmitting.value = true
    const token = useCookie('auth_token').value
    try {
        const data = await $fetch<any>(`${import.meta.env.VITE_API_BASE_URL}/api/kurikulum/penanganan/${selectedKasus.value.id}/acc`, {
            method: 'PUT',
            headers: { Authorization: `Bearer ${token}` },
            body: form.value
        })

        if (data?.success) {
            closeModal()
            await fetchKasus()
        }
    } catch (error) {
        console.error('Error updating kasus:', error)
        alert('Gagal menyimpan keputusan.')
    } finally {
        isSubmitting.value = false
    }
}

onMounted(() => {
    windowWidth.value = window.innerWidth
    window.addEventListener('resize', handleResize)
    fetchKasus()
})

onUnmounted(() => {
    window.removeEventListener('resize', handleResize)
})
</script>
