<template>
  <Banner />
  <Footer />
</template>

<script>
// @ is an alias to /src

import Banner from '@/components/ContentBanner.vue'

import Footer from '@/components/Footer.vue'

export default {
  name: 'Home',
  components: {
    Banner,
    Footer
  }
}
</script>
