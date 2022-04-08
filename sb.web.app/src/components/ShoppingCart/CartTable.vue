<template>
  <div id="cart-table">
    <VerticalList :element="index" :title="null"/>
    <VerticalList :element="image" title="Produkt" :isImage="true"/>
    <VerticalList :element="title" />
    <VerticalList :element="numberOf" title="Antal" :isNumberOf="true" />
    <VerticalList :element="price" title="Pris"  />
    <VerticalList :element="sum" title="Summa" />
    <VerticalList :isGarCan="true" />
  </div>
</template>

<script>
import VerticalList from './VerticalList.vue'
export default {
  name: 'CartTable',
  components: {
    VerticalList
  },
  props: ['index', 'title', 'image', 'numberOf', 'price', 'sum']
}
</script>

<style scoped>
* {
  font-family: 'Ubuntu' ;
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

#cart-table {
  display: flex;
  flex-direction: row;
  justify-content: space-evenly;
  min-height:200px;
}

</style>
