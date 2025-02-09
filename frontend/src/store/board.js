import {defineStore} from "pinia";
import axiosClient from "../axios.js";

const useBoardStore = defineStore('board', {
  state: () => ({
    board: {
      collections: []
    },
    boards: [],
    recentChanges: [],
    unreadChangesCount: null
  }),
  actions: {
    get(boardId) {
      axiosClient.get(`/api/board/${boardId}`)
        .then(({data}) => {
          this.board = data
        })
    },
    all() {
      return axiosClient.get(`/api/board/`)
        .then(({data}) => {
          this.boards = data
        })
    },
    create(fields = {}) {
      return axiosClient.post(`/api/board/`, fields)
        .then(({data}) => {
          this.board = data
          return data;
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
        .then(({ data }) => {
          this.recentChanges = data;
          this.unreadChangesCount = this.recentChanges.filter(change => change.read_at === null).length;
        });
    }

  }
})

export default useBoardStore
