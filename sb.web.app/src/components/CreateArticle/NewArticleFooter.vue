<template>
  <a :href=link><u>{{ ButtonText }}</u><img src="../../assets/link_arrow/Right_arrow_link.png"/></a>
</template>

<script>
export default {
  name: 'NewArticleFooter',
  props: ['ButtonText', 'link']
}
</script>

<style scoped>
  a {
    color: black;
    float: right;
    font-size: 22px;
    margin-top: 10px;
    text-decoration: underline;
    cursor: pointer;
  }

  a:hover{
    color: black;
  }

  img{
    width: 12px;
    margin-left: 2px;
  }

  @media (max-width: 400px) {
  a{
    font-size: 18px;
  }

}
</style>
