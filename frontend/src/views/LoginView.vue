<template>
  <div class="pt-16">
    <h1 class="text-3xl font-semibold mb-4">Login</h1>
    <form v-if="!waitingOnVerification" action="#" @submit.prevent="handleLogin">
      <h5 class="text-2xl font-semibold">Enter your phone number</h5>
      <p class="text-gray-500 mb-4">We will send you a verification code</p>
        <div class="overflow-hidden shadow sm:rouded-md max-w-sm mx-auto text-left">
          <div class="bg-white px-4 py-5 sm:p-6">
            <div>
              <input type="text" v-maska data-maska="+57##########" v-model="credentials.phone" name="phone" id="phone" placeholder="+573101234567"
                class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm">
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
            <button type="submit" class="inline-flex justify-center rounded-md border border-tranparent bg-black py-2 text-white px-6">
              Submit
            </button>
          </div>
        </div>
      </form>
      <form v-else action="#" @submit.prevent="handleVerification">
        <h5 class="text-2xl font-semibold">Enter the code we sent you</h5>
        <p class="text-gray-500 mb-4">We sent a 6-digit code to your phone number ending in {{ credentials.phone.slice(-3) }}</p>
        <div class="overflow-hidden shadow sm:rouded-md max-w-sm mx-auto text-left">
          <div class="bg-white px-4 py-5 sm:p-6">
            <div>
              <input type="text" v-maska data-maska="######" v-model="credentials.code" name="phone" id="phone" placeholder="123456"
                class="mt-1 block w-full px-3 py-2 rounded-md border border-gray-300 shadow-sm">
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
            <button type="submit" class="inline-flex justify-center rounded-md border border-tranparent bg-black py-2 text-white px-6">
              Verify
            </button>
          </div>
        </div>
      </form>
  </div>

</template>

<script setup>
import { onMounted, reactive, ref, computed } from "vue"
import { vMaska } from "maska"
import axios from "axios"
import { useRouter } from "vue-router"

const router = useRouter()

const credentials = reactive({
  phone: null,
  code: null
})

const waitingOnVerification = ref(false)

onMounted(() => {
  if (localStorage.getItem('token')) {
    router.push({ name: 'landing' })
  }
})

// const formattedCredentials = computed(() => {
//   return {
//     phone: credentials.phone,
//     code: credentials.code
//   }
// })

const handleLogin = () => {
  axios.post('http://localhost/api/login', credentials)
    .then(() => {
      waitingOnVerification.value = true
    })
    .catch(error => {
      console.log(error);
      alert(error.response.data.message)
    })
}

const handleVerification = (event) => {
  axios.post('http://localhost/api/login/verify', credentials)
    .then(response => {
      localStorage.setItem('token', response.data)
      router.push({ name: 'landing' })
    })
    .catch(error => {
      console.log(error);
      alert(error.response.data.message)
    })
}

</script>

<style scoped>

</style>