import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useWalasStore = defineStore('walas', () => {
    const assignedClasses = ref<any[]>([])
    const activeKelasId = ref<number | null>(null)

    const setAssignedClasses = (classes: any[]) => {
        assignedClasses.value = classes
        
        if (classes && classes.length > 0) {
            const stillExists = classes.find(c => c.id === activeKelasId.value)
            if (!stillExists) {
                activeKelasId.value = classes[0].id
            }
        } else {
            activeKelasId.value = null
        }
    }

    const setActiveKelas = (id: number) => {
        activeKelasId.value = id
    }

    return {
        assignedClasses,
        activeKelasId,
        setAssignedClasses,
        setActiveKelas
    }
}, {
    persist: {
        storage: persistedState.localStorage,
        paths: ['activeKelasId']
    }
})
