<template>
  <!-- < Admin page /> -->
  <div v-if="auth && admin">
    <AdminNavbar :screenWidth="windowWidth"/>
    <router-view/>
  </div>
  <!-- < User page /> -->
  <div id="app" v-else-if="auth">
    <Navbar :screenWidth="windowWidth"/>
    <div className='body container'>
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
import { authenticate, checkAdminStatus, getSaldo, fetchData, setStoreData } from './serverFetch'
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
  mounted () {
    authenticate().then((res) => {
      if (res) {    
        checkAdminStatus().then((res2) => {
          this.auth = res
          this.admin = res2
        })
      } 
    })

    // setStoreData()
  }
}
</script>

<!-- Add 'scoped' attribute to limit CSS to this component only -->
<style scoped>
</style>
