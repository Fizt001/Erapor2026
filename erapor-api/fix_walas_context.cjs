const fs = require('fs');

function fixWalasEkskul() {
    const file = 'd:/koding/Erapor2026/erapor-api/app/Http/Controllers/Api/Guru/WalasEkskulController.php';
    let content = fs.readFileSync(file, 'utf8');
    content = content.replace(
        /\$user = Auth::user\(\);\s*\$walas = WaliKelas::with\(\['kelas\.kurikulum'\]\)->where\('guru_id', \$user->id\)->first\(\);\s*if \(!\$walas\) \{\s*return null;\s*\}\s*\$tahunAktif = TahunAjaran::where\('is_aktif', true\)->first\(\);\s*if \(!\$tahunAktif\) return null;/,
        `$user = Auth::user();

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAktif) return null;

        $walas = WaliKelas::with(['kelas.kurikulum'])->where('guru_id', $user->id)
            ->whereHas('kelas', function($query) use ($tahunAktif) {
                $query->where('tahun_ajaran_id', $tahunAktif->id);
            })->first();
        if (!$walas) {
            return null;
        }`
    );
    fs.writeFileSync(file, content, 'utf8');
    console.log('Fixed WalasEkskulController');
}

function fixWalasRekap() {
    const file = 'd:/koding/Erapor2026/erapor-api/app/Http/Controllers/Api/Guru/WalasRekapController.php';
    let content = fs.readFileSync(file, 'utf8');
    content = content.replace(
        /\$user = Auth::user\(\);\s*\$walas = WaliKelas::with\(\['kelas\.kurikulum'\]\)->where\('guru_id', \$user->id\)->first\(\);\s*if \(!\$walas\) \{\s*return null;\s*\}\s*\$tahunAktif = TahunAjaran::where\('is_aktif', true\)->first\(\);\s*if \(!\$tahunAktif\) return null;/,
        `$user = Auth::user();

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAktif) return null;

        $walas = WaliKelas::with(['kelas.kurikulum'])->where('guru_id', $user->id)
            ->whereHas('kelas', function($query) use ($tahunAktif) {
                $query->where('tahun_ajaran_id', $tahunAktif->id);
            })->first();
        if (!$walas) {
            return null;
        }`
    );
    fs.writeFileSync(file, content, 'utf8');
    console.log('Fixed WalasRekapController');
}

function fixWalasKokurikuler() {
    const file = 'd:/koding/Erapor2026/erapor-api/app/Http/Controllers/Api/Guru/WalasKokurikulerController.php';
    let content = fs.readFileSync(file, 'utf8');
    content = content.replace(
        /\$user = Auth::user\(\);\s*\$walas = WaliKelas::where\('guru_id', \$user->id\)->first\(\);\s*if \(!\$walas\) return null;\s*\$tahunAktif = TahunAjaran::where\('is_aktif', true\)->first\(\);\s*if \(!\$tahunAktif\) return null;/,
        `$user = Auth::user();

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAktif) return null;

        $walas = WaliKelas::where('guru_id', $user->id)
            ->whereHas('kelas', function($query) use ($tahunAktif) {
                $query->where('tahun_ajaran_id', $tahunAktif->id);
            })->first();
        if (!$walas) return null;`
    );
    fs.writeFileSync(file, content, 'utf8');
    console.log('Fixed WalasKokurikulerController');
}

function fixWalasPrestasi() {
    const file = 'd:/koding/Erapor2026/erapor-api/app/Http/Controllers/Api/Guru/WalasPrestasiController.php';
    let content = fs.readFileSync(file, 'utf8');
    content = content.replace(
        /\$tahunAjaranAktif = TahunAjaran::where\('is_aktif', true\)->first\(\);\s*if \(!\$tahunAjaranAktif\) \{\s*return null;\s*\}\s*\$walas = WaliKelas::where\('guru_id', \$user->id\)->first\(\);/,
        `$tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAjaranAktif) {
            return null;
        }

        $walas = WaliKelas::where('guru_id', $user->id)
            ->whereHas('kelas', function($query) use ($tahunAjaranAktif) {
                $query->where('tahun_ajaran_id', $tahunAjaranAktif->id);
            })->first();`
    );
    fs.writeFileSync(file, content, 'utf8');
    console.log('Fixed WalasPrestasiController');
}

fixWalasEkskul();
fixWalasRekap();
fixWalasKokurikuler();
fixWalasPrestasi();
