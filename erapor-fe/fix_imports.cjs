const fs = require('fs');
const path = require('path');

const files = [
    'app/pages/guru/formatif/master.vue',
    'app/pages/guru/formatif/nilai.vue',
    'app/pages/guru/sumatif/nilai.vue',
    'app/pages/guru/sumatif/rekap.vue',
    'app/pages/guru/walas/biodata.vue',
    'app/pages/guru/walas/catatan.vue',
    'app/pages/guru/walas/ekskul.vue',
    'app/pages/guru/walas/kokurikuler.vue',
    'app/pages/guru/walas/monitoring.vue',
    'app/pages/guru/walas/naik-kelas.vue',
    'app/pages/guru/walas/p5.vue',
    'app/pages/guru/walas/prestasi.vue',
    'app/pages/guru/walas/rapor.vue',
    'app/pages/guru/walas/rekap.vue'
];

files.forEach(f => {
    const p = path.join(__dirname, f);
    if (!fs.existsSync(p)) return;
    
    let content = fs.readFileSync(p, 'utf8');
    const lines = content.split('\n');
    const newLines = [];
    let foundInjectedImport = false;
    
    for (let i = 0; i < lines.length; i++) {
        if (lines[i].includes(`import { ref, computed, onMounted } from 'vue'`)) {
            if (!foundInjectedImport) {
                foundInjectedImport = true;
                continue; // remove the injected one
            }
        }
        newLines.push(lines[i]);
    }
    
    fs.writeFileSync(p, newLines.join('\n'));
    console.log('Fixed ' + f);
});
