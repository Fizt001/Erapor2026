<template>
  <!-- Panduan Alur Kerja Sidebar Widget -->
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    
    <!-- Header -->
    <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-3" :class="headerBg">
      <div class="w-8 h-8 rounded-xl flex items-center justify-center text-lg shrink-0" :class="iconBg">
        <AppIcon v-if="iconName" :name="iconName" />
        <span v-else>{{ icon }}</span>
      </div>
      <div>
        <h3 class="text-xs font-black uppercase tracking-widest" :class="titleColor">{{ title }}</h3>
        <p class="text-[9px] font-bold text-slate-400 mt-0.5 uppercase tracking-wider">Panduan Alur Kerja</p>
      </div>
    </div>

    <!-- Steps -->
    <div class="p-4 space-y-1.5">
      <div
        v-for="(step, idx) in steps"
        :key="idx"
        class="flex items-start gap-3 p-3 rounded-xl transition-all"
        :class="step.active ? activeStepClass : 'bg-slate-50 hover:bg-slate-100'"
      >
        <!-- Nomor Urut -->
        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black shrink-0 mt-0.5"
          :class="step.done ? 'bg-emerald-500 text-white' : step.active ? activeNumClass : 'bg-slate-200 text-slate-500'">
          <span v-if="step.done">✓</span>
          <span v-else>{{ idx + 1 }}</span>
        </div>

        <!-- Konten -->
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2">
            <AppIcon v-if="step.iconName" :name="step.iconName" class="text-sm" />
            <span v-else class="text-sm">{{ step.emoji }}</span>
            <p class="text-[11px] font-black uppercase tracking-wide truncate"
              :class="step.active ? 'text-slate-800' : 'text-slate-500'">
              {{ step.label }}
            </p>
          </div>
          <p class="text-[9px] text-slate-400 font-bold mt-0.5 leading-tight">{{ step.desc }}</p>

          <!-- Link -->
          <NuxtLink v-if="step.active && step.to"
            :to="step.to"
            class="inline-flex items-center gap-1 mt-2 text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-lg transition-all"
            :class="activeLinkClass">
            Buka Menu →
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- Footer Note -->
    <div v-if="note" class="px-4 pb-4">
      <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 flex items-start gap-2">
        <span class="text-amber-500 text-xs shrink-0 mt-0.5">💡</span>
        <p class="text-[9px] font-bold text-amber-700 leading-relaxed">{{ note }}</p>
      </div>
    </div>

  </div>
</template>

<script setup>
const props = defineProps({
  title: { type: String, default: 'Alur Kerja' },
  icon: { type: String, default: '📋' },
  iconName: { type: String, default: '' },
  steps: { type: Array, default: () => [] },
  note: { type: String, default: '' },
  color: { type: String, default: 'emerald' }
})

const colorMap = {
  emerald: {
    headerBg: 'bg-emerald-50',
    iconBg: 'bg-emerald-100 text-emerald-700',
    titleColor: 'text-emerald-700',
    activeStepClass: 'bg-indigo-50 border border-indigo-100 shadow-sm',
    activeNumClass: 'bg-indigo-500 text-white',
    activeLinkClass: 'bg-indigo-500 text-white hover:bg-indigo-600'
  },
  sky: {
    headerBg: 'bg-sky-50',
    iconBg: 'bg-sky-100 text-sky-700',
    titleColor: 'text-sky-700',
    activeStepClass: 'bg-sky-50 border border-sky-100 shadow-sm',
    activeNumClass: 'bg-sky-500 text-white',
    activeLinkClass: 'bg-sky-500 text-white hover:bg-sky-600'
  },
  violet: {
    headerBg: 'bg-violet-50',
    iconBg: 'bg-violet-100 text-violet-700',
    titleColor: 'text-violet-700',
    activeStepClass: 'bg-violet-50 border border-violet-100 shadow-sm',
    activeNumClass: 'bg-violet-500 text-white',
    activeLinkClass: 'bg-violet-500 text-white hover:bg-violet-600'
  },
  rose: {
    headerBg: 'bg-rose-50',
    iconBg: 'bg-rose-100 text-rose-700',
    titleColor: 'text-rose-700',
    activeStepClass: 'bg-rose-50 border border-rose-100 shadow-sm',
    activeNumClass: 'bg-rose-500 text-white',
    activeLinkClass: 'bg-rose-500 text-white hover:bg-rose-600'
  },
  amber: {
    headerBg: 'bg-amber-50',
    iconBg: 'bg-amber-100 text-amber-700',
    titleColor: 'text-amber-700',
    activeStepClass: 'bg-amber-50 border border-amber-100 shadow-sm',
    activeNumClass: 'bg-amber-500 text-white',
    activeLinkClass: 'bg-amber-500 text-white hover:bg-amber-600'
  }
}

const c = computed(() => colorMap[props.color] || colorMap.emerald)
const headerBg = computed(() => c.value.headerBg)
const iconBg = computed(() => c.value.iconBg)
const titleColor = computed(() => c.value.titleColor)
const activeStepClass = computed(() => c.value.activeStepClass)
const activeNumClass = computed(() => c.value.activeNumClass)
const activeLinkClass = computed(() => c.value.activeLinkClass)
</script>
