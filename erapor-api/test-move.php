<?php
App\Models\Siswa::where('kelas_id', 2)->update(['kelas_id' => 4]);
echo "Updated!";
