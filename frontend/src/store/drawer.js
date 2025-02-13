import {defineStore} from "pinia";

const useDrawerStore = defineStore('drawer', {
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

export default useDrawerStore
