<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\SumatifNilai;
use App\Models\FormatifNilai;
use App\Models\Titimangsa;
use App\Models\DeskripsiTemplate;

class SumatifRekapController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mapels,id',
            'titimangsa_id' => 'required|exists:titimangsas,id',
        ]);

        $kelasId = $request->kelas_id;
        $mapelId = $request->mapel_id;
        $titimangsaId = $request->titimangsa_id;

        // Ambil data titimangsa untuk mengetahui kurikulum_id
        $titimangsa = Titimangsa::find($titimangsaId);
        if (!$titimangsa) {
            return response()->json(['status' => 'error', 'message' => 'Titimangsa tidak ditemukan'], 404);
        }

        // Ambil template deskripsi untuk kurikulum yang sesuai dengan titimangsa ini
        $template = DeskripsiTemplate::where('kurikulum_id', $titimangsa->kurikulum_id)
                                    ->where('is_active', true)
                                    ->first();
                                    
        $teksTertinggiDefault = "Peserta didik sangat menguasai materi [NAMA_TP] pada saat kegiatan pembelajaran di sekolah.";

        $tplTertinggi = $template->teks_tertinggi ?? $teksTertinggiDefault;

        $siswas = Siswa::with('user')
            ->where('kelas_id', $kelasId)
            ->where('status_siswa', 'aktif')
            ->get();

        $nilaiSumatif = SumatifNilai::where('titimangsa_id', $titimangsaId)
            ->where('mapel_id', $mapelId)
            ->where('kelas_id', $kelasId)
            ->get()
            ->keyBy('siswa_id');

        $nilaiFormatif = FormatifNilai::with('tp')
            ->where('titimangsa_id', $titimangsaId)
            ->where('mapel_id', $mapelId)
            ->where('kelas_id', $kelasId)
            ->get()
            ->groupBy('siswa_id');

        $rekapData = [];

        foreach ($siswas as $s) {
            $n = $nilaiSumatif[$s->id] ?? null;
            $na = $n ? round($n->na_value, 0) : null;
            
            $deskripsi = '-';
            
            if ($nilaiFormatif->has($s->id)) {
                $formatifSiswa = $nilaiFormatif[$s->id];
                
                // Urutkan nilai dari tertinggi ke terendah
                $formatifSorted = $formatifSiswa->sortByDesc('nilai');
                
                $tpTertinggi = $formatifSorted->first();
                $materiTertinggi = $tpTertinggi && $tpTertinggi->tp ? $tpTertinggi->tp->nama_tp : '...';
                
                // Ganti tag dengan nama materi
                $deskripsi = str_replace('[NAMA_TP]', $materiTertinggi, $tplTertinggi);
            }

            $rekapData[] = [
                'siswa_id' => $s->id,
                'nama' => $s->user->name,
                'nis' => $s->nis,
                'angka' => $na,
                'deskripsi' => $deskripsi
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $rekapData
        ]);
    }
}
