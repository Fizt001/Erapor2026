<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-slate-800">Pemantauan Kasus Guru</h1>
    </div>

    <!-- Loading State -->
    <div v-if="pending" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 text-red-600 p-4 rounded-xl border border-red-100">
      Gagal memuat data kasus guru.
    </div>

    <!-- Data Table -->
    <div v-else class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-sm text-slate-600">
              <th class="p-4 font-semibold">No</th>
              <th class="p-4 font-semibold">Tanggal</th>
              <th class="p-4 font-semibold">Nama Guru</th>
              <th class="p-4 font-semibold text-center">Total Kasus</th>
              <th class="p-4 font-semibold">Kasus Terbaru</th>
              <th class="p-4 font-semibold">Status</th>
              <th class="p-4 font-semibold text-right">Tindakan Kepsek</th>
            </tr>
          </thead>
          <tbody class="text-sm">
            <tr v-for="(item, index) in data?.data || []" :key="item.id" class="border-b border-slate-100 hover:bg-slate-50">
              <td class="p-4 text-slate-500">{{ index + 1 }}</td>
              <td class="p-4 text-slate-700">{{ formatDate(item.tanggal) }}</td>
              <td class="p-4 font-medium text-slate-800">
                {{ item.guru?.name }}
              </td>
              <td class="p-4 text-center">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-bold"
                  :class="getCaseCount(item.guru_id) >= 3 ? 'bg-red-100 text-red-600' : 'bg-slate-100 text-slate-600'">
                  {{ getCaseCount(item.guru_id) }}
                </span>
              </td>
              <td class="p-4 text-slate-600 max-w-xs truncate" :title="item.kasus">{{ item.kasus }}</td>
              <td class="p-4">
                <span :class="{
                  'px-2 py-1 text-xs font-medium rounded-full': true,
                  'bg-red-100 text-red-700': item.status === 'Terbuka',
                  'bg-yellow-100 text-yellow-700': item.status === 'Ditangani',
                  'bg-green-100 text-green-700': item.status === 'Selesai'
                }">
                  {{ item.status }}
                </span>
              </td>
              <td class="p-4 text-right">
                <button 
                  v-if="getCaseCount(item.guru_id) >= 3"
                  @click="panggilGuru(item.guru_id, item.guru?.name)" 
                  class="px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 text-xs font-medium transition-colors shadow-sm"
                  :disabled="isCalling === item.guru_id"
                >
                  {{ isCalling === item.guru_id ? 'Memproses...' : 'Kirim Panggilan' }}
                </button>
                <span v-else class="text-slate-400 text-xs italic">
                  Belum memenuhi syarat
                </span>
              </td>
            </tr>
            <tr v-if="!data?.data || data.data.length === 0">
              <td colspan="7" class="p-8 text-center text-slate-500">Belum ada data kasus guru yang dilaporkan</td>
            </tr>
          </tbody>
        </table>
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

const { data, pending, error, refresh } = await useFetch('/api/kepsek/kasus-guru', {
  headers: {
    Authorization: `Bearer ${useCookie('auth_token').value}`
  }
})

const isCalling = ref(null)

const getCaseCount = (guruId) => {
  return data.value?.guru_cases_count?.[guruId] || 0
}

const panggilGuru = async (guruId, guruName) => {
  if (confirm(`Kirim notifikasi panggilan resmi ke dashboard ${guruName}?`)) {
    isCalling.value = guruId
    try {
      await $fetch(`/api/kepsek/kasus-guru/${guruId}/panggil`, {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${useCookie('auth_token').value}`
        }
      })
      alert('Panggilan berhasil dikirim.')
      refresh()
    } catch (err) {
      alert('Gagal mengirim panggilan.')
    } finally {
      isCalling.value = null
    }
  }
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const d = new Date(dateString)
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}
</script>
