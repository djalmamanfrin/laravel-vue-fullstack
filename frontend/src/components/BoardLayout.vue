<script setup>
import useBoardStore from "../store/board.js";
import {computed, onBeforeMount} from "vue";

const boardStore = useBoardStore();
const boards = computed(() => boardStore.boards)

const fetchBoards = () => { boardStore.fetchBoards() }

onBeforeMount(fetchBoards)

</script>

<template>
  <div class="mx-auto grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 lg:mx-0 lg:max-w-none lg:grid-cols-3">
    <article v-for="board in boards" :key="board.id" class="flex max-w-xl flex-col items-start justify-between">
      <div class="flex items-center justify-between mt-8 text-xs w-full">
        <h3 class="text-2xl font-bold text-gray-900">
          {{ board.name }}
        </h3>
        <time :datetime="board.created_at" class="text-gray-500 ml-auto">
          {{ board.created_at_formatted }}
        </time>
      </div>
      <RouterLink
          :to="{ name: 'Board', params: { id: board.id } }"
          class="relative mt-1 flex items-center gap-x-4"
      >
        <img src="https://letsenhance.io/static/73136da51c245e80edc6ccfe44888a99/1015f/MainBefore.jpg" alt=""
             class="rounded-lg cursor-pointer bg-white object-cover group-hover:opacity-75 max-sm:h-16 sm:aspect-[3/2] lg:aspect-[4/3]"/>
      </RouterLink>
    </article>
  </div>
</template>

<style scoped>
</style>
