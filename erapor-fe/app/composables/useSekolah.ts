import { useState } from '#app'

export const useSekolah = () => {
  const sekolah = useState('sekolah', () => ({
    nama_sekolah: 'SMK Yatindo',
    logo: null
  }))

  const ta_aktif = useState('ta_aktif', () => null)

  const fetchSekolah = async () => {
    try {
      const tokenCookie = useCookie('auth_token')
      const response = await $fetch('http://localhost:8000/api/admin/sekolah', {
        headers: {
          Authorization: `Bearer ${tokenCookie.value}`
        }
      })
      if (response && response.data) {
        sekolah.value = {
          nama_sekolah: response.data.nama_sekolah || 'SMK Yatindo',
          logo: response.data.logo || null
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
    fetchSekolah
  }
}
