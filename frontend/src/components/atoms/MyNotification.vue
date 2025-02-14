<script setup>
import { computed, onMounted } from 'vue';
import CheckCircleIcon from "@heroicons/vue/24/outline/CheckCircleIcon";
import XMarkIcon from "@heroicons/vue/24/outline/XMarkIcon";
import ExclamationTriangleIcon from "@heroicons/vue/24/outline/ExclamationTriangleIcon";
import InformationCircleIcon from "@heroicons/vue/24/outline/InformationCircleIcon";
import {NotificationTypes} from "../../types/notification-types.js";

const props = defineProps({
  title: String,
  message: String,
  type: {
    type: String,
    default: 'info',
    validator: (value) => Object.values(NotificationTypes).includes(value)
  }
});

const emit = defineEmits(['close']);

onMounted(() => {
  setTimeout(() => {
    emit('close');
  }, 5000);
});

const IconColorClass = computed(() => {
  return {
    'text-green-700': props.type === 'success',
    'text-red-700': props.type === 'error',
    'text-yellow-700': props.type === 'warning',
    'text-blue-700': props.type === 'info',
  };
});

const iconComponent = computed(() => {
  return props.type === 'success' ? CheckCircleIcon :
      props.type === 'error' ? XMarkIcon :
          props.type === 'warning' ? ExclamationTriangleIcon :
              InformationCircleIcon;
});
</script>

<template>
  <div
      class="fixed bottom-4 right-4 p-4 rounded-lg shadow-xl border border-gray-200 flex items-center space-x-4 max-w-sm bg-white"
  >
    <div>
      <component :is="iconComponent" class="block size-8" :class="IconColorClass" aria-hidden="true" />
    </div>
    <div class="pr-5">
      <p class="font-semibold text-gray-700">{{ title }}</p>
      <p class="text-sm text-gray-500">{{ message }}</p>
    </div>
    <button @click="$emit('close')" class="absolute cursor-pointer top-2 right-2 text-gray-500 hover:text-gray-700">
      <XMarkIcon class="block size-5" aria-hidden="true"/>
    </button>
  </div>
</template>

<style scoped>
.fixed {
  z-index: 50;
}
</style>
