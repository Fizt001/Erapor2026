import re

file_path = r'd:\koding\Erapor2026\erapor-api\app\Http\Controllers\Api\AdminBukuIndukController.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Find getBiodataByKurikulum
start_str = "    public function getBiodataByKurikulum(Request $request)"
end_str = "    public function getNilaiSiswa($siswa_id)"

if start_str in content and end_str in content:
    before = content.split(start_str)[0]
    after = end_str + content.split(end_str)[1]
    
    new_method = """    public function getBiodataByKurikulum(Request $request)
    {
        $tahun_ajaran_id = $request->query('tahun_ajaran_id');
        $kurikulum_id = $request->query('kurikulum_id');

        if (!$tahun_ajaran_id || !$kurikulum_id) {
            return response()->json(['success' => false, 'message' => 'Tahun Ajaran dan Kurikulum harus diisi'], 400);
        }

        $siswas = \App\Models\Siswa::whereHas('kelas', function($q) use ($tahun_ajaran_id, $kurikulum_id) {
            $q->where('tahun_ajaran_id', $tahun_ajaran_id)
              ->where('kurikulum_id', $kurikulum_id);
        })
        ->with('user')
        ->get()
        ->sortBy(function($s) {
            return $s->name;
        })
        ->values();

        $fields = [
            'nis', 'nisn', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 
            'anak_ke', 'status_keluarga', 'warga_negara', 'agama', 'alamat', 'no_hp',
            'nama_ayah', 'ttl_ayah', 'pekerjaan_ayah', 'no_hp_ayah', 
            'nama_ibu', 'ttl_ibu', 'pekerjaan_ibu', 'no_hp_ibu', 
            'nama_wali', 'pekerjaan_wali', 'no_hp_wali', 'alamat_wali',
            'asal_sekolah', 'alamat_sekolah_asal', 'no_sttb_smp', 'tgl_sttb_smp',
            'tanggal_masuk', 'kelas_masuk', 'tanggal_keluar', 'alasan_keluar',
            'no_sttb_smk', 'tgl_sttb_smk', 'tempat_pkl', 'alamat_pkl', 'tgl_mulai_pkl', 'tgl_selesai_pkl'
        ];

        $siswaData = $siswas->map(function ($s) use ($fields) {
            $data = [
                'id' => $s->id,
                'kelas_id' => $s->kelas_id,
                'nama_lengkap' => $s->name,
            ];
            foreach ($fields as $field) {
                $data[$field] = $s->$field;
            }
            return $data;
        });

        return response()->json(['success' => true, 'data' => $siswaData]);
    }

"""
    
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(before + new_method + after)
    print("Method updated successfully")
else:
    print("Could not find method boundaries")
