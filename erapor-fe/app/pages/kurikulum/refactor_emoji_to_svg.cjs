const fs = require('fs');
const path = require('path');

const baseDir = path.join(__dirname);

function getVueFiles(dir, fileList = []) {
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const filePath = path.join(dir, file);
        if (fs.statSync(filePath).isDirectory()) {
            getVueFiles(filePath, fileList);
        } else if (filePath.endsWith('.vue')) {
            fileList.push(filePath);
        }
    }
    return fileList;
}

const emojiMap = {
    '👩‍🏫': 'users',
    '🔄': 'arrow-path',
    '📭': 'inbox',
    '🏫': 'building-office',
    '⏳': 'clock',
    '💾': 'document-check',
    '➕': 'plus',
    '📅': 'calendar',
    '✏️': 'pencil-square',
    '🗑️': 'trash',
    '⚠️': 'exclamation-triangle',
    '📝': 'document-text',
    '📋': 'clipboard-document-list',
    '📚': 'book-open',
    '👆': 'hand-raised',
    '↻': 'arrow-path',
    '🏜️': 'archive-box-x-mark',
    '📁': 'folder',
    '💼': 'briefcase',
    '⚙️': 'cog-6-tooth',
    '🛠️': 'wrench',
    '👤': 'user',
    '🛡️': 'shield-check',
    '💯': 'star',
    '🏃': 'user-plus',
    '✔️': 'check',
    '⛔': 'no-symbol',
    '👋': 'hand-raised',
    '⚡': 'bolt',
    '📌': 'pin',
    '⚖️': 'scale',
    '🚫': 'no-symbol',
    '✅': 'check-circle',
    '💡': 'light-bulb',
    '👈': 'hand-point-left',
    '👇': 'chevron-down',
    '✉️': 'envelope',
    '🔑': 'key',
    '👨‍🏫': 'users',
    '📑': 'document-text',
    'ℹ️': 'information-circle',
    '✨': 'sparkles',
    '✓': 'check',
    '🟢': 'check-circle'
};

const vueFiles = getVueFiles(baseDir);
let changedFiles = 0;

for (const filePath of vueFiles) {
    let originalContent = fs.readFileSync(filePath, 'utf8');
    let lines = originalContent.split('\n');
    let newLines = [];

    for (let line of lines) {
        // 0. Skip <option> tags to prevent breaking native HTML selects
        if (line.includes('<option')) {
            newLines.push(line);
            continue;
        }

        let modifiedLine = line;

        // 1. Fix mobileTabs icon rendering
        const tabSpanRegex = /<span\s+class="text-lg\s+mb-0\.5\s+transition-transform"\s+:class="([^"]+)"\s*>\s*\{\{\s*tab\.icon\s*\}\}\s*<\/span>/;
        if (tabSpanRegex.test(modifiedLine)) {
            modifiedLine = modifiedLine.replace(tabSpanRegex, '<AppIcon :name="tab.icon" class="w-6 h-6 mb-0.5 transition-transform" :class="$1" />');
        }

        // 2. Fix Edit/Add ternary (e.g. {{ isEdit ? '💾' : '➕' }})
        const ternaryRegex = /\{\{\s*isEdit\s*\?\s*'💾'\s*:\s*'➕'\s*\}\}/g;
        if (ternaryRegex.test(modifiedLine)) {
            modifiedLine = modifiedLine.replace(ternaryRegex, '<AppIcon :name="isEdit ? \'document-check\' : \'plus\'" class="w-5 h-5" />');
        }

        // 3. Fix Edit button (✏️)
        const editBtnRegex = />\s*✏️\s*<\/button>/g;
        if (editBtnRegex.test(modifiedLine)) {
            modifiedLine = modifiedLine.replace(editBtnRegex, '><AppIcon name="pencil-square" class="w-4 h-4" /></button>');
        }

        // 4. Fix Delete button (🗑️)
        const deleteBtnRegex = />\s*🗑️\s*<\/button>/g;
        if (deleteBtnRegex.test(modifiedLine)) {
            modifiedLine = modifiedLine.replace(deleteBtnRegex, '><AppIcon name="trash" class="w-4 h-4" /></button>');
        }

        // 5. Fix JS array values in mobileTabs (e.g. icon: '💯')
        for (const [emoji, iconName] of Object.entries(emojiMap)) {
            const jsRegex = new RegExp(`icon:\\s*'${emoji}'`, 'g');
            if (jsRegex.test(modifiedLine)) {
                modifiedLine = modifiedLine.replace(jsRegex, `icon: '${iconName}'`);
            }
        }

        // 6. Generic replacement for standalone and inline emojis in HTML
        // We only do this if the line does NOT contain 'icon:' or 'text-' properties inside JS
        if (!modifiedLine.includes('icon:') && !modifiedLine.includes("('") && !modifiedLine.includes("')")) {
            for (const [emoji, iconName] of Object.entries(emojiMap)) {
                // Check if the line has the emoji, and it's NOT inside quotes
                if (modifiedLine.includes(emoji) && !modifiedLine.includes(`'${emoji}'`) && !modifiedLine.includes(`"${emoji}"`)) {
                    // Replace it inline with a standard size (w-5 h-5 inline-block)
                    // If it's already inside a flex container or large text, it will inherit, but we give it a default sizing.
                    modifiedLine = modifiedLine.replace(new RegExp(emoji, 'g'), `<AppIcon name="${iconName}" class="w-5 h-5 inline-block mr-1" />`);
                }
            }
        }

        // 7. Generic replacement for "Memuat data..." loading spinner emoji (↻)
        const loadingRegex = />\s*↻\s*<\/span>/g;
        if (loadingRegex.test(modifiedLine)) {
            modifiedLine = modifiedLine.replace(loadingRegex, '><AppIcon name="arrow-path" class="w-6 h-6" /></span>');
        }

        newLines.push(modifiedLine);
    }

    let finalContent = newLines.join('\n');
    if (finalContent !== originalContent) {
        fs.writeFileSync(filePath, finalContent, 'utf8');
        console.log(`Updated emojis to SVGs: ${path.relative(baseDir, filePath)}`);
        changedFiles++;
    }
}

console.log(`\nEmoji migration complete! Updated ${changedFiles} files.`);
