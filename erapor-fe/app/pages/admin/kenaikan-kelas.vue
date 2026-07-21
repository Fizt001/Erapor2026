<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <div class="flex-1 flex flex-col xl:flex-row overflow-hidden relative">

      <!-- ============ PANEL DOCK KIRI (Filter) ============ -->
      <div class="xl:w-[360px] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)]">
        <div class="p-6 space-y-5 overflow-y-auto custom-scrollbar flex-1">

          <!-- Header Dock -->
          <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl p-5 border border-emerald-500 shadow-sm relative overflow-hidden">
            <div class="relative z-10">
              <h3 class="text-sm font-black uppercase tracking-widest text-white">Kenaikan Kelas</h3>
              <p class="text-[10px] text-emerald-200 font-semibold mt-0.5">Mutasi & Promosi Siswa Antar Tahun Ajaran</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-10">
              <svg class="w-24 h-24 transform translate-x-6 translate-y-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            </div>
          </div>

          <!-- Filter Tahun Ajaran -->
          <div class="space-y-2">
            <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest ml-1">Tahun Ajaran</label>
            <select v-model="selectedTahunAjaran" @change="onTahunChange"
              class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-sm font-bold text-slate-700 outline-none">
              <option value="">-- Pilih Tahun Ajaran --</option>
              <option v-for="ta in uniqueTahunAjaran" :key="ta.id" :value="ta.id">
                TA {{ ta.tahun }}
              </option>
            </select>
          </div>

          <!-- Filter Kelas -->
          <div class="space-y-2">
            <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest ml-1">Kelas Sumber</label>
            <select v-model="selectedKelas" @change="loadSiswa" :disabled="!selectedTahunAjaran"
              class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-sm font-bold text-slate-700 outline-none disabled:opacity-50 disabled:cursor-not-allowed">
              <option value="">-- Pilih Kelas --</option>
              <option v-for="k in kelasList" :key="k.id" :value="k.id">
                {{ k.tingkat }} {{ k.nama_kelas }}
              </option>
            </select>
          </div>

          <!-- Filter Kelas Tujuan (untuk naik/tinggal) -->
          <div class="space-y-2">
            <label class="block text-[11px] font-black text-slate-500 uppercase tracking-widest ml-1">Kelas Tujuan (Tahun Ajaran Baru)</label>
            <select v-model="kelasTujuanId"
              class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all text-sm font-bold text-slate-700 outline-none">
              <option value="">-- (Opsional) Pilih Kelas Tujuan --</option>
              <optgroup v-for="ta in uniqueTahunAjaran" :key="ta.id" :label="`TA ${ta.tahun}`">
                <option v-for="k in (setupData.kelas_by_tahun[ta.id] || [])" :key="k.id" :value="k.id">
                  {{ k.tingkat }} {{ k.nama_kelas }}
                </option>
              </optgroup>
            </select>
          </div>

          <!-- Legenda Rekomendasi -->
          <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 space-y-2">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-3">Legenda Rekomendasi Walas</p>
            <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span><span class="text-[11px] font-bold text-slate-600">Naik Kelas</span></div>
            <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span><span class="text-[11px] font-bold text-slate-600">Tinggal Kelas</span></div>
            <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-blue-500 shrink-0"></span><span class="text-[11px] font-bold text-slate-600">Lulus</span></div>
            <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-slate-400 shrink-0"></span><span class="text-[11px] font-bold text-slate-600">Belum Ditentukan</span></div>
          </div>

          <!-- Aksi Massal -->
          <div v-if="siswaList.length > 0" class="bg-emerald-50 rounded-xl border border-emerald-100 p-4 space-y-3">
            <p class="text-[10px] font-black uppercase tracking-widest text-emerald-700">Aksi Massal Terpilih ({{ selectedIds.length }})</p>
            <div class="grid grid-cols-2 gap-2">
              <button @click="setAksiMassal('naik')" :disabled="selectedIds.length === 0" class="py-2 px-3 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 font-bold rounded-lg text-[10px] uppercase tracking-widest transition-all disabled:opacity-40 disabled:cursor-not-allowed">✅ Naik Kelas</button>
              <button @click="setAksiMassal('tinggal')" :disabled="selectedIds.length === 0" class="py-2 px-3 bg-amber-100 hover:bg-amber-200 text-amber-700 font-bold rounded-lg text-[10px] uppercase tracking-widest transition-all disabled:opacity-40 disabled:cursor-not-allowed">⚠️ Tinggal</button>
              <button @click="setAksiMassal('lulus')" :disabled="selectedIds.length === 0" class="py-2 px-3 bg-blue-100 hover:bg-blue-200 text-blue-700 font-bold rounded-lg text-[10px] uppercase tracking-widest transition-all disabled:opacity-40 disabled:cursor-not-allowed">🎓 Lulus</button>
              <button @click="setAksiMassal('pindah_sekolah')" :disabled="selectedIds.length === 0" class="py-2 px-3 bg-rose-100 hover:bg-rose-200 text-rose-700 font-bold rounded-lg text-[10px] uppercase tracking-widest transition-all disabled:opacity-40 disabled:cursor-not-allowed">🚶 Pindah Sekolah</button>
            </div>
          </div>

        </div>

        <!-- Tombol Simpan -->
        <div class="p-6 border-t border-slate-200 shrink-0">
          <button @click="simpanProses" :disabled="isSaving || siswaList.length === 0"
            class="w-full py-3.5 bg-gradient-to-r from-emerald-600 to-teal-700 text-white font-bold rounded-2xl shadow-lg shadow-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/40 hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-2 uppercase tracking-widest text-xs">
            <span v-if="isSaving" class="animate-spin text-lg">⏳</span>
            <span v-else class="text-lg">💾</span>
            <span>{{ isSaving ? 'Menyimpan...' : 'Simpan Proses Mutasi' }}</span>
          </button>
        </div>
      </div>

      <!-- ============ PANEL FLOW KANAN (Tabel) ============ -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">

            <!-- Table Header -->
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between gap-4 shrink-0 sticky top-0 bg-white/80 backdrop-blur-xl z-10">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-600 shadow-sm flex items-center justify-center text-2xl text-white hidden sm:flex">🎓</div>
                <div>
                  <h3 class="text-sm font-black uppercase tracking-widest text-emerald-700">
                    {{ selectedKelasInfo ? `${selectedKelasInfo.tingkat} ${selectedKelasInfo.nama_kelas}` : 'Pilih Kelas' }}
                  </h3>
                  <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">
                    Total: {{ siswaList.length }} Siswa &bull; Terpilih: {{ selectedIds.length }} 
                  </p>
                </div>
              </div>
              <!-- Select All -->
              <label v-if="siswaList.length > 0" class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-600">
                <input type="checkbox" :checked="selectedIds.length === siswaList.length && siswaList.length > 0" @change="toggleSelectAll" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                Pilih Semua
              </label>
            </div>

            <!-- Loading -->
            <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60">
              <div class="w-8 h-8 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mb-4"></div>
              <span class="text-xs font-bold text-slate-500">Memuat Data Siswa...</span>
            </div>

            <!-- Empty -->
            <div v-else-if="!selectedKelas" class="flex-grow flex items-center justify-center flex-col p-10 text-center opacity-60">
              <div class="text-5xl mb-4">🏫</div>
              <p class="text-sm font-bold text-slate-500">Pilih Tahun Ajaran dan Kelas<br>untuk melihat daftar siswa.</p>
            </div>
            <div v-else-if="siswaList.length === 0" class="flex-grow flex items-center justify-center flex-col p-10 text-center opacity-60">
              <div class="text-5xl mb-4">📋</div>
              <p class="text-sm font-bold text-slate-500">Tidak ada siswa di kelas ini.</p>
            </div>

            <!-- Tabel Siswa -->
            <div v-else class="flex-1 overflow-auto custom-scrollbar">
              <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-slate-50/70 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-200 sticky top-0">
                  <tr>
                    <th class="py-3 px-4 w-10 text-center"></th>
                    <th class="py-3 px-4 w-8 text-center">No</th>
                    <th class="py-3 px-4 min-w-[180px]">Nama Siswa</th>
                    <th class="py-3 px-4 text-center">NIS</th>
                    <th class="py-3 px-4 text-center min-w-[130px]">Rekomendasi Walas</th>
                    <th class="py-3 px-4 text-center min-w-[160px]">Aksi Admin</th>
                    <th class="py-3 px-4 text-center min-w-[200px]">Kelas Tujuan</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-for="(s, idx) in siswaList" :key="s.id"
                    :class="['hover:bg-slate-50/80 transition-colors group bg-white', selectedIds.includes(s.id) ? 'bg-emerald-50/50' : '']">
                    <td class="p-4 text-center">
                      <input type="checkbox" :value="s.id" v-model="selectedIds" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                    </td>
                    <td class="p-4 text-center text-xs font-bold text-slate-400">{{ idx + 1 }}</td>
                    <td class="p-4">
                      <p class="font-black text-slate-800 text-[13px]">{{ s.name }}</p>
                      <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wide">{{ s.status_siswa || 'aktif' }}</p>
                    </td>
                    <td class="p-4 text-center">
                      <span class="inline-flex items-center px-2 py-1 rounded-md bg-slate-100 text-slate-600 text-[11px] font-black uppercase tracking-widest border border-slate-200">{{ s.nis }}</span>
                    </td>
                    <td class="p-4 text-center">
                      <span :class="rekomendasiClass(s.rekomendasi_walas)"
                        class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-[11px] font-black uppercase tracking-widest border">
                        {{ rekomendasiLabel(s.rekomendasi_walas) }}
                      </span>
                    </td>
                    <td class="p-4 text-center">
                      <select v-model="s.aksi_admin"
                        class="w-full text-[11px] font-bold border-2 rounded-xl px-2 py-1.5 outline-none transition-all bg-white border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 text-slate-700">
                        <option value="">-- Pilih --</option>
                        <option value="naik">✅ Naik Kelas</option>
                        <option value="tinggal">⚠️ Tinggal Kelas</option>
                        <option value="lulus">🎓 Lulus</option>
                        <option value="pindah_sekolah">🚶 Pindah Sekolah</option>
                        <option value="keluar">🚶 Keluar</option>
                        <option value="tetap_aktif">🔄 Tetap Aktif</option>
                      </select>
                    </td>
                    <td class="p-4 text-center">
                      <select v-model="s.kelas_tujuan_override"
                        :disabled="!['naik','tinggal'].includes(s.aksi_admin)"
                        class="w-full text-[11px] font-bold border-2 rounded-xl px-2 py-1.5 outline-none transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                        :class="['naik','tinggal'].includes(s.aksi_admin) ? 'bg-white border-emerald-200 focus:border-emerald-500' : 'bg-slate-50 border-slate-200 text-slate-400'">
                        <option value="">{{ kelasTujuanId ? '(Pakai Default)' : '-- Pilih Kelas --' }}</option>
                        <optgroup v-for="ta in setupData.tahun_ajaran" :key="ta.id" :label="`TA ${ta.tahun} Sem ${ta.semester}`">
                          <option v-for="k in (setupData.kelas_by_tahun[ta.id] || [])" :key="k.id" :value="k.id">
                            {{ k.tingkat }} {{ k.nama_kelas }}
                          </option>
                        </optgroup>
                      </select>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>

    </div>

    <!-- MODAL KONFIRMASI -->
    <div v-if="showConfirm" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
      <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden text-center">
        <div class="p-8">
          <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">⚠️</div>
          <h3 class="text-xl font-black text-slate-800 tracking-tight">Konfirmasi Proses Mutasi</h3>
          <p class="text-xs text-slate-500 mt-3 leading-relaxed">
            Anda akan memproses <strong class="text-slate-800">{{ konfirmasiCount }}</strong> siswa.<br>
            Aksi ini akan mengubah data siswa secara permanen.<br>
            <span class="text-rose-500 font-bold">Pastikan sudah benar sebelum menyimpan.</span>
          </p>
          <div class="flex items-center gap-4 mt-8">
            <button @click="showConfirm = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">Batal</button>
            <button @click="executeProses" :disabled="isSaving" class="flex-1 py-3 bg-gradient-to-r from-emerald-600 to-teal-700 text-white font-bold rounded-2xl shadow-lg shadow-emerald-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
              <span v-if="isSaving" class="animate-spin">⏳</span>
              <span>{{ isSaving ? 'Memproses...' : 'Ya, Proses!' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer Info -->
    <div class="bg-slate-100 border-t border-slate-200 text-center py-3 shrink-0 print:hidden z-20">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Created by <span class="text-emerald-600">SMK-Yatindo</span></p>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

definePageMeta({
  layout: 'admin',
  middleware: 'admin',
  title: 'Kenaikan Kelas'
})

const setupData = ref({ tahun_ajaran: [], kelas_by_tahun: {} })
const selectedTahunAjaran = ref('')
const selectedKelas = ref('')
const kelasTujuanId = ref('')
const siswaList = ref([])
const selectedIds = ref([])
const isLoading = ref(false)
const isSaving = ref(false)
const showConfirm = ref(false)

const uniqueTahunAjaran = computed(() => {
  const years = []
  const seen = new Set()
  setupData.value.tahun_ajaran.forEach(ta => {
    if (!seen.has(ta.tahun)) {
      seen.add(ta.tahun)
      years.push(ta)
    }
  })
  // Sort descending if we want newest first, or just return as is
  return years
})

const kelasList = computed(() => {
  if (!selectedTahunAjaran.value) return []
  return setupData.value.kelas_by_tahun[selectedTahunAjaran.value] || []
})

const selectedKelasInfo = computed(() => {
  if (!selectedKelas.value) return null
  return kelasList.value.find(k => k.id == selectedKelas.value) || null
})

const konfirmasiCount = computed(() => siswaList.value.filter(s => s.aksi_admin).length)

const rekomendasiLabel = (r) => {
  const m = { naik: '✅ Naik', tinggal: '⚠️ Tinggal', lulus: '🎓 Lulus', belum_ditentukan: '❓ Belum' }
  return m[r] || '❓ Belum'
}

const rekomendasiClass = (r) => {
  if (r === 'naik') return 'bg-emerald-100 text-emerald-700 border-emerald-200'
  if (r === 'tinggal') return 'bg-amber-100 text-amber-700 border-amber-200'
  if (r === 'lulus') return 'bg-blue-100 text-blue-700 border-blue-200'
  return 'bg-slate-100 text-slate-500 border-slate-200'
}

const fetchSetup = async () => {
  try {
    const token = useCookie('auth_token').value
    const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/admin/kenaikan-kelas/setup', {
      headers: { Authorization: `Bearer ${token}` }
    })
    if (res.success) setupData.value = res
  } catch (e) {
    useSwal().toast('Gagal memuat data setup.', 'error')
  }
}

const onTahunChange = () => {
  selectedKelas.value = ''
  siswaList.value = []
  selectedIds.value = []
}

const loadSiswa = async () => {
  if (!selectedKelas.value) { siswaList.value = []; return }
  isLoading.value = true
  selectedIds.value = []
  try {
    const token = useCookie('auth_token').value
    const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/admin/kenaikan-kelas/${selectedKelas.value}/siswa`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    if (res.success) {
      siswaList.value = res.siswa.map(s => ({
        ...s,
        aksi_admin: '',
        kelas_tujuan_override: ''
      }))
    }
  } catch (e) {
    useSwal().toast('Gagal memuat data siswa.', 'error')
  } finally {
    isLoading.value = false
  }
}

const toggleSelectAll = () => {
  if (selectedIds.value.length === siswaList.value.length) {
    selectedIds.value = []
  } else {
    selectedIds.value = siswaList.value.map(s => s.id)
  }
}

const setAksiMassal = (aksi) => {
  siswaList.value.forEach(s => {
    if (selectedIds.value.includes(s.id)) {
      s.aksi_admin = aksi
    }
  })
}

const simpanProses = () => {
  const yangAkanDiproses = siswaList.value.filter(s => s.aksi_admin)
  if (yangAkanDiproses.length === 0) {
    useSwal().toast('Belum ada aksi yang ditentukan untuk siswa manapun.', 'warning')
    return
  }
  showConfirm.value = true
}

const executeProses = async () => {
  isSaving.value = true
  try {
    const token = useCookie('auth_token').value
    const payload = siswaList.value
      .filter(s => s.aksi_admin)
      .map(s => ({
        id: s.id,
        aksi: s.aksi_admin,
        kelas_tujuan_id: s.kelas_tujuan_override || kelasTujuanId.value || null
      }))

    const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/admin/kenaikan-kelas/proses', {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}` },
      body: { siswa: payload }
    })
    if (res.success) {
      showConfirm.value = false
      useSwal().toast(res.message, 'success')
      await loadSiswa()
    }
  } catch (e) {
    const msg = e?.data?.message || e?.message || 'Gagal memproses.'
    useSwal().toast(msg, 'error')
  } finally {
    isSaving.value = false
  }
}

onMounted(() => {
  fetchSetup()
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>


