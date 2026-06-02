<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        Login Erapor
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Masukkan Email atau NIS (untuk Siswa)
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form class="space-y-6" @submit.prevent="handleLogin">
          
          <!-- Error Message -->
          <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-400 p-4">
            <div class="flex">
              <div class="ml-3">
                <p class="text-sm text-red-700">
                  {{ errorMessage }}
                </p>
              </div>
            </div>
          </div>

          <div>
            <label for="username" class="block text-sm font-medium text-gray-700">
              Email / NIS
            </label>
            <div class="mt-1">
              <input id="username" v-model="form.username" name="username" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Contoh: guru@sekolah.com atau 102938">
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
              Password
            </label>
            <div class="mt-1">
              <input id="password" v-model="form.password" name="password" type="password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
          </div>

          <div>
            <button type="submit" :disabled="isLoading" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
              <span v-if="isLoading">Memproses...</span>
              <span v-else>Masuk</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const form = reactive({
  username: '',
  password: ''
})

const errorMessage = ref('')
const isLoading = ref(false)

const handleLogin = async () => {
  isLoading.value = true
  errorMessage.value = ''
  
  try {
    const response = await $fetch('http://localhost:8000/api/login', {
      method: 'POST',
      body: form
    })
    
    if (response.success) {
      // Simpan token di cookie
      const tokenCookie = useCookie('auth_token')
      tokenCookie.value = response.data.token
      
      const user = response.data.user
      
      // Simpan profil user di cookie untuk keperluan layout dan middleware
      const userCookie = useCookie('user_profile')
      userCookie.value = JSON.stringify(user)
      
      // Redirect berdasarkan role
      if (user.role === 'siswa') {
        router.push('/siswa/dashboard')
      } else if (user.role === 'admin') {
        router.push('/admin/dashboard')
      } else if (user.role === 'kurikulum') {
        router.push('/kurikulum/dashboard')
      } else if (user.role === 'guru') {
        router.push('/guru/dashboard')
      } else if (user.role === 'kepsek') {
        router.push('/kepsek/dashboard')
      } else if (user.role === 'bk') {
        router.push('/bk/dashboard')
      } else {
        router.push('/dashboard')
      }
    }
  } catch (error) {
    if (error.response && error.response._data && error.response._data.message) {
      errorMessage.value = error.response._data.message
    } else {
      errorMessage.value = 'Gagal terhubung ke server.'
    }
  } finally {
    isLoading.value = false
  }
}
</script>
