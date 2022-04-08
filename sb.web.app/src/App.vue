<template>
  <!-- <MinSida /> -->
  <div id='app'>
    <Navbar :screenWidth='screenWidth'/>
      <div className='body'>
        <router-view/>
      </div>
    <SaldoCard :saldo='saldo' :screenWidth='screenWidth'/>
    <Footer id='footer' />
    <Form />
  </div>
</template>

<script>
// @ is an alias to /src
import Navbar from './components/Navbar.vue'
import Footer from '@/components/Footer.vue'
import SaldoCard from '@/components/SaldoCard.vue'
import { useRouter, useRoute } from 'vue-router'
import { onMounted, ref } from 'vue'
 
// import Home from '@/components/Home.vue'
//import Parent from '@/components/userstory4/parent.vue'

export default {
  name: 'Home',
  components: {
    Navbar,
    Footer,
    SaldoCard
  },
  setup () {
    const route = useRoute()
    const router = useRouter()
    
    onMounted(async () => {
      await router.isReady()
      onResize()
      window.addEventListener('resize', onResize)
    })

    const screenWidth = ref(0)
    const onResize = () => {
      const scrWidth = window.innerWidth
      screenWidth.value = scrWidth
      if (route.path === 'Home' || route.path === '/') {
        router.push({
          name: 'Home',
          params: { scrWidth }
        })
      }
    }

    return {
      screenWidth
    }
  },
  data () {
    return {
      saldo: 2000
    }
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

</style>
