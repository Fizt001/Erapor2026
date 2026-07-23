<template>
  <NuxtLayout :name="layoutName">
    <NuxtPage />
  </NuxtLayout>
</template>

<script setup>
import { computed, onMounted, onUnmounted } from 'vue'
import { useCookie, useRoute, useRouter, useHead } from '#imports'
import { useAutoSave } from '~/composables/useAutoSave'
import { useSwal } from '~/composables/useSwal'

const route = useRoute()
const router = useRouter()
const userCookie = useCookie('user_profile')
const tokenCookie = useCookie('auth_token')
const { triggerAutoSave } = useAutoSave()

useHead({
  title: computed(() => route.meta.title ? `${route.meta.title} - e-Rapor` : 'e-Rapor')
})

let idleTimer = null
let swalWarningActive = false

const resetIdleTime = () => {
  if (tokenCookie.value) {
    localStorage.setItem('lastActiveTime', Date.now().toString())
  }
}

const checkIdleTime = async () => {
  if (!tokenCookie.value) return

  const lastActiveStr = localStorage.getItem('lastActiveTime')
  if (!lastActiveStr) return
  
  const lastActive = parseInt(lastActiveStr)
  const diffMinutes = (Date.now() - lastActive) / 1000 / 60

  if (diffMinutes >= 60) {
    // Timeout!
    if (idleTimer) clearInterval(idleTimer)
    useSwal().close()
    await triggerAutoSave()
    tokenCookie.value = null
    userCookie.value = null
    router.push('/login')
    useSwal().toast('Sesi telah berakhir karena tidak ada aktivitas.', 'warning')
  } else if (diffMinutes >= 55 && !swalWarningActive) {
    swalWarningActive = true
    useSwal().fire({
      title: 'Sesi Akan Berakhir',
      text: 'Anda tidak melakukan aktivitas selama 55 menit. Sesi akan berakhir otomatis dalam 5 menit, dan pekerjaan akan disimpan.',
      icon: 'warning',
      timer: 5 * 60 * 1000,
      timerProgressBar: true,
      showConfirmButton: true,
      confirmButtonText: 'Tetap Login',
    }).then((result) => {
      swalWarningActive = false
      if (result.isConfirmed) {
         resetIdleTime()
      }
    })
  }
}

onMounted(() => {
  if (typeof window !== 'undefined') {
    window.addEventListener('mousemove', resetIdleTime)
    window.addEventListener('keydown', resetIdleTime)
    window.addEventListener('scroll', resetIdleTime)
    window.addEventListener('click', resetIdleTime)
    
    resetIdleTime()
    idleTimer = setInterval(checkIdleTime, 30 * 1000) // check every 30 seconds
  }
})

onUnmounted(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('mousemove', resetIdleTime)
    window.removeEventListener('keydown', resetIdleTime)
    window.removeEventListener('scroll', resetIdleTime)
    window.removeEventListener('click', resetIdleTime)
    if (idleTimer) clearInterval(idleTimer)
  }
})

const layoutName = computed(() => {
  if (userCookie.value) {
    let user = null;
    if (typeof userCookie.value === 'string') {
      try {
        user = JSON.parse(userCookie.value)
      } catch (e) {
        user = {}
      }
    } else {
      user = userCookie.value
    }
    
    if (user?.role === 'superadmin') {
      return 'superadmin'
    }
  }
  
  return route.meta.layout || 'default'
})
</script>

<style>
/* Global Page Transition: Vertical Fade & Slide */
.page-enter-active,
.page-leave-active {
  transition: opacity 0.3s ease-out, transform 0.3s ease-out;
}
.page-enter-from,
.page-leave-to {
  opacity: 0;
  transform: translateY(15px);
}

.layout-enter-active,
.layout-leave-active {
  transition: opacity 0.3s ease-out, transform 0.3s ease-out;
}
.layout-enter-from,
.layout-leave-to {
  opacity: 0;
  transform: translateY(15px);
}

/* Admin specific transition: horizontal slide */
.admin-page-enter-active,
.admin-page-leave-active {
  transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.admin-page-enter-from {
  opacity: 0;
  transform: translateX(20px);
}
.admin-page-leave-to {
  opacity: 0;
  transform: translateX(-20px);
}

/* Kurikulum specific transition: scale (zoom) */
.kurikulum-page-enter-active,
.kurikulum-page-leave-active {
  transition: opacity 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.kurikulum-page-enter-from {
  opacity: 0;
  transform: scale(0.97);
}
.kurikulum-page-leave-to {
  opacity: 0;
  transform: scale(0.97);
}

/* Guru specific transition: slide down from top */
.guru-page-enter-active,
.guru-page-leave-active {
  transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.guru-page-enter-from {
  opacity: 0;
  transform: translateY(-20px);
}
.guru-page-leave-to {
  opacity: 0;
  transform: translateY(20px);
}

/* Global Scrollbar Hiding */
::-webkit-scrollbar {
  display: none;
  width: 0px;
  height: 0px;
}
* {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
  font-family: 'Inter', sans-serif;
}
</style>
