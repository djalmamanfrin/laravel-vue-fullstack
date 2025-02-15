<script setup>
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue'
const props = defineProps({
  width: {
    type: String,
    default: 'max-w-md',
  },
  position: {
    type: String,
    default: 'right',
    validator: (value) => ['left', 'right'].includes(value)
  }
})
</script>

<template>
  <Popover class="relative flex justify-center px-2">
    <PopoverButton class="inline-flex items-center gap-x-1 cursor-pointer focus:ring-0 focus:outline-none">
      <slot name="header"></slot>
    </PopoverButton>

    <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-1" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-1">
      <PopoverPanel :class="[
        'absolute z-10 mt-10 px-4',
        position === 'left' ? '-translate-x-[10px] left-0' : 'right-0 translate-x-[10px]'
      ]">
        <div :class="`${width} flex-auto overflow-hidden rounded-lg bg-white text-sm/6 ring-1 shadow-lg ring-gray-900/5`">
          <slot name="main"></slot>
        </div>
      </PopoverPanel>
    </transition>
  </Popover>
</template>

<style scoped>

</style>
