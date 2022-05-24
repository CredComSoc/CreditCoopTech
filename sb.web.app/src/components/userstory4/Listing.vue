<template>
  <div>
      <div v-if="listingObj" class="element-container" @click="togglePopup" type="button">
          <img :src='getImgURL()' style="object-fit:contain;max-width:240px;max-height:240px;"/>
          <h4 class="element-title"> {{ listingObj.title }} </h4>
          <p class="element-desc"> {{ listingObj.shortDesc }}  </p>
      </div>
      <div v-if="listingId && listingId !== '0'" class="element-container">
          <img :src='getImgURL()' />
          <h4 class="element-title"> {{ newListingObj.title }} </h4>
          <p class="element-desc"> {{ newListingObj.shortDesc }}  </p>
      </div>
      <div v-if="listingId && listingId === '0'" class="element-container">
          <img src='../../assets/sb2.png' />
          <h4 class="element-title"> Överföring </h4>
          <p class="element-desc"> {{ comment }}  </p>
      </div>
    </div>
    
</template>

<script>

// import ListingPopup from '@/components/userstory4/ListingPopup.vue'
import { EXPRESS_URL, getArticleWithId } from '../../serverFetch'

export default {
  data () {
    return {
      newListingObj: Object
    }
  },
  mounted () {
    if (this.listingId !== '0') {
      getArticleWithId(this.listingId)
        .then(res => {
          this.newListingObj = res
        })
    } 
  },
  props: {
    listingObj: Object,
    listingId: String,
    comment: String
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
        height:265px;
        background: #FFFFFF;
        box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
        margin: 1rem;
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
    }

    .element-desc {
        color: grey;
        margin-left: 9px;
        margin-right: 30px;
    }

    img {
      object-fit: cover;
      width: 100%;
      height: 60%;
    }

</style>
