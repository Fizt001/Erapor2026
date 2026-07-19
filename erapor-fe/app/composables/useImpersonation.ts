import { useState } from '#app'

export const useImpersonation = () => {
    const impersonateUserId = useCookie('impersonate_user_id')
    const impersonateRole = useCookie('impersonate_role')

    const setImpersonation = (userId: string | number, role: string) => {
        impersonateUserId.value = userId.toString()
        impersonateRole.value = role
    }

    const clearImpersonation = () => {
        impersonateUserId.value = null
        impersonateRole.value = null
    }

    return {
        impersonateUserId,
        impersonateRole,
        setImpersonation,
        clearImpersonation
    }
}
