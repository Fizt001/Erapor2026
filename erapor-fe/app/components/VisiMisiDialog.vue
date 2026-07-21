<template>
  <Transition name="dialog-overlay">
    <div v-if="isOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 print:hidden">
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="close"></div>
      
      <!-- Dialog Content -->
      <Transition name="dialog-content">
        <div v-if="isOpen" class="relative bg-white rounded-3xl shadow-2xl shadow-slate-900/20 w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden ring-1 ring-slate-200">
          <!-- Close Button -->
          <button @click="close" class="absolute top-4 right-4 w-10 h-10 rounded-full bg-slate-100/50 hover:bg-rose-50 text-slate-400 hover:text-rose-500 flex items-center justify-center transition-colors z-10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
          
          <div class="flex-1 overflow-y-auto custom-scrollbar p-8 sm:p-10 text-center">
            <!-- Logo -->
            <div class="mb-8 flex justify-center">
              <div v-if="sekolah?.logo" class="w-32 h-32 rounded-3xl bg-white shadow-xl shadow-slate-200/50 flex items-center justify-center p-4 border border-slate-100">
                <img :src="sekolah.logo" alt="Logo Sekolah" class="max-w-full max-h-full object-contain" />
              </div>
              <div v-else class="w-32 h-32 rounded-3xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-xl shadow-emerald-500/30 flex items-center justify-center">
                <span class="text-6xl font-black text-white">e</span>
              </div>
            </div>
            
            <h2 class="text-2xl sm:text-3xl font-black text-slate-800 uppercase tracking-widest mb-2">{{ sekolah?.nama_sekolah || 'E-RAPOR' }}</h2>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-10">Visi & Misi Sekolah</p>
            
            <!-- Visi -->
            <div class="mb-10 text-left bg-emerald-50/50 p-6 sm:p-8 rounded-3xl border border-emerald-100/50 relative">
              <div class="absolute -top-4 left-6 px-4 py-1.5 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl shadow-md text-white text-[10px] font-black uppercase tracking-widest">
                Visi
              </div>
              <p v-if="sekolah?.visi" class="text-base sm:text-lg text-slate-700 leading-relaxed font-bold italic whitespace-pre-wrap">{{ sekolah.visi }}</p>
              <p v-else class="text-sm text-slate-400 italic">Visi belum diatur.</p>
            </div>
            
            <!-- Misi -->
            <div class="text-left bg-blue-50/50 p-6 sm:p-8 rounded-3xl border border-blue-100/50 relative">
              <div class="absolute -top-4 left-6 px-4 py-1.5 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl shadow-md text-white text-[10px] font-black uppercase tracking-widest">
                Misi
              </div>
              <p v-if="sekolah?.misi" class="text-sm sm:text-base text-slate-700 leading-relaxed font-medium whitespace-pre-wrap">{{ sekolah.misi }}</p>
              <p v-else class="text-sm text-slate-400 italic">Misi belum diatur.</p>
            </div>
            
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script setup>
import { ref } from 'vue'

const { sekolah } = useSekolah()
const isOpen = ref(false)

const open = () => {
  isOpen.value = true
}

const close = () => {
  isOpen.value = false
}

defineExpose({
  open,
  close
})
</script>

<style scoped>
.custom-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.custom-scrollbar::-webkit-scrollbar { display: none; }

.dialog-overlay-enter-active, .dialog-overlay-leave-active { transition: opacity 0.3s ease; }
.dialog-overlay-enter-from, .dialog-overlay-leave-to { opacity: 0; }

.dialog-content-enter-active, .dialog-content-leave-active { transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
.dialog-content-enter-from, .dialog-content-leave-to { opacity: 0; transform: scale(0.95) translateY(10px); }
</style>
