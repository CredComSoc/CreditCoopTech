<template>
  <div className="flexbox-container">
    <div className="title_container flexbox-item">
      <h1 className="title"> MIN SIDA </h1>
    </div>
    <div className='topnav flexbox-item' id='myTopnav'>
      <a href='#' @click="this.tab='profile'" :class="{ active: this.tab==='profile' }" id='profile'>Min profil</a>
      <a href='#' @click="this.tab='purchases'" :class="{ active: this.tab==='purchases' }" id='purchases'>Mina köp</a>
      <a href='#' @click="this.tab='products'" :class="{ active: this.tab==='products' }" id='products'>Mina artiklar</a>
      <a id='statistik'>Min statistik</a>
      <a href='#' @click="this.tab='requests'" :class="{ active: this.tab==='requests' }" id='purchasesrequests'>Mina köpförfrågningar</a>
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
}

.flexbox-item {
  margin: 10px;
  max-width: 1100px;
  width: 1100px;
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

/* Style the links inside the navigation bar */
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

h1  {
  text-align: center;
}

</style>
