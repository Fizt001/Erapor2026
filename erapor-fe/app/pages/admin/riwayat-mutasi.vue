<template>
  <div class="h-full flex flex-col md:flex-row bg-slate-50 relative overflow-hidden">

    <!-- PANEL KIRI (DOCK) -->
    <div class="w-full md:w-[360px] bg-white border-r border-slate-200 shrink-0 flex flex-col h-[40vh] md:h-full z-10 transition-all duration-300 relative">
      <div class="p-6 border-b border-slate-200 bg-white/80 backdrop-blur-md sticky top-0 z-20">
        <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-3">
          <span class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </span>
          Riwayat Mutasi Siswa
        </h1>
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2 ml-13">Arsip Mutasi & Siswa Keluar</p>
      </div>

      <div class="flex-1 overflow-y-auto custom-scrollbar p-6 space-y-6">
        <div class="space-y-4">
          <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl p-5 border border-emerald-500 shadow-sm relative overflow-hidden">
            <div class="relative z-10">
              <h3 class="text-sm font-black uppercase tracking-widest text-white">Filter Riwayat</h3>
              <p class="text-[10px] text-emerald-200 font-semibold mt-0.5">Filter berdasarkan jenis mutasi</p>
            </div>
          </div>

          <div class="space-y-2">
            <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest ml-1">Tampilkan Status</label>
            <select v-model="filterStatus" @change="fetchMutasi" class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-sm font-bold text-slate-700 outline-none">
              <option value="">Semua Riwayat (Approved/Rejected)</option>
              <option value="approved">Disetujui / Selesai</option>
              <option value="rejected">Ditolak / Batal</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- PANEL KANAN (FLOW) -->
    <div class="flex-1 min-w-0 flex flex-col h-[60vh] md:h-full bg-slate-50 relative z-0">
      <div class="flex-1 p-6 md:p-8 overflow-hidden flex flex-col">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm shadow-slate-200/50 flex-1 flex flex-col overflow-hidden relative">
          
          <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60">
            <div class="w-8 h-8 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-xs font-bold text-slate-500">Memuat Data...</span>
          </div>

          <div v-else-if="mutasiList.length === 0" class="flex-grow flex items-center justify-center flex-col p-10 text-center opacity-60">
            <div class="text-5xl mb-4">📋</div>
            <p class="text-sm font-bold text-slate-500">Belum ada riwayat mutasi.</p>
          </div>

          <div v-else class="flex-1 overflow-auto custom-scrollbar">
            <table class="w-full text-left border-collapse whitespace-nowrap">
              <thead class="bg-slate-50/70 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-200 sticky top-0 z-10">
                <tr>
                  <th class="py-3 px-4 w-8 text-center">No</th>
                  <th class="py-3 px-4">Tanggal Mutasi</th>
                  <th class="py-3 px-4">Siswa</th>
                  <th class="py-3 px-4">Jenis Mutasi</th>
                  <th class="py-3 px-4">Alasan</th>
                  <th class="py-3 px-4">Status</th>
                  <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100">
                <tr v-for="(m, idx) in mutasiList" :key="m.id" class="hover:bg-slate-50/80 transition-colors group bg-white">
                  <td class="p-4 text-center text-xs font-bold text-slate-400">{{ idx + 1 }}</td>
                  <td class="p-4">
                    <p class="font-bold text-slate-700 text-xs">{{ formatDate(m.tanggal_mutasi) }}</p>
                  </td>
                  <td class="p-4">
                    <p class="font-black text-slate-800 text-[13px]">{{ m.siswa?.name || 'Unknown' }}</p>
                    <p class="text-[10px] text-slate-400 font-bold">Asal: {{ m.kelas_asal?.nama_kelas || '-' }}</p>
                  </td>
                  <td class="p-4">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 text-[11px] font-black uppercase tracking-widest border border-slate-200">
                      {{ formatJenis(m.jenis_mutasi) }}
                    </span>
                    <p v-if="m.jenis_mutasi === 'pindah_kelas'" class="text-[10px] text-emerald-600 font-bold mt-1">
                      Ke: {{ m.kelas_tujuan?.nama_kelas || '-' }}
                    </p>
                  </td>
                  <td class="p-4">
                    <p class="text-xs text-slate-600 max-w-[200px] truncate" :title="m.alasan">{{ m.alasan }}</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">Oleh: {{ m.diajukan_oleh?.name || '-' }}</p>
                  </td>
                  <td class="p-4">
                    <span :class="statusClass(m.status_approval)" class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-black uppercase tracking-widest border">
                      {{ m.status_approval }}
                    </span>
                  </td>
                  <td class="p-4 text-center">
                    <button @click="cancelMutasi(m)" class="px-3 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold rounded-lg transition-colors text-[10px] uppercase tracking-widest flex items-center justify-center gap-1 mx-auto shadow-sm" title="Batalkan Mutasi ini">
                      <span>🔙</span> Batal
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      
      <!-- Footer Info -->
      <div class="bg-slate-100 border-t border-slate-200 text-center py-3 shrink-0 print:hidden z-20">
          <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Created by <span class="text-emerald-600">SMK-Yatindo</span></p>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useNuxtApp } from '#app'

definePageMeta({
  layout: 'admin',
  middleware: 'admin',
  title: 'Riwayat Mutasi'
})

const token = useCookie('auth_token')
const mutasiList = ref([])
const isLoading = ref(true)
const filterStatus = ref('')
const isProcessing = ref(false)

const cancelMutasi = async (m) => {
  if (!confirm(`Apakah Anda yakin ingin MEMBATALKAN mutasi untuk ${m.siswa?.user?.name}? Siswa akan dikembalikan ke status Aktif di kelas asalnya.`)) return

  isProcessing.value = true
  try {
    const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/admin/mutasi/${m.id}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${token.value}` }
    })
    useSwal().toast('Berhasil!', res.message, 'success')
    fetchMutasi()
  } catch (error) {
    useSwal().toast(error.response?._data?.message || 'Gagal membatalkan mutasi', 'error')
  } finally {
    isProcessing.value = false
  }
}

const fetchMutasi = async () => {
  isLoading.value = true
  try {
    const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/admin/mutasi?status_approval=${filterStatus.value}`, {
      headers: { Authorization: `Bearer ${token.value}` }
    })
    mutasiList.value = res.data
  } catch (error) {
    useSwal().toast('Gagal mengambil data mutasi', 'error')
  } finally {
    isLoading.value = false
  }
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })
}

const formatJenis = (jenis) => {
  const map = {
    'pindah_kelas': 'Pindah Kelas',
    'pindah_sekolah': 'Pindah Sekolah',
    'keluar': 'Keluar',
    'dikeluarkan': 'Dikeluarkan'
  }
  return map[jenis] || jenis
}

const statusClass = (status) => {
  if (status === 'pending') return 'bg-amber-100 text-amber-700 border-amber-200'
  if (status === 'approved') return 'bg-emerald-100 text-emerald-700 border-emerald-200'
  if (status === 'rejected') return 'bg-rose-100 text-rose-700 border-rose-200'
  return 'bg-slate-100 text-slate-700 border-slate-200'
}

onMounted(() => {
  fetchMutasi()
})
</script>
