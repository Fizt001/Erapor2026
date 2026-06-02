export default defineNuxtRouteMiddleware((to, from) => {
    const token = useCookie('auth_token')
    const userProfile = useCookie('user_profile')
  
    if (!token.value) {
      return navigateTo('/login')
    }
  
    if (userProfile.value) {
      try {
        const user = typeof userProfile.value === 'string' 
          ? JSON.parse(userProfile.value) 
          : userProfile.value
          
        if (user.role !== 'bk' && user.role !== 'admin') {
           if (user.role === 'admin') return navigateTo('/admin/dashboard')
           if (user.role === 'kurikulum') return navigateTo('/kurikulum/dashboard')
           return navigateTo('/login')
        }
      } catch (e) {
        console.error('Failed to parse user profile cookie', e)
        return navigateTo('/login')
      }
    }
  })
