<template>
  <div class="wrapper">

    <div>
      <h2 class="center-text">SHOP</h2>
    </div>

    <div class="center">
        <Searchfield @searchEvent="triggerSearch" />
    </div>

    <br>

    <div class="main">
      <!-- KOLUMN FÖR KATERGORI-->
      <div class="categories">
        <Categories @filterEvent="filteringMethod"/>
      </div>

      <!-- KOLYMN FÖR PRODUKTER -->
      <div class="listings">
        <div v-if="this.productsSearchData.length !== 0">
          <h3 >Produkter</h3>
          <Alllistings @togglePopupEvent="openPopUp" :key=productsSearchData :search-data=productsSearchData />
        </div>
        <div v-if="this.servicesSearchData.length !== 0">
          <h3>Tjänster</h3>
          <Alllistings @togglePopupEvent="openPopUp" :key=servicesSearchData :search-data=servicesSearchData />
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
      getAllListings,
      categoryArray: [],
      destinationsArray: [],
      articleArray: []
    }
  },

  components: {
    Searchfield,
    Alllistings,
    ListingPopup,
    Categories
  },

  methods: {
    triggerSearch (newSearchWord) {
      this.getAllListings(newSearchWord, this.destinationsArray, this.categoryArray, this.articleArray).then(res => {
        return res
      })
        .then(data => {
          this.productsSearchData = data.allProducts
          this.servicesSearchData = data.allServices
        })
    },
    openPopUp (listingObj) {
      this.popupActive = true
      this.listingObjPopup = listingObj
    },
    closePopup (listingObj) {
      this.popupActive = false
    },
    filteringMethod (checked, type, value) {
      if (type === 'destination') {
        this.changeFiltering(checked, this.destinationsArray, value)
      } else if (type === 'category') {
        this.changeFiltering(checked, this.categoryArray, value)
      } else if (type === 'article') {
        this.changeFiltering(checked, this.articleArray, value)
      }
    },
    changeFiltering (checked, specificArray, value) {
      if (!checked) {
        specificArray.push(value)
      } else {
        specificArray.splice(specificArray.indexOf(value), 1)
      }
    }
  },
  
  created: function () {
    this.triggerSearch('')
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
