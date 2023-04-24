<template>
  <div className="flexbox-container">
  <div>
    <h1 className="title">HANDELSREGLER & TIPS</h1>
  </div>

  <!--To add another link in the menybar, add another <a href... here and add this.tab!='namn' in the first href -->
  <div className='topnav flexbox-item' id='myTopnav'>
      <a href='#' @click="this.tab='questions'" :class="{ active: this.tab!='bartertips' && this.tab!='information' && this.tab!='policy'}" id='questions'>Vanliga frågor</a>
      <a href='#' @click="this.tab='bartertips'" :class="{ active: this.tab==='bartertips' }" id='bartertips'>Bartertips</a>
      <a href='#' @click="this.tab='information'" :class="{ active: this.tab==='information' }" id='information'>Handelsregler</a>
      <a href='#' @click="this.tab='policy'" :class="{ active: this.tab==='policy' }" id='policy'>Integritetspolicy</a>
    </div>
    <!--also, add another <NAME... here -->
    <div className="content flexbox-item">
      <BarterTips v-if="this.tab==='bartertips'"/>
      <Information v-else-if="this.tab==='information'"/>
      <Policy v-else-if="this.tab==='policy'"/>
      <Questions v-else/>
    </div>
  </div>
</template>

<script>
// @ is an alias to /src
// to add another nav in the menybar, add import here 
import Questions from '@/components/FAQ/Questions.vue'
import BarterTips from '@/components/FAQ/BarterTips.vue'
import Information from '@/components/FAQ/Information.vue'
import Policy from '@/components/FAQ/Policy.vue'

export default {
  data () {
    return {
    }
  },
  // to add another nav in the menybar, add the name here, and then move over to Navbar.vue in Navbar
  components: {
    Questions,
    BarterTips,
    Policy,
    Information
  },
  created () {
    if (this.$route.params.tab) {
      this.tab = this.$route.params.tab
    }
  },
  beforeUpdate () {
    if (this.$route.params.tab) {
      this.tab = this.$route.params.tab
    }
  }
}

</script>

<style scoped>
 * {
  font-family: Ubuntu;
  font-style: normal;
  font-weight: normal;
  letter-spacing: 0.05em;
  padding: 0;
  margin:0;
}

@media screen and (min-width: 860px) {
  .flexbox-container {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    min-width: 860px;
    justify-content: center;
    flex-direction: column;
  }

  .flexbox-item {
  margin: 10px;
  max-width: 1100px;
  width: 1100px;
  }

}

.title {
  margin-top: 4rem;
  margin-bottom: 4rem;
  font-size: 2.2rem;
  letter-spacing: 0.3em;
}

.topnav {
  text-align: center;
}

.topnav a {
  text-align: center;
  padding: 8px 25px 8px 25px;
  text-decoration: none;
  font-size: 15px;
  color: steelblue;
  border-style: solid;
  border-color: gainsboro;
  border-radius: 5px;
  margin-top: -1px;
  margin-bottom: 2px;
  margin-left: 1px;
  margin-right: -1px;
  border-width: 2px;
  font-size: 1.24rem;
}

.topnav a:hover {
      box-shadow: 2px 2px 2px rgba(0, 0, 0, 0.25);  
}

.topnav a.active {
  color: dimgrey;
  font-weight: bold;
}

.mobnav {
  position:fixed;
}

.botnav a {
  text-align: center;
  text-decoration: none;
  color: steelblue;
  border-style: solid;
  border-color: gainsboro;
  margin-left: -1px;
  margin-right: -1px;
  font-size: 1.1rem;
}

h1  {
  text-align: center;
}

</style>
