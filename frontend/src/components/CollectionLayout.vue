<script setup>
import {onBeforeMount, ref} from 'vue';
import ModalLayout from "./ModalLayout.vue";
import TabLayout from "./TabLayout.vue";
import ImageLayout from "./ImageLayout.vue";
import useCategoryStore from "../store/category.js";

defineProps({
  title: {
    type: String,
    required: true,
  }
});

const categoryStore = useCategoryStore();
const tabs = ref([]);
const activeTabContent = ref(0);
const isModalOpen = ref(false);

const handleTabChanged = (tabId) => {
  activeTabContent.value = tabId;
}

const handleSelectedImage = () => {
  isModalOpen.value = true
}

const fetchTabs = async () => {
  await categoryStore.all();
  tabs.value = categoryStore.categories;
  activeTabContent.value = categoryStore.categories[0]?.id || 0;
}

onBeforeMount(() => {
  fetchTabs();
})
</script>

<template>
  <div class="bg-gray-100">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-2xl py-1 sm:py-8 lg:max-w-none lg:py-8">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-bold text-gray-900">{{ title }}</h2>
          <button @click="isModalOpen = true" class="p-2 text-gray-900 cursor-pointer hover:text-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
          </button>
        </div>
        <TabLayout
            v-if="tabs.length"
            :tabs="tabs"
            :activeTab="activeTabContent"
            @tab-changed="handleTabChanged"
        />

        <ImageLayout
            v-if="tabs.length"
            :category-id="activeTabContent"
            @selected-image="handleSelectedImage"
        />
      </div>
    </div>
  </div>
  <ModalLayout v-if="isModalOpen" @close="isModalOpen = false"/>
</template>
