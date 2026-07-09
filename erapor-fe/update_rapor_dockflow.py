import re

filepath = 'app/pages/siswa/rapor.vue'
with open(filepath, 'r', encoding='utf-8') as f:
    original = f.read()

# Extract the INLINE RAPOR VIEW block
rapor_view_match = re.search(r'<!-- INLINE RAPOR VIEW -->(.*?)</div>\s*</div>\s*</template>', original, re.DOTALL)
if rapor_view_match:
    rapor_view_block = rapor_view_match.group(1).strip()
    
    # We want to remove the outer wrapper from rapor_view_block because we will provide our own inside the flow panel
    # The outer wrapper is: <div v-if="raporData && !isPreviewLoading" class="animate-fadeInUp bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden mb-12">
    # And its closing </div>
    inner_match = re.search(r'<div v-if="raporData && !isPreviewLoading"[^>]*>(.*)</div>', rapor_view_block, re.DOTALL)
    if inner_match:
        rapor_view_content = inner_match.group(1).strip()
        # Also remove the header with "Pratinjau Nilai Rapor" and "Tutup" button since we don't need it in flow layout
        # <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex items-center justify-between">...</div>
        header_match = re.search(r'<div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex items-center justify-between">.*?</div>(.*)', rapor_view_content, re.DOTALL)
        if header_match:
            rapor_view_content = header_match.group(1).strip()
            
else:
    print('Rapor view block not found')
    exit(1)


new_template = """<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    
    <!-- Loading State Awal -->
    <div v-if="isLoading" class="flex-1 flex flex-col items-center justify-center">
      <div class="w-10 h-10 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
      <p class="mt-4 text-sm font-semibold text-slate-500 animate-pulse">Memuat data periode rapor...</p>
    </div>
    
    <!-- Error State Awal -->
    <div v-else-if="errorMessage" class="flex-1 flex flex-col items-center justify-center text-center">
      <div class="text-red-500 mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>
      <h3 class="text-lg font-bold text-slate-800">Gagal Memuat Data</h3>
      <p class="text-sm text-slate-500 mt-1 max-w-md">{{ errorMessage }}</p>
      <button @click="loadTitimangsas" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold shadow-sm hover:bg-indigo-700">Coba Lagi</button>
    </div>
    
    <!-- Empty State Awal -->
    <div v-else-if="!pageData?.titimangsas?.length" class="flex-1 flex flex-col items-center justify-center text-center">
      <div class="text-5xl mb-4">📭</div>
      <h3 class="text-lg font-bold text-slate-700">Belum Ada Rapor</h3>
      <p class="text-slate-500 text-sm mt-1">Belum ada periode rapor yang memuat nilai Anda.</p>
    </div>

    <!-- Layout 2 Panel Dock & Flow -->
    <div v-else class="flex-1 flex overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all hidden xl:flex">
        
        <div class="p-6 shrink-0 relative z-10">
          <div class="bg-gradient-to-r from-indigo-600 to-violet-700 rounded-2xl p-5 border border-indigo-500 shadow-sm relative overflow-hidden flex items-center gap-4">
            <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0 relative z-10">📄</div>
            <div class="relative z-10">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">Rapor Saya</h3>
                <p class="text-[10px] text-indigo-100 font-semibold uppercase mt-0.5">Pilih Periode Rapor</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-20 text-white">
              <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg>
            </div>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-2 custom-scrollbar">
            <button 
                v-for="t in pageData.titimangsas" 
                :key="t.id"
                @click="pilihTitimangsa(t.id)"
                type="button" 
                class="w-full flex items-center gap-4 p-4 rounded-2xl transition-all duration-300 text-left relative group overflow-hidden border"
                :class="activeTitimangsaId === t.id ? 'bg-indigo-50 border-indigo-200 text-indigo-700 shadow-sm' : 'bg-white border-slate-100 text-slate-600 hover:bg-slate-50 hover:border-slate-200'"
            >
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl transition-all duration-300 shrink-0 shadow-sm"
                     :class="activeTitimangsaId === t.id ? 'bg-white text-indigo-600' : 'bg-slate-100 text-slate-400 group-hover:bg-white group-hover:text-indigo-500'">
                    📋
                </div>
                <div class="overflow-hidden flex-1">
                    <p class="font-bold text-xs uppercase tracking-widest truncate">{{ t.nama_periode }}</p>
                    <p class="text-[10px] font-semibold truncate mt-0.5" :class="activeTitimangsaId === t.id ? 'text-indigo-400' : 'text-slate-400'">{{ !t.is_aktif ? 'Riwayat Rapor' : 'Periode Aktif' }}</p>
                </div>
                <div v-if="activeTitimangsaId === t.id" class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></div>
            </button>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0">
        <div class="p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            
            <!-- Header Flow -->
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white/80 backdrop-blur-xl z-20 gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-400 to-indigo-600 shadow-sm flex items-center justify-center text-2xl text-white">
                        🔍
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-indigo-700">Preview Rapor</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">{{ raporData?.nama_periode || 'Pilih Periode di Samping' }}</p>
                    </div>
                </div>
                
                <!-- Mobile Selector -->
                <div class="xl:hidden w-48">
                    <select 
                      v-model="activeTitimangsaId"
                      @change="bukaPreviewRapor"
                      class="w-full text-xs font-bold text-slate-700 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 outline-none"
                    >
                      <option v-for="t in pageData.titimangsas" :key="t.id" :value="t.id">
                        {{ t.nama_periode }}
                      </option>
                    </select>
                </div>
            </div>

            <!-- Loading Preview -->
            <div v-if="isPreviewLoading" class="flex-1 flex flex-col justify-center items-center">
              <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
              <p class="text-xs font-bold text-slate-500 mt-4 animate-pulse">Menarik data rapor...</p>
            </div>

            <!-- Empty Preview -->
            <div v-else-if="!raporData" class="flex-1 flex flex-col justify-center items-center text-center p-8">
              <div class="text-4xl mb-3 opacity-50">👆</div>
              <p class="text-sm font-bold text-slate-500">Pilih periode rapor di panel sebelah kiri untuk melihat nilainya.</p>
            </div>

            <!-- Rapor Content -->
            <div v-else class="flex-1 overflow-y-auto custom-scrollbar bg-slate-100 p-4 md:p-8 relative z-0">
               <div class="bg-white p-6 md:p-12 rounded-xl shadow-sm border border-slate-200">
                  _RAPOR_VIEW_CONTENT_
               </div>
            </div>
            
          </div>
        </div>
      </div>
    </div>
    
  </div>
</template>"""

new_template = new_template.replace('_RAPOR_VIEW_CONTENT_', rapor_view_content)

# We also need to add `pilihTitimangsa` function to the script
script_replacement = """
const pilihTitimangsa = (id) => {
    activeTitimangsaId.value = id;
    bukaPreviewRapor();
}

onMounted(() => {
"""

# Replace template
new_content = re.sub(r'<template>.*?</template>', new_template, original, flags=re.DOTALL)

# Add method to script
new_content = new_content.replace('onMounted(() => {', script_replacement)

# Write to file
with open(filepath, 'w', encoding='utf-8') as f:
    f.write(new_content)

print('Updated rapor layout to dock & flow.')
