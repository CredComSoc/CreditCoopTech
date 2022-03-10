<template>
<div>
  <div @click="$emit('closePopup')" class="popup">
    
  </div>
  <div class="popup-inner">
    <img class="p-image" src="./images/bulles1.png" alt="Coffea">
    <div class="content-right">
      <div class="flex-center-bottom">
        <button className="xBtn" @click="$emit('closePopup')">x</button>
      </div>
      <div class="textContent">
        <h5>{{listingObj.title}}</h5>
        <p>{{listingObj.longDesc}}</p>
        <p>{{listingObj.destination}}</p>
        <p>{{listingObj.price}}</p>
      </div>
      <div class="flex-center-bottom align-to-bottom">
        <b-button class="decreaseBtn" @click="decreaseAmount">-</b-button>
        <p class="amountText"> {{amount}} </p>
        <b-button class="increaseBtn marginRight" @click="amount++">+</b-button>
      </div>
      <div class="flex-center-bottom bottom-placement">
        <button class="cartBtn" @click="emit('placeInCart', amount)">Lägg i varukorg</button>
      </div>
    </div>
  </div>
</div>
</template>
<script>
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

.cartBtn {
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
  width: 70rem;
  height: 50rem;
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
  display: flex;
  flex-direction: column;
  background-color: white;

}

.p-image {
  height: 100%
}

.xBtn {
  border: none;
  background-color: white;
}

.flex-center-bottom {
  align-items: center;
  display: flex;
  justify-content: flex-end;
  padding-right: 1rem;
  
}

.bottom-placement {
  align-self: flex-end;
  justify-content: flex-end;
}

.align-to-bottom {
  padding-top: 15rem;
}

</style>
