<template>
  <div>
    <div class="mb-6 flex justify-between items-end">
      <div>
        <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Periode & Titimangsa</h1>
        <p class="text-slate-500 mt-1 text-sm font-medium">Atur periode penilaian dan parameter cetak rapor.</p>
      </div>
      <div>
        <button @click="openModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-2xl font-bold transition-all shadow-lg shadow-indigo-200 active:scale-95 flex items-center">
          <span class="mr-2">➕</span> Tambah Periode
        </button>
      </div>
    </div>

    <!-- Alert Tahun Ajaran -->
    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-2xl mb-6 flex items-center justify-between shadow-sm">
      <div class="flex items-center">
        <span class="text-amber-500 text-2xl mr-4">📅</span>
        <div>
          <h3 class="font-black text-amber-800 tracking-wide uppercase text-xs">Tahun Ajaran Aktif</h3>
          <p class="text-amber-700 font-bold text-lg">{{ tahunAktif ? tahunAktif.tahun : 'Memuat...' }} <span v-if="tahunAktif" class="text-xs bg-amber-200 text-amber-800 px-2 py-0.5 rounded ml-2">{{ tahunAktif.semester }}</span></p>
        </div>
      </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden">
      <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <h2 class="text-lg font-black text-slate-800">Daftar Titimangsa</h2>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-100 text-[10px] font-black uppercase tracking-widest text-slate-500">
              <th class="p-4 border-b border-slate-200 w-16 text-center">No</th>
              <th class="p-4 border-b border-slate-200">Kurikulum</th>
              <th class="p-4 border-b border-slate-200">Nama Periode</th>
              <th class="p-4 border-b border-slate-200">Target Tingkat</th>
              <th class="p-4 border-b border-slate-200">Cetak Rapor</th>
              <th class="p-4 border-b border-slate-200 text-center">Status</th>
              <th class="p-4 border-b border-slate-200 text-center w-32">Aksi</th>
            </tr>
          </thead>
          <tbody class="text-sm">
            <tr v-for="(item, index) in titimangsas" :key="item.id" class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
              <td class="p-4 text-center font-bold text-slate-400">{{ index + 1 }}</td>
              <td class="p-4">
                <span class="px-2 py-1 bg-slate-100 text-slate-600 font-bold rounded text-xs border border-slate-200">{{ item.kurikulum?.nama_kurikulum }}</span>
              </td>
              <td class="p-4 font-black text-slate-700">{{ item.nama_periode }}</td>
              <td class="p-4 font-bold text-slate-500">{{ item.target_tingkat }}</td>
              <td class="p-4">
                <div class="font-bold text-slate-700">{{ item.tempat_cetak }}, {{ item.tanggal_cetak }}</div>
              </td>
              <td class="p-4 text-center">
                <button @click="toggleStatus(item.id)" class="px-3 py-1 text-xs font-bold rounded-full transition-colors border"
                  :class="item.is_aktif ? 'bg-emerald-50 text-emerald-600 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-50 text-slate-500 border-slate-200 hover:bg-slate-100'">
                  {{ item.is_aktif ? 'Aktif' : 'Nonaktif' }}
                </button>
              </td>
              <td class="p-4 text-center">
                <div class="flex justify-center space-x-2">
                  <button @click="openModal(item)" class="p-2 text-amber-500 hover:bg-amber-50 rounded-xl transition-colors" title="Edit">
                    ✏️
                  </button>
                  <button @click="confirmDelete(item)" class="p-2 text-rose-500 hover:bg-rose-50 rounded-xl transition-colors" title="Hapus">
                    🗑️
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="titimangsas.length === 0">
              <td colspan="7" class="p-8 text-center text-slate-400 font-bold">
                Belum ada data titimangsa.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Form -->
    <div v-if="isModalOpen" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
          <h3 class="text-lg font-black text-slate-800">{{ isEdit ? 'Edit Periode Titimangsa' : 'Tambah Periode Baru' }}</h3>
          <button @click="closeModal" class="text-slate-400 hover:text-slate-600 focus:outline-none text-2xl font-black">&times;</button>
        </div>
        <form @submit.prevent="saveData" class="p-6 space-y-4">
          
          <div>
            <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Kurikulum</label>
            <select v-model="form.kurikulum_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-bold text-slate-700 outline-none">
              <option value="" disabled>-- Pilih Kurikulum --</option>
              <option v-for="k in kurikulums" :key="k.id" :value="k.id">{{ k.nama_kurikulum }}</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Nama Periode</label>
            <select v-model="form.nama_periode" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-bold text-slate-700 outline-none">
              <option value="" disabled>-- Pilih Nama Periode --</option>
              <option v-for="rp in referensiPeriode" :key="rp.id" :value="rp.nama">{{ rp.nama }}</option>
            </select>
            <p v-if="referensiPeriode.length === 0" class="text-[10px] text-rose-500 mt-1 font-medium">Data kosong. Tambahkan dulu di Master Database -> Nama Periode.</p>
          </div>

          <div>
            <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Target Tingkat</label>
            <div class="flex flex-wrap gap-3 bg-slate-50 p-3 border border-slate-200 rounded-xl">
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" v-model="form.target_tingkat_arr" value="X" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                <span class="text-sm font-bold text-slate-700">Kelas X</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" v-model="form.target_tingkat_arr" value="XI" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                <span class="text-sm font-bold text-slate-700">Kelas XI</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" v-model="form.target_tingkat_arr" value="XII" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                <span class="text-sm font-bold text-slate-700">Kelas XII</span>
              </label>
            </div>
            <p class="text-[10px] text-slate-400 mt-1 font-medium">Pilih tingkat/kelas yang diikutsertakan pada periode ini.</p>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Tempat Cetak</label>
              <input v-model="form.tempat_cetak" type="text" required placeholder="Misal: Bekasi" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-bold text-slate-700 outline-none">
            </div>
            <div>
              <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Tanggal Cetak</label>
              <input v-model="form.tanggal_cetak" type="date" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-sm font-bold text-slate-700 outline-none">
            </div>
          </div>

          <div class="mt-8 flex justify-end space-x-3 pt-4 border-t border-slate-100">
            <button type="button" @click="closeModal" class="px-5 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition-colors">
              Batal
            </button>
            <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors shadow-lg shadow-indigo-200 active:scale-95">
              Simpan Periode
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Modal -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl shadow-xl w-full max-w-sm p-6 text-center transform transition-all">
        <div class="w-16 h-16 bg-rose-100 text-rose-500 rounded-full flex items-center justify-center text-2xl mx-auto mb-4 shadow-inner">
          🗑️
        </div>
        <h3 class="text-lg font-black text-slate-800 mb-2">Hapus Periode?</h3>
        <p class="text-slate-500 text-sm mb-6 font-medium">Periode <span class="font-bold text-slate-700">{{ deleteTarget?.nama_periode }}</span> akan dihapus permanen.</p>
        <div class="flex gap-3 justify-center">
          <button @click="isDeleteModalOpen = false" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-bold transition-colors w-full">Batal</button>
          <button @click="executeDelete" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold shadow-lg shadow-rose-200 transition-colors w-full">Ya, Hapus</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

definePageMeta({
  layout: 'kurikulum',
  middleware: 'kurikulum',
  title: 'Periode & Titimangsa'
})

const titimangsas = ref([])
const kurikulums = ref([])
const referensiPeriode = ref([])
const tahunAktif = ref(null)

const isModalOpen = ref(false)
const isEdit = ref(false)
const isDeleteModalOpen = ref(false)
const deleteTarget = ref(null)

const form = ref({
  id: null,
  kurikulum_id: '',
  nama_periode: '',
  target_tingkat_arr: ['X', 'XI', 'XII'],
  tempat_cetak: 'Bekasi',
  tanggal_cetak: ''
})

const fetchData = async () => {
  try {
    const token = useCookie('auth_token').value
    const response = await $fetch('http://localhost:8000/api/kurikulum/titimangsa', {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    if (response.success) {
      titimangsas.value = response.data.titimangsas
      kurikulums.value = response.data.kurikulums
      referensiPeriode.value = response.data.referensi_periode || []
      tahunAktif.value = response.data.tahun_aktif
    }
  } catch (error) {
    console.error('Error fetching titimangsa:', error)
  }
}

const openModal = (item = null) => {
  if (item) {
    isEdit.value = true
    form.value = { ...item, target_tingkat_arr: item.target_tingkat ? item.target_tingkat.split(',').map(s => s.trim()) : [] }
  } else {
    isEdit.value = false
    form.value = {
      id: null,
      kurikulum_id: kurikulums.value.length > 0 ? kurikulums.value[0].id : '',
      nama_periode: '',
      target_tingkat_arr: ['X', 'XI', 'XII'],
      tempat_cetak: 'Bekasi',
      tanggal_cetak: ''
    }
  }
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
}

const saveData = async () => {
  if (form.value.target_tingkat_arr.length === 0) {
      alert("Pilih minimal satu tingkat (X, XI, atau XII)!")
      return
  }

  try {
    const token = useCookie('auth_token').value
    const url = isEdit.value 
      ? `http://localhost:8000/api/kurikulum/titimangsa/${form.value.id}`
      : 'http://localhost:8000/api/kurikulum/titimangsa'
    
    const method = isEdit.value ? 'PUT' : 'POST'
    
    // Sort and join
    const sortedTingkat = [...form.value.target_tingkat_arr].sort((a,b) => {
        const order = { 'X': 1, 'XI': 2, 'XII': 3 }
        return order[a] - order[b]
    })
    const payload = {
        ...form.value,
        target_tingkat: sortedTingkat.join(',')
    }
    
    const response = await $fetch(url, {
      method,
      headers: { Authorization: `Bearer ${token}` },
      body: payload
    })
    
    if (response.success) {
      closeModal()
      fetchData()
    }
  } catch (error) {
    console.error('Error saving data:', error)
  }
}

const toggleStatus = async (id) => {
  try {
    const token = useCookie('auth_token').value
    const response = await $fetch(`http://localhost:8000/api/kurikulum/titimangsa/${id}/toggle`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}` }
    })
    
    if (response.success) {
      fetchData()
    }
  } catch (error) {
    console.error('Error toggling status:', error)
  }
}

const confirmDelete = (item) => {
  deleteTarget.value = item
  isDeleteModalOpen.value = true
}

const executeDelete = async () => {
  if (!deleteTarget.value) return
  
  try {
    const token = useCookie('auth_token').value
    await $fetch(`http://localhost:8000/api/kurikulum/titimangsa/${deleteTarget.value.id}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${token}` }
    })
    isDeleteModalOpen.value = false
    fetchData()
  } catch (error) {
    console.error('Error deleting data:', error)
  }
}

onMounted(() => {
  fetchData()
})
</script>
