<script setup>
defineProps({
  images: {
    type: Array,
    required: true,
  },
})
</script>

<template>
  <div class="mx-auto grid max-w-7xl grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
    <article v-for="image in images" :key="image.id" class="flex flex-col items-start justify-between">
      <div class="relative mt-1 flex items-center gap-x-4 group w-full">
        <img
            :src="image.path"
            alt=""
            class="rounded-lg cursor-pointer bg-white object-cover w-full group-hover:opacity-75 max-sm:h-16 sm:aspect-[3/2] lg:aspect-[4/3] group-hover:brightness-50"
        />

        <div class="absolute inset-0 flex flex-col justify-center items-center group-hover:flex group-hover:block w-full text-center break-words p-4">
          <h3 class="text-2xl font-bold text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            {{ image.title }}
          </h3>
        </div>

        <time
            :datetime="image.created_at"
            class="absolute top-0 right-0 mt-2 mr-3 text-sm text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300"
        >
          {{ image.created_at_formatted }}
        </time>
      </div>
    </article>
  </div>
</template>

<style scoped>

</style>
