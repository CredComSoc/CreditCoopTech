<template>
  <div id="cart-container">
    <h1> Varukorg </h1>
    <EmptyCart v-if="this.cart.length === 0" />
    <CartList v-if="this.cart.length > 0" />
  </div>
</template>

<script>
import EmptyCart from './EmptyCart.vue'
import CartList from './CartList.vue'

export default {
  name: 'ShoppingCart',
  props: [],
  components: {
    EmptyCart,
    CartList
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
        console.log(success)
        this.cart = success
      } // Handle the success response object
    ).catch(
      error => console.log(error) // Handle the error response object
    )
  },
  data () {
    return {
      cart: []
    }
  }
}
</script>

<style scoped>
  *{
    font-family: 'Ubuntu' ;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  p{
    font-style: italic;
  }

</style>
