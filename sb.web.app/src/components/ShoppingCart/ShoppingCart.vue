<template>
  <div id="cart-container">
    <h1> Varukorg </h1>
    <EmptyCart v-if="this.cart.length === 0" />
    <FilledCart :cart="this.cart" v-if="this.cart.length > 0" @remove-row="this.removeRow"/>
  </div>
</template>

<script>
import EmptyCart from './EmptyCart.vue'
import FilledCart from './FilledCart.vue'
export default {
  name: 'ShoppingCart',
  props: [],
  components: {
    EmptyCart,
    FilledCart
  },
  mounted () {
    fetch('http://localhost:3000/cart/TestUser1', { // Get endpoint
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
        this.cart = Array(3).fill(this.cart[0])
        console.log(success)
      } // Handle the success response object
    ).catch(
      error => console.log(error) // Handle the error response object
    )
  },
  data () {
    return {
      cart: []
    }
  },
  methods: {
    removeRow (ind) {
      console.log('hello')
      this.cart.splice(ind - 1, 1)
      //this.total = this.cart.reduce((acc, cur) => acc + cur.price * cur.items, 0)
    }
  }
}
</script>

<style scoped>

  *{
    font-family: 'Ubuntu' ;
    box-sizing: border-box;
    margin: 0 auto;
    margin-top: 30px;
  }

  #cart-container{
    width: 45%;
    min-height: 200px;
    position: relative;
  }

</style>
