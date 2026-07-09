const fs = require('fs');
const path = require('path');

const dir = 'd:/koding/Erapor2026/erapor-fe/app/pages/admin';

function processDirectory(directory) {
    const files = fs.readdirSync(directory);
    for (const file of files) {
        const fullPath = path.join(directory, file);
        const stat = fs.statSync(fullPath);
        if (stat.isDirectory()) {
            processDirectory(fullPath);
        } else if (fullPath.endsWith('.vue')) {
            let content = fs.readFileSync(fullPath, 'utf8');
            
            // Regex to find:
            // <div class="p-6 bg-gradient-to-r from-slate-900 to-slate-800 ...">
            //   <span class="text-3xl">Emoji</span>
            const regex = /(<div class=\"[^\"]*bg-gradient-to-r from-slate-900 to-slate-800[^\"]*\"[^>]*>\s*)<span class=\"text-3xl(?:\s+[^\"]*)?\">([^<]+)<\/span>/g;
            
            const newContent = content.replace(regex, '$1<div class="w-12 h-12 flex items-center justify-center text-3xl shrink-0">$2</div>');
            
            if (content !== newContent) {
                fs.writeFileSync(fullPath, newContent);
                console.log('Updated', fullPath);
            }
        }
    }
}

processDirectory(dir);
console.log('Done');
