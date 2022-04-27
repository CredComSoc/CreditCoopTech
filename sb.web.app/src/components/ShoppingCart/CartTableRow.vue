<template>
<div class="table-row">
  <div class="cart-col">  
    <p class="b-text"> {{ ind }} </p>
  </div>
  <div class="cart-col">
    <h3 v-if="ind === 1"> Produkt </h3> 
    <img class="content-img" :src='this.getImgURL()' />
  </div>
  <div class="cart-col">  
    <p :class="[`non-b-text`,`title-text`]"> {{ title }} </p>
  </div>
  <div class="cart-col">
    <h3 v-if="ind === 1"> Antal </h3>   
    <div class="item-controller">
        <div type="button" class="sub-item" @click="minItem">
            <img src="../../assets/cart_images/sub.png">
        </div>
        <p class="non-b-text" > {{ this.numberOfItems }} </p>
        <div type="button" class="add-item" @click="addItem">
            <img src="../../assets/cart_images/add.png">
        </div>
    </div>
  </div>
  <div class="cart-col"> 
    <h3 v-if="ind === 1"> Pris </h3>  
    <p class="non-b-text"> {{ this.priceOfItem }} </p>
  </div>
  <div class="cart-col">
    <h3 v-if="ind === 1"> Summa </h3>   
    <p class="non-b-text"> {{ this.totalPrice }} </p>
  </div>
  <div class="cart-col">    
    <div type="button" class="g-can" @click="this.removeRow">
      <img src="../../assets/cart_images/garbage.png">
    </div>
  </div> 
</div>
</template>

<script>
import { EXPRESS_URL } from '../../serverFetch'
export default {
  name: 'CartTableRow',
  props: ['ind', 'image', 'title', 'items', 'price', 'sum', 'coverImg'],
  data () {
    return {
      numberOfItems: this.items,
      priceOfItem: this.price,
      totalPrice: this.sum
    }
  },
  methods: {
    addItem () {
      this.numberOfItems++
      this.totalPrice = this.numberOfItems * this.priceOfItem
      this.$emit('add-item', this.ind)
    },
    minItem () {
      if (this.numberOfItems > 1) {
        this.numberOfItems--
        this.totalPrice = this.numberOfItems * this.priceOfItem
        this.$emit('min-item', this.ind)
      }
    },
    removeRow () {
      this.$emit('remove-row', this.ind)
    },
    getImgURL () {
      return EXPRESS_URL + '/image/' + this.coverImg
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Ubuntu&display=swap');
*{
  font-family: 'Ubuntu' ;
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

h3 {
  font-weight: 400;
  font-size: 22px;
  position: absolute;
  top: 3%;
}

.cart-col {
    height: 110px;
    width: 120px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.table-row {
  display: flex;
  flex-direction: row;
  justify-content: space-evenly;
  align-items: center;
  margin-top: 50px;
}

.item-controller {
  display: flex;
  flex-direction: row;
  justify-content: space-around;
  align-items: center;
  width: 100px;
}

.sub-item, .add-item {
    width: 18px;
    height: 18px;
}

.g-can {
    width: 32px;
    height: 38px;
}

.g-can img {
  margin-right: 3px;
}

.add-item:hover, .sub-item:hover {
  transform: scale(1.07);
}

.g-can:hover {
  transform: scale(1.03);
}

.sub-item, .add-item, .g-can {
    border: 1px solid black;
    border-radius: 4px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.non-b-text {
    font-weight: 300;
    font-size: 18px;
}

.b-text {
    font-weight: 700;
    font-size: 18px;
} 

.content-img {
    filter: drop-shadow(0px 4px 4px rgba(0, 0, 0, 0.25));
    width: 102px;
    height: 80px;
}

.title-text{
  word-break: break-word;
}
</style>
