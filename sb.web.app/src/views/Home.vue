<template>
  <div class="home"> 
    <Banner :companyName="companyName"/>
    <ContentCard title="SHOP" description="Bläddra bland senast upplagda produkter och tjänster." theme="blue-card" theme_btn="yellow-btn" btn_txt="Till shopen" :data="shop" :screenWidth="scrWidth" name="Shop" />
    <ContentCard title="MEDLEMMAR" description="Bläddra bland nya medlemmar i nätverket." theme="yellow-card" theme_btn="yellow-btn" btn_txt="Till medlemmar" :data="members" :screenWidth="scrWidth" name="Members" />
  </div>
</template>

<script>

import Banner from '@/components/ContentBanner.vue'
import ContentCard from '@/components/ContentCard.vue'
import { EXPRESS_URL } from '../serverFetch'

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
      members: [],
      companyName: ''
    }
  },
  created () {
    fetch(EXPRESS_URL + '/home', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json'
      },
      credentials: 'include'
    }).then(res => res.json())
      .then(data => {
        this.shop = data.shop
        this.members = data.members
        this.companyName = data.companyName
      })
  }
}

</script>
