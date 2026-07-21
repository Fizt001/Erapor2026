export default defineNuxtRouteMiddleware((to, from) => {
  const userCookie = useCookie('user_profile')
  
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

    if (user?.role === 'superadmin') return;
    
    if (user?.role !== 'admin') {
      // Tendang jika bukan admin
      return navigateTo('/login')
    }
  } else {
    return navigateTo('/login')
  }
})
