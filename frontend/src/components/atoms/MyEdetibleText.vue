<script setup>
import { PencilIcon } from "@heroicons/vue/24/outline/index.js";
import { ref, watch } from "vue";

const props = defineProps({
  text: {
    type: String,
    required: true,
  },
});

const emit = defineEmits(["text-changed"]);

const editableName = ref(props.text);
const editing = ref(false);

let debounceTimer = null;
const saveName = () => {
  if (!editing.value) return;
  editing.value = false;

  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    emit("text-changed", editableName.value);
  }, 1000);
};

watch(() => props.text, (newValue) => {
  editableName.value = newValue;
});
</script>

<template>
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
      {{ text }}
    </h2>
    <PencilIcon
        class="size-5 text-gray-500 cursor-pointer ml-2 opacity-70 group-hover:opacity-100 transition-opacity"
        @click="editing = true"
    />
  </div>
</template>


<style scoped>

</style>
