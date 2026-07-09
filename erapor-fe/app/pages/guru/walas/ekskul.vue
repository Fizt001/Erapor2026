<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex flex-col xl:flex-row overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="xl:w-[360px] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] overflow-y-auto custom-scrollbar">
        <div class="p-6 space-y-6">
          <div class="bg-gradient-to-r from-sky-600 to-blue-700 rounded-2xl p-5 border border-sky-500 shadow-sm relative overflow-hidden">
            <div class="relative z-10">
              <h3 class="text-sm font-black uppercase tracking-widest text-white">Ekstrakurikuler</h3>
              <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Input nilai kegiatan ekstrakurikuler</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-10">
              <svg class="w-24 h-24 transform translate-x-6 translate-y-6" fill="currentColor" viewBox="0 0 24 24"><path d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
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
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Kurikulum</span>
                <select 
                    v-model="pageData.kelas.kurikulum_id"
                    @change="saveKurikulum(pageData.kelas.kurikulum_id)"
                    class="text-[11px] font-bold text-slate-700 bg-transparent border-none p-0 pr-4 focus:ring-0 cursor-pointer outline-none text-right"
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
                    class="w-full pl-10 pr-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          
          <!-- Loading State -->
          <div v-if="pending" class="flex-grow flex flex-col items-center justify-center p-20 opacity-60 bg-white rounded-3xl shadow-sm border border-slate-200/60">
            <div class="w-10 h-10 border-4 border-teal-500 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Memuat Data...</span>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="flex-grow flex flex-col items-center justify-center p-16 text-center bg-white rounded-3xl shadow-sm border border-slate-200/60">
            <div class="text-rose-500 text-4xl mb-4">🔒</div>
            <h3 class="text-rose-800 font-black mb-1">Akses Ditolak</h3>
            <p class="text-rose-600 text-sm font-semibold max-w-md">{{ error.message || 'Terjadi kesalahan saat memuat data.' }}</p>
            <button @click="fetchData" class="mt-6 px-6 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm rounded-lg shadow-sm transition-colors">Coba Lagi</button>
          </div>

          <!-- Matrix Editor Container -->
          <div v-else-if="pageData" class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            
            <!-- Tabs Titimangsa -->
            <div class="flex overflow-x-auto border-b border-slate-200 bg-slate-50 custom-scrollbar shrink-0">
                <button 
                    v-for="tm in pageData.titimangsas" 
                    :key="tm.id"
                    @click="activeTab = tm.id"
                    :class="[
                        activeTab === tm.id ? 'bg-white text-teal-600 border-b-2 border-teal-500' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700',
                        (!tm.is_aktif && tm.is_aktif !== '1' && tm.is_aktif !== 1) ? 'opacity-60' : ''
                    ]"
                    class="px-8 py-4 text-[11px] font-black uppercase tracking-wider transition-all whitespace-nowrap outline-none relative z-10 flex-1 text-center border-r border-slate-200 last:border-r-0"
                >
                    <span v-if="(!tm.is_aktif && tm.is_aktif !== '1' && tm.is_aktif !== 1)" class="mr-1">🔒</span>
                    {{ tm.nama_periode_panjang || tm.nama_periode }}
                </button>
            </div>

            <!-- Form Matrix -->
            <div class="flex-1 overflow-auto custom-scrollbar relative bg-slate-50/30 flex flex-col">
                
                <!-- Read Only Warning Banner -->
                <div v-if="!isTabActive" class="bg-rose-50 border-b border-rose-200 px-6 py-3 flex items-center justify-center gap-3 shrink-0 shadow-sm z-30 sticky top-0">
                    <span class="text-rose-500 text-lg leading-none">🔒</span>
                    <span class="text-[10px] font-black text-rose-700 uppercase tracking-widest">Periode ini telah ditutup oleh Kurikulum. Data hanya dapat dilihat (Read-Only).</span>
                </div>

                <table class="w-full text-left border-collapse min-w-[800px] bg-white">
                    <thead class="sticky top-0 z-20 shadow-sm">
                        <tr>
                            <th rowspan="2" class="py-3 px-6 border-b border-slate-300 bg-slate-100 sticky left-0 z-30 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] border-r text-[10px] font-black text-slate-500 uppercase tracking-widest min-w-[200px] align-middle">
                                Nama Peserta Didik
                            </th>
                            <th class="py-2 px-2 border-b border-l border-emerald-300 text-center bg-emerald-50 w-24 border-r">
                                <span class="text-[10px] font-black text-emerald-700 uppercase tracking-widest block">Pramuka (Wajib)</span>
                            </th>
                            <th class="py-2 px-2 border-b border-l border-indigo-300 text-center bg-indigo-50 border-r" colspan="2">
                                <span class="text-[10px] font-black text-indigo-700 uppercase tracking-widest block">Ekskul Pilihan 1</span>
                            </th>
                            <th class="py-2 px-2 border-b border-l border-amber-300 text-center bg-amber-50" colspan="2">
                                <span class="text-[10px] font-black text-amber-700 uppercase tracking-widest block">Ekskul Pilihan 2</span>
                            </th>
                        </tr>
                        <tr>
                            <th class="py-1.5 px-2 border-b border-l border-emerald-300 text-center bg-emerald-100/50 w-24 border-r">
                                <span class="text-[9px] font-bold text-emerald-600 uppercase">Nilai</span>
                            </th>
                            <th class="py-1.5 px-2 border-b border-l border-indigo-300 text-center bg-indigo-100/50">
                                <span class="text-[9px] font-bold text-indigo-600 uppercase">Nama Ekskul</span>
                            </th>
                            <th class="py-1.5 px-2 border-b border-indigo-300 text-center bg-indigo-100/50 border-r w-20">
                                <span class="text-[9px] font-bold text-indigo-600 uppercase">Nilai</span>
                            </th>
                            <th class="py-1.5 px-2 border-b border-l border-amber-300 text-center bg-amber-100/50">
                                <span class="text-[9px] font-bold text-amber-600 uppercase">Nama Ekskul</span>
                            </th>
                            <th class="py-1.5 px-2 border-b border-amber-300 text-center bg-amber-100/50 w-20">
                                <span class="text-[9px] font-bold text-amber-600 uppercase">Nilai</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="siswa in filteredStudents" :key="activeTab + '-' + siswa.id" class="hover:bg-sky-50/30 transition-colors group">
                            
                            <!-- Nama Siswa (Sticky) -->
                            <td class="py-3 px-6 text-[11px] font-black text-slate-700 uppercase bg-white group-hover:bg-slate-50 sticky left-0 z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.02)] transition-colors border-r border-slate-100">
                                {{ siswa.nama }}
                            </td>
                            
                            <!-- PRAMUKA -->
                            <td class="py-2 px-2 border-r border-slate-100 bg-emerald-50/10">
                                <input 
                                    v-model="formEkskul[siswa.id][activeTab].pramuka_nilai" 
                                    type="number" 
                                    min="0" max="100" 
                                    placeholder="-" 
                                    :disabled="!isTabActive"
                                    class="w-14 h-8 rounded-lg border-emerald-200 text-xs font-bold text-center mx-auto block py-0 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-white placeholder-slate-300 transition-all outline-none disabled:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                            </td>

                            <!-- PILIHAN 1 -->
                            <td class="py-2 px-2 bg-indigo-50/10">
                                <select 
                                    v-model="formEkskul[siswa.id][activeTab].pilihan1_id"
                                    :disabled="!isTabActive"
                                    class="w-full h-8 rounded-lg border-indigo-200 text-[10px] font-bold py-0 px-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer bg-white transition-all outline-none disabled:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <option :value="null">-- Pilih --</option>
                                    <option v-for="me in pilihanEkskuls" :key="me.id" :value="me.id">{{ me.nama_ekskul }}</option>
                                </select>
                            </td>
                            <td class="py-2 px-2 border-r border-slate-100 bg-indigo-50/10">
                                <input 
                                    v-model="formEkskul[siswa.id][activeTab].pilihan1_nilai" 
                                    type="number" 
                                    min="0" max="100" 
                                    placeholder="-" 
                                    class="w-14 h-8 rounded-lg border-indigo-200 text-xs font-bold text-center mx-auto block py-0 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white placeholder-slate-300 transition-all outline-none disabled:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                    :disabled="(!isTabActive) || !formEkskul[siswa.id][activeTab].pilihan1_id"
                                >
                            </td>

                            <!-- PILIHAN 2 -->
                            <td class="py-2 px-2 bg-amber-50/10">
                                <select 
                                    v-model="formEkskul[siswa.id][activeTab].pilihan2_id"
                                    :disabled="!isTabActive"
                                    class="w-full h-8 rounded-lg border-amber-200 text-[10px] font-bold py-0 px-2 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 cursor-pointer bg-white transition-all outline-none disabled:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <option :value="null">-- Pilih --</option>
                                    <option v-for="me in pilihanEkskuls" :key="me.id" :value="me.id">{{ me.nama_ekskul }}</option>
                                </select>
                            </td>
                            <td class="py-2 px-2 bg-amber-50/10">
                                <input 
                                    v-model="formEkskul[siswa.id][activeTab].pilihan2_nilai" 
                                    type="number" 
                                    min="0" max="100" 
                                    placeholder="-" 
                                    class="w-14 h-8 rounded-lg border-amber-200 text-xs font-bold text-center mx-auto block py-0 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 bg-white placeholder-slate-300 transition-all outline-none disabled:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                    :disabled="(!isTabActive) || !formEkskul[siswa.id][activeTab].pilihan2_id"
                                >
                            </td>

                        </tr>
                        <tr v-if="filteredStudents.length === 0">
                            <td colspan="6" class="py-12 text-center text-slate-500 bg-slate-50/50">
                                <div class="text-3xl mb-2">🔍</div>
                                <div class="text-xs font-bold">Tidak ada siswa yang cocok.</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Save Action -->
            <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end shrink-0">
                <button 
                    @click="submitEkskul" 
                    :disabled="isSaving || (!isTabActive)"
                    class="px-8 py-3.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-[11px] font-black uppercase tracking-widest shadow-md hover:shadow-lg transition-all active:scale-95 flex items-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="isSaving" class="animate-spin">⏳</span>
                    <span v-else>💾</span>
                    Simpan Perubahan
                </button>
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
  title: 'Ekstrakurikuler'
})

const token = useCookie('auth_token')
const pending = ref(true)
const error = ref(null)
const pageData = ref(null)
const activeTab = ref(null)
const formEkskul = ref({})
const isSaving = ref(false)
const searchQuery = ref('')

const filteredStudents = computed(() => {
    if (!pageData.value || !pageData.value.siswas) return []
    if (!searchQuery.value) return pageData.value.siswas
    const q = searchQuery.value.toLowerCase()
    return pageData.value.siswas.filter(s => s.nama && s.nama.toLowerCase().includes(q))
})

const activeTitimangsaData = computed(() => {
    if (!pageData.value || !activeTab.value) return null
    return pageData.value.titimangsas.find(tm => tm.id === activeTab.value)
})

const isTabActive = computed(() => {
    if (!activeTitimangsaData.value) return false;
    const val = activeTitimangsaData.value.is_aktif;
    return val === 1 || val === '1' || val === true || val === 'true';
})

const pilihanEkskuls = computed(() => {
    if (!pageData.value) return []
    return pageData.value.master_ekskul.filter(me => me.id !== pageData.value.pramuka_id)
})

const initializeForm = (data) => {
    const form = {}
    data.siswas.forEach(siswa => {
        form[siswa.id] = {}
        data.titimangsas.forEach(tm => {
            const saved = data.saved_data?.[siswa.id]?.[tm.id] || {}
            form[siswa.id][tm.id] = {
                pramuka_nilai: saved.pramuka?.nilai ?? null,
                pilihan1_id: saved.pilihan1?.id ?? null,
                pilihan1_nilai: saved.pilihan1?.nilai ?? null,
                pilihan2_id: saved.pilihan2?.id ?? null,
                pilihan2_nilai: saved.pilihan2?.nilai ?? null,
            }
        })
    })
    formEkskul.value = form
}

const fetchData = async () => {
    pending.value = true
    error.value = null
    try {
        const res = await $fetch('http://localhost:8000/api/guru/walas/ekskul', {
            headers: { Authorization: `Bearer ${token.value}` }
        })
        
        if (res.success) {
            pageData.value = res.data
            if (res.data.titimangsas.length > 0) {
                activeTab.value = res.data.titimangsas[0].id
            }
            initializeForm(res.data)
        } else {
            throw new Error(res.message || 'Gagal memuat data')
        }
    } catch (err) {
        error.value = err
    } finally {
        pending.value = false
    }
}

const saveKurikulum = async (kurikulumId) => {
    try {
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
                text: 'Kurikulum berhasil diubah!',
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

const submitEkskul = async () => {
    isSaving.value = true
    try {
        const payload = {
            pramuka_id: pageData.value.pramuka_id,
            ekskul: formEkskul.value
        }

        const res = await $fetch('http://localhost:8000/api/guru/walas/ekskul/store', {
            method: 'POST',
            headers: { Authorization: `Bearer ${token.value}` },
            body: payload
        })

        if (res.success) {
            useNuxtApp().$swal.fire({
                title: 'Berhasil',
                text: res.message || 'Data Ekstrakurikuler berhasil disimpan!',
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            })
            fetchData() // Re-sync local data
        } else {
            throw new Error(res.message)
        }
    } catch (err) {
        useNuxtApp().$swal.fire({
            title: 'Gagal',
            text: err.data?.message || err.message || 'Terjadi kesalahan saat menyimpan data.',
            icon: 'error',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        })
    } finally {
        isSaving.value = false
    }
}

onMounted(() => {
    fetchData()
})
</script>

<style scoped>
/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #94a3b8; }

/* Hide number input arrows */
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
  margin: 0; 
}
input[type=number] {
  -moz-appearance: textfield;
}
</style>
