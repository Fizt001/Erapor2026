<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TahunAjaran;
use App\Models\WaliKelas;
use App\Models\Siswa;
use App\Models\PrestasiSiswa;
use App\Models\Titimangsa;
use Illuminate\Support\Facades\Validator;

class WalasPrestasiController extends Controller
{
    // Cek hak akses dan ambil kelas walas
    private function getKelasWalas($user)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$tahunAjaranAktif) {
            return null;
        }

        $walas = WaliKelas::where('guru_id', $user->id)
            ->whereHas('kelas', function($query) use ($tahunAjaranAktif) {
                $query->where('tahun_ajaran_id', $tahunAjaranAktif->id);
            })->first();

        return $walas ? $walas->kelas_id : null;
    }

    public function index(Request $request)
    {
        $kelasId = $this->getKelasWalas($request->user());
        if (!$kelasId) {
            return response()->json(['success' => false, 'message' => 'Anda bukan wali kelas aktif.'], 403);
        }

        $tahunAjaranAktif = TahunAjaran::where('is_aktif', true)->first();
        $titimangsaIds = Titimangsa::where('tahun_ajaran_id', $tahunAjaranAktif->id)->pluck('id');

        // Ambil data prestasi siswa yang berada di kelas ini
        $prestasi = PrestasiSiswa::with(['siswa.user', 'titimangsa'])
            ->whereHas('siswa', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            })
            ->whereIn('titimangsa_id', $titimangsaIds)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'siswa_id' => $p->siswa_id,
                    'nama_prestasi' => $p->nama_prestasi,
                    'jenis_prestasi' => $p->jenis_prestasi,
                    'tingkat' => $p->tingkat,
                    'penyelenggara' => $p->penyelenggara,
                    'tahun' => $p->tahun,
                    'keterangan' => $p->keterangan,
                    'titimangsa_id' => $p->titimangsa_id,
                    'titimangsa' => $p->titimangsa,
                    'siswa' => [
                        'id' => $p->siswa->id,
                        'name' => $p->siswa->name,
                        'nis' => $p->siswa->nis,
                    ]
                ];
            });
            
        // Ambil data siswa di kelas ini untuk pilihan dropdown
        $siswas = Siswa::where('kelas_id', $kelasId)
            ->whereNull('tanggal_keluar')
            ->get()
            ->sortBy('user.name') // user relationship already loaded if we use custom scope, but let's map safely
            ->map(function($s) {
                return ['id' => $s->id, 'nama' => $s->name, 'nis' => $s->nis];
            })->values();
            
        $titimangsas = Titimangsa::where('tahun_ajaran_id', $tahunAjaranAktif->id)
            ->get()
            ->map(function($t) {
                return ['id' => $t->id, 'nama' => $t->nama_periode_panjang ?: $t->nama_periode];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'prestasi' => $prestasi,
                'siswas' => $siswas,
                'titimangsas' => $titimangsas
            ]
        ]);
    }

    public function store(Request $request)
    {
        $kelasId = $this->getKelasWalas($request->user());
        if (!$kelasId) {
            return response()->json(['success' => false, 'message' => 'Anda bukan wali kelas aktif.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'siswa_id' => 'required|exists:siswa,id',
            'titimangsa_id' => 'required|exists:titimangsas,id',
            'nama_prestasi' => 'required|string|max:255',
            'jenis_prestasi' => 'required|string|max:255',
            'tingkat' => 'required|string|max:255',
            'penyelenggara' => 'required|string|max:255',
            'tahun' => 'required|string|max:4',
            'keterangan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Pastikan siswa ada di kelas walas ini
        $siswa = Siswa::where('id', $request->siswa_id)->where('kelas_id', $kelasId)->first();
        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan di kelas Anda.'], 403);
        }

        PrestasiSiswa::create($request->all());

        return response()->json(['success' => true, 'message' => 'Prestasi berhasil ditambahkan.']);
    }

    public function update(Request $request, $id)
    {
        $kelasId = $this->getKelasWalas($request->user());
        if (!$kelasId) {
            return response()->json(['success' => false, 'message' => 'Anda bukan wali kelas aktif.'], 403);
        }

        $prestasi = PrestasiSiswa::with('siswa')->find($id);
        if (!$prestasi || $prestasi->siswa->kelas_id != $kelasId) {
            return response()->json(['success' => false, 'message' => 'Prestasi tidak ditemukan atau Anda tidak memiliki akses.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_prestasi' => 'required|string|max:255',
            'jenis_prestasi' => 'required|string|max:255',
            'tingkat' => 'required|string|max:255',
            'penyelenggara' => 'required|string|max:255',
            'tahun' => 'required|string|max:4',
            'titimangsa_id' => 'required|exists:titimangsas,id',
            'keterangan' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $prestasi->update($request->only([
            'nama_prestasi', 'jenis_prestasi', 'tingkat', 
            'penyelenggara', 'tahun', 'titimangsa_id', 'keterangan'
        ]));

        return response()->json(['success' => true, 'message' => 'Prestasi berhasil diubah.']);
    }

    public function destroy(Request $request, $id)
    {
        $kelasId = $this->getKelasWalas($request->user());
        if (!$kelasId) {
            return response()->json(['success' => false, 'message' => 'Anda bukan wali kelas aktif.'], 403);
        }

        $prestasi = PrestasiSiswa::with('siswa')->find($id);
        if (!$prestasi || $prestasi->siswa->kelas_id != $kelasId) {
            return response()->json(['success' => false, 'message' => 'Prestasi tidak ditemukan atau Anda tidak memiliki akses.'], 404);
        }

        $prestasi->delete();

        return response()->json(['success' => true, 'message' => 'Prestasi berhasil dihapus.']);
    }
}
