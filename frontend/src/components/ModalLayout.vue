<script setup>
import {ref, defineEmits, computed} from 'vue';
import {Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot} from '@headlessui/vue';
import {ExclamationTriangleIcon} from '@heroicons/vue/24/outline';
import MultiOptionSelector from "./MultiOptionSelector.vue";
import axiosClient from "../axios.js";
import useImageStore from "../store/image.js";
import useCategoryStore from "../store/category.js";

const imageStore = useImageStore()
const categoryStore = useCategoryStore()
const categories = categoryStore.categories

const data = computed(() => ({
  image: null,
  imagePreview: imageStore.selectedImage?.url ?? null,
  categories: imageStore.selectedImage?.categories ?? [],
  description: imageStore.selectedImage?.description ?? '',
}));

const errors = ref({
  image: [],
  categories: [],
  description: [],
})

const emit = defineEmits(['close']);
const open = ref(true);

const handleImageChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    data.value.imagePreview = URL.createObjectURL(file);
    data.value.image = file
  }
};

const handleCloseImage = () => {
  imageStore.setSelectedImage(null);
  emit('close')
};


const handleAddCategoryOption = (option) => {
  let index = categories.findIndex(item => item.id === option.id)
  if (index === -1) {
    categories.push(option);
  }
  data.value.categories.push(option);
};

const handleRemoveCategoryOption = (option) => {
  const dataIndex = data.value.categories.findIndex(item => item.id === option.id)
  if (dataIndex !== -1) {
    data.value.categories.splice(dataIndex, 1);
  }
};

const handleDeleteImage = (image) => {
  imageStore.delete(image.id)
  handleCloseImage()
}

const update = (fields, id) => {
  axiosClient.patch('/api/image/' + id, fields)
    .then(() => {
      handleCloseImage()
    })
    .catch(error => {
      console.log(error.response.data);
      errors.value = error.response.data.errors;
    });
}

const submit = () => {
  const formData = new FormData();
  formData.append('image', data.value.image);

  let fields = {
    description: data.value.description,
    categories: data.value.categories.map(category => category.slug),
  };

  if (!imageStore.selectedImage || data.value.image) {
    axiosClient.post('/api/image/', formData)
      .then(res => {
        fields.path = res.data.path
        update(fields, 0)
      })
      .catch(error => {
        console.log(error.response.data);
        errors.value = error.response.data.errors;
      });
  } else {
    update(fields, imageStore.selectedImage.id)
  }
}
</script>

<template>
  <TransitionRoot :show="open">
    <Dialog class="relative z-10" @close="handleCloseImage">
      <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                       leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 bg-gray-500/75 transition-opacity"/>
      </TransitionChild>
      <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <TransitionChild as="template" enter="ease-out duration-300"
                           enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                           enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                           leave-from="opacity-100 translate-y-0 sm:scale-100"
                           leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <DialogPanel
                class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
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
                            <input type="file" :required="!data.imagePreview" @change="handleImageChange"
                                   class="w-full border p-2 rounded-lg"/>
                          </div>
                          <div class="mb-4">
                            <div v-if="data.imagePreview" class="w-full h-64">
                              <img :src="data.imagePreview" alt="Imagem preview"
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
                                   class="block text-sm font-medium text-gray-700">Category</label>
                            <MultiOptionSelector :initialOptions="categories"
                                                 :selectedOptions="data.categories"
                                                 @add-option="handleAddCategoryOption"
                                                 @remove-option="handleRemoveCategoryOption"
                            />
                            <p class="text-sm mt-1 text-red-600">
                              {{ errors.categories ? errors.categories[0] : '' }}
                            </p>
                          </div>
                          <div class="mb-4">
                            <label for="collectionDescription"
                                   class="block text-sm font-medium text-gray-700">Comment</label>
                            <textarea id="collectionDescription" name="collectionDescription"
                                      v-model="data.description" rows="8" required
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
                    Cancelar
                  </button>
                </div>
              </form>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<style scoped>
</style>
