export default defineNuxtRouteMiddleware((to, from) => {
  const userCookie = useCookie('user_profile')
  
  if (userCookie.value) {
    const user = typeof userCookie.value === 'string' ? JSON.parse(userCookie.value) : userCookie.value
    if (user.role === 'superadmin') return;
    
    // Boleh diakses oleh admin atau kurikulum
    if (user.role !== 'admin' && user.role !== 'kurikulum') {
      return navigateTo('/login')
    }
  } else {
    return navigateTo('/login')
  }
})
