import {defineStore} from "pinia";
import axiosClient from "../axios.js";
import router from "../router.js";

const useUserStore = defineStore('user', {
  state: () => ({
    user: null
  }),
  actions: {
    fetchUser() {
      return axiosClient.get('/api/user')
        .then(({data}) => {
          this.user = data
        })
    },
    logout() {
      return axiosClient.post('/logout')
        .then(() => {
          this.user = null
          router.push({name: 'Login'})
        })
    }
  }
})

export default useUserStore
