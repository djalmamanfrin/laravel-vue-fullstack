<script setup>
import ModalLayout from "../components/ModalLayout.vue";
import {ref} from "vue";
import BoardLayout from "../components/BoardLayout.vue";

const isModalOpen = ref(false);
</script>

<template>
  <div class="bg-gray-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-2xl py-1 sm:py-8 lg:max-w-none lg:py-8">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-bold text-gray-900"> My Boards </h2>
          <button @click="isModalOpen = true" class="p-2 text-gray-900 cursor-pointer hover:text-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
          </button>
        </div>

       <BoardLayout/>
      </div>
    </div>
  </div>
  <ModalLayout v-if="isModalOpen" @close="isModalOpen = false"/>
</template>

<style scoped>
</style>
