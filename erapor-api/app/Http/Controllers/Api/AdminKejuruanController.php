<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Models\Program;
use App\Models\Kejuruan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminKejuruanController extends Controller
{
    // === 1. GET ALL HIERARCHY ===
    public function index()
    {
        // Load with nested relations
        $data = Bidang::with('programs.kejuruans')->get();
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // === 2. BIDANG CRUD ===
    public function storeBidang(Request $request)
    {
        $request->validate(['nama_bidang' => 'required|string|max:255']);
        $bidang = Bidang::create(['nama_bidang' => $request->nama_bidang]);
        return response()->json(['success' => true, 'message' => 'Bidang berhasil ditambahkan', 'data' => $bidang]);
    }

    public function updateBidang(Request $request, $id)
    {
        $request->validate(['nama_bidang' => 'required|string|max:255']);
        $bidang = Bidang::findOrFail($id);
        $bidang->update(['nama_bidang' => $request->nama_bidang]);
        return response()->json(['success' => true, 'message' => 'Bidang berhasil diperbarui', 'data' => $bidang]);
    }

    public function destroyBidang($id)
    {
        $bidang = Bidang::findOrFail($id);
        $bidang->delete(); // Cascade on delete handles the children
        return response()->json(['success' => true, 'message' => 'Bidang berhasil dihapus']);
    }

    // === 3. PROGRAM CRUD ===
    public function storeProgram(Request $request)
    {
        $request->validate([
            'bidang_id' => 'required|exists:bidangs,id',
            'nama_program' => 'required|string|max:255'
        ]);
        $program = Program::create($request->only('bidang_id', 'nama_program'));
        return response()->json(['success' => true, 'message' => 'Program berhasil ditambahkan', 'data' => $program]);
    }

    public function updateProgram(Request $request, $id)
    {
        $request->validate([
            'bidang_id' => 'required|exists:bidangs,id',
            'nama_program' => 'required|string|max:255'
        ]);
        $program = Program::findOrFail($id);
        $program->update($request->only('bidang_id', 'nama_program'));
        return response()->json(['success' => true, 'message' => 'Program berhasil diperbarui', 'data' => $program]);
    }

    public function destroyProgram($id)
    {
        $program = Program::findOrFail($id);
        $program->delete();
        return response()->json(['success' => true, 'message' => 'Program berhasil dihapus']);
    }

    // === 4. KONSENTRASI/KEJURUAN CRUD ===
    public function storeKejuruan(Request $request)
    {
        $request->validate([
            'program_id'       => 'required|exists:programs,id',
            'kode_konsentrasi' => ['required', 'digits:3', 'unique:kejuruans,kode_konsentrasi'],
            'nama_konsentrasi' => 'required|string|max:255',
        ]);
        $kejuruan = Kejuruan::create($request->only('program_id', 'kode_konsentrasi', 'nama_konsentrasi'));
        return response()->json(['success' => true, 'message' => 'Konsentrasi berhasil ditambahkan', 'data' => $kejuruan]);
    }

    public function updateKejuruan(Request $request, $id)
    {
        $request->validate([
            'program_id'       => 'required|exists:programs,id',
            'kode_konsentrasi' => ['required', 'digits:3', Rule::unique('kejuruans', 'kode_konsentrasi')->ignore($id)],
            'nama_konsentrasi' => 'required|string|max:255',
        ]);
        $kejuruan = Kejuruan::findOrFail($id);
        $kejuruan->update($request->only('program_id', 'kode_konsentrasi', 'nama_konsentrasi'));
        return response()->json(['success' => true, 'message' => 'Konsentrasi berhasil diperbarui', 'data' => $kejuruan]);
    }

    // === 5. LIST KEJURUAN UNTUK DROPDOWN MAPEL PRODUKTIF ===
    public function getKejuruanList()
    {
        $kejuruans = Kejuruan::with('program.bidang')
            ->whereRaw('kode_konsentrasi REGEXP \'^\'[0-9]{3}\'\'')  // only 3-digit numeric codes
            ->orderBy('kode_konsentrasi')
            ->get()
            ->map(function ($k) {
                return [
                    'id'               => $k->id,
                    'kode'             => $k->kode_konsentrasi,
                    'nama_konsentrasi' => $k->nama_konsentrasi,
                    'nama_program'     => optional($k->program)->nama_program,
                    'label'            => $k->kode_konsentrasi . ' — ' . $k->nama_konsentrasi,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $kejuruans
        ]);
    }

    public function destroyKejuruan($id)
    {
        $kejuruan = Kejuruan::findOrFail($id);
        $kejuruan->delete();
        return response()->json(['success' => true, 'message' => 'Konsentrasi berhasil dihapus']);
    }
}
