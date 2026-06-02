<template>
  <div class="animate-fadeIn max-w-7xl mx-auto pb-12 mt-4">
    <!-- Main Form Architecture -->
    <form @submit.prevent="saveData" class="relative">
      
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- ==============================================
             KIRI: MODERN SIDEBAR TABS (Col 3)
             ============================================== -->
        <div class="hidden lg:block lg:col-span-3 space-y-2 sticky top-6">
          <button 
            v-for="tab in tabs" :key="tab.id" 
            type="button" 
            @click="activeTab = tab.id"
            class="w-full flex items-center gap-4 px-5 py-4 rounded-2xl transition-all duration-300 text-left relative group overflow-hidden"
            :class="activeTab === tab.id ? 'bg-white shadow-md text-emerald-700' : 'text-slate-500 hover:bg-slate-200/50 hover:text-slate-800'"
          >
            <!-- Active Indicator Line -->
            <div 
              class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500 transition-transform origin-left duration-300"
              :class="activeTab === tab.id ? 'scale-y-100' : 'scale-y-0 group-hover:scale-y-50 bg-slate-300'"
            ></div>
            
            <div 
              class="w-10 h-10 rounded-2xl flex items-center justify-center text-xl transition-all duration-300"
              :class="activeTab === tab.id ? 'bg-emerald-100 text-emerald-600 scale-110' : 'bg-slate-100 text-slate-400 group-hover:bg-white group-hover:shadow-sm'"
            >
              {{ tab.icon }}
            </div>
            
            <div>
              <p class="font-bold text-sm tracking-wide">{{ tab.title }}</p>
              <p class="text-[10px] uppercase tracking-wider font-semibold opacity-70 mt-0.5">{{ tab.subtitle }}</p>
            </div>
          </button>
        </div>

        <!-- MOBILE VIEW (Hanya tampil di HP atau layar sempit) -->
        <div class="lg:hidden mb-10 mt-2">
          <div class="grid grid-cols-4 gap-2">
            <button v-for="tab in tabs" :key="'mob-'+tab.id" type="button" @click="activeTab = tab.id"
              :class="activeTab === tab.id ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 ring-2 ring-emerald-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
              class="rounded-2xl flex flex-col items-center justify-center py-4 px-1 transition-all active:scale-95">
              <span class="text-2xl mb-1.5 transition-transform" :class="activeTab === tab.id ? 'scale-110' : ''">{{ tab.icon }}</span>
              <span class="text-[9px] font-black uppercase tracking-wider text-center leading-none">{{ tab.shortTitle }}</span>
            </button>
          </div>
        </div>

        <!-- ==============================================
             KANAN: CONTENT AREA & FORMS (Col 9)
             ============================================== -->
        <div class="lg:col-span-9">
          <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden relative">
            
            <!-- Beautiful Card Header Background -->
            <div class="h-24 bg-gradient-to-r from-emerald-800 to-slate-900 relative overflow-hidden">
                <div class="absolute right-0 bottom-0 transform text-emerald-100 opacity-20 text-8xl -mr-4 -mb-4 font-black">
                  {{ currentTab.icon }}
                </div>
            </div>

            <!-- Card Content Wrapper -->
            <div class="px-8 pb-8 pt-6 relative">
              
              <!-- Floating Tab Title -->
              <div class="absolute -top-8 left-4 lg:-top-12 lg:left-8 bg-white px-4 py-3 lg:px-6 lg:py-4 rounded-2xl lg:rounded-2xl shadow-lg border border-slate-100 flex items-center gap-3 lg:gap-4 z-10 max-w-[85%] lg:max-w-full overflow-hidden">
                 <div class="w-10 h-10 lg:w-12 lg:h-12 flex-shrink-0 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-lg lg:rounded-2xl flex items-center justify-center text-xl lg:text-2xl text-white shadow-inner">
                    {{ currentTab.icon }}
                 </div>
                 <div class="min-w-0">
                    <h3 class="text-sm lg:text-lg font-black text-slate-800 truncate">{{ currentTab.title }}</h3>
                    <p class="text-[9px] lg:text-[11px] font-bold text-slate-400 uppercase tracking-widest truncate">{{ currentTab.subtitle }}</p>
                 </div>
              </div>

              <!-- Jarak untuk floating title -->
              <div class="mt-14">
                <!-- 1. IDENTITAS -->
                <div v-show="activeTab === 'identitas'" class="animate-slideUpFade">
                  <div class="space-y-6">
                    <div>
                      <label class="block text-sm font-bold text-slate-700 mb-2">Nama Sekolah Lengkap <span class="text-rose-500">*</span></label>
                      <input type="text" v-model="form.nama_sekolah" required class="w-full px-5 py-3.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="Misal: SMK Negeri 1 Jakarta">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">NPSN</label>
                        <input type="text" v-model="form.npsn" class="w-full px-5 py-3.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="Nomor Pokok Sekolah Nasional">
                      </div>
                      <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">NSS / NDS</label>
                        <input type="text" v-model="form.nss" class="w-full px-5 py-3.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="Nomor Statistik Sekolah">
                      </div>
                    </div>
                  </div>
                </div>

                <!-- 2. PIMPINAN -->
                <div v-show="activeTab === 'pimpinan'" class="animate-slideUpFade">
                  <div class="space-y-6">
                    <div>
                      <label class="block text-sm font-bold text-slate-700 mb-2">Nama Kepala Sekolah</label>
                      <input type="text" v-model="form.nama_kepsek" class="w-full px-5 py-3.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="Masukkan nama lengkap beserta gelar">
                    </div>
                    <div>
                      <label class="block text-sm font-bold text-slate-700 mb-2">NIP Kepala Sekolah</label>
                      <input type="text" v-model="form.nip_kepsek" class="w-full px-5 py-3.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="Nomor Induk Pegawai (Opsional)">
                    </div>
                  </div>
                </div>

                <!-- 3. MEDIA -->
                <div v-show="activeTab === 'media'" class="animate-slideUpFade">
                  <div class="space-y-6">
                    <div>
                      <label class="block text-sm font-bold text-slate-700 mb-2">Situs Web Resmi</label>
                      <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">🌐</span>
                        <input type="url" v-model="form.website" class="w-full pl-11 pr-5 py-3.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="https://www.sekolahanda.sch.id">
                      </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Email</label>
                        <div class="relative">
                          <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">✉️</span>
                          <input type="email" v-model="form.email" class="w-full pl-11 pr-5 py-3.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="info@sekolahanda.sch.id">
                        </div>
                      </div>
                      <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nomor Telepon</label>
                        <div class="relative">
                          <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">📞</span>
                          <input type="text" v-model="form.telepon" class="w-full pl-11 pr-5 py-3.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="(021) 1234567">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- 4. LOKASI -->
                <div v-show="activeTab === 'lokasi'" class="animate-slideUpFade">
                  <div class="space-y-6">
                    <div>
                      <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Lengkap</label>
                      <textarea v-model="form.alamat" rows="3" class="w-full px-5 py-3.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400 leading-relaxed" placeholder="Tuliskan nama jalan, RT/RW, dan patokan..."></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Kelurahan / Desa</label>
                        <input type="text" v-model="form.kelurahan" class="w-full px-5 py-3.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="Nama Kelurahan">
                      </div>
                      <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Kecamatan</label>
                        <input type="text" v-model="form.kecamatan" class="w-full px-5 py-3.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="Nama Kecamatan">
                      </div>
                      <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Kabupaten / Kota</label>
                        <input type="text" v-model="form.kota" class="w-full px-5 py-3.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="Nama Kota">
                      </div>
                      <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Provinsi</label>
                        <input type="text" v-model="form.provinsi" class="w-full px-5 py-3.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="Nama Provinsi">
                      </div>
                      <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Kode Pos</label>
                        <input type="text" v-model="form.kode_pos" class="w-full md:w-1/2 px-5 py-3.5 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="12345">
                      </div>
                    </div>
                  </div>
                </div>
                
              </div> <!-- End Margin Top for Floating Title -->
            </div>
            
            <!-- Card Footer & Submit -->
            <div class="bg-slate-50 p-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 rounded-b-3xl">
              <div class="flex items-center gap-3 text-slate-500">
                <div class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center text-emerald-500 border border-slate-200">ℹ️</div>
                <p class="text-xs font-semibold leading-relaxed">Perubahan akan langsung<br>memperbarui database.</p>
              </div>
              
              <button type="submit" :disabled="isSaving" class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-bold rounded-2xl shadow-lg shadow-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/40 hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-3 group">
                <span v-if="isSaving" class="animate-spin text-xl">⏳</span>
                <span v-else class="text-xl group-hover:scale-110 transition-transform">💾</span>
                <span class="tracking-wide uppercase text-sm">Simpan Perubahan</span>
              </button>
            </div>
            
          </div>
        </div>
      </div>
    </form>

    <!-- Sleek Toast Notification -->
    <div v-if="showToast" class="fixed bottom-10 left-1/2 -translate-x-1/2 bg-slate-900/95 backdrop-blur-md text-white px-6 py-4 rounded-full shadow-2xl flex items-center gap-4 z-[100] animate-slideUp">
      <div class="w-8 h-8 bg-gradient-to-br from-emerald-400 to-emerald-500 rounded-full flex items-center justify-center text-white text-sm shadow-inner shadow-white/20">✓</div>
      <p class="font-bold text-sm tracking-wide pr-2">Data Sekolah berhasil disimpan!</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

definePageMeta({
  layout: 'admin',
  middleware: 'admin',
  title: 'Data Sekolah & Profil Instansi'
})

// === TAB MANAGEMENT ===
const activeTab = ref('identitas')
const tabs = [
  { id: 'identitas', title: 'Identitas Sekolah', shortTitle: 'Profil', subtitle: 'Informasi Dasar', icon: '🏫' },
  { id: 'pimpinan', title: 'Pimpinan Lembaga', shortTitle: 'Kepsek', subtitle: 'Kepala Sekolah', icon: '👨‍💼' },
  { id: 'media', title: 'Media & Digital', shortTitle: 'Sosmed', subtitle: 'Kontak & Website', icon: '🌐' },
  { id: 'lokasi', title: 'Lokasi Geografis', shortTitle: 'Lokasi', subtitle: 'Alamat & Wilayah', icon: '📍' }
]

const currentTab = computed(() => tabs.find(t => t.id === activeTab.value))

// === FORM STATE ===
const form = ref({
  nama_sekolah: '',
  npsn: '',
  nss: '',
  website: '',
  email: '',
  telepon: '',
  nama_kepsek: '',
  nip_kepsek: '',
  alamat: '',
  kelurahan: '',
  kecamatan: '',
  kota: '',
  provinsi: '',
  kode_pos: ''
})

const isSaving = ref(false)
const showToast = ref(false)

// === FETCH DATA INITAL ===
const fetchSekolah = async () => {
  const tokenCookie = useCookie('auth_token')
  try {
    const response = await $fetch('http://localhost:8000/api/admin/sekolah', {
      headers: { Authorization: `Bearer ${tokenCookie.value}` }
    })
    
    if (response.success && response.data) {
      Object.keys(form.value).forEach(key => {
        if (response.data[key] !== undefined && response.data[key] !== null) {
          form.value[key] = response.data[key]
        }
      })
    }
  } catch (error) {
    console.error('Failed to fetch sekolah:', error)
  }
}

onMounted(() => {
  fetchSekolah()
})

// === SAVE DATA ===
const saveData = async () => {
  if (isSaving.value) return
  isSaving.value = true
  
  const tokenCookie = useCookie('auth_token')
  try {
    const response = await $fetch('http://localhost:8000/api/admin/sekolah', {
      method: 'PUT',
      headers: { 
        Authorization: `Bearer ${tokenCookie.value}`,
        'Content-Type': 'application/json'
      },
      body: form.value
    })
    
    if (response.success) {
      showToast.value = true
      setTimeout(() => { showToast.value = false }, 3500)
    }
  } catch (error) {
    console.error('Failed to save sekolah:', error)
    alert('Terjadi kesalahan saat menyimpan data.')
  } finally {
    isSaving.value = false
  }
}
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-fadeIn {
  animation: fadeIn 0.4s ease-out forwards;
}

@keyframes slideUpFade {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-slideUpFade {
  animation: slideUpFade 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

@keyframes slideUp {
  from { opacity: 0; transform: translate(-50%, 50px); }
  to { opacity: 1; transform: translate(-50%, 0); }
}
.animate-slideUp {
  animation: slideUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

.hide-scrollbar::-webkit-scrollbar {
  display: none;
}
.hide-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
