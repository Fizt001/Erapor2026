<template>
  <Teleport to="body">
    <div v-if="isProfileModalOpen" class="fixed inset-0 z-[150] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity p-4">
      <!-- Overlay Click to Close -->
      <div class="absolute inset-0 z-0" @click="closeProfileModal"></div>
      
      <!-- Modal Panel -->
      <div class="w-full sm:max-w-2xl sm:h-auto max-h-[90vh] bg-white sm:rounded-3xl shadow-2xl flex flex-col relative z-10 transform transition-transform duration-300 animate-slideUpFade overflow-hidden">
        
        <!-- Header Modal -->
        <div class="p-5 border-b flex items-center justify-between shrink-0 z-20" :class="theme.header">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 text-white flex items-center justify-center backdrop-blur-md border border-white/30"><AppIcon name="user" class="w-6 h-6"/></div>
                <div>
                    <h3 class="font-black text-white text-base uppercase tracking-widest">Profil Saya</h3>
                    <p class="text-[10px] font-semibold tracking-wider mt-0.5 uppercase" :class="theme.headerText">Identitas & Kredensial Akun</p>
                </div>
            </div>
            <button @click="closeProfileModal" class="w-8 h-8 rounded-full bg-white/10 hover:text-white hover:bg-rose-500 flex items-center justify-center transition-colors shadow-sm border border-white/10 relative z-30" :class="theme.headerText"><AppIcon name="x-mark" /></button>
        </div>

        <!-- Content Container -->
        <div class="flex-1 overflow-y-auto custom-scrollbar relative z-10 bg-white">
            <div class="p-6 md:p-8">
                <div class="animate-fadeIn space-y-8">
                    <!-- Avatar Banner -->
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 border-b border-slate-100 pb-8">
                        <div class="w-24 h-24 rounded-3xl bg-gradient-to-br from-slate-800 to-slate-900 shadow-lg shadow-slate-500/30 flex items-center justify-center text-white text-4xl font-black border-4 border-white shrink-0">
                            {{ userInitials }}
                        </div>
                        <div class="text-center sm:text-left pt-2">
                            <h2 class="text-2xl font-black text-slate-800">{{ userProfile?.name || 'Pengguna' }}</h2>
                            <p class="text-sm font-semibold tracking-wider uppercase mt-1" :class="theme.textAccent">{{ userProfile?.role || 'User' }} Sistem Erapor</p>
                            <span class="inline-flex items-center mt-3 px-3 py-1 text-xs font-bold rounded-lg border gap-1.5" :class="theme.badge">
                                <AppIcon name="check-circle" class="text-sm" /> Akun Aktif
                            </span>
                        </div>
                    </div>

                    <!-- Informasi Akun -->
                    <form @submit.prevent="saveProfile">
                        <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-4 border-l-4 pl-3" :class="theme.accentBorder">Rincian Profil</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Nama Lengkap -->
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Nama Lengkap</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400"><AppIcon name="user" /></span>
                                    <input type="text" v-model="form.name" required class="w-full pl-11 pr-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" :class="theme.inputFocus" placeholder="Masukkan nama lengkap">
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Alamat Email (Login)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400"><AppIcon name="envelope" /></span>
                                    <input type="email" v-model="form.email" required class="w-full pl-11 pr-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" :class="theme.inputFocus" placeholder="email@contoh.com">
                                </div>
                            </div>

                            <!-- Password Baru -->
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Ubah Password (Opsional)</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400"><AppIcon name="key" /></span>
                                    <input type="password" v-model="form.password" class="w-full pl-11 pr-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 transition-all text-sm font-semibold text-slate-800 placeholder-slate-400" :class="theme.inputFocus" placeholder="Biarkan kosong jika tidak ingin mengubah password">
                                </div>
                            </div>

                        </div>
                        
                        <div class="mt-8 flex justify-end">
                            <button type="submit" :disabled="isSaving" class="w-full sm:w-auto px-8 py-3.5 text-white font-bold rounded-2xl shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2" :class="theme.btnSubmit">
                                <span v-if="isSaving" class="animate-spin"><AppIcon name="clock" /></span>
                                <span v-else><AppIcon name="save" /></span> 
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                    
                    <!-- Peringatan Keamanan -->
                    <div v-if="userProfile?.role === 'admin' || userProfile?.role === 'kepsek'" class="bg-amber-50 rounded-2xl p-5 border border-amber-200 flex gap-4 items-start mt-8">
                        <div class="text-amber-500 text-xl shrink-0"><AppIcon name="exclamation-triangle" /></div>
                        <div>
                            <h5 class="text-xs font-bold text-amber-800 uppercase tracking-wider mb-1">Informasi Keamanan</h5>
                            <p class="text-[11px] text-amber-700 leading-relaxed font-medium">Akun Anda memiliki hak akses tinggi. Mohon jaga kerahasiaan kredensial login Anda. Pastikan password sulit ditebak dan tidak dibagikan ke siapapun.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { useCookie } from '#imports'
import { useProfileModal } from '~/composables/useProfileModal'
import { useSwal } from '~/composables/useSwal'

const { isProfileModalOpen, closeProfileModal } = useProfileModal()

const userCookie = useCookie('user_profile')
const userProfile = computed(() => {
  if (!userCookie.value) return null
  return typeof userCookie.value === 'string' ? JSON.parse(userCookie.value) : userCookie.value
})

const userInitials = computed(() => {
  if (!userProfile.value || !userProfile.value.name) return 'U'
  return userProfile.value.name.charAt(0).toUpperCase()
})

const theme = computed(() => {
  const role = userProfile.value?.role?.toLowerCase() || 'admin'
  switch (role) {
    case 'guru':
      return {
        header: 'bg-gradient-to-r from-sky-500 to-indigo-600 border-sky-400/50',
        headerText: 'text-sky-100',
        badge: 'bg-sky-50 text-sky-600 border-sky-100',
        textAccent: 'text-sky-600',
        accentBorder: 'border-sky-500',
        inputFocus: 'focus:ring-sky-500/10 focus:border-sky-500',
        btnSubmit: 'bg-gradient-to-r from-sky-500 to-indigo-600 shadow-sky-500/30'
      }
    case 'siswa':
      return {
        header: 'bg-gradient-to-r from-indigo-500 to-purple-600 border-indigo-400/50',
        headerText: 'text-indigo-100',
        badge: 'bg-indigo-50 text-indigo-600 border-indigo-100',
        textAccent: 'text-indigo-600',
        accentBorder: 'border-indigo-500',
        inputFocus: 'focus:ring-indigo-500/10 focus:border-indigo-500',
        btnSubmit: 'bg-gradient-to-r from-indigo-500 to-purple-600 shadow-indigo-500/30'
      }
    case 'kurikulum':
      return {
        header: 'bg-gradient-to-r from-amber-500 to-orange-600 border-amber-400/50',
        headerText: 'text-amber-100',
        badge: 'bg-amber-50 text-amber-600 border-amber-100',
        textAccent: 'text-amber-600',
        accentBorder: 'border-amber-500',
        inputFocus: 'focus:ring-amber-500/10 focus:border-amber-500',
        btnSubmit: 'bg-gradient-to-r from-amber-500 to-orange-600 shadow-amber-500/30'
      }
    case 'bk':
      return {
        header: 'bg-gradient-to-r from-rose-500 to-pink-600 border-rose-400/50',
        headerText: 'text-rose-100',
        badge: 'bg-rose-50 text-rose-600 border-rose-100',
        textAccent: 'text-rose-600',
        accentBorder: 'border-rose-500',
        inputFocus: 'focus:ring-rose-500/10 focus:border-rose-500',
        btnSubmit: 'bg-gradient-to-r from-rose-500 to-pink-600 shadow-rose-500/30'
      }
    case 'walas':
      return {
        header: 'bg-gradient-to-r from-amber-400 to-orange-500 border-amber-400/50',
        headerText: 'text-amber-100',
        badge: 'bg-amber-50 text-amber-600 border-amber-100',
        textAccent: 'text-amber-600',
        accentBorder: 'border-amber-500',
        inputFocus: 'focus:ring-amber-500/10 focus:border-amber-500',
        btnSubmit: 'bg-gradient-to-r from-amber-400 to-orange-500 shadow-amber-500/30'
      }
    case 'admin':
    case 'kepsek':
    default:
      return {
        header: 'bg-gradient-to-r from-emerald-600 to-teal-700 border-emerald-500/50',
        headerText: 'text-emerald-100',
        badge: 'bg-emerald-50 text-emerald-600 border-emerald-100',
        textAccent: 'text-emerald-600',
        accentBorder: 'border-emerald-500',
        inputFocus: 'focus:ring-emerald-500/10 focus:border-emerald-500',
        btnSubmit: 'bg-gradient-to-r from-emerald-500 to-teal-600 shadow-emerald-500/30'
      }
  }
})

const form = ref({
  name: '',
  email: '',
  password: ''
})

const isSaving = ref(false)

watch(isProfileModalOpen, (isOpen) => {
  if (isOpen && userProfile.value) {
    form.value.name = userProfile.value.name || ''
    form.value.email = userProfile.value.email || ''
    form.value.password = ''
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
      closeProfileModal()
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
@keyframes slideUpFade {
  from { opacity: 0; transform: translateY(20px) scale(0.98); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}
.animate-slideUpFade {
  animation: slideUpFade 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

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
