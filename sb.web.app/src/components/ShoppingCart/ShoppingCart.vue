<template>
  <div id="Cart-container">
    <h1> Varukorg </h1>
    <!--<EmptyCart />-->
    <FilledCart/>
  </div>
</template>

<script>
//import EmptyCart from './EmptyCart.vue'
import FilledCart from './FilledCart.vue'
export default {
  name: 'ShoppingCart',
  props: [],
  components: {
    //EmptyCart,
    FilledCart
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
    box-sizing: border-box;
    margin: 0 auto;
    margin-top: 30px;
  }

  #Cart-container{
    width: 700px;
    min-height: 200px;
    position: relative;
  }
</style>
