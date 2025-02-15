<script setup>
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue'
import { EnvelopeIcon, EnvelopeOpenIcon, RectangleGroupIcon, PhotoIcon, ViewColumnsIcon, NoSymbolIcon } from '@heroicons/vue/24/outline'
import { BellAlertIcon, BellIcon } from '@heroicons/vue/24/outline'
import { computed } from "vue";
import useBoardStore from "../../store/board.js";

const props = defineProps({
  title: {
    type: String,
    required: true
  },
});

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
  <Popover class="relative flex justify-center px-2">
    <PopoverButton class="inline-flex items-center gap-x-1 cursor-pointer focus:ring-0 focus:outline-none">
      <BellAlertIcon v-if="boardStore.unreadChangesCount" class="block text-yellow-500 size-6" aria-hidden="true" />
      <BellIcon v-else class="block size-6" aria-hidden="true" />
    </PopoverButton>

    <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-1" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-1">
      <PopoverPanel class="absolute z-10 mt-15 flex max-w-md -translate-x-1/5 px-4">
      <div class="w-screen max-w-md flex-auto overflow-hidden rounded-3xl bg-white text-sm/6 ring-1 shadow-lg ring-gray-900/5">
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
        </div>
      </PopoverPanel>
    </transition>
  </Popover>
</template>

<style scoped>

</style>
