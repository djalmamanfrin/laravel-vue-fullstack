<script setup>
import {useRoute} from 'vue-router';
import useBoardStore from "../store/board.js";
import {computed, onBeforeMount, onMounted, watch} from "vue";
import { CalendarDaysIcon, UserCircleIcon, ViewColumnsIcon, PhotoIcon, PlusIcon, TrashIcon,
  ChevronLeftIcon, ArchiveBoxIcon } from '@heroicons/vue/24/outline'
import MyButton from "../components/atoms/MyButton.vue";
import useImageStore from "../store/image.js";
import MyImage from "../components/atoms/MyImage.vue";
import {PencilIcon} from "@heroicons/vue/24/outline/index.js";
import MyEditableText from "../components/atoms/MyEditableText.vue";
import useNotificationStore from "../store/notification.js";
import MyNotification from "../components/atoms/MyNotification.vue";
import {NotificationTypes} from "../types/notification-types.js";
import router from "../router.js";
import MyTooltip from "../components/atoms/MyTooltip.vue";
import MyRecentChanges from "../components/atoms/MyRecentChanges.vue";

const route = useRoute();
const boardId = route.params.id;

const notification = useNotificationStore()

const boardStore = useBoardStore()
const board = computed(() => boardStore.board)

const imageStore = useImageStore();
const images = computed(() => imageStore.images)

const handleDeleteCollection = (collectionId) => {
  boardStore.deleteCollection(collectionId)
}

const handleDeleteBoard = () => {
  if (board.value.images_counter > 0) {
    notification.type = NotificationTypes.ERROR;
    notification.title = 'Board cannot be archived';
    notification.message = 'You cannot archive this board because it still contains images. Please remove them first.';
    notification.open();
    return;
  }

  boardStore.delete().then(() => {
    router.push({ name: 'Home'})

    notification.type = NotificationTypes.SUCCESS;
    notification.title = 'Board successfully deleted';
    notification.message = 'The board has been deleted successfully. All related data has been removed';
    notification.open();
  })
}

const handleCreateCollection = () => {
  if (board.value.collections.length >= 5) {
    notification.type = NotificationTypes.WARNING;
    notification.title = 'Collections limit reached';
    notification.message = 'The number of collections has exceeded the allowed limit of 5. Please remove some collections to continue.';
    notification.open();
    return;
  }
  let fields = {name: 'Untitled', order: board.value.collections.length + 1}
  boardStore.createCollection(fields)
      .catch(error => {})
}
const handleBoardNameChanged = (value) => {
  boardStore.update({name: value})
}

const handleCollectionNameChanged = (value, collectionId) => {
  let capitalizeValue = value.charAt(0).toUpperCase() + value.slice(1)
  boardStore.updateCollection(collectionId, {name: capitalizeValue})
}

watch(() => imageStore.images, () => {
  boardStore.get(boardId)
})

onBeforeMount(() => {
  boardStore.get(boardId)
  imageStore.all()
})

onMounted(() => {
  boardStore.fetchRecentChanges(boardId)
})

const onColumnDrag = (event, collectionId) => {
  event.dataTransfer.dropEffect = 'move'
  event.dataTransfer.effectAllowed = 'move'
  event.dataTransfer.setData('dragCollectionId', collectionId)

}

const onImageDrag = (event, imageId) => {
  event.dataTransfer.dropEffect = 'move'
  event.dataTransfer.effectAllowed = 'move'
  event.dataTransfer.setData('imageId', imageId)
};

const onImageDrop = (event, dropCollectionId) => {
  let imageId = event.dataTransfer.getData('imageId')
  if (imageId) {
    imageStore.update(imageId, {collection_id: dropCollectionId});
  }
  let dragCollectionId = event.dataTransfer.getData('dragCollectionId')
  if (dragCollectionId) {
    boardStore.update({
      drag_collection_id: dragCollectionId,
      drop_collection_id: dropCollectionId,
    })
  }
};
const getColumnClass = (index) => {
  return (index + 1 === board.value.collections.length)
    ? 'rounded-[calc(var(--radius-lg)+1px)] lg:rounded-r-[calc(2rem+1px)]'
    : 'rounded-[1rem]'
}
</script>

<template>
  <MyNotification
      v-if="notification.isOpened"
      :type="notification.type"
      :title="notification.title"
      :message="notification.message"
      @close="notification.close()"
  />
  <div class="bg-gray-50 pt-16 pb-8">
    <div class="mx-auto px-6 lg:px-8">
      <div class="lg:flex lg:items-center lg:justify-between">
        <div class="min-w-0 flex-1">
          <div v-if="board.name" class="flex items-center group">
            <button onclick="window.history.back()" class="">
              <ChevronLeftIcon class="block size-8 cursor-pointer font-semibold" aria-hidden="true"/>
            </button>
            <MyEditableText
              :text="board.name"
              html-tag="h2"
              styleText="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight"
              @text-changed="handleBoardNameChanged"
            />
            <PencilIcon
                class="size-5 text-gray-500 cursor-pointer ml-2 opacity-0 group-hover:opacity-100 transition-opacity"
            />
          </div>
          <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div v-if="board.owner" class="mt-2 flex items-center text-sm text-gray-500 gap-1">
              <UserCircleIcon class="block size-6" aria-hidden="true" />
              <span>Owner <b class="pl-1">{{ board.owner.name }}</b></span>
            </div>
            <div class="mt-2 flex items-center text-sm text-gray-500 gap-1">
              <MyTooltip @click="handleDeleteBoard" position="bottom" text="The last column is not included in the count" class="flex justify-center items-center cursor-pointer">
                <PhotoIcon class="block size-7" aria-hidden="true" />
                <span><b class="pl-1">{{ board.images_counter }} images</b> being worked on</span>
              </MyTooltip>
            </div>
            <div class="mt-2 flex items-center text-sm text-gray-500 gap-1">
              <MyTooltip @click="handleDeleteBoard" position="bottom" text="The My local images column is not included in the count" class="flex justify-center items-center cursor-pointer">
                <ViewColumnsIcon class="block size-7" aria-hidden="true" />
                <span><b class="pl-1"> {{ board.collections.length }} / 5</b> available collection</span>
              </MyTooltip>
            </div>
            <div class="mt-2 flex items-center text-sm text-gray-500 gap-1">
              <CalendarDaysIcon class="block size-6" aria-hidden="true" />
              <span>Created at <b class="pl-1">{{ board.created_at_formatted }}</b></span>
            </div>
          </div>
        </div>
        <div class="mt-5 flex justify-center">
          <MyTooltip @click="handleDeleteBoard" position="top" text="Archive board" class="flex justify-center items-center cursor-pointer">
              <ArchiveBoxIcon class="size-6 text-red-500"/>
          </MyTooltip>
          <MyRecentChanges/>
          <MyButton @click="handleCreateCollection" name="collection" :left-icon="PlusIcon" />
        </div>
      </div>

      <!-- Board Layout -->
      <div :class="`mt-8 grid gap-2 lg:grid-cols-6 h-screen`">
      <!-- My local images - column fixed -->
        <div class="relative lg:row-span-2 flex flex-col h-full">
          <div class="absolute inset-px rounded-lg bg-white lg:rounded-l-[2rem]" />
          <div
              @drop="onImageDrop($event, null)"
              @dragenter.prevent
              @dragover.prevent
              class="relative flex flex-col flex-1 overflow-auto rounded-[calc(var(--radius-lg)+1px)]
           lg:rounded-l-[calc(2rem+1px)] border-gray-300 scrollbar-hide"
          >
            <div class="relative px-3 pt-4">
              <p class="text-lg font-medium tracking-tight text-gray-950 text-center">
                My local images
              </p>
            </div>
            <div class="relative flex flex-col flex-1 overflow-auto rounded-[calc(var(--radius-lg)+1px)] scrollbar-hide">
              <div
                  v-if="images.length > 0"
                  v-for="image in images.filter(image => image.collection_id === null)"
                  :key="image.id"
                  draggable="true"
                  @dragstart="onImageDrag($event, image.id)"
                  class="flex items-start justify-center px-4 pt-4"
              >
                <div class="w-full flex flex-col items-start justify-start flex-grow">
                  <MyImage :image="image" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <template v-if="board.collections.length === 0">
          <div class="relative lg:row-span-2 flex flex-col h-full">
            <div class="absolute inset-px rounded-lg border-2 border-dashed border-gray-300" />
            <div class="flex flex-col flex-1 overflow-auto rounded-lg">
              <div class="px-8 py-6">
                <p class="text-lg font-medium tracking-tight text-gray-950 text-center">Step 1</p>
              </div>
              <div class="flex flex-1 items-center justify-center px-8 max-lg:pt-10 max-lg:pb-12 sm:px-10 lg:pb-2">
                <p class="text-center text-gray-500">No collections yet. Add new ones to start organizing.</p>
              </div>
            </div>
          </div>

          <div class="relative lg:row-span-2 flex flex-col h-full">
            <div class="absolute inset-px border-dashed border-2 rounded-[calc(var(--radius-lg)+1px)] lg:rounded-r-[calc(2rem+1px)] border-gray-300" />
            <div class="flex flex-col flex-1 overflow-auto rounded-lg">
              <div class="px-8 py-6">
                <p class="text-lg font-medium tracking-tight text-gray-950 text-center">Step 2</p>
              </div>
              <div class="flex flex-1 items-center justify-center px-8 max-lg:pt-10 max-lg:pb-12 sm:px-10 lg:pb-2">
                <p class="text-center text-gray-500">No collections yet. Add new ones to start organizing.</p>
              </div>
            </div>
          </div>
        </template>
        <template v-else>
          <div
              v-for="(collection, index) in board.collections"
              :key="collection.id"
              draggable="true"
              @dragstart="onColumnDrag($event, collection.id)"
              @drop="onImageDrop($event, collection.id)"
              @dragenter.prevent
              @dragover.prevent
              class="relative lg:row-span-2 flex flex-col h-full"
          >
            <div class="absolute inset-px rounded-lg bg-white" :class="getColumnClass(index)" />

            <div class="relative flex items-center justify-center w-full pt-4 px-3 group">
              <MyEditableText
                  :text="collection.name"
                  html-tag="p"
                  styleText="block w-full text-center text-lg font-medium tracking-tight text-gray-950"
                  @text-changed="handleCollectionNameChanged($event, collection.id)"
              />
              <div
                @click="handleDeleteCollection(collection.id)"
                class="absolute top-[15px] right-2 p-1 opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                <TrashIcon class="size-5 text-red-500"/>
              </div>
            </div>

            <div
                @drop="onImageDrop($event, collection.id)"
                @dragenter.prevent
                @dragover.prevent
                class="relative flex flex-col flex-1 overflow-auto rounded-[calc(var(--radius-lg)+1px)]"
            >
              <div
                  v-for="image in collection.images"
                  :key="image.id"
                  draggable="true"
                  @dragstart.stop="onImageDrag($event, image.id)"
                  class="flex items-start justify-center px-4 pt-4"
              >
                <div class="w-full flex flex-col items-start justify-start flex-grow">
                  <MyImage :image="image"/>
                </div>
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<style scoped>
.scrollbar-hide {
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
</style>
