<template>
  <p>{{ $t('cart.cart_empty') }}</p>
</template>

<script>
export default {
  name: 'EmptyCart'
}
</script>

<style scoped>
  *{
    font-family: 'Ubuntu' ;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  p{
    font-style: italic;
  }
</style>
