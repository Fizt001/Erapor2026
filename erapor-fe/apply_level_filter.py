import re

# --- UPDATE API CONTROLLER ---
api_file = 'd:\\koding\\Erapor2026\\erapor-api\\app\\Http\\Controllers\\Api\\Siswa\\SiswaRaporController.php'
with open(api_file, 'r', encoding='utf-8') as f:
    api_content = f.read()

api_replacement = """
        // Ambil titimangsa dari nilai sumatif yang pernah didapatkan
        $sumatifs = \\App\\Models\\SumatifNilai::with('kelas')
            ->where('siswa_id', $siswa->id)
            ->select('titimangsa_id', 'kelas_id')
            ->get();

        $titimangsaTingkat = [];
        foreach($sumatifs as $s) {
            if($s->kelas) {
                $titimangsaTingkat[$s->titimangsa_id] = $s->kelas->tingkat;
            }
        }

        // Tambahkan titimangsa aktif saat ini dengan tingkat kelas siswa sekarang
        $activeTitimangsa = Titimangsa::where('is_aktif', 1)->first();
        $currentTingkat = $siswa->kelas->tingkat ?? 'X';
        
        if($activeTitimangsa) {
            $titimangsaTingkat[$activeTitimangsa->id] = $currentTingkat;
        }

        // Ambil data titimangsa
        $titimangsas = Titimangsa::whereIn('id', array_keys($titimangsaTingkat))
            ->orderBy('id', 'desc')
            ->get()
            ->map(function($t) use ($titimangsaTingkat) {
                $t->tingkat = $titimangsaTingkat[$t->id] ?? 'X';
                return $t;
            });

        return response()->json([
            'success' => true,
            'data' => [
                'titimangsas' => $titimangsas,
                'current_tingkat' => $currentTingkat
            ]
        ]);
"""

# Replace the index method contents
match = re.search(r'// Ambil ID titimangsa dimana siswa memiliki nilai sumatif.*?\]\]\);', api_content, flags=re.DOTALL)
if match:
    api_content = api_content.replace(match.group(0), api_replacement.strip())
else:
    print("API Match not found")

with open(api_file, 'w', encoding='utf-8') as f:
    f.write(api_content)


# --- UPDATE VUE TEMPLATE ---
vue_file = 'd:\\koding\\Erapor2026\\erapor-fe\\app\\pages\\siswa\\rapor.vue'
with open(vue_file, 'r', encoding='utf-8') as f:
    vue_content = f.read()

# Add the activeTingkat ref
vue_content = vue_content.replace('const activeTitimangsaId = ref(\'\')', "const activeTitimangsaId = ref('')\nconst activeTingkat = ref('X')")

# Add the computed for filteredTitimangsas
script_setup = """import { ref, onMounted, computed } from 'vue'

definePageMeta({
  layout: 'siswa',
  middleware: 'siswa',
  title: 'Rapor Saya'
})

const pageData = ref(null)
const activeTitimangsaId = ref('')
const activeTingkat = ref('X')
const isLoading = ref(true)
const isPreviewLoading = ref(false)
const errorMessage = ref('')

const filteredTitimangsas = computed(() => {
    if (!pageData.value || !pageData.value.titimangsas) return [];
    return pageData.value.titimangsas.filter(t => t.tingkat === activeTingkat.value);
})
"""
match_script = re.search(r"import \{ ref, onMounted \} from 'vue'.*?const errorMessage = ref\(''\)", vue_content, flags=re.DOTALL)
if match_script:
    vue_content = vue_content.replace(match_script.group(0), script_setup)
else:
    print("Vue Script Match not found")

# Update the loadTitimangsas method to set activeTingkat to current_tingkat
load_replacement = """
        if (res.success) {
            pageData.value = res.data
            
            // Set default tab to current tingkat
            if (res.data.current_tingkat) {
                activeTingkat.value = res.data.current_tingkat;
            }
            
            if (res.data.titimangsas && res.data.titimangsas.length > 0) {
                // Find first item matching active tingkat
                const firstMatch = res.data.titimangsas.find(t => t.tingkat === activeTingkat.value);
                if (firstMatch) {
                    activeTitimangsaId.value = firstMatch.id;
                    setTimeout(() => { bukaPreviewRapor(); }, 300);
                }
            }
        }
"""
match_load = re.search(r'if \(res\.success\) \{.*?if \(res\.data\.titimangsas && res\.data\.titimangsas\.length > 0\) \{.*?activeTitimangsaId\.value = res\.data\.titimangsas\[0\]\.id\n.*?\}\n.*?\}', vue_content, flags=re.DOTALL)
if match_load:
    vue_content = vue_content.replace(match_load.group(0), load_replacement.strip())
else:
    print("Vue Load Match not found")

# Update the template to include the tabs in the left panel
tabs_html = """
        <div class="p-4 shrink-0 relative z-10 border-b border-slate-100 bg-white">
          <div class="flex items-center gap-1 bg-slate-100 p-1.5 rounded-xl border border-slate-200/60 shadow-inner">
            <button @click="pindahTingkat('X')" :class="activeTingkat === 'X' ? 'bg-white shadow-sm text-indigo-700 ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'" class="flex-1 py-2 rounded-lg text-xs font-black uppercase tracking-wider transition-all duration-300">Kelas X</button>
            <button @click="pindahTingkat('XI')" :class="activeTingkat === 'XI' ? 'bg-white shadow-sm text-indigo-700 ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'" class="flex-1 py-2 rounded-lg text-xs font-black uppercase tracking-wider transition-all duration-300">Kelas XI</button>
            <button @click="pindahTingkat('XII')" :class="activeTingkat === 'XII' ? 'bg-white shadow-sm text-indigo-700 ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'" class="flex-1 py-2 rounded-lg text-xs font-black uppercase tracking-wider transition-all duration-300">Kelas XII</button>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-2 custom-scrollbar">
"""
vue_content = vue_content.replace('<div class="flex-1 overflow-y-auto p-4 space-y-2 custom-scrollbar">', tabs_html)

# Add pindahTingkat method
pindah_method = """
const pindahTingkat = (tingkat) => {
    activeTingkat.value = tingkat;
    const firstMatch = filteredTitimangsas.value[0];
    if (firstMatch) {
        activeTitimangsaId.value = firstMatch.id;
        bukaPreviewRapor();
    } else {
        activeTitimangsaId.value = '';
        raporData.value = null;
    }
}

const pilihTitimangsa = (id) => {
"""
vue_content = vue_content.replace('const pilihTitimangsa = (id) => {', pindah_method)

# Change v-for from pageData.titimangsas to filteredTitimangsas
vue_content = vue_content.replace('v-for="t in pageData.titimangsas"', 'v-for="t in filteredTitimangsas"')

# Update empty state for empty filteredTitimangsas
empty_state_html = """
            <!-- Empty Preview / Not In Class Yet -->
            <div v-else-if="filteredTitimangsas.length === 0" class="flex-1 flex flex-col justify-center items-center text-center p-8 bg-gradient-to-br from-indigo-50/50 via-white to-white m-6 rounded-3xl border border-indigo-100/50 shadow-sm relative overflow-hidden">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-[0.03]"></div>
                <div class="w-32 h-32 mb-6 relative z-10">
                    <div class="absolute inset-0 bg-indigo-200 rounded-full animate-ping opacity-20 duration-1000"></div>
                    <div class="w-full h-full bg-gradient-to-br from-indigo-100 to-indigo-50 rounded-full flex items-center justify-center text-6xl shadow-inner border-4 border-white relative z-10 transform hover:scale-105 transition-transform">
                        ⏳
                    </div>
                    <div class="absolute -top-2 -right-2 bg-white p-2 rounded-xl shadow-md rotate-12 text-2xl z-20 animate-bounce">🔒</div>
                </div>
                <h3 class="text-2xl font-black text-indigo-900 mb-3 tracking-tight relative z-10">Sabar Dulu Ya! 🚀</h3>
                <p class="text-sm font-semibold text-slate-500 max-w-sm leading-relaxed relative z-10">
                    Saat ini kamu masih berada di <span class="text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded shadow-sm border border-indigo-100">Kelas {{ pageData?.current_tingkat || 'X' }}</span>.<br>
                    Data Rapor untuk <span class="text-indigo-600 font-bold underline decoration-indigo-200 underline-offset-4">Kelas {{ activeTingkat }}</span> belum tersedia.
                </p>
                <div class="mt-8 px-6 py-3 bg-white rounded-2xl border border-slate-100 shadow-sm inline-flex items-center gap-3 relative z-10">
                    <span class="text-xl">✨</span>
                    <span class="text-slate-400 text-xs italic font-bold tracking-wide">"Tetap semangat belajar untuk menuju jenjang ini!"</span>
                </div>
            </div>

            <!-- Select Period State -->
            <div v-else-if="!raporData" class="flex-1 flex flex-col justify-center items-center text-center p-8">
              <div class="text-4xl mb-3 opacity-50 animate-bounce">👆</div>
              <p class="text-sm font-bold text-slate-500">Pilih periode rapor di panel sebelah kiri untuk melihat nilainya.</p>
            </div>
"""
match_empty = re.search(r'<!-- Empty Preview -->.*?</div>\s*</div>\s*<!-- Rapor Content -->', vue_content, flags=re.DOTALL)
if match_empty:
    vue_content = vue_content.replace(match_empty.group(0), empty_state_html.strip() + "\n\n            <!-- Rapor Content -->")
else:
    print("Vue Empty Match not found")


with open(vue_file, 'w', encoding='utf-8') as f:
    f.write(vue_content)

print("Updated Rapor API and Vue to support Level Tabs (Tingkat X, XI, XII).")
