const fs = require('fs');
const path = require('path');

const walasDir = path.join(__dirname, 'erapor-fe', 'app', 'pages', 'guru', 'walas');

// Files to fix
const filesToFix = ['biodata.vue', 'catatan.vue', 'ekskul.vue', 'kokurikuler.vue', 'monitoring.vue', 'naik-kelas.vue', 'p5.vue', 'rekap.vue'];

// The variable declarations to inject
const variableDeclarations = `
const windowWidth = ref(1024)
const isDesktop = computed(() => windowWidth.value >= 1280)
const activeTabMobile = ref('filter')
const mobileTabs = [
  { id: 'filter', title: 'Filter / Form', icon: 'funnel' },
  { id: 'flow', title: 'Data Workspace', icon: 'table-cells' }
]
`;

// The onMounted code for windowWidth
const onMountedWindowCode = `    if (typeof window !== 'undefined') {
        windowWidth.value = window.innerWidth
        window.addEventListener('resize', () => {
            windowWidth.value = window.innerWidth
            if (isDesktop.value) activeTabMobile.value = 'filter'
        })
    }`;

let fixed = 0;

filesToFix.forEach(file => {
    const filePath = path.join(walasDir, file);
    if (!fs.existsSync(filePath)) {
        console.log(`SKIP (not found): ${file}`);
        return;
    }
    
    let content = fs.readFileSync(filePath, 'utf8');
    let modified = false;
    
    // Check if windowWidth is already declared
    if (content.includes('const windowWidth = ref')) {
        console.log(`OK (already has windowWidth): ${file}`);
        // Still might need to fix theme colors
    } else {
        // Find the script setup block and inject declarations after definePageMeta
        const definePageMetaEnd = content.indexOf('definePageMeta({');
        if (definePageMetaEnd !== -1) {
            // Find the end of definePageMeta block
            let depth = 0;
            let i = definePageMetaEnd;
            while (i < content.length) {
                if (content[i] === '{') depth++;
                if (content[i] === '}') {
                    depth--;
                    if (depth === 0) {
                        i++; // Move past the closing brace
                        // Move past trailing ) and newlines
                        while (i < content.length && (content[i] === ')' || content[i] === '\r' || content[i] === '\n')) {
                            i++;
                        }
                        break;
                    }
                }
                i++;
            }
            
            // Inject the variable declarations after definePageMeta
            content = content.slice(0, i) + '\n' + variableDeclarations + content.slice(i);
            modified = true;
            console.log(`FIXED (added windowWidth vars): ${file}`);
        } else {
            console.log(`WARNING: No definePageMeta found in ${file}`);
        }
    }
    
    // Fix the onMounted - remove duplicate windowWidth code inside existing onMounted 
    // (they already have the code but missing the declaration)
    // The files have onMounted with windowWidth in it, let's ensure the inner window code exists
    const onMountedPattern = /onMounted\(\(\) => \{[\s\r\n]+if \(typeof window !== 'undefined'\)/;
    if (!onMountedPattern.test(content)) {
        // Find onMounted and add the window code at start
        const onMountedIdx = content.indexOf('onMounted(() => {');
        if (onMountedIdx !== -1) {
            const insertPos = onMountedIdx + 'onMounted(() => {'.length;
            content = content.slice(0, insertPos) + '\n' + onMountedWindowCode + content.slice(insertPos);
            modified = true;
            console.log(`  + Added window resize listener in onMounted`);
        }
    }
    
    // Fix mobile tab button colors: sky -> amber for walas theme  
    const skyBlueReplacements = [
        // Mobile tab active state
        ['bg-gradient-to-br from-sky-500 to-sky-600 text-white shadow-md shadow-sky-500/20 ring-2 ring-sky-500 ring-offset-1', 
         'bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-md shadow-amber-500/20 ring-2 ring-amber-500 ring-offset-1'],
        // Dock panel header gradient (sky -> amber)
        ['bg-gradient-to-r from-sky-600 to-blue-700 rounded-2xl p-5 border border-sky-500',
         'bg-gradient-to-r from-amber-600 to-orange-700 rounded-2xl p-5 border border-amber-500'],
        ['bg-gradient-to-r from-sky-600 to-blue-700 rounded-2xl p-5 border border-sky-500 shadow-sm relative overflow-hidden',
         'bg-gradient-to-r from-amber-600 to-orange-700 rounded-2xl p-5 border border-amber-500 shadow-sm relative overflow-hidden'],
        // Subtitle text inside header card
        ['text-[10px] text-slate-400 font-semibold mt-0.5',
         'text-[10px] text-amber-100 font-semibold mt-0.5'],
        ['text-[10px] text-sky-100 font-semibold uppercase mt-0.5',
         'text-[10px] text-amber-100 font-semibold uppercase mt-0.5'],
        // focus rings - sky -> amber
        ['focus:ring-sky-500/10 focus:border-sky-500',
         'focus:ring-amber-500/10 focus:border-amber-500'],
        // Indigo references for save buttons -> amber
        ['bg-indigo-600 hover:bg-indigo-700',
         'bg-amber-600 hover:bg-amber-700'],
        // Sky text color in headers
        ['text-sky-600',
         'text-amber-600'],
    ];
    
    skyBlueReplacements.forEach(([from, to]) => {
        if (content.includes(from)) {
            content = content.split(from).join(to);
            modified = true;
        }
    });
    
    if (modified) {
        fs.writeFileSync(filePath, content, 'utf8');
        fixed++;
        console.log(`  -> Saved: ${file}`);
    }
});

console.log(`\nDone! Fixed ${fixed} files.`);
