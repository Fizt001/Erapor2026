<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Rekap Absensi Poin & Catatan</h2>
        <p class="text-sm font-semibold text-slate-500 mt-1 mb-3">Tinjau rekap absensi dari BK dan masukkan catatan wali kelas.</p>

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
            <div class="flex border-b border-slate-200 bg-slate-50 overflow-x-auto custom-scrollbar">
                <button 
                    v-for="titimangsa in pageData.titimangsas" 
                    :key="titimangsa.id"
                    @click="activeTab = titimangsa.id"
                    :class="[
                        'px-8 py-4 text-sm font-black transition-all whitespace-nowrap outline-none relative',
                        activeTab === titimangsa.id 
                            ? 'text-teal-700 bg-white' 
                            : 'text-slate-400 hover:text-slate-600 hover:bg-slate-100/50',
                        !titimangsa.is_aktif ? 'opacity-60' : ''
                    ]"
                >
                    <span v-if="!titimangsa.is_aktif" class="mr-1">🔒</span>
                    {{ titimangsa.nama_periode }}
                    <div v-if="activeTab === titimangsa.id" class="absolute bottom-0 left-0 w-full h-0.5 bg-teal-500 rounded-t-full"></div>
                </button>
            </div>
            
            <!-- Warning Closed -->
            <div v-if="activeTitimangsaData && !activeTitimangsaData.is_aktif" class="bg-amber-50 border-b border-amber-100 px-6 py-3 flex items-center gap-3">
                <span class="text-amber-500">🔒</span>
                <p class="text-xs font-bold text-amber-700">Periode ini sudah ditutup. Catatan wali kelas bersifat Read-Only.</p>
            </div>

            <!-- Tab Content (Matrix Table) -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-[10px] uppercase text-slate-400 bg-slate-50/50 font-black tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4 w-12 text-center border-r border-slate-100">No</th>
                            <th class="px-6 py-4 min-w-[200px] border-r border-slate-100">Nama Siswa</th>
                            <th class="px-4 py-4 w-20 text-center border-r border-slate-100" title="Sakit">S</th>
                            <th class="px-4 py-4 w-20 text-center border-r border-slate-100" title="Izin">I</th>
                            <th class="px-4 py-4 w-20 text-center border-r border-slate-100" title="Alpha">A</th>
                            <th class="px-6 py-4 min-w-[300px]">Catatan Wali Kelas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template v-if="activeTitimangsaData">
                            <tr 
                                v-for="(siswa, idx) in activeTitimangsaData.siswa" 
                                :key="siswa.id"
                                class="hover:bg-slate-50/50 transition-colors group"
                            >
                                <td class="px-6 py-4 text-center font-semibold text-slate-400 border-r border-slate-100">
                                    {{ idx + 1 }}
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-700 border-r border-slate-100">
                                    {{ siswa.nama_siswa }}
                                </td>
                                <td class="px-4 py-4 text-center border-r border-slate-100 bg-slate-50">
                                    <span class="font-bold text-slate-500">{{ siswa.absensi.s }}</span>
                                </td>
                                <td class="px-4 py-4 text-center border-r border-slate-100 bg-slate-50">
                                    <span class="font-bold text-slate-500">{{ siswa.absensi.i }}</span>
                                </td>
                                <td class="px-4 py-4 text-center border-r border-slate-100 bg-slate-50">
                                    <span class="font-bold text-rose-500">{{ siswa.absensi.a }}</span>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <textarea
                                        v-model="siswa.catatan"
                                        :disabled="!activeTitimangsaData.is_aktif"
                                        @blur="saveCatatan(siswa.id, siswa.catatan)"
                                        :class="[
                                            'w-full text-sm text-slate-700 placeholder-slate-300 border-0 focus:ring-2 focus:ring-teal-500 rounded-lg p-3 min-h-[60px] transition-all resize-none custom-scrollbar',
                                            !activeTitimangsaData.is_aktif ? 'bg-slate-100 opacity-70 cursor-not-allowed' : 'bg-slate-50 hover:bg-white focus:bg-white'
                                        ]"
                                        placeholder="Ketikan catatan untuk siswa ini..."
                                    ></textarea>
                                </td>
                            </tr>
                            <tr v-if="activeTitimangsaData.siswa.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-4xl mb-3 opacity-20">📭</div>
                                    <p class="text-slate-400 font-semibold text-sm">Tidak ada data siswa di kelas ini.</p>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="bg-slate-50 p-4 border-t border-slate-200 flex items-center justify-between">
                <p class="text-xs font-semibold text-slate-500 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                    Perubahan otomatis tersimpan
                </p>
                <div class="flex gap-2">
                    <span v-if="isSaving" class="text-xs font-bold text-amber-500 px-3 py-1.5 bg-amber-50 rounded-lg flex items-center gap-2">
                        <div class="w-3 h-3 border-2 border-amber-500 border-t-transparent rounded-full animate-spin"></div>
                        Menyimpan...
                    </span>
                    <span v-if="saveSuccess" class="text-xs font-bold text-teal-600 px-3 py-1.5 bg-teal-50 rounded-lg animate-fadeIn">
                        ✓ Tersimpan
                    </span>
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
    middleware: 'guru'
})

const config = useRuntimeConfig()
const pageData = ref(null)
const pending = ref(true)
const error = ref(null)
const activeTab = ref(null)
const isSaving = ref(false)
const saveSuccess = ref(false)

const activeTitimangsaData = computed(() => {
    if (!pageData.value || !activeTab.value) return null
    return pageData.value.titimangsas.find(t => t.id === activeTab.value)
})

const displayToast = (message, type = 'success') => {
    if (process.client) {
        window.dispatchEvent(new CustomEvent('toast', { detail: { message, type } }))
    }
}

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

const saveCatatan = async (siswaId, catatanText) => {
    isSaving.value = true
    saveSuccess.value = false
    
    try {
        const token = useCookie('auth_token')
        const payload = {
            siswa_id: siswaId,
            titimangsa_id: activeTab.value,
            catatan: catatanText
        }

        const res = await $fetch(`http://localhost:8000/api/guru/walas/rekap/catatan`, {
            method: 'POST',
            headers: { Authorization: `Bearer ${token.value}` },
            body: payload
        })

        if (res.success) {
            saveSuccess.value = true
            setTimeout(() => {
                saveSuccess.value = false
            }, 2000)
        } else {
            displayToast(res.message || 'Gagal menyimpan catatan.', 'error')
        }
    } catch (err) {
        displayToast(err.data?.message || 'Terjadi kesalahan jaringan.', 'error')
    } finally {
        isSaving.value = false
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
            displayToast('Kurikulum berhasil diubah!', 'success')
        } else {
            displayToast(res.message || 'Gagal merubah kurikulum.', 'error')
        }
    } catch (err) {
        displayToast(err.data?.message || 'Terjadi kesalahan jaringan.', 'error')
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

/* Custom Scrollbar for Tabs and Textareas */
.custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
