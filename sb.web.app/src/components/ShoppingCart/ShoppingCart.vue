<template>
  <div id="cart-container">
    <h1> Varukorg </h1>
    <EmptyCart v-if="this.gotCartRes && this.cart.length === 0" />
    <FilledCart v-if="this.gotCartRes && this.cart.length > 0" :total="this.total" :cart="this.cart" @remove-row="this.removeRow"  @add-item="this.addItem" @min-item="this.minItem" @complete-purchase="this.completePurchase"/>
    <PopupCard v-if="this.confirmPress" title="Tack för ditt köp" btnLink="/" btnText="Ok" :cardText="`Tack för ditt köp! Säljaren har meddelats. Du kommer få en\nnotis när säljaren bekräftat din köpförfrågan.`" />
  </div>
</template>

<script>
import EmptyCart from './EmptyCart.vue'
import FilledCart from './FilledCart.vue'
import PopupCard from '../CreateArticle/PopupCard.vue'
import { EXPRESS_URL, getCart } from '../../serverFetch'
export default {
  name: 'ShoppingCart',
  props: [],
  components: {
    EmptyCart,
    FilledCart,
    PopupCard
  },
  mounted () {
    getCart().then((res) => { 
      if (res) {
        this.cart = res
        // this.cart[0].quantity = 2
        // let first = {}
        // let sec = {}
        // let third = {}
        // first = Object.assign(first, this.cart[0])
        // first.title = 'Test1'
        // sec = Object.assign(sec, this.cart[0])
        // sec.title = 'Test2'
        // third = Object.assign(third, this.cart[0])
        // third.title = 'Test3'
        // const ma = [first, sec, third]
        // this.cart = ma
        this.calcTotal()
      }
      this.gotCartRes = true
    })
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
      fetch(EXPRESS_URL + '/cart/remove/item' + this.cart[ind - 1].id, {
        method: 'POST',
        credentials: 'include'
      }).then(
        success => {
          console.log(success)
          this.cart.splice(ind - 1, 1)

          this.calcTotal()
        }
      ).catch(
        error => console.log(error)
      )
    },
    addItem (ind) {
      this.cart[ind - 1].quantity++
      this.calcTotal()
    },
    minItem (ind) {
      if (this.cart[ind - 1].quantity > 1) {
        this.cart[ind - 1].quantity--
        this.calcTotal()
      }
    },
    calcTotal () {
      let total = 0
      for (let i = 0; i < this.cart.length; i++) {
        total += this.cart[i].price * this.cart[i].quantity
      }
      this.total = total
    },
    completePurchase () {
      this.confirmPress = true
      this.cart = []
      
      // remove all items from cart
      fetch(EXPRESS_URL + '/cart/remove', {
        method: 'POST',
        credentials: 'include'
      }).then(
        success => {
          console.log(success)
        }
      ).catch(
        error => console.log(error)
      )
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

  @media (max-width: 860px) {
      #cart-container{
          width: 70%;
      }
 }

  @media (max-width: 500px) {
      #cart-container{
          width: 80%;
      }
 }

 @media (max-width: 370px) {
      #cart-container{
          width: 95%;
      }
 }

</style>
