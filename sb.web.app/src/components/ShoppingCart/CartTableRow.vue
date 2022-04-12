<template>
<div class="table-row">
  <div class="cart-col">  
    <p class="b-text"> {{ ind }} </p>
  </div>
  <div class="cart-col">
    <h3 v-if="ind === 1"> Produkt </h3>  
    <img class="content-img" src="../../assets/list_images/Event_6.png" />
  </div>
  <div class="cart-col">  
    <p class="non-b-text"> {{ title }} </p>
  </div>
  <div class="cart-col">
    <h3 v-if="ind === 1"> Antal </h3>   
    <div class="item-controller">
        <div class="sub-item">
            <img src="../../assets/cart_images/sub.png">
        </div>
        <p class="non-b-text"> {{ items }} </p>
        <div class="add-item">
            <img src="../../assets/cart_images/add.png">
        </div>
    </div>
  </div>
  <div class="cart-col"> 
    <h3 v-if="ind === 1"> Pris </h3>  
    <p class="non-b-text"> {{ price }} </p>
  </div>
  <div class="cart-col">
    <h3 v-if="ind === 1"> Summa </h3>   
    <p class="non-b-text"> {{ sum }} </p>
  </div>
  <div class="cart-col">    
    <div class="g-can">
      <img src="../../assets/cart_images/garbage.png">
    </div>
  </div> 
</div>
</template>

<script>
export default {
  name: 'CartTableRow',
  props: ['ind', 'image', 'title', 'items', 'price', 'sum']
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

.cart-col {
    height: 110px;
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
    padding-right: 3px;
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
    font-weight: 500;
    font-size: 18px;
} 

.content-img {
    filter: drop-shadow(0px 4px 4px rgba(0, 0, 0, 0.25));
    width: 102px;
    height: 80px;
}
</style>
