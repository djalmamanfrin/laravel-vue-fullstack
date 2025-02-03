import {defineStore} from "pinia";
import axiosClient from "../axios.js";

const useBoardStore = defineStore('board', {
  state: () => ({
    board: {},
    boards: [],
    recentChanges: []
  }),
  actions: {
    setSelectedBoard(boardId) {
      axiosClient.get(`/api/board/${boardId}`)
        .then(({data}) => {
          this.board = data
        })
    },
    fetchBoards() {
      return axiosClient.get(`/api/board/`)
        .then(({data}) => {
          this.boards = data
        })
    },
    fetchRecentChanges(boardId) {
      return axiosClient.get(`/api/board/${boardId}/recent-changes`)
        .then(({data}) => {
          this.recentChanges = data
        })
    }
  }
})

export default useBoardStore
