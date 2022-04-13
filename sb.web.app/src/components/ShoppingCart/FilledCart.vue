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
        @remove-row="this.removeRow" 
        @add-item="this.addItem"
        @min-item="this.minItem"
      />
    </div>
  </div>
  <div id="bottom">
    <TableBottom @complete-purchase="this.completePurchase" :total="this.total" v-if="this.cart.length > 0"/>
  </div>
</template>

<script>
import CartTableRow from './CartTableRow.vue'
import TableBottom from './TableBottom.vue'

export default {
  name: 'FilledCart',
  components: {
    CartTableRow,
    TableBottom
  },
  props: ['cart', 'total'],
  emits: ['remove-row', 'add-item', 'min-item', 'complete-purchase'],
  methods: {
    removeRow (ind) {
      this.$emit('remove-row', ind)
    },
    addItem (ind) {
      this.$emit('add-item', ind)
    },
    minItem (ind) {
      this.$emit('min-item', ind)
    },
    completePurchase () {
      this.$emit('complete-purchase')
    }
  }
}
</script>

<style scoped>
    @import url('https://fonts.googleapis.com/css2?family=Ubuntu&display=swap');
    #confirm{
        right: 0;
        bottom:0;
        position:absolute;
        background-color:#4690CD;
        color: white;
        padding: 5px 15px 5px 15px;
        border-radius: 4px;
        border: none;
    }

    #confirm:hover{
      background: #457EAD;
    }

    .total{
      right: 0;
      bottom: 25%;
      position:absolute;
      font-size: 18px;
    }

    #total-text{
      font-family: 'Ubuntu', sans-serif;
      font-weight: 700;
    }

  *{
    font-family: 'Ubuntu' ;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  #cart-table {
    margin-top: 30px;
    border: solid 1px black;
    position: relative;
  }

  #table-content {
    min-height: 160px;
  }

  #bottom{
    width: 100%;
    height: 100px;
    position: relative;
    margin-top: 40px;
  }

</style>
