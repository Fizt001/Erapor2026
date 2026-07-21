const fs = require('fs');
const files = ['bk.vue', 'kurikulum.vue', 'siswa.vue', 'superadmin.vue'];

files.forEach(f => {
  let content = fs.readFileSync('app/layouts/' + f, 'utf8');
  
  // 1. Add VisiMisiDialog
  content = content.replace(
    '<div class="min-h-screen bg-slate-50 flex text-slate-800 text-sm print:bg-white print:block print:min-h-0">\n    <!-- Sidebar',
    '<div class="min-h-screen bg-slate-50 flex text-slate-800 text-sm print:bg-white print:block print:min-h-0">\n    <VisiMisiDialog ref="visiMisiDialog" />\n    <!-- Sidebar'
  );
  content = content.replace(
    '<div class="min-h-screen bg-slate-50 flex text-slate-800 text-sm print:bg-white print:block print:min-h-0">\r\n    <!-- Sidebar',
    '<div class="min-h-screen bg-slate-50 flex text-slate-800 text-sm print:bg-white print:block print:min-h-0">\r\n    <VisiMisiDialog ref="visiMisiDialog" />\r\n    <!-- Sidebar'
  );

  // 2. Desktop logo image
  content = content.replace(
    'class="h-8 w-8 object-contain lg:mr-3 shrink-0" />',
    'class="h-8 w-8 object-contain lg:mr-3 shrink-0 cursor-pointer hover:scale-110 transition-transform" @click="visiMisiDialog?.open()" />'
  );
  content = content.replace(
    'class="h-8 w-8 object-contain mr-2" />', // in bk.vue
    'class="h-8 w-8 object-contain mr-2 cursor-pointer hover:scale-110 transition-transform" @click="visiMisiDialog?.open()" />'
  );

  // 3. Desktop logo text fallback
  content = content.replace(
    /<span v-else class="(.*?text-xl.*?)">e<\/span>/,
    '<span v-else class="$1 cursor-pointer hover:scale-110 transition-transform" @click="visiMisiDialog?.open()">e</span>'
  );

  // 4. Desktop e-rapor text (ONLY THE ONE RIGHT AFTER THE LOGO)
  content = content.replace(
    /<\/span>\s*<span class="opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300(.*?)">\s*<span v-if="sekolah\?\.logo"/,
    '</span>\n        <span class="opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300$1 cursor-pointer" @click="visiMisiDialog?.open()">\n          <span v-if="sekolah?.logo"'
  );
  content = content.replace( // For superadmin and others with different spacing
    /<\/span>\r\n\s*<span class="opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300(.*?)">\r\n\s*<span v-if="sekolah\?\.logo"/,
    '</span>\r\n        <span class="opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300$1 cursor-pointer" @click="visiMisiDialog?.open()">\r\n          <span v-if="sekolah?.logo"'
  );
  
  // Also handle bk.vue which is inline
  content = content.replace(
    /<\/span><span class="opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300">-Rapor<\/span>/,
    '</span><span class="opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity duration-300 cursor-pointer" @click="visiMisiDialog?.open()">-Rapor</span>'
  );

  // 5. Mobile logo container
  content = content.replace(
    /<div class="lg:hidden flex items-center gap-2">/, // only replace the first occurrence
    '<div class="lg:hidden flex items-center gap-2 cursor-pointer hover:scale-105 transition-transform" @click="visiMisiDialog?.open()">'
  );

  // 6. Script setup ref
  content = content.replace(
    /const route = useRoute\(\)\n/,
    'const route = useRoute()\nconst visiMisiDialog = ref(null)\n'
  );
  content = content.replace(
    /const route = useRoute\(\)\r\n/,
    'const route = useRoute()\r\nconst visiMisiDialog = ref(null)\r\n'
  );

  fs.writeFileSync('app/layouts/' + f, content);
  console.log('Patched ' + f);
});
