<template>
  <div class="space-y-6 pb-20 print:pb-0 print:space-y-0">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-5 rounded-2xl shadow-sm border border-slate-100 print:hidden">
      <div>
        <h1 class="text-xl font-bold text-slate-800">Catatan Kenaikan Kelas</h1>
        <p class="text-sm text-slate-500 mt-1">Rangkuman data siswa sebagai pendamping arsip rapat kenaikan kelas.</p>
      </div>
      <div class="mt-4 sm:mt-0 flex gap-2">
        <button @click="printPage" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl shadow-sm transition-all flex items-center">
          <span class="mr-2">🖨️</span> Cetak Dokumen
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="pending" class="flex flex-col items-center justify-center py-20 bg-white rounded-2xl border border-slate-100 shadow-sm print:hidden">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mb-4"></div>
      <p class="text-slate-500 font-medium">Memuat data kenaikan kelas...</p>
    </div>

    <!-- Print Area -->
    <div v-else-if="data" class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 print:p-0 print:border-none print:shadow-none print:bg-transparent print-area">
      <!-- Kop Surat / Header Laporan -->
      <div class="text-center mb-8">
        <h2 class="text-xl font-bold uppercase">{{ data.sekolah }}</h2>
        <h3 class="text-lg font-bold uppercase mt-1">CATATAN KENAIKAN KELAS</h3>
        <p class="text-md mt-1">Tahun Pelajaran {{ data.tahun_ajaran }}</p>
      </div>

      <!-- Info Kelas & Wali Kelas -->
      <div class="flex justify-between items-end mb-4 text-sm">
        <table class="border-none">
          <tbody>
            <tr>
              <td class="pr-4 py-1">Kelas/ Jurusan</td>
              <td class="px-2 py-1">:</td>
              <td class="font-semibold py-1">{{ data.kelas }}</td>
            </tr>
            <tr>
              <td class="pr-4 py-1 align-top">Jumlah Siswa</td>
              <td class="px-2 py-1 align-top">:</td>
              <td class="py-1">
                <div class="font-semibold">{{ data.jumlah_siswa }} Siswa</div>
                <div class="text-slate-600 print:text-black mt-1 ml-4">{{ data.jumlah_laki }} Siswa Laki-laki</div>
                <div class="text-slate-600 print:text-black mt-1 ml-4">{{ data.jumlah_perempuan }} Siswa Perempuan</div>
              </td>
            </tr>
          </tbody>
        </table>

        <div class="text-right">
          <div class="mb-1">Wali Kelas : <span class="font-semibold uppercase border-b border-black print:border-black">{{ data.wali_kelas }}</span></div>
        </div>
      </div>

      <!-- Tabel Data -->
      <div class="overflow-x-auto print:overflow-visible">
        <table class="w-full text-xs border-collapse table-auto print-table">
          <thead>
            <tr>
              <th rowspan="2" class="border border-black p-2 text-center w-10">No</th>
              <th rowspan="2" class="border border-black p-2 text-center min-w-[150px]">Nama Siswa</th>
              <th colspan="5" class="border border-black p-2 text-center">Masalah</th>
              <th colspan="2" class="border border-black p-2 text-center">Kesimpulan Rapat</th>
              <th rowspan="2" class="border border-black p-2 text-center w-16">Ket</th>
            </tr>
            <tr>
              <th class="border border-black p-2 text-center min-w-[120px]">% Hadir</th>
              <th class="border border-black p-2 text-center min-w-[120px]">Mapel tidak Lulus</th>
              <th class="border border-black p-2 text-center w-16">Nilai</th>
              <th class="border border-black p-2 text-center min-w-[150px]">Sikap/Perilaku Kasus</th>
              <th class="border border-black p-2 text-center min-w-[150px]">Tindak Lanjut Kasus</th>
              <th class="border border-black p-2 text-center w-16">Naik Kelas</th>
              <th class="border border-black p-2 text-center w-16">Tidak Naik</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="data.tabel.length === 0">
              <td colspan="10" class="border border-black p-4 text-center text-slate-500 italic">Tidak ada data siswa.</td>
            </tr>
            <tr v-for="siswa in data.tabel" :key="siswa.no">
              <td class="border border-black p-2 text-center">{{ siswa.no }}</td>
              <td class="border border-black p-2">{{ siswa.nama }}</td>
              <td class="border border-black p-2">
                <div><span class="font-semibold">Smt Ganjil:</span> {{ siswa.absensi.ganjil }}</div>
                <div class="mt-1"><span class="font-semibold">Smt Genap:</span> {{ siswa.absensi.genap }}</div>
              </td>
              <td class="border border-black p-2">
                <template v-if="siswa.mapel_bawah_kkm.length > 0">
                  <div v-for="(m, i) in siswa.mapel_bawah_kkm" :key="i" class="mb-1 last:mb-0">
                    {{ i + 1 }}. {{ m.mapel }} <span class="text-[10px] text-gray-500 print:text-gray-700">({{ m.semester }})</span>
                  </div>
                </template>
                <span v-else class="text-gray-400 italic text-[10px] print:text-transparent">-</span>
              </td>
              <td class="border border-black p-2 text-center">
                <template v-if="siswa.mapel_bawah_kkm.length > 0">
                  <div v-for="(m, i) in siswa.mapel_bawah_kkm" :key="i" class="mb-1 last:mb-0">
                    {{ m.nilai }}
                  </div>
                </template>
              </td>
              <td class="border border-black p-2 whitespace-pre-wrap">{{ siswa.sikap_kasus || '-' }}</td>
              <td class="border border-black p-2 whitespace-pre-wrap">{{ siswa.tindak_lanjut || '-' }}</td>
              <!-- Kosong untuk diisi manual saat rapat -->
              <td class="border border-black p-2"></td>
              <td class="border border-black p-2"></td>
              <td class="border border-black p-2"></td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Footer Info -->
      <div class="mt-4 text-[10px] text-slate-500 print:text-black italic">
        * Catatan Kenaikan Kelas ini digenerate dari e-Rapor pada {{ currentDateTime }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'

definePageMeta({
  layout: "walas",
  title: 'Catatan Kenaikan',
  middleware: 'guru'
})

const data = ref(null)
const pending = ref(true)
const error = ref(null)

const loadData = async () => {
    pending.value = true
    error.value = null
    try {
        const token = useCookie('auth_token').value
        const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/guru/walas/kenaikan-kelas`, {
            headers: { 'Authorization': `Bearer ${token}` }
        })
        if (res && res.success) {
            data.value = res.data
        } else {
            error.value = res?.message || 'Gagal memuat data'
        }
    } catch (err) {
        error.value = err.message || 'Terjadi kesalahan'
    } finally {
        pending.value = false
    }
}

onMounted(() => {
    loadData()
})

const currentDateTime = computed(() => {
  const d = new Date()
  return d.toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' })
})

const printPage = () => {
  const printContent = document.querySelector('.print-area');
  if (!printContent) return;

  const clone = printContent.cloneNode(true);
  clone.id = 'temp-print-container';
  document.body.appendChild(clone);

  const style = document.createElement('style');
  style.innerHTML = `
      @media print {
          body > *:not(#temp-print-container) { display: none !important; }
          #temp-print-container { display: block !important; position: absolute; left: 0; top: 0; width: 100%; background: white; color: black; }
          @page { size: landscape; margin: 1cm; }
          .print-table { width: 100%; color: black; border-collapse: collapse; }
          .print-table th, .print-table td { border: 1px solid black !important; color: black !important; padding: 8px; }
      }
  `;
  document.head.appendChild(style);

  window.print();

  document.body.removeChild(clone);
  document.head.removeChild(style);
}
</script>

<style>
/* Print Styles untuk mode langsung print Ctrl+P jika tidak menggunakan tombol */
@media print {
  @page {
    size: landscape;
    margin: 1cm;
  }
  
  body > *:not(#__nuxt) { display: none; }
  
  .print-table {
    width: 100%;
    color: black;
  }

  .print-table th, .print-table td {
    border-color: black !important;
    color: black !important;
  }
}
</style>
