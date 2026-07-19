export default defineNuxtPlugin((nuxtApp) => {
  const _fetch = globalThis.$fetch
  
  globalThis.$fetch = async (request, options = {}) => {
    const impersonateCookie = useCookie('impersonate_user_id')
    
    if (impersonateCookie.value) {
      options.headers = options.headers || {}
      
      let isBypass = false;
      if (options.headers instanceof Headers) {
          isBypass = options.headers.get('X-Bypass-Impersonation') === 'true';
      } else if (Array.isArray(options.headers)) {
          isBypass = options.headers.some(h => h[0] === 'X-Bypass-Impersonation' && h[1] === 'true');
      } else {
          isBypass = options.headers['X-Bypass-Impersonation'] === 'true' || options.headers['X-Bypass-Impersonation'] === true;
      }

      if (!isBypass) {
          if (options.headers instanceof Headers) {
            options.headers.set('X-Impersonate-User-Id', impersonateCookie.value as string)
          } else if (Array.isArray(options.headers)) {
            options.headers.push(['X-Impersonate-User-Id', impersonateCookie.value as string])
          } else {
            options.headers['X-Impersonate-User-Id'] = impersonateCookie.value
          }
      }
    }
    
    return _fetch(request, options)
  }
})
