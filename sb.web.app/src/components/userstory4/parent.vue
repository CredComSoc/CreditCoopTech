<template>
  <b-container fluid>
    <b-row align-h="center">
      <b-col cols="4" class="d-flex align-items-center justify-content-center">
        <h2>SHOP</h2>
      </b-col>
    </b-row>
    <b-row align-h="center">
      <b-col cols="4" class="d-flex align-items-center justify-content-center">
        <Searchfield @searchEvent="testMethod" />
      </b-col>
    </b-row>
    <b-row>
      <Alllistings @togglePopupEvent="togglePopup" :key=searchData :search-data=searchData />
      <ListingPopup v-if="popupActive" :key="popupActive" :listing-obj=listingObjPopup />
    </b-row>
  </b-container>
</template>

<script>
import Searchfield from '@/components/userstory4/searchfield.vue'
import Alllistings from '@/components/userstory4/all_listings.vue'
import ListingPopup from '@/components/userstory4/ListingPopup.vue'

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
    ListingPopup
  },

  methods: {
    testMethod (newSearchWord) {
      this.searchData = this.backendFunction()
    },
    togglePopup (listingObj) {
      this.popupActive = true
      this.listingObjPopup = listingObj
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
            price: '50 bKr', 
            amount: 1
          },
          {
            title: 'Peters bullar', 
            ign: 'random', 
            shortDesc: 'goda bullar för virtuella pengar', 
            longDesc: 'Välkommen att handla nybakat bakverk till ditt företag hos Annas Kanelbullar!\nVarje paket innehåller 10 bakverk.\nVängligen ange antal 10-pack.\n*Paket upphämtas på egen hand',
            destination: 'Söderköping', 
            price: '50 bKr', 
            amount: 1
          },
          {
            title: 'Karlssons bullar', 
            ign: 'random', 
            shortDesc: 'goda bullar för virtuella pengar', 
            longDesc: 'Välkommen att handla nybakat bakverk till ditt företag hos Annas Kanelbullar!\nVarje paket innehåller 10 bakverk.\nVängligen ange antal 10-pack.\n*Paket upphämtas på egen hand',
            destination: 'Söderköping', 
            price: '50 bKr', 
            amount: 1
          },
          {
            title: 'Rågers bullar', 
            ign: 'random', 
            shortDesc: 'goda bullar för virtuella pengar', 
            longDesc: 'Välkommen att AOIUGFHJAIOFAOIJFOAJFIAJFIOWJIFOAJOIWJ nybakat bakverk till ditt företag hos Annas Kanelbullar!\nVarje paket innehåller 10 bakverk.\nVängligen ange antal 10-pack.\n*Paket upphämtas på egen hand',
            destination: 'Söderköping', 
            price: '50 bKr', 
            amount: 1
          },
          {
            title: 'Brorsans bullar', 
            ign: 'random', 
            shortDesc: 'goda bullar för virtuella pengar', 
            longDesc: 'Välkommen att handla nybakat bakverk till ditt företag hos Annas Kanelbullar!\nVarje paket innehåller 10 bakverk.\nVängligen ange antal 10-pack.\n*Paket upphämtas på egen hand',
            destination: 'Söderköping', 
            price: '50 bKr', 
            amount: 1
          },
          {
            title: 'Kalles bullar', 
            ign: 'random', 
            shortDesc: 'goda bullar för virtuella pengar', 
            longDesc: 'Välkommen att handla nybakat bakverk till ditt företag hos Annas Kanelbullar!\nVarje paket innehåller 10 bakverk.\nVängligen ange antal 10-pack.\n*Paket upphämtas på egen hand',
            destination: 'Söderköping', 
            price: '50 bKr', 
            amount: 1
          }
        ]
      }
    }
  }
}
</script>
