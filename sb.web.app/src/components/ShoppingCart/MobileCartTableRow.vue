<template>
<div class="table-row">
  <div class="floating-left">
    <img class="content-img" :src="this.getImgURL()" />
    <p :class="[`non-b-text`,`title-text`]"> {{ title }} </p>
    <p :class="[`non-b-text`, `price-text`]"> Pris: {{ this.priceOfItem }} bKr </p>
  </div>
  <!--<h3 v-if="ind === 1"> Antal </h3>-->  
  <!--<h3 v-if="ind === 1"> Pris </h3>-->
  <!--<h3 v-if="ind === 1"> Summa </h3>-->   
  <div class="right-part">
    <div type="button" class="g-can" @click="this.removeRow">
      <img src="../../assets/cart_images/garbage.png">
    </div>
    <div class="floating-right">
      <div class="item-controller">
        <div type="button" class="sub-item" @click="minItem">
          <img src="../../assets/cart_images/sub.png">
        </div>
        <p class="non-b-text" > {{ this.numberOfItems }} </p>
        <div type="button" class="add-item" @click="addItem">
          <img src="../../assets/cart_images/add.png">
        </div>
      </div>
      <p :class="[`non-b-text`, `sum-text`]"> Summa: {{ this.totalPrice }} </p>   
    </div>
  </div>
</div>
  
</template>

<script>
import { EXPRESS_URL } from '../../serverFetch'
export default {
  name: 'MobileCartTableRow',
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

.table-row {
  width: 100%;
  height: 130px;
  position: relative;
  margin-bottom: 15px;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  border-top: 1px solid #e6e6e6;
}

.floating-left {
  flex: 1;
}

.floating-right {
  position: absolute;
  bottom: 0;
  right: 0;
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
    position: absolute;
    top: 0;
    right: 0;
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

.title-text{
  word-break: break-word;
}

.sum-text {
  font-weight: 700;
}

.b-text {
    font-weight: 500;
    font-size: 18px;
} 

.content-img {
    filter: drop-shadow(0px 4px 4px rgba(0, 0, 0, 0.25));
    width: 102px;
    height: 80px;
    float: left;
    margin-right: 10px;
    margin-top: 5px;
    margin-left: 5px;
}

@media (max-width: 860px) {
  .title-text{
    width: 80%;
  }
 }

@media (max-width: 500px) {
  .title-text, .price-text{
    font-size: 14px;
  }
 }
</style>
