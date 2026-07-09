const fs = require('fs');
const path = require('path');

const dir = 'd:/koding/Erapor2026/erapor-fe/app/pages/guru/walas';
const files = fs.readdirSync(dir).filter(f => f.endsWith('.vue'));

files.forEach(file => {
    let filePath = path.join(dir, file);
    let content = fs.readFileSync(filePath, 'utf8');
    let modified = false;

    // Replace the dark background with Sky/Blue
    const bgRegex = /bg-gradient-to-r from-slate-900 to-slate-800 rounded-2xl p-5 border border-slate-700/g;
    if (content.match(bgRegex)) {
        content = content.replace(bgRegex, 'bg-gradient-to-r from-sky-600 to-blue-700 rounded-2xl p-5 border border-sky-500');
        modified = true;
    }

    // Replace text-slate-400 or text-[a-z]+-100 with text-sky-100 in the subtitle
    // Usually it's: <p class="text-[10px] text-slate-400 font-semibold uppercase mt-0.5">
    const subRegex = /<p class="text-\[10px\] text-(?:slate-400|slate-300|[a-z]+-100) font-semibold uppercase mt-0\.5">/g;
    if (content.match(subRegex)) {
        content = content.replace(subRegex, '<p class="text-[10px] text-sky-100 font-semibold uppercase mt-0.5">');
        modified = true;
    }

    if (modified) {
        fs.writeFileSync(filePath, content, 'utf8');
        console.log('Fixed theme in', file);
    }
});
