<script setup>
defineProps({
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
  }
})
</script>

<template>
  <button type="button" :title="title" :name="name"
          class="inline-flex items-center gap-2 cursor-pointer rounded-md bg-indigo-600 px-8 py-3 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
    <component :is="leftIcon" class="block size-5" aria-hidden="true" />
    {{ name.charAt(0).toUpperCase() + name.slice(1) }}
    <component :is="rightIcon" class="block size-5" aria-hidden="true" />
  </button>
</template>

<style scoped>

</style>
