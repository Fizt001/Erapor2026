<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex flex-col xl:flex-row overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="xl:w-[360px] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] overflow-y-auto custom-scrollbar">
        <div class="p-6 space-y-6">
          <div class="bg-gradient-to-r from-sky-600 to-blue-700 rounded-2xl p-5 border border-sky-500 shadow-sm relative overflow-hidden">
            <div class="relative z-10">
              <h3 class="text-sm font-black uppercase tracking-widest text-white">Prestasi Siswa</h3>
              <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Input data kejuaraan siswa kelas Anda</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-10">
              <svg class="w-24 h-24 transform translate-x-6 translate-y-6" fill="currentColor" viewBox="0 0 24 24"><path d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
            </div>
          </div>
          
          <div class="space-y-4">
            <!-- Aksi Tambah -->
            <button @click="openModal()" class="w-full px-5 py-3.5 bg-sky-600 hover:bg-sky-700 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-sm transition-all active:scale-95 flex items-center justify-center gap-2">
              <span class="text-lg leading-none">+</span> Tambah Prestasi
            </button>

            <!-- Search / Filter -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pencarian Prestasi</label>
              <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 pointer-events-none">🔍</span>
                  <input type="text" v-model="searchQuery" placeholder="Cari nama siswa atau prestasi..." 
                    class="w-full pl-10 pr-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          
          <!-- Loading State -->
          <div v-if="isLoading" class="flex-grow flex flex-col items-center justify-center p-20 opacity-60 bg-white rounded-3xl shadow-sm border border-slate-200/60">
            <div class="w-10 h-10 border-4 border-sky-500 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Memuat Data Prestasi...</span>
          </div>

          <!-- Forbidden State -->
          <div v-else-if="errorMessage && errorMessage.toLowerCase().includes('wali kelas')" class="flex-grow flex flex-col items-center justify-center py-20 text-center bg-white rounded-3xl border border-slate-200/60 shadow-sm">
            <div class="w-24 h-24 bg-amber-50 border-4 border-amber-100 rounded-full flex items-center justify-center mb-6 shadow-inner">
                <span class="text-5xl">🚫</span>
            </div>
            <h3 class="text-2xl font-black text-slate-800 mb-2 tracking-tight">Akses Ditolak</h3>
            <p class="text-slate-500 font-medium max-w-md mx-auto leading-relaxed text-sm">
                Anda tidak terdaftar sebagai Wali Kelas pada Tahun Ajaran yang sedang aktif saat ini. Data prestasi tidak tersedia.
            </p>
          </div>

          <!-- Error State -->
          <div v-else-if="errorMessage" class="flex-grow flex flex-col items-center justify-center p-16 text-center bg-white rounded-3xl shadow-sm border border-slate-200/60">
            <div class="text-rose-500 text-4xl mb-4">⚠️</div>
            <h3 class="text-rose-800 font-black mb-1">Gagal Memuat Data</h3>
            <p class="text-rose-600 text-sm font-semibold max-w-md">{{ errorMessage }}</p>
            <button @click="fetchData" class="mt-4 px-4 py-2 bg-rose-100 hover:bg-rose-200 text-rose-700 text-[10px] font-black uppercase tracking-widest rounded-lg transition-colors">
              Coba Lagi
            </button>
          </div>

          <!-- List Area -->
          <div v-else class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            
            <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-white shrink-0 z-10">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center text-lg border border-sky-100">🏆</div>
                <div>
                  <h3 class="text-[13px] font-black leading-none uppercase tracking-wide text-slate-800">Daftar Prestasi</h3>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ filteredPrestasi.length }} Prestasi Tercatat</p>
                </div>
              </div>
            </div>

            <div class="flex-1 overflow-auto custom-scrollbar relative bg-slate-50/30">
              <table class="w-full text-left border-collapse bg-white">
                <thead class="sticky top-0 z-20 shadow-sm">
                  <tr class="bg-slate-100 border-b border-slate-200">
                    <th class="py-3 px-4 text-center text-[10px] font-black uppercase tracking-widest text-slate-500 w-[60px] border-r border-slate-200">No</th>
                    <th class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500 border-r border-slate-200 min-w-[200px] shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] sticky left-0 bg-slate-100 z-30">Siswa</th>
                    <th class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500 min-w-[200px] border-r border-slate-200">Prestasi</th>
                    <th class="py-3 px-4 text-center text-[10px] font-black uppercase tracking-widest text-slate-500 border-r border-slate-200">Tingkat</th>
                    <th class="py-3 px-4 text-center text-[10px] font-black uppercase tracking-widest text-slate-500 border-r border-slate-200 w-24">Tahun</th>
                    <th class="py-3 px-4 text-center text-[10px] font-black uppercase tracking-widest text-slate-500 w-24 bg-slate-50">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-for="(item, index) in filteredPrestasi" :key="item.id" class="hover:bg-slate-50/80 transition-colors group">
                    <td class="py-3 px-4 text-center text-[11px] font-bold text-slate-400 border-r border-slate-100">{{ index + 1 }}</td>
                    <td class="py-3 px-4 border-r border-slate-100 sticky left-0 bg-white group-hover:bg-slate-50/90 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.02)] z-10">
                      <div class="text-[12px] font-black text-slate-700 uppercase tracking-wide">{{ item.siswa ? item.siswa.name : '-' }}</div>
                      <div class="text-[10px] text-slate-400 font-bold tracking-widest mt-0.5">NIS: {{ item.siswa ? item.siswa.nis : '-' }}</div>
                    </td>
                    <td class="py-3 px-4 border-r border-slate-100">
                      <div class="text-[12px] font-black text-slate-700">{{ item.nama_prestasi }}</div>
                      <div class="text-[10px] text-sky-600 font-bold mt-0.5">{{ item.jenis_prestasi }} • {{ item.penyelenggara }}</div>
                    </td>
                    <td class="py-3 px-4 text-center border-r border-slate-100">
                      <span class="inline-flex items-center justify-center px-2 py-1 rounded bg-amber-50 text-amber-600 border border-amber-100 text-[10px] font-black uppercase tracking-widest">
                        {{ item.tingkat }}
                      </span>
                    </td>
                    <td class="py-3 px-4 text-center border-r border-slate-100">
                      <div class="text-[11px] font-bold text-slate-600">{{ item.tahun }}</div>
                      <div class="text-[9px] font-bold text-slate-400 uppercase mt-0.5">{{ item.titimangsa ? (item.titimangsa.nama_periode_panjang || item.titimangsa.nama_periode) : '-' }}</div>
                    </td>
                    <td class="py-3 px-4 text-center bg-slate-50/30">
                      <div class="flex items-center justify-center gap-2">
                        <button @click="openModal(item)" class="w-8 h-8 rounded-lg bg-white border border-sky-200 text-sky-600 hover:bg-sky-50 flex items-center justify-center transition-colors shadow-sm" title="Edit">
                          ✏️
                        </button>
                        <button @click="confirmDelete(item)" class="w-8 h-8 rounded-lg bg-white border border-rose-200 text-rose-500 hover:bg-rose-50 flex items-center justify-center transition-colors shadow-sm" title="Hapus">
                          🗑️
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="filteredPrestasi.length === 0">
                    <td colspan="6" class="py-12 text-center text-slate-500 bg-slate-50/50">
                      <div class="text-4xl mb-3 opacity-50">🏆</div>
                      <div class="text-xs font-bold">Belum ada data prestasi yang sesuai.</div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Form -->
    <Teleport to="body">
      <div v-if="showModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl animate-scaleIn flex flex-col max-h-[90vh] overflow-hidden border border-slate-200/50">
          <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 shrink-0 flex justify-between items-center">
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">{{ isEditing ? 'Edit Prestasi' : 'Tambah Prestasi' }}</h3>
            <button @click="closeModal" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-200 text-slate-500 hover:bg-slate-300 hover:text-slate-700 transition-colors font-bold">&times;</button>
          </div>
          
          <div class="p-6 overflow-y-auto custom-scrollbar flex-1 bg-white">
            <form @submit.prevent="submitForm" class="space-y-5">
              
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                  <div class="space-y-1.5">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Siswa <span class="text-rose-500">*</span></label>
                    <select v-model="form.siswa_id" required :disabled="isEditing" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none transition-all appearance-none disabled:opacity-60 disabled:cursor-not-allowed">
                      <option value="" disabled>-- Pilih Siswa --</option>
                      <option v-for="s in siswas" :key="s.id" :value="s.id">{{ s.nama }} ({{ s.nis }})</option>
                    </select>
                  </div>
    
                  <div class="space-y-1.5">
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Periode Penyerahan <span class="text-rose-500">*</span></label>
                    <select v-model="form.titimangsa_id" required class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none transition-all appearance-none">
                      <option value="" disabled>-- Pilih Periode --</option>
                      <option v-for="t in titimangsas" :key="t.id" :value="t.id">{{ t.nama }}</option>
                    </select>
                  </div>
              </div>

              <div class="space-y-1.5">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Nama Prestasi/Kejuaraan <span class="text-rose-500">*</span></label>
                <input type="text" v-model="form.nama_prestasi" required placeholder="Contoh: Juara 1 Lomba Cerdas Cermat" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none transition-all">
              </div>

              <div class="grid grid-cols-2 gap-5">
                <div class="space-y-1.5">
                  <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Jenis <span class="text-rose-500">*</span></label>
                  <select v-model="form.jenis_prestasi" required class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none transition-all appearance-none">
                    <option value="" disabled>Pilih</option>
                    <option value="Akademik">Akademik</option>
                    <option value="Non-Akademik">Non-Akademik</option>
                  </select>
                </div>
                <div class="space-y-1.5">
                  <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Tingkat <span class="text-rose-500">*</span></label>
                  <select v-model="form.tingkat" required class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none transition-all appearance-none">
                    <option value="" disabled>Pilih Tingkat</option>
                    <option value="Sekolah">Sekolah</option>
                    <option value="Kecamatan">Kecamatan</option>
                    <option value="Kab/Kota">Kabupaten/Kota</option>
                    <option value="Provinsi">Provinsi</option>
                    <option value="Nasional">Nasional</option>
                    <option value="Internasional">Internasional</option>
                  </select>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-5">
                <div class="space-y-1.5">
                  <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Penyelenggara <span class="text-rose-500">*</span></label>
                  <input type="text" v-model="form.penyelenggara" required placeholder="Dinas Pendidikan" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none transition-all">
                </div>
                <div class="space-y-1.5">
                  <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Tahun <span class="text-rose-500">*</span></label>
                  <input type="text" v-model="form.tahun" required placeholder="2026" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none transition-all">
                </div>
              </div>

              <div class="space-y-1.5">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Keterangan Tambahan</label>
                <textarea v-model="form.keterangan" rows="2" class="w-full bg-slate-50 border-2 border-slate-200/70 rounded-xl px-4 py-2.5 text-xs font-semibold focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none transition-all resize-none" placeholder="Opsional..."></textarea>
              </div>

            </form>
          </div>
          
          <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex justify-end gap-3 shrink-0 rounded-b-3xl">
            <button @click="closeModal" type="button" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-xl text-[11px] font-black uppercase tracking-widest transition-colors shadow-sm">
              Batal
            </button>
            <button @click="submitForm" type="button" :disabled="isSubmitting" class="px-6 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl text-[11px] font-black uppercase tracking-widest transition-all shadow-md active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
              <span v-if="isSubmitting" class="mr-2 animate-spin">⏳</span>
              Simpan Data
            </button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'

definePageMeta({
  layout: "walas",
  title: 'Prestasi Siswa',
  middleware: 'guru'
})

const isLoading = ref(true)
const isSubmitting = ref(false)
const errorMessage = ref('')
const prestasiList = ref([])
const siswas = ref([])
const titimangsas = ref([])
const searchQuery = ref('')

const showModal = ref(false)
const isEditing = ref(false)
const editId = ref(null)

const form = ref({
    siswa_id: '',
    titimangsa_id: '',
    nama_prestasi: '',
    jenis_prestasi: '',
    tingkat: '',
    penyelenggara: '',
    tahun: '',
    keterangan: ''
})

const filteredPrestasi = computed(() => {
    if (!searchQuery.value) return prestasiList.value
    const q = searchQuery.value.toLowerCase()
    return prestasiList.value.filter(p => 
        (p.siswa?.name && p.siswa.name.toLowerCase().includes(q)) || 
        (p.nama_prestasi && p.nama_prestasi.toLowerCase().includes(q))
    )
})

const resetForm = () => {
    form.value = {
        siswa_id: '',
        titimangsa_id: '',
        nama_prestasi: '',
        jenis_prestasi: '',
        tingkat: '',
        penyelenggara: '',
        tahun: new Date().getFullYear().toString(),
        keterangan: ''
    }
    isEditing.value = false
    editId.value = null
}

const openModal = (item = null) => {
    if (item && typeof item === 'object' && item.id) {
        isEditing.value = true
        editId.value = item.id
        form.value = {
            siswa_id: item.siswa_id,
            titimangsa_id: item.titimangsa_id,
            nama_prestasi: item.nama_prestasi,
            jenis_prestasi: item.jenis_prestasi,
            tingkat: item.tingkat,
            penyelenggara: item.penyelenggara,
            tahun: item.tahun,
            keterangan: item.keterangan || ''
        }
    } else {
        resetForm()
        if (siswas.value.length > 0 && !form.value.siswa_id) {
            // Biarkan kosong agar user milih
        }
    }
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    resetForm()
}

const fetchData = async () => {
    isLoading.value = true
    errorMessage.value = ''
    
    try {
        const tokenCookie = useCookie('auth_token')
        const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/guru/walas/prestasi', {
            headers: {
                'Authorization': `Bearer ${tokenCookie.value}`,
                'Accept': 'application/json'
            }
        })
        
        if (res.success) {
            prestasiList.value = res.data.prestasi
            siswas.value = res.data.siswas
            titimangsas.value = res.data.titimangsas
            
            // set default titimangsa if available
            if (titimangsas.value.length > 0 && !form.value.titimangsa_id) {
                form.value.titimangsa_id = titimangsas.value[0].id
            }
        } else {
            errorMessage.value = res.message || 'Gagal mengambil data'
        }
    } catch (error) {
        console.error('Fetch error:', error)
        errorMessage.value = error.response?._data?.message || 'Terjadi kesalahan jaringan atau server.'
    } finally {
        isLoading.value = false
    }
}

const submitForm = async () => {
    // Validasi manual
    if (!form.value.siswa_id || !form.value.titimangsa_id || !form.value.nama_prestasi || !form.value.jenis_prestasi || !form.value.tingkat) {
        useNuxtApp().$swal.fire({
            title: 'Peringatan',
            text: 'Mohon lengkapi semua field yang wajib (*)',
            icon: 'warning',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
        return;
    }

    isSubmitting.value = true
    
    try {
        const tokenCookie = useCookie('auth_token')
        const url = isEditing.value 
            ? `${import.meta.env.VITE_API_BASE_URL}/api/guru/walas/prestasi/${editId.value}`
            : import.meta.env.VITE_API_BASE_URL + '/api/guru/walas/prestasi'
            
        const method = isEditing.value ? 'PUT' : 'POST'
        
        const res = await $fetch(url, {
            method: method,
            headers: {
                'Authorization': `Bearer ${tokenCookie.value}`,
                'Accept': 'application/json'
            },
            body: form.value
        })
        
        if (res.success) {
            closeModal()
            await fetchData()
            useNuxtApp().$swal.fire({
                title: 'Berhasil',
                text: 'Data prestasi tersimpan.',
                icon: 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        } else {
            throw new Error(res.message || 'Gagal menyimpan data')
        }
    } catch (error) {
        console.error('Submit error:', error)
        useNuxtApp().$swal.fire({
            title: 'Gagal',
            text: error.response?._data?.message || error.message || 'Terjadi kesalahan jaringan atau server.',
            icon: 'error',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    } finally {
        isSubmitting.value = false
    }
}

const confirmDelete = async (item) => {
    const answer = await useSwal().fire({
        title: 'Hapus Prestasi?',
        text: `Apakah Anda yakin ingin menghapus prestasi "${item.nama_prestasi}" milik ${item.siswa?.name}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    })
    
    if (answer.isConfirmed) {
        try {
            const tokenCookie = useCookie('auth_token')
            const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/guru/walas/prestasi/${item.id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${tokenCookie.value}`,
                    'Accept': 'application/json'
                }
            })
            
            if (res.success) {
                await fetchData()
                useNuxtApp().$swal.fire({
                    title: 'Berhasil',
                    text: 'Data prestasi dihapus.',
                    icon: 'success',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                throw new Error(res.message || 'Gagal menghapus data')
            }
        } catch (error) {
            console.error('Delete error:', error)
            useNuxtApp().$swal.fire({
                title: 'Gagal',
                text: error.response?._data?.message || error.message || 'Terjadi kesalahan jaringan atau server.',
                icon: 'error',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        }
    }
}

onMounted(() => {
    fetchData()
})
</script>

<style scoped>
.animate-scaleIn {
  animation: scaleIn 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes scaleIn {
  from { opacity: 0; transform: scale(0.95) translateY(-10px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}

.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #94a3b8; }
</style>
