<template>
  <div class="wrapper">

    <div>
      <h2 class="center-text">SHOP</h2>
    </div>

    <div class="center">
        <Searchfield @searchEvent="triggerSearch" :place-holder-message="'Vad vill du söka efter idag?'" />
    </div>

    <br>

    <div class="main">

    <div class="filterButton">
      <FilterButton v-if="filterButtonActive" @filterTrigger="triggerFilter" />
    </div>

      <!-- KOLUMN FÖR KATERGORI-->
      <div id="categories" class="categories">
        <Categories v-if="filterActive" @filterEvent="filteringMethod"/>
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
        <ListingPopup @closePopup="closePopup" @placeInCart="this.placeInCart" v-if="popupActive" :key="popupActive" :listing-obj=listingObjPopup :username="this.username" />
      </div>
    </div>
    
  </div>
</template>

<script>
import Searchfield from '@/components/userstory4/searchfield.vue'
import Alllistings from '@/components/userstory4/all_listings.vue'
import ListingPopup from '@/components/userstory4/ListingPopup.vue'
import Categories from '@/components/userstory4/Categories.vue'
import FilterButton from '@/components/userstory4/filterButton.vue'
import { EXPRESS_URL, getAllListings } from './../../serverFetch.js'

export default {

  data () {
    return {
      productsSearchData: [],
      servicesSearchData: [],
      singleListingData: [],
      popupActive: false,
      filterActive: false,
      filterButtonActive: false,
      listingObjPopup: Object,
      getAllListings,
      categoryArray: [],
      destinationsArray: [],
      articleArray: [],
      statusArray: [],
      username: ''
    }
  },

  components: {
    Searchfield,
    Alllistings,
    ListingPopup,
    Categories,
    FilterButton
  },

  methods: {
    triggerSearch (newSearchWord) {
      this.getAllListings(newSearchWord, this.destinationsArray, this.categoryArray, this.articleArray, this.statusArray).then(res => {
        return res
      })
        .then(data => {
          this.productsSearchData = data.allProducts
          this.servicesSearchData = data.allServices
          this.username = data.username
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
      console.log(value)
      console.log(checked)

      if (type === 'destination') {
        this.changeFiltering(checked, this.destinationsArray, value)
      } else if (type === 'category') {
        this.changeFiltering(checked, this.categoryArray, value)
      } else if (type === 'article') {
        this.changeFiltering(checked, this.articleArray, value)
      } else if (type === 'status') {
        this.changeFiltering(checked, this.statusArray, value)
      }
    },
    changeFiltering (checked, specificArray, value) {
      if (!checked) {
        console.log('ADDED')
        specificArray.push(value)
      } else {
        console.log('removed')
        specificArray.splice(specificArray.indexOf(value), 1)
      }
    },
    triggerFilter () {
      this.filterActive = !this.filterActive
    },
    onResize () {
      if (window.innerWidth <= 860) {
        this.filterButtonActive = true
        this.filterActive = false
      } else {
        this.filterButtonActive = false
        this.filterActive = true
      }
    },
    placeInCart (amount, listingObj) {
      const JSONdata = new FormData()
      const cartItem = {
        title: listingObj.title,
        coverImg: listingObj.coverImg,
        price: listingObj.price,
        quantity: amount, // number of items
        article: listingObj.article, // produkt eller tjänst
        id: listingObj.id, // Id for the article
        status: listingObj.status, // köpes eller säljes
        userUploader: listingObj.userUploader, // user who uploaded the article, use to see if article is still for sale
        'end-date': listingObj['end-date'] // end date for the article
      }
      JSONdata.append('cartItem', JSON.stringify(cartItem))

      fetch(EXPRESS_URL + '/cart', { // POST endpoint
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify(cartItem) // This is your file object
      }).then(
        response => response
      ).then(
        success => {
          console.log(success)
          this.popupActive = false
        } // Handle the success response object
      ).catch(
        error => console.log(error) // Handle the error response object
      )
    }
  },
  
  created: function () {
    this.triggerSearch('')
    window.addEventListener('resize', this.onResize)
    this.onResize()
  },
  unmounted () {
    window.removeEventListener('resize', this.onResize)
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

@media screen and (max-width: 860px) {
  .main {
    display: flex;
    flex-wrap: wrap;
    flex-direction: row;
  }

  .categories {
    flex-basis: 100%;
  }
}
</style>
