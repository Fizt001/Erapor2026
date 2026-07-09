const fs = require('fs');

const files = [
    'd:/koding/Erapor2026/erapor-fe/app/pages/guru/formatif/master.vue',
    'd:/koding/Erapor2026/erapor-fe/app/pages/guru/formatif/nilai.vue',
    'd:/koding/Erapor2026/erapor-fe/app/pages/guru/sumatif/nilai.vue',
    'd:/koding/Erapor2026/erapor-fe/app/pages/guru/sumatif/rekap.vue'
];

files.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    
    // Replace the v-for logic
    const searchRegex = /v-for="ta in references\.tahunAjarans"/g;
    const replacement = 'v-for="ta in references.tahunAjarans.filter(t => t.is_aktif)"';
    
    if (content.match(searchRegex)) {
        content = content.replace(searchRegex, replacement);
        
        // Also let's visually add "(Aktif)" text next to the year just to be clear
        const optionRegex = /{{ ta\.tahun }}<\/option>/g;
        content = content.replace(optionRegex, '{{ ta.tahun }} (Aktif)</option>');

        fs.writeFileSync(file, content, 'utf8');
        console.log('Fixed', file);
    }
});
