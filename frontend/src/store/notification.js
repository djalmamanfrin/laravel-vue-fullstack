import {defineStore} from "pinia";

const useNotificationStore = defineStore('notification', {
  state: () => ({
    isOpened: false,
    type: '',
    title: '',
    message: '',
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

export default useNotificationStore
