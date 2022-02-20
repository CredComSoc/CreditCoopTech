<template>
  <div>
    <Navbar />
    <Parent />
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
