<template>
  <div id="create-header">
    <a href="/shop" id="shop"><img class="arrow" src="../../assets/link_arrow/left_arrow_link.png"/>Shop</a>
  </div>
</template>

<script>
export default {
  name: 'CreateHeader'  
}
</script>

<style>

#create-header {
    width: 100%;
}

img.arrow {
  width: 12px;
  margin-left: 2px;
}

a#shop {
  float: left;
  color: black;
  font-size: 22px;
}
</style>
