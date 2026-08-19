export default defineNuxtRouteMiddleware((to, from) => {
  if (import.meta.server) return;
  const userCookie = useCookie('user_profile')

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

  if (!user || user.role !== 'kepsek') {
    return navigateTo('/login')
  }

  // Set layout
  to.meta.layout = 'kepsek'
  setPageLayout('kepsek')
})
