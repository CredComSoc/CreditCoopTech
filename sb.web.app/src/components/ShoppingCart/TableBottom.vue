<template>
  <div>
    <div class="saldo">
      <span id="total-text">{{ $t('cart.current_balance') }} </span>
      <span >{{ this.$store.state.saldo }} {{ $t('org.tkn') }}</span>
    </div>
    <div class="total">
      <span id="total-text">{{ $t('cart.total_cost') }} </span>
      <span >{{ total }} {{ $t('org.tkn') }}</span>
    </div>
    <div class="after">
      <span id="total-text">{{ $t('cart.balance_after') }} </span>
      <span >{{ this.$store.state.saldo - total }} {{ $t('org.tkn') }}</span>
    </div>
    <button @click="completePurchase" id="confirm">{{ $t('shop.complete_purchase') }}</button>
  </div>
</template>

<script>
export default {
  props: ['total'],
  emits: ['complete-purchase'],
  methods: {
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
        bottom: 0;
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
      top: 20%;
      position:absolute;
      font-size: 18px;
    }

    #total-text{
      font-family: 'Ubuntu', sans-serif;
      font-weight: 700;
    }
    .saldo {
      right: 0;
      top: 0;
      position:absolute;
      font-size: 18px;
    }

    .after {
      right: 0;
      top: 40%;
      position:absolute;
      font-size: 18px;
      margin-bottom: 150px;     
    }

</style>
