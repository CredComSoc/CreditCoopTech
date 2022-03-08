<template>
  <div id="create-header">
    <a :href=link><img class="arrow" src="../../assets/link_arrow/left_arrow_link.png"/>{{ButtonText}}</a>
  </div>
</template>

<script>
export default {
  name: 'CreateHeader',
  props: ['ButtonText', 'link']
}
</script>

<style scoped>

#create-header {
    width: 100%;
}

img{
  width: 12px;
  margin-left: 2px;
}

a{
  float: left;
  color: black;
  font-size: 22px;
}

a:hover{
  color: black;
}

@media (max-width: 400px) {
  a{
    font-size: 18px;
  }

}
</style>
