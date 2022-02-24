<template>
  <b-container class="mb-12">
    <ul>
      <b-row cols="4" class="row g-0">
        <li v-for="item in searchData['allListings']" :key="item.title">
          <b-col>
            <Listing @togglePopupEvent="togglePopup" :listingObj="item"/>
          </b-col>
        </li>
      </b-row>
    </ul>
  </b-container>
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
li {
  display: inline-block;
}
</style>
