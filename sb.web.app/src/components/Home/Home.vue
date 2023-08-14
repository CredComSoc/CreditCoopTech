<template>
  <div class="home"> 
    <Banner :companyName="this.$store.state.user.profile.accountName"/>
    <ContentCard :title="$t('shop.shopCAPS')" :description="$t('shop.shopSubtitle')" theme="blue-card" theme_btn="yellow-btn" :btn_txt="$t('shop.see_the_shop')" :data="this.shop" :screenWidth="scrWidth" name="Shop" />
    <ContentCard :title="$t('memberCAPS')" :description="$t('shop.shopSubtitle')" theme="yellow-card" theme_btn="yellow-btn" :btn_txt="$t('nav.all_members')" :data="this.members" :screenWidth="scrWidth" name="Members" />
  </div>
</template>

<script>

import Banner from './ContentBanner.vue'
import ContentCard from './ContentCard.vue'
import { authenticate, checkAdminStatus, getSaldo, fetchData } from '../../serverFetch'

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
  methods: {
    async setStoreData () {
      if (this.auth && !document.hidden) {
        const data = await fetchData().then((res) => {
          if (res) {
            return res
          }
        })
        if (data) { 
          window.localStorage.removeItem('vuex')
          if (data.user) {
            this.$store.commit('replaceUser', data.user)

            const oldNotifications = []
            const newNotifications = []

            for (const notification of data.user.notifications) {
              if (notification.seen) {
                oldNotifications.push(notification)
              } else {
                newNotifications.push(notification)
              }
            }

            oldNotifications.sort(function (a, b) {
              return new Date(b.date) - new Date(a.date)
            })

            newNotifications.sort(function (a, b) {
              return new Date(b.date) - new Date(a.date)
            })

            this.$store.commit('replaceOldNotifications', oldNotifications)
            this.$store.commit('replaceNewNotifications', newNotifications)
          }

          if (data.allArticles) {
            this.$store.commit('replaceAllArticles', data.allArticles)
          }

          if (data.allMembers) {       
            this.$store.commit('replaceAllMembers', data.allMembers)
          }

          if (data.myCart) {
            this.$store.commit('replaceMyCart', data.myCart)

            let cartSize = 0
            for (const item of data.myCart) {
              cartSize += item.quantity
            }
            this.$store.commit('replaceMyCartSize', cartSize)
          }
          
          this.$store.commit('replaceSaldo', data.saldo)
          this.$store.commit('replaceCreditLine', data.creditLine)
          this.$store.commit('replaceCreditLimit', data.creditLimit)
          if (data.requests) {
            this.$store.commit('replaceRequests', data.requests)            
          }

          if (data.pendingPurchases) {
            this.$store.commit('replacePendingPurchases', data.pendingPurchases)            
          }    

          if (data.completedTransactions) {
            this.$store.commit('replaceCompletedTransactions', data.completedTransactions)            
          }           
          
          if (data.allEvents) {       
            this.$store.commit('replaceAllEvents', data.allEvents)
          }
          // console.log(this.$store.state.user.email)
        }
      }
    }
  },
  mounted: function () {
    this.setStoreData()

    setInterval(async () => {
      this.setStoreData()
    }, 2000)


    this.shop = this.$store.state.allArticles
    this.members = this.$store.state.allMembers
  }
}

</script>
