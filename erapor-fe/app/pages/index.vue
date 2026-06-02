<template>
  <div>Redirecting...</div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { onMounted } from 'vue'

const router = useRouter()

onMounted(() => {
  router.push('/login')
})
</script>
