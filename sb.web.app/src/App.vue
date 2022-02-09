<template>
  <Navbar />
  <Banner />
</template>

<script>
// @ is an alias to /src
import Navbar from '@/components/Navbar.vue'

import Banner from '@/components/ContentBanner.vue'

export default {
  name: 'Home',
  components: {
    Navbar,
    Banner
  }
}
</script>
