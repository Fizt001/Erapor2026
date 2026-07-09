export default defineNuxtRouteMiddleware((to, from) => {
  if (to.path.startsWith('/admin')) {
    to.meta.pageTransition = { name: 'admin-page', mode: 'out-in' }
  } else if (to.path.startsWith('/kurikulum')) {
    to.meta.pageTransition = { name: 'kurikulum-page', mode: 'out-in' }
  } else if (to.path.startsWith('/guru')) {
    to.meta.pageTransition = { name: 'guru-page', mode: 'out-in' }
  } else {
    to.meta.pageTransition = { name: 'page', mode: 'out-in' }
  }
})
