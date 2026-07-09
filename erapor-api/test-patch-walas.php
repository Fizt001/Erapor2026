<?php

$files = [
    'd:\koding\Erapor2026\erapor-api\app\Http\Controllers\Api\Guru\WalasCetakController.php',
    'd:\koding\Erapor2026\erapor-api\app\Http\Controllers\Api\Guru\WalasController.php',
    'd:\koding\Erapor2026\erapor-api\app\Http\Controllers\Api\Guru\WalasEkskulController.php',
    'd:\koding\Erapor2026\erapor-api\app\Http\Controllers\Api\Guru\WalasKokurikulerController.php',
    'd:\koding\Erapor2026\erapor-api\app\Http\Controllers\Api\Guru\WalasRekapController.php',
    'd:\koding\Erapor2026\erapor-api\app\Http\Controllers\Api\Guru\WalasPrestasiController.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);

    $old = <<<PHP
        \$walas = WaliKelas::with(['kelas.kurikulum', 'kelas.kejuruan.program.bidang', 'guru.biodata'])->where('guru_id', \$user->id)->first();
        if (!\$walas) return null;

        \$tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!\$tahunAktif) return null;
PHP;

    $new = <<<PHP
        \$tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!\$tahunAktif) return null;

        \$walas = WaliKelas::with(['kelas.kurikulum', 'kelas.kejuruan.program.bidang', 'guru.biodata'])
            ->where('guru_id', \$user->id)
            ->whereHas('kelas', function (\$query) use (\$tahunAktif) {
                \$query->where('tahun_ajaran_id', \$tahunAktif->id);
            })
            ->first();
        if (!\$walas) return null;
PHP;

    $content = str_replace($old, $new, $content);
    file_put_contents($file, $content);
}
echo "Patched all controllers!";
