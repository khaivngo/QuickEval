import Vue from 'vue'

export const store = Vue.observable({
  updated: false
})

export const mutations = {
  setUpdated (update) {
    store.updated = update
  }
}

// export const store = {
//   debug: true,
//   state: {
//     user: {
//       id: 0,
//       role: 0
//     }
//   },
//   setUser (newValue) {
//     if (this.debug) console.log('setMessageAction triggered with', newValue)
//     this.state.user = newValue
//   },
//   clearUser () {
//     if (this.debug) console.log('clearMessageAction triggered')
//     this.state.user = {
//       id: 0,
//       role: 0
//     }
//   }
// }
