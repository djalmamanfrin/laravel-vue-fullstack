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
        .then(({data}) => {
          this.get(boardId)
        })
    },
    fetchImages() {
      axiosClient.get(`/api/board/${boardId}/images`)
        .then(({data}) => {
          this.boards = data
        })
    },
    createCollection(fields) {
      let boardId = this.board.id
      return axiosClient.post(`/api/board/${boardId}/collection`, fields)
        .then(({data}) => {
          this.board = data
        })
    },
    updateCollection(collectionId,fields) {
      let boardId = this.board.id
      return axiosClient.patch(`/api/board/${boardId}/collection/${collectionId}`, fields)
        .then(({data}) => {
          this.board = data
        })
    },
    deleteCollection(collectionId) {
      let boardId = this.board.id
      return axiosClient.delete(`/api/board/${boardId}/collection/${collectionId}`)
        .then(({data}) => {
          this.board = data
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
