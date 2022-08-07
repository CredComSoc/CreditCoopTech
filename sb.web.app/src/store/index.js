import { createStore } from 'vuex'
import createPersistedState from 'vuex-persistedstate'
import * as Cookies from 'js-cookie'

export default createStore({
  state: {
    user: {},

    oldNotifications: [],
    newNotifications: [],

    myArticles: [],
    allArticles: [],

    allMembers: [],

    myCart: [],
    myCartSize: 0

  },
  mutations: {

    replaceUser (state, payload) {
      state.user = payload
    },

    replaceOldNotifications (state, payload) {
      state.oldNotifications = payload
    }, 

    replaceNewNotifications (state, payload) {
      state.newNotifications = payload
    },

    replaceMyArticles (state, payload) {
      state.myArticles = payload
    },

    replaceAllArticles (state, payload) {
      state.allArticles = payload
    },

    replaceAllMembers (state, payload) {
      state.allMembers = payload
    },

    replaceMyCart (state, payload) {
      state.myCart = payload
    },

    replaceMyCartSize (state, payload) {
      state.myCartSize = payload
    }

  },
  actions: {
  },
  modules: {
  },
  plugins: [
    createPersistedState({
      getState: (key) => Cookies.getJSON(key),
      setState: (key, state) => Cookies.set(key, state, { expires: 3, secure: false, sameSite: 'Lax' })
    })
  ]
})
