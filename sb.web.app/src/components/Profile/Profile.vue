<template> 
  <div className="flexbox-container">
    <div className='topnav mobnav' id='myMobnav' v-if="isMobile()">
      <a style="background-color:skyblue" href='#' @click="this.tab='profile'" :class="{ active: this.tab !== 'purchases' && this.tab !== 'articles' && this.tab!=='requests' }" id='profile'>{{ $t('user.profile') }}</a>
      <a style="background-color:antiquewhite" href='#' @click="this.tab='purchases'" :class="{ active: this.tab === 'purchases' }" id='purchases'>{{ $t('nav.trades') }}</a>
      <a style="background-color:#b3ffb3" href='#' @click="this.tab='articles'" :class="{ active: this.tab === 'articles' }" id='articles'>{{ $t('nav.items') }}</a>
      <!-- <a style="background-color:papayawhip" href='#' @click="this.tab='requests'" :class="{ active: this.tab === 'requests' }" id='requests'>{{ $t('Needs') }}</a> -->
      <a style="background-color:papayawhip" href='#' @click="this.tab='economy'" :class="{ active: this.tab === 'economy' }" id='economy'>{{ $t('nav.my_balance') }}</a>

    </div>
    <div className="title_container flexbox-item" v-if="!isMobile()">
      <h1 className="title" > {{ $t('nav.my_account') }} </h1>
    </div>
    <div className='topnav flexbox-item' id='myTopnav' v-if="!isMobile()">
      <a href='#' @click="this.tab='profile'" :class="{ active: this.tab!='purchases' && this.tab!='articles' && this.tab!='requests' && this.tab!='economy' }" id='profile'> {{ $t('nav.my_profile') }} </a>
      <a href='#' @click="this.tab='purchases'" :class="{ active: this.tab==='purchases' }" id='purchases'> {{ $t('nav.my_trades') }} </a>
      <a href='#' @click="this.tab='articles'" :class="{ active: this.tab==='articles' }" id='articles'> {{ $t('nav.my_items') }} </a>
     <!-- <a href='#' @click="this.tab='requests'" :class="{ active: this.tab==='requests' }" id='requests'> {{ $t('nav.my_purchase_requests') }} </a>-->
      <a href='#' @click="this.tab='economy'" :class="{ active: this.tab==='economy' }" id='economy'> {{ $t('nav.balance') }} </a>
    </div>
    <div className="content flexbox-item">
      <MyPurchases v-if="this.tab==='purchases'"/>
      <MyArticles v-else-if="this.tab==='articles'"/>
      <!--<MyRequests v-else-if="this.tab==='requests'"/>-->
      <MyEconomy v-else-if="this.tab==='economy'"/>
      <MyProfile v-else/>
    </div> 
  </div>
</template>

<script>
// @ is an alias to /src 
import MyProfile from '@/components/Profile/MyProfile.vue'
import MyPurchases from '@/components/Profile/MyPurchases.vue'
import MyArticles from '@/components/Profile/MyArticles.vue'
//import MyRequests from '@/components/Profile/MyRequests.vue'
import MyEconomy from '@/components/Profile/MyEconomy.vue'

export default {
  data () {
    return {
      tab: ''
    }
  },
  components: {
    MyProfile,
    MyPurchases,
    MyArticles,
    //MyRequests,
    MyEconomy
  },
  created () {
    if (this.$route.params.tab) {
      this.tab = this.$route.params.tab
    }
  },
  beforeMount () {
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
  text-transform: uppercase;
}

.topnav {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
  flex-direction: row;
}

.topnav a {
  text-align: center;
  margin: 0.125em;
  padding: 8px 17.5px 8px 17.5px;
  text-decoration: none;
  font-size: 15px;
  color: steelblue;
  border-style: solid;
  border-color: gainsboro;
  border-radius: 5px;
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
  height: fit-content;
  overflow: inherit;
  top: 130px;
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
