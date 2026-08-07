export default defineNuxtRouteMiddleware((to, from) => {
  const token = useCookie('auth_token')
  const userProfileCookie = useCookie('user_profile')

  // Belum login
  if (!token.value) {
    return navigateTo('/login')
  }

  if (userProfileCookie.value) {
    let user = null
    try {
      user = typeof userProfileCookie.value === 'string'
        ? JSON.parse(userProfileCookie.value)
        : userProfileCookie.value
    } catch (e) {
      console.error('Failed to parse user profile cookie', e)
      return navigateTo('/login')
    }

    // Superadmin boleh masuk ke semua role
    if (user?.role === 'superadmin') return

    // Hanya role 'bk' yang diizinkan
    if (user?.role !== 'bk') {
      if (user?.role === 'admin') return navigateTo('/admin/dashboard')
      if (user?.role === 'kurikulum') return navigateTo('/kurikulum/dashboard')
      if (user?.role === 'guru') return navigateTo('/guru/dashboard')
      if (user?.role === 'siswa') return navigateTo('/siswa/dashboard')
      return navigateTo('/login')
    }
  } else {
    return navigateTo('/login')
  }
})
