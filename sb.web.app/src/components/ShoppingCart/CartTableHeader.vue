<template>
  <div id="cart-header">
    <span></span>
    <h3> {{ $t('item') }} </h3>
    <span></span>
    <h3> {{ $t('quantity') }} </h3>
    <h3> {{ $t('price') }} </h3>
    <h3> {{ $t('amount') }} </h3>
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
