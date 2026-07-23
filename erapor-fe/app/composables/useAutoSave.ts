import { useState } from '#imports'

export const useAutoSave = () => {
    // Menyimpan fungsi callback auto-save dari komponen yang sedang aktif
    const autoSaveCallback = useState<(() => Promise<void>) | null>('autoSaveCallback', () => null)
    const isAutoSaving = useState<boolean>('isAutoSaving', () => false)

    const registerAutoSave = (fn: () => Promise<void>) => {
        autoSaveCallback.value = fn
    }

    const unregisterAutoSave = () => {
        autoSaveCallback.value = null
    }

    const triggerAutoSave = async () => {
        if (autoSaveCallback.value) {
            isAutoSaving.value = true
            try {
                await autoSaveCallback.value()
            } catch (error) {
                console.error('Auto-save failed:', error)
            } finally {
                isAutoSaving.value = false
            }
        }
    }

    return { 
        registerAutoSave, 
        unregisterAutoSave, 
        triggerAutoSave,
        isAutoSaving
    }
}
