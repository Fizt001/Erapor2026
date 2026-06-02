<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4 relative">
    
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Master Mata Pelajaran</h2>
        <p class="text-sm font-semibold text-slate-500 mt-1">Kelola data mata pelajaran untuk kategori Umum dan Kejuruan.</p>
      </div>
    </div>

    <!-- MAIN GRID LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      
      <!-- ==============================================
           KIRI: FORM MAPEL (Col 4)
           ============================================== -->
      <div class="lg:col-span-4 space-y-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-100">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">{{ isEditing ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran' }}</h3>
                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Lengkapi form di bawah ini.</p>
            </div>
            
            <div class="p-6">
                <form @submit.prevent="saveData" class="space-y-5">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Kurikulum</label>
                        <select v-model="formData.kurikulum_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-medium text-slate-700 outline-none">
                            <option value="">-- Pilih Kurikulum --</option>
                            <option v-for="kur in kurikulums" :key="kur.id" :value="kur.id">{{ kur.nama_kurikulum }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kategori</label>
                        <select v-model="formData.kategori" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm font-medium text-slate-700 outline-none" @change="fetchData">
                            <option v-for="kat in refKategoriMapel" :key="kat.kode" :value="kat.kode">{{ kat.nama }}</option>
                        </select>
                    </div>

                    <div v-if="refKelompokMapel.length > 0">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kelompok</label>
                        <select v-model="formData.kelompok" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm font-medium text-slate-700 outline-none">
                            <option value="">-- Tanpa Kelompok --</option>
                            <option v-for="kel in refKelompokMapel" :key="kel.kode" :value="kel.kode">{{ kel.nama }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kode Mapel</label>
                        <input type="text" v-model="formData.kode_mapel" required placeholder="Contoh: B.IND, MTK, PAI" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-bold text-slate-800 placeholder:text-slate-400 outline-none placeholder:font-normal">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Mapel</label>
                        <input type="text" v-model="formData.nama_mapel" required placeholder="Nama lengkap mata pelajaran" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-bold text-slate-800 placeholder:text-slate-400 outline-none placeholder:font-normal">
                    </div>
                    
                    <div class="pt-4 flex gap-3">
                        <button v-if="isEditing" type="button" @click="resetForm" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all text-xs uppercase tracking-widest">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSaving" class="flex-[2] py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                            <span v-if="isSaving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                            <span>{{ isEditing ? 'Simpan Perubahan' : 'Tambah Mapel' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
      </div>

      <!-- ==============================================
           KANAN: TABEL MAPEL (Col 8)
           ============================================== -->
      <div class="lg:col-span-8">
         <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col min-h-[600px]">
            
            <div class="p-6 md:p-8 bg-slate-50/50 border-b border-slate-100">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-white shadow-sm border border-slate-200 flex items-center justify-center text-2xl">📚</div>
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-widest text-slate-700">Daftar Mapel</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Filter berdasarkan kategori & kurikulum</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <select v-model="filterKurikulum" @change="fetchData" class="px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 outline-none focus:border-emerald-500 max-w-[150px] md:max-w-xs">
                            <option value="">Semua Kurikulum</option>
                            <option v-for="kur in kurikulums" :key="kur.id" :value="kur.id">{{ kur.nama_kurikulum }}</option>
                        </select>
                        <button @click="fetchData" class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-slate-200 transition-colors" title="Refresh">
                            🔄
                        </button>
                    </div>
                </div>

                <!-- Tabs Filter Kategori -->
                <div class="flex p-2 bg-slate-100 rounded-2xl gap-2 overflow-x-auto">
                    <button v-for="kat in refKategoriMapel" :key="kat.kode"
                        @click="activeTab = kat.kode; fetchData()" 
                        class="flex-1 py-3 px-6 rounded-xl text-sm font-bold transition-all whitespace-nowrap text-center"
                        :class="activeTab === kat.kode ? 'bg-white text-emerald-600 shadow-sm border border-slate-200' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'">
                        {{ kat.nama }}
                    </button>
                    <div v-if="refKategoriMapel.length === 0" class="py-3 px-6 text-sm text-slate-500 font-bold w-full text-center">
                        ⚠️ Silakan isi Master Database untuk Kategori Mapel terlebih dahulu.
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60">
                <div class="w-8 h-8 border-4 border-slate-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Data...</span>
            </div>

            <!-- Empty State -->
            <div v-else-if="!mapels || mapels.length === 0" class="flex-grow flex items-center justify-center flex-col p-16 text-center">
                <div class="text-6xl opacity-20 mb-4">🏜️</div>
                <p class="text-sm font-bold text-slate-500">Belum ada data mata pelajaran.</p>
                <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-2 font-bold">Silakan tambahkan melalui form di samping.</p>
            </div>

            <!-- Table Content for Grouped View (If Referensi Kelompok Exists) -->
            <div v-else-if="mapels.length > 0 && refKelompokMapel.length > 0" class="flex-grow p-6 pt-2 space-y-4">
                <div v-for="(groupList, kelompok) in groupedMapels" :key="kelompok" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div @click="toggleCollapse(kelompok)" class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between cursor-pointer hover:bg-slate-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-md bg-white border border-slate-200 flex items-center justify-center text-xs text-slate-400 font-black shadow-sm">{{ kelompok.charAt(0) }}</span>
                            <h4 class="font-black text-slate-700 text-sm uppercase tracking-widest">Kelompok {{ kelompok }}</h4>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-[10px] font-black uppercase tracking-widest text-indigo-600 bg-indigo-50 px-2 py-1 rounded-md border border-indigo-100">{{ groupList.length }} Mapel</span>
                            <span class="text-slate-400 text-lg transition-transform duration-300" :class="{ 'rotate-180': isCollapsed[kelompok] }">👇</span>
                        </div>
                    </div>
                    
                    <div v-show="!isCollapsed[kelompok]" class="overflow-x-auto">
                        <div v-if="groupList.length === 0" class="p-8 text-center text-slate-400 font-bold text-xs uppercase tracking-widest bg-slate-50/30">
                            Belum ada mapel di kelompok ini.
                        </div>
                        <table v-else class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-white text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-slate-100">
                                    <th class="py-3 px-4 pl-6 w-16 text-center">#</th>
                                    <th class="py-3 px-4">Mata Pelajaran</th>
                                    <th class="py-3 px-4 text-right pr-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr v-for="(item, index) in groupList" :key="item.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors group">
                                    <td class="py-2 px-4 pl-6 text-center text-xs font-bold text-slate-300">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="py-2 px-4">
                                        <p class="font-black text-slate-700 text-xs">{{ item.nama_mapel }}</p>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="text-[9px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-100">{{ item.kode_mapel }}</span>
                                            <span class="text-[9px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded border border-slate-100 bg-indigo-50 text-indigo-600" v-if="item.kurikulum">{{ item.kurikulum.nama_kurikulum }}</span>
                                        </div>
                                    </td>
                                    <td class="py-2 px-4 pr-6 text-right">
                                        <div class="flex items-center justify-end gap-1.5 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                            <button @click.stop="editData(item)" class="w-7 h-7 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-indigo-500 hover:bg-indigo-50 flex items-center justify-center transition-all shadow-sm" title="Edit">✏️</button>
                                            <button @click.stop="confirmDelete(item)" class="w-7 h-7 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:bg-rose-50 flex items-center justify-center transition-all shadow-sm" title="Hapus">🗑️</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Table Content for Flat View -->
            <div v-else class="flex-grow overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-slate-200">
                            <th class="py-3 px-4 pl-6 w-16 text-center">#</th>
                            <th class="py-3 px-4">Mata Pelajaran</th>
                            <th class="py-3 px-4 text-right pr-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr v-for="(item, index) in mapels" :key="item.id" class="border-b border-slate-100 hover:bg-slate-50/80 transition-colors group">
                            <td class="py-2.5 px-4 pl-6 text-center text-xs font-bold text-slate-300">
                                {{ index + 1 }}
                            </td>
                            <td class="py-2.5 px-4">
                                <p class="font-black text-slate-800 text-xs">{{ item.nama_mapel }}</p>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[9px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">{{ item.kode_mapel }}</span>
                                    <span class="text-[9px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded border border-slate-200 bg-indigo-50 text-indigo-600" v-if="item.kurikulum">{{ item.kurikulum.nama_kurikulum }}</span>
                                </div>
                            </td>
                            <td class="py-2.5 px-4 pr-6 text-right">
                                <div class="flex items-center justify-end gap-1.5 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                    <button @click="editData(item)" class="w-7 h-7 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-indigo-500 hover:bg-indigo-50 flex items-center justify-center transition-all shadow-sm" title="Edit">✏️</button>
                                    <button @click="confirmDelete(item)" class="w-7 h-7 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:bg-rose-50 flex items-center justify-center transition-all shadow-sm" title="Hapus">🗑️</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

         </div>
      </div>
    </div>

    <!-- Modal Delete -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade text-center">
            <div class="p-8">
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner border-4 border-white ring-4 ring-rose-50">⚠️</div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Hapus Mapel?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Anda yakin ingin menghapus mapel:<br>
                    <span class="font-bold text-slate-800">{{ deleteTarget?.nama_mapel }}</span>?
                </p>
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isDeleteModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all text-xs uppercase tracking-widest">Batal</button>
                    <button @click="executeDelete" class="flex-1 py-3 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        <span>Hapus</span>
                    </button>
                </div>
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
  layout: 'kurikulum',
  middleware: 'kurikulum',
  title: 'Master Mata Pelajaran'
})

const activeTab = ref('')
const mapels = ref([])
const kurikulums = ref([])
const isLoading = ref(true)
const isSaving = ref(false)

const isEditing = ref(false)
const formData = ref({
    id: null,
    kurikulum_id: '',
    kode_mapel: '',
    nama_mapel: '',
    kategori: 'umum',
    kelompok: ''
})

const groupedMapels = computed(() => {
    const groups = { 
        'Belum Terkelompokkan': [] 
    };
    
    // Initialize groups from referensi
    refKelompokMapel.value.forEach(kel => {
        groups[kel.nama] = [];
    });
    
    mapels.value.forEach(m => {
        const found = refKelompokMapel.value.find(kel => kel.kode === m.kelompok);
        const k = found ? found.nama : 'Belum Terkelompokkan';
        
        if (!groups[k]) groups[k] = [];
        groups[k].push(m);
    });
    
    // Ordering logic
    const result = {};
    refKelompokMapel.value.forEach(kel => {
        // Jika kategori umum, tampilkan semua kelompok walau kosong.
        // Jika bukan umum, hanya tampilkan kelompok yang ada isinya.
        if (activeTab.value === 'umum' || groups[kel.nama].length > 0) {
            result[kel.nama] = groups[kel.nama];
        }
    });
    
    if (groups['Belum Terkelompokkan'].length > 0) {
        result['Belum Terkelompokkan'] = groups['Belum Terkelompokkan'];
    }
    return result;
});

const isCollapsed = ref({})
const toggleCollapse = (k) => {
    isCollapsed.value[k] = !isCollapsed.value[k]
}

const isDeleteModalOpen = ref(false)
const deleteTarget = ref(null)

const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('success')

const refKategoriMapel = ref([])
const refKelompokMapel = ref([])

const fetchReferensi = async () => {
    const token = useCookie('auth_token').value
    try {
        const timestamp = Date.now()
        const [resKat, resKel] = await Promise.all([
            $fetch(`http://localhost:8000/api/admin/referensi?jenis=kategori_mapel&t=${timestamp}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            }),
            $fetch(`http://localhost:8000/api/admin/referensi?jenis=kelompok_mapel&t=${timestamp}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            })
        ])
        const rawKategori = resKat.data || []
        
        // Urutkan kategori sesuai request user: umum -> produktif -> industri -> lainnya
        const sortOrder = ['umum', 'produktif', 'industri']
        refKategoriMapel.value = rawKategori.sort((a, b) => {
            const indexA = sortOrder.indexOf(a.kode.toLowerCase())
            const indexB = sortOrder.indexOf(b.kode.toLowerCase())
            
            if (indexA !== -1 && indexB !== -1) return indexA - indexB
            if (indexA !== -1) return -1
            if (indexB !== -1) return 1
            // Sisa urutkan berdasarkan nama
            return a.nama.localeCompare(b.nama)
        })
        
        refKelompokMapel.value = resKel.data || []
        
        if (refKategoriMapel.value.length > 0) {
            activeTab.value = refKategoriMapel.value[0].kode
        }
        
    } catch (error) {
        console.error('Error fetching referensi', error)
    }
}

// Removed fetchKurikulums

const filterKurikulum = ref('')

const fetchData = async () => {
    if (!activeTab.value) return
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        let url = `http://localhost:8000/api/kurikulum/mapel?kategori=${activeTab.value}`
        if (filterKurikulum.value) {
            url += `&kurikulum_id=${filterKurikulum.value}`
        }
        const response = await $fetch(url, {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            mapels.value = response.data
            kurikulums.value = response.kurikulums || []
            formData.value.kategori = activeTab.value 
        }
    } catch (error) {
        console.error('Failed to fetch mapel:', error)
    } finally {
        isLoading.value = false
    }
}

const saveData = async () => {
    isSaving.value = true
    const token = useCookie('auth_token').value
    const url = isEditing.value 
        ? `http://localhost:8000/api/kurikulum/mapel/${formData.value.id}` 
        : `http://localhost:8000/api/kurikulum/mapel`
    const method = isEditing.value ? 'PUT' : 'POST'

    try {
        const response = await $fetch(url, {
            method: method,
            headers: { Authorization: `Bearer ${token}` },
            body: formData.value
        })
        
        if (response.success) {
            displayToast(response.message, 'success')
            activeTab.value = formData.value.kategori
            resetForm()
            fetchData()
        }
    } catch (error) {
        console.error('Save error:', error)
        let errMsg = 'Gagal menyimpan data mapel.';
        if (error.response && error.response.status === 422) {
            errMsg = 'Mohon periksa kembali isian form Anda.';
            if (error.response._data?.message) errMsg = error.response._data.message;
        } else if (error.response) {
            errMsg = `Gagal menyimpan: Terjadi kesalahan pada server (${error.response.status}).`;
        }
        displayToast(errMsg, 'error')
    } finally {
        isSaving.value = false
    }
}

const editData = (item) => {
    isEditing.value = true
    formData.value = {
        id: item.id,
        kurikulum_id: item.kurikulum_id,
        kode_mapel: item.kode_mapel,
        nama_mapel: item.nama_mapel,
        kategori: item.kategori,
        kelompok: item.kelompok || ''
    }
}

const resetForm = () => {
    isEditing.value = false
    formData.value = {
        id: null,
        kurikulum_id: '',
        kode_mapel: '',
        nama_mapel: '',
        kategori: activeTab.value,
        kelompok: ''
    }
}

const confirmDelete = (item) => {
    deleteTarget.value = item
    isDeleteModalOpen.value = true
}

const executeDelete = async () => {
    if(!deleteTarget.value) return
    
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`http://localhost:8000/api/kurikulum/mapel/${deleteTarget.value.id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            isDeleteModalOpen.value = false
            displayToast(response.message)
            fetchData()
        }
    } catch (error) {
        console.error('Delete failed:', error)
        displayToast('Gagal menghapus data mapel.', 'error')
    }
}

const displayToast = (msg, type = 'success') => {
    toastMessage.value = msg
    toastType.value = type
    showToast.value = true
    setTimeout(() => { showToast.value = false }, 3500)
}

onMounted(async () => {
    await fetchReferensi()
    await fetchData()
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }

@keyframes slideUpFade {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-slideUpFade { animation: slideUpFade 0.3s ease-out forwards; }

@keyframes slideUp {
  from { opacity: 0; transform: translate(-50%, 20px); }
  to { opacity: 1; transform: translate(-50%, 0); }
}
.animate-slideUp { animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
