<template>
  <div class="h-screen w-full flex flex-col font-sans bg-[#090C15] relative overflow-hidden items-center justify-center">
    
    <!-- Vibrant Mozilla-style Orbs -->
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[20%] -left-[10%] w-[60%] h-[70%] rounded-full mix-blend-screen filter blur-[100px] opacity-60 bg-gradient-to-br from-orange-500 to-rose-600 animate-pulse" style="animation-duration: 8s;"></div>
        <div class="absolute -bottom-[20%] -right-[10%] w-[70%] h-[80%] rounded-full mix-blend-screen filter blur-[120px] opacity-50 bg-gradient-to-tl from-indigo-600 via-violet-600 to-transparent"></div>
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiLz48L3N2Zz4=')]"></div>
    </div>

    <!-- Main Card -->
    <div class="relative z-10 w-full max-w-md bg-white rounded-3xl shadow-2xl px-8 pt-10 pb-12 mx-4 animate-scaleIn">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-rose-500 rounded-2xl mx-auto flex items-center justify-center shadow-lg shadow-orange-500/30 mb-5">
                <span class="text-3xl text-white">🎓</span>
            </div>
            <h2 class="text-2xl font-black tracking-widest uppercase mb-2 text-slate-800">
                Pilih Peran
            </h2>
            <p class="text-sm text-slate-500 font-medium">Akun Anda memiliki akses ganda. Silakan pilih peran yang ingin digunakan saat ini.</p>
        </div>

        <div class="space-y-4">
            <button @click="selectRole('guru')" class="w-full flex items-center gap-4 p-4 rounded-2xl border-2 border-slate-100 hover:border-orange-500 hover:bg-orange-50/50 transition-all group active:scale-95">
                <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600 text-xl group-hover:scale-110 transition-transform">
                    <AppIcon name="academic-cap" />
                </div>
                <div class="text-left flex-1">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Guru Mata Pelajaran</h3>
                    <p class="text-[11px] font-bold text-slate-400 mt-0.5">Input nilai sumatif & formatif</p>
                </div>
                <AppIcon name="chevron-right" class="text-slate-300 group-hover:text-orange-500 text-xl transition-colors" />
            </button>

            <button @click="selectRole('walas')" class="w-full flex items-center gap-4 p-4 rounded-2xl border-2 border-slate-100 hover:border-amber-500 hover:bg-amber-50/50 transition-all group active:scale-95">
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 text-xl group-hover:scale-110 transition-transform">
                    <AppIcon name="users" />
                </div>
                <div class="text-left flex-1">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Wali Kelas</h3>
                    <p class="text-[11px] font-bold text-slate-400 mt-0.5">Kelola kelas & cetak rapor</p>
                </div>
                <AppIcon name="chevron-right" class="text-slate-300 group-hover:text-amber-500 text-xl transition-colors" />
            </button>
        </div>
        
        <div class="mt-8 text-center">
            <button @click="logout" class="text-xs font-bold text-slate-400 hover:text-rose-500 uppercase tracking-widest transition-colors flex items-center justify-center gap-1 mx-auto">
                <AppIcon name="arrow-left-on-rectangle" class="text-sm" /> Keluar dari Akun
            </button>
        </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useCookie, onMounted } from '#app'

definePageMeta({
  layout: false
})

const router = useRouter()
const userCookie = useCookie('user_profile')
const tokenCookie = useCookie('auth_token')
const runtimeConfig = useRuntimeConfig()
const apiUrl = import.meta.env.VITE_API_BASE_URL || runtimeConfig.public.apiBase || 'http://localhost:8000'

onMounted(() => {
    // Keamanan: Pastikan user sudah login
    if (!userCookie.value || !tokenCookie.value) {
        router.push('/login')
        return
    }

    // Redirect otomatis jika bukan dual role (guru + walas)
    const user = typeof userCookie.value === 'string' ? JSON.parse(userCookie.value) : userCookie.value
    if (user.role !== 'guru' || !user.is_walas) {
        if (user.role === 'admin' || user.role === 'superadmin') {
            router.push('/admin/dashboard')
        } else {
            router.push(`/${user.role}/dashboard`)
        }
    }
})

const selectRole = (role) => {
    if (role === 'guru') {
        router.push('/guru/dashboard')
    } else if (role === 'walas') {
        router.push('/guru/walas/dashboard')
    }
}

const logout = async () => {
  try {
    await $fetch(apiUrl + '/api/logout', {
      method: 'POST',
      headers: { Authorization: `Bearer ${tokenCookie.value}` }
    })
  } catch (error) {
    console.error(error)
  }
  
  tokenCookie.value = null
  userCookie.value = null
  router.push('/login')
}
</script>

<style scoped>
.animate-scaleIn {
  animation: scaleIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes scaleIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
</style>
