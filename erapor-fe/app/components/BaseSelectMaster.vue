<template>
  <div class="form-control w-full">
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }}
    </label>
    
    <div class="relative">
      <select 
        :value="modelValue" 
        @input="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
        class="w-full rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 sm:text-sm appearance-none disabled:bg-gray-100 disabled:text-gray-500"
        :disabled="disabled || isLoading"
        :required="required"
      >
        <option value="" disabled>{{ placeholder || `Pilih ${label || jenis}` }}</option>
        <option 
          v-for="item in options" 
          :key="item.kode" 
          :value="item.kode"
        >
          {{ item.nama }}
        </option>
      </select>
      
      <!-- Loading Indicator or Chevron -->
      <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
        <svg v-if="isLoading" class="animate-spin h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <svg v-else class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
          <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
        </svg>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  jenis: {
    type: String,
    required: true
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  required: {
    type: Boolean,
    default: false
  }
})

defineEmits(['update:modelValue'])

const isLoading = ref(false)
const options = ref<any[]>([])
const { fetchReferensi } = useReferensi()

onMounted(async () => {
  isLoading.value = true
  try {
    options.value = await fetchReferensi(props.jenis)
  } finally {
    isLoading.value = false
  }
})
</script>
