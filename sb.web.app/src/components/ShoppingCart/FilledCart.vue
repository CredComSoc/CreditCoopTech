<template>
  <p>Totalt: {{total}} bKr</p>
  <div type="button" id="confirme">Slutför köp</div>
</template>

<script>
export default {
  name: 'FilledCart',
  props: ['total']
}
</script>

<style scoped>
    #confirme{
        right: 0;
        bottom:0;
        position:absolute;
        background-color:#4690CD;
        color: white;
        padding: 5px 10px 5px 10px;
        border-radius: 4px;
    }

    #confirme:hover{
        background: #457EAD;
    }
</style>
