import Swal from 'sweetalert2';

const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 1500,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.onmouseenter = Swal.stopTimer;
    toast.onmouseleave = Swal.resumeTimer;
  }
});

const swalInstance = Object.assign(Swal, {
  toast: (title, text = '', icon = 'success') => {
    if (['success', 'error', 'warning', 'info', 'question'].includes(text) && icon === 'success') {
      icon = text;
      text = '';
    }
    
    return Toast.fire({
      icon: icon,
      title: title,
      text: text
    });
  }
});

export const useSwal = () => swalInstance;

