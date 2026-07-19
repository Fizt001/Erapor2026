<template>
  <div class="h-screen w-full flex flex-col lg:flex-row relative font-sans bg-slate-900 overflow-hidden">
    
    <!-- Background Decorators (Abstract Blobs) -->
    <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-emerald-600/30 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-teal-600/30 rounded-full blur-[120px] pointer-events-none"></div>

    <!-- LEFT PANEL (Desktop) / TOP AREA (Mobile) -->
    <div class="w-full lg:w-1/2 flex flex-col justify-center p-4 lg:p-12 relative z-10 flex-shrink-0 lg:h-full mt-2 lg:mt-0">
        
        <!-- Header & Logo -->
        <div class="flex items-center gap-3 mb-6 lg:mb-12 mt-4 lg:mt-0 w-full max-w-md lg:max-w-xl mx-auto lg:ml-auto lg:mr-0 lg:w-[80%]">
            <div class="w-12 h-12 lg:w-16 lg:h-16 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center shadow-xl border border-white/20">
                <img v-if="sekolah && sekolah.logo" :src="`http://localhost:8000/${sekolah.logo}`" alt="Logo" class="w-8 h-8 lg:w-12 lg:h-12 object-contain drop-shadow-md">
                <span v-else class="text-2xl">🎓</span>
            </div>
            <div>
                <h1 class="text-xl lg:text-3xl font-black text-white tracking-tight">e-Rapor <span class="text-emerald-400">Pro</span></h1>
                <p class="text-[10px] lg:text-xs text-emerald-200/80 uppercase tracking-widest font-bold">{{ sekolah?.nama_sekolah || 'Sistem Penilaian Akademik' }}</p>
            </div>
        </div>

        <!-- Role Information (Auto-rotating Card) -->
        <div class="flex flex-col flex-grow min-h-0 justify-center w-full max-w-md lg:max-w-xl mx-auto lg:ml-auto lg:mr-0 lg:w-[80%]">
            <h2 class="text-xl lg:text-4xl font-black text-white leading-tight mb-4 lg:mb-10">
                PENGEMBANGAN DASHBOARD BUSINESS INTELLIGENCE<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-200 text-lg lg:text-2xl leading-snug mt-2 block">
                    SEBAGAI EARLY WARNING SYSTEM PENCAPAIAN AKADEMIK SISWA
                </span>
            </h2>
            
            <!-- Single Rotating Card -->
            <div class="relative w-full h-[160px] lg:h-[220px]">
                <TransitionGroup name="fade">
                    <div v-for="(card, index) in cards" :key="'card-'+index" v-show="activeIndex === index" 
                         class="absolute inset-0 p-5 lg:p-8 rounded-3xl bg-white/5 backdrop-blur-sm border border-white/10 shadow-lg shadow-emerald-500/10 flex flex-col justify-center">
                        <div class="w-10 h-10 lg:w-14 lg:h-14 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-xl lg:text-3xl mb-3 lg:mb-5 shadow-lg shadow-emerald-500/20">
                            {{ card.icon }}
                        </div>
                        <h3 class="text-sm lg:text-lg font-black text-white mb-1.5 lg:mb-3">{{ card.title }}</h3>
                        <p class="text-[10px] lg:text-sm text-slate-300 leading-snug font-medium line-clamp-3">{{ card.desc }}</p>
                    </div>
                </TransitionGroup>
            </div>
            
            <!-- Indicator Dots -->
            <div class="flex gap-1.5 mt-4 lg:mt-6 ml-2 lg:ml-4">
                <div v-for="(_, index) in cards" :key="'dot-'+index" 
                     class="h-1.5 rounded-full transition-all duration-500 ease-out"
                     :class="activeIndex === index ? 'w-6 bg-emerald-500' : 'w-1.5 bg-white/20'">
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL (Desktop) / BOTTOM AREA (Mobile) -->
    <div class="w-full lg:w-1/2 flex flex-col items-center justify-center p-4 pb-6 lg:p-20 z-20 flex-grow lg:h-full relative lg:items-start">
        
        <!-- Login Card -->
        <div class="w-full max-w-md lg:max-w-lg bg-white/10 backdrop-blur-2xl border border-white/20 rounded-3xl shadow-2xl p-6 lg:p-12 relative flex flex-col justify-center mt-auto mb-auto lg:ml-8">
            <div class="text-center lg:text-left mb-4 lg:mb-10">
                <h2 class="text-xl lg:text-2xl font-black text-white mb-1 lg:mb-2">Selamat Datang 👋</h2>
                <p class="text-[10px] lg:text-xs text-slate-300 font-medium">Silakan masuk menggunakan kredensial Anda.</p>
            </div>

            <form @submit.prevent="handleLogin" class="space-y-3 lg:space-y-5">
                
                <div v-if="errorMessage" class="p-2 lg:p-3.5 rounded-2xl bg-rose-500/20 border border-rose-500/30 backdrop-blur-md flex items-start gap-2 lg:gap-3 animate-fadeIn">
                    <span class="text-rose-400 mt-0.5 text-xs lg:text-base">⚠️</span>
                    <p class="text-[10px] lg:text-xs font-bold text-rose-200">{{ errorMessage }}</p>
                </div>

                <div>
                    <label class="block text-[9px] lg:text-[10px] font-black uppercase tracking-widest text-slate-300 mb-1 lg:mb-2 ml-1">Email / NIS</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 lg:pl-4 flex items-center text-slate-400 text-xs lg:text-base">👤</span>
                        <input v-model="form.username" type="text" required 
                               class="w-full pl-9 lg:pl-11 pr-3 lg:pr-4 py-2.5 lg:py-3.5 bg-white/5 border border-white/10 rounded-xl lg:rounded-2xl text-white placeholder-slate-400 text-xs lg:text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 focus:bg-white/10 transition-all shadow-inner" 
                               placeholder="Contoh: 102938">
                    </div>
                </div>

                <div>
                    <label class="block text-[9px] lg:text-[10px] font-black uppercase tracking-widest text-slate-300 mb-1 lg:mb-2 ml-1">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 lg:pl-4 flex items-center text-slate-400 text-xs lg:text-base">🔒</span>
                        <input v-model="form.password" :type="showPassword ? 'text' : 'password'" required 
                               class="w-full pl-9 lg:pl-11 pr-10 lg:pr-12 py-2.5 lg:py-3.5 bg-white/5 border border-white/10 rounded-xl lg:rounded-2xl text-white placeholder-slate-400 text-xs lg:text-sm font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 focus:bg-white/10 transition-all shadow-inner" 
                               placeholder="••••••••">
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 lg:pr-4 flex items-center text-slate-400 hover:text-white transition-colors text-xs lg:text-base">
                            {{ showPassword ? '👁️' : '🙈' }}
                        </button>
                    </div>
                </div>

                <div class="pt-2 lg:pt-4">
                    <button type="submit" :disabled="isLoading" 
                            class="w-full py-3 lg:py-4 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-black rounded-xl lg:rounded-2xl shadow-xl shadow-emerald-500/20 transition-all transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 text-[10px] lg:text-sm uppercase tracking-widest border border-emerald-400/30">
                        <span v-if="isLoading" class="animate-spin text-base lg:text-lg">⏳</span>
                        <span v-else>Masuk Sekarang</span>
                        <span v-if="!isLoading" class="text-base lg:text-lg ml-1">→</span>
                    </button>
                </div>
            </form>

            <div class="mt-4 lg:mt-8 text-center">
                <p class="text-[9px] lg:text-[10px] text-slate-400 font-medium">Lupa password? Silakan hubungi operator sekolah.</p>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="mt-4 lg:mt-8 text-center w-full max-w-md lg:max-w-lg mb-2 lg:mb-0 lg:ml-8">
            <p class="text-[9px] lg:text-[11px] font-black text-slate-500 uppercase tracking-[0.3em]">Created by <span class="text-emerald-500 drop-shadow-md">SMK-Yatindo</span></p>
        </div>

    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'

const { sekolah, fetchSekolah } = useSekolah()

const windowWidth = ref(1024)
const isDesktop = computed(() => windowWidth.value >= 1024)

const showPassword = ref(false)

const activeIndex = ref(0)
const statsData = ref({
    top_student: null,
    guru_umum: 0,
    guru_produktif: 0,
    walas: 0
})

const cards = computed(() => [
  {
    icon: '🏆',
    title: 'Peringkat 1 Umum',
    desc: statsData.value.top_student 
        ? `${statsData.value.top_student.nama} dari kelas ${statsData.value.top_student.kelas} meraih Peringkat 1 Umum di sekolah dengan total capaian nilai akhir ${statsData.value.top_student.total_nilai}.`
        : 'Belum ada kalkulasi data nilai akhir sumatif untuk semester aktif saat ini.'
  },
  {
    icon: '👨‍🏫',
    title: 'Tenaga Pendidik',
    desc: `Pendidikan optimal didukung oleh kolaborasi dari ${statsData.value.guru_umum} Guru Umum dan ${statsData.value.guru_produktif} Guru Produktif/Kejuruan yang aktif mengajar.`
  },
  {
    icon: '👨‍👩‍👧‍👦',
    title: 'Wali Kelas Aktif',
    desc: `Terdapat ${statsData.value.walas} Wali Kelas berdedikasi yang siap mendampingi, memantau absensi, dan membimbing siswa-siswi di tiap rombel kelas.`
  }
])

onMounted(async () => {
  fetchSekolah()
  windowWidth.value = window.innerWidth
  window.addEventListener('resize', () => { windowWidth.value = window.innerWidth })

  try {
    const res = await $fetch('http://localhost:8000/api/public/stats')
    if (res.success && res.data) {
        statsData.value = res.data
        if (res.data.sekolah) {
            sekolah.value = {
                nama_sekolah: res.data.sekolah.nama_sekolah || 'SMK Yatindo',
                logo: res.data.sekolah.logo || null
            }
        }
    }
  } catch (err) {
    console.error('Gagal mengambil statistik publik')
  }

  // Auto rotate cards
  setInterval(() => {
      activeIndex.value = (activeIndex.value + 1) % cards.value.length
  }, 5000)
})

const router = useRouter()

const form = reactive({
  username: '',
  password: ''
})

const errorMessage = ref('')
const isLoading = ref(false)

const handleLogin = async () => {
  isLoading.value = true
  errorMessage.value = ''
  
  try {
    const response = await $fetch('http://localhost:8000/api/login', {
      method: 'POST',
      body: form
    })
    
    if (response.success) {
      // Bersihkan cookie penyamaran dari sesi sebelumnya jika ada
      const impersonateUserId = useCookie('impersonate_user_id')
      const impersonateRole = useCookie('impersonate_role')
      impersonateUserId.value = null
      impersonateRole.value = null

      // Simpan token di cookie
      const tokenCookie = useCookie('auth_token')
      tokenCookie.value = response.data.token
      
      const user = response.data.user
      
      // Simpan profil user di cookie untuk keperluan layout dan middleware
      const userCookie = useCookie('user_profile')
      userCookie.value = JSON.stringify(user)
      
      useSwal().toast('Berhasil masuk!', 'success')
      
      // Redirect berdasarkan role
      if (user.role === 'siswa') {
        router.push('/siswa/dashboard')
      } else if (user.role === 'admin') {
        router.push('/admin/dashboard')
      } else if (user.role === 'kurikulum') {
        router.push('/kurikulum/dashboard')
      } else if (user.role === 'guru') {
        router.push('/guru/dashboard')
      } else if (user.role === 'kepsek') {
        router.push('/kepsek/dashboard')
      } else if (user.role === 'bk') {
        router.push('/bk/dashboard')
      } else if (user.role === 'superadmin') {
        router.push('/admin/dashboard')
      } else {
        router.push('/dashboard')
      }
    }
  } catch (error) {
    if (error.response && error.response._data && error.response._data.message) {
      errorMessage.value = error.response._data.message
    } else {
      errorMessage.value = 'Gagal terhubung ke server. Periksa koneksi Anda.'
    }
    useSwal().toast(errorMessage.value, 'error')
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
/* Hide scrollbar for Chrome, Safari and Opera */
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}
/* Hide scrollbar for IE, Edge and Firefox */
.hide-scrollbar {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}

/* Fade Transition for Carousel Cards */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }
</style>
