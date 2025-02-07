<script setup>
import {computed, onBeforeMount, onMounted, ref} from "vue";
import BoardLayout from "../components/BoardLayout.vue";
import MyTab from "../components/atoms/MyTab.vue";
import useImageStore from "../store/image.js";
import useBoardStore from "../store/board.js";
import MyImage from "../components/atoms/MyImage.vue";

const imageStore = useImageStore();
const images = computed(() => imageStore.images)

const boardStore = useBoardStore();
const boards = computed(() => boardStore.boards)

const activeTab = ref(1)

const tabs = computed(() => [
  {
    id: 1,
    name: 'Gallery',
    counter: images.value.length
  },
  {
    id: 2,
    name: 'Board',
    counter: boards.value.length
  }
])
const handleTabChanged = (tabId) => {
  activeTab.value = tabId;
}

onBeforeMount(() => {
  imageStore.all()
})

onMounted(() => {
  boardStore.fetchBoards()
})
</script>

<template>
  <div class="mt-4 p-4 mx-auto max-w-2xl lg:max-w-7xl">
    <div class="relative py-6 rounded-lg lg:row-span-2 flex flex-col">
      <MyTab :tabs="tabs" :active-tab="activeTab" @tab-changed="handleTabChanged"/>
      <div v-if="activeTab === 1" class="mx-auto flex max-w-2xl items-center space-x-2 px-4 sm:px-6 lg:max-w-7xl lg:px-8">
        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
          <article v-for="image in images" :key="image.id" class="flex flex-col items-start justify-between">
            <div class="relative mt-1 flex items-center gap-x-4 group w-full">
              <MyImage :image="image" />
            </div>
          </article>
        </div>
      </div>
      <div v-if="activeTab === 2" class="mx-auto flex max-w-2xl items-center space-x-2 px-4 sm:px-6 lg:max-w-7xl lg:px-8">
        <BoardLayout :boards="boards"/>
      </div>
    </div>
  </div>
</template>

<style scoped>
</style>
