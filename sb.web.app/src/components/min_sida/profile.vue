<template>
  <div className="flexbox-container">
    <div v-if="isMobile()">
      <div className='topnav mobnav' id='myMobnav' v-if="isMobile()">
        <a style="background-color:skyblue" href='#' @click="this.tab='profile'" :class="{ active: this.tab!='purchases' && this.tab!='products' && this.tab!='requests' }" id='profile'>Profil</a>
        <a style="background-color:antiquewhite" href='#' @click="this.tab='purchases'" :class="{ active: this.tab==='purchases' }" id='purchases'>Köp</a>
        <a style="background-color:#b3ffb3" href='#' @click="this.tab='products'" :class="{ active: this.tab==='products' }" id='products'>Artiklar</a>
        <a style="background-color:lavender" id='statistik'>Statistik</a>
        <a style="background-color:papayawhip" href='#' @click="this.tab='requests'" :class="{ active: this.tab==='requests' }" id='requests'>Behov</a>
        <a style="background-color:#ffa366" id='settings'>Inställningar</a>
      </div>
    </div>
    <div className="title_container flexbox-item" v-if="!isMobile()">
      <h1 className="title" > MIN SIDA </h1>
    </div>
    <div className='topnav flexbox-item' id='myTopnav' v-if="!isMobile()">
      <a href='#' @click="this.tab='profile'" :class="{ active: this.tab!='purchases' && this.tab!='products' && this.tab!='requests' }" id='profile'>Min profil</a>
      <a href='#' @click="this.tab='purchases'" :class="{ active: this.tab==='purchases' }" id='purchases'>Mina köp</a>
      <a href='#' @click="this.tab='products'" :class="{ active: this.tab==='products' }" id='products'>Mina artiklar</a>
      <a id='statistik'>Min statistik</a>
      <a href='#' @click="this.tab='requests'" :class="{ active: this.tab==='requests' }" id='requests'>Mina köpförfrågningar</a>
      <a id='settings'>Inställningar</a>
    </div>
    <div className="content flexbox-item">
      <MyPurchases v-if="this.tab==='purchases'"/>
      <MyProducts v-else-if="this.tab==='products'"/>
      <MyRequests v-else-if="this.tab==='requests'"/>
      <MyProfile v-else/>
    </div>
  </div>
</template>

<script>
// @ is an alias to /src
import MyProfile from '@/components/min_sida/MyProfile.vue'
import MyPurchases from '@/components/min_sida/MyPurchases.vue'
import MyProducts from '@/components/min_sida/MyProducts.vue'
import MyRequests from '@/components/min_sida/MyRequests.vue'

export default {
  data () {
    return {
    }
  },
  components: {
    MyProfile,
    MyPurchases,
    MyProducts,
    MyRequests
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
  },
  methods: {
    isMobile () {
      if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        return true
      } else {
        return false
      }
    }
  }
}

</script>

<!-- Add 'scoped' attribute to limit CSS to this component only -->
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
    flex-direction: column;
    flex-wrap: wrap;
    align-items: center;
    min-width: 860px;
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
  font-size: 1.5rem;
  letter-spacing: 0.3em;
}

.topnav {
  overflow: hidden;
  display: flex;
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
  margin-bottom: -1px;
  margin-left: -1px;
  margin-right: -1px;
  border-width: 2px;
  font-size: 1.24rem;
}

.topnav a.active {
  color: dimgrey;
  border-bottom: none;
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
