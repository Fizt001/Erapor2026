
$kelas = App\Models\Kelas::find(4);
if ($kelas) {
    $kelas->tingkat = 'XII';
    $kelas->save();
    echo 'BERHASIL UPDATE KELAS ID 4 KE TINGKAT XII';
} else {
    echo 'Kelas tidak ditemukan';
}
