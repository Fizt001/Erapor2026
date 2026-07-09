import subprocess

php_code = """<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$kelas = App\Models\Kelas::find(4);
if ($kelas) {
    $kelas->tingkat = 'XII';
    $kelas->save();
    echo "BERHASIL UPDATE KELAS ID 4 KE TINGKAT XII";
} else {
    echo "Kelas tidak ditemukan";
}
"""

script_path = r'd:\koding\Erapor2026\erapor-api\test_update_kelas2.php'
with open(script_path, 'w', encoding='utf-8') as f:
    f.write(php_code)

result = subprocess.run(['php', script_path], capture_output=True, text=True, cwd=r'd:\koding\Erapor2026\erapor-api')
print(result.stdout)
print(result.stderr)
