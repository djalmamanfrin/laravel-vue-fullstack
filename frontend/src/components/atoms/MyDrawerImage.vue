<script setup>
import {Disclosure} from "@headlessui/vue";
import {ArchiveBoxIcon, EllipsisVerticalIcon, PencilIcon, TrashIcon} from "@heroicons/vue/24/outline/index.js";
import MyEditableText from "./MyEditableText.vue";
import MyButton from "./MyButton.vue";
import MyDrawer from "./MyDrawer.vue";
import MyFlyoutMenu from "./MyFlyoutMenu.vue";

const props = defineProps({
  image: {
    type: Object,
    required: true,
  },
})

const actions = [
  {
    id: 1,
    text: 'Delete',
    icon: TrashIcon
  },
  {
    id: 2,
    text: 'Archive',
    icon: ArchiveBoxIcon
  }
]
</script>

<template>
  <MyDrawer>
    <template #header>
      <Disclosure as="nav" class="bg-white border-b border-gray-200">
        <div class="mx-auto max-w-7xl">
          <div class="flex h-24 items-center justify-between cursor-pointer">
            <div class="flex justify-start items-center w-full pr-3 pl-6">
              <MyEditableText style-text="block text-2xl font-medium tracking-tight"
                              html-tag="h2" :text="image.title">
                <PencilIcon
                    class="size-5 text-gray-500 cursor-pointer ml-2 opacity-0 group-hover:opacity-100 transition-opacity"
                />
              </MyEditableText>
            </div>
            <div class="flex justify-center w-1/2">
              <MyButton name="Save Changes" />
              <MyFlyoutMenu class="px-4 cursor-pointer">
                <template #header>
                  <EllipsisVerticalIcon class="block size-6" aria-hidden="true"/>
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
      <div class="col-span-full">
        <img
            :src="image.path"
            alt=""
            class="w-full max-h-[300px] rounded-lg"
        />
      </div>
      <div class="col-span-full">
        <label for="about" class="block text-sm/6 font-medium text-gray-900">Description</label>
        <div class="mt-2">
          <textarea v-model="image.description" name="about" id="about" rows="6" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
        </div>
        <small class="mt-3 text-gray-600">Add, remove or update the #hashtags if necessary</small>
      </div>
    </template>
  </MyDrawer>
</template>

<style scoped>

</style>
