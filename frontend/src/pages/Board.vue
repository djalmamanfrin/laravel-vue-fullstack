<script setup>
import {useRoute} from 'vue-router';
import useBoardStore from "../store/board.js";
import {computed, onBeforeMount, onMounted, ref, watch} from "vue";
import { CalendarDaysIcon, UserCircleIcon, ViewColumnsIcon, PhotoIcon, PlusIcon, PencilIcon, BellIcon, BellAlertIcon, TrashIcon, ChevronLeftIcon } from '@heroicons/vue/24/outline'
import MyButton from "../components/atoms/MyButton.vue";
import useImageStore from "../store/image.js";
import MyImage from "../components/atoms/MyImage.vue";
import MyModal from "../components/atoms/MyModal.vue";
import {ExclamationTriangleIcon, XMarkIcon} from "@heroicons/vue/24/outline/index.js";
import {Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot} from "@headlessui/vue";

const route = useRoute();
const boardId = route.params.id;

const info = ref({
  collections: [],
})

const isDrawerOpened = ref(false)
const isCollectionModalOpened = ref(false)
const boardStore = useBoardStore()
const board = computed(() => boardStore.board)
const recentChanges = computed(() => boardStore.recentChanges)

const imageStore = useImageStore();
const images = computed(() => imageStore.images)

const combineCollections = ref([]);
const inputCollection = ref(null)

const removeBoardCollection = (item) => {
  combineCollections.value = combineCollections.value.filter(collection => collection.slug !== item.slug)
  boardStore.deleteCollection(item.id)
}

const createCollection = () => {
  if (combineCollections.value.length >= 5) {
    info.value.collections = ['The limit of 6 collections has been reached.']
  }
  info.value.collections = ['Collection added! Press Save to persist']
  let capitalizeName = inputCollection.value.replace(/\b\w/g, char => char.toUpperCase())
  combineCollections.value.push({name: capitalizeName, slug: crypto.randomUUID(), order: board.value.collections.length + 1})
  inputCollection.value = null
}

const getNewCollectionsToPersist = () => {
  return combineCollections.value
      .filter(collection => !collection.hasOwnProperty('id'))
}
const submit = () => {
  let newCollection = getNewCollectionsToPersist()
  if (newCollection.length === 0) {
    info.value.collections = ['Collection not found! Type a new one and press Enter to save']
    return;
  }

  info.value.collections = []
  newCollection
    .map(collection => {
      boardStore.createCollection({name: collection.name, order: collection.order})
          .then(() => {
            combineCollections.value = []
            isCollectionModalOpened.value = false;
          })
          .catch(error => {
            info.value.collections = [error.response.data.message]
          })
    })
}
const editableName = ref(null)
const editing = ref(false)

const saveName = () => {
  if (!editing.value) return;
  editing.value = false;
  boardStore.update({name: editableName.value})
};

watch(() => editing.value, () => {
  editableName.value = board.value.name;
});

watch(() => boardStore.board, (newBoard) => {
  combineCollections.value = newBoard.collections
})

watch(() => imageStore.images, () => {
  boardStore.get(boardId)
})

watch(() => isCollectionModalOpened.value, () => {
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
  <MyModal :is-opened="isCollectionModalOpened" @close-modal="isCollectionModalOpened = false">
    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
      <div class="sm:flex sm:items-center sm:space-x-4 py-3">
        <div
            class="mx-auto flex size-16 shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:size-10">
          <ExclamationTriangleIcon class="size-6 text-blue-600" aria-hidden="true"/>
        </div>
        <DialogTitle as="h3" class="sm:ml-2 text-base font-semibold text-gray-900">
          Create the collations
        </DialogTitle>
      </div>
      <div class="sm:flex sm:items-start">
        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
          <div class="mb-4">
            <p
                class="mb-2 text-sm text-center font-medium bg-yellow-100 w-full mb-1 p-2"
            >
              <b>{{ board.collections.length }} / 5 </b> available collection
            </p>
            <label for="collectionDescription" class="block text-sm font-medium text-gray-700">
              Type a collection name to add:
            </label>
            <input
                @keyup.enter="createCollection"
                v-model="inputCollection"
                type="text"
                name="input-title"
                class="w-full border rounded-lg p-3 mt-2"
                placeholder="Collection name"
            />
          </div>
          <div class="relative px-2">
            <ul v-if="combineCollections.length === 0" class="divide-y divide-gray-100">
              <li  class="flex justify-between gap-x-6 py-2">
                <p class="text-gray-500">No collection yet</p>
              </li>
            </ul>
            <ul
                v-else
                v-for="(collection, index) in combineCollections"
                class="divide-y divide-gray-100"
            >
                <li class="flex justify-between cursor-pointer gap-x-6 py-2">
                  <div class="flex min-w-0 gap-x-4">
                    <p class="text-gray-500">{{ ++index }}</p>
                    <p class="text-gray-500">{{ collection.name }}</p>
                  </div>
                  <div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end">
                    <TrashIcon  @click="removeBoardCollection(collection)" class="cursor-pointer text-red-600 block size-5" aria-hidden="true"/>
                  </div>
                </li>
            </ul>
            <p class="text-sm mt-1 text-center text-yellow-600">
              {{ info.collections ? info.collections[0] : '' }}
            </p>
          </div>
        </div>
      </div>
    </div>
    <div class="bg-gray-50 gap-4 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
      <MyButton v-if="getNewCollectionsToPersist().length > 0" @click="submit" name="save" />

      <button type="button"
              @click="isCollectionModalOpened = false"
              class="mt-3 inline-flex w-full justify-center cursor-pointer rounded-md bg-white px-8 py-3 text-sm font-semibold text-gray-900 ring-1 shadow-xs ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto">
        Back
      </button>
    </div>
  </MyModal>
  <div class="bg-gray-50 pt-16 pb-8">
    <div class="mx-auto max-w-2xl px-6 lg:max-w-7xl lg:px-8">
      <div class="lg:flex lg:items-center lg:justify-between">
        <div class="min-w-0 flex-1">
          <div class="flex items-center">
            <button onclick="window.history.back()" class="">
              <ChevronLeftIcon class="block size-8 cursor-pointer font-semibold" aria-hidden="true"/>
            </button>
            <div class="relative group flex items-center">
              <input
                  v-if="editing"
                  v-model="editableName"
                  @blur="saveName"
                  @keyup.enter="saveName"
                  class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight border border-gray-300 rounded px-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  autofocus
              />
              <h2
                  v-else
                  class="text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight cursor-pointer"
                  @click="editing = true"
              >
                {{ board.name }}
              </h2>
              <PencilIcon
                  class="size-5 text-gray-500 cursor-pointer ml-2 opacity-70 group-hover:opacity-100 transition-opacity"
                  @click="editing = true"
              />
            </div>
          </div>
          <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
            <div v-if="board.owner" class="mt-2 flex items-center text-sm text-gray-500 gap-1">
              <UserCircleIcon class="block size-6" aria-hidden="true" />
              <span>Owner</span> <b>{{ board.owner.name }}</b>
            </div>
            <div class="mt-2 flex items-center text-sm text-gray-500 gap-1">
              <PhotoIcon class="block size-7" aria-hidden="true" />
              <b>{{ board.images_counter }} images</b> being worked on
            </div>
            <div class="mt-2 flex items-center text-sm text-gray-500 gap-1">
              <ViewColumnsIcon class="block size-7" aria-hidden="true" />
              <b>{{ board.collections.length }} / 5 </b> available collection
            </div>
            <div class="mt-2 flex items-center text-sm text-gray-500 gap-1">
              <CalendarDaysIcon class="block size-6" aria-hidden="true" />
              <span>Created at</span> <b>{{ board.created_at_formatted }}</b>
            </div>
          </div>
        </div>
        <div class="mt-5 flex lg:mt-0 lg:ml-4">
          <span @click="isDrawerOpened = true" class="flex items-center cursor-pointer sm:ml-3">
            <BellAlertIcon v-if="boardStore.unreadChangesCount" class="block text-yellow-500 size-6" aria-hidden="true" />
            <BellIcon v-else class="block size-6" aria-hidden="true" />
          </span>
          <span class="sm:ml-3">
            <MyButton @click="isCollectionModalOpened = true" name="collection" :left-icon="PlusIcon" />
          </span>
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
        <template v-if="combineCollections.length === 0">
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
            <div class="absolute inset-px rounded-lg border-dashed border-2 rounded-[calc(var(--radius-lg)+1px)] lg:rounded-r-[calc(2rem+1px)] border-gray-300" />
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

            <div class="relative px-3 pt-4">
              <p class="text-lg font-medium tracking-tight text-gray-950 text-center">{{ collection.name }}</p>
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

  <TransitionRoot as="template" :show="isDrawerOpened">
    <Dialog class="relative z-10" @close="isDrawerOpened = false">
      <TransitionChild as="template" enter="ease-in-out duration-500" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in-out duration-500" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
          <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
            <TransitionChild as="template" enter="transform transition ease-in-out duration-500 sm:duration-700" enter-from="translate-x-full" enter-to="translate-x-0" leave="transform transition ease-in-out duration-500 sm:duration-700" leave-from="translate-x-0" leave-to="translate-x-full">
              <DialogPanel class="pointer-events-auto relative w-screen max-w-md">
                <TransitionChild as="template" enter="ease-in-out duration-500" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in-out duration-500" leave-from="opacity-100" leave-to="opacity-0">
                  <div class="absolute top-0 left-0 -ml-8 flex pt-4 pr-2 sm:-ml-10 sm:pr-4">
                    <button type="button" class="relative cursor-pointer rounded-md text-gray-300 hover:text-white focus:ring-2 focus:ring-white focus:outline-hidden" @click="isDrawerOpened = false">
                      <span class="absolute -inset-2.5" />
                      <span class="sr-only">Close panel</span>
                      <XMarkIcon class="size-6" aria-hidden="true" />
                    </button>
                  </div>
                </TransitionChild>
                <div class="flex h-full flex-col overflow-y-scroll bg-white py-6 shadow-xl">
                  <div class="px-4 sm:px-6">
                    <DialogTitle class="text-base font-semibold text-gray-900">Recent Board Changes</DialogTitle>
                  </div>

                  <div class="relative mt-6 flex-1 px-4 sm:px-6 space-y-4">
                    <div v-for="change in recentChanges" :key="change.id" class="border-b border-gray-200 pb-2">
                      <div class="flex justify-between text-sm text-gray-600">
                        <span v-if="change.user" class="font-semibold">{{ change.user.name }}</span>
                        <span>{{ change.changed_at }}</span>
                      </div>
                      <p class="text-gray-800 text-sm mt-1">{{ change.action }}</p>
                    </div>
                  </div>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<style scoped>
.scrollbar-hide {
  scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
</style>
