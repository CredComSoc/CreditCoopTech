<template>
  <div>
    <h1><b> {{ $t('activeArticles')}} </b></h1>
    <div v-if="activeArticles.length===0">
      <p style="font-style: italic;"> {{ $t('youHaveNoActiveArticles')}} </p>
    </div>
    <div v-if="activeArticles.length!==0" style="max-height: 50em; overflow: scroll; padding-top: 20px; padding-bottom: 20px;">
      <table>
        <tr>
          <th>{{ $t('article')}}</th>
          <th>{{ $t('category')}}</th>
          <th>{{ $t('price') }}</th>
          <th>{{ $t('created')}}</th>
          <th>{{ $t('status')}}</th>
        </tr>
        <tr v-for="(item, index) in activeArticles" :key="item">
          <td>
            <Listing className='article' @togglePopupEvent='openPopUp' :listingObj="item" :hideSeller="true" />
            <ListingPopup @closePopup="closePopup" @placeInCart="this.placeInCart" v-if="popupActive" :key="popupActive" :listing-obj=listingObjPopup :username="this.username" />
          </td>
          <td>{{item.category}}</td>
          <td>{{item.price}}</td>
          <td>{{item.uploadDate}} </td>
          <td> 
            <div class="edit">
              <button v-if="(new Date(item['end-date'])).getTime() > Date.now()" @click="remove(item, index)" 
                style="background-color: red;"> {{ $t('remove')}}</button>
              <!-- <router-link :to="{name:'New_Article', params:{artID: item.id}}"> {{ $t('edit_ads') }} </router-link> -->
            </div> 
          </td> 
        </tr>
      </table>
    </div>
    <h1><b>{{ $t('inActiveArticles') }}</b></h1>
    <div v-if="inactiveArticles.length!==0" style="max-height: 50em; overflow: scroll; padding-top: 20px; padding-bottom: 20px;">
      <table>
        <tr>
          <th>{{ $t('article') }}</th>
          <th>{{ $t('category')}}</th>
          <th>{{ $t('price') }}</th>
          <th>{{ $t('created')}}</th>
        </tr>
        <tr v-for="(item) in inactiveArticles" :key="item">
          <td><Listing className='article' :listingObj="item"/></td>
          <td>{{item.category}}</td>
          <td>{{item.price}}</td> 
          <td>{{item.uploadDate}} </td> 
          <td>   
            <div class="edit">
              <!-- <router-link :to="{name:'New_Article', params:{artID: item.id}}"> {{ $t('edit_ads') }} </router-link> -->
            </div> 
          </td>
        </tr>
      </table>
    </div>
 </div>
</template>

<script>
import { getArticles, deactivateArticle } from '../../serverFetch'
import Listing from '@/components/SharedComponents/Listing.vue'
import ListingPopup from '@/components/SharedComponents/ListingPopup.vue'

export default {
  data () {
    return {
      articles: [], 
      activeArticles: [],
      inactiveArticles: [],
      date: new Date(),
      popupActive: false,
      listingObjPopup: Object,
      username: ''
    }
  },
  mounted () {
    getArticles().then(res => {
      this.activeArticles = []
      this.inactiveArticles = []
      for (const article of res.products) {
        console.log(article)
        if ((new Date(article['end-date'])).getTime() < Date.now()) {
          this.inactiveArticles.push(article)
        } else {
          this.activeArticles.push(article)
        }
      }
    })
  },
  components: {
    Listing,
    ListingPopup
  },
  methods: {
    remove (item, index) {
      deactivateArticle(item.id)
      getArticles().then(res => {
        this.activeArticles = []
        this.inactiveArticles = []
        for (const article of res.products) {
          if ((new Date(article['end-date'])).getTime() < Date.now()) {
            this.inactiveArticles.push(article)
          } else {
            this.activeArticles.push(article)
          }
        }
      })
    },
    openPopUp (listingObj) {
      this.popupActive = true
      this.listingObjPopup = listingObj
    },
    closePopup (listingObj) {
      this.popupActive = false
      //this.putInCart = false
    }
  }
}

</script>

<style scoped>

table {
  margin-left: auto;
  margin-right: auto;
  border-spacing:50px;
  width: 100%;
  text-align: center;
  font-size: 1.2rem;
}

h1 {
  padding: 10px 0px 10px 20px;
  font-size: 1.6rem;
}

p {
  padding: 10px 0px 10px 20px;
  font-size: 1.2rem;
}

th {
  padding: 20px;
  font-weight: normal;
}

td {
  padding: 10px 0px 10px 0px;
}

.article {
  align-content: center;
  display: flex;
  justify-content: center;
}

button {
  color: white;
  margin-right: 10px;
  border-radius: 5px;
  font-size: 1.2rem;
  padding: 2px 15px 2px 15px;
}

</style>
