<script setup>
import { ref, onMounted } from 'vue'
import MyDrawer from "./MyDrawer.vue";
import useDrawerStore from "../../store/drawer.js";

const props = defineProps({
  image: {
    type: Object,
    required: true,
  },
})

const drawer = useDrawerStore()

const container = ref(null)
const fontSize = ref('1rem')
const timeSize = ref('0.50rem')

onMounted(() => {
  const resizeObserver = new ResizeObserver(entries => {
    for (const entry of entries) {
      const width = entry.contentRect.width

      fontSize.value = `${Math.max(12, width / 10)}px`
      timeSize.value = `${Math.max(10, width / 15)}px`
    }
  })

  if (container.value) {
    resizeObserver.observe(container.value)
  }
})
</script>

<template>
  <MyDrawer title="Image details">
  </MyDrawer>
  <div @click="drawer.open()" ref="container" class="relative group w-full max-w-[280px] min-w-[160px] aspect-square cursor-pointer">
    <img
        :src="image.path"
        alt=""
        class="w-full h-full rounded-lg cursor-pointer bg-white object-cover group-hover:opacity-75 group-hover:brightness-50"
    />
    <div class="absolute inset-0 flex flex-col justify-center items-center w-full text-center p-2">
      <h3
          class="font-bold text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300"
          :style="{ fontSize }"
      >
        {{ image.title }}
      </h3>
    </div>
    <time
        :datetime="image.created_at"
        class="absolute top-2 right-2 text-white px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300"
        :style="{ fontSize: timeSize }"
    >
      {{ image.created_at_formatted }}
    </time>
  </div>
</template>
