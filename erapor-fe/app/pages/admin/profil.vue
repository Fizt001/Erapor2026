<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    
<!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all hidden xl:flex">
        
        <div class="p-6 shrink-0 relative z-10">
          <div class="bg-gradient-to-r from-teal-600 to-emerald-700 rounded-2xl p-5 border border-teal-500 shadow-sm relative overflow-hidden flex items-center gap-4">
            <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0 relative z-10">👤</div>
            <div class="relative z-10">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">Profil Pengelola</h3>
                <p class="text-[10px] text-teal-100 font-semibold uppercase mt-0.5">Identitas Administrator</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-20 text-white">
              <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg>
            </div>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-2 custom-scrollbar">
            <button 
                type="button" 
                class="w-full flex items-center gap-4 p-4 rounded-2xl transition-all duration-300 text-left relative group overflow-hidden border bg-emerald-50 border-emerald-200 text-emerald-700"
            >
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl transition-all duration-300 shrink-0 bg-white text-emerald-600 shadow-sm">
                    🛡️
                </div>
                <div class="overflow-hidden">
                    <p class="font-bold text-xs uppercase tracking-widest truncate">Data Akun</p>
                    <p class="text-[10px] font-semibold text-slate-400 truncate mt-0.5">Informasi Kredensial</p>
                </div>
            </button>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0">
        <div class="p-6 lg:p-8 max-w-5xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            
            <!-- Header -->
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white/80 backdrop-blur-xl z-20">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 shadow-sm flex items-center justify-center text-2xl text-white">
                        🛡️
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-emerald-700">Data Akun Admin</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Informasi Kredensial Login</p>
                    </div>
                </div>
                <button @click="saveProfile" :disabled="isSaving" class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl shadow-md disabled:opacity-70 flex items-center justify-center gap-2 text-xs uppercase tracking-widest transition-all">
                    <span v-if="isSaving" class="animate-spin">⏳</span>
                    <span v-else>💾 Simpan</span>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar p-6 md:p-10 relative z-0">
                <div class="animate-fadeIn space-y-8">
                    <!-- Avatar Banner -->
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 border-b border-slate-100 pb-8">
                        <div class="w-24 h-24 rounded-3xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/30 flex items-center justify-center text-white text-4xl font-black border-4 border-white shrink-0">
                            {{ userInitials }}
                        </div>
                        <div class="text-center sm:text-left pt-2">
                            <h2 class="text-2xl font-black text-slate-800">{{ userProfile?.name || 'Administrator' }}</h2>
                            <p class="text-sm font-semibold text-emerald-600 tracking-wider uppercase mt-1">{{ userProfile?.role || 'Admin' }} Sistem Erapor</p>
                            <span class="inline-block mt-3 px-3 py-1 bg-slate-100 text-slate-500 text-xs font-bold rounded-lg border border-slate-200">
                                🟢 Akun Aktif
                            </span>
                        </div>
                    </div>

                    <!-- Informasi Akun -->
                    <form @submit.prevent="saveProfile">
                        <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-4 border-l-4 border-emerald-500 pl-3">Rincian Profil</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Nama Lengkap -->
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Nama Lengkap</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">👤</span>
                                    <input type="text" v-model="form.name" required class="w-full pl-11 pr-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="Masukkan nama lengkap">
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Alamat Email (Login)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">✉️</span>
                                    <input type="email" v-model="form.email" required class="w-full pl-11 pr-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="email@contoh.com">
                                </div>
                            </div>

                            <!-- Password Baru -->
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Ubah Password (Opsional)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">🔑</span>
                                    <input type="password" v-model="form.password" class="w-full pl-11 pr-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" placeholder="Biarkan kosong jika tidak ingin mengubah password">
                                </div>
                            </div>

                        </div>
                    </form>
                    
                    <!-- Peringatan Keamanan -->
                    <div class="bg-amber-50 rounded-2xl p-5 border border-amber-200 flex gap-4 items-start">
                        <div class="text-amber-500 text-xl shrink-0">⚠️</div>
                        <div>
                            <h5 class="text-xs font-bold text-amber-800 uppercase tracking-wider mb-1">Informasi Keamanan</h5>
                            <p class="text-[11px] text-amber-700 leading-relaxed font-medium">Akun ini memiliki hak akses penuh (Super Administrator) untuk mengelola Master Data Sistem, Pengguna, dan Tahun Ajaran. Mohon jaga kerahasiaan kredensial login Anda. Untuk mengubah password, silakan hubungi tim IT atau gunakan menu Reset Password di halaman Login jika tersedia.</p>
                        </div>
                    </div>

                </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'

definePageMeta({
  layout: 'admin',
  middleware: 'admin',
  title: 'Profil Admin'
})

const userCookie = useCookie('user_profile')
const userProfile = computed(() => {
  if (!userCookie.value) return null
  return typeof userCookie.value === 'string' ? JSON.parse(userCookie.value) : userCookie.value
})

const userInitials = computed(() => {
  if (!userProfile.value || !userProfile.value.name) return 'A'
  return userProfile.value.name.charAt(0).toUpperCase()
})

const form = ref({
  name: '',
  email: '',
  password: ''
})

const isSaving = ref(false)

onMounted(() => {
  if (userProfile.value) {
    form.value.name = userProfile.value.name || ''
    form.value.email = userProfile.value.email || ''
  }
})

const saveProfile = async () => {
  if (isSaving.value) return
  isSaving.value = true

  const tokenCookie = useCookie('auth_token')
  try {
    const payload = {
      name: form.value.name,
      email: form.value.email,
    }
    if (form.value.password) {
      payload.password = form.value.password
    }

    const response = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/user/profile', {
      method: 'PUT',
      headers: { 
        Authorization: `Bearer ${tokenCookie.value}`,
      },
      body: payload
    })

    if (response.success) {
      // Update cookie
      const updatedProfile = {
        ...userProfile.value,
        name: response.data.name,
        email: response.data.email
      }
      userCookie.value = JSON.stringify(updatedProfile)
      
      form.value.password = ''
      useSwal().toast('Profil berhasil diperbarui!', 'success')
    }
  } catch (error) {
    console.error('Failed to update profile:', error)
    if (error.response?._data?.errors?.email) {
      useSwal().toast('Email sudah digunakan oleh akun lain.', 'error')
    } else {
      useSwal().toast('Gagal memperbarui profil.', 'error')
    }
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
  animation: fadeIn 0.3s ease-out forwards;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #94a3b8;
}
</style>
