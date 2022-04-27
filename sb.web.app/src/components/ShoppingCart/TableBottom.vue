<template>
  <div>
    <div class="total">
      <span id="total-text">Totalt: </span>
      <span >{{ total }} bKr</span>
    </div>
    <button @click="completePurchase" id="confirm">Slutför köp</button>
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
      top: 0%;
      position:absolute;
      font-size: 18px;
    }

    #total-text{
      font-family: 'Ubuntu', sans-serif;
      font-weight: 700;
    }

</style>
