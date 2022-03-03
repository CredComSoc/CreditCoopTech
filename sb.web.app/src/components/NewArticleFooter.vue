<template>
  <a href="#">{{ButtonText}}<img src="../assets/link_arrow/Right_arrow_link.png"/></a>
</template>

<script>
export default {
  props: ['ButtonText']
}
</script>

<style scoped>
  a{
    color: black;
    float: right;
  }
  a:hover{
    color: black;
  }
  img{
    width: 12px;
    margin-left: 2px;
  }
</style>
