const fs = require('fs');
const path = require('path');

const targetDirs = [
    'd:/koding/Erapor2026/erapor-fe/app/pages/kurikulum',
    'd:/koding/Erapor2026/erapor-fe/app/pages/kurikulum/deskripsi'
];

function processFile(filePath) {
    if (!filePath.endsWith('.vue')) return;
    let content = fs.readFileSync(filePath, 'utf8');
    let originalContent = content;

    // 1. Replace Dock Header
    const dockHeaderRegex = /<div class="p-4 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-100 (flex[^"]*)"\s*>\s*<div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0">([^<]+)<\/div>\s*<div>\s*<h3 class="text-sm font-black uppercase tracking-widest text-white">([^<]+)<\/h3>\s*<p class="text-\[10px\] text-(?:indigo|emerald|slate|teal)-400 font-semibold uppercase mt-0\.5">([^<]+)<\/p>\s*<\/div>\s*<\/div>/g;
    
    content = content.replace(dockHeaderRegex, (match, flexClasses, icon, title, subtitle) => {
        return `<div class="p-6 shrink-0 z-10 relative">
          <div class="bg-gradient-to-r from-indigo-600 to-blue-700 rounded-2xl p-5 border border-indigo-500 shadow-sm relative overflow-hidden flex items-center gap-4">
            <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0 relative z-10">${icon}</div>
            <div class="relative z-10">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">${title}</h3>
                <p class="text-[10px] text-indigo-100 font-semibold uppercase mt-0.5">${subtitle}</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-20 text-white">
              <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path></svg>
            </div>
          </div>
        </div>`;
    });

    // Handle dashboard dock header
    const dashDockHeaderRegex = /<div class="p-6 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-100 flex items-center gap-4 shrink-0">\s*<div class="w-12 h-12 flex items-center justify-center text-3xl shrink-0">([^<]+)<\/div>\s*<div>\s*<h3 class="text-sm font-black uppercase tracking-widest text-white">([^<]+)<\/h3>\s*<p class="text-\[10px\] text-(?:indigo|emerald|slate|teal)-400 font-semibold uppercase mt-0\.5">([^<]+)<\/p>\s*<\/div>\s*<\/div>/g;
    content = content.replace(dashDockHeaderRegex, (match, icon, title, subtitle) => {
        return `<div class="p-6 shrink-0 z-10 relative">
          <div class="bg-gradient-to-r from-indigo-600 to-blue-700 rounded-2xl p-5 border border-indigo-500 shadow-sm relative overflow-hidden flex items-center gap-4">
            <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0 relative z-10">${icon}</div>
            <div class="relative z-10">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">${title}</h3>
                <p class="text-[10px] text-indigo-100 font-semibold uppercase mt-0.5">${subtitle}</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-20 text-white">
              <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path></svg>
            </div>
          </div>
        </div>`;
    });


    // 2. Wrap flow panel
    // Pattern: <!-- Panel Flow Kanan -->\n      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0', ...
    const flowPanelRegex = /(<!-- Panel Flow Kanan.*?\n\s*<div[^>]*min-w-0[^>]*>)\n\s*<!-- Header Flow -->/g;
    
    if (content.match(flowPanelRegex)) {
        content = content.replace(flowPanelRegex, `$1
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            <!-- Header Flow -->`);
            
        // We also need to add the closing divs
        // Typically it ends with:
        //       </div>
        //     </div>
        //   </div>
        // </template>
        
        // Find the end of the flow panel before modals. Usually the last </div></div></div> before <!-- Modal
        const modalRegex = /<!-- (?:Modal|MODAL)/;
        const modalMatch = content.match(modalRegex);
        if (modalMatch) {
            // Insert 2 closing divs before the modal, but we must find the layout container ends.
            // Let's do this by string replacement near the end of the layout.
            // Usually there are 3 closing divs before the modal.
            const endLayoutRegex = /(\s*<\/div>\s*<\/div>\s*<\/div>\s*)(?:<!-- Modal|<!-- MODAL)/;
            content = content.replace(endLayoutRegex, `
              </div>
            </div>$1`);
        } else {
            const endTemplateRegex = /(\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/template>)/;
            content = content.replace(endTemplateRegex, `
              </div>
            </div>$1`);
        }
    }

    // 3. Update Flow Header to be sticky
    const headerFlowRegex = /<div class="p-4 bg-white border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shrink-0 z-10 shadow-sm">/g;
    content = content.replace(headerFlowRegex, `<div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shrink-0 z-10 sticky top-0 bg-white/80 backdrop-blur-xl">`);

    const headerFlowRegex2 = /<div class="p-6 bg-white border-b border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shrink-0 z-10">/g;
    content = content.replace(headerFlowRegex2, `<div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shrink-0 z-10 sticky top-0 bg-white/80 backdrop-blur-xl">`);


    if (content !== originalContent) {
        fs.writeFileSync(filePath, content, 'utf8');
        console.log('Updated:', filePath);
    }
}

targetDirs.forEach(dir => {
    if (fs.existsSync(dir)) {
        const files = fs.readdirSync(dir);
        files.forEach(file => {
            const fullPath = path.join(dir, file);
            if (fs.statSync(fullPath).isFile()) {
                processFile(fullPath);
            }
        });
    }
});

console.log('Script finished.');
