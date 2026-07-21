export default defineNuxtRouteMiddleware((to, from) => {
  if (import.meta.server) return;
  const token = useCookie('auth_token')

  // Jika tidak ada token dan bukan sedang ke halaman login
  if (!token.value && to.path !== '/login' && to.path !== '/') {
    return navigateTo('/login')
  }

  if (token.value) {
    const userCookie = useCookie('user_profile')
    if (userCookie.value) {
      let user = null;
      if (typeof userCookie.value === 'string') {
        try {
          user = JSON.parse(userCookie.value)
        } catch (e) {
          user = {} // fallback
        }
      } else {
        user = userCookie.value
      }
      
      if (user?.role === 'superadmin') {
        to.meta.layout = 'superadmin'
        setPageLayout('superadmin')
        if (to.path === '/login' || to.path === '/') return navigateTo('/admin/dashboard')
      } else {
        // Jika sudah login tapi malah buka halaman login
        if (to.path === '/login' || to.path === '/') {
          if (user.role === 'admin') return navigateTo('/admin/dashboard')
          if (user.role === 'siswa') return navigateTo('/siswa/dashboard')
          if (user.role === 'guru') return navigateTo('/guru/dashboard')
          if (user.role === 'kurikulum') return navigateTo('/kurikulum/dashboard')
          if (user.role === 'bk') return navigateTo('/bk/dashboard')
          return navigateTo('/dashboard')
        }
      }
    }
  }
})
