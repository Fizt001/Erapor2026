const fs = require('fs');
const path = require('path');

const filesToFix = [
    'd:/koding/Erapor2026/erapor-fe/app/pages/kurikulum/ekskul.vue',
    'd:/koding/Erapor2026/erapor-fe/app/pages/kurikulum/kkm.vue',
    'd:/koding/Erapor2026/erapor-fe/app/pages/kurikulum/mapel.vue',
    'd:/koding/Erapor2026/erapor-fe/app/pages/kurikulum/struktur-kejuruan.vue',
    'd:/koding/Erapor2026/erapor-fe/app/pages/kurikulum/struktur-umum.vue',
    'd:/koding/Erapor2026/erapor-fe/app/pages/kurikulum/wali-kelas.vue'
];

filesToFix.forEach(filePath => {
    let content = fs.readFileSync(filePath, 'utf8');

    // Using [\s\S]*? to correctly bridge across any characters between comment, the div, and the next comment
    const flowStartRegex = /(<!-- Panel Flow Kanan[\s\S]*?<div[^>]*min-w-0[^>]*>)([\s\S]*?)<!-- Header Flow -->/m;
    
    if (content.match(flowStartRegex)) {
        content = content.replace(flowStartRegex, `$1
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
        <!-- Header Flow -->`);

        const endLayoutRegex = /(\s*<\/div>\s*<\/div>\s*<\/div>\s*)(?:<!-- Modal|<!-- MODAL)/;
        if (content.match(endLayoutRegex)) {
            content = content.replace(endLayoutRegex, `
              </div>
            </div>$1`);
        } else {
            const endTemplateRegex = /(\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/template>)/;
            content = content.replace(endTemplateRegex, `
              </div>
            </div>$1`);
        }

        fs.writeFileSync(filePath, content, 'utf8');
        console.log('Fixed', filePath);
    } else {
        console.log('Could not match flow panel in', filePath);
    }
});
