<script setup>
import GuestLayout from "../components/GuestLayout.vue";
import axiosClient from "../axios.js";
import {ref} from "vue";
import router from "../router.js";
import MyButton from "../components/atoms/MyButton.vue";

const data = ref({
  email: '',
  password: '',
})
const errorMessage = ref('')

function submit() {
  axiosClient.get('/sanctum/csrf-cookie').then(response => {
    axiosClient.post("/login", data.value)
        .then(response => {
          router.push({name: 'Home'})
        })
        .catch(error => {
          console.log(error.response)
          errorMessage.value = error.response.data.message;
        })
  });
}
</script>

<template>
  <GuestLayout>
    <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">
      Sign in to your account
    </h2>
    <div class="mt-4 sm:mx-auto sm:w-full sm:max-w-sm">
      <div v-if="errorMessage" class="mt-4 py-2 px-3 rounded text-white bg-red-400">
        {{errorMessage}}
      </div>
      <form @submit.prevent="submit" class="mt-2 space-y-6">
        <div>
          <label for="email" class="block text-sm/6 font-medium text-gray-900">Email address</label>
          <div class="mt-2">
            <input type="email" name="email" id="email" autocomplete="email" v-model="data.email" required=""
                   class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between">
            <label for="password" class="block text-sm/6 font-medium text-gray-900">Password</label>
            <div class="text-sm">
              <a href="#" class="font-semibold text-indigo-600 hover:text-indigo-500">Forgot password? (todo:need implementation)</a>
            </div>
          </div>
          <div class="mt-2">
            <input type="password" name="password" id="password" autocomplete="current-password" v-model="data.password" required=""
                   class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
          </div>
        </div>

        <div>
          <MyButton type="submit" name="Sign in" />
        </div>
      </form>

      <p class="mt-10 text-center text-sm/6 text-gray-500">
        Not a member?
        {{ ' ' }}
        <RouterLink :to="{name: 'Signup'}" class="font-semibold text-indigo-600 hover:text-indigo-500">
         Create an account
        </RouterLink>
      </p>
    </div>
  </GuestLayout>
</template>
<style scoped>

</style>
