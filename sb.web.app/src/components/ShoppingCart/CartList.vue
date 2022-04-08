<template>
  <div id="cart-list-container">
      <div id="cart-list">
          <CartTable 
          :index="cart.length"
          :image="cart.map((obj) => obj.image)"
          :numberOf="cart.map((obj) => obj.items)"
          :price="cart.map((obj) => obj.price)"
          :sum="cart.map((obj) => obj.price * obj.items)"
          :title="cart.map((obj) => obj.title)"
          />
      </div>
  </div>
</template>

<script>
import CartTable from './CartTable.vue'
export default {
  name: 'CartList',
  components: {
    CartTable
  },
  props: ['cart']
}
</script>

<style scoped>
* {
  font-family: 'Ubuntu' ;
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

#cart-list-container {
    width: 100%;
    border: 1px solid black;
    min-height: 400px;
}

#cart-list {
    width: 95%;
    min-height: 400px;
}

</style>
