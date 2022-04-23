<template>
  <div id="cart-header">
    <span></span>
    <h3> Produkt </h3>
    <span></span>
    <h3> Antal </h3>
    <h3> Pris </h3>
    <h3> Summa </h3>
    <span></span>
  </div>
</template>

<script>
export default {
  name: 'CartTableHeader'
}
</script>

<style scoped>
*{
  font-family: 'Ubuntu';
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-weight: 400;
  font-size: 22px;
}

#cart-header {
    display: flex;
    flex-direction: row;
    justify-content: space-around;
}

</style>
