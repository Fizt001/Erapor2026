<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex overflow-hidden relative">
      
      <!-- MOBILE VIEW TABS -->
      <div class="xl:hidden absolute top-0 left-0 w-full bg-white border-b border-slate-200 flex-shrink-0 p-2 grid grid-cols-2 gap-2 z-20">
        <button v-for="tab in mobileTabs" :key="'mob-'+tab.id" type="button" @click="activeTabMobile = tab.id"
          :class="activeTabMobile === tab.id ? 'bg-gradient-to-br from-amber-500 to-amber-600 text-white shadow-md shadow-amber-500/20 ring-2 ring-amber-500 ring-offset-1' : 'bg-white text-slate-500 shadow-sm border border-slate-100'"
          class="rounded-xl flex items-center justify-center py-2 px-1 transition-all active:scale-95">
          <AppIcon :name="tab.icon" class="w-6 h-6 mb-0.5 transition-transform" :class="activeTabMobile === tab.id ? 'scale-110' : ''" />
          <span class="text-[10px] font-black uppercase tracking-wider text-center leading-none">{{ tab.title }}</span>
        </button>
      </div>

      <!-- Panel Dock Kiri (Form) -->
      <div :class="['w-full xl:w-[360px] bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] transition-all', activeTabMobile === 'form' || isDesktop ? 'block' : 'hidden xl:flex', !isDesktop ? 'pt-[60px]' : '']">
        
        <div class="p-6 shrink-0 z-10 relative">
          <div class="bg-gradient-to-r from-amber-600 to-orange-600 rounded-2xl p-5 border border-amber-500 shadow-sm relative overflow-hidden flex items-center gap-4">
            <div class="w-10 h-10 flex items-center justify-center text-2xl shrink-0 relative z-10"><AppIcon name="book-open" class="w-6 h-6" /></div>
            <div class="relative z-10">
                <h3 class="text-sm font-black uppercase tracking-widest text-white">{{ isEditing ? 'Edit Mata Pelajaran' : 'Mapel Baru' }}</h3>
                <p class="text-[10px] text-amber-100 font-semibold uppercase mt-0.5">{{ isEditing ? 'Perbarui Data' : 'Tambah Data Manual' }}</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-20 text-white">
              <svg class="w-20 h-20 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"></path></svg>
            </div>
          </div>
        </div>
        
        <div class="flex-1 overflow-y-auto custom-scrollbar p-5 pt-0">
            <form @submit.prevent="saveData" class="space-y-4">
                
                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Pilih Kurikulum</label>
                    <select v-model="formData.kurikulum_id" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-bold text-slate-700 outline-none cursor-pointer">
                        <option value="" disabled>-- Pilih Kurikulum --</option>
                        <option v-for="kur in kurikulums" :key="kur.id" :value="kur.id">{{ kur.nama_kurikulum }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kategori</label>
                    <select v-model="formData.kategori" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-bold text-slate-700 outline-none cursor-pointer" @change="fetchData">
                        <option v-for="kat in refKategoriMapel" :key="kat.kode" :value="kat.kode">{{ kat.nama }}</option>
                    </select>
                </div>

                <div v-if="refKelompokMapel.length > 0">
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kelompok (Opsional)</label>
                    <select v-model="formData.kelompok" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-bold text-slate-700 outline-none cursor-pointer">
                        <option value="">-- Tanpa Kelompok --</option>
                        <option v-for="kel in refKelompokMapel" :key="kel.kode" :value="kel.kode">{{ kel.nama }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kode Mapel</label>
                    <input type="text" v-model="formData.kode_mapel" required placeholder="Contoh: B.IND, MTK" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-bold text-slate-800 placeholder:text-slate-400 outline-none">
                </div>

                <div>
                    <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Nama Mapel</label>
                    <input type="text" v-model="formData.nama_mapel" required placeholder="Nama lengkap mata pelajaran" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all text-sm font-bold text-slate-800 placeholder:text-slate-400 outline-none">
                </div>
                
                <div class="pt-4 border-t border-slate-100 flex gap-3">
                    <button v-if="isEditing" type="button" @click="resetForm" class="flex-1 py-3 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold rounded-2xl transition-all text-xs uppercase tracking-widest border border-rose-200">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSaving" class="flex-[2] py-3 bg-gradient-to-r from-amber-500 to-amber-600 text-white font-bold rounded-2xl shadow-lg shadow-amber-500/30 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 text-xs uppercase tracking-widest disabled:opacity-50">
                        <span v-if="isSaving" class="animate-spin"><AppIcon name="clock" class="w-6 h-6" /></span>
                        <span v-else>{{ isEditing ? '💾' : '➕' }}</span> 
                        {{ isEditing ? 'Simpan' : 'Tambah' }}
                    </button>
                </div>
            </form>
        </div>
      </div>

      <!-- Panel Flow Kanan (Tabel) -->
      <div :class="['flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative', activeTabMobile === 'table' || isDesktop ? 'flex' : 'hidden', !isDesktop ? 'pt-[60px]' : '']">
        <div class="p-2 sm:p-2 sm:p-6 lg:p-8 max-w-5xl mx-auto w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            
            <!-- Header Flow -->
            <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shrink-0 z-10 bg-white">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 shadow-sm border border-amber-100 flex items-center justify-center text-xl hidden sm:flex text-amber-500"><AppIcon name="book-open" class="w-6 h-6" /></div>
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-amber-700">Tahun Ajaran: {{ tahunAjaranAktif?.nama || 'Belum Diatur' }}</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Filter Kategori & Kurikulum</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <select v-model="filterKurikulum" @change="fetchData" class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 text-xs font-bold text-slate-700 outline-none cursor-pointer">
                        <option value="">Semua Kurikulum</option>
                        <option v-for="kur in kurikulums" :key="kur.id" :value="kur.id">{{ kur.nama_kurikulum }}</option>
                    </select>
                    <button @click="fetchData" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-slate-200 hover:text-slate-700 font-bold transition-colors" title="Refresh">
                        <AppIcon name="arrow-path" class="w-5 h-5 inline-block mr-1" />
                    </button>
                </div>
            </div>

            <!-- Tabs Kategori in Flow Header -->
            <div class="bg-white px-4 pt-4 border-b border-slate-200 shrink-0 shadow-sm overflow-x-auto flex gap-2">
                <button v-for="kat in refKategoriMapel" :key="kat.kode"
                    @click="kategoriTab = kat.kode; fetchData()" 
                    class="px-6 py-3 rounded-t-xl text-xs font-black uppercase tracking-widest transition-all whitespace-nowrap"
                    :class="kategoriTab === kat.kode ? 'bg-amber-50 text-amber-600 border-b-2 border-amber-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'">
                    {{ kat.nama }}
                </button>
                <div v-if="refKategoriMapel.length === 0" class="py-3 px-6 text-sm text-rose-500 font-bold w-full text-center">
                    <AppIcon name="exclamation-triangle" class="w-5 h-5 inline-block mr-1" /> Silakan isi Master Database untuk Kategori Mapel.
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
                    <div class="text-6xl opacity-20 mb-4"><AppIcon name="book-open" class="w-6 h-6" /></div>
                    <p class="text-sm font-black uppercase tracking-widest text-slate-500">Belum ada data mapel.</p>
                </div>

                <!-- Table Content for Grouped View -->
                <div v-else-if="mapels.length > 0 && refKelompokMapel.length > 0" class="p-4 sm:p-6 space-y-4">
                    <div v-for="(groupList, kelompok) in groupedMapels" :key="kelompok" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div @click="toggleCollapse(kelompok)" class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between cursor-pointer hover:bg-slate-100 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-md bg-white border border-slate-200 flex items-center justify-center text-xs text-slate-400 font-black shadow-sm">{{ kelompok.charAt(0) }}</span>
                                <h4 class="font-black text-slate-700 text-xs uppercase tracking-widest">Kelompok {{ kelompok }}</h4>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-[10px] font-black uppercase tracking-widest text-amber-600 bg-amber-50 px-2 py-1 rounded border border-amber-100">{{ groupList.length }} Mapel</span>
                                <span class="text-slate-400 text-lg transition-transform duration-300" :class="{ 'rotate-180': isCollapsed[kelompok] }"><AppIcon name="chevron-down" class="w-5 h-5 inline-block mr-1" /></span>
                            </div>
                        </div>
                        
                        <div v-show="!isCollapsed[kelompok]" class="overflow-x-auto">
                            <div v-if="groupList.length === 0" class="p-6 text-center text-slate-400 font-bold text-[10px] uppercase tracking-widest bg-white">
                                Belum ada mapel di kelompok ini.
                            </div>
                            <table v-else class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-white text-[9px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">
                                        <th class="py-3 px-4 pl-6 w-16 text-center">No</th>
                                        <th class="py-3 px-4">Mata Pelajaran</th>
                                        <th class="py-3 px-4 text-center w-24">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="flex flex-col sm:table-row-group text-sm">
                                    <tr v-for="(item, index) in groupList" :key="item.id" class="border-b border-slate-100 hover:bg-slate-50/80 transition-colors bg-white group flex flex-col sm:table-row p-4 sm:p-0 relative">
                                        <td class="px-0 py-1 sm:p-4 text-left sm:text-center text-[10px] font-bold text-slate-400 flex sm:table-cell items-center justify-between">
                                            <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-slate-400">No</span>
                                            {{ index + 1 }}
                                        </td>
                                        <td class="px-0 py-1 sm:p-4 flex sm:table-cell items-center justify-between">
                                            <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-slate-400">Mapel</span>
                                            <div class="text-right sm:text-left">
                                                <p class="font-black text-slate-700 text-xs">{{ item.nama_mapel }}</p>
                                                <div class="flex items-center justify-end sm:justify-start gap-1.5 mt-0.5">
                                                    <span class="text-[9px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">{{ item.kode_mapel }}</span>
                                                    <span class="text-[9px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded border border-amber-100 bg-amber-50 text-amber-600" v-if="item.kurikulum">{{ item.kurikulum.nama_kurikulum }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-0 pt-3 sm:p-4 text-center border-t sm:border-0 border-slate-100 mt-2 sm:mt-0 flex sm:table-cell justify-center">
                                            <div class="flex items-center justify-center gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                                <button @click.stop="editData(item)" class="w-10 h-10 sm:w-8 sm:h-8 rounded-xl sm:rounded-lg bg-white border border-slate-200 text-slate-400 hover:border-amber-200 hover:bg-amber-50 hover:text-amber-600 flex items-center justify-center transition-all shadow-sm" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg></button>
                                                <button @click.stop="confirmDelete(item)" class="w-10 h-10 sm:w-8 sm:h-8 rounded-xl sm:rounded-lg bg-white border border-slate-200 text-slate-400 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 flex items-center justify-center transition-all shadow-sm" title="Hapus"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Table Content for Flat View -->
                <div v-else class="p-4 sm:p-6">
                  <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-[9px] font-black uppercase tracking-widest text-slate-500 border-b border-slate-200">
                                <th class="py-3 px-4 pl-6 w-16 text-center">No</th>
                                <th class="py-3 px-4">Mata Pelajaran</th>
                                <th class="py-3 px-4 text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="flex flex-col sm:table-row-group text-sm">
                            <tr v-for="(item, index) in mapels" :key="item.id" class="border-b border-slate-100 hover:bg-slate-50 transition-colors bg-white group flex flex-col sm:table-row p-4 sm:p-0 relative">
                                <td class="px-0 py-1 sm:p-4 text-left sm:text-center text-[10px] font-bold text-slate-400 flex sm:table-cell items-center justify-between">
                                    <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-slate-400">No</span>
                                    {{ index + 1 }}
                                </td>
                                <td class="px-0 py-1 sm:p-4 flex sm:table-cell items-center justify-between">
                                    <span class="sm:hidden text-[9px] font-black uppercase tracking-widest text-slate-400">Mapel</span>
                                    <div class="text-right sm:text-left">
                                        <p class="font-black text-slate-800 text-xs">{{ item.nama_mapel }}</p>
                                        <div class="flex items-center justify-end sm:justify-start gap-1.5 mt-0.5">
                                            <span class="text-[9px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">{{ item.kode_mapel }}</span>
                                            <span class="text-[9px] font-black uppercase tracking-widest px-1.5 py-0.5 rounded border border-amber-100 bg-amber-50 text-amber-600" v-if="item.kurikulum">{{ item.kurikulum.nama_kurikulum }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-0 pt-3 sm:p-4 text-center border-t sm:border-0 border-slate-100 mt-2 sm:mt-0 flex sm:table-cell justify-center">
                                    <div class="flex items-center justify-center gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                        <button @click="editData(item)" class="w-10 h-10 sm:w-8 sm:h-8 rounded-xl sm:rounded-lg bg-white border border-slate-200 text-slate-400 hover:border-amber-200 hover:bg-amber-50 hover:text-amber-600 flex items-center justify-center transition-all shadow-sm" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg></button>
                                        <button @click="confirmDelete(item)" class="w-10 h-10 sm:w-8 sm:h-8 rounded-xl sm:rounded-lg bg-white border border-slate-200 text-slate-400 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 flex items-center justify-center transition-all shadow-sm" title="Hapus"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
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

// Responsiveness detector
const windowWidth = ref(1024) 
const isDesktop = computed(() => windowWidth.value >= 1280) // xl breakpoint

// Tabs for Mobile
const activeTabMobile = ref('table')
const mobileTabs = [
  { id: 'form', title: 'Form Data', icon: 'document-text' },
  { id: 'table', title: 'Database', icon: 'clipboard' }
]

const kategoriTab = ref('')
const mapels = ref([])
const kurikulums = ref([])
const tahunAjaranAktif = ref(null)
const isLoading = ref(true)
const isSaving = ref(false)

const isEditing = ref(false)
const formData = ref({
    id: null,
    kurikulum_id: '',
    kode_mapel: '',
    nama_mapel: '',
    kategori: 'umum',
    kelompok: ''
})

const groupedMapels = computed(() => {
    const groups = { 
        'Belum Terkelompokkan': [] 
    };
    
    // Initialize groups from referensi
    refKelompokMapel.value.forEach(kel => {
        groups[kel.nama] = [];
    });
    
    mapels.value.forEach(m => {
        const found = refKelompokMapel.value.find(kel => kel.kode === m.kelompok);
        const k = found ? found.nama : 'Belum Terkelompokkan';
        
        if (!groups[k]) groups[k] = [];
        groups[k].push(m);
    });
    
    // Ordering logic
    const result = {};
    refKelompokMapel.value.forEach(kel => {
        if (kategoriTab.value === 'umum' || groups[kel.nama].length > 0) {
            result[kel.nama] = groups[kel.nama];
        }
    });
    
    if (groups['Belum Terkelompokkan'].length > 0) {
        result['Belum Terkelompokkan'] = groups['Belum Terkelompokkan'];
    }
    return result;
});

const { registerAutoSave, unregisterAutoSave } = useAutoSave()

onUnmounted(() => {
    unregisterAutoSave()
})

const isCollapsed = ref({})
const toggleCollapse = (k) => {
    isCollapsed.value[k] = !isCollapsed.value[k]
}

const isDeleteModalOpen = ref(false)
const deleteTarget = ref(null)

const refKategoriMapel = ref([])
const refKelompokMapel = ref([])

const fetchReferensi = async () => {
    const token = useCookie('auth_token').value
    try {
        const timestamp = Date.now()
        const [resKat, resKel] = await Promise.all([
            $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/referensi?jenis=kategori_mapel`, {
                headers: { 'Authorization': `Bearer ${token}` }
            }),
            $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/referensi?jenis=kelompok_mapel`, {
                headers: { 'Authorization': `Bearer ${token}` }
            })
        ])
        const rawKategori = resKat.data || []
        
        const sortOrder = ['umum', 'produktif', 'industri']
        refKategoriMapel.value = rawKategori.sort((a, b) => {
            const indexA = sortOrder.indexOf(a.kode.toLowerCase())
            const indexB = sortOrder.indexOf(b.kode.toLowerCase())
            if (indexA !== -1 && indexB !== -1) return indexA - indexB
            if (indexA !== -1) return -1
            if (indexB !== -1) return 1
            return a.nama.localeCompare(b.nama)
        })
        
        refKelompokMapel.value = resKel.data || []
        
        if (refKategoriMapel.value.length > 0) {
            kategoriTab.value = refKategoriMapel.value[0].kode
        }
        
    } catch (error) {
        console.error('Error fetching referensi', error)
    }
}

const filterKurikulum = ref('')

const fetchData = async () => {
    if (!kategoriTab.value) return
    isLoading.value = true
    const token = useCookie('auth_token').value
    try {
        let url = `${import.meta.env.VITE_API_BASE_URL}/api/kurikulum/mapel?kategori=${kategoriTab.value}`
        if (filterKurikulum.value) {
            url += `&kurikulum_id=${filterKurikulum.value}`
        }
        const response = await $fetch(url, {
            headers: { Authorization: `Bearer ${token}` }
        })
        if (response.success) {
            mapels.value = response.data
            kurikulums.value = response.kurikulums || []
            tahunAjaranAktif.value = response.tahun_ajaran_aktif || null
            formData.value.kategori = kategoriTab.value 
        }
    } catch (error) {
        console.error('Failed to fetch mapel:', error)
    } finally {
        isLoading.value = false
    }
}

const saveData = async () => {
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
            body: formData.value
        })
        
        if (response.success) {
            useSwal().toast(response.message, 'success')
            kategoriTab.value = formData.value.kategori
            resetForm()
            fetchData()
            if (!isDesktop.value) activeTabMobile.value = 'table'
        }
    } catch (error) {
        console.error('Save error:', error)
        let errMsg = 'Gagal menyimpan data mapel.';
        if (error.response && error.response.status === 422) {
            errMsg = 'Mohon periksa kembali isian form Anda.';
            if (error.response._data?.message) errMsg = error.response._data.message;
        } else if (error.response) {
            errMsg = `Gagal menyimpan: Terjadi kesalahan pada server (${error.response.status}).`;
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
        kategori: item.kategori,
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
        kategori: kategoriTab.value,
        kelompok: ''
    }
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

    // Auto-save logic
    registerAutoSave(async () => {
        if (formData.value.nama_mapel) {
            await saveData()
        }
    })

    await fetchReferensi()
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
