<template>
  <div class="banner">
    <img src="../assets/banner.jpg" alt="välkommen">
    <Welcome :companyName="companyName"/>
  </div>
</template>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
   .banner{
        width: 100%;
        height: 405px;
        position: relative;
    }
    img{
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
</style>

<script>

import Welcome from '@/components/WelcomeMessage.vue'

export default {
  name: 'Banner',
  components: {
    Welcome
  },
  props: ['companyName']
}
</script>
