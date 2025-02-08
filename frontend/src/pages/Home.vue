<script setup>
import {computed, onBeforeMount, ref} from "vue";
import BoardLayout from "../components/BoardLayout.vue";
import MyTab from "../components/atoms/MyTab.vue";
import useImageStore from "../store/image.js";
import useBoardStore from "../store/board.js";
import MyImage from "../components/atoms/MyImage.vue";
import MyNotification from "../components/atoms/MyNotification.vue";
import router from "../router.js";

const showNotification = ref(false);
const notification = ref({
  type: '',
  title: '',
  message: '',
})

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
  boardStore.all()
})

const handleCreateBoard = () => {
  boardStore.create()
      .then(({data}) => {
        debugger
        notification.value.type = 'success';
        notification.value.title = 'Successfully saved!';
        notification.value.message = 'Your board has been successfully created and is ready to use';
        showNotification.value = true;

        setTimeout(() => {
          router.push({ name: 'Board', params: { id: data.id } })
        }, 5000);
      })
      .catch(() => {
        notification.value.type = 'error';
        notification.value.title = 'Error board!';
        notification.value.message = 'Error to create the board';
        showNotification.value = true;
      })
}
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
        <BoardLayout :boards="boards" @handle-create-board="handleCreateBoard" />
      </div>
    </div>
  </div>
  <MyNotification
      v-if="showNotification"
      :type="notification.type"
      :title="notification.title"
      :message="notification.message"
      @close="showNotification = false"
  />
</template>

<style scoped>
</style>
