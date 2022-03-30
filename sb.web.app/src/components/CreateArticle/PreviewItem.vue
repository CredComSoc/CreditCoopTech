<template>
  <h3> {{ this.title }} </h3>
  <p> {{ this.text }} </p>
</template>

<script>
export default {
  name: 'PreviewItem',
  props: ['title', 'text']
}
</script>

<style scoped>
* {
  font-family: 'Ubuntu', sans-serif;
  padding: 0;
  margin: 0;
  box-sizing: border-box;
}

h3 {
  font-size: 24px;
  font-weight: 700;
  margin-top: 40px;
  margin-bottom: 15px;
}

p {
    word-break: break-word;
}

</style>
