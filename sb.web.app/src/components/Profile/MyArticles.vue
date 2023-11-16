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
          <td>{{item.price}} {{ $t('org.token') }}</td>
            <td>{{ new Date(item.uploadDate).toLocaleDateString() }} </td>
            <td> 
            <div class="edit">
              <button v-if="(new Date(item['end-date'])).getTime() > Date.now()" @click="remove(item, index)" 
                style="background-color: red;"> {{ $t('shop.remove')}}</button>
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
          <th></th>
        </tr>
        <tr v-for="(item) in inactiveArticles" :key="item">
          <td><Listing className='article' :listingObj="item"/></td>
          <td>{{item.category}}</td>
          <td>{{item.price}} {{ $t('org.token') }}</td> 
          <td>{{ new Date(item.uploadDate).toLocaleDateString() }} </td>
          <td>   
            <div class="edit">
              <button @click="activate(item, index)" 
                style="background-color: green;"> {{ $t('activate')}}</button>
              <!-- <router-link :to="{name:'New_Article', params:{artID: item.id}}"> {{ $t('edit_ads') }} </router-link> -->
            </div> 
          </td>
        </tr>
      </table>
    </div>
    <ConfirmDialogBox v-if="this.openConfirmDialogBox" class="confirm-dialog" :title = "this.removeItemText.title" :body= "this.removeItemText.body" @confirm="this.confirmDialogBox" @cancel="cancelDialogBox()" :values = "this.removedItem"/>
    <LoadingComponent ref="loadingComponent" />
</div> 
</template>

<script>
import { getArticles, deactivateArticle, activateArticle } from '../../serverFetch'
import Listing from '@/components/SharedComponents/Listing.vue'
import ListingPopup from '@/components/SharedComponents/ListingPopup.vue'
import ConfirmDialogBox from '../SharedComponents/ConfirmDialogBox.vue'
import LoadingComponent from '../SharedComponents/LoadingComponent.vue'

export default {
  data () {
    return {
      articles: [], 
      activeArticles: [],
      inactiveArticles: [],
      date: new Date(),
      popupActive: false,
      listingObjPopup: Object,
      username: '',
      openConfirmDialogBox: false,
      removedItem: {
        item: {},
        index: 0,
        status: false
      },
      removeItemText: {
        title: 'Remove',
        body: 'Are you sure you want to remove this item?'
      }
    }
  },
  mounted () {
    getArticles().then(res => {
      this.activeArticles = []
      this.inactiveArticles = []
      for (const article of res) {
        //console.log(article)
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
    ListingPopup,
    ConfirmDialogBox,
    LoadingComponent
  },
  methods: {
    remove (item, index) {
      this.removedItem.item = item
      this.removedItem.index = index
      this.removedItem.status = false
      this.removeItemText.title = this.$i18n.t('confirm_item_removal')
      this.removeItemText.body = this.$i18n.t('confirm_item_removal_text')
      this.openConfirmDialogBox = true
    },
    activate (item, index) {
      this.removedItem.item = item
      this.removedItem.index = index
      this.removedItem.status = true
      this.removeItemText.title = this.$i18n.t('confirm_item_activation')
      this.removeItemText.body = this.$i18n.t('confirm_item_activation_text')
      this.openConfirmDialogBox = true
    },
    openPopUp (listingObj) {
      this.popupActive = true
      this.listingObjPopup = listingObj
    },
    closePopup (listingObj) {
      this.popupActive = false
      //this.putInCart = false
    },
    cancelDialogBox () {
      this.openConfirmDialogBox = false
    },
    async confirmDialogBox (values) {
      this.openConfirmDialogBox = false
      this.$refs.loadingComponent.showLoading()
      if (this.removedItem.status) {
        await activateArticle(values.item.id)
      } else {
        await deactivateArticle(values.item.id)
      }
      await getArticles().then(res => {
        this.activeArticles = []
        this.inactiveArticles = []
        for (const article of res) {
          if ((new Date(article['end-date'])).getTime() < Date.now()) {
            this.inactiveArticles.push(article)
          } else {
            this.activeArticles.push(article)
          }
        }
      })
      this.$refs.loadingComponent.hideLoading()
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
.confirm-dialog {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

</style>
