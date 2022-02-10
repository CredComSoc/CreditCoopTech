<template>
  <div id="app">
    <Navbar />
    <img src="./assets/welcome_banner.png" alt="Välkommen"/>
  </div>
</template>

<script>
// @ is an alias to /src
import Navbar from './components/Navbar.vue'

export default {
  name: 'Home',
  components: {
    Navbar
  }
}
</script>
