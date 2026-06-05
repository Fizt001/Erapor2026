<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Ekstrakurikuler</h2>
        <p class="text-sm font-semibold text-slate-500 mt-1 mb-3">Input nilai kegiatan ekstrakurikuler peserta didik.</p>

        <!-- Compact Info Badges -->
        <div v-if="pageData" class="flex flex-wrap items-center gap-2 animate-fadeIn">
            <div class="px-3 py-1.5 bg-white shadow-sm rounded-lg flex items-center gap-2 border border-slate-200">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Kelas:</span>
                <span class="text-xs font-bold text-slate-700">{{ pageData.kelas?.tingkat }} {{ pageData.kelas?.nama_kelas }}</span>
            </div>
            <div class="px-3 py-1.5 bg-white shadow-sm rounded-lg flex items-center gap-2 border border-slate-200">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Tahun:</span>
                <span class="text-xs font-bold text-slate-700">{{ pageData.tahun_ajaran?.tahun }}</span>
            </div>
            <div class="px-3 py-1.5 bg-white shadow-sm rounded-lg flex items-center gap-2 border border-slate-200">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Kurikulum:</span>
                <select 
                    v-model="pageData.kelas.kurikulum_id"
                    @change="saveKurikulum(pageData.kelas.kurikulum_id)"
                    class="text-xs font-bold text-slate-700 bg-transparent border-none p-0 focus:ring-0 cursor-pointer outline-none min-w-[120px]"
                >
                    <option v-for="kuri in pageData.master_kurikulum" :key="kuri.id" :value="kuri.id">{{ kuri.nama_kurikulum }}</option>
                </select>
            </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="pending" class="flex items-center justify-center min-h-[400px]">
        <div class="flex flex-col items-center opacity-60">
            <div class="w-10 h-10 border-4 border-teal-500 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</span>
        </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-rose-50 border border-rose-200 rounded-2xl p-8 text-center min-h-[400px] flex flex-col items-center justify-center">
        <span class="text-4xl mb-4">🔒</span>
        <h3 class="text-lg font-black text-rose-700">Akses Ditolak</h3>
        <p class="text-sm font-semibold text-rose-500 mt-2 max-w-md">{{ error.message || 'Terjadi kesalahan saat memuat data.' }}</p>
        <button @click="fetchData" class="mt-6 px-6 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm rounded-lg shadow-sm transition-colors">Coba Lagi</button>
    </div>

    <!-- Main Content -->
    <div v-else-if="pageData" class="flex flex-col gap-6">
        
        <!-- Matrix Editor Container -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            
            <!-- Tabs Titimangsa -->
            <div class="flex overflow-x-auto border-b border-slate-200 bg-slate-50 custom-scrollbar">
                <button 
                    v-for="tm in pageData.titimangsas" 
                    :key="tm.id"
                    @click="activeTab = tm.id"
                    :class="[
                        activeTab === tm.id ? 'bg-white text-teal-600 border-b-2 border-teal-500' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700',
                        (!tm.is_aktif && tm.is_aktif !== '1' && tm.is_aktif !== 1) ? 'opacity-60' : ''
                    ]"
                    class="px-6 py-4 text-xs font-black uppercase tracking-wider transition-all whitespace-nowrap outline-none relative z-10 flex-1 text-center"
                >
                    <span v-if="(!tm.is_aktif && tm.is_aktif !== '1' && tm.is_aktif !== 1)" class="mr-1">🔒</span>
                    {{ tm.nama_periode }}
                </button>
            </div>

            <!-- Form Matrix -->
            <div class="p-0 overflow-x-auto relative min-h-[300px]">
                
                <!-- Read Only Warning Banner -->
                <div v-if="!isTabActive" class="bg-rose-50 border-b border-rose-200 px-6 py-3 flex items-center justify-center gap-3">
                    <span class="text-rose-500 text-lg">🔒</span>
                    <span class="text-[11px] font-black text-rose-600 uppercase tracking-widest">Periode ini telah ditutup oleh Kurikulum. Data hanya dapat dilihat (Read-Only).</span>
                </div>

                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr>
                            <th class="py-4 px-6 border-b border-slate-200 bg-white sticky left-0 z-30 shadow-[1px_0_0_0_#e2e8f0] text-[10px] font-black text-slate-400 uppercase tracking-widest min-w-[200px] w-1/3">
                                Nama Peserta Didik
                            </th>
                            <th class="py-4 px-4 border-b border-l border-emerald-100 text-center bg-emerald-50 w-24">
                                <span class="text-[10px] font-black text-emerald-700 uppercase tracking-widest block mb-1">Pramuka (Wajib)</span>
                                <span class="text-[9px] font-bold text-emerald-600/70 uppercase">Nilai</span>
                            </th>
                            <th class="py-4 px-4 border-b border-l border-indigo-100 text-center bg-indigo-50" colspan="2">
                                <span class="text-[10px] font-black text-indigo-700 uppercase tracking-widest block mb-1">Ekskul Pilihan 1</span>
                                <div class="flex justify-between w-full">
                                    <span class="text-[9px] font-bold text-indigo-600/70 uppercase w-2/3">Nama Ekskul</span>
                                    <span class="text-[9px] font-bold text-indigo-600/70 uppercase w-1/3">Nilai</span>
                                </div>
                            </th>
                            <th class="py-4 px-4 border-b border-l border-amber-100 text-center bg-amber-50" colspan="2">
                                <span class="text-[10px] font-black text-amber-700 uppercase tracking-widest block mb-1">Ekskul Pilihan 2</span>
                                <div class="flex justify-between w-full">
                                    <span class="text-[9px] font-bold text-amber-600/70 uppercase w-2/3">Nama Ekskul</span>
                                    <span class="text-[9px] font-bold text-amber-600/70 uppercase w-1/3">Nilai</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <tr v-for="siswa in pageData.siswas" :key="siswa.id" class="hover:bg-slate-50 transition-colors group">
                            
                            <!-- Nama Siswa (Sticky) -->
                            <td class="py-3 px-6 text-[11px] font-black text-slate-700 uppercase bg-white group-hover:bg-slate-50 sticky left-0 z-20 shadow-[1px_0_0_0_#f1f5f9] transition-colors">
                                {{ siswa.nama }}
                            </td>
                            
                            <!-- PRAMUKA -->
                            <td class="py-2 px-4 border-l border-emerald-50 bg-emerald-50/30">
                                <input 
                                    v-model="formEkskul[siswa.id][activeTab].pramuka_nilai" 
                                    type="number" 
                                    min="0" max="100" 
                                    placeholder="-" 
                                    :disabled="!isTabActive"
                                    class="w-16 h-8 rounded-lg border-emerald-200 text-xs font-bold text-center mx-auto block py-0 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-white placeholder-slate-300 transition-all outline-none disabled:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                            </td>

                            <!-- PILIHAN 1 -->
                            <td class="py-2 px-4 border-l border-indigo-50 bg-indigo-50/20 pr-2">
                                <select 
                                    v-model="formEkskul[siswa.id][activeTab].pilihan1_id"
                                    :disabled="!isTabActive"
                                    class="w-full h-8 rounded-lg border-indigo-200 text-[10px] font-bold py-0 pl-2 pr-6 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer bg-white transition-all outline-none disabled:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <option :value="null">-- Pilih Ekskul --</option>
                                    <option v-for="me in pilihanEkskuls" :key="me.id" :value="me.id">{{ me.nama_ekskul }}</option>
                                </select>
                            </td>
                            <td class="py-2 px-4 pl-2 bg-indigo-50/20">
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
                            <td class="py-2 px-4 border-l border-amber-50 bg-amber-50/20 pr-2">
                                <select 
                                    v-model="formEkskul[siswa.id][activeTab].pilihan2_id"
                                    :disabled="!isTabActive"
                                    class="w-full h-8 rounded-lg border-amber-200 text-[10px] font-bold py-0 pl-2 pr-6 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 cursor-pointer bg-white transition-all outline-none disabled:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <option :value="null">-- Pilih Ekskul --</option>
                                    <option v-for="me in pilihanEkskuls" :key="me.id" :value="me.id">{{ me.nama_ekskul }}</option>
                                </select>
                            </td>
                            <td class="py-2 px-4 pl-2 bg-amber-50/20">
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
                    </tbody>
                </table>
            </div>
            
            <!-- Save Action -->
            <div class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end">
                <button 
                    @click="submitEkskul" 
                    :disabled="isSaving || (!isTabActive)"
                    class="px-8 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-teal-500/20 transition-all flex items-center gap-3 disabled:opacity-50 active:scale-95 disabled:cursor-not-allowed"
                >
                    <span v-if="isSaving" class="animate-spin">⏳</span>
                    <span v-else>💾</span>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div v-if="showToast" class="fixed bottom-10 left-1/2 -translate-x-1/2 bg-slate-900/95 backdrop-blur-md text-white px-6 py-4 rounded-full shadow-2xl flex items-center gap-4 z-[100] animate-slideUp">
      <div :class="toastType === 'success' ? 'from-emerald-400 to-emerald-500' : 'from-rose-400 to-rose-500'" class="w-8 h-8 bg-gradient-to-br rounded-full flex items-center justify-center text-white text-sm shadow-inner shadow-white/20">
          {{ toastType === 'success' ? '✓' : '✕' }}
      </div>
      <p class="font-bold text-sm tracking-wide pr-2">{{ toastMessage }}</p>
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

// Toast State
const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('success')

const activeTitimangsaData = computed(() => {
    if (!pageData.value || !activeTab.value) return null
    return pageData.value.titimangsas.find(tm => tm.id === activeTab.value)
})

const isTabActive = computed(() => {
    if (!activeTitimangsaData.value) return false;
    const val = activeTitimangsaData.value.is_aktif;
    return val === 1 || val === '1' || val === true || val === 'true';
})

const displayToast = (msg, type = 'success') => {
    toastMessage.value = msg
    toastType.value = type
    showToast.value = true
    setTimeout(() => { showToast.value = false }, 3500)
}

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
            displayToast('Kurikulum berhasil diubah!')
        } else {
            displayToast(res.message || 'Gagal merubah kurikulum.', 'error')
        }
    } catch (err) {
        displayToast(err.data?.message || 'Terjadi kesalahan jaringan.', 'error')
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
            displayToast(res.message || 'Data Ekstrakurikuler berhasil disimpan!', 'success')
            fetchData() // Re-sync local data
        } else {
            throw new Error(res.message)
        }
    } catch (err) {
        displayToast(err.data?.message || err.message || 'Terjadi kesalahan saat menyimpan data.', 'error')
    } finally {
        isSaving.value = false
    }
}


onMounted(() => {
    fetchData()
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }

@keyframes slideUp {
  from { opacity: 0; transform: translate(-50%, 20px); }
  to { opacity: 1; transform: translate(-50%, 0); }
}
.animate-slideUp { animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

/* Custom Scrollbar for Tabs and Table */
.custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
