<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    
<!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-2 grid grid-cols-4 gap-2 z-20">
        <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTab = tab.id"
          :class="activeTab === tab.id ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/20 ring-2 ring-emerald-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="rounded-xl flex flex-col items-center justify-center py-2 px-1 transition-all active:scale-95">
          <AppIcon :name="tab.iconName" class="text-lg mb-0.5 transition-transform" :class="activeTab === tab.id ? 'scale-110' : ''" />
          <span class="text-[9px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
        </button>
      </div>

      <!-- Panel Dock Kiri -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all', activeTab === 'bidang' || activeTab === 'program' || activeTab === 'konsentrasi' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[76px]' : '']">
        
        <!-- Desktop Tabs (Toggle Form) -->
        <div class="hidden xl:flex bg-slate-50/50 border-b border-slate-200 p-2 shrink-0">
            <button @click="activeTab = 'bidang'" :class="activeTab === 'bidang' ? 'bg-white text-emerald-700 shadow-sm border border-slate-200' : 'bg-transparent text-slate-500 hover:bg-slate-50 border border-transparent'" class="flex-1 py-2 rounded-xl font-bold text-[10px] uppercase tracking-widest transition-all">
                <AppIcon name="cog" />️ Bidang
            </button>
            <button @click="activeTab = 'program'" :class="activeTab === 'program' ? 'bg-white text-emerald-700 shadow-sm border border-slate-200' : 'bg-transparent text-slate-500 hover:bg-slate-50 border border-transparent'" class="flex-1 py-2 rounded-xl font-bold text-[10px] uppercase tracking-widest transition-all">
                <AppIcon name="academic-cap" /> Prog
            </button>
            <button @click="activeTab = 'konsentrasi'" :class="activeTab === 'konsentrasi' ? 'bg-white text-emerald-700 shadow-sm border border-slate-200' : 'bg-transparent text-slate-500 hover:bg-slate-50 border border-transparent'" class="flex-1 py-2 rounded-xl font-bold text-[10px] uppercase tracking-widest transition-all">
                <AppIcon name="shield" /> Fokus
            </button>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <!-- Panel Form Bidang -->
            <div v-show="activeTab === 'bidang'" class="animate-fadeIn">
                <div class="p-6 shrink-0">
                  <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-2xl p-5 border border-emerald-500 shadow-sm relative overflow-hidden flex items-center gap-4">
                    <div class="w-10 h-10 flex items-center justify-center text-xl shrink-0 bg-white/20 rounded-xl backdrop-blur-sm border border-white/20 relative z-10"><AppIcon name="cog" />️</div>
                    <div class="relative z-10">
                        <h3 class="text-sm font-black uppercase tracking-widest text-white">Bidang Keahlian</h3>
                        <p class="text-[10px] text-emerald-100 font-semibold uppercase mt-0.5">Tingkatan 1 (Tertinggi)</p>
                    </div>
                    <div class="absolute right-0 bottom-0 opacity-20 text-white">
                      <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.5 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"></path></svg>
                    </div>
                  </div>
                </div>
                <div class="px-6 pb-6">
                    <form @submit.prevent="saveBidang" class="space-y-4">
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Nama Bidang</label>
                            <input type="text" v-model="bidangForm.nama_bidang" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800" placeholder="Misal: Teknologi Informasi">
                        </div>
                        <div class="pt-4 border-t border-slate-100">
                            <button type="submit" :disabled="isSaving" class="w-full py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-2xl shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 uppercase tracking-widest text-xs">
                                <span v-if="isSaving" class="animate-spin text-lg"><AppIcon name="clock" /></span>
                                <span v-else class="text-lg"><AppIcon name="save" /></span> Simpan Bidang
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Panel Form Program -->
            <div v-show="activeTab === 'program'" class="animate-fadeIn">
                <div class="p-6 shrink-0">
                  <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-5 border border-blue-500 shadow-sm relative overflow-hidden flex items-center gap-4">
                    <div class="w-10 h-10 flex items-center justify-center text-xl shrink-0 bg-white/20 rounded-xl backdrop-blur-sm border border-white/20 relative z-10"><AppIcon name="academic-cap" /></div>
                    <div class="relative z-10">
                        <h3 class="text-sm font-black uppercase tracking-widest text-white">Program Keahlian</h3>
                        <p class="text-[10px] text-blue-100 font-semibold uppercase mt-0.5">Tingkatan 2 (Menengah)</p>
                    </div>
                    <div class="absolute right-0 bottom-0 opacity-20 text-white">
                      <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"></path></svg>
                    </div>
                  </div>
                </div>
                <div class="px-6 pb-6">
                    <form @submit.prevent="saveProgram" class="space-y-4">
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pilih Induk (Bidang)</label>
                            <select v-model="programForm.bidang_id" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-bold text-slate-700 cursor-pointer appearance-none">
                                <option value="" disabled>-- Pilih Bidang Keahlian --</option>
                                <option v-for="b in treeData" :key="b.id" :value="b.id">{{ b.nama_bidang }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Nama Program</label>
                            <input type="text" v-model="programForm.nama_program" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800" placeholder="Misal: Teknik Komputer dan Informatika">
                        </div>
                        <div class="pt-4 border-t border-slate-100">
                            <button type="submit" :disabled="isSaving" class="w-full py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-2xl shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 uppercase tracking-widest text-xs">
                                <span v-if="isSaving" class="animate-spin text-lg"><AppIcon name="clock" /></span>
                                <span v-else class="text-lg"><AppIcon name="save" /></span> Simpan Program
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Panel Form Konsentrasi -->
            <div v-show="activeTab === 'konsentrasi'" class="animate-fadeIn">
                <div class="p-6 shrink-0">
                  <div class="bg-gradient-to-r from-purple-600 to-fuchsia-700 rounded-2xl p-5 border border-purple-500 shadow-sm relative overflow-hidden flex items-center gap-4">
                    <div class="w-10 h-10 flex items-center justify-center text-xl shrink-0 bg-white/20 rounded-xl backdrop-blur-sm border border-white/20 relative z-10"><AppIcon name="shield" /></div>
                    <div class="relative z-10">
                        <h3 class="text-sm font-black uppercase tracking-widest text-white">Konsentrasi Keahlian</h3>
                        <p class="text-[10px] text-purple-100 font-semibold uppercase mt-0.5">Tingkatan 3 (Kejuruan/Jurusan)</p>
                    </div>
                    <div class="absolute right-0 bottom-0 opacity-20 text-white">
                      <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm0 10c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4z"></path></svg>
                    </div>
                  </div>
                </div>
                <div class="px-6 pb-6">
                    <form @submit.prevent="saveKejuruan" class="space-y-4">
                        <div>
                            <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pilih Induk (Program)</label>
                            <select v-model="kejuruanForm.program_id" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-bold text-slate-700 cursor-pointer appearance-none">
                                <option value="" disabled>-- Pilih Program Keahlian --</option>
                                <option v-for="p in flatProgramList" :key="p.id" :value="p.id">{{ p.nama_program }}</option>
                            </select>
                        </div>
                        <div class="space-y-5">
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kode Konsentrasi</label>
                                <input type="text" v-model="kejuruanForm.kode_konsentrasi" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-black uppercase text-slate-800" placeholder="Contoh: TKJ">
                            </div>
                            <div>
                                <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Nama Lengkap Konsentrasi</label>
                                <input type="text" v-model="kejuruanForm.nama_konsentrasi" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-semibold text-slate-800" placeholder="Misal: Teknik Komputer dan Jaringan">
                            </div>
                        </div>
                        <div class="pt-4 border-t border-slate-100">
                            <button type="submit" :disabled="isSaving" class="w-full py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold rounded-2xl shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 uppercase tracking-widest text-xs">
                                <span v-if="isSaving" class="animate-spin text-lg"><AppIcon name="clock" /></span>
                                <span v-else class="text-lg"><AppIcon name="save" /></span> Simpan Konsentrasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative', activeTab === 'table' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[76px]' : '']">
        <div class="p-2 sm:p-6 lg:p-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-2xl sm:rounded-[2rem] shadow-[0_2px_15px_-3px_rgba(0,0,0,0.05)] overflow-hidden flex flex-col flex-1 relative min-h-0">
            <div class="px-6 py-5 border-b border-slate-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white shrink-0 z-10">
                <div class="flex items-center gap-4 w-full sm:w-auto">
                    <div class="w-10 h-10 rounded-2xl bg-slate-50 shadow-sm border border-slate-200 flex items-center justify-center text-xl hidden sm:flex"><AppIcon name="clipboard" /></div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-emerald-700">Database Kejuruan</h3>
                    </div>
                </div>
                <div class="flex gap-2 w-full sm:w-auto justify-end">
                        <button @click="expandAll" class="w-1/2 sm:w-auto px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 shadow-sm transition-all">Buka Semua</button>
                        <button @click="collapseAll" class="w-1/2 sm:w-auto px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 shadow-sm transition-all">Tutup Semua</button>
                </div>
            </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60">
            <div class="w-8 h-8 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mb-4"></div>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Memuat Struktur...</span>
        </div>

        <!-- Tree View Data -->
        <div v-else class="flex-1 overflow-y-auto custom-scrollbar p-4 md:p-6 bg-slate-50">
            
            <div v-if="treeData.length === 0" class="text-center py-16 flex flex-col items-center justify-center h-full min-h-[400px]">
                <div class="text-6xl opacity-30 mb-4 block"><AppIcon name="empty-state" /></div>
                <h3 class="text-lg font-black text-slate-700 mb-2 uppercase tracking-widest">Data Kosong</h3>
                <p class="text-slate-500 text-sm font-semibold max-w-sm">Belum ada struktur kejuruan.<br>Mulai dengan menambahkan Bidang Keahlian di panel sebelah kiri.</p>
            </div>

            <!-- LOOP 1: BIDANG -->
            <div v-for="b in treeData" :key="'b-'+b.id" class="mb-4 bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden animate-slideUpFade">
                <!-- Bidang Header -->
                <div class="flex items-center justify-between p-4 cursor-pointer hover:bg-slate-50 transition-colors group/bidang" @click="toggleNode('b-'+b.id)">
                    <div class="flex items-center gap-4 flex-1">
                        <span class="w-6 h-6 flex items-center justify-center rounded-md bg-slate-100 text-slate-500 transition-transform" :class="isNodeExpanded('b-'+b.id) ? 'rotate-90' : ''"><AppIcon name="arrow-right" /></span>
                        <div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 bg-slate-100 px-2 py-0.5 rounded mr-2 border border-slate-200">BIDANG</span>
                            <span class="text-sm font-bold text-slate-800">{{ b.nama_bidang }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 opacity-100 sm:opacity-0 sm:group-hover/bidang:opacity-100 transition-opacity">
                        <button @click.stop="openEdit('bidang', b)" class="w-8 h-8 flex items-center justify-center rounded-xl text-emerald-600 hover:bg-emerald-50 border border-transparent hover:border-emerald-200 shadow-sm" title="Edit Bidang">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                        <button @click.stop="confirmDelete('bidang', b.id, b.nama_bidang)" class="w-8 h-8 flex items-center justify-center rounded-xl text-rose-500 hover:bg-rose-50 border border-transparent hover:border-rose-200 shadow-sm" title="Hapus Bidang">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                    </div>
                </div>

                <!-- Programs Container (Collapsible) -->
                <div v-show="isNodeExpanded('b-'+b.id)" class="border-t border-slate-100 bg-slate-50/50 p-4 pl-12 space-y-3 relative">
                    <!-- Line Guide -->
                    <div class="absolute left-7 top-0 bottom-6 w-px bg-slate-200"></div>
                    
                    <div v-if="!b.programs || b.programs.length === 0" class="text-[10px] font-bold text-slate-400 italic">Belum ada program keahlian.</div>

                    <!-- LOOP 2: PROGRAM -->
                    <div v-for="p in b.programs" :key="'p-'+p.id" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden relative group">
                        <!-- Program Header -->
                        <div class="flex items-center justify-between p-3 cursor-pointer hover:bg-slate-50 transition-colors group/program" @click="toggleNode('p-'+p.id)">
                            <div class="flex items-center gap-3 flex-1">
                                <span class="w-5 h-5 flex items-center justify-center rounded bg-slate-100 text-slate-400 transition-transform text-[10px]" :class="isNodeExpanded('p-'+p.id) ? 'rotate-90' : ''"><AppIcon name="arrow-right" /></span>
                                <div>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 bg-slate-100 px-2 py-0.5 rounded mr-2 border border-slate-200">PROGRAM</span>
                                    <span class="text-xs font-bold text-slate-700">{{ p.nama_program }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 opacity-100 sm:opacity-0 sm:group-hover/program:opacity-100 transition-opacity">
                                <button @click.stop="openEdit('program', p)" class="w-7 h-7 flex items-center justify-center rounded-lg text-emerald-600 hover:bg-emerald-50 text-xs border border-transparent hover:border-emerald-200 shadow-sm" title="Edit Program">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                <button @click.stop="confirmDelete('program', p.id, p.nama_program)" class="w-7 h-7 flex items-center justify-center rounded-lg text-rose-500 hover:bg-rose-50 text-xs border border-transparent hover:border-rose-200 shadow-sm" title="Hapus Program">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Kejuruans Container (Collapsible) -->
                        <div v-show="isNodeExpanded('p-'+p.id)" class="border-t border-slate-100 bg-slate-50 p-3 pl-10 space-y-2 relative">
                            <div class="absolute left-5 top-0 bottom-4 w-px bg-slate-200"></div>
                            
                            <div v-if="!p.kejuruans || p.kejuruans.length === 0" class="text-[10px] font-bold text-slate-400 italic">Belum ada konsentrasi.</div>

                            <!-- LOOP 3: KONSENTRASI / KEJURUAN -->
                            <div v-for="k in p.kejuruans" :key="'k-'+k.id" class="flex items-center justify-between p-2.5 bg-white rounded-lg border border-slate-200 hover:shadow-sm transition-shadow group/k">
                                <div class="flex items-center gap-3">
                                    <span class="text-lg"><AppIcon name="shield" /></span>
                                    <div>
                                        <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded uppercase tracking-widest mr-2 border border-emerald-200">{{ k.kode_konsentrasi }}</span>
                                        <span class="text-xs font-bold text-slate-700">{{ k.nama_konsentrasi }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 opacity-100 sm:opacity-0 sm:group-hover/k:opacity-100 transition-opacity">
                                    <button @click="openEdit('konsentrasi', k)" class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-50 text-emerald-600 hover:bg-emerald-100 text-xs border border-transparent hover:border-emerald-200 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                    <button @click="confirmDelete('konsentrasi', k.id, k.nama_konsentrasi)" class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-50 text-rose-500 hover:bg-rose-100 text-xs border border-transparent hover:border-rose-200 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ==============================================
         MODAL KONFIRMASI HAPUS
         ============================================== -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade text-center">
            <div class="p-8">
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner border-4 border-white ring-4 ring-rose-50"><AppIcon name="exclamation-triangle" />️</div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Hapus Data?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Anda yakin ingin menghapus <span class="uppercase font-black text-rose-600">{{ deleteTarget.type }}</span>:<br>
                    <span class="font-bold text-slate-800">{{ deleteTarget.name }}</span>?
                </p>
                <p v-if="deleteTarget.type === 'bidang'" class="text-[10px] font-bold text-rose-500 mt-2 p-2 bg-rose-50 rounded-lg">AWAS: Menghapus Bidang akan menghapus SEMUA Program dan Konsentrasi di bawahnya!</p>
                <p v-if="deleteTarget.type === 'program'" class="text-[10px] font-bold text-rose-500 mt-2 p-2 bg-rose-50 rounded-lg">AWAS: Menghapus Program akan menghapus SEMUA Konsentrasi di bawahnya!</p>
                
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isDeleteModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">Batal</button>
                    <button @click="executeDelete" :disabled="isSaving" class="flex-1 py-3 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        <span v-if="isSaving" class="animate-spin text-base"><AppIcon name="clock" /></span>
                        <span v-else>Hapus</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

definePageMeta({
  layout: 'admin',
  middleware: 'admin',
  title: 'Master Kejuruan'
})

const windowWidth = ref(1024) 
const isDesktop = computed(() => windowWidth.value >= 1280) // xl breakpoint

// Tabs for Mobile
const activeTab = ref('table')
const mobileTabs = [
  { id: 'bidang', title: 'Bidang', iconName: 'cog' },
  { id: 'program', title: 'Program', iconName: 'academic-cap' },
  { id: 'konsentrasi', title: 'Fokus', iconName: 'shield' },
  { id: 'table', title: 'Database', iconName: 'clipboard' }
]

// Tree Data State
const treeData = ref([])
const isLoading = ref(true)
const expandedNodes = ref(new Set())

// Forms State
const bidangForm = ref({ id: null, nama_bidang: '' })
const programForm = ref({ id: null, bidang_id: '', nama_program: '' })
const kejuruanForm = ref({ id: null, program_id: '', kode_konsentrasi: '', nama_konsentrasi: '' })
const isSaving = ref(false)

// Delete State
const isDeleteModalOpen = ref(false)
const deleteTarget = ref({ type: '', id: null, name: '' })

// Computed Helpers for Select Dropdowns
const flatProgramList = computed(() => {
    let programs = []
    treeData.value.forEach(b => {
        if (b.programs) {
            b.programs.forEach(p => {
                programs.push({
                    id: p.id,
                    nama_program: `${b.nama_bidang} - ${p.nama_program}`
                })
            })
        }
    })
    return programs
})

// === TREE ACCORDION LOGIC ===
const toggleNode = (nodeId) => {
    const newSet = new Set(expandedNodes.value)
    if (newSet.has(nodeId)) {
        newSet.delete(nodeId)
    } else {
        newSet.add(nodeId)
    }
    expandedNodes.value = newSet
}
const isNodeExpanded = (nodeId) => expandedNodes.value.has(nodeId)
const expandAll = () => {
    const newSet = new Set()
    treeData.value.forEach(b => {
        newSet.add('b-'+b.id)
        if(b.programs) b.programs.forEach(p => newSet.add('p-'+p.id))
    })
    expandedNodes.value = newSet
}
const collapseAll = () => {
    expandedNodes.value = new Set()
}

// === API CALLS ===
const fetchTreeData = async () => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/admin/kejuruan/data', {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            treeData.value = response.data
            // Auto expand if only few items
            if (treeData.value.length === 1) expandAll()
        }
    } catch (error) {
        console.error('Failed to fetch tree:', error)
    } finally {
        isLoading.value = false
    }
}

const saveBidang = async () => {
    await submitForm('bidang', bidangForm.value)
    bidangForm.value = { id: null, nama_bidang: '' }
}

const saveProgram = async () => {
    await submitForm('program', programForm.value)
    programForm.value = { id: null, bidang_id: '', nama_program: '' }
}

const saveKejuruan = async () => {
    await submitForm('konsentrasi', kejuruanForm.value)
    kejuruanForm.value = { id: null, program_id: '', kode_konsentrasi: '', nama_konsentrasi: '' }
}

const submitForm = async (type, payload) => {
    isSaving.value = true
    const token = useCookie('auth_token').value
    const isUpdate = payload.id !== null
    const method = isUpdate ? 'PUT' : 'POST'
    const url = isUpdate 
        ? `${import.meta.env.VITE_API_BASE_URL}/api/admin/kejuruan/${type}/${payload.id}`
        : `${import.meta.env.VITE_API_BASE_URL}/api/admin/kejuruan/${type}`
    
    try {
        const response = await $fetch(url, {
            method: method,
            headers: { Authorization: `Bearer ${token}` },
            body: payload
        })
        if (response.success) {
            useSwal().toast(response.message)
            fetchTreeData()
            if (!isDesktop.value) activeTab.value = 'table'
        }
    } catch (error) {
        console.error(`Save ${type} failed:`, error)
        useSwal().fire('Gagal', `Gagal menyimpan data ${type}.`, 'error')
    } finally {
        isSaving.value = false
    }
}

const openEdit = (type, item) => {
    if (type === 'bidang') {
        bidangForm.value = { id: item.id, nama_bidang: item.nama_bidang }
        activeTab.value = 'bidang'
    } else if (type === 'program') {
        programForm.value = { id: item.id, bidang_id: item.bidang_id, nama_program: item.nama_program }
        activeTab.value = 'program'
    } else if (type === 'konsentrasi') {
        kejuruanForm.value = { id: item.id, program_id: item.program_id, kode_konsentrasi: item.kode_konsentrasi, nama_konsentrasi: item.nama_konsentrasi }
        activeTab.value = 'konsentrasi'
    }
}

const confirmDelete = (type, id, name) => {
    deleteTarget.value = { type, id, name }
    isDeleteModalOpen.value = true
}

const executeDelete = async () => {
    if (!deleteTarget.value.id) return
    isSaving.value = true
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/admin/kejuruan/${deleteTarget.value.type}/${deleteTarget.value.id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            isDeleteModalOpen.value = false
            useSwal().toast(response.message)
            fetchTreeData()
        }
    } catch (error) {
        console.error('Delete failed:', error)
        useSwal().toast('Gagal menghapus data.', 'error')
    } finally {
        isSaving.value = false
    }
}

onMounted(() => {
    windowWidth.value = window.innerWidth
    window.addEventListener('resize', () => { windowWidth.value = window.innerWidth })
    
    if (isDesktop.value) {
        activeTab.value = 'bidang' 
    } else {
        activeTab.value = 'table' 
    }

    fetchTreeData()
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }

@keyframes slideUpFade {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-slideUpFade { animation: slideUpFade 0.3s ease-out forwards; }

.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #94a3b8;
}
</style>
