<script setup>
import {onMounted, ref, watch} from "vue";
import axiosClient from "../axios.js";

const props = defineProps({
  categoryId: Number,
});

const images = ref([])
const fetchImages = async (newCategoryId) => {
    await axiosClient.get(`/api/categories/${newCategoryId}/images`)
        .then(response => {
          images.value = response.data;
        })
        .catch(error => {
          // console.log(error.response.data)
          // errors.value = error.response.data.errors;
          console.log('Error: ' + error)
        })
};

watch(() => props.categoryId, (newCategoryId) => {
  fetchImages(newCategoryId);
});

onMounted(() => {
  fetchImages(props.categoryId);
})
</script>

<template>
  <div class="mx-auto grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 lg:mx-0 lg:max-w-none lg:grid-cols-3">
    <article v-for="image in images" :key="image.id" class="flex max-w-xl flex-col items-start justify-between">
      <div class="flex items-center gap-x-4 text-xs">
        <time :datetime="image.created_at" class="text-gray-500">
          {{ image.created_at_formatted }}
        </time>
      </div>
      <div class="group relative">
<!--        <h3 class="mt-3 text-lg/6 font-semibold text-gray-900 group-hover:text-gray-600">-->
<!--          <a :href="image.href">-->
<!--            <span class="absolute inset-0" />-->
<!--            {{ image.title }}-->
<!--          </a>-->
<!--        </h3>-->
        <p class="mt-5 line-clamp-3 text-sm/6 text-gray-600">{{ image.description }}</p>
      </div>
      <div class="relative mt-8 flex items-center gap-x-4">
        <img :src="image.url" alt=""
             class="rounded-lg bg-white object-cover group-hover:opacity-75 max-sm:h-16 sm:aspect-[3/2] lg:aspect-[4/3]"/>
      </div>
    </article>
  </div>
</template>

<style scoped>

</style>
