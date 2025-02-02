import {defineStore} from "pinia";
import axiosClient from "../axios.js";

const useCategoryStore = defineStore('category', {
  state: () => ({
    images: [],
    categories: []
  }),
  actions: {
    all() {
      return axiosClient.get(`/api/category`)
        .then(({data}) => {
          this.categories = data;
        })
    },
    get(categoryId) {
      return axiosClient.get(`/api/category/${categoryId}`)
        .then(({data}) => {
          this.categories = data;
        })
    },
    imagesBy(newCategoryId) {
      return axiosClient.get(`/api/categories/${newCategoryId}/images`)
        .then(({data}) => {
          this.images = data
        })
    },
    delete(categoryId) {
      return axiosClient.delete(`/api/category/${categoryId}`)
      .then(response => {
        this.categories = this.categories.filter(category => category.id !== categoryId)
      })
    }
  }
})

export default useCategoryStore
