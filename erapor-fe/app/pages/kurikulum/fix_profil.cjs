const fs = require('fs');
let content = fs.readFileSync('d:/koding/Erapor2026/erapor-fe/app/pages/kurikulum/profil.vue', 'utf8');

content = content
  .replace(/from-teal-600 to-emerald-700/g, 'from-indigo-600 to-blue-700')
  .replace(/border-teal-500/g, 'border-indigo-500')
  .replace(/text-teal-100/g, 'text-indigo-100')
  .replace(/bg-emerald-50/g, 'bg-indigo-50')
  .replace(/border-emerald-200/g, 'border-indigo-200')
  .replace(/text-emerald-700/g, 'text-indigo-700')
  .replace(/text-emerald-600/g, 'text-indigo-600')
  .replace(/from-emerald-400 to-emerald-600/g, 'from-indigo-400 to-indigo-600')
  .replace(/from-emerald-500 to-teal-600/g, 'from-indigo-500 to-blue-600')
  .replace(/shadow-emerald-500\/30/g, 'shadow-indigo-500/30')
  .replace(/bg-emerald-500 hover:bg-emerald-600/g, 'bg-indigo-500 hover:bg-indigo-600')
  .replace(/border-emerald-500/g, 'border-indigo-500')
  .replace(/ring-emerald-500\/10/g, 'ring-indigo-500/10')
  .replace(/layout: 'admin'/g, "layout: 'kurikulum'")
  .replace(/middleware: 'admin'/g, "middleware: 'kurikulum'")
  .replace(/title: 'Profil Admin'/g, "title: 'Profil Kurikulum'")
  .replace(/Admin Sistem Erapor/g, 'Waka Kurikulum Erapor')
  .replace(/Data Akun Admin/g, 'Data Akun Kurikulum')
  .replace(/Administrator/g, 'Kurikulum')
  .replace(/Identitas Administrator/g, 'Identitas Kurikulum')
  .replace(/Profil Admin/g, 'Profil Kurikulum')
  .replace(/Super Administrator/g, 'Waka Kurikulum');

fs.writeFileSync('d:/koding/Erapor2026/erapor-fe/app/pages/kurikulum/profil.vue', content);
