<script setup>
import {computed, onBeforeMount, ref} from "vue";
import BoardLayout from "../components/BoardLayout.vue";
import MyTab from "../components/atoms/MyTab.vue";
import useImageStore from "../store/image.js";
import useBoardStore from "../store/board.js";
import MyImage from "../components/atoms/MyImage.vue";
import MyNotification from "../components/atoms/MyNotification.vue";
import router from "../router.js";
import PlusIcon from "@heroicons/vue/24/outline/PlusIcon.js";
import useModalStore from "../store/modal.js";
import useNotificationStore from "../store/notification.js";

const modal = useModalStore()
const notification = useNotificationStore()

const imageStore = useImageStore()
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
  boardStore.all()
})

const handleCreateBoard = () => {
  boardStore.create()
      .then((data) => {
        console.log(data)
        notification.type = 'success';
        notification.title = 'Successfully saved!';
        notification.message = 'Your board has been successfully created and is ready to use';
        notification.open();

        setTimeout(() => {
          router.push({ name: 'Board', params: { id: data.id } })
        }, 1000);
      })
      .catch(() => {
        notification.type = 'error';
        notification.title = 'Error board!';
        notification.message = 'Error to create the board';
        notification.open();
      })
}
</script>

<template>
  <div class="mt-4 p-4 mx-auto max-w-2xl lg:max-w-7xl">
    <div class="relative py-6 rounded-lg lg:row-span-2 flex flex-col">
      <MyTab :tabs="tabs" :active-tab="activeTab" @tab-changed="handleTabChanged"/>
      <div v-if="activeTab === 1" class="mx-auto flex max-w-2xl items-center space-x-2 px-4 sm:px-6 lg:max-w-7xl lg:px-8">
        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
          <div @click="modal.open()" class="flex items-center justify-center bg-gray-50 border border-gray-200 hover:bg-gray-100 hover:border-gray-300 cursor-pointer rounded-lg h-56 w-56">
            <PlusIcon class="w-12 h-12 text-gray-400" />
          </div>
          <article v-for="image in images" :key="image.id" class="flex flex-col items-start justify-between h-56 w-56">
            <div class="relative mt-1 flex items-center gap-x-4 group w-full">
              <MyImage :image="image" />
            </div>
          </article>
        </div>
      </div>
      <div v-if="activeTab === 2" class="mx-auto flex max-w-2xl items-center space-x-2 px-4 sm:px-6 lg:max-w-7xl lg:px-8">
        <BoardLayout :boards="boards" @handle-create-board="handleCreateBoard" />
      </div>
    </div>
  </div>
  <MyNotification
      v-if="notification.isOpened"
      :type="notification.type"
      :title="notification.title"
      :message="notification.message"
      @close="notification.close"
  />
</template>

<style scoped>
</style>
