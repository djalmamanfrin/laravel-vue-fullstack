<script setup>
import {ref, watch, nextTick} from "vue";

const props = defineProps({
  text: {
    type: String,
    required: true,
  },
  htmlTag: {
    type: String,
    required: true,
  },
  styleText: {
    type: String,
    required: true,
  }
});

const emit = defineEmits(["text-changed"]);

const editableName = ref(props.text);
const editing = ref(false);
const inputRef = ref(null);
const startEditing = () => {
  editing.value = true;
  nextTick(() => {
    if (inputRef.value) {
      inputRef.value.focus();
      inputRef.value.setSelectionRange(0, editableName.value.length);
    }
  });
};

let debounceTimer = null;

const saveName = () => {
  if (!editing.value) return;

  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    emit("text-changed", editableName.value);
  }, 500);

  editing.value = false;
};

watch(() => props.text, (newValue) => {
  editableName.value = newValue;
});
</script>

<template>
    <input
        v-if="editing"
        v-model="editableName"
        @blur="saveName"
        @keyup.enter="saveName"
        :class="[styleText, 'w-full border border-gray-300 rounded px-2 focus:outline-none focus:ring-2 focus:ring-indigo-500']"
        autofocus
    />
    <component v-else :is="props.htmlTag" @click="startEditing" :class="[styleText, 'relative cursor-pointer']">
      {{ text }}
    </component>
</template>

<style scoped>

</style>
