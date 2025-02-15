<script setup>
import {ref, onMounted} from 'vue'
import MyDrawer from "./MyDrawer.vue";
import useDrawerStore from "../../store/drawer.js";
import {Disclosure} from "@headlessui/vue";
import MyEditableText from "./MyEditableText.vue";
import {TrashIcon, ArchiveBoxIcon, EllipsisVerticalIcon} from "@heroicons/vue/24/outline";
import MyFlyoutMenu from "./MyFlyoutMenu.vue";

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

const actions = [
  {
    id: 1,
    text: 'Deletar',
    icon: TrashIcon
  },
  {
    id: 2,
    text: 'Arquivar',
    icon: ArchiveBoxIcon
  }
]
</script>

<template>
  <MyDrawer title="Image details">
    <template #header>
      <Disclosure as="nav" class="bg-gray-800">
        <div class="mx-auto max-w-7xl">
          <div class="flex h-16 items-center justify-between">
            <MyEditableText style-text="block w-full text-center text-lg font-medium tracking-tight text-white"
                            html-tag="h2" :text="image.title"/>
            <div class="px-4 cursor-pointer">
              <MyFlyoutMenu>
                <template #header>
                  <EllipsisVerticalIcon class="block size-6 text-white" aria-hidden="true"/>
                </template>
                <template #main>
                  <div class="max-h-[500px] overflow-y-auto my-2 px-3">
                    <div v-for="item in actions" :key="item.id" class="group relative flex items-center gap-x-3 rounded-md px-4 py-2 hover:bg-gray-100">
                      <component :is="item.icon" class="size-5 text-gray-600 group-hover:text-indigo-600" aria-hidden="true" />
                      <span class="text-gray-600 font-semibold">
                        {{ item.text }}
                        <span class="absolute inset-0" />
                      </span>
                    </div>
                  </div>
                </template>
              </MyFlyoutMenu>
            </div>
          </div>
        </div>
      </Disclosure>
    </template>
    <template #main>
    </template>
  </MyDrawer>
  <div @click="drawer.open()" ref="container"
       class="relative group w-full max-w-[280px] min-w-[160px] aspect-square cursor-pointer">
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
