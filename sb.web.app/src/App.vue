<template>
  <div id="app">
    <Navbar />
    <Banner />
    <SaldoCard/>
    <!-- <ContentCard title="Events" description="Bläddra bland senast upplagda produkter och tjänster." /> -->
    <ContentCard title="SHOP" description="Bläddra bland senast upplagda produkter och tjänster." theme="yellow-card" theme_btn="yellow-btn" btn_txt="Till shopen" />
    <ContentCard title="EVENTS" description="Bläddra bland senast upplagda event." theme="blue-card" theme_btn="blue-btn" btn_txt="Till events"  />
    <ContentCard title="MEDLEMMAR" description="Bläddra bland nya medlemmar i nätverket." theme="yellow-card" theme_btn="yellow-btn" btn_txt="Till medlemmar" />
    <Footer />
  </div>
</template>

<script>
// @ is an alias to /src
import Navbar from './components/Navbar.vue'
import Banner from '@/components/ContentBanner.vue'
import Footer from '@/components/Footer.vue'
import ContentCard from '@/components/ContentCard.vue'
import SaldoCard from '@/components/SaldoCard.vue'

export default {
  name: 'Home',
  components: {
    Banner,
    Footer,
    Navbar,
    ContentCard,
    SaldoCard
  }
}
</script>
