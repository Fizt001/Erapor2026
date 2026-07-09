import re

file_path = r'd:\koding\Erapor2026\erapor-fe\app\pages\admin\tahun-ajaran.vue'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace confirm in setAktif
old_confirm = "if (confirm(`Anda yakin ingin mengaktifkan Tahun Ajaran ${ta.tahun}? Tahun ajaran lain akan dinonaktifkan.`)) {"
new_confirm = """const result = await useSwal().fire({
        title: 'Aktifkan Tahun Ajaran?',
        text: `Anda yakin ingin mengaktifkan Tahun Ajaran ${ta.tahun}? Tahun ajaran lain akan dinonaktifkan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Aktifkan',
        cancelButtonText: 'Batal',
        reverseButtons: true
    })
    
    if (result.isConfirmed) {"""

content = content.replace(old_confirm, new_confirm)

# Replace alerts
content = content.replace("alert('Gagal menyimpan tahun ajaran. Periksa kembali form Anda.')", "displayToast('Gagal menyimpan tahun ajaran. Periksa kembali form Anda.', 'error')")
content = content.replace("alert(error.response._data.message)", "displayToast(error.response._data.message, 'error')")
content = content.replace("alert('Gagal menghapus data tahun ajaran.')", "displayToast('Gagal menghapus data tahun ajaran.', 'error')")

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated tahun-ajaran.vue")
