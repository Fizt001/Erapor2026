<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-slate-800">Jadwal Supervisi Anda</h1>
    </div>

    <!-- Loading State -->
    <div v-if="pending" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 text-red-600 p-4 rounded-xl border border-red-100">
      Gagal memuat jadwal supervisi.
    </div>

    <!-- Empty State -->
    <div v-else-if="!data?.data || data.data.length === 0" class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
      <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
        <AppIcon name="calendar" class="w-8 h-8" />
      </div>
      <h3 class="text-lg font-semibold text-slate-800 mb-2">Belum Ada Jadwal Supervisi</h3>
      <p class="text-slate-500">Anda belum menerima jadwal supervisi dari Kepala Sekolah saat ini.</p>
    </div>

    <!-- Data List -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div v-for="item in data.data" :key="item.id" 
        class="bg-white rounded-xl shadow-sm border p-6 relative overflow-hidden"
        :class="item.status === 'Selesai' ? 'border-green-200 bg-green-50/30' : 'border-blue-200'">
        
        <div class="absolute top-0 right-0 px-4 py-1 text-xs font-bold text-white rounded-bl-xl shadow-sm"
          :class="{
            'bg-blue-600': item.status === 'Terjadwal',
            'bg-green-600': item.status === 'Selesai',
            'bg-slate-500': item.status === 'Dibatalkan'
          }">
          {{ item.status }}
        </div>

        <div class="flex items-center gap-3 mb-4 mt-2">
          <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
            <AppIcon name="calendar-days" class="w-5 h-5" />
          </div>
          <div>
            <h3 class="font-bold text-slate-800">{{ formatDate(item.tanggal) }}</h3>
            <p class="text-sm text-slate-500">Jam: {{ item.waktu || '-' }}</p>
          </div>
        </div>

        <div class="space-y-4">
          <div class="bg-blue-50/50 p-4 rounded-lg border border-blue-100">
            <h4 class="text-sm font-semibold text-blue-800 flex items-center gap-2 mb-2">
              <AppIcon name="information-circle" class="w-4 h-4" />
              Persiapan yang Dibutuhkan
            </h4>
            <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ item.keterangan || 'Menunggu arahan dari Kepala Sekolah.' }}</p>
          </div>

          <div v-if="item.status === 'Selesai'" class="bg-green-50 p-4 rounded-lg border border-green-100">
            <h4 class="text-sm font-semibold text-green-800 flex items-center gap-2 mb-2">
              <AppIcon name="check-badge" class="w-4 h-4" />
              Evaluasi & Tindak Lanjut
            </h4>
            <div class="text-sm text-slate-700 space-y-2">
              <p><strong>Evaluasi:</strong><br/>{{ item.evaluasi || '-' }}</p>
              <p><strong>Tindak Lanjut:</strong><br/>{{ item.tindak_lanjut || '-' }}</p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  middleware: 'guru',
  layout: 'guru'
})

const { data, pending, error } = await useFetch('/api/guru/supervisi', {
  headers: {
    Authorization: `Bearer ${useCookie('auth_token').value}`
  }
})

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const d = new Date(dateString)
  return d.toLocaleDateString('id-ID', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' })
}
</script>
