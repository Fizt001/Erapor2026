<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex flex-col xl:flex-row overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="xl:w-[360px] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] overflow-y-auto custom-scrollbar">
        <div class="p-6 space-y-6">
          <div class="bg-gradient-to-r from-sky-600 to-blue-700 rounded-2xl p-5 border border-sky-500 shadow-sm relative overflow-hidden">
            <div class="relative z-10">
              <h3 class="text-sm font-black uppercase tracking-widest text-white">Catatan Wali Kelas</h3>
              <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Isi catatan wali kelas per periode</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-10">
              <svg class="w-24 h-24 transform translate-x-6 translate-y-6" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
          </div>
          
          <div class="space-y-4" v-if="pageData">
            <!-- Informasi Kelas & Tahun -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 space-y-3">
              <div class="flex justify-between items-center pb-2 border-b border-slate-200">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Kelas</span>
                <span class="text-[11px] font-bold text-slate-700">{{ pageData.kelas?.tingkat }} {{ pageData.kelas?.nama_kelas }}</span>
              </div>
              <div class="flex justify-between items-center pb-2 border-b border-slate-200">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Tahun Ajaran</span>
                <span class="text-[11px] font-bold text-slate-700">{{ pageData.tahun_ajaran?.tahun }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 mt-1">Kurikulum</span>
                <select 
                  v-model="pageData.kelas.kurikulum_id"
                  @change="saveKurikulum(pageData.kelas.kurikulum_id)"
                  class="text-[11px] font-bold text-indigo-700 bg-transparent border-none p-0 focus:ring-0 cursor-pointer outline-none text-right max-w-[150px]"
                >
                  <option v-for="kuri in pageData.master_kurikulum" :key="kuri.id" :value="kuri.id">{{ kuri.nama_kurikulum }}</option>
                </select>
              </div>
            </div>

            <!-- Search / Filter -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pencarian Siswa</label>
              <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 pointer-events-none">🔍</span>
                  <input type="text" v-model="searchQuery" placeholder="Cari nama siswa..." 
                    class="w-full pl-10 pr-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-semibold text-xs text-slate-700 outline-none">
              </div>
            </div>
            
            <div class="bg-indigo-50 p-4 rounded-2xl border border-indigo-100 flex flex-col items-center justify-center text-center">
                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center mb-2">
                    <span class="animate-pulse">💾</span>
                </div>
                <h4 class="text-[11px] font-black uppercase tracking-widest text-indigo-800">Auto-Save</h4>
                <p class="text-[10px] font-bold text-indigo-600 mt-1">Catatan akan tersimpan otomatis saat Anda selesai mengetik.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          
          <!-- Loading State -->
          <div v-if="pending" class="flex-grow flex flex-col items-center justify-center p-20 opacity-60 bg-white rounded-3xl shadow-sm border border-slate-200/60">
            <div class="w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Memuat Data...</span>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="flex-grow flex flex-col items-center justify-center p-16 text-center bg-white rounded-3xl shadow-sm border border-slate-200/60">
            <div class="text-rose-500 text-4xl mb-4">🔒</div>
            <h3 class="text-rose-800 font-black mb-1">Akses Ditolak</h3>
            <p class="text-rose-600 text-sm font-semibold max-w-md">{{ error.message || 'Terjadi kesalahan saat memuat data.' }}</p>
            <button @click="fetchData" class="mt-4 px-4 py-2 bg-rose-100 hover:bg-rose-200 text-rose-700 text-[10px] font-black uppercase tracking-widest rounded-lg transition-colors">
              Coba Lagi
            </button>
          </div>

          <!-- Main Content Container -->
          <div v-else-if="pageData" class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            
            <!-- Tabs Titimangsa -->
            <div class="flex border-b border-slate-200 bg-slate-50 overflow-x-auto custom-scrollbar shrink-0">
                <button 
                    v-for="titimangsa in pageData.titimangsas" 
                    :key="titimangsa.id"
                    @click="activeTab = titimangsa.id"
                    :class="[
                        'px-8 py-4 text-[11px] font-black uppercase tracking-widest transition-all whitespace-nowrap outline-none relative',
                        activeTab === titimangsa.id 
                            ? 'text-indigo-700 bg-white' 
                            : 'text-slate-400 hover:text-slate-600 hover:bg-slate-100/50',
                        !titimangsa.is_aktif ? 'opacity-60' : ''
                    ]"
                >
                    <span v-if="!titimangsa.is_aktif" class="mr-1">🔒</span>
                    {{ titimangsa.nama_periode_panjang || titimangsa.nama_periode }}
                    <div v-if="activeTab === titimangsa.id" class="absolute bottom-0 left-0 w-full h-[3px] bg-indigo-500 rounded-t-full"></div>
                </button>
            </div>
            
            <!-- Warning Closed -->
            <div v-if="activeTitimangsaData && !activeTitimangsaData.is_aktif" class="bg-amber-50 border-b border-amber-100 px-6 py-3 flex items-center gap-3 shrink-0">
                <span class="text-amber-500">🔒</span>
                <p class="text-[11px] font-black uppercase tracking-widest text-amber-700">Periode ini sudah ditutup. Catatan wali kelas bersifat Read-Only.</p>
            </div>

            <div class="flex-1 overflow-auto custom-scrollbar relative bg-slate-50/30">
              <table class="w-full text-left border-collapse bg-white">
                <thead class="sticky top-0 z-20 shadow-sm">
                  <tr class="bg-slate-100 border-b border-slate-200">
                    <th class="py-3 px-4 text-center text-[10px] font-black uppercase tracking-widest text-slate-500 w-[60px] border-r border-slate-200">No</th>
                    <th class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500 border-r border-slate-200 min-w-[200px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] sticky left-0 bg-slate-100 z-30">Nama Siswa</th>
                    <th v-if="activeTitimangsaData && activeTitimangsaData.is_akhir_tahun" class="py-3 px-4 text-center text-[10px] font-black uppercase tracking-widest text-slate-500 w-[180px] border-r border-slate-200">Rekomendasi</th>
                    <th class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500 min-w-[300px]">Catatan Wali Kelas</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <template v-if="activeTitimangsaData">
                    <tr 
                        v-for="(siswa, idx) in filteredSiswa" 
                        :key="activeTab + '-' + siswa.id"
                        class="hover:bg-slate-50/80 transition-colors group"
                    >
                        <td class="py-3 px-4 text-center text-[11px] font-bold text-slate-400 border-r border-slate-100 align-top">
                            {{ idx + 1 }}
                        </td>
                        <td class="py-3 px-4 border-r border-slate-100 sticky left-0 bg-white group-hover:bg-slate-50/90 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.02)] z-10 align-top">
                            <div class="text-[12px] font-black text-slate-700 uppercase tracking-wide">{{ siswa.nama_siswa }}</div>
                        </td>
                        
                        <td v-if="activeTitimangsaData && activeTitimangsaData.is_akhir_tahun" class="py-3 px-4 border-r border-slate-100 align-top">
                            <select 
                                v-model="siswa.rekomendasi_kenaikan"
                                :disabled="!activeTitimangsaData.is_aktif"
                                @change="handleInput(siswa)"
                                :class="[
                                    'w-full text-[11px] font-bold text-slate-700 border-2 rounded-xl px-2 py-1.5 outline-none transition-all',
                                    !activeTitimangsaData.is_aktif ? 'bg-slate-50 border-slate-200/50 opacity-70 cursor-not-allowed' : 'bg-white border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10'
                                ]"
                            >
                                <option value="belum_ditentukan">- Pilih -</option>
                                <option value="naik">Naik Kelas</option>
                                <option value="tinggal">Tinggal Kelas</option>
                                <option value="lulus">Lulus</option>
                            </select>
                        </td>

                        <td class="py-2 px-3 align-top">
                            <div class="relative w-full">
                                <textarea
                                    v-model="siswa.catatan"
                                    :disabled="!activeTitimangsaData.is_aktif"
                                    @input="handleInput(siswa)"
                                    :class="[
                                        'w-full text-xs font-bold text-slate-700 placeholder-slate-300 border-2 focus:ring-4 focus:ring-indigo-500/10 rounded-xl p-3 min-h-[60px] transition-all resize-none custom-scrollbar outline-none',
                                        !activeTitimangsaData.is_aktif ? 'bg-slate-50 border-slate-200/50 opacity-70 cursor-not-allowed' : 'bg-white border-slate-200/70 focus:border-indigo-500'
                                    ]"
                                    placeholder="Ketikan catatan untuk siswa ini..."
                                ></textarea>
                                <div class="absolute bottom-2 right-3 pointer-events-none transition-opacity duration-300" 
                                    :class="savingStatus[siswa.id] ? 'opacity-100' : 'opacity-0'">
                                    <span v-if="savingStatus[siswa.id] === 'saving'" class="flex items-center text-[10px] font-bold text-indigo-500 bg-white/90 backdrop-blur px-2 py-0.5 rounded shadow-sm">
                                        <svg class="animate-spin -ml-1 mr-1.5 h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Menyimpan...
                                    </span>
                                    <span v-else-if="savingStatus[siswa.id] === 'saved'" class="flex items-center text-[10px] font-bold text-emerald-500 bg-white/90 backdrop-blur px-2 py-0.5 rounded shadow-sm">
                                        ✓ Tersimpan
                                    </span>
                                    <span v-else-if="savingStatus[siswa.id] === 'error'" class="flex items-center text-[10px] font-bold text-rose-500 bg-white/90 backdrop-blur px-2 py-0.5 rounded shadow-sm">
                                        ⚠️ Gagal
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filteredSiswa.length === 0">
                        <td :colspan="activeTitimangsaData && activeTitimangsaData.is_akhir_tahun ? 4 : 3" class="py-12 text-center text-slate-500 bg-slate-50/50">
                            <div class="text-3xl mb-3 opacity-50">🔍</div>
                            <div class="text-xs font-bold">Tidak ada siswa yang sesuai.</div>
                        </td>
                    </tr>
                  </template>
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
import { ref, computed, onMounted } from 'vue'

definePageMeta({
    layout: 'guru',
    middleware: 'guru',
    title: 'Catatan Wali Kelas'
})

const pageData = ref(null)
const pending = ref(true)
const error = ref(null)
const activeTab = ref(null)
const searchQuery = ref('')

// State untuk menyimpan status per siswa
const savingStatus = ref({})
const saveTimeouts = ref({})

const activeTitimangsaData = computed(() => {
    if (!pageData.value || !activeTab.value) return null
    return pageData.value.titimangsas.find(t => t.id === activeTab.value)
})

const filteredSiswa = computed(() => {
    if (!activeTitimangsaData.value || !activeTitimangsaData.value.siswa) return []
    if (!searchQuery.value) return activeTitimangsaData.value.siswa
    
    const q = searchQuery.value.toLowerCase()
    return activeTitimangsaData.value.siswa.filter(s => 
        s.nama_siswa && s.nama_siswa.toLowerCase().includes(q)
    )
})

const fetchData = async () => {
    pending.value = true
    error.value = null
    try {
        const token = useCookie('auth_token')
        const res = await $fetch(`http://localhost:8000/api/guru/walas/rekap`, {
            headers: {
                'Authorization': `Bearer ${token.value}`
            }
        })
        
        if (res.success) {
            pageData.value = res.data
            if (res.data.titimangsas.length > 0) {
                activeTab.value = res.data.titimangsas[0].id
            }
        } else {
            throw new Error(res.message || 'Gagal memuat data')
        }
    } catch (err) {
        error.value = err
    } finally {
        pending.value = false
    }
}

const handleInput = (siswa) => {
    savingStatus.value[siswa.id] = 'saving'
    if (saveTimeouts.value[siswa.id]) {
        clearTimeout(saveTimeouts.value[siswa.id])
    }
    saveTimeouts.value[siswa.id] = setTimeout(() => {
        saveCatatan(siswa.id, siswa.catatan, siswa.rekomendasi_kenaikan)
    }, 1000)
}

const saveCatatan = async (siswaId, catatanText, rekomendasi) => {
    savingStatus.value[siswaId] = 'saving'
    try {
        const token = useCookie('auth_token')
        const payload = {
            siswa_id: siswaId,
            titimangsa_id: activeTab.value,
            catatan: catatanText,
            rekomendasi_kenaikan: rekomendasi
        }

        const res = await $fetch(`http://localhost:8000/api/guru/walas/rekap/catatan`, {
            method: 'POST',
            headers: { Authorization: `Bearer ${token.value}` },
            body: payload
        })

        if (res.success) {
            savingStatus.value[siswaId] = 'saved'
            setTimeout(() => {
                if (savingStatus.value[siswaId] === 'saved') {
                    savingStatus.value[siswaId] = null
                }
            }, 2000)
        } else {
            savingStatus.value[siswaId] = 'error'
        }
    } catch (err) {
        savingStatus.value[siswaId] = 'error'
    }
}

const saveKurikulum = async (kurikulumId) => {
    try {
        const token = useCookie('auth_token')
        // We reuse the ekskul endpoint since it just updates the class' kurikulum_id
        const res = await $fetch('http://localhost:8000/api/guru/walas/ekskul/kurikulum', {
            method: 'POST',
            headers: { Authorization: `Bearer ${token.value}` },
            body: {
                kurikulum_id: kurikulumId
            }
        })

        if (res.success) {
            useNuxtApp().$swal.fire({
                title: 'Berhasil',
                text: 'Kurikulum berhasil diubah.',
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })
        } else {
            throw new Error(res.message || 'Gagal merubah kurikulum.')
        }
    } catch (err) {
        useNuxtApp().$swal.fire({
            title: 'Gagal',
            text: err.data?.message || err.message || 'Terjadi kesalahan jaringan.',
            icon: 'error',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        })
    }
}

onMounted(() => {
    fetchData()
})
</script>

<style scoped>
/* Custom Scrollbar for Tabs and Textareas */
.custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
