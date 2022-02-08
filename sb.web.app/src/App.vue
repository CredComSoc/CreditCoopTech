<template>
  <Navbar />
  <img src="välkommen_banner.png" alt="Välkommen">
</template>

<script>
// @ is an alias to /src
import Navbar from '@/components/Navbar.vue'

export default {
  name: 'Home',
  components: {
    Navbar
  }
}
</script>
