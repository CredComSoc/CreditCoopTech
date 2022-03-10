<template>
  <div class="wrapper">

    <div>
      <h2 class="center-text">SHOP</h2>
    </div>

    <div class="center">
        <Searchfield @searchEvent="testMethod" />
    </div>

    <br>

    <div class="main">
      <!-- KOLUMN FÖR KATERGORI-->
      <div class="categories">
        <Categories/>
      </div>

      <!-- KOLYMN FÖR PRODUKTER -->
      <div class="listings">
        <div>
          <h3>Produkter</h3>
          <Alllistings @togglePopupEvent="togglePopup" :key=productsSearchData :search-data=productsSearchData />
        </div>
        <div>
          <h3>Tjänster</h3>
          <Alllistings @togglePopupEvent="togglePopup" :key=servicesSearchData :search-data=servicesSearchData />
        </div>
        <ListingPopup @closePopup="closePopup" v-if="popupActive" :key="popupActive" :listing-obj=listingObjPopup />
      </div>
    </div>
    
  </div>
</template>

<script>
import Searchfield from '@/components/userstory4/searchfield.vue'
import Alllistings from '@/components/userstory4/all_listings.vue'
import ListingPopup from '@/components/userstory4/ListingPopup.vue'
import Categories from '@/components/userstory4/Categories.vue'
import { getAllListings } from './../../serverFetch.js'

export default {

  data () {
    return {
      productsSearchData: [],
      servicesSearchData: [],
      singleListingData: [],
      popupActive: false,
      listingObjPopup: Object,
      getAllListings
    }
  },

  components: {
    Searchfield,
    Alllistings,
    ListingPopup,
    Categories
  },

  methods: {
    testMethod (newSearchWord) {
      this.backendFunction(newSearchWord)
    },
    togglePopup (listingObj) {
      this.popupActive = true
      this.listingObjPopup = listingObj
    },
    closePopup (listingObj) {
      this.popupActive = false
    },
    backendFunction (newSearchWord) {
      this.getAllListings(newSearchWord).then(res => {
        return res
      })
        .then(data => {
          this.productsSearchData = data.allProducts
          this.servicesSearchData = data.allServices
        })
    }
  }
}
</script>

<style scoped>

.wrapper {
  display: flex;
  flex-direction: column;
}

.center {
  justify-content: center;
}

.main {
  display: flex;
  flex-direction: row;
  
}

.categories {
  flex-basis: 20%;
  height: auto;

}

.listings {
  flex-basis: 80%;
  width: auto;
  margin-left: 3rem;
}

.center-text {
  text-align: center;
}

h2 {
  margin-top: 2rem;
  font-size: 3rem;
}

h3 {
  margin-left: 1rem;
}
</style>
