import Vuex from 'vuex'
import createPersistedState from 'vuex-persistedstate'

const store = new Vuex.Store ({
  state: {
    user: {},

    oldNotifications: [],
    newNotifications: [],

    myArticles: [],
    allArticles: [],

    allMembers: [],

    myCart: [],
    myCartSize: 0,

    saldo: 0,

    requests: [],
    pendingPurchases: [],
    completedPurchases: [],

    activeArticles: [],
    inactiveArticles: [],

    allEvents: []

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
    },

    replaceSaldo (state, payload) {
      state.saldo = payload
    },

    replaceRequests (state, payload) {
      state.requests = payload
    },

    replacePendingPurchases (state, payload) {
      state.pendingPurchases = payload
    },

    replaceCompletedPurchases (state, payload) {
      state.completedPurchases = payload
    },

    replaceActiveArticles (state, payload) {
      state.activeArticles = payload
    },

    replaceInactiveArticles (state, payload) {
      state.inactiveArticles = payload
    },

    replaceAllEvents (state, payload) {
      state.allEvents = payload
    }
  },
  actions: {
  },
  modules: {
  },
  plugins: [
    createPersistedState()
  ]
})

export default store
