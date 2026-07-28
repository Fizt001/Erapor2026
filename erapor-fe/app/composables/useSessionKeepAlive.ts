/**
 * useSessionKeepAlive
 *
 * Mencegah cookie auth_token dan user_profile expired saat user
 * sedang aktif bekerja di satu halaman (tanpa navigasi).
 *
 * Cara kerja:
 * - Setiap 15 menit, cookie di-touch (di-assign ulang) sehingga
 *   masa berlakunya selalu 1 jam dari SEKARANG.
 * - Saat user logout (token kosong), interval otomatis berhenti.
 */
export function useSessionKeepAlive() {
  if (import.meta.server) return

  const COOKIE_MAX_AGE = 3600               // 1 jam
  const REFRESH_INTERVAL = 15 * 60 * 1000  // Refresh setiap 15 menit

  let intervalId: ReturnType<typeof setInterval> | null = null

  const refreshCookies = () => {
    const token = useCookie('auth_token', { maxAge: COOKIE_MAX_AGE })
    const userProfile = useCookie('user_profile', { maxAge: COOKIE_MAX_AGE })

    if (token.value) {
      // Assign ke diri sendiri untuk trigger browser update maxAge
      token.value = token.value
      if (userProfile.value) {
        userProfile.value = userProfile.value
      }
    } else {
      // Token hilang (logout/expired), hentikan interval
      if (intervalId) {
        clearInterval(intervalId)
        intervalId = null
      }
    }
  }

  onMounted(() => {
    refreshCookies()
    intervalId = setInterval(refreshCookies, REFRESH_INTERVAL)
  })

  onUnmounted(() => {
    if (intervalId) {
      clearInterval(intervalId)
      intervalId = null
    }
  })
}
