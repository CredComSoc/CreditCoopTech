<!--
This page may cause weird errors in the console if the variable-names from listingObj 
do not match the equivalent of the database. 
-->
<template>
  <div>
    <router-link :to="{ name: 'MemberUserprofile', params: { userprofile: listingObj.accountName }} ">
    <div class="element-container" @click="enterProfile"> 
      <div class="imgContainer">
        <img :src='getImgURL()' />
      </div>
      <h4 class="element-title"> {{ listingObj.accountName }} </h4>
    </div>
    </router-link>
  </div>
</template>

<script>
import userProfileVue from './userProfile.vue'
import { EXPRESS_URL } from '../../serverFetch'

export default {

  props: {
    listingObj: Object
  },
  methods: {
    getImgURL () {
      /** http://155.4.159.231:3000/image/' + this.listingObj.logo */
      return EXPRESS_URL + '/image/' + this.listingObj.logo
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

    .imgContainer {
      height: 3rem;
      width: 3rem;
    }

    img {
      object-fit: cover;
      width: 100%;
      height: 100%;
    }
    h4 {
      margin-left: 0.5rem;
      align-self: flex-end;
    }

    a { 
      color: inherit; 
    } 

</style>
