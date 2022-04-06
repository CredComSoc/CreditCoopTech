<template>
  <div>
    <div class="element-container">
        <img :src='getImgURL()' />
        <h4 class="element-title"> {{ listingObj.accountname }} </h4>
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
    getImgURL () {
      return 'http://155.4.159.231:3000/image/' + this.listingObj.img
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
        font-size: 20px;
        text-align: center;
        vertical-align: middle;
    }

</style>
