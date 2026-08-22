import Swal from 'sweetalert2'

export default defineNuxtPlugin(() => {
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
  })

  return {
    provide: {
      toast: {
        success: (message: string) => Toast.fire({ icon: 'success', title: message }),
        error: (message: string) => Toast.fire({ icon: 'error', title: message }),
        warning: (message: string) => Toast.fire({ icon: 'warning', title: message }),
        info: (message: string) => Toast.fire({ icon: 'info', title: message })
      }
    }
  }
})
