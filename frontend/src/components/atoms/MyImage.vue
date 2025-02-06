<script setup>
defineProps({
  image: {
    type: Object,
    required: true,
  },
})
</script>

<template>
  <div class="relative p-4 flex items-center gap-x-4 group w-full">
    <img
        :src="image.path"
        alt=""
        class="w-full h-auto rounded-lg cursor-pointer bg-white object-cover group-hover:opacity-75 aspect-[4/3] sm:aspect-[3/2] md:aspect-[16/9] lg:aspect-[4/3] group-hover:brightness-50"
    />

    <div
        class="absolute inset-0 flex flex-col justify-center items-center group-hover:flex group-hover:block w-full text-center break-words p-4"
    >
      <h3
          class="text-2xl p-1 sm:text-sm md:text-base md:mt-2 font-bold text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300"
      >
        {{ image.title }}
      </h3>
    </div>

    <time
        :datetime="image.created_at"
        class="absolute text-sm md:text-xs text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300"
    >
      {{ image.created_at_formatted }}
    </time>
  </div>
</template>
