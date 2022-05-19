<template>
<div>
  <div @click="$emit('closePopup')" class="popup">
    
  </div>
  <div class="popup-inner">
    <img class="p-image" :src='getImgURL()' alt="Coffea">
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
        <div>
          <b-button class="decreaseBtn" @click="decreaseAmount">-</b-button>
          <p class="amountText"> {{amount}} </p>
          <b-button class="increaseBtn" @click="amount++">+</b-button>
        </div>
        <div>
          <button class="cartBtn" @click="placeInCart">Lägg i varukorg</button>
        </div>
      </div>
      <div class="interactContent" v-if="listingObj.status === 'buying'">
        <div>
          <button class="chattBtn" @click="goToChat">Starta chatt</button>
        </div>
      </div>
    </div>
  </div>
</div>
</template>
<script>
import { EXPRESS_URL, profile } from '../../serverFetch'
export default {

  props: {
    listingObj: Object  
  },

  data () {
    return {
      amount: 1
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
    }
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
  height: 70%;
  /*max-height: 70vh;*/
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
