import {defineStore} from "pinia";

const useModalStore = defineStore('modal', {
  state: () => ({
    isOpened: false
  }),
  actions: {
    open() {
      this.isOpened = true
    },
    close() {
      this.isOpened = false
    }
  }
})

export default useModalStore
