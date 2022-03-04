<template>
  <a href="#">{{ButtonText}}<img src="../assets/link_arrow/Right_arrow_link.png"/></a>
</template>

<script>
export default {
  name: 'NewArticleFooter',
  props: ['ButtonText']
}
</script>

<style scoped>
  a{
    color: black;
    float: right;
    font-size: 22px;
    margin-top: 10px;
  }
  a:hover{
    color: black;
  }
  img{
    width: 12px;
    margin-left: 2px;
  }
</style>
