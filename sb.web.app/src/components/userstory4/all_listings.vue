<!-- 
*   Component which display all listings/articles relevant to the search result. 
*   SearchData is a array of listing-objects, which contain relevant information
*   for the Listing component. 
*   A toggle-event is active on each li-element, displaying additional information
*   when clicked.
-->

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
  vertical-align: top;
}

/* @media only screen and (min-width: 1200) {
  li {
    display: inline-block;
    flex-grow: 1;
    width: calc(100% * (1/4) - 10px - 1px);
  }
} */

</style>
