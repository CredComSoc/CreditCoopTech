<template>
  <div class="container_all_listings">
    <ul>
      <li v-for="item in searchData['allListings']" :key="item.title">
        <Listing @togglePopupEvent="togglePopup" :listingObj="item"/>
      </li>
    </ul>
  </div>
</template>

<script>
//  import Searchfield from '@/components/userstory4/searchfield.vue'
import Listing from '@/components/userstory4/Listing.vue'

export default {

  components: {
    Listing
  },

  methods: {
    togglePopup (listingObj) {
      this.$emit('togglePopupEvent', listingObj)
    }
  },

  props: {
    searchData: Array
  }
}
</script>

<style scoped>
.container_all_listings {
  display: flex;
  
}

.container_all_listings > * {
  flex-basis: 100%;
}

li {
  display: inline-block;
}
</style>
