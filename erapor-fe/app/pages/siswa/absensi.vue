<template>
  <div class="pb-12 mt-4 relative">
    <!-- Header -->
    <div class="mb-6">
      <h2 class="text-2xl font-black text-slate-800 tracking-tight">Kalender Kehadiran</h2>
      <p class="text-slate-500 text-sm font-medium mt-1">Pantau rekap kehadiran harian Anda selama satu bulan penuh.</p>
    </div>

    <!-- Superadmin Empty State -->
    <div v-if="isSuperadminWithoutImpersonation" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 flex flex-col items-center text-center">
      <div class="w-20 h-20 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mb-6 shadow-inner border border-amber-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
      </div>
      <h3 class="text-2xl font-black text-slate-800 mb-2">Menunggu Pilihan Siswa</h3>
      <p class="text-slate-500 text-base max-w-md mx-auto">Anda sedang dalam Mode Superadmin. Silakan pilih kelas dan nama siswa dari bilah menu di <strong class="text-amber-600 font-bold">pojok kanan atas</strong> untuk melihat Laporan Absensi.</p>
    </div>
    
    <div v-else>
        <!-- Filter Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                <div class="flex flex-col w-full sm:w-1/3">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Semester</label>
                    <select v-model="selectedSemester" class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 font-medium transition-colors cursor-pointer">
                        <option value="Ganjil">Semester Ganjil</option>
                        <option value="Genap">Semester Genap</option>
                    </select>
                </div>
                <div class="flex flex-col w-full sm:w-1/3">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Bulan</label>
                    <select v-model="selectedBulan" @change="fetchData" class="w-full bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 font-medium transition-colors cursor-pointer">
                        <option v-for="(nama, num) in listBulanTersedia" :key="num" :value="num">{{ nama }}</option>
                    </select>
                </div>
                <div class="hidden sm:block sm:w-1/3"></div>
            </div>
        </div>

        <!-- Calendar Container -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden relative">
            
            <div v-if="isLoading" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-10 flex items-center justify-center">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
            </div>

            <!-- Calendar Header -->
            <div class="bg-gradient-to-r from-rose-500 to-pink-600 p-6 text-white text-center relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="text-2xl font-black tracking-tight drop-shadow-sm capitalize">{{ namaBulan }} {{ calendarData?.tahun || '' }}</h3>
                    <p class="text-rose-100 text-sm font-medium mt-1">Laporan Absensi Siswa</p>
                </div>
                <div class="absolute right-0 top-0 opacity-20 transform translate-x-4 -translate-y-4">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 002 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM9 14H7v-2h2v2zm4 0h-2v-2h2v2zm4 0h-2v-2h2v2zm-8 4H7v-2h2v2zm4 0h-2v-2h2v2zm4 0h-2v-2h2v2z"/></svg>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="p-6 overflow-x-auto">
                <div class="min-w-[600px]">
                    <!-- Days Header -->
                    <div class="grid grid-cols-7 gap-px bg-slate-200 border-b border-slate-200 mb-px">
                        <div v-for="day in daysHeader" :key="day.id" class="bg-slate-50 py-3 text-center text-xs font-black uppercase tracking-wider" :class="day.class">
                            {{ day.name }}
                        </div>
                    </div>

                    <!-- Dates Grid -->
                    <div class="grid grid-cols-7 gap-px bg-slate-200 border-b border-x border-slate-200">
                        <!-- Empty slots for previous month -->
                        <div v-for="n in startingBlankDays" :key="'blank-'+n" class="bg-slate-50/50 min-h-[70px] p-2 relative"></div>

                        <!-- Actual Dates -->
                        <div v-for="dateInfo in calendarDays" :key="'date-'+dateInfo.date" 
                            class="min-h-[70px] p-2 flex flex-col relative transition-all group border border-transparent hover:border-indigo-100"
                            :class="{
                                'bg-emerald-500 hover:bg-emerald-600': dateInfo.status === 'H',
                                'bg-amber-500 hover:bg-amber-600': dateInfo.status === 'S',
                                'bg-sky-500 hover:bg-sky-600': dateInfo.status === 'I',
                                'bg-rose-500 hover:bg-rose-600': dateInfo.status === 'A',
                                'bg-indigo-500 hover:bg-indigo-600': dateInfo.status === 'P',
                                'bg-rose-50/30 hover:bg-slate-50': dateInfo.isSunday && !['H','S','I','A','P'].includes(dateInfo.status),
                                'bg-white hover:bg-slate-50': !dateInfo.isSunday && !['H','S','I','A','P'].includes(dateInfo.status)
                            }">
                            
                            <span class="text-xs font-bold absolute top-1.5 left-2" 
                                :class="{
                                    'text-white': ['H','S','I','A','P'].includes(dateInfo.status),
                                    'text-rose-500': dateInfo.isSunday && !['H','S','I','A','P'].includes(dateInfo.status),
                                    'text-slate-700': !dateInfo.isSunday && !['H','S','I','A','P'].includes(dateInfo.status)
                                }">
                                {{ dateInfo.date }}
                            </span>
                            
                            <div class="mt-5 flex-1 flex flex-col items-center justify-center">
                                <span v-if="dateInfo.isSunday && !['H','S','I','A','P'].includes(dateInfo.status)" class="text-[9px] font-bold text-rose-500 bg-rose-100 px-2 py-0.5 rounded-full mt-1 border border-rose-200">LIBUR</span>
                                <template v-else>
                                    <div v-if="dateInfo.status === 'H'" class="text-white font-black text-xs sm:text-sm uppercase tracking-widest drop-shadow-md" title="Hadir">HADIR</div>
                                    <div v-else-if="dateInfo.status === 'S'" class="text-white font-black text-xs sm:text-sm uppercase tracking-widest drop-shadow-md" title="Sakit">SAKIT</div>
                                    <div v-else-if="dateInfo.status === 'I'" class="text-white font-black text-xs sm:text-sm uppercase tracking-widest drop-shadow-md" title="Izin">IZIN</div>
                                    <div v-else-if="dateInfo.status === 'A'" class="text-white font-black text-xs sm:text-sm uppercase tracking-widest drop-shadow-md" title="Alpa">ALPA</div>
                                    <div v-else-if="dateInfo.status === 'P'" class="text-white font-black text-xs sm:text-sm uppercase tracking-widest drop-shadow-md" title="PKL">PKL</div>
                                    <!-- Kosong/Tidak ada pertemuan -->
                                    <div v-else-if="dateInfo.status === ''" class="w-4 h-1 bg-slate-200/60 rounded-full mt-1" title="Tidak Ada Jadwal/Absensi"></div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Legends -->
            <div class="bg-slate-50 border-t border-slate-200 p-4 flex flex-wrap gap-4 items-center justify-center text-xs font-semibold text-slate-600">
                <div class="flex items-center"><span class="w-4 h-4 rounded bg-emerald-500 border border-emerald-600 text-white flex items-center justify-center font-bold text-[8px] mr-2">H</span> Hadir</div>
                <div class="flex items-center"><span class="w-4 h-4 rounded bg-amber-500 border border-amber-600 text-white flex items-center justify-center font-bold text-[8px] mr-2">S</span> Sakit</div>
                <div class="flex items-center"><span class="w-4 h-4 rounded bg-sky-500 border border-sky-600 text-white flex items-center justify-center font-bold text-[8px] mr-2">I</span> Izin</div>
                <div class="flex items-center"><span class="w-4 h-4 rounded bg-rose-500 border border-rose-600 text-white flex items-center justify-center font-bold text-[8px] mr-2">A</span> Alpa</div>
                <div class="flex items-center"><span class="w-4 h-4 rounded bg-indigo-500 border border-indigo-600 text-white flex items-center justify-center font-bold text-[8px] mr-2">P</span> PKL</div>
                <div class="flex items-center"><span class="w-4 h-1 bg-slate-300 rounded-full mr-2"></span> Tidak Ada Jadwal</div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'

definePageMeta({
  layout: 'siswa',
  title: 'Laporan Absensi',
  middleware: 'siswa'
})

const isLoading = ref(true)
const errorMessage = ref('')
const calendarData = ref(null)

const listBulanGanjil = {
    7: 'Juli', 8: 'Agustus', 9: 'September', 10: 'Oktober', 11: 'November', 12: 'Desember'
}
const listBulanGenap = {
    1: 'Januari', 2: 'Februari', 3: 'Maret', 4: 'April', 5: 'Mei', 6: 'Juni'
}

const currentDate = new Date();
const currentMonth = currentDate.getMonth() + 1;
const defaultSemester = (currentMonth >= 7 && currentMonth <= 12) ? 'Ganjil' : 'Genap';

const selectedSemester = ref(defaultSemester);
const selectedBulan = ref(currentMonth);

const listBulanTersedia = computed(() => {
    return selectedSemester.value === 'Ganjil' ? listBulanGanjil : listBulanGenap;
});

watch(selectedSemester, (newVal) => {
    if (newVal === 'Ganjil') {
        selectedBulan.value = 7;
    } else {
        selectedBulan.value = 1;
    }
    fetchData();
});

const userProfile = useCookie('user_profile')

const isSuperadminWithoutImpersonation = computed(() => {
  let role = null;
  if (typeof userProfile.value === 'string') {
    try {
      role = JSON.parse(userProfile.value)?.role
    } catch (e) {
      role = null;
    }
  } else {
    role = userProfile.value?.role
  }
  return role === 'superadmin' && !!errorMessage.value
})

const fetchData = async () => {
    isLoading.value = true
    errorMessage.value = ''
    
    try {
        const tokenCookie = useCookie('auth_token')
        const res = await $fetch(`${import.meta.env.VITE_API_BASE_URL}/api/siswa/absensi?bulan=${selectedBulan.value}`, {
            headers: {
                'Authorization': `Bearer ${tokenCookie.value}`,
                'Accept': 'application/json'
            }
        })
        
        if (res.success) {
            calendarData.value = res.data
        } else {
            errorMessage.value = res.message || 'Gagal mengambil data'
        }
    } catch (e) {
        errorMessage.value = e.message || 'Terjadi kesalahan sistem'
    } finally {
        isLoading.value = false
    }
}

onMounted(() => {
    fetchData()
})

const namaBulan = computed(() => {
    return listBulanTersedia.value[selectedBulan.value] || ''
})

const daysHeader = [
    { id: 1, name: 'Min', class: 'text-rose-500' },
    { id: 2, name: 'Sen', class: 'text-sky-600' },
    { id: 3, name: 'Sel', class: 'text-sky-600' },
    { id: 4, name: 'Rab', class: 'text-sky-600' },
    { id: 5, name: 'Kam', class: 'text-sky-600' },
    { id: 6, name: 'Jum', class: 'text-emerald-600' },
    { id: 7, name: 'Sab', class: 'text-sky-600' }
]

const startingBlankDays = computed(() => {
    if (!calendarData.value) return 0;
    // Get the first day of the month (0 = Sunday, 1 = Monday, etc.)
    const firstDay = new Date(calendarData.value.tahun, calendarData.value.bulan - 1, 1).getDay();
    return firstDay;
})

const calendarDays = computed(() => {
    if (!calendarData.value) return [];
    
    const year = calendarData.value.tahun;
    const month = calendarData.value.bulan - 1;
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const absensiMap = calendarData.value.absensi || {};
    
    let days = [];
    for (let i = 1; i <= daysInMonth; i++) {
        const dateObj = new Date(year, month, i);
        const dayOfWeek = dateObj.getDay();
        const isSunday = dayOfWeek === 0;
        
        days.push({
            date: i,
            isSunday: isSunday,
            status: isSunday ? '' : (absensiMap[i] ?? '')
        });
    }
    
    return days;
})
</script>
