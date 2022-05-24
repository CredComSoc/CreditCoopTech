<template>
<div>
  <div @click="$emit('closePopup')" class="popup">
    
  </div>
  <div class="popup-inner">
    <img class="p-image" :src='getImgURL()' alt="Coffea" style="object-fit:contain;max-width:240px;max-height:240px;">
    
    <div class="content-right">
      <div class="flex-center-bottom">
        <button className="xBtn" @click="$emit('closePopup')">x</button>
      </div>
      <div class="textContent">
        <h5>{{listingObj.title}}</h5>
        <p>{{listingObj.longDesc}}</p>
        <p>{{listingObj.destination}}</p>
        <p>{{listingObj.price}} Barter Kr</p>
      </div>
      <div class="interactContent" v-if="listingObj.status === 'selling'">
        <div v-if="this.username.toLowerCase() !== listingObj.userUploader.toLowerCase()">
          <b-button class="decreaseBtn" @click="decreaseAmount">-</b-button>
          <p class="amountText"> {{amount}} </p>
          <b-button class="increaseBtn" @click="amount++">+</b-button>
        </div>
        <div>
          <button v-if="this.username.toLowerCase() !== listingObj.userUploader.toLowerCase()" class="cartBtn" @click="placeInCart">Lägg i varukorg</button>
        </div>
      </div>
      <div class="interactContent" v-if="listingObj.status === 'buying'">
        <div>
          <button v-if="this.username.toLowerCase() !== listingObj.userUploader.toLowerCase()" class="chattBtn" @click="goToChat">Starta chatt</button>
        </div>
      </div>
    </div>
        <!-- KOLLA OM DET FINNS FLER BILDER ÄN COVER IMAGE INNAN FÖLJANDE KOD KÖRS -->
    <div class="p-image small-images-container">
      <!-- OM 1 eller fler bilder -->
      <img v-if="this.listingObj.img.length >= 1" class="small-images" :src='displayImage0' alt="img1" style="object-fit:contain;">
      <!-- OM 2 eller fler bilder -->
      <img v-if="this.listingObj.img.length >= 2" class="small-images" :src='displayImage1' alt="img2">
      <!-- OM 3 eller fler bilder -->
      <img v-if="this.listingObj.img.length >= 3" class="small-images" :src='displayImage2' alt="img3">
      
      <img v-if="this.listingObj.img.length >= 4" class="arrow" id="right-rotating-arrow" src="../../assets/list_images/right_arrow.png" alt="rotera shop" @click="rotateImages" />
    </div>

  </div>
</div>
</template>
<script>
import { EXPRESS_URL, profile } from '../../serverFetch'
export default {

  props: {
    listingObj: Object,
    username: String  
  },

  data () {
    return {
      amount: 1,
      displayImage0: String,
      displayImage1: String,
      displayImage2: String
    }
  },

  methods: {
    decreaseAmount () {
      if (this.amount > 1) {
        this.amount--
      }
    },
    increaseAmount () {
      if (this.amount < 99) {
        this.amount++
      }
    },
    getImgURL () {
      return EXPRESS_URL + '/image/' + this.listingObj.coverImg
    },
    placeInCart () {
      profile().then(res => {
        // if (res.name !== this.listingObj.userUploader) {
        this.$emit('placeInCart', this.amount, this.listingObj)
        // }
      })
    },
    goToChat () {
      fetch(EXPRESS_URL + '/chat/' + this.listingObj.userUploader, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include'
      }).then(res => res.json())
        .then(data => {
          if (data !== false) {
            this.$router.push({ name: 'Chat', params: { chatID: data } })
          } else {
            console.log('chat error!!')
            this.chatError = true
          }
        }).catch(err => console.log(err))
    },
    getSmallerImages (imgArray) {
      if (imgArray.length >= 1) {
        this.displayImage0 = EXPRESS_URL + '/image/' + imgArray[0]
      }
      if (imgArray.length >= 2) {
        this.displayImage1 = EXPRESS_URL + '/image/' + imgArray[1]
      }
      if (imgArray.length >= 3) {
        this.displayImage2 = EXPRESS_URL + '/image/' + imgArray[2]
      }
    },
    rotateImages () {
      const dataCopy = this.listingObj.img
      dataCopy.push(dataCopy.shift())
      // this.listingObj.img = dataCopy
      if (dataCopy.length >= 1) {
        this.displayImage0 = EXPRESS_URL + '/image/' + dataCopy[0]
      }
      if (dataCopy.length >= 2) {
        this.displayImage1 = EXPRESS_URL + '/image/' + dataCopy[1]
      }
      if (dataCopy.length >= 3) {
        this.displayImage2 = EXPRESS_URL + '/image/' + dataCopy[2]
      }
    }
  },
  created: function () {
    this.getSmallerImages(this.listingObj.img)
  }
}
</script>

<style scoped>

body {
  align-items: center;
  justify-content: center;
}

.decreaseBtn, .increaseBtn {
  display: inline-block;
}

.cartBtn, .chattBtn {
  display: inline-block;
  margin-bottom: 0.5rem;
}

.amountText {
  display: inline-block;
  margin-left: 1rem;
  margin-right: 1rem;
  margin-top: 1rem;
}

.popup {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 99;
  background-color: rgba(0, 0, 0, 0.05);
  display: flex;
  z-index: 2
}

.popup-inner {
  position: fixed; 
  background: #FFFFFF;
  border: 4px solid #C4C4C4;
  box-sizing: border-box;
  box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.1);
  display: flex;
  width: max(70%, 30rem);
  height: max(80%, 20rem);
  font-size-adjust: 0.58;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  padding:0;
  z-index: 5;
  font-size: 1.4rem;
}

h5 {
  font-size: 1.8rem;
}

.textContent {
  padding: 1rem;
}

.content-right {
  width:100%;
  display: flex;
  flex-direction: column;
  background-color: white;

}

.p-image {
  object-fit: cover;
  width: 70%;
  height: 100%;
  /*max-height: 70vh;*/
}

.small-images-container {
  /* box-sizing: border-box; */
  position: fixed; 
  bottom: 0;
  padding:0;
  display: flex;
  /* flex-direction: row; */
  justify-content: space-evenly;
  /* height: 150px; */

  height: 200px;
  vertical-align:middle;

  object-fit: cover;
  overflow: hidden;
}

.small-image {
  /*position: absolute;*/
  /**object-fit: cover;*/
  width: 20%;
  height: 20%;
  bottom:0;
  height:0px;
}

.small-images-container {
    display: none;
    visibility: hidden;
}

.arrow {
  margin-top: 6%;
  height:50%;
  
}

.xBtn {
  border: none;
  background-color: white;
      position: absolute;
    top: 0;
    right: 0;
}

.flex-center-bottom {
  align-items: center;
  display: flex;
  justify-content: flex-end;
  padding-right: 1rem;
  
}

.interactContent {
  
  position:absolute;
  text-align: right;
  bottom: 0;
  right: 1rem;
  /* width: 33%; */
}

.interactContent > * {
  width: 100%;
}

@media screen and (max-width: 860px) {
  .popup-inner {
    width: 80%;
    flex-wrap: wrap;
  }
  .content-right {
    width: 100%;
  }

  .p-image {
    width: 100%;
    height: 45%;
  }

}

</style>
