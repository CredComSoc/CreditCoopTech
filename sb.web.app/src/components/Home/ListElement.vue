<template>
  <div>
    <a href="Javascript:void(0)" @click="openPopup">
      <span v-if="elementInfo.title" class="element-container">
          <img v-if="elementInfo.coverImg.length === 0" class="regular" :src="require(`../../assets/list_images/event.png`)" style="object-fit:contain;max-width:240px;max-height:240px;">
          <img v-else class="regular" :src="getArticleImgURL()" style="object-fit:contain;max-width:240px;max-height:240px;">
          <h4 class="element-title"> {{ elementInfo.title }} </h4>
          <!-- TODO: Remove the fist parameter of the below two if statement oly checking offer and want only -->
          <p v-if="elementInfo.status === 'selling' || elementInfo.status === 'offer' " class="element-desc"> {{ $t('offer') }} </p>
          <p v-if="elementInfo.status === 'buying' || elementInfo.status === 'want'" class="element-desc">  {{ $t('want') }} </p>
      </span>
    </a>
    <router-link  v-if="elementInfo.accountName" :to="{ name: 'MemberUserprofile', params: { userprofile: elementInfo.accountName }} ">
      <div v-if="elementInfo.accountName" class="ellipse-container">
            <img v-if="elementInfo.logo.length === 0" class="ellipse" :src="require(`../../assets/list_images/user.png`)" style="object-fit:contain;max-width:240px;max-height:240px;">
            <img v-else class="ellipse" :src="getUserImgURL()" style="object-fit:contain;max-width:240px;max-height:240px;">
            <h4 class="element-title"> {{ formatTitle(elementInfo.accountName) }} </h4>
      </div>
    </router-link>
  </div>
  <ListingPopup v-if="this.displayPopup" @closePopup="closePopup" @placeInCart="this.placeInCart" :listingObj="elementInfo" :username="''"></ListingPopup>
</template>

<script>

import { EXPRESS_URL, setStoreData, setCartData } from '../../serverFetch'
import ListingPopup from '../SharedComponents/ListingPopup.vue'

export default {
  name: 'ListElement',
  props: ['elementInfo'],
  data () {
    return {
      displayPopup: false
    }
  },
  methods: {
    openPopup () {
      this.displayPopup = true
    },
    closePopup () {
      this.displayPopup = false
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
        setTimeout(() => {
          setCartData()
        })
      ).catch(
        error => console.log(error) // Handle the error response object
      )
    },
    // Set limits for number of chars depending on Upper or lower case for the description in list element
    formatText (str) {
      if (str.length > 35) {
        if (str.substring(0, 35).replace(/[a-z]/g, '').length > 20) {
          return str.substring(0, 30) + '...'
        } else {
          return str.substring(0, 35) + '...'
        }
      } else {
        if (str.length > 30) {
          if (str.replace(/[a-z]/g, '').length > 25) {
            return str.substring(0, 30) + '...'
          } else {
            return str
          }
        } else {
          return str
        }
      }
    },
    getArticleImgURL () {
      return EXPRESS_URL + '/image/' + this.elementInfo.coverImg
    },
    getUserImgURL () {
      return EXPRESS_URL + '/image/' + this.elementInfo.logo
    },
    // Set limits for number of chars depending on Upper or lower case for the title in list element
    formatTitle (str) {
      if (str.length > 20) {
        if (str.substring(0, 20).replace(/[a-z]/g, '').length > 10) {
          return str.substring(0, 15) + '...'
        } else {
          return str.substring(0, 20) + '...'
        }
      } else {
        if (str.length >= 15) {
          if (str.replace(/[a-z]/g, '').length > 10) {
            return str.substring(0, 15) + '...'
          } else {
            return str
          }
        } else {
          return str
        }
      }
    }
  },
  components: { ListingPopup }
}
</script>

<style scoped>
    * {
        font-weight: 500;
        font-size: 11px;
    }

    .regular {
        width: 100%;
        height: 70%;
    }

    .ellipse {
        width: 100%;
        height: 70%;
        margin-top: 20px;
    }

    a {
      text-decoration: none;
      color: black;
    }

    a:hover {
      color: black;
    }

    .element-container {
        display: block;
        width: 160px;
        height: 160px;
        background: #FFFFFF;
        box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
        word-break: break-all;
        text-align: center;
    }

    .element-container:hover { 
      box-shadow: 0px 8px 8px rgba(0, 0, 0, 0.25);
    }

    .element-container h4 {
        margin-top: 4px;
        font-weight: bold;
        font-size: 14px;
    }

    .element-title {
        margin-left: 8px;
    }

    .element-desc {
        color: grey;
        font-size: 12px;
    }

    .ellipse-container {
        display: block;
        width: 160px;
        height: 160px;
        background: #FFFFFF;
        box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
        word-break: break-all;
        text-align: center;
    }

    .ellipse-container:hover { 
      box-shadow: 0px 8px 8px rgba(0, 0, 0, 0.25);
    }

    .ellipse-container h4 {
        margin-top: 4px;
        font-weight: bold;
        font-size: 14px;
    }

    a.cover-link {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        text-decoration: none;
    }

</style>
