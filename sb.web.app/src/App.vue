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
    <SaldoCard :saldo="saldo" :screenWidth="windowWidth"/>
    <Footer id="footer" />
  </div>
  <!-- < Login page /> -->
  <div v-else>
     <router-view />
  </div>
</template>

<script>
// @ is an alias to /src
import Navbar from './components/Navbar.vue'
import Footer from '@/components/Footer.vue'
import SaldoCard from '@/components/SaldoCard.vue'
import Login from './components/Login.vue'
import AdminNavbar from './components/AdminSection/AdminNavbar.vue'
import { useRouter, useRoute } from 'vue-router'
import { onMounted, ref } from 'vue'
import { authenticate, checkAdminStatus, getSaldo } from './serverFetch'
import { useWindowSize } from 'vue-window-size'
 
// import Home from '@/components/Home.vue'
//import Parent from '@/components/userstory4/parent.vue'

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
      } else {
        this.auth = res
      }
    })
    getSaldo().then((res) => {
      this.saldo = res
    })

    setInterval(() => getSaldo().then((res) => {
      this.saldo = res
    }), 10000)
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
