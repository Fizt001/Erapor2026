export default defineNuxtRouteMiddleware((to, from) => {
  const token = useCookie('auth_token')

  // Jika tidak ada token dan bukan sedang ke halaman login
  if (!token.value && to.path !== '/login' && to.path !== '/') {
    return navigateTo('/login')
  }

  // Jika sudah login tapi malah buka halaman login
  if (token.value && to.path === '/login') {
    const userCookie = useCookie('user_profile')
    if (userCookie.value) {
      const user = typeof userCookie.value === 'string' ? JSON.parse(userCookie.value) : userCookie.value
      
      if (user.role === 'admin') return navigateTo('/admin/dashboard')
      if (user.role === 'siswa') return navigateTo('/siswa/dashboard')
      if (user.role === 'guru') return navigateTo('/guru/dashboard')
      if (user.role === 'kurikulum') return navigateTo('/kurikulum/dashboard')
      if (user.role === 'bk') return navigateTo('/bk/dashboard')
      return navigateTo('/dashboard')
    }
  }
})
