<template>
  <div id="cart-container">
    <h1> Varukorg </h1>
    <EmptyCart v-if="this.cart.length === 0" />
    <CartList v-if="this.cart.length > 0" />
    <FilledCart :total="50"/>
  </div>
</template>

<script>
import EmptyCart from './EmptyCart.vue'
import CartList from './CartList.vue'
import FilledCart from './FilledCart.vue'

export default {
  name: 'ShoppingCart',
  props: [],
  components: {
    EmptyCart,
    CartList,
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
