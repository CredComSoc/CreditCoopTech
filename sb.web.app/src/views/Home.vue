<template>
  <div class="home"> 
    <Banner :companyName="companyName"/>
    <ContentCard title="SHOP" description="Bläddra bland senast upplagda produkter och tjänster." theme="yellow-card" theme_btn="yellow-btn" btn_txt="Till shopen" :data="shop" :screenWidth="scrWidth" name="Shop" />
    <ContentCard title="EVENTS" description="Bläddra bland senast upplagda event." theme="blue-card" theme_btn="blue-btn" btn_txt="Till events" :data="events" :screenWidth="scrWidth" name="" />
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
      events: [{ id: 0, img_path: '', title: 'Barterbowling', desc: '18/12-21', theme: 'regular' },
        { id: 1, img_path: '', title: 'Yoga med SB', desc: '12/11-21', theme: 'regular' },
        { id: 2, img_path: '', title: 'Barter-fika', desc: '12/12-21', theme: 'regular' },
        { id: 3, img_path: '', title: 'Svensk Barter 5-kamp', desc: '5/11-21', theme: 'regular' },
        { id: 5, img_path: '', title: 'Systuga', desc: '1/12-21', theme: 'regular' },
        { id: 6, img_path: '', title: 'Systuga', desc: '1/12-21', theme: 'regular' }],
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
