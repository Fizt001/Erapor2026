<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MutasiSiswa;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminMutasiController extends Controller
{
    public function index(Request $request)
    {
        $query = MutasiSiswa::with(['siswa', 'kelas_asal', 'kelas_tujuan', 'diajukanOleh', 'diaccOleh']);

        if ($request->has('status_approval') && $request->status_approval != '') {
            $query->where('status_approval', $request->status_approval);
        }

        $mutasi = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $mutasi
        ]);
    }

    public function approve(Request $request, $id)
    {
        $mutasi = MutasiSiswa::findOrFail($id);

        if ($mutasi->status_approval !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Status mutasi sudah diproses'], 400);
        }

        DB::beginTransaction();
        try {
            $mutasi->status_approval = 'approved';
            $mutasi->diacc_oleh = $request->user()->id;
            $mutasi->save();

            $siswa = Siswa::findOrFail($mutasi->siswa_id);

            if ($mutasi->jenis_mutasi === 'pindah_kelas') {
                $siswa->kelas_id = $mutasi->kelas_tujuan_id;
            } else {
                $siswa->status_siswa = $mutasi->jenis_mutasi;
                $siswa->tanggal_keluar = $mutasi->tanggal_mutasi;
                $siswa->alasan_keluar = $mutasi->alasan;
                // Kosongkan kelas agar tidak tampil di absen/nilai berjalan? 
                // Biasanya tidak perlu dikosongkan jika sistem filter by status_siswa, tapi lebih aman tetap di kelasnya namun statusnya keluar.
            }
            $siswa->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Mutasi berhasil disetujui',
                'data' => $mutasi
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'catatan_admin' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $mutasi = MutasiSiswa::findOrFail($id);

        if ($mutasi->status_approval !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Status mutasi sudah diproses'], 400);
        }

        $mutasi->status_approval = 'rejected';
        $mutasi->diacc_oleh = $request->user()->id;
        $mutasi->catatan_admin = $request->catatan_admin;
        $mutasi->save();

        return response()->json([
            'success' => true,
            'message' => 'Mutasi berhasil ditolak',
            'data' => $mutasi
        ]);
    }

    public function prosesLangsung(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'siswa_id' => 'required|exists:siswa,id',
            'jenis_mutasi' => 'required|in:pindah_kelas,pindah_sekolah,keluar,dikeluarkan',
            'kelas_tujuan_id' => 'required_if:jenis_mutasi,pindah_kelas|nullable|exists:kelas,id',
            'alasan' => 'required_unless:jenis_mutasi,pindah_kelas|nullable|string|max:500',
            'tanggal_mutasi' => 'required_unless:jenis_mutasi,pindah_kelas|nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $siswa = Siswa::findOrFail($request->siswa_id);
            $kelasAsalId = $siswa->kelas_id;

            // Buat record arsip mutasi
            $mutasi = MutasiSiswa::create([
                'siswa_id' => $siswa->id,
                'kelas_asal_id' => $kelasAsalId,
                'kelas_tujuan_id' => $request->jenis_mutasi === 'pindah_kelas' ? $request->kelas_tujuan_id : null,
                'jenis_mutasi' => $request->jenis_mutasi,
                'alasan' => $request->jenis_mutasi === 'pindah_kelas' ? 'Dipindahkan oleh Admin' : $request->alasan,
                'tanggal_mutasi' => $request->jenis_mutasi === 'pindah_kelas' ? date('Y-m-d') : $request->tanggal_mutasi,
                'diajukan_oleh' => $request->user()->id,
                'status_approval' => 'approved',
                'diacc_oleh' => $request->user()->id,
                'catatan_admin' => 'Mutasi langsung oleh Admin'
            ]);

            // Eksekusi mutasi
            if ($request->jenis_mutasi === 'pindah_kelas') {
                $siswa->kelas_id = $request->kelas_tujuan_id;
            } else {
                $siswa->status_siswa = $request->jenis_mutasi;
                $siswa->tanggal_keluar = $request->tanggal_mutasi;
                $siswa->alasan_keluar = $request->alasan;
            }
            $siswa->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Mutasi berhasil diproses dan diarsipkan',
                'data' => $mutasi
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
    public function cancel(Request $request, $id)
    {
        $mutasi = MutasiSiswa::findOrFail($id);

        DB::beginTransaction();
        try {
            $siswa = Siswa::findOrFail($mutasi->siswa_id);

            // Revert changes to siswa
            $siswa->kelas_id = $mutasi->kelas_asal_id;
            $siswa->status_siswa = 'aktif';
            $siswa->tanggal_keluar = null;
            $siswa->alasan_keluar = null;
            $siswa->save();

            // Delete the mutation record
            $mutasi->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Mutasi berhasil dibatalkan dan siswa dikembalikan.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal membatalkan mutasi: ' . $e->getMessage()], 500);
        }
    }
}
