<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-1.5 flex gap-1.5 shadow-sm z-20">
        <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTabMobile = tab.id"
          :class="activeTabMobile === tab.id ? 'bg-gradient-to-br from-amber-500 to-amber-600 text-white shadow-md shadow-amber-500/20 ring-2 ring-amber-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="flex-1 rounded-lg flex flex-col items-center justify-center py-2 px-1 transition-all active:scale-95">
          <AppIcon :name="tab.icon" class="text-lg mb-0.5 transition-transform" :class="activeTabMobile === tab.id ? 'scale-110' : ''" />
          <span class="text-[10px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
        </button>
      </div>

      <!-- =============================================== -->
      <!-- PANEL DOCK KIRI (Form Input)                    -->
      <!-- =============================================== -->
      <div :class="['w-full xl:w-[380px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all', activeTabMobile === 'form' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[52px]' : '']">
        
        <!-- Tabs: Umum vs Produktif -->
        <div class="p-3 pb-0 shrink-0">
          <div class="flex bg-slate-100 rounded-2xl p-1 gap-1">
            <button type="button" @click="switchFormTab('umum')" :class="activeFormTab === 'umum' ? 'bg-white text-amber-700 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all">
              📚 Mapel Umum
            </button>
            <button type="button" @click="switchFormTab('produktif')" :class="activeFormTab === 'produktif' ? 'bg-white text-purple-700 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all">
              🏭 Produktif
            </button>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar">

          <!-- ===================== -->
          <!-- TAB: MAPEL UMUM (A/B/C/D) -->
          <!-- ===================== -->
          <div v-show="activeFormTab === 'umum'" class="p-4 space-y-4">
            <div class="bg-gradient-to-r from-amber-600 to-orange-600 rounded-2xl p-4 border border-amber-500 shadow-sm relative overflow-hidden flex items-center gap-3">
              <div class="w-8 h-8 flex items-center justify-center shrink-0 bg-white/10 rounded-lg text-white"><AppIcon name="book-open" class="w-5 h-5" /></div>
              <div>
                <h3 class="text-xs font-black uppercase tracking-widest text-white">{{ isEditing ? 'Edit Mapel' : 'Tambah Mapel' }}</h3>
                <p class="text-[10px] text-amber-100 font-semibold mt-0.5">Kelompok A / B / C / D</p>
              </div>
            </div>
            <form @submit.prevent="saveData" class="space-y-4">
              <!-- Pilih Kurikulum -->
              <div>
                <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pilih Kurikulum</label>
                <select v-model="formData.kurikulum_id" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-bold text-slate-700 outline-none cursor-pointer">
                  <option value="" disabled>-- Pilih Kurikulum --</option>
                  <option v-for="kur in kurikulums" :key="kur.id" :value="kur.id">{{ kur.nama_kurikulum }}</option>
                </select>
              </div>
              <!-- Pilih Kelompok -->
              <div>
                <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kelompok Mapel <span class="text-rose-500">*</span></label>
                <select v-model="formData.kelompok" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-bold text-slate-700 outline-none cursor-pointer">
                  <option value="" disabled>-- Pilih Kelompok --</option>
                  <option v-for="kel in refKelompokMapel" :key="kel.kode" :value="kel.kode">{{ kel.kode }} &ndash; {{ kel.nama }}</option>
                </select>
              </div>
              <!-- Kode Mapel -->
              <div>
                <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kode Mapel</label>
                <input type="text" v-model="formData.kode_mapel" required placeholder="Contoh: A1, B3, C4"
                  class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-bold text-slate-800 placeholder:text-slate-400 outline-none">
              </div>
              <!-- Nama Mapel -->
              <div>
                <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Nama Mapel</label>
                <input type="text" v-model="formData.nama_mapel" required placeholder="Nama lengkap mata pelajaran"
                  class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-bold text-slate-800 placeholder:text-slate-400 outline-none">
              </div>
              <div class="pt-4 border-t border-slate-100 flex gap-3">
                <button v-if="isEditing" type="button" @click="resetForm" class="flex-1 py-3 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest border border-rose-200">Batal</button>
                <button type="submit" :disabled="isSaving" class="flex-[2] py-3 bg-gradient-to-r from-amber-500 to-amber-600 text-white font-bold rounded-2xl shadow-lg shadow-amber-500/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 text-xs uppercase tracking-widest disabled:opacity-50">
                  <span v-if="isSaving" class="animate-spin"><AppIcon name="clock" class="w-5 h-5" /></span>
                  <span v-else>{{ isEditing ? '💾' : '➕' }}</span>
                  {{ isEditing ? 'Simpan Perubahan' : 'Tambah Mapel' }}
                </button>
              </div>
            </form>
          </div>

          <!-- ===================== -->
          <!-- TAB: MAPEL PRODUKTIF  -->
          <!-- ===================== -->
          <div v-show="activeFormTab === 'produktif'" class="p-4 space-y-4">
            <div class="bg-gradient-to-r from-purple-600 to-fuchsia-700 rounded-2xl p-4 border border-purple-500 shadow-sm relative overflow-hidden flex items-center gap-3">
              <div class="w-8 h-8 flex items-center justify-center shrink-0 bg-white/10 rounded-lg text-white">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18" /></svg>
              </div>
              <div>
                <h3 class="text-xs font-black uppercase tracking-widest text-white">{{ isEditingProduktif ? 'Edit Mapel Produktif' : 'Tambah Mapel Produktif' }}</h3>
                <p class="text-[10px] text-purple-100 font-semibold mt-0.5">Berbasis Kode Kejuruan 3-Digit</p>
              </div>
            </div>

            <form @submit.prevent="saveProduktif" class="space-y-4 mt-4">
              <!-- Pilih Kurikulum -->
              <div>
                <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pilih Kurikulum</label>
                <select v-model="formProduktif.kurikulum_id" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all text-sm font-bold text-slate-700 outline-none cursor-pointer">
                  <option value="" disabled>-- Pilih Kurikulum --</option>
                  <option v-for="kur in kurikulums" :key="kur.id" :value="kur.id">{{ kur.nama_kurikulum }}</option>
                </select>
              </div>

              <!-- 3 Areas -->
              <div class="flex gap-3 items-stretch">
                <!-- Dropdown 1: Kode Kejuruan -->
                <div class="flex-[2] min-w-0">
                  <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kode Kejuruan <span class="text-rose-500">*</span></label>
                  <div v-if="isLoadingKejuruan" class="w-full px-3 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 text-xs text-slate-400 font-bold flex items-center min-h-[46px]">Memuat...</div>
                  <select v-else v-model="formProduktif.kode_kejuruan" required @change="updateKodePreview" class="w-full px-3 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all text-xs sm:text-sm font-bold text-slate-700 outline-none cursor-pointer truncate">
                    <option value="" disabled>-- Pilih --</option>
                    <option v-for="k in kejuruanList" :key="k.id" :value="k.kode">{{ k.label }}</option>
                  </select>
                </div>
                
                <!-- Dropdown 2: Tingkat -->
                <div class="flex-1 min-w-0">
                  <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Tingkat <span class="text-rose-500">*</span></label>
                  <select v-model="formProduktif.tingkat" required @change="updateKodePreview" class="w-full px-3 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all text-sm font-black text-slate-700 outline-none cursor-pointer text-center">
                    <option value="" disabled>-</option>
                    <option value="X">X</option>
                    <option value="XI">XI</option>
                    <option value="XII">XII</option>
                  </select>
                </div>

                <!-- Input: Kode Identitas -->
                <div class="flex-1 min-w-0">
                  <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kode Mapel <span class="text-rose-500">*</span></label>
                  <input type="text" v-model="formProduktif.kode_identitas" required placeholder="Ex: B5a"
                    @input="updateKodePreview"
                    class="w-full px-3 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all text-sm font-black text-purple-800 placeholder:text-slate-300 placeholder:font-normal outline-none text-center">
                </div>
              </div>
              <p v-if="kejuruanList.length === 0 && !isLoadingKejuruan" class="text-[10px] text-rose-500 font-bold mt-1 ml-1">⚠️ Belum ada kejuruan dengan kode 3 digit. Buat dahulu di Admin → Master Kejuruan.</p>

              <!-- Nama Mapel & Smart Description -->
              <div>
                <label class="block text-[11px] font-black text-slate-500 uppercase mb-1 ml-1 flex justify-between items-end">
                  <span>Nama Mapel <span class="text-rose-500">*</span></span>
                  <span v-if="kodeProduktifPreview" class="text-[10px] text-purple-600 font-black bg-purple-50 px-2 py-0.5 rounded-lg border border-purple-200 tracking-widest">
                    {{ kodeProduktifPreview }}
                  </span>
                </label>
                <div v-if="smartDescriptionProduktif" class="mb-1.5 ml-1">
                  <p class="text-[10px] text-purple-600 font-bold bg-purple-50 inline-flex items-center px-2 py-0.5 rounded-md border border-purple-100">
                    💡 {{ smartDescriptionProduktif }}
                  </p>
                </div>
                <input type="text" v-model="formProduktif.nama_mapel" required placeholder="Misal: Dasar Listrik, Dasar Mikrokontroler"
                  class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 transition-all text-sm font-bold text-slate-800 placeholder:text-slate-400 outline-none">
              </div>

              <div class="pt-4 border-t border-slate-100 flex gap-3">
                <button v-if="isEditingProduktif" type="button" @click="resetProduktifForm" class="flex-1 py-3 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest border border-rose-200">Batal</button>
                <button type="submit" :disabled="isSaving || !kodeProduktifPreview" class="flex-[2] py-3 bg-gradient-to-r from-purple-600 to-fuchsia-600 text-white font-bold rounded-2xl shadow-lg shadow-purple-500/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 text-xs uppercase tracking-widest disabled:opacity-50">
                  <span v-if="isSaving" class="animate-spin"><AppIcon name="clock" class="w-5 h-5" /></span>
                  <span v-else>{{ isEditingProduktif ? '💾' : '➕' }}</span>
                  {{ isEditingProduktif ? 'Simpan Perubahan' : 'Tambah Mapel Produktif' }}
                </button>
              </div>
            </form>
          </div>

        </div><!-- end flex-1 scrollable -->
      </div><!-- end panel kiri -->



      <!-- =============================================== -->
      <!-- PANEL FLOW KANAN (Tabel Data Global)            -->
      <!-- =============================================== -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative', activeTabMobile === 'table' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[52px]' : '']">
        <div class="p-2 sm:pt-3 sm:pb-6 sm:px-6 lg:pt-3 lg:pb-8 lg:px-8 max-w-7xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-none sm:rounded-[2rem] shadow-[0_2px_15px_-3px_rgba(0,0,0,0.05)] overflow-hidden flex flex-col flex-1 relative min-h-0 border-0 sm:border sm:border-slate-200/60">
            
            <!-- Header Tabel -->
            <div class="px-6 py-4 border-b border-slate-100 shrink-0 z-10 bg-white">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <!-- Judul -->
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 shadow-sm border border-amber-100 flex items-center justify-center text-xl hidden sm:flex text-amber-500"><AppIcon name="book-open" class="w-6 h-6" /></div>
                        <div>
                            <h3 class="text-sm font-black uppercase tracking-widest text-amber-700">Daftar Mata Pelajaran</h3>
                            <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5 hidden sm:block">
                                {{ filteredCount }} mapel | Diurutkan berdasarkan kode kelompok
                            </p>
                        </div>
                    </div>
                    <!-- Filter Controls -->
                    <div class="flex items-center gap-2 w-full sm:w-auto flex-wrap sm:flex-nowrap">
                        <!-- Filter Kurikulum -->
                        <select v-model="filterKurikulum" @change="fetchData" class="flex-1 sm:flex-none px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-xs font-bold text-slate-700 outline-none cursor-pointer">
                            <option value="">Semua Kurikulum</option>
                            <option v-for="kur in kurikulums" :key="kur.id" :value="kur.id">{{ kur.nama_kurikulum }}</option>
                        </select>
                        <!-- Filter Kelompok -->
                        <select v-model="filterKelompok" @change="fetchData" class="flex-1 sm:flex-none px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-xs font-bold text-slate-700 outline-none cursor-pointer">
                            <option value="">Semua Kelompok</option>
                            <option v-for="kel in refKelompokMapel" :key="kel.kode" :value="kel.kode">{{ kel.kode }} – {{ kel.nama }}</option>
                        </select>
                        <!-- Refresh -->
                        <button @click="fetchData" class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-slate-200 hover:text-slate-700 transition-colors shrink-0" title="Refresh">
                            <AppIcon name="arrow-path" class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="flex-1 overflow-y-auto custom-scrollbar relative bg-slate-50">
                <!-- Loading State -->
                <div v-if="isLoading" class="flex-grow flex items-center justify-center flex-col p-10 opacity-60 h-full">
                    <div class="w-8 h-8 border-4 border-amber-400 border-t-transparent rounded-full animate-spin mb-4"></div>
                    <span class="text-xs font-black text-amber-500 uppercase tracking-widest">Memuat Data...</span>
                </div>

                <!-- Empty State -->
                <div v-else-if="!mapels || mapels.length === 0" class="flex-grow flex items-center justify-center flex-col p-16 text-center h-full">
                    <div class="text-5xl mb-4">📚</div>
                    <p class="text-sm font-black uppercase tracking-widest text-slate-500">Belum ada data mapel.</p>
                    <p class="text-xs text-slate-400 mt-1">Tambahkan mapel melalui form di sebelah kiri.</p>
                </div>

                <!-- Grouped Table Content -->
                <div v-else class="p-4 sm:p-6 space-y-4">
                    <div v-for="(groupList, kelompokKey) in groupedMapels" :key="kelompokKey"
                         class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        
                        <!-- Group Header -->
                        <div @click="toggleCollapse(kelompokKey)"
                             class="px-5 py-3 bg-gradient-to-r from-slate-50 to-amber-50/30 border-b border-slate-100 flex items-center justify-between cursor-pointer hover:bg-amber-50/50 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg bg-amber-100 border border-amber-200 flex items-center justify-center text-[10px] text-amber-700 font-black shadow-sm">
                                    {{ kelompokKey.charAt(0) }}
                                </span>
                                <div>
                                    <h4 class="font-black text-slate-700 text-xs uppercase tracking-widest">
                                        Kelompok {{ kelompokKey }}
                                    </h4>
                                    <p class="text-[10px] text-slate-400 font-semibold mt-0.5" v-if="getKelompokNama(kelompokKey)">
                                        {{ getKelompokNama(kelompokKey) }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] font-black uppercase tracking-widest text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-100">
                                    {{ groupList.length }} Mapel
                                </span>
                                <AppIcon name="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-300" :class="{ 'rotate-180': isCollapsed[kelompokKey] }" />
                            </div>
                        </div>
                        
                        <!-- Group Rows -->
                        <div v-show="!isCollapsed[kelompokKey]" class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-white text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">
                                        <th class="py-2.5 px-3 pl-6 w-10 text-center">No</th>
                                        <th class="py-2.5 px-3 w-20">Kode</th>
                                        <th class="py-2.5 px-3">Mata Pelajaran</th>
                                        <th class="py-2.5 px-3 text-center w-10">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    <tr v-for="(item, index) in groupList" :key="item.id"
                                        class="border-b border-slate-100 hover:bg-amber-50/30 transition-colors bg-white group">
                                        <td class="py-2.5 px-3 pl-6 text-center w-10 text-[10px] font-bold text-slate-400">{{ index + 1 }}</td>
                                        <td class="py-2.5 px-3 w-20">
                                            <span class="text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-100">
                                                {{ item.kode_mapel }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3">
                                            <p class="font-bold text-slate-700 text-[11px] leading-snug">{{ item.nama_mapel }}</p>
                                            <p class="text-[10px] text-slate-400 mt-0.5" v-if="item.kurikulum">{{ item.kurikulum.nama_kurikulum }}</p>
                                        </td>
                                        <td class="py-2.5 px-3 text-center w-10">
                                            <div class="flex flex-col items-center justify-center gap-1 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                                <button @click.stop="item.kelompok && item.kelompok.match(/^\d{3}\./) ? editProduktif(item) : editData(item)" class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-slate-400 hover:border-amber-200 hover:bg-amber-50 hover:text-amber-600 flex items-center justify-center transition-all shadow-sm" title="Edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                                </button>
                                                <button @click.stop="confirmDelete(item)" class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-slate-400 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 flex items-center justify-center transition-all shadow-sm" title="Hapus">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Delete -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-fadeIn">
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden animate-slideUpFade text-center">
            <div class="p-8">
                <div class="w-20 h-20 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner border-4 border-white ring-4 ring-rose-50"><AppIcon name="exclamation-triangle" class="w-6 h-6" /></div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Hapus Mapel?</h3>
                <p class="text-xs text-slate-500 mt-3 leading-relaxed">
                    Anda yakin ingin menghapus mapel:<br>
                    <span class="font-bold text-rose-600">{{ deleteTarget?.nama_mapel }}</span>?
                </p>
                <div class="flex items-center gap-4 mt-8">
                    <button @click="isDeleteModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest">Batal</button>
                    <button @click="executeDelete" class="flex-1 py-3 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-2xl shadow-lg shadow-rose-500/30 transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                        <span>Hapus</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAutoSave } from '~/composables/useAutoSave'

definePageMeta({
  layout: 'kurikulum',
  middleware: 'kurikulum',
  title: 'Master Mata Pelajaran'
})

// Responsiveness
const windowWidth = ref(1024) 
const isDesktop = computed(() => windowWidth.value >= 1280)

// Mobile tabs
const activeTabMobile = ref('table')
const mobileTabs = [
  { id: 'form', title: 'Form Data', icon: 'document-text' },
  { id: 'table', title: 'Database', icon: 'clipboard' }
]

// Active form tab (umum vs produktif)
const activeFormTab = ref('umum')
const switchFormTab = (tab) => {
  activeFormTab.value = tab
  if (tab === 'produktif' && kejuruanList.value.length === 0) fetchKejuruanList()
}

// Produktif Form State
const isEditingProduktif = ref(false)
const isLoadingKejuruan = ref(false)
const kejuruanList = ref([])
const formProduktif = ref({
  id: null,
  kurikulum_id: '',
  kode_kejuruan: '',
  tingkat: '',
  kode_identitas: '',
  nama_mapel: ''
})
const kodeProduktifPreview = computed(() => {
  const { kode_kejuruan, tingkat, kode_identitas } = formProduktif.value
  if (kode_kejuruan && tingkat && kode_identitas) {
    return `${kode_kejuruan}.${tingkat}.${kode_identitas}`
  }
  return ''
})

const smartDescriptionProduktif = computed(() => {
  const { kode_kejuruan, tingkat } = formProduktif.value
  if (!kode_kejuruan || !tingkat) return ''
  const kejuruan = kejuruanList.value.find(k => k.kode === kode_kejuruan)
  if (!kejuruan) return ''

  if (tingkat === 'X') {
    return `Mapel untuk Program Keahlian: ${kejuruan.nama_program}`
  } else {
    return `Mapel untuk Konsentrasi Keahlian: ${kejuruan.nama_konsentrasi}`
  }
})

// Data
const mapels = ref([])
const kurikulums = ref([])
const refKelompokMapel = ref([])
const isLoading = ref(true)
const isSaving = ref(false)
const isEditing = ref(false)

// Filters
const filterKurikulum = ref('')
const filterKelompok = ref('')

// Form
const formData = ref({
    id: null,
    kurikulum_id: '',
    kode_mapel: '',
    nama_mapel: '',
    kelompok: ''
})

// Collapse state per kelompok
const isCollapsed = ref({})
const toggleCollapse = (k) => {
    isCollapsed.value[k] = !isCollapsed.value[k]
}

// Delete modal
const isDeleteModalOpen = ref(false)
const deleteTarget = ref(null)

// Count total
const filteredCount = computed(() => mapels.value.length)

// Get nama kelompok from ref (A=Mata Pelajaran Umum, dll.)
const getKelompokNama = (kode) => {
    const found = refKelompokMapel.value.find(k => k.kode === kode)
    return found ? found.nama : (kode.includes('.') ? 'Mata Pelajaran Kejuruan-Produktif' : null)
}

// Group mapels by kelompok, sorted alphabetically by key
const groupedMapels = computed(() => {
    const groups = {}
    mapels.value.forEach(m => {
        const k = m.kelompok || 'Lainnya'
        if (!groups[k]) groups[k] = []
        groups[k].push(m)
    })
    // Sort keys: A, B, then custom codes (e.g. 251.X, 251.XI), then C, D
    const sortedKeys = Object.keys(groups).sort((a, b) => {
        // A and B first
        if (a === 'A') return -1
        if (b === 'A') return 1
        if (a === 'B') return -1
        if (b === 'B') return 1
        // C and D last among standard
        if (a === 'C' && !b.includes('.')) return 1
        if (b === 'C' && !a.includes('.')) return -1
        if (a === 'D' && !b.includes('.')) return 1
        if (b === 'D' && !a.includes('.')) return -1
        return a.localeCompare(b, undefined, { numeric: true })
    })
    const result = {}
    sortedKeys.forEach(k => { result[k] = groups[k] })
    return result
})

const { registerAutoSave, unregisterAutoSave } = useAutoSave()
onUnmounted(() => { unregisterAutoSave() })

// ===== API CALLS =====
const fetchData = async () => {
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        let url = `${import.meta.env.VITE_API_BASE_URL}/api/kurikulum/mapel`
        const params = []
        if (filterKurikulum.value) params.push(`kurikulum_id=${filterKurikulum.value}`)
        if (filterKelompok.value) params.push(`kelompok=${filterKelompok.value}`)
        if (params.length) url += '?' + params.join('&')

        const response = await $fetch(url, {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            mapels.value = response.data
            kurikulums.value = response.kurikulums || []
            // Merge kelompok from API (includes standard A,B,C,D)
            if (response.kelompok_mapel?.length) {
                refKelompokMapel.value = response.kelompok_mapel
            }
        }
    } catch (error) {
        console.error('Failed to fetch mapel:', error)
    } finally {
        isLoading.value = false
    }
}

const saveData = async () => {
    if (!formData.value.kelompok) {
        useSwal().toast('Kelompok Mapel wajib diisi!', 'error')
        return
    }
    isSaving.value = true
    const token = useCookie('auth_token').value
    const url = isEditing.value 
        ? `${import.meta.env.VITE_API_BASE_URL}/api/kurikulum/mapel/${formData.value.id}` 
        : `${import.meta.env.VITE_API_BASE_URL}/api/kurikulum/mapel`
    const method = isEditing.value ? 'PUT' : 'POST'

    try {
        const response = await $fetch(url, {
            method: method,
            headers: { Authorization: `Bearer ${token}` },
            body: {
                kurikulum_id: formData.value.kurikulum_id,
                kode_mapel: formData.value.kode_mapel,
                nama_mapel: formData.value.nama_mapel,
                kelompok: formData.value.kelompok,
            }
        })
        
        if (response.success) {
            useSwal().toast(response.message, 'success')
            resetForm()
            fetchData()
            if (!isDesktop.value) activeTabMobile.value = 'table'
        }
    } catch (error) {
        console.error('Save error:', error)
        let errMsg = 'Gagal menyimpan data mapel.'
        if (error.response?.status === 422) {
            errMsg = error.response._data?.message || 'Mohon periksa kembali isian form.'
        } else if (error.response) {
            errMsg = `Gagal menyimpan: Kesalahan server (${error.response.status}).`
        }
        useSwal().toast(errMsg, 'error')
    } finally {
        isSaving.value = false
    }
}

const editData = (item) => {
    isEditing.value = true
    formData.value = {
        id: item.id,
        kurikulum_id: item.kurikulum_id,
        kode_mapel: item.kode_mapel,
        nama_mapel: item.nama_mapel,
        kelompok: item.kelompok || ''
    }
    if (!isDesktop.value) activeTabMobile.value = 'form'
}

const resetForm = () => {
  isEditing.value = false
  formData.value = {
    id: null,
    kurikulum_id: '',
    kode_mapel: '',
    nama_mapel: '',
    kelompok: ''
  }
}

// ===== PRODUKTIF FUNCTIONS =====
const fetchKejuruanList = async () => {
  isLoadingKejuruan.value = true
  const token = useCookie('auth_token').value
  try {
    const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/kurikulum/kejuruan-list`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    if (res.success) kejuruanList.value = res.data
  } catch (e) {
    console.error('Failed to fetch kejuruan list:', e)
  } finally {
    isLoadingKejuruan.value = false
  }
}

const updateKodePreview = () => {
  // Computed automatically recalculates - this is called to trigger reactivity
}

const saveProduktif = async () => {
  if (!kodeProduktifPreview.value) {
    useSwal().toast('Lengkapi semua field Mapel Produktif!', 'error')
    return
  }
  isSaving.value = true
  const token = useCookie('auth_token').value
  // kelompok = kode_kejuruan.tingkat  (e.g. 251.XI)
  // kode_mapel = kode_kejuruan.tingkat.kode_identitas  (e.g. 251.XI.B6a)
  const payload = {
    kurikulum_id: formProduktif.value.kurikulum_id,
    kode_mapel: kodeProduktifPreview.value,
    nama_mapel: formProduktif.value.nama_mapel,
    kelompok: `${formProduktif.value.kode_kejuruan}.${formProduktif.value.tingkat}`,
  }
  const url = isEditingProduktif.value
    ? `${import.meta.env.VITE_API_BASE_URL}/api/kurikulum/mapel/${formProduktif.value.id}`
    : `${import.meta.env.VITE_API_BASE_URL}/api/kurikulum/mapel`
  const method = isEditingProduktif.value ? 'PUT' : 'POST'
  try {
    const response = await $fetch(url, {
      method,
      headers: { Authorization: `Bearer ${token}` },
      body: payload
    })
    if (response.success) {
      useSwal().toast(response.message, 'success')
      resetProduktifForm()
      fetchData()
      if (!isDesktop.value) activeTabMobile.value = 'table'
    }
  } catch (error) {
    let errMsg = 'Gagal menyimpan data mapel produktif.'
    if (error.response?.status === 422) errMsg = error.response._data?.message || errMsg
    useSwal().toast(errMsg, 'error')
  } finally {
    isSaving.value = false
  }
}

const resetProduktifForm = () => {
  isEditingProduktif.value = false
  formProduktif.value = { id: null, kurikulum_id: '', kode_kejuruan: '', tingkat: '', kode_identitas: '', nama_mapel: '' }
}

const editProduktif = (item) => {
  // Fetch kejuruan list if it's empty so the dropdown can populate
  if (kejuruanList.value.length === 0) fetchKejuruanList()
  
  // Parse kelompok "251.XI" → kode_kejuruan="251", tingkat="XI"
  const parts = (item.kelompok || '').split('.')
  formProduktif.value = {
    id: item.id,
    kurikulum_id: item.kurikulum_id,
    kode_kejuruan: parts[0] || '',
    tingkat: parts[1] || '',
    kode_identitas: (item.kode_mapel || '').split('.').slice(2).join('.'),
    nama_mapel: item.nama_mapel
  }
  isEditingProduktif.value = true
  activeFormTab.value = 'produktif'
  if (!isDesktop.value) activeTabMobile.value = 'form'
}

const confirmDelete = (item) => {
    deleteTarget.value = item
    isDeleteModalOpen.value = true
}

const executeDelete = async () => {
    if(!deleteTarget.value) return
    const token = useCookie('auth_token').value
    try {
        const response = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/kurikulum/mapel/${deleteTarget.value.id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            isDeleteModalOpen.value = false
            useSwal().toast(response.message, 'success')
            fetchData()
        }
    } catch (error) {
        console.error('Delete failed:', error)
        useSwal().toast('Gagal menghapus data mapel.', 'error')
    }
}

onMounted(async () => {
    windowWidth.value = window.innerWidth
    window.addEventListener('resize', () => { windowWidth.value = window.innerWidth })
    
    if (isDesktop.value) {
        activeTabMobile.value = 'form'
    } else {
        activeTabMobile.value = 'table'
    }

    registerAutoSave(async () => {
        if (formData.value.nama_mapel) {
            await saveData()
        }
    })

    await fetchData()
})
</script>

<style scoped>
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

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }

@keyframes slideUpFade {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-slideUpFade { animation: slideUpFade 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
