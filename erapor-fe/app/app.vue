<template>
  <NuxtLayout :name="layoutName">
    <NuxtPage />
  </NuxtLayout>
</template>

<script setup>
import { computed } from 'vue'
import { useCookie, useRoute } from '#imports'

const route = useRoute()
const userCookie = useCookie('user_profile')

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
