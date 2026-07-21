<template>
  <div class="h-screen w-full flex flex-col font-sans bg-slate-50 relative overflow-hidden">
    
    <!-- HEADER -->
    <header class="w-full bg-slate-900 h-16 flex items-center justify-between px-6 lg:px-12 relative z-20 shadow-md flex-shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center">
                <img v-if="sekolah?.logo" :src="getImageUrl(sekolah.logo)" alt="Logo" class="w-7 h-7 object-contain">
                <span v-else class="text-xl">🎓</span>
            </div>
            <h1 class="text-xl font-black text-white tracking-tight">e-Rapor <span class="text-emerald-400">SMK</span></h1>
        </div>
        <!-- Desktop Menu -->
        <div class="hidden lg:flex items-center gap-6">
            <a href="#" class="text-[11px] uppercase tracking-widest font-black text-slate-300 hover:text-emerald-400 flex items-center gap-2 transition-colors">
                <span>🔒</span> Masuk
            </a>
            <a href="#" class="text-[11px] uppercase tracking-widest font-black text-emerald-400 hover:text-emerald-300 flex items-center gap-2 transition-colors">
                <span>📘</span> Panduan Aplikasi
            </a>
            <a href="#" class="text-[11px] uppercase tracking-widest font-black text-amber-400 hover:text-amber-300 flex items-center gap-2 transition-colors">
                <span>🔑</span> Reset Password
            </a>
        </div>

        <!-- Mobile Hamburger Button -->
        <button @click="showMobileMenu = !showMobileMenu" class="lg:hidden text-slate-300 hover:text-white p-2 focus:outline-none">
            <svg v-if="!showMobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <!-- Mobile Menu Dropdown -->
        <Transition name="fade-slide">
            <div v-if="showMobileMenu" class="absolute top-16 left-0 w-full bg-slate-900 border-t border-slate-800 shadow-xl flex flex-col py-4 px-6 gap-2 lg:hidden z-40">
                <a href="#" class="text-xs uppercase tracking-widest font-black text-slate-300 hover:text-emerald-400 flex items-center gap-3 py-3 transition-colors border-b border-slate-800/50">
                    <span>🔒</span> Masuk
                </a>
                <a href="#" class="text-xs uppercase tracking-widest font-black text-emerald-400 hover:text-emerald-300 flex items-center gap-3 py-3 transition-colors border-b border-slate-800/50">
                    <span>📘</span> Panduan Aplikasi
                </a>
                <a href="#" class="text-xs uppercase tracking-widest font-black text-amber-400 hover:text-amber-300 flex items-center gap-3 py-3 transition-colors">
                    <span>🔑</span> Reset Password
                </a>
            </div>
        </Transition>
    </header>

    <!-- MAIN SPLIT AREA -->
    <main class="flex-1 flex flex-col-reverse lg:flex-row w-full relative z-10 min-h-0 overflow-y-auto lg:overflow-hidden">
        
        <!-- LEFT: BANNER -->
        <div class="w-full lg:w-[70%] h-auto lg:h-full flex-shrink-0 lg:flex-shrink relative flex flex-col justify-center py-12 px-6 lg:p-16 bg-slate-800 lg:bg-transparent overflow-hidden">
            <!-- Background Gradient & Patterns -->
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-800 to-slate-900 z-0">
                <div class="absolute top-[-20%] left-[-10%] w-[70%] h-[70%] bg-emerald-600/20 rounded-full blur-[80px] pointer-events-none"></div>
                <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-teal-600/20 rounded-full blur-[80px] pointer-events-none"></div>
            </div>

            <div class="relative z-10 flex flex-col lg:flex-row items-center gap-8 lg:gap-12 w-full max-w-5xl mx-auto mt-4 lg:mt-0">
                
                <!-- Photo Left -->
                <div class="w-full sm:max-w-md lg:max-w-none lg:w-[45%] flex-shrink-0 relative group">
                    <div class="relative rounded-3xl border-4 border-white/20 overflow-hidden shadow-2xl shadow-emerald-900/50 flex items-center justify-center bg-slate-800 transition-transform duration-500 group-hover:scale-[1.02] aspect-[4/3]">
                        <img v-if="sekolah?.foto_1" :src="getImageUrl(sekolah.foto_1)" alt="Foto Sekolah" class="w-full h-full object-cover">
                        <div v-else class="text-center text-slate-500 flex flex-col items-center justify-center h-full w-full">
                            <span class="text-5xl lg:text-6xl mb-2 block">🏫</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest px-4 text-center">Foto 1 (Upload di Master)</span>
                        </div>
                    </div>
                </div>

                <!-- Text Right -->
                <div class="w-full lg:w-[55%] text-center lg:text-left mt-4 lg:mt-0">
                    <div class="inline-block bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg px-3 py-1 mb-4 lg:mb-6">
                        <span class="text-emerald-300 font-bold uppercase tracking-widest text-[10px]">Versi 2026.1 - {{ sekolah?.nama_sekolah || 'Sistem E-Rapor Terpadu' }}</span>
                    </div>
                    
                    <h2 class="text-4xl lg:text-5xl xl:text-6xl font-black text-white leading-tight drop-shadow-md mb-4 lg:mb-6">
                        e-Rapor <span class="text-emerald-400">SMK</span>
                    </h2>
                    
                    <p class="text-base lg:text-lg xl:text-xl text-slate-300 font-medium leading-relaxed max-w-xl mx-auto lg:mx-0">
                        DASHBOARD BUSINESS INTELLIGENCE<br>
                        SEBAGAI EARLY WARNING SYSTEM<br>
                        PENCAPAIAN AKADEMIK SISWA
                    </p>
                </div>
            </div>
        </div>

        <!-- RIGHT: FORM -->
        <div class="w-full lg:w-[30%] h-auto lg:h-full flex-shrink-0 lg:flex-shrink bg-white flex flex-col justify-center items-center py-10 px-6 lg:p-8 relative z-20 shadow-[-20px_0_40px_-10px_rgba(0,0,0,0.2)] lg:overflow-y-auto">
            <div class="w-full max-w-sm lg:max-w-md xl:max-w-lg">
                
                <div class="text-center mb-8 lg:mb-10 pb-6 border-b-2 border-slate-100 relative">
                    <div class="inline-flex items-center justify-center gap-3 text-xl lg:text-2xl font-black text-slate-800 tracking-wider">
                        <span class="text-2xl">🚪</span> MASUK
                    </div>
                    <p class="text-[10px] text-slate-400 font-bold mt-3 uppercase tracking-widest absolute -bottom-2.5 left-1/2 transform -translate-x-1/2 bg-white px-4">Silakan Masuk Untuk Memulai Aplikasi</p>
                </div>

                <form @submit.prevent="handleLogin" class="space-y-5 lg:space-y-6 mt-10">
                    
                    <div v-if="errorMessage" class="p-3 rounded-xl bg-rose-50 border border-rose-100 flex items-start gap-3 animate-fadeIn mb-2">
                        <span class="text-rose-500 text-sm mt-0.5">⚠️</span>
                        <p class="text-xs font-bold text-rose-600">{{ errorMessage }}</p>
                    </div>

                    <!-- Input Group: Username -->
                    <div class="flex w-full border border-slate-300 rounded-lg overflow-hidden focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-500/20 transition-all bg-white group">
                        <div class="w-2/5 lg:w-1/3 bg-slate-100 group-focus-within:bg-emerald-50 text-slate-600 group-focus-within:text-emerald-700 text-[11px] lg:text-xs font-bold flex items-center px-4 border-r border-slate-300 transition-colors">
                            Username
                        </div>
                        <input v-model="form.username" type="text" required
                               class="w-3/5 lg:w-2/3 py-3 lg:py-4 px-4 text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none" 
                               placeholder="Isi dengan pengguna">
                    </div>

                    <!-- Input Group: Password -->
                    <div class="flex w-full border border-slate-300 rounded-lg overflow-hidden focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-500/20 transition-all bg-white group relative">
                        <div class="w-2/5 lg:w-1/3 bg-slate-100 group-focus-within:bg-emerald-50 text-slate-600 group-focus-within:text-emerald-700 text-[11px] lg:text-xs font-bold flex items-center px-4 border-r border-slate-300 transition-colors">
                            Password
                        </div>
                        <input v-model="form.password" :type="showPassword ? 'text' : 'password'" required
                               class="w-3/5 lg:w-2/3 py-3 lg:py-4 pl-4 pr-12 text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none" 
                               placeholder="Ketikkan Kata Sandi">
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-emerald-600 transition-colors">
                            {{ showPassword ? '👁️' : '🙈' }}
                        </button>
                    </div>

                    <div class="flex justify-between items-center mt-2">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest group-hover:text-slate-700">Ingat Saya</span>
                        </label>
                        <a href="#" class="text-[10px] font-bold text-sky-600 hover:text-sky-500 uppercase tracking-widest">Lupa Password ?</a>
                    </div>

                    <div class="pt-4 lg:pt-6">
                        <button type="submit" :disabled="isLoading" 
                                class="w-full py-4 bg-slate-900 hover:bg-emerald-700 text-white font-black rounded-lg shadow-lg hover:shadow-emerald-700/30 transition-all transform active:scale-[0.98] disabled:opacity-50 flex items-center justify-center gap-2 text-xs uppercase tracking-widest border border-transparent">
                            <span v-if="isLoading" class="animate-spin text-lg">⏳</span>
                            <span v-else>🔓 Masuk Aplikasi</span>
                        </button>
                    </div>

                    <div class="mt-8 text-center border-t border-slate-100 pt-6 relative">
                        <span class="text-[10px] text-slate-400 font-bold bg-white px-4 absolute -top-2 left-1/2 transform -translate-x-1/2 whitespace-nowrap">Isi User & Password dengan benar</span>
                    </div>
                </form>

            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="w-full bg-slate-950 text-slate-400 py-3 px-6 text-center text-[9px] lg:text-[10px] font-bold uppercase tracking-widest z-20 flex-shrink-0 flex flex-col lg:flex-row justify-between items-center gap-2">
        <span>Aplikasi e-Rapor SMK | Versi 2026.1</span>
        <span>Direktorat Sekolah Menengah Kejuruan | Ditjen Pendidikan Vokasi</span>
    </footer>
    
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCookie } from '#app'

const showMobileMenu = ref(false)

const { sekolah } = useSekolah()

const runtimeConfig = useRuntimeConfig()
const apiUrl = import.meta.env.VITE_API_BASE_URL || runtimeConfig.public.apiBase || 'http://localhost:8000'

const getImageUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http') || path.startsWith('blob:')) return path
  return `${apiUrl}/${path.replace(/^\//, '')}`
}

onMounted(async () => {
  try {
    const res = await $fetch(apiUrl + '/api/public/stats')
    if (res.success && res.data && res.data.sekolah) {
      sekolah.value = res.data.sekolah
    }
  } catch (err) {
    console.error('Gagal mengambil data sekolah publik:', err)
  }
})

const router = useRouter()

const form = reactive({
  username: '',
  password: ''
})

const errorMessage = ref('')
const isLoading = ref(false)
const showPassword = ref(false)

const handleLogin = async () => {
  isLoading.value = true
  errorMessage.value = ''
  
  try {
    const runtimeConfig = useRuntimeConfig()
    const apiUrl = import.meta.env.VITE_API_BASE_URL || runtimeConfig.public.apiBase || 'http://localhost:8000'
    const response = await $fetch(apiUrl + '/api/login', {
      method: 'POST',
      body: form
    })
    
    if (response.success) {
      const impersonateUserId = useCookie('impersonate_user_id')
      const impersonateRole = useCookie('impersonate_role')
      impersonateUserId.value = null
      impersonateRole.value = null

      const tokenCookie = useCookie('auth_token')
      tokenCookie.value = response.data.token
      
      const user = response.data.user
      const userCookie = useCookie('user_profile')
      userCookie.value = JSON.stringify(user)
      
      useSwal().toast('Berhasil masuk!', 'success')
      
      if (['admin', 'superadmin'].includes(user.role)) {
        router.push('/admin/dashboard')
      } else {
        router.push(`/${user.role}/dashboard`)
      }
    }
  } catch (error) {
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
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
  animation: fadeIn 0.3s ease-out forwards;
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
</style>