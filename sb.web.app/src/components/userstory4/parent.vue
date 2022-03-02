<template>
  <b-container fluid>

    <b-row align-h="center">
      <b-col cols="4" class="d-flex align-items-center justify-content-center">
        <h2>SHOP</h2>
      </b-col>
    </b-row>

    <b-row align-h="center">
        <Searchfield @searchEvent="testMethod" />
    </b-row>

    <br>

    <b-row>
      <!-- KOLUMN FÖR KATERGORI-->
      <b-col cols="3">
        <Categories/>
      </b-col>

      <!-- KOLYMN FÖR PRODUKTER -->
      <b-col cols="9">
        <b-row>
          <b-col>
            <h3>Produkter</h3>
          </b-col>
        </b-row>
        <b-row>
          <Alllistings @togglePopupEvent="togglePopup" :key=searchData :search-data=searchData />
          <ListingPopup @closePopup="closePopup" v-if="popupActive" :key="popupActive" :listing-obj=listingObjPopup />
        </b-row>
      </b-col>
    </b-row>
    
  </b-container>
</template>

<script>
import Searchfield from '@/components/userstory4/searchfield.vue'
import Alllistings from '@/components/userstory4/all_listings.vue'
import ListingPopup from '@/components/userstory4/ListingPopup.vue'
import Categories from '@/components/userstory4/Categories.vue'

export default {

  data () {
    return {
      searchData: [],
      singleListingData: [],
      popupActive: false,
      listingObjPopup: Object
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
      this.searchData = this.backendFunction()
    },
    togglePopup (listingObj) {
      this.popupActive = true
      this.listingObjPopup = listingObj
    },
    closePopup (listingObj) {
      this.popupActive = false
    },
    backendFunction () {
      return {
        allListings: [
          {
            title: 'annas bullar', 
            ign: 'random', 
            shortDesc: 'goda bullar för virtuella pengar', 
            longDesc: 'Välkommen att handla nybakat bakverk till ditt företag hos Annas Kanelbullar!\nVarje paket innehåller 10 bakverk.\nVängligen ange antal 10-pack.\n*Paket upphämtas på egen hand',
            destination: 'Söderköping', 
            price: '50 bKr'
          },
          {
            title: 'Peters bullar', 
            ign: 'random', 
            shortDesc: 'goda bullar för virtuella pengar', 
            longDesc: 'Välkommen att handla nybakat bakverk till ditt företag hos Annas Kanelbullar!\nVarje paket innehåller 10 bakverk.\nVängligen ange antal 10-pack.\n*Paket upphämtas på egen hand',
            destination: 'Söderköping', 
            price: '50 bKr'
          },
          {
            title: 'Karlssons bullar', 
            ign: 'random', 
            shortDesc: 'goda bullar för virtuella pengar', 
            longDesc: 'Välkommen att handla nybakat bakverk till ditt företag hos Annas Kanelbullar!\nVarje paket innehåller 10 bakverk.\nVängligen ange antal 10-pack.\n*Paket upphämtas på egen hand',
            destination: 'Söderköping', 
            price: '50 bKr'
          },
          {
            title: 'Rågers bullar', 
            ign: 'random', 
            shortDesc: 'goda bullar för virtuella pengar', 
            longDesc: 'Välkommen att AOIUGFHJAIOFAOIJFOAJFIAJFIOWJIFOAJOIWJ nybakat bakverk till ditt företag hos Annas Kanelbullar!\nVarje paket innehåller 10 bakverk.\nVängligen ange antal 10-pack.\n*Paket upphämtas på egen hand',
            destination: 'Söderköping', 
            price: '50 bKr'
          },
          {
            title: 'Brorsans bullar', 
            ign: 'random', 
            shortDesc: 'goda bullar för virtuella pengar', 
            longDesc: 'Välkommen att handla nybakat bakverk till ditt företag hos Annas Kanelbullar!\nVarje paket innehåller 10 bakverk.\nVängligen ange antal 10-pack.\n*Paket upphämtas på egen hand',
            destination: 'Söderköping', 
            price: '50 bKr'
          },
          {
            title: 'Kalles bullar', 
            ign: 'random', 
            shortDesc: 'goda bullar för virtuella pengar', 
            longDesc: 'Välkommen att handla nybakat bakverk till ditt företag hos Annas Kanelbullar!\nVarje paket innehåller 10 bakverk.\nVängligen ange antal 10-pack.\n*Paket upphämtas på egen hand',
            destination: 'Söderköping', 
            price: '50 bKr'
          }
        ]
      }
    }
  }
}
</script>

<style scoped>

h2 {
  margin-top: 2rem;
  font-size: 3rem;
}

h3 {
  margin-left: 3rem;
}
</style>
