import { useState } from '#imports'

export const useProfileModal = () => {
  const isProfileModalOpen = useState<boolean>('isProfileModalOpen', () => false)
  
  const openProfileModal = () => {
    isProfileModalOpen.value = true
  }
  
  const closeProfileModal = () => {
    isProfileModalOpen.value = false
  }
  
  return {
    isProfileModalOpen,
    openProfileModal,
    closeProfileModal
  }
}
