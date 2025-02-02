<script setup>
import {defineProps} from 'vue';

defineProps({
  tabs: Array,
  activeTab: Number,
});

const emit = defineEmits(['tab-changed']);

</script>

<template>
  <div class="mx-auto py-6">
    <div class="border-b border-gray-300 relative">
      <button
          type="button"
          v-for="tab in tabs"
          :key="tab.id"
          :class="[
        'tab cursor-pointer px-4 py-2 relative rounded-t-sm',
        {'border-t border-x border-gray-300': activeTab === tab.id}
      ]"
          @click="emit('tab-changed', tab.id)">
        {{ tab.name }}
        <span v-if="tab.images_count" class="ml-2 py-1 px-2 rounded-lg bg-gray-600  text-sm text-white">
          {{ tab.images_count }}
        </span>
        <span v-if="activeTab === tab.id" class="absolute bottom-[-2px] left-0 w-full border-b-2 border-gray-100"></span>
      </button>
    </div>
  </div>
</template>

<style scoped>
</style>
