import subprocess

php_code = """
$kelas = App\Models\Kelas::find(4);
if ($kelas) {
    $kelas->tingkat = 'XII';
    $kelas->save();
    echo 'BERHASIL UPDATE KELAS ID 4 KE TINGKAT XII';
} else {
    echo 'Kelas tidak ditemukan';
}
"""

with open(r'd:\koding\Erapor2026\erapor-api\test_update_kelas.php', 'w', encoding='utf-8') as f:
    f.write(php_code)
