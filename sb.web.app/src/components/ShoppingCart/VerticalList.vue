<template>
  <div id="cart-column">
      <h3 id="column-header" > {{ this.title }} </h3>
      <div class="column-content" v-for="(item,index) in element"
      :key="index">
        <img id="column-image" v-if="isImage" src="../../assets/list_images/Event_6.png" />
        <p > {{ item }} </p>
      </div>
  </div>
</template>

<script>
export default {
  name: 'VerticalList',
  props: ['element', 'title', 'isNumberOf', 'isGarCan', 'isImage']
}
</script>

<style scoped>
* {
  font-family: 'Ubuntu' ;
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

#cart-column {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-evenly;
}

#column-image {
    filter: drop-shadow(0px 4px 4px rgba(0, 0, 0, 0.25));
    width: 102px;
    height: 80px;
}
</style>
