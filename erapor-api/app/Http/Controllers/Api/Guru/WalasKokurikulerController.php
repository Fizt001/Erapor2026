<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WaliKelas;
use App\Models\TahunAjaran;
use App\Models\Titimangsa;
use App\Models\Siswa;
use App\Models\Kokurikuler;

class WalasKokurikulerController extends Controller
{
    private function getWalasContext()
    {
        $user = Auth::user();

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAktif) return null;

        $walas = WaliKelas::where('guru_id', $user->id)
            ->whereHas('kelas', function($query) use ($tahunAktif) {
                $query->where('tahun_ajaran_id', $tahunAktif->id);
            })->first();
        if (!$walas) return null;

        return [
            'kelas_id' => $walas->kelas_id,
            'tahun_ajaran' => $tahunAktif,
        ];
    }

    public function index(Request $request)
    {
        $context = $this->getWalasContext();
        if (!$context) {
            return response()->json(['success' => false, 'message' => 'Anda bukan wali kelas aktif.'], 403);
        }

        $kelasId = $context['kelas_id'];
        $tahunId = $context['tahun_ajaran']->id;

        // Ambil titimangsa tipe PSAS dan PSAT saja untuk periode kokurikuler (sesuai arahan: "hanya inputan utk PSAS dan PSAT")
        $periodes = Titimangsa::where('tahun_ajaran_id', $tahunId)
            ->whereIn('nama_periode', ['PSAS', 'PSAT'])
            ->orderBy('id', 'asc')
            ->get();

        // Ambil data siswa di kelas tersebut
        $siswas = Siswa::with('user')
            ->where('kelas_id', $kelasId)
            ->whereNull('tanggal_keluar')
            ->get()
            ->sortBy(function($siswa) {
                return $siswa->user ? $siswa->user->name : '';
            })
            ->values();

        // Ambil data kokurikuler
        $kokurikulers = Kokurikuler::whereIn('siswa_id', $siswas->pluck('id'))
            ->whereIn('titimangsa_id', $periodes->pluck('id'))
            ->get();

        // Mapping Data untuk Frontend
        $data = $siswas->map(function ($siswa) use ($periodes, $kokurikulers) {
            $siswaData = [
                'id' => $siswa->id,
                'nama_lengkap' => $siswa->user ? $siswa->user->name : '-',
                'nis' => $siswa->nis,
            ];

            foreach ($periodes as $p) {
                $ko = $kokurikulers->where('siswa_id', $siswa->id)
                    ->where('titimangsa_id', $p->id)
                    ->first();
                
                $siswaData['ko_'.$p->id] = $ko ? $ko->keterangan : '';
            }

            return $siswaData;
        });

        $kelas = \App\Models\Kelas::with('kurikulum')->find($kelasId);
        $master_kurikulum = \App\Models\Kurikulum::all();

        return response()->json([
            'success' => true,
            'data' => $data,
            'periodes' => $periodes,
            'kelas' => $kelas,
            'master_kurikulum' => $master_kurikulum,
            'tahun_ajaran' => $context['tahun_ajaran']
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'titimangsa_id' => 'required|exists:titimangsas,id',
            'keterangan' => 'nullable|string'
        ]);

        $context = $this->getWalasContext();
        if (!$context) {
            return response()->json(['success' => false, 'message' => 'Anda bukan wali kelas.'], 403);
        }

        // Verifikasi Siswa adalah murid kelas walas tersebut
        $siswa = Siswa::find($request->siswa_id);
        if (!$siswa || $siswa->kelas_id != $context['kelas_id']) {
            return response()->json(['success' => false, 'message' => 'Siswa tidak valid.'], 403);
        }

        $titimangsa = Titimangsa::find($request->titimangsa_id);
        if (!$titimangsa || !$titimangsa->is_aktif) {
            return response()->json([
                'success' => false,
                'message' => 'Periode titimangsa sudah ditutup.'
            ], 403);
        }

        Kokurikuler::updateOrCreate(
            [
                'siswa_id' => $request->siswa_id,
                'titimangsa_id' => $request->titimangsa_id
            ],
            [
                'keterangan' => $request->keterangan
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Catatan tersimpan'
        ]);
    }

    public function updateKurikulum(Request $request)
    {
        $request->validate([
            'kurikulum_id' => 'required|exists:kurikulums,id'
        ]);

        $context = $this->getWalasContext();
        if (!$context) {
            return response()->json(['success' => false, 'message' => 'Anda bukan wali kelas.'], 403);
        }

        $kelas = \App\Models\Kelas::find($context['kelas_id']);
        if ($kelas) {
            $kelas->kurikulum_id = $request->kurikulum_id;
            $kelas->save();
            return response()->json(['success' => true, 'message' => 'Kurikulum kelas berhasil diperbarui']);
        }

        return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan'], 404);
    }
}
