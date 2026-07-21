import { useState } from '#app'

export const useSekolah = () => {
  const sekolah = useState('sekolah', () => ({
    nama_sekolah: 'SMK Yatindo',
    logo: null,
    foto_1: null,
    foto_2: null,
    foto_3: null
  }))

  const ta_aktif = useState('ta_aktif', () => null)

  const apiUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'

  const getImageUrl = (path) => {
    if (!path) return null
    if (path.startsWith('http') || path.startsWith('blob:')) return path
    return `${apiUrl}/${path.replace(/^\//, '')}`
  }

  const fetchSekolah = async () => {
    try {
      const tokenCookie = useCookie('auth_token')
      const response = await $fetch(apiUrl + '/api/admin/sekolah', {
        headers: {
          Authorization: `Bearer ${tokenCookie.value}`
        }
      })
      if (response && response.data) {
        sekolah.value = {
          nama_sekolah: response.data.nama_sekolah || 'SMK Yatindo',
          logo: getImageUrl(response.data.logo),
          foto_1: getImageUrl(response.data.foto_1),
          foto_2: getImageUrl(response.data.foto_2),
          foto_3: getImageUrl(response.data.foto_3)
        }
        if (response.ta_aktif) {
          ta_aktif.value = response.ta_aktif
        }
      }
    } catch (error) {
      console.error('Failed to fetch sekolah:', error)
    }
  }

  return {
    sekolah,
    ta_aktif,
    fetchSekolah,
    getImageUrl
  }
}
