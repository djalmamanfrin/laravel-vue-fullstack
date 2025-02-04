<script setup>
import {defineProps} from 'vue';

defineProps({
  tabs: {
    type: Array,
    required: true
  },
  activeTab: {
    type: Number,
    required: true
  }
})

const emit = defineEmits(['tab-changed']);

</script>

<template>
  <div class="mx-auto py-6">
    <div class="relative flex items-center gap-4">
      <button
          type="button"
          v-for="tab in tabs"
          :key="tab.id"
          :class="[
        'cursor-pointer px-6 py-3 relative rounded-full hover:bg-gray-100',
        {'text-white bg-gray-900 hover:bg-gray-900': activeTab === tab.id}
      ]"
          @click="emit('tab-changed', tab.id)">
        {{ tab.name }}
        <span v-if="tab.counter" class="ml-2 py-1 px-2 rounded-lg bg-gray-600  text-sm text-white">
          {{ tab.counter }}
        </span>
      </button>
    </div>
  </div>
</template>

<style scoped>
</style>
