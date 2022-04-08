<template>
  <div id="cart-list-container">
      <div id="cart-list">
          <VerticalList 
          v-for="(item) in this.cart"
          :elementInfo="item"
          :key="item.id" 
          />
      </div>
  </div>
</template>

<script>
import VerticalList from './VerticalList.vue'
export default {
  name: 'CartList',
  components: {
    VerticalList
  }
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
}

#cart-list {
    width: 95%;
}

</style>
