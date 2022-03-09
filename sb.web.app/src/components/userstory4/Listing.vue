<template>
  <div>
    <!-- <ListingPopup v-if="true"/> -->
    <div class="element-container" @click="togglePopup">
        <img :src='getImgURL()' />
        <h4 class="element-title"> {{ listingObj.title }} </h4>
        <p class="element-desc"> {{ listingObj.shortDesc }}  </p>
    </div>
  </div>
</template>
<script>

// import ListingPopup from '@/components/userstory4/ListingPopup.vue'

export default {

  props: {
    listingObj: Object
  },
  methods: {
    togglePopup () {
      this.$emit('togglePopupEvent', this.listingObj)
    },
    getImgURL () {
      return 'http://localhost:3000/image/' + this.listingObj.img
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

</style>
