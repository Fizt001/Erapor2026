<template>
  <div class="h-screen w-full flex flex-col font-sans bg-[#090C15] relative overflow-hidden">
    
    <!-- HEADER -->
    <header class="w-full bg-[#090C15]/80 backdrop-blur-md h-16 flex items-center justify-between px-6 lg:px-12 relative z-50 flex-shrink-0 border-b border-white/5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-rose-500 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30 p-1">
                <div class="w-full h-full bg-[#090C15] rounded-lg flex items-center justify-center">
                    <img v-if="sekolah?.logo" :src="sekolah.logo" alt="Logo" class="w-6 h-6 object-contain">
                    <span v-else class="text-xl">🎓</span>
                </div>
            </div>
            <div class="flex flex-col">
                <h1 class="text-xl font-black text-white tracking-tight leading-none">e-Rapor <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-rose-400">SMK</span></h1>
                <span class="text-[10px] font-bold text-slate-200 uppercase tracking-widest mt-1">{{ sekolah?.nama_sekolah || 'SMK Tinta Emas Indonesia' }}</span>
            </div>
        </div>
        <!-- Desktop Menu -->
        <div class="hidden lg:flex items-center gap-6">
            <button @click="showLoginModal = true" class="px-5 py-2.5 bg-gradient-to-r from-orange-500 to-rose-500 text-white font-bold rounded-lg shadow-lg hover:shadow-orange-500/30 transition-all active:scale-95 text-xs uppercase tracking-widest flex items-center gap-2">
                MASUK KE APLIKASI
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
            </button>
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
        
        <!-- LEFT: BANNER PANEL (Full width) -->
        <div class="w-full flex-1 min-h-0 flex flex-col bg-[#090C15] overflow-y-auto relative">
            
            <!-- Vibrant Mozilla-style Orbs -->
            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-[20%] -left-[10%] w-[60%] h-[70%] rounded-full mix-blend-screen filter blur-[100px] opacity-60 bg-gradient-to-br from-orange-500 to-rose-600 animate-pulse" style="animation-duration: 8s;"></div>
                <div class="absolute -bottom-[20%] -right-[10%] w-[70%] h-[80%] rounded-full mix-blend-screen filter blur-[120px] opacity-50 bg-gradient-to-tl from-indigo-600 via-violet-600 to-transparent"></div>
                <div class="absolute top-[30%] left-[40%] w-[30%] h-[40%] rounded-full mix-blend-screen filter blur-[90px] opacity-30 bg-gradient-to-r from-cyan-400 to-blue-500"></div>
                <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiLz48L3N2Zz4=')]"></div>
            </div>

            <div class="relative z-10 flex flex-col w-full px-6 lg:px-12 xl:px-16 pt-8 lg:pt-12 pb-36 lg:pb-12 h-full">
                
                <!-- Watermark Tagline (menggantikan hero) -->
                <div class="mb-6 lg:mb-8 flex-shrink-0">
                    <p class="text-2xl lg:text-4xl xl:text-5xl font-black italic text-white/20 leading-snug select-none tracking-tight">
                        Early Warning System<br>
                        <span class="text-white/30">Pencapaian Akademik Siswa.</span>
                    </p>
                </div>

                <!-- CARD PENCAPAIAN AKADEMIK -->
                <div class="w-full flex-1 flex flex-col min-h-0">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 flex-1">
                        <!-- Card Kelas X -->
                        <div class="flex flex-col gap-3 h-full">
                            <h5 class="text-white font-bold text-xl px-1">Kelas X</h5>
                            <div class="relative rounded-3xl p-[3px] bg-gradient-to-r from-orange-500 to-violet-600 flex-1 min-h-[220px] shadow-[0_0_30px_-5px_rgba(249,115,22,0.4)] flex flex-col">
                                <div class="w-full flex-1 bg-[#090C15] rounded-[22px] flex flex-col items-center justify-center text-center p-6">
                                    <AppIcon name="chart-bar" class="text-5xl lg:text-7xl mb-4 opacity-50 text-white" />
                                    <span class="text-xs font-bold uppercase tracking-widest text-slate-500">Pencapaian Akademik</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Kelas XI -->
                        <div class="flex flex-col gap-3 h-full">
                            <h5 class="text-white font-bold text-xl px-1">Kelas XI</h5>
                            <div class="relative rounded-3xl p-[3px] bg-gradient-to-r from-orange-500 to-violet-600 flex-1 min-h-[220px] shadow-[0_0_30px_-5px_rgba(249,115,22,0.4)] flex flex-col">
                                <div class="w-full flex-1 bg-[#090C15] rounded-[22px] flex flex-col items-center justify-center text-center p-6">
                                    <AppIcon name="chart-bar" class="text-5xl lg:text-7xl mb-4 opacity-50 text-white" />
                                    <span class="text-xs font-bold uppercase tracking-widest text-slate-500">Pencapaian Akademik</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Kelas XII -->
                        <div class="flex flex-col gap-3 h-full">
                            <h5 class="text-white font-bold text-xl px-1">Kelas XII</h5>
                            <div class="relative rounded-3xl p-[3px] bg-gradient-to-r from-orange-500 to-violet-600 flex-1 min-h-[220px] shadow-[0_0_30px_-5px_rgba(249,115,22,0.4)] flex flex-col">
                                <div class="w-full flex-1 bg-[#090C15] rounded-[22px] flex flex-col items-center justify-center text-center p-6">
                                    <AppIcon name="chart-bar" class="text-5xl lg:text-7xl mb-4 opacity-50 text-white" />
                                    <span class="text-xs font-bold uppercase tracking-widest text-slate-500">Pencapaian Akademik</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MOBILE ONLY: Footer inside scrollable area -->
                <div class="lg:hidden w-full text-slate-600 py-6 text-center text-[10px] font-bold uppercase tracking-widest border-t border-white/5 mt-8">
                    <span>Aplikasi e-Rapor SMK | Versi 2026.1</span>
                </div>
            </div>
        </div>

        <!-- MOBILE ONLY: Tombol Login fixed di bawah panel kiri -->
        <div class="lg:hidden fixed bottom-0 left-0 right-0 z-50 p-4 pb-6 bg-gradient-to-t from-[#090C15] via-[#090C15]/95 to-transparent pt-10">
            <button @click="showLoginModal = true"
                    class="w-full py-4 bg-gradient-to-r from-orange-500 to-rose-500 text-white font-black rounded-xl shadow-[0_10px_30px_-5px_rgba(249,115,22,0.6)] text-sm uppercase tracking-widest flex items-center justify-center gap-2 active:scale-95 transition-transform">
                <span>Masuk ke Aplikasi</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
            </button>
        </div>
    </main>

    <!-- Modal Dialog Login -->
    <Transition name="modal-fade">
        <div v-if="showLoginModal" class="fixed inset-0 z-[100] flex items-end lg:items-center justify-center" @click.self="showLoginModal = false">
            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="showLoginModal = false"></div>
            <div class="relative w-full lg:max-w-md bg-white rounded-t-3xl lg:rounded-3xl shadow-2xl px-6 pt-8 pb-10 max-h-[92vh] overflow-y-auto">
                <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto mb-6"></div>
                <button @click="showLoginModal = false" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700 transition-colors p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center text-2xl font-black tracking-widest uppercase mb-1">
                        <span class="bg-gradient-to-r from-orange-500 via-rose-500 to-violet-600 text-transparent bg-clip-text animate-gradient-x">SELAMAT DATANG</span>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">Silakan masuk ke akun Anda</p>
                </div>
                <form @submit.prevent="handleLogin" class="space-y-5">
                    <div v-if="errorMessage" class="p-4 rounded-xl bg-rose-50 border border-rose-100 flex items-start gap-3 animate-fadeIn">
                        <AppIcon name="exclamation-triangle" class="text-rose-500 mt-0.5 text-base" />
                        <p class="text-xs font-bold text-rose-600 leading-relaxed">{{ errorMessage }}</p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Username</label>
                        <div class="flex w-full border-2 border-slate-100 rounded-xl overflow-hidden focus-within:border-orange-500 focus-within:ring-4 focus-within:ring-orange-500/10 transition-all bg-slate-50">
                            <div class="pl-4 pr-3 flex items-center justify-center text-slate-400 text-lg"><AppIcon name="user" /></div>
                            <input v-model="form.username" type="text" required class="w-full py-3.5 pr-4 bg-transparent text-sm font-bold text-slate-800 placeholder-slate-400 focus:outline-none" placeholder="Ketik username Anda">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest ml-1">Password</label>
                        <div class="flex w-full border-2 border-slate-100 rounded-xl overflow-hidden focus-within:border-orange-500 focus-within:ring-4 focus-within:ring-orange-500/10 transition-all bg-slate-50 relative">
                            <div class="pl-4 pr-3 flex items-center justify-center text-slate-400 text-lg"><AppIcon name="lock-closed" /></div>
                            <input v-model="form.password" :type="showPassword ? 'text' : 'password'" required class="w-full py-3.5 pr-12 bg-transparent text-sm font-bold text-slate-800 placeholder-slate-400 focus:outline-none" placeholder="Ketik password Anda">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-orange-500 transition-colors">
                                <AppIcon :name="showPassword ? 'eye' : 'eye-slash'" class="text-xl" />
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center px-1">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-orange-500 focus:ring-orange-500 transition-all">
                            <span class="text-[11px] font-bold text-slate-500 group-hover:text-slate-800 transition-colors">Ingat Saya</span>
                        </label>
                    </div>
                    <div class="pt-2">
                        <button type="submit" :disabled="isLoading" class="w-full py-4 bg-gradient-to-r from-orange-500 to-rose-500 text-white font-black rounded-xl shadow-[0_10px_20px_-10px_rgba(249,115,22,0.5)] transition-all active:scale-95 disabled:opacity-50 flex items-center justify-center gap-2 text-sm uppercase tracking-widest">
                            <AppIcon v-if="isLoading" name="arrow-path" class="animate-spin text-xl" />
                            <span v-else class="flex items-center gap-2">Masuk ke Aplikasi <AppIcon name="arrow-right" class="text-lg" /></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Transition>

    <!-- FOOTER -->
    <footer class="hidden lg:flex w-full bg-[#05070A] text-slate-500 py-4 px-6 text-center text-[10px] font-bold uppercase tracking-widest z-50 flex-shrink-0 flex items-center justify-center border-t border-white/5">
        <span>Aplikasi e-Rapor SMK | Versi 2026.1</span>
    </footer>
    
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCookie } from '#app'

// State modal login (mobile only)
const showLoginModal = ref(false)


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
      sekolah.value = {
        ...sekolah.value,
        nama_sekolah: res.data.sekolah.nama_sekolah,
        logo: getImageUrl(res.data.sekolah.logo)
      }
    }
  } catch (err) {
    console.error('Gagal mengambil data sekolah publik:', err)
  }
})

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
      const tokenCookie = useCookie('auth_token', { maxAge: 3600 })
      const userCookie = useCookie('user_profile', { maxAge: 3600 })
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


/* Modal fade transition (mobile login dialog) */
.modal-fade-enter-active {
  transition: opacity 0.3s ease;
}
.modal-fade-leave-active {
  transition: opacity 0.25s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
.modal-fade-enter-active .relative,
.modal-fade-leave-active .relative {
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.modal-fade-enter-from .relative {
  transform: translateY(100%);
}
.modal-fade-leave-to .relative {
  transform: translateY(100%);
}

</style>
