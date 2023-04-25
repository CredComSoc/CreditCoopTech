<template>
  <div>
      <div v-if="listingObj" class="element-container" @click="togglePopup" type="button">
          <img :src='getImgURL()' style="object-fit:contain;max-width:240px;max-height:140px;"/>
          <h4 class="element-title"> {{ listingObj.title }} </h4>
          <p class="element-desc"> {{ formatDesc(listingObj.longDesc)}}  </p>
      </div>
      <div v-if="listingId && listingId === '0'" class="element-container">
          <img src='../../assets/icons/transaction.png' style="object-fit:contain;max-width:240px;max-height:140px;"/>
          <h4 class="element-title"> Överföring</h4>
          <p class="element-desc"> {{ comment }}  </p>
      </div>
    </div>
    
</template>

<script>

// import ListingPopup from '@/components/Shop/ListingPopup.vue'
import { EXPRESS_URL, getArticleWithId } from '../../serverFetch'

export default {
  data () {
    return {
      newListingObj: Object
    }
  },
  mounted () {
    if (this.listingId && this.listingId !== '0') {
      getArticleWithId(this.listingId)
        .then(res => {
          this.newListingObj = res
        })
    } 
  },
  props: {
    listingObj: Object,
    listingId: String,
    comment: String,
    small: Boolean,
    hideSeller: Boolean
  },
  methods: {
    togglePopup () {
      this.$emit('togglePopupEvent', this.listingObj)
    },
    getImgURL () {
      if (this.listingId) {
        return EXPRESS_URL + '/image/' + this.newListingObj.coverImg
      } else {
        return EXPRESS_URL + '/image/' + this.listingObj.coverImg
      }
    },
    // Set limits for number of chars depending on Upper or lower case for the title in list element
    formatDesc (str) {
      if (str.length >= 45) {
        if (str.replace(/[a-z]/g, '').length > 10) {
          return str.substring(0, 45) + '...'
        } else {
          return str
        }
      } else {
        return str
      }
    }
  }
}
</script>

<style scoped>
 * {
        font-family: Ubuntu;
        font-style: normal;
        font-weight: normal;
        letter-spacing: 0.05em;
        padding: 0;
        margin:0;
        font-weight: 500;
        font-size: 12px;
    }

    .element-container {
        width: 200px;
        height: 230px;
        background: #FFFFFF;
        box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
        margin: 1rem;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .element-container:hover {
        box-shadow: 0px 8px 8px rgba(0, 0, 0, 0.35);
    }

     .element-container h4 {
        margin-top: 4px;
        font-weight: bold;
     }

    .element-title {
      margin-left: 8px;
      font-size: 20px
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .element-seller {
      color: rgba(0, 0, 0, 0.65);
      font-size: 14px;
      margin-left: 8px;
    }

    .element-desc {
      color: grey;
      font-size: 14px;
      min-height: 40px;
    }

    img {
      object-fit: cover;
      width: 100%;
      height: 60%;
    }

</style>
