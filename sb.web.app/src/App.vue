<template>
  <div id="app">
    <Navbar />
    <Banner />
    <SaldoCard/>
    <Footer />
  </div>
</template>

<script>
// @ is an alias to /src
import Navbar from './components/Navbar.vue'
import Banner from '@/components/ContentBanner.vue'
import Footer from '@/components/Footer.vue'
import SaldoCard from '@/components/SaldoCard.vue'

export default {
  name: 'Home',
  components: {
    Banner,
    Footer,
    Navbar,
    SaldoCard
  }
}
</script>
