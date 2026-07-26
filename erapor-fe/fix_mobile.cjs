const fs = require('fs');
const path = require('path');

const walk = (dir, callback) => {
    fs.readdirSync(dir).forEach(f => {
        const dirPath = path.join(dir, f);
        const isDirectory = fs.statSync(dirPath).isDirectory();
        isDirectory ? walk(dirPath, callback) : callback(path.join(dir, f));
    });
};

const dirs = [
    path.join(__dirname, 'app', 'pages', 'guru'),
    path.join(__dirname, 'app', 'pages', 'walas')
];

let filesProcessed = 0;

dirs.forEach(dir => {
    if (!fs.existsSync(dir)) return;
    walk(dir, (filePath) => {
        if (!filePath.endsWith('.vue')) return;
        
        let content = fs.readFileSync(filePath, 'utf-8');
        
        // Skip if already has MOBILE VIEW TABS
        if (content.includes('MOBILE VIEW TABS')) return;
        
        // Check if it has the dock/flow layout
        if (!content.includes('Panel Dock Kiri') || !content.includes('Panel Flow Kanan')) return;
        
        let modified = false;

        // 1. Inject the MOBILE VIEW TABS template
        const templateSearch = `<div class="flex-1 flex flex-col xl:flex-row overflow-hidden relative">`;
        if (content.includes(templateSearch)) {
            const tabsHtml = `
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-2 grid grid-cols-2 gap-2 z-20 shadow-sm">
        <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTabMobile = tab.id"
          :class="activeTabMobile === tab.id ? 'bg-gradient-to-br from-sky-500 to-sky-600 text-white shadow-md shadow-sky-500/20 ring-2 ring-sky-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="rounded-xl flex items-center justify-center py-2 px-1 transition-all active:scale-95">
          <AppIcon :name="tab.icon" class="text-lg mr-1.5 transition-transform" :class="activeTabMobile === tab.id ? 'scale-110' : ''" />
          <span class="text-[10px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
        </button>
      </div>
`;
            content = content.replace(templateSearch, templateSearch + '\n      ' + tabsHtml);
            modified = true;
        }

        // 2. Modify Panel Dock Kiri class
        const genericDockSearch = /<!-- Panel Dock Kiri -->\s+<div class="xl:w-\[360px\] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-\[2px_0_10px_-4px_rgba\(0,0,0,0\.05\)\] overflow-y-auto custom-scrollbar">/g;
        if (content.match(genericDockSearch)) {
            content = content.replace(genericDockSearch, `<!-- Panel Dock Kiri -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] overflow-y-auto custom-scrollbar transition-all', activeTabMobile === 'filter' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[60px]' : '']">`);
        }

        // 3. Modify Panel Flow Kanan class
        const genericFlowSearch = /<!-- Panel Flow Kanan -->\s+<div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative">/g;
        if (content.match(genericFlowSearch)) {
             content = content.replace(genericFlowSearch, `<!-- Panel Flow Kanan -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative transition-all', activeTabMobile === 'flow' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[60px]' : '']">`);
        }

        // 4. Inject script logic
        // Inject Vue imports if needed
        if (content.includes('<script setup>')) {
            if (!content.includes('onMounted } from \'vue\'')) {
                 if (content.includes('import { ref, computed } from \'vue\'')) {
                     content = content.replace('import { ref, computed } from \'vue\'', 'import { ref, computed, onMounted } from \'vue\'');
                 } else if (content.includes('import { ref } from \'vue\'')) {
                     content = content.replace('import { ref } from \'vue\'', 'import { ref, computed, onMounted } from \'vue\'');
                 } else {
                     content = content.replace('<script setup>', '<script setup>\nimport { ref, computed, onMounted } from \'vue\'');
                 }
            }
        }

        const scriptInjections = `
const windowWidth = ref(1024)
const isDesktop = computed(() => windowWidth.value >= 1280)
const activeTabMobile = ref('filter')
const mobileTabs = [
  { id: 'filter', title: 'Filter / Form', icon: 'funnel' },
  { id: 'flow', title: 'Data Workspace', icon: 'table-cells' }
]

onMounted(() => {
    if (typeof window !== 'undefined') {
        windowWidth.value = window.innerWidth
        window.addEventListener('resize', () => {
            windowWidth.value = window.innerWidth
            if (isDesktop.value) activeTabMobile.value = 'filter'
        })
    }
})`;
        
        if (content.includes('onMounted(() => {')) {
           // We have an existing block onMounted, inject the code inside it
           content = content.replace('onMounted(() => {', 'onMounted(() => {\n    if (typeof window !== \'undefined\') {\n        windowWidth.value = window.innerWidth\n        window.addEventListener(\'resize\', () => {\n            windowWidth.value = window.innerWidth\n            if (isDesktop.value) activeTabMobile.value = \'filter\'\n        })\n    }');
           
           if (!content.includes('const windowWidth = ref(1024)')) {
               content = content.replace('const isLoading = ref(true)', `const windowWidth = ref(1024)
const isDesktop = computed(() => windowWidth.value >= 1280)
const activeTabMobile = ref('filter')
const mobileTabs = [
  { id: 'filter', title: 'Filter / Form', icon: 'funnel' },
  { id: 'flow', title: 'Data Workspace', icon: 'table-cells' }
]
const isLoading = ref(true)`);
           }
        } else if (content.includes('onMounted(() => fetchData())')) {
           content = content.replace('onMounted(() => fetchData())', scriptInjections + '\n\nonMounted(() => fetchData())');
        } else {
           const metaSearch = '})\n';
           if (content.includes(metaSearch)) {
               const pos = content.indexOf(metaSearch) + metaSearch.length;
               content = content.slice(0, pos) + scriptInjections + '\n' + content.slice(pos);
           }
        }

        if (modified) {
            fs.writeFileSync(filePath, content, 'utf-8');
            filesProcessed++;
            console.log('Modified:', filePath);
        }
    });
});

console.log('Processed', filesProcessed, 'files');
