const fs = require('fs');

let adminLayout = fs.readFileSync('app/layouts/admin.vue', 'utf8');

// Replace admin with kurikulum
let kurikulumLayout = adminLayout
  .replace(/adminMenus/g, 'kurikulumMenus')
  .replace(/\/admin\//g, '/kurikulum/')
  .replace(/Admin Workspace/g, 'Kurikulum Workspace')
  .replace(/Administrator/g, 'Waka Kurikulum')
  .replace(/admin@erapor\.com/g, 'kurikulum@erapor.com')
  .replace(/Admin/g, 'Kurikulum')
  // Theme colors
  .replace(/emerald/g, 'amber')
  .replace(/teal/g, 'orange')
  // Drawer titles
  .replace(/Pengaturan Sistem/g, 'Persiapan & Struktur')
  .replace(/Master Data/g, 'Pembagian Tugas')
  .replace(/Administrasi Akademik/g, 'Standar & Referensi')
  // Bottom Nav items
  .replace(/Sistem/g, 'Persiapan')
  .replace(/Master/g, 'Tugas')
  .replace(/Akademik/g, 'Standar')
  // Icons mapping for Drawer
  .replace(/pengaturan/g, 'persiapan')
  .replace(/master/g, 'tugas')
  .replace(/akademik/g, 'standar')

// Update drawerMenuGroups inside the script
const newDrawerMenuGroups = `
const drawerMenuGroups = {
  persiapan: {
    title: 'Persiapan & Struktur',
    menus: [
      { name: 'Periode & Titimangsa', path: '/kurikulum/titimangsa', icon: '⏳' },
      { name: 'Mata Pelajaran', path: '/kurikulum/mapel', icon: '📚' },
      { name: 'Struktur Umum', path: '/kurikulum/struktur-umum', icon: '📑' },
      { name: 'Struktur Kejuruan', path: '/kurikulum/struktur-kejuruan', icon: '⚙️' },
      { name: 'Ekstrakurikuler', path: '/kurikulum/ekskul', icon: '🏃' },
    ]
  },
  tugas: {
    title: 'Pembagian Tugas',
    menus: [
      { name: 'Plot Guru Mapel', path: '/kurikulum/pengampu', icon: '👨‍🏫' },
      { name: 'Wali Kelas', path: '/kurikulum/wali-kelas', icon: '👨‍👩‍👧‍👦' },
    ]
  },
  standar: {
    title: 'Standar & Referensi',
    menus: [
      { name: 'Standar Nilai (KKM)', path: '/kurikulum/kkm', icon: '🎯' },
      { name: 'Master Deskripsi', path: '/kurikulum/deskripsi', icon: '📝' },
      { name: 'Penanganan Kasus', path: '/kurikulum/penanganan', icon: '⚖️' },
    ]
  },
  all: {
    title: 'Semua Menu Kurikulum',
    menus: kurikulumMenus
  }
}
`;

kurikulumLayout = kurikulumLayout.replace(/const drawerMenuGroups = \{[\s\S]*?menus: adminMenus\n  \}\n\}/, newDrawerMenuGroups.trim());

// Update getSvgIcon
const getSvgIconReplacement = `
const getSvgIcon = (emoji) => {
  const icons = {
    '📊': '<svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" /></svg>',
    '⏳': '<svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
    '📚': '<svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>',
    '📑': '<svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>',
    '⚙️': '<svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>',
    '🏃': '<svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>',
    '👨‍🏫': '<svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>',
    '👨‍👩‍👧‍👦': '<svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>',
    '🎯': '<svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>',
    '📝': '<svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>',
    '⚖️': '<svg xmlns="http://www.w3.org/2000/svg" class="h-[22px] w-[22px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" /></svg>',
  };
  return icons[emoji] || \`<span class="text-xl">\${emoji}</span>\`;
}
`;

kurikulumLayout = kurikulumLayout.replace(/const getSvgIcon = \(emoji\) => \{[\s\S]*?return icons\[emoji\] \|\| `<span class="text-xl">\$\{emoji\}<\/span>`;\n\}/, getSvgIconReplacement.trim());

fs.writeFileSync('app/layouts/kurikulum.vue', kurikulumLayout);
console.log('Layout updated');
