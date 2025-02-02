import {defineStore} from "pinia";
import axiosClient from "../axios.js";

const useImageStore = defineStore('image', {
  state: () => ({
    images: [],
    selectedImage: null
  }),
  actions: {
    setSelectedImage(image) {
      this.selectedImage = image;
    },
    get(imageId) {
      return axiosClient.get(`/api/image/${imageId}`)
        .then(({data}) => {
          this.selectedImage = data;
        })
    },
    delete(imageId) {
      return axiosClient.delete(`/api/image/${imageId}`)
      .then(response => {
        this.images = this.images.filter(image => image.id !== imageId)
        this.selectedImage = null
      })
    }
  }
})

export default useImageStore
