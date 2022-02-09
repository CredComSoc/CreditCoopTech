<template>
  <div>
    <Navbar />
    <Parent />
    <img src="välkommen_banner.png" alt="Välkommen">
  </div>
</template>

<script>
// @ is an alias to /src
import Navbar from '@/components/Navbar.vue'
import Parent from '@/components/userstory4/parent.vue'

export default {
  name: 'Home',
  components: {
    Navbar,
    Parent
  }
}
</script>
