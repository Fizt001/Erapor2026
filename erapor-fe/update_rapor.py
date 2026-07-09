import re

filepath = 'app/pages/siswa/rapor.vue'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace('max-w-5xl', 'max-w-7xl')
content = content.replace('emerald', 'indigo')
content = content.replace('#10b981', '#4f46e5')

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated rapor layout.")
