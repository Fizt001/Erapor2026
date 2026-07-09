const fs = require('fs');
const path = require('path');

const dir = 'd:/koding/Erapor2026/erapor-fe/app/pages/bk';
const files = fs.readdirSync(dir).filter(f => f.endsWith('.vue'));

files.forEach(file => {
    let filePath = path.join(dir, file);
    let content = fs.readFileSync(filePath, 'utf8');

    let modified = false;

    // Fix dock headers
    const headerRegex = /<div class="bg-gradient-to-[a-z]+ from-[a-z]+-600 to-[a-z]+-700 rounded-2xl p-5 border border-[a-z]+-500 shadow-sm relative overflow-hidden flex items-center gap-4">/g;
    if (content.match(headerRegex)) {
        content = content.replace(headerRegex, '<div class="bg-gradient-to-r from-rose-600 to-rose-700 rounded-2xl p-5 border border-rose-500 shadow-sm relative overflow-hidden flex items-center gap-4">');
        modified = true;
    }

    // Fix subtitle texts (e.g., text-teal-100, text-orange-100) inside the header
    // Actually, we can just replace text-[a-z]+-100 with text-rose-100 in the line immediately following the header
    // Let's use a more robust regex for the subtitle line: <p class="text-[10px] text-teal-100 font-semibold uppercase mt-0.5">
    const subtitleRegex = /<p class="text-\[10px\] text-[a-z]+-100 font-semibold uppercase mt-0\.5">/g;
    if (content.match(subtitleRegex)) {
        content = content.replace(subtitleRegex, '<p class="text-[10px] text-rose-100 font-semibold uppercase mt-0.5">');
        modified = true;
    }

    // Fix quick access in dashboard
    if (file === 'dashboard.vue') {
        const qaRegex = /<!-- Quick Actions -->\s*<div class="bg-slate-900 p-5 rounded-2xl shadow-sm text-white relative overflow-hidden">/g;
        if (content.match(qaRegex)) {
            content = content.replace(qaRegex, `<!-- Quick Actions -->\n          <div class="bg-gradient-to-r from-rose-600 to-rose-700 p-5 rounded-2xl shadow-sm text-white relative overflow-hidden border border-rose-500">`);
            modified = true;
        }

        const qhRegex = /<h3 class="text-xs font-bold uppercase tracking-widest mb-3 text-slate-300 flex items-center">⚡ Akses Cepat<\/h3>/;
        if (content.match(qhRegex)) {
            content = content.replace(qhRegex, `<h3 class="text-xs font-bold uppercase tracking-widest mb-3 text-rose-100 flex items-center">⚡ Akses Cepat</h3>`);
            modified = true;
        }

        const qlRegex = /class="block bg-slate-800 hover:bg-rose-600/g;
        if (content.match(qlRegex)) {
            content = content.replace(qlRegex, `class="block bg-rose-900/40 border border-rose-500/50 hover:bg-rose-900/80`);
            modified = true;
        }
        
        const qpRegex = /<div class="absolute -right-4 -bottom-4 text-slate-800 opacity-50 text-6xl font-black">/g;
        if (content.match(qpRegex)) {
            content = content.replace(qpRegex, `<div class="absolute -right-4 -bottom-4 text-rose-800 opacity-50 text-6xl font-black">`);
            modified = true;
        }
    }

    if (modified) {
        fs.writeFileSync(filePath, content, 'utf8');
        console.log('Modified', file);
    }
});
