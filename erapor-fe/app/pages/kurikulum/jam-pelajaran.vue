<template>
  <div class="space-y-6">
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100/60 relative overflow-hidden">
      <div class="absolute -right-6 -top-6 w-32 h-32 bg-amber-50 rounded-full blur-3xl opacity-60"></div>
      
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
        <div>
          <h1 class="text-2xl font-black text-slate-800 tracking-tight">Master Jam Pelajaran</h1>
          <p class="text-slate-500 text-sm mt-1">Atur waktu mulai dan selesai untuk setiap Jam Pelajaran (JP).</p>
        </div>
        <button @click="save" :disabled="loading" class="w-full sm:w-auto px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl shadow-lg shadow-slate-200 transition-all active:scale-95 disabled:opacity-50 flex items-center justify-center gap-2">
          <svg v-if="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
          <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
          {{ loading ? 'Menyimpan...' : 'Simpan Perubahan' }}
        </button>
      </div>
    </div>

    <!-- TABS -->
    <div class="flex gap-2 p-1 bg-white rounded-2xl shadow-sm border border-slate-100 max-w-sm">
      <button @click="activeTab = 'senin-kamis'" class="flex-1 py-2 text-sm font-bold rounded-xl transition-all" :class="activeTab === 'senin-kamis' ? 'bg-amber-100 text-amber-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50'">
        Senin - Kamis
      </button>
      <button @click="activeTab = 'jumat'" class="flex-1 py-2 text-sm font-bold rounded-xl transition-all" :class="activeTab === 'jumat' ? 'bg-amber-100 text-amber-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50'">
        Jumat
      </button>
    </div>

    <div v-if="fetching" class="text-center py-20 text-slate-400">
      <svg class="animate-spin h-8 w-8 mx-auto mb-4 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
      Memuat data jam...
    </div>

    <div v-else class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
      <!-- TABLE SENIN-KAMIS -->
      <div v-show="activeTab === 'senin-kamis'" class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/50 border-b border-slate-100">
              <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-wider w-24 text-center">Jam Ke</th>
              <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-wider">Waktu Mulai</th>
              <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-wider">Waktu Selesai</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            <tr v-for="(item, idx) in seninKamisData" :key="'sk-'+idx" class="hover:bg-slate-50/50 transition-colors">
              <td class="px-6 py-3 text-center">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold text-sm">{{ item.jam_ke }}</span>
              </td>
              <td class="px-6 py-3">
                <input type="time" v-model="item.waktu_mulai" class="w-full sm:w-40 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
              </td>
              <td class="px-6 py-3">
                <input type="time" v-model="item.waktu_selesai" class="w-full sm:w-40 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
              </td>
            </tr>
            <tr v-if="seninKamisData.length === 0">
              <td colspan="3" class="px-6 py-8 text-center text-slate-400 text-sm">Data jam pelajaran kosong.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- TABLE JUMAT -->
      <div v-show="activeTab === 'jumat'" class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/50 border-b border-slate-100">
              <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-wider w-24 text-center">Jam Ke</th>
              <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-wider">Waktu Mulai</th>
              <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-wider">Waktu Selesai</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            <tr v-for="(item, idx) in jumatData" :key="'j-'+idx" class="hover:bg-slate-50/50 transition-colors">
              <td class="px-6 py-3 text-center">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold text-sm">{{ item.jam_ke }}</span>
              </td>
              <td class="px-6 py-3">
                <input type="time" v-model="item.waktu_mulai" class="w-full sm:w-40 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
              </td>
              <td class="px-6 py-3">
                <input type="time" v-model="item.waktu_selesai" class="w-full sm:w-40 px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
              </td>
            </tr>
            <tr v-if="jumatData.length === 0">
              <td colspan="3" class="px-6 py-8 text-center text-slate-400 text-sm">Data jam pelajaran kosong.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useNuxtApp } from '#app'

definePageMeta({
  layout: 'kurikulum',
  middleware: 'kurikulum',
  title: 'Master Jam Pelajaran'
})

const { $toast } = useNuxtApp()
const activeTab = ref('senin-kamis')
const loading = ref(false)
const fetching = ref(true)

const seninKamisData = ref([])
const jumatData = ref([])

const fetchJamPelajaran = async () => {
  try {
    fetching.value = true
    const tokenCookie = useCookie('auth_token')
    const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/kurikulum/jam-pelajaran', {
      headers: { Authorization: `Bearer ${tokenCookie.value}` }
    })
    
    if (res.success) {
      // Parse HH:mm:ss to HH:mm for input type time
      seninKamisData.value = res.data.senin_kamis.map(item => ({
        ...item,
        waktu_mulai: item.waktu_mulai ? item.waktu_mulai.substring(0,5) : '',
        waktu_selesai: item.waktu_selesai ? item.waktu_selesai.substring(0,5) : ''
      }))
      
      jumatData.value = res.data.jumat.map(item => ({
        ...item,
        waktu_mulai: item.waktu_mulai ? item.waktu_mulai.substring(0,5) : '',
        waktu_selesai: item.waktu_selesai ? item.waktu_selesai.substring(0,5) : ''
      }))
      
      // If empty, initialize defaults
      if (seninKamisData.value.length === 0) {
        for (let i = 1; i <= 11; i++) seninKamisData.value.push({ jam_ke: i, waktu_mulai: '', waktu_selesai: '' })
      }
      if (jumatData.value.length === 0) {
        for (let i = 1; i <= 10; i++) jumatData.value.push({ jam_ke: i, waktu_mulai: '', waktu_selesai: '' })
      }
    }
  } catch (error) {
    console.error(error)
    $toast.error('Gagal memuat data jam pelajaran')
  } finally {
    fetching.value = false
  }
}

const save = async () => {
  try {
    loading.value = true
    const tokenCookie = useCookie('auth_token')
    
    // Format back to HH:mm:ss
    const payload = {
      senin_kamis: seninKamisData.value.map(item => ({
        jam_ke: item.jam_ke,
        waktu_mulai: item.waktu_mulai ? item.waktu_mulai + ':00' : '00:00:00',
        waktu_selesai: item.waktu_selesai ? item.waktu_selesai + ':00' : '00:00:00'
      })),
      jumat: jumatData.value.map(item => ({
        jam_ke: item.jam_ke,
        waktu_mulai: item.waktu_mulai ? item.waktu_mulai + ':00' : '00:00:00',
        waktu_selesai: item.waktu_selesai ? item.waktu_selesai + ':00' : '00:00:00'
      }))
    }
    
    const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/kurikulum/jam-pelajaran', {
      method: 'POST',
      headers: { Authorization: `Bearer ${tokenCookie.value}` },
      body: payload
    })
    
    if (res.success) {
      $toast.success('Jam pelajaran berhasil disimpan!')
    }
  } catch (error) {
    console.error(error)
    $toast.error('Gagal menyimpan jam pelajaran')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchJamPelajaran()
})
</script>
