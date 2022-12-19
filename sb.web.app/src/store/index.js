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
    creditLine: 0,

    requests: [],
    pendingPurchases: [],
    completedTransactions: [],

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
    replaceCreditLine (state, payload) {
      state.creditLine = payload
    },

    replaceRequests (state, payload) {
      state.requests = payload
    },

    replacePendingPurchases (state, payload) {
      state.pendingPurchases = payload
    },

    replaceCompletedTransactions (state, payload) {
      state.completedTransactions = payload
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
