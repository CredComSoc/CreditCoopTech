<!--
This page may cause weird errors in the console if the variable-names from listingObj 
do not match the equivalent of the database. 
-->
<template>
  <div>
    <div class="button" >
      <div class="element-container" @click="enterProfile"> 
        <div class="imgContainer">
          <img v-if="this.listingObj.logo !== ''" :src='getImgURL()' />
          <img v-if="this.listingObj.logo == ''" src='@/assets/list_images/user.png' />
        </div>
        <h4 class="element-title"> {{ listingObj.accountName }} </h4>
        <h5 class="element-title"> {{ listingObj.phone }} </h5>
        <button @click="userselected">Account</button>
      </div>
      <h5>Senast Online: {{ getOnlineStatus() }}</h5>
    </div>
<!--
  <router-link :to="{ name: 'MemberUserprofile', params: { userprofile: listingObj.accountName }} ">
    
    </router-link>
-->
  </div>
  
</template>

<script>
import { EXPRESS_URL } from '@/serverFetch'

export default {

  props: {
    listingObj: Object
  },
  methods: {
    getImgURL () {
      return EXPRESS_URL + '/image/' + this.listingObj.logo
    },
    getOnlineStatus () {
      if (this.listingObj.last_online) {
        const lastOnline = new Date(this.listingObj.last_online)
        if (Date.now() - lastOnline < 1000 * 60 * 3) { // 3 min
          return 'Nu'
        } else if (Date.now() - lastOnline < 1000 * 60 * 60 * new Date().getHours() + 1) { // today
          return 'Idag'
        } else if (Date.now() - lastOnline < 1000 * 60 * 60 * (new Date().getHours() + 25)) { // yday
          return 'Igår'
        } else {
          let days = ((Date.now() - lastOnline) / (1000 * 60 * 60 * 24)) + 1
          days = Math.floor(days * 10) / 10
          return days + ' dagar sedan'
        }
      } else {
        return 'Aldrig'
      } 
    },
    userselected () {
      this.$emit('openProfile', { name: this.listingObj.accountName })
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
        white-space: nowrap; 
        /* margin: 1rem;*/
    }
    
     .element-container h4 {
        /* margin-top: 4px;*/
        width: 100%; 
        font-weight: bold;
     }

    .element-title {
        font-size: 20px;
        text-align: left;
    }

    .element-text {
        font-size: 15px;
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

    h5 {
      margin-top: 5px;
      margin-bottom: 2px;
      margin-left: 3px;
      font-size: medium;
    }

    .button { 
      display:flex;
      flex-direction: column;
      color: inherit; 
      text-decoration: none;
      outline: 0.1rem solid grey;
      box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
    }
    
    .button:hover {
      color: black;
      box-shadow: 0px 8px 8px rgba(0, 0, 0, 0.25);
    }

    .button:hover h4{
      text-decoration: underline;
      text-decoration-thickness: 2px;
    }

</style>
