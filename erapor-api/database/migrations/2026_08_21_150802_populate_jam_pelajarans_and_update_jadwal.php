<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\JadwalPelajaran;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $waktuSeninKamis = [
            1 => ['07:00:00', '07:45:00'],
            2 => ['07:45:00', '08:20:00'],
            3 => ['08:20:00', '08:55:00'],
            4 => ['08:55:00', '09:30:00'],
            5 => ['09:50:00', '10:35:00'],
            6 => ['10:35:00', '11:10:00'],
            7 => ['11:10:00', '11:45:00'],
            8 => ['11:45:00', '12:20:00'],
            9 => ['13:15:00', '13:50:00'],
            10 => ['13:50:00', '14:25:00'],
            11 => ['14:25:00', '15:00:00'],
        ];

        $waktuJumat = [
            1 => ['07:00:00', '07:40:00'],
            2 => ['07:40:00', '08:15:00'],
            3 => ['08:15:00', '08:50:00'],
            4 => ['08:50:00', '09:25:00'],
            5 => ['09:45:00', '10:20:00'],
            6 => ['10:20:00', '10:55:00'],
            7 => ['10:55:00', '11:30:00'],
            8 => ['13:00:00', '13:35:00'],
            9 => ['13:35:00', '14:10:00'],
            10 => ['14:10:00', '14:45:00'],
        ];

        // 1. Seed jam_pelajarans
        DB::table('jam_pelajarans')->truncate();
        $insert = [];
        $now = now();
        foreach ($waktuSeninKamis as $jam => $waktu) {
            $insert[] = [
                'kategori_hari' => 'Senin-Kamis',
                'jam_ke' => $jam,
                'waktu_mulai' => $waktu[0],
                'waktu_selesai' => $waktu[1],
                'created_at' => $now,
                'updated_at' => $now
            ];
        }
        foreach ($waktuJumat as $jam => $waktu) {
            $insert[] = [
                'kategori_hari' => 'Jumat',
                'jam_ke' => $jam,
                'waktu_mulai' => $waktu[0],
                'waktu_selesai' => $waktu[1],
                'created_at' => $now,
                'updated_at' => $now
            ];
        }
        DB::table('jam_pelajarans')->insert($insert);

        // 2. Fix existing jadwal_pelajarans
        $jadwals = DB::table('jadwal_pelajarans')->get();
        foreach ($jadwals as $j) {
            $update = [];
            if (in_array($j->hari, ['Senin', 'Selasa', 'Rabu', 'Kamis'])) {
                if (isset($waktuSeninKamis[$j->jam_ke])) {
                    $update['waktu_mulai'] = $waktuSeninKamis[$j->jam_ke][0];
                    $update['waktu_selesai'] = $waktuSeninKamis[$j->jam_ke][1];
                }
            } else if ($j->hari === 'Jumat') {
                if (isset($waktuJumat[$j->jam_ke])) {
                    $update['waktu_mulai'] = $waktuJumat[$j->jam_ke][0];
                    $update['waktu_selesai'] = $waktuJumat[$j->jam_ke][1];
                }
            }
            if (!empty($update)) {
                DB::table('jadwal_pelajarans')->where('id', $j->id)->update($update);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
