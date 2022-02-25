<template>
  <b-container>
    <b-row>
      <div @click="$emit('closePopup')" class="popup"></div>
      <div class="popup-inner">
          <b-col cols="8">
            <img class="p-image" src="./images/bulles1.png" alt="Coffea">
          </b-col>
          <b-col cols="4" class="content-right">
            <b-row>
              <b-col>
                <div class="float-end">
                  <button className="xBtn marginRight" @click="$emit('closePopup')">x</button>
                </div>
              </b-col>
            </b-row>
            <b-row>
              <b-col class="d-flex align-items-center justify-content-center">
               <h5>{{listingObj.title}}</h5>
              </b-col>
            </b-row>
            <b-row class="marginLeft">
              <p>{{listingObj.longDesc}}</p>
            </b-row>
            <b-row class="marginLeft">
              <p>{{listingObj.destination}}</p>
            </b-row>
            <b-row class="marginLeft">
              <p>{{listingObj.price}}</p>
            </b-row>
            <b-row class="fixed-bottom">
              <b-col>
                <div class="float-end amountRow">
                  <b-button class="decreaseBtn" @click="decreaseAmount">-</b-button>
                  <p class="amountText"> {{amount}} </p>
                  <b-button class="increaseBtn marginRight" @click="amount++">+</b-button>
                </div>
              </b-col>
            </b-row>
            <b-row class="fixed-bottom">
              <b-col>
                <div class="float-end">
                  <b-button class="cartBtn marginRight" variant="primary" @click="emit('placeInCart', amount)">Lägg i varukorg</b-button>
                </div>
              </b-col>
            </b-row>
          </b-col>
        </div>
    </b-row>
  </b-container>
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

.amountRow {
  margin-bottom: 3rem;
}

.cartBtn {
  display: inline-block;
  margin-bottom: 0.5rem;
}

.lastRow {
  margin-top: auto;
}

.marginLeft {
  margin-left: 0.5rem;
  margin-right: 0.5rem;
}

.marginRight {
  margin-right: 0.5rem;
}

.amountText {
  display: inline-block;
  margin-left: 1rem;
  margin-right: 1rem;
  
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

/* .content-left {
  width: 100%;
  height: 100%;
} */

.content-right {
  background-color: white;
}

.p-image {
  height: 100%
}

.xBtn {
  border: none;
  background-color: white;
}

</style>
