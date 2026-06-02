<template>
  <div class="animate-fadeIn">
    <div class="mb-6">
      <h2 class="text-2xl font-black text-slate-800 tracking-tight">{{ title }}</h2>
      <p class="text-sm font-semibold text-slate-500 mt-1">Halaman sedang dalam tahap pengembangan (Under Construction).</p>
    </div>
    
    <div class="bg-white rounded-2xl p-20 flex flex-col items-center justify-center border-2 border-dashed border-slate-200 shadow-sm text-center">
      <div class="text-6xl mb-6">??</div>
      <h3 class="text-lg font-black text-slate-800 uppercase tracking-widest">Under Construction</h3>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: "guru", middleware: "guru" })
const route = useRoute()
const title = route.path.split("/").pop().replace("-", " ").toUpperCase()
</script>
