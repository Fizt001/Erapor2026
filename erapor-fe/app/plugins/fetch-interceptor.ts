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
    // Walas Multi-Class Support
    if (typeof request === 'string' && request.includes('/api/guru/walas') && process.client) {
        try {
            const activeKelasCookie = useCookie('walas-active-kelas-id')
            if (activeKelasCookie.value) {
                const url = new URL(request.startsWith('http') ? request : window.location.origin + request)
                if (!url.searchParams.has('kelas_id')) {
                    url.searchParams.append('kelas_id', activeKelasCookie.value.toString())
                    request = url.toString()
                }
            }
        } catch (e) {
            console.error('Walas Interceptor Error:', e)
        }
    }
    
    return _fetch(request, options)
  }
})
