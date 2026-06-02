<template>
    <div class="space-y-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Master Database Referensi</h2>
                <p class="text-slate-500 text-sm mt-1">Kelola data referensi statis seperti Kategori, Kelompok, dll.</p>
            </div>
            <button @click="openAddModal" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-sm shadow-emerald-200 transition-all flex items-center gap-2">
                <span>➕</span> Tambah Referensi
            </button>
        </div>

        <div v-if="isLoading" class="flex justify-center p-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600"></div>
        </div>

        <div v-else class="space-y-6">
            <div v-for="(group, jenis) in groupedData" :key="jenis" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-black text-slate-700 text-sm uppercase tracking-widest">{{ formatJenis(jenis) }}</h3>
                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md border border-emerald-100">{{ group.length }} Item</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-slate-100">
                                <th class="py-3 px-4 pl-6 w-16 text-center">#</th>
                                <th class="py-3 px-4">Kode / Value</th>
                                <th class="py-3 px-4">Nama (Display)</th>
                                <th class="py-3 px-4 text-right pr-6">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <tr v-for="(item, index) in group" :key="item.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors group">
                                <td class="py-2.5 px-4 pl-6 text-center text-xs font-bold text-slate-300">
                                    {{ index + 1 }}
                                </td>
                                <td class="py-2.5 px-4">
                                    <span class="text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded bg-slate-100 text-slate-600 border border-slate-200">{{ item.kode }}</span>
                                </td>
                                <td class="py-2.5 px-4">
                                    <p class="font-bold text-slate-700 text-xs">{{ item.nama }}</p>
                                </td>
                                <td class="py-2.5 px-4 pr-6 text-right">
                                    <div class="flex items-center justify-end gap-1.5 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                        <button @click="editData(item)" class="w-7 h-7 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-emerald-500 hover:bg-emerald-50 flex items-center justify-center transition-all shadow-sm" title="Edit">✏️</button>
                                        <button @click="confirmDelete(item)" class="w-7 h-7 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:bg-rose-50 flex items-center justify-center transition-all shadow-sm" title="Hapus">🗑️</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="group.length === 0">
                                <td colspan="4" class="py-8 text-center text-slate-400 text-xs font-bold">Belum ada data untuk jenis ini.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div v-if="Object.keys(groupedData).length === 0" class="bg-white p-12 rounded-2xl border border-slate-200 text-center">
                <span class="text-4xl mb-4 block">🗄️</span>
                <h3 class="text-lg font-bold text-slate-700 mb-2">Master Database Kosong</h3>
                <p class="text-slate-500 text-sm">Belum ada referensi yang ditambahkan.</p>
            </div>
        </div>

        <!-- Form Modal -->
        <div v-if="isFormOpen" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-slate-800">{{ isEditing ? 'Edit Referensi' : 'Tambah Referensi' }}</h3>
                    <button @click="closeForm" class="text-slate-400 hover:text-rose-500 transition-colors">✕</button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jenis Referensi</label>
                        <input type="text" v-model="formData.jenis" required placeholder="Contoh: kategori_mapel" list="jenis-list" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm font-bold text-slate-800 outline-none">
                        <datalist id="jenis-list">
                            <option value="kategori_mapel">Kategori Mapel (Umum/Kejuruan)</option>
                            <option value="kelompok_mapel">Kelompok Mapel (A/B/C/D)</option>
                            <option value="nama_periode">Nama Periode (PSTS, PAS, dll)</option>
                        </datalist>
                        <p class="text-[10px] text-slate-500 mt-1">Gunakan huruf kecil & underscore (tanpa spasi).</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kode / Value</label>
                        <input type="text" v-model="formData.kode" required placeholder="Contoh: umum, kejuruan, A, B" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm font-bold text-slate-800 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Tampilan</label>
                        <input type="text" v-model="formData.nama" required placeholder="Contoh: Muatan Umum, Kelompok A (Nasional)" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all text-sm font-bold text-slate-800 outline-none">
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                    <button @click="closeForm" class="px-4 py-2 text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">Batal</button>
                    <button @click="saveData" :disabled="isSaving" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-xl text-sm font-bold shadow-sm shadow-emerald-200 transition-all disabled:opacity-50">
                        {{ isSaving ? 'Menyimpan...' : 'Simpan' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="isDeleteModalOpen" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center transform transition-all">
                <div class="w-16 h-16 bg-rose-100 text-rose-500 rounded-full flex items-center justify-center text-2xl mx-auto mb-4 shadow-inner">
                    🗑️
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Hapus Referensi?</h3>
                <p class="text-slate-500 text-sm mb-6">Data <span class="font-bold text-slate-700">{{ deleteTarget?.nama }}</span> akan dihapus permanen.</p>
                <div class="flex gap-3 justify-center">
                    <button @click="isDeleteModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-bold transition-colors w-full">Batal</button>
                    <button @click="executeDelete" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold shadow-sm shadow-rose-200 transition-colors w-full">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

definePageMeta({
  layout: 'admin',
  title: 'Master Database'
})

const tokenCookie = useCookie('auth_token')
const referensis = ref([])
const isLoading = ref(true)
const isSaving = ref(false)
const isFormOpen = ref(false)
const isEditing = ref(false)
const isDeleteModalOpen = ref(false)
const deleteTarget = ref(null)

const formData = ref({
    id: null,
    jenis: '',
    kode: '',
    nama: ''
})

const groupedData = computed(() => {
    // Tentukan urutan yang diinginkan dan pastikan selalu tampil walau kosong
    const expectedOrder = ['kategori_mapel', 'kelompok_mapel', 'nama_periode']
    const groups = {
        'kategori_mapel': [],
        'kelompok_mapel': [],
        'nama_periode': []
    }
    
    referensis.value.forEach(item => {
        if (!groups[item.jenis]) {
            groups[item.jenis] = []
            expectedOrder.push(item.jenis)
        }
        groups[item.jenis].push(item)
    })
    
    // Bentuk object dengan urutan yang sudah ditentukan
    const orderedGroups = {}
    expectedOrder.forEach(key => {
        orderedGroups[key] = groups[key]
    })
    
    return orderedGroups
})

const formatJenis = (jenis) => {
    return jenis.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')
}

const fetchData = async () => {
    isLoading.value = true
    try {
        const response = await $fetch('http://localhost:8000/api/admin/referensi', {
            headers: { 'Authorization': `Bearer ${tokenCookie.value}` }
        })
        referensis.value = response.data || []
    } catch (error) {
        console.error('Error fetching referensi:', error)
    } finally {
        isLoading.value = false
    }
}

const openAddModal = () => {
    isEditing.value = false
    formData.value = { id: null, jenis: '', kode: '', nama: '' }
    isFormOpen.value = true
}

const editData = (item) => {
    isEditing.value = true
    formData.value = { ...item }
    isFormOpen.value = true
}

const closeForm = () => {
    isFormOpen.value = false
}

const saveData = async () => {
    if (!formData.value.jenis || !formData.value.kode || !formData.value.nama) return

    isSaving.value = true
    try {
        const url = isEditing.value 
            ? `http://localhost:8000/api/admin/referensi/${formData.value.id}`
            : 'http://localhost:8000/api/admin/referensi'
            
        await $fetch(url, {
            method: isEditing.value ? 'PUT' : 'POST',
            headers: { 
                'Authorization': `Bearer ${tokenCookie.value}`,
                'Content-Type': 'application/json'
            },
            body: formData.value
        })
        
        await fetchData()
        closeForm()
    } catch (error) {
        console.error('Error saving:', error)
    } finally {
        isSaving.value = false
    }
}

const confirmDelete = (item) => {
    deleteTarget.value = item
    isDeleteModalOpen.value = true
}

const executeDelete = async () => {
    if (!deleteTarget.value) return
    
    try {
        await $fetch(`http://localhost:8000/api/admin/referensi/${deleteTarget.value.id}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${tokenCookie.value}` }
        })
        await fetchData()
        isDeleteModalOpen.value = false
    } catch (error) {
        console.error('Error deleting:', error)
    }
}

onMounted(() => {
    fetchData()
})
</script>
