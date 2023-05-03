<template>
  <div class="home"> 
    <Banner :companyName="this.$store.state.user.profile.accountName"/>
    <ContentCard :title="$t('shop.shopCAPS')" :description="$t('shop.shopSubtitle')" theme="blue-card" theme_btn="yellow-btn" btn_txt="Till shoppen" :data="this.shop" :screenWidth="scrWidth" name="Shop" />
    <ContentCard :title="$t('memberCAPS')" description="Bläddra bland nya medlemmar i nätverket." theme="yellow-card" theme_btn="yellow-btn" btn_txt="Till medlemmar" :data="this.members" :screenWidth="scrWidth" name="Members" />
  </div>
</template>

<script>

import Banner from './ContentBanner.vue'
import ContentCard from './ContentCard.vue'

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
    this.shop = this.$store.state.allArticles
    this.members = this.$store.state.allMembers
  }
}

</script>
