export interface MenuConfig {
  name?: string
  path?: string
  icon?: string
  divider?: boolean
  dividerLabel?: string
}

export const adminMenus: MenuConfig[] = [
  { name: 'Dashboard Admin', path: '/admin/dashboard', icon: '📊' },
  { divider: true, dividerLabel: 'Pengaturan Sistem' },
  { name: 'Data Sekolah', path: '/admin/sekolah', icon: '🏢' },
  { name: 'Tahun Ajaran', path: '/admin/tahun-ajaran', icon: '🗓️' },
  { name: 'Manajemen User', path: '/admin/users', icon: '👥' },
  { divider: true, dividerLabel: 'Master Data' },
  { name: 'Master Database', path: '/admin/master-database', icon: '🗄️' },
  { name: 'Master Kejuruan', path: '/admin/kejuruan', icon: '🛠️' },
  { name: 'Master Kurikulum', path: '/admin/kurikulum', icon: '📚' },
  { name: 'Master Kelas', path: '/admin/kelas', icon: '🏫' },
  { divider: true, dividerLabel: 'Administrasi Akademik' },
  { name: 'Riwayat Mutasi', path: '/admin/riwayat-mutasi', icon: '📝' },
  { name: 'Kenaikan Kelas', path: '/admin/kenaikan-kelas', icon: '🎓' },
  { divider: true, dividerLabel: 'Laporan & Arsip' },
  { name: 'Buku Induk', path: '/admin/buku-induk', icon: '📖' },
  { name: 'Backup & Arsip', path: '/admin/backup', icon: '💾' },
]

export const kurikulumMenus: MenuConfig[] = [
  { name: 'Dashboard Kurikulum', path: '/kurikulum/dashboard', icon: '📊' },
  { divider: true, dividerLabel: 'Persiapan & Struktur' },
  { name: 'Periode & Titimangsa', path: '/kurikulum/titimangsa', icon: '⏳' },
  { name: 'Mata Pelajaran', path: '/kurikulum/mapel', icon: '📚' },
  { name: 'Struktur Umum', path: '/kurikulum/struktur-umum', icon: '📑' },
  { name: 'Struktur Kejuruan', path: '/kurikulum/struktur-kejuruan', icon: '⚙️' },
  { name: 'Ekstrakurikuler', path: '/kurikulum/ekskul', icon: '🏃' },
  { divider: true, dividerLabel: 'Pembagian Tugas' },
  { name: 'Plot Guru Mapel', path: '/kurikulum/pengampu', icon: '👨‍🏫' },
  { name: 'Wali Kelas', path: '/kurikulum/wali-kelas', icon: '👨‍👩‍👧‍👦' },
  { divider: true, dividerLabel: 'Standar & Referensi' },
  { name: 'Standar Nilai (KKM)', path: '/kurikulum/kkm', icon: '🎯' },
  { name: 'Master Deskripsi', path: '/kurikulum/deskripsi', icon: '📝' },
  { name: 'Penanganan Kasus', path: '/kurikulum/penanganan', icon: '⚖️' },
]

export const guruMenus: MenuConfig[] = [
  { name: 'Dashboard', path: '/guru/dashboard', icon: '📊' },
  { divider: true, dividerLabel: 'Kegiatan KBM' },
  { name: 'Absensi Pertemuan', path: '/guru/absensi', icon: '📆' },
  { name: 'Jurnal Mengajar', path: '/guru/jurnal', icon: '📓' },
  { divider: true, dividerLabel: 'Penilaian Formatif' },
  { name: 'Data TP Formatif', path: '/guru/formatif/master', icon: '📝' },
  { name: 'Input Asessmen Formatif', path: '/guru/formatif/nilai', icon: '✏️' },
  { divider: true, dividerLabel: 'Penilaian Sumatif' },
  { name: 'Input Asessmen Sumatif', path: '/guru/sumatif/nilai', icon: '📋' },
  { name: 'Rekapitulasi Akhir', path: '/guru/sumatif/rekap', icon: '📈' },
]

export const walasMenus: MenuConfig[] = [
  { name: 'Dashboard Walas', path: '/guru/walas/dashboard', icon: '📊' },
  { divider: true, dividerLabel: 'Data & Monitoring' },
  { name: 'Biodata Siswa', path: '/guru/walas/biodata', icon: '🧑‍🎓' },
  { name: 'Kalender Absensi', path: '/guru/walas/absensi', icon: '📅' },
  { name: 'Monitoring Nilai', path: '/guru/walas/monitoring', icon: '👀' },
  { divider: true, dividerLabel: 'Input Data Rapor' },
  { name: 'Ekstrakurikuler', path: '/guru/walas/ekskul', icon: '🏃' },
  { name: 'Kokurikuler', path: '/guru/walas/kokurikuler', icon: '🌱' },
  { name: 'Catatan Walas', path: '/guru/walas/catatan', icon: '📝' },
  { name: 'Bimbingan Walas', path: '/guru/walas/bimbingan', icon: '🤝' },
  { divider: true, dividerLabel: 'Rekapitulasi & Cetak' },
  { name: 'Rekap Semester', path: '/guru/walas/rekap', icon: '📒' },
  { name: 'Cetak Leger & Rapor', path: '/guru/walas/rapor', icon: '🖨️' },
  { name: 'Catatan Kenaikan', path: '/guru/walas/kenaikan', icon: '📈' },
]

export const bkMenus: MenuConfig[] = [
  { name: 'Dashboard BK', path: '/bk/dashboard', icon: '📊' },
  { divider: true, dividerLabel: 'Data Pokok BK' },
  { name: 'Master Kategori', path: '/bk/master-database', icon: '🗄️' },
  { name: 'Master Pelanggaran', path: '/bk/pelanggaran', icon: '📋' },
  { divider: true, dividerLabel: 'Layanan & Tindakan' },
  { name: 'Input Poin Siswa', path: '/bk/poin', icon: '✍️' },
  { name: 'Penanganan Kasus', path: '/bk/penanganan', icon: '⚖️' },
  { divider: true, dividerLabel: 'Laporan Konseling' },
  { name: 'Buku Kasus Siswa', path: '/bk/buku-kasus', icon: '📖' },
  { name: 'Grafik Kedisiplinan', path: '/bk/laporan', icon: '📈' },
]

export const siswaMenus: MenuConfig[] = [
  { name: 'Dashboard Siswa', path: '/siswa/dashboard', icon: '📊' },
  { name: 'Biodata', path: '/siswa/biodata', icon: '👤' },
  { name: 'Lihat Rapor', path: '/siswa/rapor', icon: '📑' },
  { name: 'Laporan Absensi', path: '/siswa/absensi', icon: '📅' },
  { name: 'Catatan Kedisiplinan', path: '/siswa/kedisiplinan', icon: '⚖️' },
]
