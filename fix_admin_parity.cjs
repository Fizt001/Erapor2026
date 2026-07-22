const fs = require('fs');
const path = require('path');

const targetDir = path.join(__dirname, 'erapor-fe', 'app', 'pages', 'kurikulum');

function processFile(filePath) {
    let content = fs.readFileSync(filePath, 'utf8');
    let original = content;

    // 1. Flow Container Classes
    content = content.replace(/p-6\s+lg:p-8\s+max-w-7xl/g, 'p-2 sm:p-6 lg:p-8 max-w-5xl');
    content = content.replace(/p-6\s+lg:p-8\s+max-w-5xl/g, 'p-2 sm:p-6 lg:p-8 max-w-5xl');
    
    // Add relative to flex-1 wrapper if missing
    content = content.replace(/class="\['flex-1 bg-slate-50 flex flex-col h-full min-w-0'(?! relative)/g, 'class="[\'flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative\'');

    // 2. Mobile Tabs
    content = content.replace(/class="rounded-xl\s+flex\s+flex-col\s+items-center\s+justify-center\s+py-2\s+px-1\s+transition-all\s+active:scale-95"/g, 'class="rounded-xl flex items-center justify-center py-2 px-1 transition-all active:scale-95"');
    
    content = content.replace(/<AppIcon\s+:name="tab\.icon"\s+class="w-6\s+h-6\s+mb-0\.5\s+transition-transform"\s+:class="activeTab\s+===\s+tab\.id\s+\?\s+'scale-110'\s+:\s+''"\s*\/>/g, 
      '<span class="text-lg mr-1.5 transition-transform" :class="activeTab === tab.id ? \'scale-110\' : \'\'"><AppIcon :name="tab.icon" /></span>');
    
    content = content.replace(/<span\s+class="text-\[9px\]\s+font-black\s+uppercase\s+tracking-wider\s+text-center\s+leading-none">{{ tab\.title }}<\/span>/g, 
      '<span class="text-[10px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>');

    // 3. Header Flow
    content = content.replace(/sticky\s+top-0\s+bg-white\/80\s+backdrop-blur-xl/g, 'bg-white');

    // 4. Action Icons
    content = content.replace(/<AppIcon\s+name="pencil-square"[^>]*\/>/g, '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>');
    
    content = content.replace(/<AppIcon\s+name="trash"[^>]*\/>/g, '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>');
    
    content = content.replace(/<AppIcon\s+:name="[a-zA-Z]+\s+\?\s+'document-check'\s+:\s+'plus'"[^>]*\/>/g, '<AppIcon name="save" />');
    content = content.replace(/<span\s+v-else><AppIcon\s+name="save"\s*\/><\/span>/g, '<span v-else class="text-lg"><AppIcon name="save" /></span>');

    // Fix database tab icon
    content = content.replace(/{\s*id:\s*'table',\s*title:\s*'Database',\s*icon:\s*'clipboard-document-list'\s*}/g, "{ id: 'table', title: 'Database', icon: 'clipboard' }");
    content = content.replace(/{\s*id:\s*'table',\s*title:\s*'Database',\s*icon:\s*'circle-stack'\s*}/g, "{ id: 'table', title: 'Database', icon: 'clipboard' }");
    
    // Add some basic table structure classes if missing (just the thead/tbody)
    content = content.replace(/<thead\s+class="(?!.*hidden sm:table-header-group)([^"]+)"/g, '<thead class="hidden sm:table-header-group $1"');
    content = content.replace(/<tbody\s+class="(?!.*flex flex-col sm:table-row-group)([^"]+)"/g, '<tbody class="flex flex-col sm:table-row-group $1"');

    // If it changed, write back
    if (content !== original) {
        fs.writeFileSync(filePath, content, 'utf8');
        console.log('Updated:', filePath);
    }
}

function traverseDir(dir) {
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const fullPath = path.join(dir, file);
        if (fs.statSync(fullPath).isDirectory()) {
            traverseDir(fullPath);
        } else if (fullPath.endsWith('.vue')) {
            processFile(fullPath);
        }
    }
}

traverseDir(targetDir);
console.log('Refactor script completed.');
