<template>
  <div class="h-full flex flex-col min-h-0 bg-slate-50">
    <!-- Layout 2 Panel Dock & Flow -->
    <div class="flex-1 flex flex-col xl:flex-row overflow-hidden relative">
      
      <!-- Panel Dock Kiri -->
      <div class="xl:w-[360px] w-full bg-white border-r border-slate-200 flex-shrink-0 flex flex-col h-full xl:z-10 shadow-[2px_0_10px_-4px_rgba(0,0,0,0.05)] overflow-y-auto custom-scrollbar">
        <div class="p-4 pb-2 space-y-4">
          <div class="bg-gradient-to-r from-sky-600 to-blue-700 rounded-2xl p-4 border border-sky-500 shadow-sm relative overflow-hidden flex items-center gap-3">
            <div class="w-8 h-8 flex items-center justify-center shrink-0 bg-white/10 rounded-lg relative z-10 text-white"><AppIcon name="document-text" class="w-5 h-5" /></div>
            <div class="relative z-10">
              <h3 class="text-xs font-black uppercase tracking-widest text-white">Filter Data</h3>
              <p class="text-[9px] text-sky-100 font-semibold uppercase mt-0.5">Asessmen Sumatif</p>
            </div>
            <div class="absolute right-0 bottom-0 opacity-15 text-white pointer-events-none">
              <svg class="w-16 h-16 transform translate-x-4 translate-y-4" fill="currentColor" viewBox="0 0 24 24"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            </div>
          </div>
          
          <div class="space-y-4">
            <!-- Tahun Ajaran -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Tahun Ajaran</label>
              <div v-if="references.tahunAjarans.length" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 font-bold text-xs text-slate-700 flex items-center justify-between">
                  <span>{{ references.tahunAjarans.find(t => t.is_aktif)?.tahun || '-' }}</span>
                  <span class="px-2 py-1 bg-sky-100 text-sky-700 rounded text-[9px] font-black uppercase tracking-widest">Aktif</span>
              </div>
            </div>
            
            <!-- Kurikulum -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Kurikulum</label>
              <select :value="filter.kurikulum_id" @change="handleFilterChange('kurikulum_id', $event)" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none" :disabled="isLoading || references.kurikulums.length === 0">
                <option v-if="references.kurikulums.length === 0" value="" disabled>Belum diseting</option>
                <option v-for="kur in references.kurikulums" :key="kur.id" :value="kur.id">{{ kur.nama_kurikulum }}</option>
              </select>
            </div>

            <!-- Periode (Titimangsa) -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Periode</label>
              <select :value="filter.titimangsa_id" @change="handleFilterChange('titimangsa_id', $event)" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none" :disabled="isLoading || references.periodes.length === 0">
                <option v-if="references.periodes.length === 0" value="" disabled>Belum diseting</option>
                <option v-else value="">- Pilih Periode -</option>
                <option v-for="per in references.periodes" :key="per.id" :value="per.id">
                  {{ per.nama_periode_panjang || per.nama_periode }} {{ per.is_aktif ? '(Aktif)' : '(Ditutup)' }}
                </option>
              </select>
            </div>

            <!-- Kelas -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Rombongan Belajar</label>
              <select :value="filter.kelas_id" @change="handleFilterChange('kelas_id', $event)" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none" :disabled="isLoading || references.kelases.length === 0">
                <option v-if="references.kelases.length === 0" value="" disabled>Belum diseting</option>
                <option v-else value="">- Pilih Rombel -</option>
                <option v-for="kelas in references.kelases" :key="kelas.id" :value="kelas.id">{{ kelas.tingkat }} {{ kelas.nama_kelas }}</option>
              </select>
            </div>

            <!-- Mata Pelajaran -->
            <div>
              <label class="block text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1">Mata Pelajaran</label>
              <select :value="filter.mapel_id" @change="handleFilterChange('mapel_id', $event)" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition-all font-semibold text-xs text-slate-700 outline-none" :disabled="isLoading || !filter.kelas_id || references.mapels.length === 0">
                <option v-if="references.mapels.length === 0" value="" disabled>Belum diseting</option>
                <option v-else value="">- Pilih Mapel -</option>
                <option v-for="mapel in references.mapels" :key="mapel.id" :value="mapel.id">{{ mapel.nama_mapel }}</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Panel Flow Kanan -->
      <div class="flex-1 bg-slate-50 flex flex-col h-full min-w-0 relative">
        <div class="p-6 lg:p-8 w-full h-full flex flex-col relative z-0">
          <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col flex-1 relative min-h-0">
            
            <!-- Header Card Kanan -->
            <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-white shrink-0 z-10">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center text-lg border border-sky-100">🏫</div>
                <div>
                  <h3 class="text-[13px] font-black leading-none uppercase tracking-wide text-slate-800">
                    {{ filter.kelas_id ? (references.kelases.find(k => k.id === filter.kelas_id)?.tingkat + ' ' + references.kelases.find(k => k.id === filter.kelas_id)?.nama_kelas) : 'Matriks Nilai Sumatif' }}
                  </h3>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ filter.kelas_id ? siswas.length + ' Siswa Aktif' : 'Sistem otomatis menyimpan ketikan anda' }}</p>
                </div>
              </div>
              
              <!-- Save Status Indicator -->
              <div class="flex items-center space-x-4" v-if="isFilterComplete && references.is_titimangsa_aktif">
                <div class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-lg border border-emerald-200 transition-all" v-if="saveStatus === 'saved'">
                  <span class="text-sm">✓</span> <span>Tersimpan</span>
                </div>
                <div class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest px-3 py-1.5 bg-amber-50 text-amber-600 rounded-lg border border-amber-200 transition-all" v-if="saveStatus === 'saving'">
                  <span class="animate-spin text-sm">↻</span> <span>Menyimpan...</span>
                </div>
              </div>
            </div>

            <!-- Alert Belum Lengkap -->
            <div v-if="!isFilterComplete" class="flex-grow flex flex-col items-center justify-center p-16 text-center relative overflow-hidden bg-slate-50/50">
              <div class="w-24 h-24 mb-6 rounded-full bg-sky-50 flex items-center justify-center border-8 border-white shadow-sm relative z-10">
                  <span class="text-4xl animate-bounce" style="animation-duration: 3s;">📋</span>
              </div>
              <h3 class="font-black text-slate-700 text-xl tracking-tight relative z-10 mb-2">Pilih Filter Data</h3>
              <p class="text-sm font-semibold text-slate-500 max-w-md relative z-10 leading-relaxed">
                  Matriks penilaian masih kosong. Silakan lengkapi parameter filter di panel sebelah kiri untuk memuat data siswa.
              </p>
              <div class="mt-8 flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest relative z-10">
                  <span class="animate-pulse text-lg">👈</span> Arahkan perhatian ke panel kiri
              </div>
            </div>

            <!-- Loading Indicator -->
            <div v-else-if="isLoading" class="flex-grow flex flex-col items-center justify-center p-20 opacity-60">
              <div class="w-8 h-8 border-4 border-sky-400 border-t-transparent rounded-full animate-spin mb-4"></div>
              <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Memuat Matriks...</span>
            </div>

            <!-- List Workspace Area (MATRIKS SUMATIF) -->
            <div v-else class="flex-grow flex flex-col relative bg-slate-50/30 overflow-hidden">
              
              <!-- Panel Konfigurasi Bobot Global -->
              <div class="bg-white border-b border-slate-200 p-3 shrink-0 shadow-sm z-10 flex flex-col xl:flex-row xl:items-center gap-3 xl:gap-6 justify-between">
                  <div class="flex items-center gap-2 shrink-0">
                      <span class="text-lg leading-none">⚖️</span>
                      <div>
                          <h4 class="text-[10px] font-black text-slate-700 uppercase tracking-widest leading-none">Konfigurasi Bobot (%)</h4>
                          <div class="text-[8px] font-black uppercase tracking-widest mt-1">
                              <span v-if="references.is_psts && (globalBobot.b_uh + globalBobot.b_ujian) !== 100" class="text-rose-500">⚠️ Harian+Ujian ≠ 100%</span>
                              <span v-else-if="!references.is_psts && (globalBobot.b_uh + globalBobot.b_ujian + globalBobot.b_psts_lalu) !== 100" class="text-rose-500">⚠️ UH+Ujian+PSTS ≠ 100%</span>
                              <span v-else-if="(globalBobot.b_praktek + globalBobot.b_teori) !== 100" class="text-rose-500">⚠️ Praktek+Teori ≠ 100%</span>
                              <span v-else class="text-emerald-500">✓ Valid</span>
                          </div>
                      </div>
                  </div>
                  
                  <div class="flex flex-1 gap-3 max-w-3xl xl:ml-auto">
                      <!-- BOX 1: BOBOT RAPOR -->
                      <div class="flex-[3] border border-slate-200 rounded flex items-center bg-slate-50 overflow-hidden">
                          <div class="bg-slate-100 border-r border-slate-200 px-2 py-1.5 h-full flex items-center justify-center shrink-0">
                              <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap">Rapor</span>
                          </div>
                          <div class="flex-1 flex items-center divide-x divide-slate-200 h-full">
                              <div class="flex-1 flex items-center px-1 group">
                                  <span class="text-[9px] font-bold text-slate-400 mr-1">UH</span>
                                  <input type="number" v-model="globalBobot.b_uh" @input="markAsUnsaved(null, null)" min="0" max="100" :disabled="!references.is_titimangsa_aktif"
                                      class="w-full bg-transparent text-[11px] font-black text-slate-700 text-center outline-none focus:ring-0 group-hover:bg-white transition-colors h-full">
                              </div>
                              <div class="flex-1 flex items-center px-1 group">
                                  <span class="text-[9px] font-bold text-slate-400 mr-1">UJI</span>
                                  <input type="number" v-model="globalBobot.b_ujian" @input="markAsUnsaved(null, null)" min="0" max="100" :disabled="!references.is_titimangsa_aktif"
                                      class="w-full bg-transparent text-[11px] font-black text-slate-700 text-center outline-none focus:ring-0 group-hover:bg-white transition-colors h-full">
                              </div>
                              <div v-if="!references.is_psts" class="flex-1 flex items-center px-1 group">
                                  <span class="text-[9px] font-bold text-slate-400 mr-1">PSTS</span>
                                  <input type="number" v-model="globalBobot.b_psts_lalu" @input="markAsUnsaved(null, null)" min="0" max="100" :disabled="!references.is_titimangsa_aktif"
                                      class="w-full bg-transparent text-[11px] font-black text-slate-700 text-center outline-none focus:ring-0 group-hover:bg-white transition-colors h-full">
                              </div>
                          </div>
                      </div>

                      <!-- BOX 2: BOBOT UJIAN -->
                      <div class="flex-[2] border border-slate-200 rounded flex items-center bg-slate-50 overflow-hidden">
                          <div class="bg-slate-100 border-r border-slate-200 px-2 py-1.5 h-full flex items-center justify-center shrink-0">
                              <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest whitespace-nowrap">Ujian</span>
                          </div>
                          <div class="flex-1 flex items-center divide-x divide-slate-200 h-full">
                              <div class="flex-1 flex items-center px-1 group">
                                  <span class="text-[9px] font-bold text-slate-400 mr-1">PRK</span>
                                  <input type="number" v-model="globalBobot.b_praktek" @input="markAsUnsaved(null, null)" min="0" max="100" :disabled="!references.is_titimangsa_aktif"
                                      class="w-full bg-transparent text-[11px] font-black text-slate-700 text-center outline-none focus:ring-0 group-hover:bg-white transition-colors h-full">
                              </div>
                              <div class="flex-1 flex items-center px-1 group">
                                  <span class="text-[9px] font-bold text-slate-400 mr-1">TEO</span>
                                  <input type="number" v-model="globalBobot.b_teori" @input="markAsUnsaved(null, null)" min="0" max="100" :disabled="!references.is_titimangsa_aktif"
                                      class="w-full bg-transparent text-[11px] font-black text-slate-700 text-center outline-none focus:ring-0 group-hover:bg-white transition-colors h-full">
                              </div>
                          </div>
                      </div>
                  </div>
              </div>

              <!-- Alert Periode Terkunci (Portal) -->
              <div v-if="!references.is_titimangsa_aktif" class="p-3 bg-amber-50 border-b border-amber-200 flex items-center gap-3 shadow-sm z-20">
                <div class="w-8 h-8 bg-white border border-amber-200 rounded-full flex items-center justify-center text-sm shrink-0 shadow-sm">🔒</div>
                <div>
                    <h4 class="text-[11px] font-black text-amber-700 uppercase tracking-widest">Akses Terkunci (Read-Only)</h4>
                    <p class="text-[9px] font-bold text-amber-600 uppercase tracking-widest mt-0.5">Periode ini sudah ditutup Admin. Data nilai tidak dapat diubah.</p>
                </div>
              </div>

              <div class="flex-1 overflow-auto custom-scrollbar relative pb-10">
                <table class="w-full text-left border-collapse bg-white table-fixed">
                  <thead class="sticky top-0 z-20 shadow-sm">
                    <!-- Header Grouping -->
                    <tr class="bg-slate-200 border-b border-slate-300">
                      <th rowspan="2" class="py-3 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500 sticky left-0 bg-slate-100 z-30 w-[180px] sm:w-[250px] border-r border-slate-300 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] align-middle">
                        Nama Siswa
                      </th>
                      <th colspan="4" class="py-1.5 px-1 border-r border-slate-300 text-center bg-indigo-50 text-[10px] font-black text-indigo-700 uppercase tracking-widest">
                        Nilai Harian (UH)
                      </th>
                      <th colspan="2" class="py-1.5 px-1 border-r border-slate-300 text-center bg-teal-50 text-[10px] font-black text-teal-700 uppercase tracking-widest">
                        Ujian Sumatif
                      </th>
                      <th rowspan="2" class="py-2 px-1 border-r border-slate-300 text-center w-[80px] bg-slate-50 align-middle" v-if="!references.is_psts">
                        <span class="text-[9px] font-black text-slate-600 uppercase tracking-tight block leading-tight">Nilai PSTS<br>Lalu</span>
                      </th>
                      <th rowspan="2" class="py-2 px-1 border-r border-slate-300 text-center w-[80px] bg-slate-50 align-middle">
                        <span class="text-[9px] font-black text-slate-600 uppercase tracking-tight block leading-tight">Nilai<br>Literasi</span>
                      </th>
                      <th rowspan="2" class="py-2 px-1 border-r border-slate-300 text-center w-[90px] bg-sky-100 align-middle shadow-inner">
                        <span class="text-[10px] font-black text-sky-800 uppercase tracking-wider block leading-tight">Nilai Akhir<br>(NA)</span>
                      </th>
                    </tr>
                    <!-- Sub Header -->
                    <tr class="bg-slate-100 border-b border-slate-200">
                      <th class="py-2 px-1 border-r border-slate-200 text-center w-[60px] bg-indigo-50/30 text-[9px] font-bold text-indigo-600 uppercase">UH1</th>
                      <th class="py-2 px-1 border-r border-slate-200 text-center w-[60px] bg-indigo-50/30 text-[9px] font-bold text-indigo-600 uppercase">UH2</th>
                      <th class="py-2 px-1 border-r border-slate-200 text-center w-[60px] bg-indigo-50/30 text-[9px] font-bold text-indigo-600 uppercase">UH3</th>
                      <th class="py-2 px-1 border-r border-slate-300 text-center w-[60px] bg-indigo-50/30 text-[9px] font-bold text-indigo-600 uppercase">UH4</th>
                      
                      <th class="py-2 px-1 border-r border-slate-200 text-center w-[70px] bg-teal-50/30 text-[9px] font-bold text-teal-600 uppercase">Praktek</th>
                      <th class="py-2 px-1 border-r border-slate-300 text-center w-[70px] bg-teal-50/30 text-[9px] font-bold text-teal-600 uppercase">Teori</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    <tr v-for="siswa in siswas" :key="siswa.id" class="hover:bg-sky-50/20 group transition-colors h-12">
                      <td class="py-2 px-4 sticky left-0 bg-white group-hover:bg-slate-50/90 z-10 border-r border-slate-100 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.02)] overflow-hidden text-ellipsis whitespace-nowrap">
                        <h4 class="text-[11px] font-black text-slate-700 uppercase tracking-wide truncate" :title="siswa.nama">{{ siswa.nama }}</h4>
                        <p class="text-[9px] font-bold text-slate-400">{{ siswa.nis }}</p>
                      </td>
                      
                      <!-- INPUT Harian (UH1-UH4) -->
                      <td v-for="uhField in ['uh1', 'uh2', 'uh3', 'uh4']" :key="uhField" class="p-0 border-r border-slate-100 text-center h-12 relative">
                        <input type="text" v-model="getNilai(siswa.id)[uhField]" @input="markAsUnsaved(siswa.id, uhField)" :disabled="!references.is_titimangsa_aktif"
                          class="w-full h-full border-none p-0 text-center font-black text-xs transition-all focus:ring-inset focus:ring-2 focus:ring-indigo-500"
                          :class="!references.is_titimangsa_aktif ? 'bg-slate-50 text-slate-400 cursor-not-allowed' : 'bg-transparent text-indigo-800 focus:bg-white hover:bg-indigo-50/30'"
                          placeholder="-">
                      </td>

                      <!-- INPUT Ujian (Praktek, Teori) -->
                      <td v-for="ujiField in ['praktek', 'teori']" :key="ujiField" class="p-0 border-r border-slate-100 text-center h-12 relative">
                        <input type="text" v-model="getNilai(siswa.id)[ujiField]" @input="markAsUnsaved(siswa.id, ujiField)" :disabled="!references.is_titimangsa_aktif"
                          class="w-full h-full border-none p-0 text-center font-black text-xs transition-all focus:ring-inset focus:ring-2 focus:ring-teal-500"
                          :class="!references.is_titimangsa_aktif ? 'bg-slate-50 text-slate-400 cursor-not-allowed' : 'bg-transparent text-teal-800 focus:bg-white hover:bg-teal-50/30'"
                          placeholder="-">
                      </td>

                      <!-- NILAI PSTS LALU (Read Only) -->
                      <td v-if="!references.is_psts" class="p-0 border-r border-slate-100 text-center h-12 bg-amber-50/30">
                          <span class="font-black text-xs text-amber-700">{{ getNilai(siswa.id).psts_lalu || '-' }}</span>
                      </td>

                      <!-- INPUT Literasi -->
                      <td class="p-0 border-r border-slate-100 text-center h-12 relative">
                        <input type="text" v-model="getNilai(siswa.id).literasi" @input="markAsUnsaved(siswa.id, 'literasi')" :disabled="!references.is_titimangsa_aktif"
                          class="w-full h-full border-none p-0 text-center font-black text-xs transition-all focus:ring-inset focus:ring-2 focus:ring-slate-500"
                          :class="!references.is_titimangsa_aktif ? 'bg-slate-50 text-slate-400 cursor-not-allowed' : 'bg-transparent text-slate-700 focus:bg-white hover:bg-slate-100/50'"
                          placeholder="-">
                      </td>

                      <!-- HASIL NA (Computed Real-Time) -->
                      <td class="p-0 border-r border-slate-100 text-center h-12 bg-sky-50/50">
                          <span class="font-black text-sm text-sky-700 tracking-tight">{{ hitungNA(siswa.id) }}</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              
              <!-- Bottom Sticky Config Info -->
              <div class="p-3 bg-white border-t border-slate-100 flex items-center justify-between shrink-0 absolute bottom-0 left-0 right-0 z-30 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                  <div class="flex items-center gap-2">
                      <div class="w-1.5 h-1.5 bg-sky-500 rounded-full"></div>
                      <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">
                          Pembagi Rata-Rata UH: {{ getMaxUh() }} Nilai Terbanyak
                      </p>
                  </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCookie, useNuxtApp } from '#app'
import { onBeforeRouteLeave } from 'vue-router'

definePageMeta({ 
  layout: "guru", 
  middleware: "guru",
  title: "Input Asessmen Sumatif"
})

const isLoading = ref(true)
const saveStatus = ref('idle')
const hasUnsavedChanges = ref(false)
const filter = ref({
  tahun_ajaran_id: '',
  kurikulum_id: '',
  titimangsa_id: '',
  mapel_id: '',
  kelas_id: ''
})

const references = ref({
  tahunAjarans: [],
  kurikulums: [],
  periodes: [],
  mapels: [],
  kelases: [],
  is_titimangsa_aktif: true,
  is_psts: false,
  jumlah_tp: 1
})

const globalBobot = ref({
    b_uh: 60,
    b_ujian: 40,
    b_praktek: 50,
    b_teori: 50,
    b_psts_lalu: 0
})

const getBobotLabel = (key) => {
    const labels = { b_uh: 'Harian', b_ujian: 'Ujian', b_praktek: 'Praktek', b_teori: 'Teori', b_psts_lalu: 'PSTS Lalu' }
    return labels[key] || key
}

const siswas = ref([])
const nilaiMatrix = ref([]) 
let debounceTimer = null

const isFilterComplete = computed(() => {
  return filter.value.tahun_ajaran_id && 
         filter.value.kurikulum_id && 
         filter.value.titimangsa_id && 
         filter.value.mapel_id && 
         filter.value.kelas_id
})

// === PORTAL FILTER ===
const handleFilterChange = async (key, event) => {
    const newVal = event.target.value
    if (hasUnsavedChanges.value) {
        const { $swal } = useNuxtApp()
        const result = await $swal.fire({
            title: 'Ada Nilai Belum Tersimpan!',
            text: 'Simpan perubahan sebelum pindah?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Abaikan'
        })

        if (result.isConfirmed) {
            clearTimeout(debounceTimer)
            await executeManualSave()
            filter.value[key] = newVal
            fetchData()
        } else if (result.dismiss === $swal.DismissReason.cancel) {
            hasUnsavedChanges.value = false
            filter.value[key] = newVal
            fetchData()
        } else {
            event.target.value = filter.value[key]
        }
    } else {
        filter.value[key] = newVal
        fetchData()
    }
}

// === FETCH DATA ===
const fetchData = async () => {
  isLoading.value = true
  try {
    const token = useCookie('auth_token').value
    const queryParams = new URLSearchParams()
    Object.keys(filter.value).forEach(key => {
      if (filter.value[key]) queryParams.append(key, filter.value[key])
    })

    const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/guru/sumatif/nilai?${queryParams.toString()}`, {
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' }
    })

    if (res.success) {
      references.value.tahunAjarans = res.data.tahun_ajarans || []
      references.value.kurikulums = res.data.kurikulums || []
      references.value.periodes = res.data.periodes || []
      references.value.mapels = res.data.mapels || []
      references.value.kelases = res.data.kelases || []
      references.value.is_titimangsa_aktif = res.data.selections.is_titimangsa_aktif
      references.value.is_psts = res.data.selections.is_psts
      references.value.jumlah_tp = res.data.jumlah_tp || 1
      
      if (res.data.global_bobot) {
          globalBobot.value = { ...res.data.global_bobot }
      }
      
      if (!filter.value.tahun_ajaran_id) filter.value.tahun_ajaran_id = res.data.selections.tahun_ajaran_id
      if (!filter.value.kurikulum_id) filter.value.kurikulum_id = res.data.selections.kurikulum_id
      if (!filter.value.titimangsa_id) filter.value.titimangsa_id = res.data.selections.titimangsa_id

      siswas.value = res.data.siswas || []
      
      const serverNilai = res.data.nilai || []
      const uiMatrix = []
      
      siswas.value.forEach(s => {
          const n = serverNilai.find(x => x.siswa_id === s.id)
          const pLalu = res.data.psts_data[s.id] || null
          uiMatrix.push({
            siswa_id: s.id,
            uh1: n ? n.uh1 : '',
            uh2: n ? n.uh2 : '',
            uh3: n ? n.uh3 : '',
            uh4: n ? n.uh4 : '',
            praktek: n ? n.praktek : '',
            teori: n ? n.teori : '',
            literasi: n ? n.literasi : '',
            psts_lalu: n && n.psts_lalu !== null ? n.psts_lalu : pLalu,
          })
      })
      nilaiMatrix.value = uiMatrix
    }
  } catch (error) {
    console.error('Failed to fetch data:', error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => fetchData())

// === HELPER ===
const getNilai = (siswaId) => {
  return nilaiMatrix.value.find(n => n.siswa_id === siswaId) || {}
}

const safeNum = (val) => {
    if (val === null || val === undefined || val === '') return 0
    const numVal = parseFloat(val.toString().replace(',', '.'))
    return isNaN(numVal) ? 0 : numVal
}

const getMaxUh = () => {
    let max = 1
    siswas.value.forEach(s => {
        const row = getNilai(s.id)
        let count = 0
        if(safeNum(row.uh1) > 0) count++
        if(safeNum(row.uh2) > 0) count++
        if(safeNum(row.uh3) > 0) count++
        if(safeNum(row.uh4) > 0) count++
        if(count > max) max = count
    })
    return max
}

const hitungNA = (siswaId) => {
    const row = getNilai(siswaId)
    const bPrk = safeNum(globalBobot.value.b_praktek) / 100
    const bTeo = safeNum(globalBobot.value.b_teori) / 100
    const bUh = safeNum(globalBobot.value.b_uh) / 100
    const bUji = safeNum(globalBobot.value.b_ujian) / 100
    const bLalu = references.value.is_psts ? 0 : safeNum(globalBobot.value.b_psts_lalu) / 100
    
    const divider = getMaxUh()
    const totalUh = safeNum(row.uh1) + safeNum(row.uh2) + safeNum(row.uh3) + safeNum(row.uh4)
    const avgUh = totalUh / divider
    
    const nUjian = (safeNum(row.praktek) * bPrk) + (safeNum(row.teori) * bTeo)
    
    let na = (avgUh * bUh) + (nUjian * bUji) + (safeNum(row.psts_lalu) * bLalu) + safeNum(row.literasi)
    
    if (totalUh === 0 && nUjian === 0 && safeNum(row.literasi) === 0 && safeNum(row.psts_lalu) === 0) return '-'
    return na.toFixed(1)
}

// === AUTO SAVE LOGIC ===
const markAsUnsaved = (siswaId = null, field = null) => {
    hasUnsavedChanges.value = true
    
    if (siswaId && field) {
        const cell = getNilai(siswaId)
        if(cell[field]) cell[field] = cell[field].toString().replace(/[^0-9.,]/g, '').replace(',', '.')
    }

    // Debounce Auto-Save Logic
    saveStatus.value = 'saving'
    clearTimeout(debounceTimer)
    debounceTimer = setTimeout(() => {
        executeManualSave()
    }, 1000)
}

const executeManualSave = async () => {
  if (!references.value.is_titimangsa_aktif || siswas.value.length === 0) {
      saveStatus.value = 'idle'
      return
  }

  saveStatus.value = 'saving'
  
  try {
    const token = useCookie('auth_token').value
    
    const bulkData = nilaiMatrix.value.map(row => ({
      siswa_id: row.siswa_id,
      uh1: row.uh1, uh2: row.uh2, uh3: row.uh3, uh4: row.uh4,
      praktek: row.praktek, teori: row.teori, literasi: row.literasi,
      psts_lalu: row.psts_lalu,
    }))

    const payload = {
      tahun_ajaran_id: filter.value.tahun_ajaran_id,
      kurikulum_id: filter.value.kurikulum_id,
      titimangsa_id: filter.value.titimangsa_id,
      mapel_id: filter.value.mapel_id,
      kelas_id: filter.value.kelas_id,
      
      max_uh: getMaxUh(),
      
      b_uh: globalBobot.value.b_uh,
      b_ujian: globalBobot.value.b_ujian,
      b_praktek: globalBobot.value.b_praktek,
      b_teori: globalBobot.value.b_teori,
      b_psts_lalu: globalBobot.value.b_psts_lalu,
      
      data: bulkData
    }

    const res = await $fetch(import.meta.env.VITE_API_BASE_URL + '/api/guru/sumatif/nilai/store', {
      method: 'POST',
      headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
      body: payload
    })

    if (res.success) {
      saveStatus.value = 'saved'
      hasUnsavedChanges.value = false
      setTimeout(() => { if(saveStatus.value === 'saved') saveStatus.value = 'idle' }, 2000)
    }
  } catch (error) {
    console.error('Auto Save Error:', error)
    saveStatus.value = 'idle'
    const { $swal } = useNuxtApp()
    $swal.fire({
      title: 'Gagal Menyimpan!',
      text: 'Terjadi kesalahan saat menyimpan data otomatis. Cek koneksi.',
      icon: 'error',
      toast: true,
      position: 'top-end',
      timer: 3000,
      showConfirmButton: false
    })
  }
}

// Guard Router Leave
onBeforeRouteLeave((to, from, next) => {
    if (hasUnsavedChanges.value) {
        useSwal().fire({
            title: 'Yakin ingin meninggalkan halaman?',
            text: 'Perubahan belum disimpan. Yakin ingin keluar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Tinggalkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                next()
            } else {
                next(false)
            }
        })
    } else {
        next()
    }
})

</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar:hover::-webkit-scrollbar-thumb { background: #94a3b8; }

/* Hide number input arrows */
input[type=number]::-webkit-inner-spin-button, 
input[type=number]::-webkit-outer-spin-button { 
  -webkit-appearance: none; 
  margin: 0; 
}
input[type=number] {
  -moz-appearance: textfield;
}
</style>
