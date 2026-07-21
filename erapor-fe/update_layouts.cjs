const fs = require('fs');
const path = require('path');
const layouts = ['guru.vue', 'bk.vue', 'kurikulum.vue', 'siswa.vue'];
const dir = 'd:/koding/Erapor2026/erapor-fe/app/layouts';

layouts.forEach(file => {
  const filePath = path.join(dir, file);
  if (!fs.existsSync(filePath)) return;
  
  let content = fs.readFileSync(filePath, 'utf8');
  
  // Replace the logo part
  content = content.replace(
    /<div class="h-14 flex items-center px-4 bg-slate-950 font-bold text-base tracking-wider border-b border-slate-800 whitespace-nowrap overflow-hidden">([\s\S]*?)<\/div>/,
    `<div class="h-14 flex items-center px-4 bg-slate-950 font-bold text-base tracking-wider border-b border-slate-800 whitespace-nowrap overflow-hidden">
        <img v-if="sekolah?.logo" :src="\`https://api.erapor.yatindosystem.net/\${sekolah.logo}\`" alt="Logo" class="h-8 w-8 object-contain mr-2" />
        <span v-else class="text-emerald-500 mr-2 ml-1 text-xl">e</span><span class="opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300">-Rapor</span>
      </div>`
  );
  
  // Add script imports
  if (!content.includes('useSekolah()')) {
    content = content.replace(
      /const route = useRoute\(\)\s+const sidebarOpen = ref\(false\)/,
      `const route = useRoute()
const sidebarOpen = ref(false)

const { sekolah, fetchSekolah } = useSekolah()

onMounted(() => {
  fetchSekolah()
})`
    );
  }
  
  fs.writeFileSync(filePath, content);
  console.log('Updated ' + file);
});
