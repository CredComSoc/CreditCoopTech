<template>
  <div class="container_all_listings">
    <ul>
      <li v-for="item in searchData" :key="item.title">
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
  flex-wrap: wrap;
}

ul {
  padding: 0;
}

.container_all_listings > * {
  flex-basis: 100%;
}

li {
  display: inline-block;
  flex: 1 0 calc(25% - 10px);

}

/* @media only screen and (min-width: 1200) {
  li {
    display: inline-block;
    flex-grow: 1;
    width: calc(100% * (1/4) - 10px - 1px);
  }
} */

</style>
