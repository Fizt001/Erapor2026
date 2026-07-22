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

const vueFiles = getVueFiles(baseDir);

let changedFiles = 0;

for (const filePath of vueFiles) {
    let originalContent = fs.readFileSync(filePath, 'utf8');
    let content = originalContent;

    // 1. Remove AppIcon inside <option> tags
    const appIconOptionRegex = /(<option[^>]*>)\s*<AppIcon[^>]+>\s*(?:️\s*)?(.*?<\/option>)/g;
    let tempContent = content.replace(appIconOptionRegex, '$1$2');
    while(tempContent !== content) { // In case there are multiple in one line or something
        content = tempContent;
        tempContent = content.replace(appIconOptionRegex, '$1$2');
    }

    // 2. Replace gradients: to-blue-700 -> to-orange-600
    content = content.replace(/to-blue-700/g, 'to-orange-600');
    content = content.replace(/to-blue-800/g, 'to-orange-700');

    // 3. Replace indigo with amber
    content = content.replace(/-indigo-([0-9]+)/g, '-amber-$1');

    if (content !== originalContent) {
        fs.writeFileSync(filePath, content, 'utf8');
        console.log(`Updated: ${path.relative(baseDir, filePath)}`);
        changedFiles++;
    }
}

console.log(`\nRefactoring complete! Updated ${changedFiles} files.`);
