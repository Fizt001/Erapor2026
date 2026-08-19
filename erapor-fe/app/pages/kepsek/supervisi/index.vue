<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-slate-800">Jadwal Supervisi Guru</h1>
      <button @click="openForm(null)" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
        <AppIcon name="plus" class="w-5 h-5" />
        Tambah Jadwal
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="pending" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 text-red-600 p-4 rounded-xl border border-red-100">
      Gagal memuat data supervisi.
    </div>

    <!-- Data Table -->
    <div v-else class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-sm text-slate-600">
              <th class="p-4 font-semibold">No</th>
              <th class="p-4 font-semibold">Tanggal & Waktu</th>
              <th class="p-4 font-semibold">Nama Guru</th>
              <th class="p-4 font-semibold">Keterangan / Persiapan</th>
              <th class="p-4 font-semibold">Evaluasi</th>
              <th class="p-4 font-semibold">Status</th>
              <th class="p-4 font-semibold text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="text-sm">
            <tr v-for="(item, index) in data?.data || []" :key="item.id" class="border-b border-slate-100 hover:bg-slate-50">
              <td class="p-4 text-slate-500">{{ index + 1 }}</td>
              <td class="p-4 text-slate-700 font-medium">
                {{ formatDate(item.tanggal) }} <br/>
                <span class="text-xs text-slate-500">{{ item.waktu }}</span>
              </td>
              <td class="p-4 font-medium text-slate-800">{{ item.guru?.name }}</td>
              <td class="p-4 text-slate-600 max-w-xs truncate" :title="item.keterangan">{{ item.keterangan || '-' }}</td>
              <td class="p-4 text-slate-600 max-w-xs truncate" :title="item.evaluasi">{{ item.evaluasi || '-' }}</td>
              <td class="p-4">
                <span :class="{
                  'px-2 py-1 text-xs font-medium rounded-full': true,
                  'bg-blue-100 text-blue-700': item.status === 'Terjadwal',
                  'bg-green-100 text-green-700': item.status === 'Selesai',
                  'bg-slate-100 text-slate-700': item.status === 'Dibatalkan'
                }">
                  {{ item.status }}
                </span>
              </td>
              <td class="p-4 text-right space-x-2">
                <button @click="openForm(item)" class="text-blue-600 hover:text-blue-800 p-1">
                  {{ item.status === 'Selesai' ? 'Lihat' : 'Edit / Evaluasi' }}
                </button>
                <button @click="deleteItem(item.id)" class="text-red-600 hover:text-red-800 p-1">
                  Hapus
                </button>
              </td>
            </tr>
            <tr v-if="!data?.data || data.data.length === 0">
              <td colspan="7" class="p-8 text-center text-slate-500">Belum ada jadwal supervisi</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Form -->
    <div v-if="showModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white sticky top-0 z-10">
          <h3 class="text-lg font-semibold text-slate-800">{{ isEdit ? 'Update / Evaluasi Supervisi' : 'Tambah Jadwal Supervisi' }}</h3>
          <button @click="closeForm" class="text-slate-400 hover:text-slate-600">
            <AppIcon name="x-mark" class="w-5 h-5" />
          </button>
        </div>
        
        <form @submit.prevent="saveForm" class="p-6 space-y-4 overflow-y-auto">
          <div v-if="!isEdit">
            <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Guru</label>
            <select v-model="form.guru_id" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" required>
              <option value="">-- Pilih Guru --</option>
              <option v-for="g in gurus?.data || []" :key="g.id" :value="g.id">{{ g.name }}</option>
            </select>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
              <input type="date" v-model="form.tanggal" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Waktu (Jam)</label>
              <input type="time" v-model="form.waktu" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" required />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan / Persiapan yang dibutuhkan</label>
            <textarea v-model="form.keterangan" rows="3" placeholder="Contoh: RPP, Silabus, dan Media Pembelajaran" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" required></textarea>
          </div>
          
          <template v-if="isEdit">
            <div class="border-t border-slate-200 pt-4 mt-4">
              <h4 class="font-medium text-slate-800 mb-2">Hasil Supervisi (Diisi setelah supervisi)</h4>
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Evaluasi / Penilaian</label>
                  <textarea v-model="form.evaluasi" rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Tindak Lanjut</label>
                  <textarea v-model="form.tindak_lanjut" rows="2" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Status Supervisi</label>
                  <select v-model="form.status" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                    <option value="Terjadwal">Terjadwal</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Dibatalkan">Dibatalkan</option>
                  </select>
                </div>
              </div>
            </div>
          </template>

          <div class="pt-4 flex justify-end gap-3 sticky bottom-0 bg-white border-t border-slate-100">
            <button type="button" @click="closeForm" class="px-4 py-2 text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
              Batal
            </button>
            <button type="submit" :disabled="isSaving" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50">
              {{ isSaving ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

definePageMeta({
  middleware: 'kepsek',
  layout: 'kepsek'
})

const { data, pending, error, refresh } = await useFetch('/api/kepsek/supervisi', {
  headers: {
    Authorization: `Bearer ${useCookie('auth_token').value}`
  }
})

const { data: gurus } = await useFetch('/api/kepsek/supervisi/gurus', {
  headers: {
    Authorization: `Bearer ${useCookie('auth_token').value}`
  }
})

const showModal = ref(false)
const isEdit = ref(false)
const isSaving = ref(false)

const form = ref({
  id: null,
  guru_id: '',
  tanggal: new Date().toISOString().split('T')[0],
  waktu: '08:00',
  keterangan: '',
  evaluasi: '',
  tindak_lanjut: '',
  status: 'Terjadwal'
})

const openForm = (item) => {
  if (item) {
    isEdit.value = true
    form.value = {
      id: item.id,
      guru_id: item.guru_id,
      tanggal: item.tanggal,
      waktu: item.waktu || '08:00',
      keterangan: item.keterangan || '',
      evaluasi: item.evaluasi || '',
      tindak_lanjut: item.tindak_lanjut || '',
      status: item.status
    }
  } else {
    isEdit.value = false
    form.value = {
      id: null,
      guru_id: '',
      tanggal: new Date().toISOString().split('T')[0],
      waktu: '08:00',
      keterangan: '',
      evaluasi: '',
      tindak_lanjut: '',
      status: 'Terjadwal'
    }
  }
  showModal.value = true
}

const closeForm = () => {
  showModal.value = false
}

const saveForm = async () => {
  isSaving.value = true
  try {
    const url = isEdit.value 
      ? `/api/kepsek/supervisi/${form.value.id}`
      : `/api/kepsek/supervisi`
      
    const method = isEdit.value ? 'PUT' : 'POST'

    await $fetch(url, {
      method,
      headers: {
        Authorization: `Bearer ${useCookie('auth_token').value}`
      },
      body: form.value
    })
    
    closeForm()
    refresh()
  } catch (err) {
    alert('Terjadi kesalahan saat menyimpan data')
  } finally {
    isSaving.value = false
  }
}

const deleteItem = async (id) => {
  if (confirm('Yakin ingin membatalkan/menghapus jadwal supervisi ini?')) {
    try {
      await $fetch(`/api/kepsek/supervisi/${id}`, {
        method: 'DELETE',
        headers: {
          Authorization: `Bearer ${useCookie('auth_token').value}`
        }
      })
      refresh()
    } catch (err) {
      alert('Gagal menghapus jadwal')
    }
  }
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const d = new Date(dateString)
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}
</script>
