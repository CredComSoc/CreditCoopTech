<template>
  <div class="banner">
    <img src="../assets/banner.jpg" alt="välkommen">
    <WelcomeMessage :companyName="companyName"/>
  </div>
</template>

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
// Component that represent outer container for WelcomeMessage and banner image

import WelcomeMessage from '@/components/WelcomeMessage.vue'

export default {
  name: 'Banner',
  components: {
    WelcomeMessage
  },
  props: ['companyName']
}
</script>
