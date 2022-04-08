<template>
  <h1> Varukorg </h1>
  <EmptyCart />
</template>

<script>
import EmptyCart from './EmptyCart.vue'
export default {
  name: 'ShoppingCart',
  props: [],
  components: {
    EmptyCart
  },
  mounted () {
    fetch('http://localhost:3000/cart/' + 'TestUser1')
      .then(response => {
        if (!response.ok) {
          throw new Error('HTTP error ' + response.status)
        }
        return response.json()
      })
      .then(json => {
        this.cart = json
        console.log(this.cart)
      })
      .catch(error => {
        console.log(error)
      })
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
