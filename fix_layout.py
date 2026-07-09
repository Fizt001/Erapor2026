import os
import re

files = [
    r'd:\koding\Erapor2026\erapor-fe\app\pages\admin\backup.vue',
    r'd:\koding\Erapor2026\erapor-fe\app\pages\admin\buku-induk.vue',
    r'd:\koding\Erapor2026\erapor-fe\app\pages\admin\kejuruan.vue',
    r'd:\koding\Erapor2026\erapor-fe\app\pages\admin\kurikulum.vue',
    r'd:\koding\Erapor2026\erapor-fe\app\pages\admin\master-database.vue',
    r'd:\koding\Erapor2026\erapor-fe\app\pages\admin\sekolah.vue',
    r'd:\koding\Erapor2026\erapor-fe\app\pages\admin\tahun-ajaran.vue',
    r'd:\koding\Erapor2026\erapor-fe\app\pages\admin\users.vue',
    r'd:\koding\Erapor2026\erapor-fe\app\pages\admin\kelas\index.vue',
    r'd:\koding\Erapor2026\erapor-fe\app\pages\admin\kelas\[id]\siswa.vue'
]

for path in files:
    if not os.path.exists(path): continue
    
    with open(path, 'r', encoding='utf-8') as file:
        content = file.read()
    
    content = re.sub(
        r'class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start"',
        r'class="flex flex-col lg:flex-row gap-8 items-start"',
        content
    )
    
    content = re.sub(r'\blg:col-span-4\b', r'w-full lg:w-[360px] shrink-0', content)
    content = re.sub(r'\blg:col-span-8\b', r'flex-1 min-w-0', content)
    content = re.sub(r'\blg:col-span-3\b', r'w-full lg:w-[300px] shrink-0', content)
    content = re.sub(r'\blg:col-span-9\b', r'flex-1 min-w-0', content)
    
    with open(path, 'w', encoding='utf-8') as file:
        file.write(content)
        
    print(f'Processed {path}')
