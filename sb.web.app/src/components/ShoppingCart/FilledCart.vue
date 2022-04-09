<template>
  <div id="cart-table">
    <!-- <CartTableHeader /> -->
    <div id="table-content">
      <CartTableRow 
        v-for="(row,index) in this.cart"
        :key="row.id"
        :ind="index + 1"
        :image="row.image"
        :title="row.title"
        :items="row.items"
        :price="row.price"
        :sum="row.price * row.items" 
      />
    </div>
  </div>
  <p>Totalt: {{ total }} bKr</p>
  <div type="button" id="confirm">Slutför köp</div>
</template>

<script>
//import CartTableHeader from './CartTableHeader.vue'
import CartTableRow from './CartTableRow.vue'

export default {
  name: 'FilledCart',
  components: {
    //CartTableHeader,
    CartTableRow
  },
  props: ['total', 'cart']
}
</script>

<style scoped>
  *{
    font-family: 'Ubuntu' ;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  #confirm{
      right: 0;
      bottom:0;
      position:absolute;
      background-color:#4690CD;
      color: white;
      padding: 5px 10px 5px 10px;
      border-radius: 4px;
  }

  #confirm:hover{
      background: #457EAD;
  }

  #cart-table {
    margin-top: 30px;
    border: solid 1px black;
  }

  #table-content {
    min-height: 200px;
  }

</style>
