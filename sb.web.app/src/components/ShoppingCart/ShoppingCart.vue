<template>
  <div id="cart-container">
    <h1> Varukorg </h1>
    <EmptyCart v-if="this.gotCartRes && this.cart.length === 0" />
    <FilledCart :total="this.total" :cart="this.cart" v-if="this.gotCartRes && this.cart.length > 0" @remove-row="this.removeRow"  @add-item="this.addItem" @min-item="this.minItem" @complete-purchase="this.completePurchase"/>
    <PopupCard v-if="this.confirmPress" title="Tack för ditt köp" btnLink="/" btnText="Ok" :cardText="`Tack för ditt köp! Säljaren har meddelats. Du kommer få en\nnotis när säljaren bekräftat din köpförfrågan.`" />
  </div>
</template>

<script>
import EmptyCart from './EmptyCart.vue'
import FilledCart from './FilledCart.vue'
import PopupCard from '../CreateArticle/PopupCard.vue'
export default {
  name: 'ShoppingCart',
  props: [],
  components: {
    EmptyCart,
    FilledCart,
    PopupCard
  },
  mounted () {
    fetch('http://localhost:3000/cart/TestUser', { // Get endpoint
      method: 'GET',
      headers: {
        'Content-Type': 'application/json'
      }
    }).then(
      response => response.json()
    ).then(
      success => {
        this.cart = success
        this.cart[0].items = 2
        let first = {}
        let sec = {}
        let third = {}
        first = Object.assign(first, this.cart[0])
        sec = Object.assign(sec, this.cart[0])
        third = Object.assign(third, this.cart[0])
        const ma = [first, sec, third]
        this.cart = ma
        this.calcTotal()
        console.log(success)
        this.gotCartRes = true
      } // Handle the success response object
    ).catch(
      error => {
        console.log(error)
        this.gotCartRes = true
      } // Handle the error response object
    )
  },
  data () {
    return {
      cart: [],
      total: 0,
      gotCartRes: false,
      confirmPress: false
    }
  },
  methods: {
    removeRow (ind) {
      this.cart.splice(ind - 1, 1)
      this.calcTotal()
    },
    addItem (ind) {
      this.cart[ind - 1].items++
      console.log('CART:', this.cart)
      this.calcTotal()
    },
    minItem (ind) {
      if (this.cart[ind - 1].items > 1) {
        this.cart[ind - 1].items--
        this.calcTotal()
      }
    },
    calcTotal () {
      let total = 0
      for (let i = 0; i < this.cart.length; i++) {
        total += this.cart[i].price * this.cart[i].items
      }
      this.total = total
    },
    completePurchase () {
      this.confirmPress = true
    }
  }
}
</script>

<style scoped>

  *{
    font-family: 'Ubuntu' ;
    box-sizing: border-box;
    margin: 0 auto;
  }

  #cart-container{
    margin-top: 75px;
    width: 60%;
    min-height: 200px;
    position: relative;
  }

  @media (max-width: 1450px) {
      #cart-container{
          width: 70%;
      }
 }

 @media (max-width: 1300px) {
      #cart-container{
          width: 80%;
      }
 }

 @media (max-width: 1200px) {
      #cart-container{
          width: 90%;
      }
 }

 @media (max-width: 1000px) {
      #cart-container{
          width: 100%;
      }
 }

</style>
