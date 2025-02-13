import {defineStore} from "pinia";
import {NotificationTypes} from "../types/notification-types.js";

const useNotificationStore = defineStore('notification', {
  state: () => ({
    isOpened: false,
    type: '',
    title: '',
    message: '',
  }),
  actions: {
    open() {
      if (!Object.values(NotificationTypes).includes(type)) {
        console.error(`Invalid notification type: ${type}`);
        return;
      }
      this.isOpened = true
    },
    close() {
      this.isOpened = false
    }
  }
})

export default useNotificationStore
