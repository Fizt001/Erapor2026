export const useReferensi = () => {
  const referensiData = useState<Record<string, any[]>>('referensi_data', () => ({}))
  const config = useRuntimeConfig()

  const fetchReferensi = async (jenis: string) => {
    // Return cached data if available
    if (referensiData.value[jenis]) {
      return referensiData.value[jenis]
    }

    try {
      const token = useCookie('auth_token')
      const response = await $fetch<{ success: boolean, data: any[] }>('http://localhost:8000/api/referensi', {
        params: { jenis },
        headers: {
          Authorization: `Bearer ${token.value}`
        }
      })

      if (response.success) {
        // Save to cache
        referensiData.value[jenis] = response.data
        return response.data
      }
    } catch (error) {
      console.error(`Failed to fetch referensi for ${jenis}:`, error)
    }
    
    return []
  }

  return {
    referensiData,
    fetchReferensi
  }
}
