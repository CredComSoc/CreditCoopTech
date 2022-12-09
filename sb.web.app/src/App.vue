<template>
  <!-- < Admin page /> -->
  <div v-if="auth && admin">
    <AdminNavbar :screenWidth="windowWidth"/>
    <router-view/>
  </div>
  <!-- < User page /> -->
  <div id="app" v-else-if="auth">
    <Navbar :screenWidth="windowWidth"/>
      <div id="space">
      </div>
      <div className='body'>
        <router-view :scrWidth="windowWidth"/>
      </div>
    <SaldoCard :screenWidth="windowWidth"/>
    <Footer id="footer" />
  </div>
  <!-- < Login page /> -->
  <div v-else>
     <router-view />
  </div>
</template>

<script>
// @ is an alias to /src
import Navbar from './components/Navbar/Navbar.vue'
import Footer from '@/components/Footer/Footer.vue'
import SaldoCard from '@/components/SaldoCard.vue'
import AdminNavbar from './components/AdminSection/AdminNavbar.vue'
import { useRouter } from 'vue-router'
import { onMounted, ref } from 'vue'
import { authenticate, checkAdminStatus, getSaldo, fetchData } from './serverFetch'
import { useWindowSize } from 'vue-window-size'

export default {
  name: 'Home',
  components: {
    Navbar,
    Footer,
    SaldoCard,
    AdminNavbar
  },
  setup () {
    const { width, height } = useWindowSize()

    return {
      windowWidth: width,
      windowHeight: height
    }
  },
  data () {
    return {
      saldo: null,
      auth: false,
      admin: false
    }
  },
  methods: {
    async setStoreData () {
      if (this.auth && !document.hidden) {
        const data = await fetchData().then((res) => {
          if (res) {
            return res
          }
        })
        if (data) { 
          window.localStorage.removeItem('vuex')
          if (data.user) {
            this.$store.commit('replaceUser', data.user)

            const oldNotifications = []
            const newNotifications = []

            for (const notification of data.user.notifications) {
              if (notification.seen) {
                oldNotifications.push(notification)
              } else {
                newNotifications.push(notification)
              }
            }

            oldNotifications.sort(function (a, b) {
              return new Date(b.date) - new Date(a.date)
            })

            newNotifications.sort(function (a, b) {
              return new Date(b.date) - new Date(a.date)
            })

            this.$store.commit('replaceOldNotifications', oldNotifications)
            this.$store.commit('replaceNewNotifications', newNotifications)
          }

          if (data.allArticles) {
            this.$store.commit('replaceAllArticles', data.allArticles)
          }

          if (data.allMembers) {       
            this.$store.commit('replaceAllMembers', data.allMembers)
          }

          if (data.myCart) {
            this.$store.commit('replaceMyCart', data.myCart)

            let cartSize = 0
            for (const item of data.myCart) {
              cartSize += item.quantity
            }
            this.$store.commit('replaceMyCartSize', cartSize)
          }
          
          this.$store.commit('replaceSaldo', data.saldo)
          if (data.requests) {
            this.$store.commit('replaceRequests', data.requests)            
          }

          if (data.pendingPurchases) {
            this.$store.commit('replacePendingPurchases', data.pendingPurchases)            
          }    

          if (data.completedTransactions) {
            this.$store.commit('replaceCompletedTransactions', data.completedTransactions)            
          }           
          // console.log(this.$store.state.user.email)
        }
      }
    }
  },
  mounted () {
    const router = useRouter()
    authenticate().then((res) => {
      if (res) {    
        checkAdminStatus().then((res2) => {
          this.auth = res
          this.admin = res2
          if (res2) {
            router.push({ name: 'AdminHome' })
          }
        })
      } 
    })

    this.setStoreData()

    setInterval(async () => {
      this.setStoreData()
    }, 2000)
  }
}
</script>

<!-- Add 'scoped' attribute to limit CSS to this component only -->
<style scoped>
html, body {
   margin: 0;
   padding: 0;
   height: 100%;
}

.body {  
  min-height: calc(90vh - 70px);
}

#space {
  height:65px;
}

</style>
