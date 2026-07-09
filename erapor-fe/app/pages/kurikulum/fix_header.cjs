const fs = require('fs');
const path = require('path');

const files = [
    {
        path: 'd:/koding/Erapor2026/erapor-fe/app/pages/kurikulum/struktur-umum.vue',
        title: 'Struktur Umum',
        subtitle: 'Plotting Mapel Wajib',
        icon: '📑'
    },
    {
        path: 'd:/koding/Erapor2026/erapor-fe/app/pages/kurikulum/struktur-kejuruan.vue',
        title: 'Struktur Kejuruan',
        subtitle: 'Plotting Mapel Kejuruan',
        icon: '💼'
    }
];

files.forEach(fileInfo => {
    let content = fs.readFileSync(fileInfo.path, 'utf8');

    // Remove the old header block completely
    const oldHeaderRegex = /<div class="p-4 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-100 flex flex-col gap-3 shrink-0">[\s\S]*?<\/div>\s*<\/div>\s*<\/div>/;

    const newHeaderBlock = `
        <div class="p-6 shrink-0 z-10 relative">
          <div class="bg-gradient-to-r from-indigo-600 to-blue-700 rounded-2xl p-5 border border-indigo-500 shadow-sm relative overflow-hidden flex flex-col gap-4">
            <div class="flex items-center gap-4 relative z-10">
                <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0">${fileInfo.icon}</div>
                <div>
                    <h3 class="text-sm font-black uppercase tracking-widest text-white">${fileInfo.title}</h3>
                    <p class="text-[10px] text-indigo-200 font-semibold uppercase mt-0.5">${fileInfo.subtitle}</p>
                </div>
            </div>
            
            <div class="absolute right-0 bottom-0 opacity-20 text-white pointer-events-none">
              <svg class="w-24 h-24 transform translate-x-6 translate-y-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path></svg>
            </div>

            <!-- Kurikulum & Tingkat Selector -->
            <div class="space-y-2 relative z-10">
                <select v-model="selectedKurikulumId" @change="fetchData" class="w-full px-3 py-2 rounded-xl bg-indigo-900/40 border border-indigo-500/50 text-sm font-bold text-white focus:ring-2 focus:ring-white/50 outline-none">
                    <option value="" disabled>-- Pilih Kurikulum --</option>
                    <option v-for="kur in kurikulums" :key="kur.id" :value="kur.id" class="text-slate-800">{{ kur.nama_kurikulum }}</option>
                </select>
                <div class="grid grid-cols-3 gap-1">
                    <button @click="tingkat = 'X'" :class="tingkat === 'X' ? 'bg-white text-indigo-700 shadow-sm' : 'bg-indigo-900/40 text-indigo-200 hover:bg-indigo-900/60'" class="py-1.5 rounded-lg font-black text-[10px] uppercase tracking-widest transition-all">Kelas X</button>
                    <button @click="tingkat = 'XI'" :class="tingkat === 'XI' ? 'bg-white text-indigo-700 shadow-sm' : 'bg-indigo-900/40 text-indigo-200 hover:bg-indigo-900/60'" class="py-1.5 rounded-lg font-black text-[10px] uppercase tracking-widest transition-all">Kelas XI</button>
                    <button @click="tingkat = 'XII'" :class="tingkat === 'XII' ? 'bg-white text-indigo-700 shadow-sm' : 'bg-indigo-900/40 text-indigo-200 hover:bg-indigo-900/60'" class="py-1.5 rounded-lg font-black text-[10px] uppercase tracking-widest transition-all">Kelas XII</button>
                </div>
            </div>
          </div>
        </div>
`;

    if (content.match(oldHeaderRegex)) {
        content = content.replace(oldHeaderRegex, newHeaderBlock.trim());
        fs.writeFileSync(fileInfo.path, content, 'utf8');
        console.log('Fixed', fileInfo.path);
    } else {
        console.log('Could not find old header in', fileInfo.path);
    }
});
