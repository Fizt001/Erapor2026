import os
import re

file_path = r'd:\koding\Erapor2026\erapor-fe\app\pages\admin\buku-induk.vue'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Remove the "Kelas" filter dropdown from the template
kelas_dropdown = """                    <div>
                        <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kelas</label>
                        <select v-model="selectedKelasId" @change="loadStudents" class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-200/70 rounded-2xl focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-bold text-slate-700 outline-none cursor-pointer appearance-none">
                            <option value="">-- Pilih Kelas --</option>
                            <option v-for="kelas in filteredKelasList" :key="kelas.id" :value="kelas.id">{{ kelas.tingkat }} {{ kelas.nama_kelas }}</option>
                        </select>
                    </div>"""
content = content.replace(kelas_dropdown, "")

# 2. Update the "Pilih Kelas" empty state text
content = content.replace(
    '<h3 class="text-lg font-black text-slate-700 mb-2 uppercase tracking-wide">Pilih Kelas</h3>',
    '<h3 class="text-lg font-black text-slate-700 mb-2 uppercase tracking-wide">Pilih Filter</h3>'
)
content = content.replace(
    '<p class="text-slate-500 text-xs font-bold">Silakan pilih tahun ajaran, kurikulum, dan kelas terlebih dahulu.</p>',
    '<p class="text-slate-500 text-xs font-bold">Silakan pilih tahun ajaran dan kurikulum terlebih dahulu.</p>'
)

# 3. Update the condition for showing the empty state
content = content.replace(
    'v-if="!selectedKelasId || students.length === 0"',
    'v-if="(!selectedTahunAjaranId || !selectedKurikulumId) || students.length === 0"'
)

# 4. Modify currentKelas computed property
old_current_kelas = """const currentKelas = computed(() => {
    return kelasList.value.find((k) => k.id === selectedKelasId.value)
})"""
new_current_kelas = """const currentKelas = computed(() => {
    if (!currentStudent.value) return null
    return kelasList.value.find((k) => k.id === currentStudent.value.kelas_id)
})"""
content = content.replace(old_current_kelas, new_current_kelas)

# 5. Modify loadStudents function
old_load_students = """const loadStudents = async () => {
    if (!selectedKelasId.value) {
        students.value = []
        return
    }
    try {
        // Fetch data siswa
        const res = await $fetch(`http://localhost:8000/api/admin/buku-induk/${selectedKelasId.value}/biodata`, {
            headers: { Authorization: `Bearer ${token.value}` }
        })
        
        students.value = res.data || res || []
        currentStudentIndex.value = 0
        
        // Load mapel struktur untuk kelas ini
        const resMapel = await $fetch(`http://localhost:8000/api/admin/buku-induk/${selectedKelasId.value}/mapel-struktur`, {
            headers: { Authorization: `Bearer ${token.value}` }
        })
        if (resMapel) {
            mapelStruktur.value = resMapel.data || resMapel || []
        }
        
        if (!isDesktop.value && students.value.length > 0) activeTab.value = 'preview'
        
    } catch (e) {
        console.error('Failed loading students', e)
        students.value = []
        mapelStruktur.value = []
    }
}"""
new_load_students = """const loadStudents = async () => {
    if (!selectedTahunAjaranId.value || !selectedKurikulumId.value) {
        students.value = []
        return
    }
    try {
        // Fetch data siswa
        const res = await $fetch(`http://localhost:8000/api/admin/buku-induk/biodata?tahun_ajaran_id=${selectedTahunAjaranId.value}&kurikulum_id=${selectedKurikulumId.value}`, {
            headers: { Authorization: `Bearer ${token.value}` }
        })
        
        students.value = res.data || res || []
        currentStudentIndex.value = 0
        
        if (!isDesktop.value && students.value.length > 0) activeTab.value = 'preview'
        
    } catch (e) {
        console.error('Failed loading students', e)
        students.value = []
    }
}

watch([selectedTahunAjaranId, selectedKurikulumId], () => {
    loadStudents()
})
"""
content = content.replace(old_load_students, new_load_students)

# 6. Modify watch(currentStudent)
old_watch_current_student = """watch(currentStudent, () => {
    if (currentStudent.value) {
        fetchNilaiSiswa()
    } else {
        nilaiSiswa.value = {}
    }
})"""

new_watch_current_student = """watch(currentStudent, async () => {
    if (currentStudent.value) {
        fetchNilaiSiswa()
        
        // Load mapel struktur for this student's class
        try {
            const resMapel = await $fetch(`http://localhost:8000/api/admin/buku-induk/${currentStudent.value.kelas_id}/mapel-struktur`, {
                headers: { Authorization: `Bearer ${token.value}` }
            })
            if (resMapel) {
                mapelStruktur.value = resMapel.data || resMapel || []
            }
        } catch (e) {
            console.error('Failed loading mapel struktur', e)
            mapelStruktur.value = []
        }
    } else {
        nilaiSiswa.value = {}
        mapelStruktur.value = []
    }
})"""
content = content.replace(old_watch_current_student, new_watch_current_student)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Vue component updated!")
