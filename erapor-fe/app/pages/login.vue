<template>
  <div class="h-screen w-full flex flex-col font-sans bg-[#090C15] relative overflow-hidden" 
       @touchstart="handleTouchStart" 
       @touchmove.prevent="handleTouchMove"
       @wheel="handleWheel">
    
    <!-- HEADER -->
    <header class="w-full bg-[#090C15]/80 backdrop-blur-md h-16 flex items-center justify-between px-6 lg:px-12 relative z-50 flex-shrink-0 border-b border-white/5" :class="{'opacity-0 pointer-events-none lg:opacity-100 lg:pointer-events-auto transition-opacity duration-500': isMobileFormActive}">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-rose-500 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30 p-1">
                <div class="w-full h-full bg-[#090C15] rounded-lg flex items-center justify-center">
                    <img v-if="sekolah?.logo" :src="sekolah.logo" alt="Logo" class="w-6 h-6 object-contain">
                    <span v-else class="text-xl">🎓</span>
                </div>
            </div>
            <h1 class="text-xl font-black text-white tracking-tight">e-Rapor <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-rose-400">SMK</span></h1>
        </div>
        <!-- Desktop Menu -->
        <div class="hidden lg:flex items-center gap-6">
            <a href="#" class="text-[11px] uppercase tracking-widest font-bold text-slate-400 hover:text-white flex items-center gap-2 transition-colors">
                Panduan Aplikasi
            </a>
        </div>

        <!-- Mobile Hamburger Button -->
        <button @click="showMobileMenu = !showMobileMenu" class="lg:hidden text-slate-300 hover:text-white p-2 focus:outline-none">
            <svg v-if="!showMobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <!-- Mobile Menu Dropdown -->
        <Transition name="fade-slide">
            <div v-if="showMobileMenu" @mouseleave="showMobileMenu = false" class="absolute top-16 left-0 w-full bg-[#090C15] border-t border-white/10 shadow-2xl flex flex-col py-4 px-6 gap-2 lg:hidden z-40">
                <a href="#" @click="showMobileMenu = false" class="text-xs uppercase tracking-widest font-bold text-slate-300 hover:text-white flex items-center gap-3 py-3 transition-colors">
                    Panduan Aplikasi
                </a>
            </div>
        </Transition>
        <div v-if="showMobileMenu" @click="showMobileMenu = false" class="lg:hidden fixed inset-0 z-30"></div>
    </header>

    <!-- MAIN SPLIT AREA -->
    <main class="flex-1 flex flex-col lg:flex-row w-full relative z-10 overflow-hidden">
        
        <!-- LEFT: VIBRANT BANNER -->
        <div class="w-full lg:w-[65%] h-full absolute inset-0 lg:static z-10 flex flex-col justify-center py-12 px-6 lg:p-16 bg-[#090C15] overflow-hidden transition-all duration-[800ms] ease-[cubic-bezier(0.23,1,0.32,1)] origin-top"
             :class="isMobileFormActive ? 'scale-90 opacity-0 -translate-y-20 lg:scale-100 lg:opacity-100 lg:translate-y-0' : 'scale-100 opacity-100 translate-y-0'">
            
            <!-- Vibrant Mozilla-style Orbs -->
            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-[20%] -left-[10%] w-[60%] h-[70%] rounded-full mix-blend-screen filter blur-[100px] opacity-60 bg-gradient-to-br from-orange-500 to-rose-600 animate-pulse" style="animation-duration: 8s;"></div>
                <div class="absolute -bottom-[20%] -right-[10%] w-[70%] h-[80%] rounded-full mix-blend-screen filter blur-[120px] opacity-50 bg-gradient-to-tl from-indigo-600 via-violet-600 to-transparent"></div>
                <div class="absolute top-[30%] left-[40%] w-[30%] h-[40%] rounded-full mix-blend-screen filter blur-[90px] opacity-30 bg-gradient-to-r from-cyan-400 to-blue-500"></div>
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiLz48L3N2Zz4=')]"></div>
            </div>

            <div class="relative z-10 flex flex-col lg:flex-row items-center gap-8 lg:gap-12 w-full max-w-5xl mx-auto mt-4 lg:mt-0">
                <!-- Slideshow Left -->
                <div class="w-full sm:max-w-md lg:max-w-none lg:w-[45%] flex-shrink-0 relative group">
                    <div class="relative rounded-2xl p-1 bg-gradient-to-br from-orange-500 via-rose-500 to-violet-600 shadow-[0_0_40px_-10px_rgba(249,115,22,0.4)]">
                        <div class="relative rounded-xl overflow-hidden aspect-[4/3] bg-[#090C15]">
                            <template v-if="slidePhotos.length > 0">
                                <div v-for="(photo, idx) in slidePhotos" :key="idx"
                                    class="absolute inset-0 w-full h-full transition-opacity duration-1000"
                                    :class="currentSlide === idx ? 'opacity-100 z-10' : 'opacity-0 z-0'">
                                    <img :src="photo" alt="Foto Sekolah" class="w-full h-full object-cover opacity-90 mix-blend-luminosity hover:mix-blend-normal transition-all duration-500">
                                </div>
                                <div v-if="slidePhotos.length > 1" class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-20 bg-black/40 backdrop-blur-md px-3 py-1.5 rounded-full">
                                    <button v-for="(_, idx) in slidePhotos" :key="idx" @click="currentSlide = idx"
                                        class="w-2 h-2 rounded-full transition-all"
                                        :class="currentSlide === idx ? 'bg-orange-500 scale-125 w-4' : 'bg-white/50'"
                                    ></button>
                                </div>
                            </template>
                            <div v-else class="text-center text-slate-500 flex flex-col items-center justify-center h-full w-full">
                                <span class="text-5xl lg:text-6xl mb-2 block opacity-50">🏫</span>
                                <span class="text-[10px] font-bold uppercase tracking-widest px-4 text-center">Foto Sekolah</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Text Right -->
                <div class="w-full lg:w-[60%] xl:w-[55%] text-center lg:text-left mt-6 lg:mt-0 flex flex-col items-center lg:items-start px-4 lg:px-0">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black text-white leading-tight mb-2 tracking-tight sm:whitespace-nowrap">
                        e-Rapor
                    </h2>
                    
                    <h3 class="text-sm sm:text-base lg:text-lg xl:text-xl font-bold text-slate-200 mb-6 uppercase tracking-widest drop-shadow-md leading-snug">
                        {{ sekolah?.nama_sekolah || 'SMK Tinta Emas Indonesia' }}
                    </h3>
                    
                    <div class="inline-block bg-white/5 backdrop-blur-md border border-white/10 rounded-full px-4 py-1.5 mb-6 shadow-xl">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-rose-400 font-black uppercase tracking-widest text-[10px]">Versi : 2026.1</span>
                    </div>
                    
                    <p class="text-sm sm:text-base lg:text-lg text-slate-400 font-medium italic leading-relaxed max-w-lg mx-auto lg:mx-0">
                        <span class="text-slate-200 font-semibold">Dashboard Business Intelligence</span><br>
                        Sebagai Early Warning System<br>
                        Pencapaian Akademik Siswa.
                    </p>
                </div>
            </div>

            <!-- Swipe Up Indicator (Mobile Only) -->
            <div class="absolute bottom-8 inset-x-0 w-full flex flex-col items-center justify-center lg:hidden animate-bounce text-slate-400">
                <span class="text-[10px] font-bold uppercase tracking-widest mb-2">Usap ke atas</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
            </div>
        </div>

        <!-- RIGHT: CLEAN FORM AREA (BottomSheet on Mobile) -->
        <div class="w-full lg:w-[35%] h-[90vh] lg:h-full absolute bottom-0 left-0 lg:static z-20 bg-white flex flex-col justify-center items-center py-10 px-6 lg:p-10 shadow-[0_-30px_60px_-15px_rgba(0,0,0,0.5)] lg:shadow-[-30px_0_60px_-15px_rgba(0,0,0,0.5)] rounded-t-[2.5rem] lg:rounded-none transition-transform duration-[800ms] ease-[cubic-bezier(0.23,1,0.32,1)]"
             :class="isMobileFormActive ? 'translate-y-0' : 'translate-y-full lg:translate-y-0'">
             
            <!-- Mobile Drag Handle -->
            <div class="w-12 h-1.5 bg-slate-200 rounded-full absolute top-4 left-1/2 -translate-x-1/2 lg:hidden"></div>

            <div class="w-full max-w-sm pt-12 lg:pt-0 overflow-y-auto custom-scrollbar h-full lg:h-auto pb-16 lg:pb-0 flex flex-col justify-center" @touchmove.stop>
                
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center text-2xl lg:text-3xl font-black tracking-widest uppercase mb-2">
                        <span class="bg-gradient-to-r from-orange-500 via-rose-500 to-violet-600 text-transparent bg-clip-text animate-gradient-x">
                            SELAMAT DATANG
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">Silakan masuk ke akun Anda</p>
                </div>

                <form @submit.prevent="handleLogin" class="space-y-6 mt-8">
                    
                    <div v-if="errorMessage" class="p-4 rounded-xl bg-rose-50 border border-rose-100 flex items-start gap-3 animate-fadeIn mb-4">
                        <AppIcon name="exclamation-triangle" class="text-rose-500 mt-0.5 text-base" />
                        <p class="text-xs font-bold text-rose-600 leading-relaxed">{{ errorMessage }}</p>
                    </div>

                    <!-- Input Group: Username -->
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Username</label>
                        <div class="flex w-full border-2 border-slate-100 rounded-xl overflow-hidden focus-within:border-orange-500 focus-within:ring-4 focus-within:ring-orange-500/10 transition-all bg-slate-50">
                            <div class="pl-4 pr-3 flex items-center justify-center text-slate-400 text-lg"><AppIcon name="user" /></div>
                            <input v-model="form.username" type="text" required
                                   class="w-full py-3.5 pr-4 bg-transparent text-sm font-bold text-slate-800 placeholder-slate-400 focus:outline-none" 
                                   placeholder="Ketik username Anda">
                        </div>
                    </div>

                    <!-- Input Group: Password -->
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Password</label>
                        <div class="flex w-full border-2 border-slate-100 rounded-xl overflow-hidden focus-within:border-orange-500 focus-within:ring-4 focus-within:ring-orange-500/10 transition-all bg-slate-50 relative">
                            <div class="pl-4 pr-3 flex items-center justify-center text-slate-400 text-lg"><AppIcon name="lock-closed" /></div>
                            <input v-model="form.password" :type="showPassword ? 'text' : 'password'" required
                                   class="w-full py-3.5 pr-12 bg-transparent text-sm font-bold text-slate-800 placeholder-slate-400 focus:outline-none" 
                                   placeholder="Ketik password Anda">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-orange-500 transition-colors">
                                <AppIcon :name="showPassword ? 'eye' : 'eye-slash'" class="text-xl" />
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-between items-center px-1">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-orange-500 focus:ring-orange-500 transition-all">
                            <span class="text-[11px] font-bold text-slate-500 group-hover:text-slate-800 transition-colors">Ingat Saya</span>
                        </label>
                    </div>

                    <div class="pt-4">
                        <button type="submit" :disabled="isLoading" 
                                class="w-full py-4 bg-gradient-to-r from-orange-500 to-rose-500 hover:from-orange-600 hover:to-rose-600 text-white font-black rounded-xl shadow-[0_10px_20px_-10px_rgba(249,115,22,0.5)] hover:shadow-[0_15px_25px_-10px_rgba(249,115,22,0.6)] transition-all transform hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] disabled:opacity-50 flex items-center justify-center gap-2 text-sm uppercase tracking-widest">
                            <AppIcon v-if="isLoading" name="arrow-path" class="animate-spin text-xl" />
                            <span v-else class="flex items-center gap-2">Masuk ke Aplikasi <AppIcon name="arrow-right" class="text-lg" /></span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="w-full bg-[#05070A] text-slate-500 py-4 px-6 text-center text-[10px] font-bold uppercase tracking-widest z-50 flex-shrink-0 flex items-center justify-center border-t border-white/5">
        <span>Aplikasi e-Rapor SMK | Versi 2026.1</span>
    </footer>
    
  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCookie } from '#app'

// Mobile Interaction Logic
const isMobileFormActive = ref(false)
let touchStartY = 0
let isScrollingWithinForm = false

const handleTouchStart = (e) => {
    touchStartY = e.touches[0].clientY
    // Check if touch is inside a scrollable area in the form
    const target = e.target.closest('.overflow-y-auto')
    if (target && target.scrollTop > 0) {
        isScrollingWithinForm = true
    } else {
        isScrollingWithinForm = false
    }
}

const handleTouchMove = (e) => {
    if (window.innerWidth >= 1024) return;
    const touchEndY = e.touches[0].clientY
    const diff = touchStartY - touchEndY
    
    // Allow default scroll if inside form inputs/scrollable areas
    if (e.target.tagName.toLowerCase() === 'input' || isScrollingWithinForm) {
        return;
    }

    if (diff > 50 && !isMobileFormActive.value) {
        isMobileFormActive.value = true
        touchStartY = touchEndY // reset
    }
    
    if (diff < -50 && isMobileFormActive.value) {
        isMobileFormActive.value = false
        touchStartY = touchEndY // reset
    }
}

const handleWheel = (e) => {
    if (window.innerWidth >= 1024) return;
    
    // Allow default scroll if inside form inputs/scrollable areas
    if (e.target.tagName.toLowerCase() === 'input') return;

    if (e.deltaY > 20 && !isMobileFormActive.value) {
        isMobileFormActive.value = true
    }
    if (e.deltaY < -20 && isMobileFormActive.value) {
        isMobileFormActive.value = false
    }
}


const showMobileMenu = ref(false)
const { sekolah } = useSekolah()
const runtimeConfig = useRuntimeConfig()
const apiUrl = import.meta.env.VITE_API_BASE_URL || runtimeConfig.public.apiBase || 'http://localhost:8000'

const getImageUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http') || path.startsWith('blob:')) return path
  return `${apiUrl}/${path.replace(/^\//, '')}`
}

// === SLIDESHOW ===
const currentSlide = ref(0)
const slidePhotos = computed(() => {
  const photos = [sekolah.value?.foto_1, sekolah.value?.foto_2, sekolah.value?.foto_3]
  return photos.filter(Boolean)
})

let slideTimer = null
const startSlideshow = () => {
  slideTimer = setInterval(() => {
    if (slidePhotos.value.length > 1) {
      currentSlide.value = (currentSlide.value + 1) % slidePhotos.value.length
    }
  }, 4000)
}
onMounted(async () => {
  try {
    const res = await $fetch(apiUrl + '/api/public/stats')
    if (res.success && res.data && res.data.sekolah) {
      sekolah.value = {
        ...sekolah.value,
        nama_sekolah: res.data.sekolah.nama_sekolah,
        logo: getImageUrl(res.data.sekolah.logo),
        foto_1: getImageUrl(res.data.sekolah.foto_1),
        foto_2: getImageUrl(res.data.sekolah.foto_2),
        foto_3: getImageUrl(res.data.sekolah.foto_3)
      }
    }
  } catch (err) {
    console.error('Gagal mengambil data sekolah publik:', err)
  }
  startSlideshow()
})

onUnmounted(() => { if (slideTimer) clearInterval(slideTimer) })

const router = useRouter()
const form = reactive({ username: '', password: '' })
const errorMessage = ref('')
const isLoading = ref(false)
const showPassword = ref(false)

const handleLogin = async () => {
  isLoading.value = true
  errorMessage.value = ''
  
  try {
    const res = await $fetch(apiUrl + '/api/login', {
      method: 'POST',
      body: form
    })
    if (res.success && res.data && res.data.token) {
      const tokenCookie = useCookie('auth_token', { maxAge: 7200 })
      const userCookie = useCookie('user_profile', { maxAge: 7200 })
      tokenCookie.value = res.data.token
      userCookie.value = res.data.user
      useSwal().toast('Berhasil login!', 'success')
      
      const role = res.data.user.role
      if (role === 'superadmin' || role === 'admin') {
        router.push('/admin/dashboard')
      } else {
        router.push(`/${role}/dashboard`)
      }
    } else {
      errorMessage.value = res.message || 'Login gagal'
      useSwal().toast(errorMessage.value, 'error')
    }
  } catch (error) {
    console.error(error)
    if (error.response?._data?.message) {
      errorMessage.value = error.response._data.message
    } else {
      errorMessage.value = 'Gagal terhubung ke server.'
    }
    useSwal().toast(errorMessage.value, 'error')
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
@keyframes gradientX {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
.animate-gradient-x {
  background-size: 200% 200%;
  animation: gradientX 3s ease infinite;
}
.animate-fadeIn {
  animation: fadeIn 0.3s ease-out forwards;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.3s ease;
}
.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
</style>
