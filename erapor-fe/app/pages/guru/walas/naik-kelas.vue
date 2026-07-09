<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex flex-col xl:flex-row overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="xl:w-[360px] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] overflow-y-auto custom-scrollbar">
        <div class="p-6 space-y-6">
          <div class="bg-gradient-to-r from-sky-600 to-blue-700 rounded-2xl p-5 border border-sky-500 shadow-sm relative overflow-hidden">
            <div class="relative z-10">
              <h3 class="text-sm font-black uppercase tracking-widest text-white">Kenaikan Kelas</h3>
              <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Kelola status kenaikan/kelulusan siswa</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-10">
              <svg class="w-24 h-24 transform translate-x-6 translate-y-6" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
          </div>
          
          <div class="space-y-4">
            <!-- Informasi Kelas -->
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 flex justify-between items-center shadow-sm">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Kelas Anda</span>
                <span class="text-xs font-black text-slate-700">{{ walasData?.kelas?.tingkat }} {{ walasData?.kelas?.nama_kelas || '-' }}</span>
            </div>

            <!-- Search / Filter -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pencarian Siswa</label>
              <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 pointer-events-none">🔍</span>
                  <input type="text" v-model="searchQuery" placeholder="Cari nama siswa..." 
                    class="w-full pl-10 pr-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-semibold text-xs text-slate-700 outline-none">
              </div>
            </div>

            <!-- Daftar Siswa -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pilih Siswa</label>
              <div class="space-y-1">
                <div v-if="loading" class="p-8 flex justify-center">
                  <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                </div>
                <template v-else-if="filteredStudents.length > 0">
                  <button 
                    v-for="(student, idx) in filteredStudents" 
                    :key="student.id"
                    @click="selectedStudent = student"
                    class="w-full text-left p-3 rounded-xl border transition-all duration-200 group flex items-center"
                    :class="selectedStudent?.id === student.id ? 'bg-indigo-50 border-indigo-200 shadow-sm' : 'border-transparent hover:bg-slate-50 hover:border-slate-200'"
                  >
                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500 mr-3 shrink-0"
                        :class="selectedStudent?.id === student.id ? 'bg-indigo-200 text-indigo-700' : 'group-hover:bg-slate-200'">
                      {{ idx + 1 }}
                    </div>
                    <div class="min-w-0 flex-1">
                      <div class="text-sm font-bold truncate transition-colors"
                          :class="selectedStudent?.id === student.id ? 'text-indigo-700' : 'text-slate-700'">
                        {{ student.user?.name || student.nama_lengkap }}
                      </div>
                      <div class="text-[11px] text-slate-500 truncate mt-0.5">
                        NISN: {{ student.nisn || '-' }}
                      </div>
                    </div>
                  </button>
                </template>
                <div v-else class="text-center py-10 px-4 bg-slate-50 rounded-2xl border border-slate-200 border-dashed">
                  <div class="bg-slate-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-xl">📭</span>
                  </div>
                  <p class="text-xs font-bold text-slate-600">Tidak ada siswa sesuai.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          
          <div v-if="!selectedStudent" class="flex-1 flex flex-col items-center justify-center text-center p-8 bg-white rounded-3xl shadow-sm border border-slate-200/60 min-h-0">
            <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-indigo-100/50">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
              </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">Pilih Siswa</h3>
            <p class="text-sm font-semibold text-slate-500 max-w-sm">Silakan pilih siswa dari panel dock di sebelah kiri untuk memproses kenaikan kelas atau kelulusan.</p>
          </div>

          <div v-else class="flex-1 bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden relative flex flex-col min-h-0">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-indigo-500 to-sky-400"></div>
            
            <div class="p-6 sm:p-8 flex-1 overflow-y-auto custom-scrollbar">
              <div class="flex items-start gap-4 mb-8">
                <div class="w-16 h-16 rounded-2xl bg-indigo-50 flex items-center justify-center shrink-0 border border-indigo-100">
                  <span class="text-3xl">🧑‍🎓</span>
                </div>
                <div>
                  <h2 class="text-2xl font-black text-slate-800 tracking-tight">{{ selectedStudent.user?.name || selectedStudent.nama_lengkap }}</h2>
                  <div class="flex flex-wrap items-center gap-3 mt-2 text-[11px] font-bold text-slate-500 uppercase tracking-widest">
                    <span class="flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-slate-300 mr-2"></span> NISN: <strong class="ml-1 text-slate-700">{{ selectedStudent.nisn || '-' }}</strong></span>
                    <span class="flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-slate-300 mr-2"></span> NIS: <strong class="ml-1 text-slate-700">{{ selectedStudent.nis || '-' }}</strong></span>
                  </div>
                </div>
              </div>

              <div v-if="isSemesterGenap && !hasPsatBackup" class="bg-rose-50 border-2 border-rose-200 rounded-2xl p-8 text-center relative overflow-hidden">
                <div class="absolute right-0 top-0 opacity-5 p-4 text-8xl pointer-events-none">🔒</div>
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-rose-100 shadow-sm">
                  <span class="text-2xl">🔒</span>
                </div>
                <h3 class="text-lg font-black text-rose-700 tracking-tight mb-2 uppercase">Fitur Terkunci!</h3>
                <p class="text-[11px] font-semibold text-rose-600 max-w-md mx-auto leading-relaxed">
                  Sesuai aturan sistem, Anda harus memastikan bahwa seluruh data dan aktivitas <strong class="font-black text-rose-800">Semester Genap</strong> telah diarsipkan (Backup) terlebih dahulu sebelum memproses kenaikan kelas.
                  <br><br>
                  Harap hubungi <strong class="font-black text-rose-800">Admin</strong> untuk melakukan <span class="bg-white px-2 py-0.5 rounded text-rose-800 font-bold border border-rose-200 shadow-sm">Backup Arsip (Mode PSAT)</span>.
                </p>
              </div>

              <div v-else class="bg-slate-50 rounded-2xl p-6 border border-slate-200 mb-6 relative overflow-hidden">
                <div class="absolute right-0 top-0 opacity-5 p-4 text-6xl pointer-events-none">🔄</div>
                <label class="block text-[11px] font-black tracking-widest text-slate-500 uppercase mb-3 ml-1 relative z-10">Tentukan Status Baru:</label>
                <div class="relative z-10">
                  <select v-model="formKelasTujuan" class="block w-full rounded-xl border-2 border-slate-200/70 py-3.5 pl-4 pr-10 text-slate-800 text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 bg-white shadow-sm transition-all appearance-none cursor-pointer outline-none">
                    <option value="" disabled>-- Pilih Status / Kelas Tujuan --</option>
                    <option v-for="k in availableClasses" :key="k.id" :value="k.id">
                      {{ k.tingkat === walasData?.kelas?.tingkat ? 'Tinggal di Kelas' : 'Naik ke Kelas' }}: {{ k.tingkat }} {{ k.nama_kelas }} ({{ k.tahun_ajaran?.tahun }})
                    </option>
                    <option value="LULUS" class="font-bold text-emerald-600">🎓 Luluskan Siswa Ini</option>
                  </select>
                  <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                      <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                  </div>
                </div>
                <div class="mt-4 flex items-start p-3 bg-amber-50 border border-amber-100 rounded-xl relative z-10">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 shrink-0 text-amber-500 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                  </svg>
                  <p class="text-[11px] font-semibold text-amber-800 leading-relaxed">
                    <strong class="font-black">Catatan:</strong> Mengubah status akan langsung memperbarui database. Siswa mungkin akan hilang dari daftar kelas Anda saat ini jika kelas tujuannya berbeda.
                  </p>
                </div>
              </div>
            </div>
            
            <div v-if="!(isSemesterGenap && !hasPsatBackup)" class="p-6 bg-slate-50 border-t border-slate-200 flex justify-end gap-3 shrink-0">
                <button @click="selectedStudent = null" class="px-6 py-3 bg-white border border-slate-300 text-slate-700 text-[11px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-50 shadow-sm transition-colors">
                  Batal
                </button>
                <button 
                  @click="saveNaikKelas" 
                  :disabled="isSaving || !formKelasTujuan"
                  class="px-8 py-3 bg-indigo-600 text-white text-[11px] font-black uppercase tracking-widest rounded-xl shadow-md hover:shadow-lg hover:bg-indigo-700 active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                >
                  <span v-if="isSaving" class="mr-2 animate-spin">⏳</span>
                  {{ isSaving ? 'Menyimpan...' : 'Simpan Status' }}
                </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'

definePageMeta({
  layout: 'guru',
  middleware: 'guru',
  title: 'Kenaikan Kelas'
})

const walasData = ref(null)
const students = ref([])
const availableClasses = ref([])
const selectedStudent = ref(null)
const formKelasTujuan = ref('')
const searchQuery = ref('')
const isSemesterGenap = ref(false)
const hasPsatBackup = ref(false)

const loading = ref(true)
const isSaving = ref(false)

const filteredStudents = computed(() => {
    if (!searchQuery.value) return students.value
    const q = searchQuery.value.toLowerCase()
    return students.value.filter(s => 
        (s.user?.name && s.user.name.toLowerCase().includes(q)) || 
        (s.nama_lengkap && s.nama_lengkap.toLowerCase().includes(q))
    )
})

const loadData = async () => {
  loading.value = true
  try {
    const token = useCookie('auth_token').value
    const res = await $fetch('http://localhost:8000/api/guru/walas/cetak', {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    if (res.success) {
      walasData.value = res.data
      students.value = res.data.siswas || []
      availableClasses.value = res.data.all_kelas || []
      isSemesterGenap.value = res.data.is_semester_genap || false
      hasPsatBackup.value = res.data.has_psat_backup || false
    }
  } catch (error) {
    console.error('Failed to load data:', error)
  } finally {
    loading.value = false
  }
}

const saveNaikKelas = async () => {
  if (!selectedStudent.value || !formKelasTujuan.value) return

  isSaving.value = true
  try {
    const token = useCookie('auth_token').value
    const res = await $fetch('http://localhost:8000/api/guru/walas/naik-kelas', {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}` },
      body: {
        siswa_id: selectedStudent.value.id,
        kelas_id_tujuan: formKelasTujuan.value
      }
    })

    if (res.success) {
      useNuxtApp().$swal.fire({
          title: 'Berhasil',
          text: res.message || 'Status kenaikan kelas tersimpan.',
          icon: 'success',
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
      })
      selectedStudent.value = null
      formKelasTujuan.value = ''
      await loadData() // Refresh list since the student might have moved
    } else {
      throw new Error(res.message || 'Terjadi kesalahan')
    }
  } catch (error) {
    console.error('Error saving:', error)
    useNuxtApp().$swal.fire({
        title: 'Gagal',
        text: error.data?.message || error.message || 'Terjadi kesalahan koneksi saat menyimpan.',
        icon: 'error',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    })
  } finally {
    isSaving.value = false
  }
}

onMounted(() => {
  loadData()
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #94a3b8; }
</style>
