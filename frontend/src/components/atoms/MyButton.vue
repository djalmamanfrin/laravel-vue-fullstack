<script setup>
defineProps({
  type: {
    type: String,
    default: 'button',
  },
  name: {
    type: String,
    required: true,
  },
  title: {
    type: String,
    required: false,
  },
  leftIcon: {
    type: Function,
    required: false,
  },
  rightIcon: {
    type: Function,
    required: false,
  },
  isDisabled: {
    type: Boolean,
    default: false,
  },
  color: {
    type: String,
    default: 'indigo',
  },
})
</script>

<template>
  <button :type="type" :title="title" :name="name" :disabled="isDisabled"
          :class="`bg-${color}-600 hover:bg-${color}-500 focus-visible:outline-${color}-600`"
          class="flex w-full justify-center cursor-pointer rounded-md bg-indigo-600 px-3 py-2 text-sm/6 font-semibold text-white shadow-xs focus-visible:outline-2 focus-visible:outline-offset-2"
  >
    <component :is="leftIcon" class="block size-5" aria-hidden="true" />
    {{ name.charAt(0).toUpperCase() + name.slice(1) }}
    <component :is="rightIcon" class="block size-5" aria-hidden="true" />
  </button>
</template>

<style scoped>

</style>
