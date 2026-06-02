export default defineNuxtRouteMiddleware((to, from) => {
  const userCookie = useCookie('user_profile')
  
  if (userCookie.value) {
    const user = typeof userCookie.value === 'string' ? JSON.parse(userCookie.value) : userCookie.value
    
    if (user.role !== 'admin') {
      // Tendang jika bukan admin
      return navigateTo('/login')
    }
  } else {
    return navigateTo('/login')
  }
})
