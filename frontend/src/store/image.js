import {defineStore} from "pinia";
import axiosClient from "../axios.js";

const useImageStore = defineStore('image', {
  state: () => ({
    images: [],
    selectedImage: null
  }),
  actions: {
    setSelectedImage(image) {
      this.selectedImage = image
    },
    all() {
      return axiosClient.get(`/api/image/`)
        .then(({data}) => {
          console.log(data)
          this.images = data
        })
    },
    get(imageId) {
      return axiosClient.get(`/api/image/${imageId}`)
        .then(({data}) => {
          this.selectedImage = data
        })
    },
    upload(formData) {
      return axiosClient.post('/api/image/', formData)
        .then(({data}) => {
          this.selectedImage = data
        })
    },
    update(imageId, fields) {
      return axiosClient.patch('/api/image/' + imageId, fields)
        .then(({data}) => {
          this.images = this.images.map(item =>
            item.id === data.id ? { ...item, ...data } : item
          )
          this.selectedImage = data
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
