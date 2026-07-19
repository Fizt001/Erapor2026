<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WaliKelas;
use App\Models\Kelas;
use App\Models\Siswa;

class SuperadminController extends Controller
{
    public function getGurus()
    {
        $gurus = User::where('role', 'guru')->get(['id', 'name', 'email']);
        return response()->json(['success' => true, 'data' => $gurus]);
    }

    public function getWalasClasses()
    {
        $aktifTahunAjaran = \App\Models\TahunAjaran::where('is_aktif', true)->first();
        $tahunAjaranId = $aktifTahunAjaran ? $aktifTahunAjaran->id : null;

        // Get classes that have a Wali Kelas in the active academic year
        $walas = WaliKelas::whereHas('kelas', function($q) use ($tahunAjaranId) {
            if ($tahunAjaranId) $q->where('tahun_ajaran_id', $tahunAjaranId);
        })->with(['kelas', 'guru'])->get();
        
        $classes = $walas->map(function ($w) {
            return [
                'kelas_id' => $w->kelas_id,
                'nama_kelas' => ($w->kelas->tingkat ? $w->kelas->tingkat . ' ' : '') . $w->kelas->nama_kelas,
                'guru_id' => $w->guru_id,
                'guru_name' => $w->guru ? $w->guru->name : 'Unknown'
            ];
        });
        
        return response()->json(['success' => true, 'data' => $classes]);
    }

    public function getSiswas()
    {
        $aktifTahunAjaran = \App\Models\TahunAjaran::where('is_aktif', true)->first();
        $tahunAjaranId = $aktifTahunAjaran ? $aktifTahunAjaran->id : null;

        // Get all students with their class and user account in the active academic year
        $siswas = Siswa::whereHas('kelas', function($q) use ($tahunAjaranId) {
            if ($tahunAjaranId) $q->where('tahun_ajaran_id', $tahunAjaranId);
        })->with(['kelas:id,tingkat,nama_kelas', 'user:id,name'])
          ->whereNotNull('user_id')
          ->get(['id', 'user_id', 'nis', 'kelas_id']);
        
        $data = $siswas->map(function ($s) {
            return [
                'siswa_id' => $s->id,
                'user_id' => $s->user_id,
                'name' => $s->user ? $s->user->name : 'Unknown',
                'nis' => $s->nis,
                'kelas_id' => $s->kelas_id,
                'nama_kelas' => $s->kelas ? (($s->kelas->tingkat ? $s->kelas->tingkat . ' ' : '') . $s->kelas->nama_kelas) : 'Unknown'
            ];
        });
        
        return response()->json(['success' => true, 'data' => $data]);
    }
}
