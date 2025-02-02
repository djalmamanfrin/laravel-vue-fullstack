<script setup>
import {computed, onMounted, watch} from "vue";
import useCategoryStore from "../store/category.js";
import useImageStore from "../store/image.js";

const emit = defineEmits(['selected-image']);

const props = defineProps({
  categoryId: Number,
});
const imageStore = useImageStore();
const selectImage = (image) => {
  imageStore.setSelectedImage(image);
  emit('selected-image')
};

const categoryStore = useCategoryStore()
const images = computed(() => categoryStore.images)
const fetchImages = () => { categoryStore.imagesBy(props.categoryId) }

watch(() => props.categoryId, fetchImages);
onMounted(fetchImages);
</script>

<template>
  <div class="mx-auto grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 lg:mx-0 lg:max-w-none lg:grid-cols-3">
    <article v-for="image in images" :key="image.id" class="flex max-w-xl flex-col items-start justify-between">
      <div class="flex items-center gap-x-4 text-xs">
        <time :datetime="image.created_at" class="text-gray-500">
          {{ image.created_at_formatted }}
        </time>
      </div>
      <p class="mt-5 line-clamp-3 text-sm/6 text-gray-600">{{ image.description }}</p>
      <div class="relative mt-8 flex items-center gap-x-4">
        <img :src="image.url" alt="" @click="selectImage(image)"
             class="rounded-lg cursor-pointer bg-white object-cover group-hover:opacity-75 max-sm:h-16 sm:aspect-[3/2] lg:aspect-[4/3]"/>
      </div>
    </article>
  </div>
</template>

<style scoped>

</style>
