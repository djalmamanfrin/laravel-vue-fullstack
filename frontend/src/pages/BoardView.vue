<script setup>
import { useRoute } from 'vue-router';
import useBoardStore from "../store/board.js";
import {computed, onBeforeMount} from "vue";

const route = useRoute();
const boardId = route.params.id;

const boardStore = useBoardStore()
const board = computed(() => boardStore.board)
onBeforeMount(() => { boardStore.setSelectedBoard(boardId) })

</script>

<template>
  <div class="bg-gray-50 py-24 sm:py-32">
    <div class="mx-auto max-w-2xl px-6 lg:max-w-7xl lg:px-8">
      <h2 class="text-center text-base/7 font-semibold text-indigo-600">Created at {{ board.created_at_formatted }}</h2>
      <p class="mx-auto mt-2 max-w-lg text-center text-4xl font-semibold tracking-tight text-balance text-gray-950 sm:text-5xl">
        {{ board.name }}</p>
      <div class="mt-10 grid gap-4 sm:mt-16 lg:grid-cols-3 lg:grid-rows-2 h-screen">
        <div class="relative lg:row-span-2 flex flex-col h-full">
          <div class="absolute inset-px rounded-lg bg-white lg:rounded-l-[2rem]" />
          <div class="relative flex flex-col flex-1 overflow-auto rounded-[calc(var(--radius-lg)+1px)] lg:rounded-l-[calc(2rem+1px)]">
            <div class="px-8 pt-8 pb-3 sm:px-10 sm:pt-10 sm:pb-0">
              <p class="mt-2 text-lg font-medium tracking-tight text-gray-950 text-center">My local images</p>
            </div>
            <div class="flex flex-1 items-center justify-center px-8 max-lg:pt-10 max-lg:pb-12 sm:px-10 lg:pb-2">
              imagens aqui
            </div>
          </div>
        </div>

        <div class="relative lg:row-span-2 flex flex-col h-full">
          <div class="absolute inset-px rounded-lg bg-white max-lg:rounded-t-[2rem]" />
          <div class="relative flex flex-col flex-1 overflow-auto rounded-[calc(var(--radius-lg)+1px)] max-lg:rounded-t-[calc(2rem+1px)]">
            <div class="px-8 pt-8 sm:px-10 sm:pt-10">
              <p class="mt-2 text-lg font-medium tracking-tight text-gray-950 text-center">Mobile friendly</p>
            </div>
            <div class="flex flex-1 items-center justify-center px-8 max-lg:pt-10 max-lg:pb-12 sm:px-10 lg:pb-2">
              imagens aqui
            </div>
          </div>
        </div>

        <div class="relative lg:row-span-2 flex flex-col h-full">
          <div class="absolute inset-px rounded-lg bg-white max-lg:rounded-b-[2rem] lg:rounded-r-[2rem]" />
          <div class="relative flex flex-col flex-1 overflow-auto rounded-[calc(var(--radius-lg)+1px)] max-lg:rounded-b-[calc(2rem+1px)] lg:rounded-r-[calc(2rem+1px)]">
            <div class="px-8 pt-8 pb-3 sm:px-10 sm:pt-10 sm:pb-0">
              <p class="mt-2 text-lg font-medium tracking-tight text-gray-950 text-center">Mobile friendly</p>
            </div>
            <div class="flex flex-1 items-center justify-center px-8 max-lg:pt-10 max-lg:pb-12 sm:px-10 lg:pb-2">
              imagens aqui
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
</style>
