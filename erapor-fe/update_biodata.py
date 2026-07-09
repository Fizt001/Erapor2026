import re

filepath = 'app/pages/siswa/biodata.vue'
with open(filepath, 'r', encoding='utf-8') as f:
    original = f.read()

# Extract view block
view_match = re.search(r'<!-- TAB: VIEW BIODATA -->(.*?)<!-- TAB: EDIT BIODATA -->', original, re.DOTALL)
if view_match:
    view_block = view_match.group(1).strip()
else:
    print('View block not found')
    exit(1)

# Extract edit block
edit_match = re.search(r'<!-- TAB: EDIT BIODATA -->(.*?)</div>\s*</div>\s*<!-- Toast Notification -->', original, re.DOTALL)
if edit_match:
    edit_block = edit_match.group(1).strip()
else:
    print('Edit block not found')
    exit(1)

# Extract toast block
toast_match = re.search(r'<!-- Toast Notification -->(.*?)</template>', original, re.DOTALL)
if toast_match:
    toast_block = toast_match.group(1).strip()
else:
    toast_block = ''

new_template = """<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all hidden xl:flex">
        
        <div class="p-6 shrink-0 relative z-10">
          <div class="bg-gradient-to-r from-indigo-600 to-violet-700 rounded-2xl p-5 border border-indigo-500 shadow-sm relative overflow-hidden flex items-center gap-4">
            <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0 relative z-10">🧑‍🎓</div>
            <div class="relative z-10">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">Biodata Diri</h3>
                <p class="text-[10px] text-indigo-100 font-semibold uppercase mt-0.5">Identitas Siswa</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-20 text-white">
              <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path></svg>
            </div>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-2 custom-scrollbar">
            <button 
                @click="activeTab = 'view'"
                type="button" 
                class="w-full flex items-center gap-4 p-4 rounded-2xl transition-all duration-300 text-left relative group overflow-hidden border"
                :class="activeTab === 'view' ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'bg-white border-slate-100 text-slate-600 hover:bg-slate-50 hover:border-slate-200'"
            >
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl transition-all duration-300 shrink-0 shadow-sm"
                     :class="activeTab === 'view' ? 'bg-white text-indigo-600' : 'bg-slate-100 text-slate-400 group-hover:bg-white group-hover:text-indigo-500'">
                    📄
                </div>
                <div class="overflow-hidden">
                    <p class="font-bold text-xs uppercase tracking-widest truncate">Lihat Biodata</p>
                    <p class="text-[10px] font-semibold truncate mt-0.5" :class="activeTab === 'view' ? 'text-indigo-400' : 'text-slate-400'">Tampilkan Data Diri</p>
                </div>
            </button>
            
            <button 
                @click="activeTab = 'edit'"
                type="button" 
                class="w-full flex items-center gap-4 p-4 rounded-2xl transition-all duration-300 text-left relative group overflow-hidden border"
                :class="activeTab === 'edit' ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'bg-white border-slate-100 text-slate-600 hover:bg-slate-50 hover:border-slate-200'"
            >
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl transition-all duration-300 shrink-0 shadow-sm"
                     :class="activeTab === 'edit' ? 'bg-white text-indigo-600' : 'bg-slate-100 text-slate-400 group-hover:bg-white group-hover:text-indigo-500'">
                    ✍️
                </div>
                <div class="overflow-hidden">
                    <p class="font-bold text-xs uppercase tracking-widest truncate">Edit Biodata</p>
                    <p class="text-[10px] font-semibold truncate mt-0.5" :class="activeTab === 'edit' ? 'text-indigo-400' : 'text-slate-400'">Perbarui Informasi</p>
                </div>
            </button>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0">
        <div class="p-6 lg:p-8 max-w-5xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            
            <!-- Header -->
            <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between sticky top-0 bg-white/80 backdrop-blur-xl z-20 gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-400 to-indigo-600 shadow-sm flex items-center justify-center text-2xl text-white">
                        {{ activeTab === 'view' ? '📄' : '✍️' }}
                    </div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-indigo-700">{{ activeTab === 'view' ? 'Informasi Biodata' : 'Perbarui Biodata' }}</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Kelengkapan Administrasi Sekolah</p>
                    </div>
                </div>
                
                <!-- Tab Switcher (Mobile Only) -->
                <div class="flex xl:hidden items-center bg-slate-100 p-1 rounded-xl">
                  <button @click="activeTab = 'view'" :class="['px-4 py-2 text-xs font-bold rounded-lg transition-all', activeTab === 'view' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500']">Lihat</button>
                  <button @click="activeTab = 'edit'" :class="['px-4 py-2 text-xs font-bold rounded-lg transition-all', activeTab === 'edit' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500']">Edit</button>
                </div>
            </div>

            <!-- Loading -->
            <div v-if="isLoading" class="flex-1 flex justify-center items-center">
              <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
            </div>

            <div v-else-if="formData" class="flex-1 overflow-y-auto custom-scrollbar p-6 md:p-10 relative z-0">
              <!-- TAB: VIEW BIODATA -->
              _VIEW_BLOCK_
              
              <!-- TAB: EDIT BIODATA -->
              _EDIT_BLOCK_
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    _TOAST_BLOCK_
  </div>
</template>"""

# Replace colors in the extracted blocks
view_block = view_block.replace('emerald', 'indigo')
edit_block = edit_block.replace('emerald', 'indigo')

# Inject blocks into the new template
new_template = new_template.replace('_VIEW_BLOCK_', view_block)
new_template = new_template.replace('_EDIT_BLOCK_', edit_block)
new_template = new_template.replace('_TOAST_BLOCK_', toast_block)

# Replace the template section in the original file
new_content = re.sub(r'<template>.*?</template>', new_template, original, flags=re.DOTALL)

# Write to file
with open(filepath, 'w', encoding='utf-8') as f:
    f.write(new_content)

print('Updated biodata layout successfully.')
