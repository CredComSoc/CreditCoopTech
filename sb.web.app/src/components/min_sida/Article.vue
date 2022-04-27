<template>
  <div>
      <img src="../../assets/städning2.png" alt="Generisk Bild">
      <p>{{article}}</p>
      <p>{{article.shortDesc}}</p>
  </div>
</template>

<script>

export default {
  name: 'Article',
  props: { article: String },
  created () {
    console.log(this.article)
  },
  data () {
    return {
      
    }
  }
}

</script>

<style scoped>

</style>
