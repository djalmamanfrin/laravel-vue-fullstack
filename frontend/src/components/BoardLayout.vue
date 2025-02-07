<script setup>
defineProps({
  boards: {
    type: Array,
    required: true,
  },
})
</script>

<template>
  <div class="mx-auto grid max-w-7xl grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
    <article v-for="board in boards" :key="board.id" class="flex flex-col items-start justify-between">
      <RouterLink
          :to="{ name: 'Board', params: { id: board.id } }"
          class="relative mt-1 flex items-center gap-x-4 group w-full"
      >
        <div class="grid grid-cols-2 grid-rows-2 w-full group-hover:opacity-75 group-hover:brightness-50">
          <img
              v-if="board.images?.length >= 3"
              :src="board.images[0].path"
              alt=""
              class="col-span-2 row-span-1 rounded-t-lg object-cover w-full h-20 sm:h-28 lg:h-36"
          />
          <img
              v-if="board.images?.length >= 3"
              :src="board.images[1].path"
              alt=""
              class="col-span-1 row-span-1 rounded-bl-lg object-cover w-full h-20 sm:h-28 lg:h-36"
          />
          <img
              v-if="board.images?.length >= 3"
              :src="board.images[2].path"
              alt=""
              class="col-span-1 row-span-1 rounded-br-lg object-cover w-full h-20 sm:h-28 lg:h-36"
          />
        </div>

        <div class="absolute inset-0 flex flex-col justify-center items-center group-hover:flex w-full text-center break-words p-4">
          <h3 class="text-2xl font-bold text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            {{ board.name }}
          </h3>
        </div>

        <time
            :datetime="board.created_at"
            class="absolute top-0 right-0 mt-2 mr-3 text-sm text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300"
        >
          {{ board.created_at_formatted }}
        </time>
      </RouterLink>
    </article>
  </div>
</template>
