<template>
  <div class="home"> 
    <Banner :companyName="this.$store.state.user.profile.accountName"/>
    <ContentCard :title="$t('marketplace')" :description="$t('shop.shopSubtitle')" theme="blue-card" theme_btn="yellow-btn" :btn_txt="$t('shop.see_the_shop')" :data="this.shop" :screenWidth="scrWidth" name="Shop" />
    <ContentCard :title="$t('member')" :description="$t('shop.shopSubtitle')" theme="yellow-card" theme_btn="yellow-btn" :btn_txt="$t('nav.all_members')" :data="this.members" :screenWidth="scrWidth" name="Members" />
  </div>
</template>

<script>

import Banner from './ContentBanner.vue'
import ContentCard from './ContentCard.vue'
import { setStoreData } from '../../serverFetch'

export default {
  name: 'Home',
  components: {
    Banner,
    ContentCard
  },
  props: ['scrWidth'],
  data () {
    return {
      shop: [],
      members: []
    }
  },
  mounted: function () {
    // setStoreData()  //unnecessary call because it is called in the router index

    this.shop = this.$store.state.allArticles
    this.members = this.$store.state.allMembers
  }
}

</script>
