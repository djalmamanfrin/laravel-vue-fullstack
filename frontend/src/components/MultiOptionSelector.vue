<script setup>
import {ref, defineProps, defineEmits, watch} from 'vue';

const props = defineProps({
  initialOptions: {
    type: Array,
    required: true,
  }
});

const emit = defineEmits(['add-option', 'remove-option']);

const newOption = ref('');
const isDropdownVisible = ref(false);
const filteredOptions = ref(props.initialOptions);
const selectedOptions = ref([]);

const filterNewOption = (event) => {
  if (event.key === 'Enter') {
    filteredOptions.value = props.initialOptions
    return;
  }

  let value = event.target.value;
  if (value.length >= 2) {
    filteredOptions.value = props.initialOptions.filter(option =>
        option.toLowerCase().includes(value.toLowerCase())
    );
    isDropdownVisible.value = true;
  }
}

const addOption = () => {
  document.querySelector('input[name="multi-select-input"]').blur();
  const option = newOption.value.trim();
  if (option && !selectedOptions.value.includes(option)) {
    emit('add-option', option);
    selectedOptions.value.push(option);
    newOption.value = '';
  }
}

const addSelectedOption = (option) => {
  const index = selectedOptions.value.indexOf(option);
  if (index === -1) {
    selectedOptions.value.push(option);
  } else {
    selectedOptions.value = selectedOptions.value.filter((item) => item !== option);
  }
}

const removeOption = (option) => {
  emit('remove-option', option);
}
</script>

<template>
  <div>
    <div class="m-2">
      <div class="flex flex-wrap gap-2">
        <span
            v-for="(option, index) in selectedOptions"
            :key="index"
            class="bg-blue-100 text-blue-600 py-1 px-3 rounded-full flex items-center"
        >
          {{ option }}
        </span>
      </div>
    </div>
    <div class="relative flex items-center">
      <input
          v-model="newOption"
          @keyup.enter="addOption"
          @keyup="filterNewOption"
          @focus="isDropdownVisible = true"
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
              v-for="(option, index) in filteredOptions"
              :key="index"
              class="p-2 cursor-pointer hover:bg-gray-200"
              :class="{'bg-gray-200': selectedOptions.includes(option)}"
              @click="addSelectedOption(option)"
          >
            {{ option }}
          </li>
        </template>
      </ul>
    </div>
  </div>
</template>

<style scoped>
</style>
