<script setup>
import { computed } from 'vue';
import {TooltipPositions} from "../../types/tooltip-position.js";

const props = defineProps({
  text: {
    type: String,
    required: true
  },
  position: {
    type: String,
    default: 'top',
    validator: (value) => Object.values(TooltipPositions).includes(value)
  },
});

const computedClasses = computed(() => {
  const base = `absolute`;

  const positions = {
    top: "bottom-full left-1/2 -translate-x-1/2",
    bottom: "top-full left-1/2 -translate-x-1/2",
    left: "right-full top-1/2 -translate-y-1/2",
    right: "left-full top-1/2 -translate-y-1/2"
  };

  return `${base} ${positions[props.position]}`;
});

const arrowClasses = computed(() => {
  return {
    top: "top-full left-1/2 -translate-x-1/2 -translate-y-1",
    bottom: "bottom-full left-1/2 -translate-x-1/2 translate-y-1",
    left: "left-full top-1/2 -translate-y-1/2 -translate-x-1",
    right: "right-full top-1/2 -translate-y-1/2 translate-x-1"
  }[props.position]
})
</script>

<template>
  <div class="relative group flex justify-center">
    <slot />

    <div
        class="absolute flex-grow-1 text-white text-xs py-1 px-2 rounded shadow-lg bg-gray-700 min-w-[6rem] max-w-[20rem] opacity-0
             group-hover:opacity-100 transition-opacity duration-300 text-center z-50"
        :class="computedClasses"
    >
      {{ text }}
      <div
          class="absolute w-2 h-2 bg-gray-700 rotate-45"
          :class="arrowClasses">
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
