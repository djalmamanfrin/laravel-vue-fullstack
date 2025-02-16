<script setup>
import MyFlyoutMenu from "../atoms/MyFlyoutMenu.vue";
import {
  BellAlertIcon,
  BellIcon, EnvelopeIcon, EnvelopeOpenIcon, NoSymbolIcon,
  PhotoIcon,
  RectangleGroupIcon,
  ViewColumnsIcon
} from "@heroicons/vue/24/outline/index.js";
import {computed} from "vue";
import useBoardStore from "../../store/board.js";


const ICON_MAP = {
  1: RectangleGroupIcon,
  2: PhotoIcon,
  3: ViewColumnsIcon
}

const TITLE_MAP = {
  1: 'Board',
  2: 'Image',
  3: 'Collection'
}

const boardStore = useBoardStore()
const formattedChanges = computed(() => {
  return boardStore.recentChanges.map(change => ({
    id: change.id,
    title: TITLE_MAP[change.change_type_id] || NoSymbolIcon,
    username: change.user.name,
    changed_at: change.changed_at,
    action: change.action,
    icon: ICON_MAP[change.change_type_id] || NoSymbolIcon
  }))
})

const actions = [
  { name: 'Mark all as read', icon: EnvelopeOpenIcon },
  { name: 'Unread notifications', icon: EnvelopeIcon },
]

</script>

<template>
  <MyFlyoutMenu width="w-screen max-w-lg">
    <template #header>
      <BellAlertIcon v-if="boardStore.unreadChangesCount" class="block text-yellow-500 size-6" aria-hidden="true" />
      <BellIcon v-else class="block size-6" aria-hidden="true" />
    </template>
    <template #main>
      <div class="grid grid-cols-2 divide-x divide-gray-900/5 bg-gray-50">
        <a v-for="item in actions" :key="item.name" href="#" class="flex items-center justify-center gap-x-2.5 p-3 font-semibold text-gray-900 hover:bg-gray-100">
          <component :is="item.icon" class="size-5 flex-none text-gray-400" aria-hidden="true" />
          {{ item.name }}
        </a>
      </div>
      <div class="max-h-[500px] overflow-y-auto my-2 px-4">
        <div v-for="item in formattedChanges" :key="item.id" class="group relative flex gap-x-6 rounded-lg px-3 py-2 hover:bg-gray-50">
          <div class="mt-1 flex size-11 flex-none items-center justify-center rounded-lg bg-gray-50 group-hover:bg-white">
            <component :is="item.icon" class="size-6 text-gray-600 group-hover:text-indigo-600" aria-hidden="true" />
          </div>
          <div class="w-full">
                <span class="font-semibold text-gray-900">
                  {{ item.name }}
                  <span class="absolute inset-0" />
                </span>
            <div class="w-full flex justify-between text-sm text-gray-600 pb-1">
              <span class="font-semibold">{{ item.username }}</span>
              <span>{{ item.changed_at }}</span>
            </div>
            <p v-html="item.action" class="mt-1 text-gray-600"></p>
          </div>
        </div>
      </div>
    </template>
  </MyFlyoutMenu>
</template>

<style scoped>

</style>
