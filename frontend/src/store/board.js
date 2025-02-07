import {defineStore} from "pinia";
import axiosClient from "../axios.js";

const useBoardStore = defineStore('board', {
  state: () => ({
    board: {
      collections: []
    },
    boards: [],
    recentChanges: []
  }),
  actions: {
    get(boardId) {
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
    update(fields) {
      let boardId = this.board.id
      return axiosClient.patch(`/api/board/${boardId}`, fields)
        .then(() => {
          this.get(boardId)
        })
    },
    createCollection(fields) {
      let boardId = this.board.id
      return axiosClient.post(`/api/board/${boardId}/collection`, fields)
        .then(() => {
          this.get(boardId)
        })
    },
    updateCollection(collectionId,fields) {
      let boardId = this.board.id
      return axiosClient.patch(`/api/board/${boardId}/collection/${collectionId}`, fields)
        .then(() => {
          this.get(boardId)
        })
    },
    deleteCollection(collectionId) {
      let boardId = this.board.id
      return axiosClient.delete(`/api/board/${boardId}/collection/${collectionId}`)
        .then(() => {
          this.get(boardId)
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
