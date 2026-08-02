export const useWalasStore = () => {
    const assignedClasses = useState<any[]>('walas-classes', () => [])
    const activeKelasId = useCookie<number | null>('walas-active-kelas-id', { default: () => null })

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

    return reactive({
        assignedClasses,
        activeKelasId,
        setAssignedClasses,
        setActiveKelas
    })
}
