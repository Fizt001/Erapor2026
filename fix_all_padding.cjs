const fs = require('fs');
const path = require('path');

const targetDirs = [
    path.join(__dirname, 'erapor-fe/app/pages/admin'),
    path.join(__dirname, 'erapor-fe/app/pages/kurikulum')
];

function walk(dir) {
    let results = [];
    const list = fs.readdirSync(dir);
    list.forEach(file => {
        const filePath = path.join(dir, file);
        const stat = fs.statSync(filePath);
        if (stat && stat.isDirectory()) {
            results = results.concat(walk(filePath));
        } else if (file.endsWith('.vue')) {
            results.push(filePath);
        }
    });
    return results;
}

const vueFiles = targetDirs.flatMap(dir => {
    if (fs.existsSync(dir)) {
        return walk(dir);
    }
    return [];
});

console.log(`Found ${vueFiles.length} VUE files.`);

vueFiles.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    let original = content;

    // 1. Replace max-w-7xl with max-w-5xl in outer layout containers
    // Matches patterns like: class="p-2 sm:p-6 lg:p-8 max-w-7xl"
    content = content.replace(/(class="[^"]*)max-w-7xl([^"]*")/g, (match, prefix, suffix) => {
        // If it's a modal (like max-w-md, max-w-sm, max-w-[210mm]), we don't want to mess with it
        // But if it's the main container containing padding and max-w-7xl, replace it
        if (prefix.includes('p-2') || prefix.includes('p-6') || prefix.includes('p-8')) {
            console.log(`[${path.basename(file)}] Replacing max-w-7xl with max-w-5xl`);
            return `${prefix}max-w-5xl${suffix}`;
        }
        return match;
    });

    // 2. Specifially handle dashboard.vue style: p-6 lg:p-8 space-y-6 max-w-7xl
    if (content.includes('p-6 lg:p-8 space-y-6 max-w-7xl')) {
        console.log(`[${path.basename(file)}] Replacing dashboard container padding and max-w-7xl`);
        content = content.replace('p-6 lg:p-8 space-y-6 max-w-7xl', 'p-2 sm:p-6 lg:p-8 space-y-6 max-w-5xl relative');
    }

    // 3. Clean up duplicated paddings like p-2 sm:p-2 sm:p-6
    if (content.includes('p-2 sm:p-2 sm:p-6')) {
        console.log(`[${path.basename(file)}] Cleaning up duplicate padding`);
        content = content.replace('p-2 sm:p-2 sm:p-6', 'p-2 sm:p-6');
    }

    if (content !== original) {
        fs.writeFileSync(file, content, 'utf8');
        console.log(`Updated: ${file}`);
    }
});
