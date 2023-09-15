<template>
  <div class="wrapper">

    <div>
      <h2 class="center-text">{{ $t('marketplace') }}</h2>
    </div>

    <div class="center" id="searchfield">
        <Searchfield @searchEvent="triggerSearch" :place-holder-message="$t('shop.whatDoYouWantToSearchForTodayLabel')" />
    </div>

    <div class="main">

      <!--
    <div class="filterButton">
      <FilterButton v-if="filterButtonActive" @filterTrigger="triggerFilter" />
    </div> -->

      <!-- {{ $t('shop.column_categories') }}
      <div id="categories" class="categories">
        <Categories v-if="filterActive" @filterEvent="filteringMethod"/>
      </div> -->

      <!-- {{ $t('shop.column_product') }} -->
      <div class="listings">
        <div v-if="this.sellingSearchData.length !== 0">
          <h3 >{{ $t('Offers') }}</h3>
          <Alllistings @togglePopupEvent="openPopUp" :key=sellingSearchData :search-data=sellingSearchData />
        </div>
        <div v-if="this.buyingSearchData.length !== 0">
          <h3>{{ $t('Wants') }}</h3>
          <Alllistings @togglePopupEvent="openPopUp" :key=buyingSearchData :search-data=buyingSearchData />
        </div>
        <h3 v-if="this.buyingSearchData.length === 0 && this.sellingSearchData.length === 0">{{ $t('shop.no_product_found', {searchWord: this.searchWord}) }}</h3>
        <ListingPopup @closePopup="closePopup" @placeInCart="this.placeInCart" v-if="popupActive" :key="popupActive" :listing-obj=listingObjPopup :username="this.username" />
        <PopupCard v-if="this.putInCart" @closePopup="this.closePopup" :title="$t('success')" btnText="Ok" :cardText="$t('shop.item_added')" />
      </div>
    </div>

  </div>
</template>

<script>
import Searchfield from '@/components/SharedComponents/searchfield.vue'
import Alllistings from '@/components/Shop/all_listings.vue'
import ListingPopup from '@/components/SharedComponents/ListingPopup.vue'
import Categories from '@/components/Shop/Categories.vue'
import FilterButton from '@/components/Shop/filterButton.vue'
import PopupCard from '@/components/SharedComponents/PopupCard.vue'
import { authenticate, checkAdminStatus, EXPRESS_URL, setStoreData, setCartData } from './../../serverFetch.js'

export default {

  data () {
    return {
      productsSearchData: [],
      servicesSearchData: [],
      buyingSearchData: [],
      sellingSearchData: [],
      singleListingData: [],
      popupActive: false,
      filterActive: false,
      filterButtonActive: false,
      listingObjPopup: Object,
      categoryArray: [],
      destinationsArray: [],
      articleArray: [],
      statusArray: [],
      username: '',
      enableSearch: true,
      putInCart: false
    }
  },

  components: { // disabled components: Categories,FilterButton
    Searchfield,
    Alllistings,
    ListingPopup,
    PopupCard
  },

  methods: {
    triggerSearch (searchWord) {
      if (this.enableSearch) {
        this.productsSearchData = []
        this.servicesSearchData = []
        this.buyingSearchData = []
        this.sellingSearchData = []
        this.searchWord = searchWord

        this.enableSearch = false
        const otherItems = this.$store.state.allArticles.filter(it => it.userUploader !== this.$store.state.user.profile.accountName)
        for (const article of otherItems) {
          const now = new Date()
          const chosenDate = new Date(article['end-date'])
          if (now.getTime() > chosenDate.getTime()) {
            continue
          }

          let foundSearchword = true
          if (searchWord.length !== 0) {
            if (!article.title.match(new RegExp(searchWord, 'i')) &&
                !article.longDesc.match(new RegExp(searchWord, 'i')) &&
                !article.userUploader.match(new RegExp(searchWord, 'i'))
            ) {
              foundSearchword = false
              continue
            }
          }

          // IMPLEMENT FILTERING HERE

          if (article.article === 'product') {
            this.productsSearchData.push(article)
          } else if (article.article === 'service') {
            this.servicesSearchData.push(article)
          }

          if (article.status === 'buying' || article.status === 'want') {
            this.buyingSearchData.push(article)
          } else if (article.status === 'selling' || article.status === 'offer') {
            this.sellingSearchData.push(article)
          }
        }

        this.enableSearch = true
      }
    },
    openPopUp (listingObj) {
      this.popupActive = true
      this.listingObjPopup = listingObj
    },
    closePopup (listingObj) {
      this.popupActive = false
      this.putInCart = false
    },

    // The below method is not being called any where

    // filteringMethod (checked, type, value) {
    //   console.log(value)
    //   console.log(checked)

    //   if (type === 'destination') {
    //     this.changeFiltering(checked, this.destinationsArray, value)
    //   } else if (type === 'category') {
    //     this.changeFiltering(checked, this.categoryArray, value)
    //   } else if (type === 'article') {
    //     this.changeFiltering(checked, this.articleArray, value)
    //   } else if (type === 'status') {
    //     this.changeFiltering(checked, this.statusArray, value)
    //   }
    // },
    changeFiltering (checked, specificArray, value) {
      if (!checked) {
        //console.log('ADDED')
        specificArray.push(value)
      } else {
        //console.log('removed')
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
        article: listingObj.article, // product or service
        id: listingObj.id, // Id for the article
        status: listingObj.status, // is for sale
        userUploader: listingObj.userUploader, // user who uploaded the article, use to see if article is still for sale
        'end-date': listingObj['end-date'] // end date for the article
      }
      JSONdata.append('cartItem', JSON.stringify(cartItem))

      this.popupActive = false
      this.putInCart = true

      fetch(EXPRESS_URL + '/cart', { // POST endpoint
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify(cartItem) // This is your file object
      }).then(
        response => response,
        // TODO: get the cart data endpoint only and replace it with the whole data endpoint
        setTimeout(() => {
          setCartData()
        })
      ).then(
        success => {
          //console.log(success)
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
  mounted () {
    authenticate().then((res) => {
      if (res) {    
        checkAdminStatus().then((res2) => {
          this.auth = res
          this.admin = res2
        })
      } 
    })
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
  text-align: center;
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
  flex-basis: 100%;
  width: auto;
  margin-top: 1rem;
}

.center-text {
  text-align: center;
  margin-top: 4rem;
  margin-bottom: 4rem;
  font-size: 2.2rem;
  letter-spacing: 0.3em;
  text-transform: uppercase;
}

h2 {
  margin-top: 2rem;
  font-size: 3rem;
}

h3 {
  margin-left: 1rem;
  margin-top: 2rem;
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
