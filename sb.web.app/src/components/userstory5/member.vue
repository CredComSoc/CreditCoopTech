<template>
  <div>
    <div class="element-container">
      <!--<router-link :to="{name:'MemberUserprofile', params:{tab: 'profile'}}">-->
        <img :src='getImgURL()' />
        <h4 class="element-title"> {{ listingObj.accountname }} </h4>
      <!--</router-link>-->
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
        font-weight: 500;
        font-size: 12px;
    }

    .element-container {
        display:flex;
        width: 100%;
        height:auto;
        background: #FFFFFF;
        outline: 0.1rem solid grey;
        /* margin: 1rem;*/
    }
    
     .element-container h4 {
        /* margin-top: 4px;*/
        width: 100%; 
        float: right;

        font-weight: bold;
     }

    .element-title {
        font-size: 20px;
        text-align: left;
    }
    img {
      height: 3rem;
      width: 3rem;
    }
    h4 {
      margin-left: 0.5rem;
      align-self: flex-end;
    }

</style>
