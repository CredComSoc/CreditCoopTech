<template>
  <div className="flexbox-container">
    <div className="title_container flexbox-item">
      <h1 className="title"> MIN SIDA </h1>
    </div>
    <div className='topnav flexbox-item' id='myTopnav'>
      <a href='#profile' @click="activate('profile'); profile=true" :class="{ active: profile }" id='profile'>Min profil</a>
      <a href='#purchases' @click="activate('purchases'); purchases=true" :class="{ active: purchases }" id='purchases'>Mina köp</a>
      <a href='#products' @click="activate('products'); products=true" :class="{ active: products }" id='products'>Mina artiklar</a>
      <a id='statistik'>Min statistik</a>
      <a href='#requests' @click="activate('requests'); requests=true" :class="{ active: requests }" id='purchasesrequests'>Mina köpförfrågningar</a>
      <a id='settings'>Inställningar</a>
    </div>
    <div className="content flexbox-item">
      <MyPurchases v-if="purchases"/>
      <MyProducts v-else-if="products"/>
      <MyRequests v-else-if="requests"/>
      <MyProfile v-else/>
    </div>
  </div>
</template>

<script>
// @ is an alias to /src
import MyProfile from '@/components/MyProfile.vue'
import MyPurchases from '@/components/MyPurchases.vue'
import MyProducts from '@/components/MyProducts.vue'
import MyRequests from '@/components/MyRequests.vue'

export default {
  data () {
    return {
      profile: !((window.location.href.indexOf('#purchases') > -1) || (window.location.href.indexOf('#products') > -1) || (window.location.href.indexOf('#requests') > -1)),
      purchases: (window.location.href.indexOf('#purchases') > -1),
      products: (window.location.href.indexOf('#products') > -1),
      requests: (window.location.href.indexOf('#requests') > -1)
    }
  },
  components: {
    MyProfile,
    MyPurchases,
    MyProducts,
    MyRequests
  },
  methods: {
    activate: function (element) {
      this.profile = (element === 'profile')
      this.purchases = (element === 'purchases')
      this.products = (element === 'products')
      this.requests = (element === 'requests')
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

.flexbox-container {
  display: flex;
  flex-direction: column;
  flex-wrap: wrap;
  align-items: center;
  min-width: 1100px;
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
