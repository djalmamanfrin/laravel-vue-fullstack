<script setup>
import { ref } from 'vue'
import {
  Dialog, DialogPanel, DialogTitle,
  Disclosure,
  DisclosureButton,
  DisclosurePanel,
  Menu,
  MenuButton,
  MenuItem,
  MenuItems, TransitionChild, TransitionRoot
} from '@headlessui/vue'
import { Bars3Icon, BellIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import useUserStore from "../store/user.js";
import {computed} from "vue";
import {ExclamationTriangleIcon, PlusIcon} from "@heroicons/vue/24/outline/index.js";
import MyButton from "./atoms/MyButton.vue";
import MyModal from "./atoms/MyModal.vue";
import useImageStore from "../store/image.js";
import useModalStore from "../store/modal.js";

const modal = useModalStore();

const imageStore = useImageStore()
const userStore = useUserStore()
const user = computed(() => userStore.user)

const image = ref(null)
const imagePreview = ref(imageStore.selectedImage?.path ?? null)
const title = ref(imageStore.selectedImage?.title ?? '')
const description = ref(imageStore.selectedImage?.description ?? '')

const errors = ref({
  image: [],
  title: [],
  description: [],
})

const handleCloseImage = () => {
  imageStore.setSelectedImage(null);
  modal.close()
};

const handleDeleteImage = (image) => {
  imageStore.delete(image.id)
  handleCloseImage()
}

const handleImageChange = (event) => {
  let file = event.target.files[0];
  if (file) {
    imagePreview.value = URL.createObjectURL(file)
    image.value = file
  }
}

const update = (fields, id) => {
  imageStore.update(id, fields)
    .then(() => {
      handleCloseImage()
    })
    .catch(error => {
      console.log(error.response.data);
      errors.value = error.response.data.errors;
    })
}
const submit = () => {
  const formData = new FormData();
  formData.append('image', image.value);

  let fields = {
    description: description.value,
    title: title.value,
  };

  if (!imageStore.selectedImage || image.value) {
    imageStore.upload(formData)
      .then(() => {
        fields.path = imageStore.selectedImage.path
        update(fields, 0)
      })
      .catch(error => {
        errors.value = error.data.errors;
      })
  } else {
    update(fields, imageStore.selectedImage.id)
  }
}
</script>

<template>
  <div class="min-h-full">
    <MyModal size="max-w-3xl" :is-opened="modal.isOpened" @close-modal="modal.close()">
      <form @submit.prevent="submit">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-center sm:space-x-4">
            <div
                class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:size-10">
              <ExclamationTriangleIcon class="size-6 text-blue-600" aria-hidden="true"/>
            </div>
            <DialogTitle as="h3" class="sm:ml-2 text-base font-semibold text-gray-900">Upload your Image
            </DialogTitle>
          </div>
          <div class="sm:flex sm:items-start">
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
              <div class="mt-4 sm:flex">
                <div class="sm:w-1/3">
                  <div class="mb-4">
                    <input type="file" name="input-file" :required="!imagePreview" @change="handleImageChange"
                           class="w-full border p-2 rounded-lg"/>
                  </div>
                  <div class="mb-4">
                    <div v-if="imagePreview" class="w-full h-64">
                      <img :src="imagePreview" alt="Imagem preview"
                           class="w-full h-full object-cover rounded-lg"/>
                    </div>
                    <div v-else class="w-full h-64 bg-gray-200 rounded-lg"></div>
                    <p class="text-sm mt-1 text-red-600">
                      {{ errors.image ? errors.image[0] : '' }}
                    </p>
                  </div>
                </div>
                <div class="sm:w-2/3 sm:ml-6">
                  <div class="mb-4">
                    <label for="collectionDescription"
                           class="block text-sm font-medium text-gray-700">Title</label>
                    <input
                        v-model="title"
                        type="text"
                        name="input-title"
                        class="w-full border p-2 rounded-lg pl-4 pr-10"
                        placeholder="Enter image title"
                    />
                    <p class="text-sm mt-1 text-red-600">
                      {{ errors.title ? errors.title[0] : '' }}
                    </p>
                  </div>
                  <div class="mb-4">
                    <label for="collectionDescription"
                           class="block text-sm font-medium text-gray-700">Comment</label>
                    <textarea id="collectionDescription" name="collectionDescription"
                              v-model="description" rows="8" required
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                    <p class="text-sm mt-1 text-red-600">
                      {{ errors.description ? errors.description[0] : '' }}
                    </p>
                    <small> Add #hashtags to help the community to find your post</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
          <button type="submit"
                  class="inline-flex w-full justify-center cursor-pointer rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-blue-500 sm:ml-3 sm:w-auto">
            {{imageStore.selectedImage ? 'Update' : 'Upload'}}
          </button>

          <button v-if="imageStore.selectedImage" type="button"
                  @click="handleDeleteImage(imageStore.selectedImage)"
                  class="inline-flex w-full justify-center cursor-pointer rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">
            Delete
          </button>

          <button type="button"
                  @click="handleCloseImage"
                  class="mt-3 inline-flex w-full justify-center cursor-pointer rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 shadow-xs ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto">
            Back
          </button>
        </div>
      </form>
    </MyModal>
    <Disclosure as="nav" class="bg-gray-800" v-slot="{ open }">
      <div class="mx-auto max-w-7xl">
        <div class="flex h-16 items-center justify-between">
          <div class="flex items-center">
          </div>
          <div class="hidden md:block">
            <div class="ml-4 flex items-center md:ml-6 gap-8">
              <div class="flex items-center">

                <!-- Profile dropdown -->
                <Menu as="div" class="relative ml-3">
                  <div>
                    <MenuButton class="relative flex cursor-pointer max-w-xs items-center rounded-full bg-gray-800 text-sm focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden">
                      <span class="absolute -inset-1.5" />
                      <span class="sr-only">Open user menu</span>
                      <img class="size-8 rounded-full"
                           src="https://randomuser.me/api/portraits/men/81.jpg" alt=""/>
                      <span class="text-white ml-3">{{ user.name }}</span>
                    </MenuButton>
                  </div>
                  <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                    <MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 ring-1 shadow-lg ring-black/5 focus:outline-hidden">
                      <MenuItem>
                        <button @click="userStore.logout()" :class="['block px-4 py-2 text-sm text-gray-700']">Sign out</button>
                      </MenuItem>
                    </MenuItems>
                  </transition>
                </Menu>
              </div>
              <MyButton @click="modal.open()" name="upload images" :left-icon="PlusIcon" />
            </div>
          </div>
          <div class="flex md:hidden mr-5">
            <!-- Mobile menu button -->
            <DisclosureButton class="relative inline-flex items-center justify-center rounded-md bg-gray-800 p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden">
              <span class="absolute -inset-0.5" />
              <span class="sr-only">Open main menu</span>
              <Bars3Icon v-if="!open" class="block size-6" aria-hidden="true" />
              <XMarkIcon v-else class="block size-6" aria-hidden="true" />
            </DisclosureButton>
          </div>
        </div>
      </div>

      <DisclosurePanel class="md:hidden">
        <div class="border-t border-gray-700 pt-4 pb-3">
          <div class="flex items-center px-5">
            <div class="shrink-0">
              <img class="size-8 rounded-full"
                   src="https://randomuser.me/api/portraits/men/81.jpg" alt=""/>
            </div>
            <div class="ml-3">
              <div class="text-base/5 font-medium text-white">{{ user.name }}</div>
              <div class="text-sm font-medium text-gray-400">{{ user.email }}</div>
            </div>
          </div>
          <div class="mt-3 space-y-1 px-2">
            <DisclosureButton @click="userStore.logout()" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white">
              Sign out
            </DisclosureButton>
          </div>
        </div>
      </DisclosurePanel>
    </Disclosure>
    <RouterView/>
  </div>
</template>

<style scoped>

</style>
