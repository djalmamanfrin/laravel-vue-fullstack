<script setup>
import {ref, defineProps, defineEmits} from 'vue';

const props = defineProps({
  initialOptions: {
    type: Array,
    required: true,
  },
  selectedOptions: {
    type: Array,
    required: true,
  }
});

const emit = defineEmits(['add-option', 'remove-option']);

const newOption = ref('')
const isDropdownVisible = ref(false)
const filteredOptions = ref(null)

const filterNewOption = (event) => {
  filteredOptions.value = props.initialOptions
  isDropdownVisible.value = true;

  if (event.key === 'Enter') {
    return;
  }

  let value = event.target.value;
  if (value.length >= 2) {
    filteredOptions.value = props.initialOptions.filter(option =>
        option.slug.toLowerCase().includes(value.toLowerCase())
    );
  }
}

const addOption = () => {
  document.querySelector('input[name="multi-select-input"]').blur();
  const option = {
    id: null,
    slug: newOption.value.toLowerCase(),
    name: newOption.value.charAt(0).toUpperCase() + newOption.value.slice(1),
  }
  let index = props.selectedOptions.findIndex(item => item.slug === option.slug)
  if (option && index === -1) {
    emit('add-option', option);
    newOption.value = '';
  }
}

const addSelectedOption = (option) => {
  let index = props.selectedOptions.findIndex(item => item.id === option.id)
  if (index === -1) {
    emit('add-option', option);
  } else {
    emit('remove-option', option);
  }
}
</script>

<template>
  <div>
    <div class="m-2">
      <div class="flex flex-wrap gap-2">
        <span
            v-for="option in props.selectedOptions"
            :key="option.slug"
            class="bg-blue-100 text-blue-600 py-1 px-3 rounded-full flex items-center"
        >
          {{ option.name }}
        </span>
      </div>
    </div>
    <div class="relative flex items-center">
      <input
          v-model="newOption"
          @keyup.enter="addOption"
          @keyup="filterNewOption"
          @focus="filterNewOption"
          type="text"
          name="multi-select-input"
          class="w-full border p-2 rounded-lg pl-4 pr-10"
          placeholder="Digite uma opção e pressione Enter"
      />
      <button v-if="isDropdownVisible" @click="isDropdownVisible = false" class="ml-2 text-sm text-red-500">x</button>
    </div>

    <div class="relative">
      <ul v-if="isDropdownVisible" class="mt-2 w-full bg-white border rounded-lg shadow-lg absolute z-10 max-h-60 overflow-y-auto">
        <template v-if="filteredOptions.length === 0">
          <li class="p-2 text-gray-500">No items found</li>
        </template>

        <template v-else>
          <li
              v-for="option in filteredOptions"
              :key="option.slug"
              class="p-2 cursor-pointer hover:bg-gray-200"
              :class="{'bg-gray-200': selectedOptions.find(item => item.id === option.id)}"
              @click="addSelectedOption(option)"
          >
            {{ option.name }}
          </li>
        </template>
      </ul>
    </div>
  </div>
</template>

<style scoped>
</style>
