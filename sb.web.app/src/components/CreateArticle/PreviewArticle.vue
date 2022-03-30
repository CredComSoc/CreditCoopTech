<template>
  <div id="center-header">
    <h2> FÖRHANDSGRANSKNING </h2>
    <p> Kontrollera att uppgifterna stämmer </p>
  </div>
  <div id="items-container">
    <PreviewItem title='Rubrik' :text='this.savedProgress.title' />
    <PreviewItem title='Beskrivning' :text='this.savedProgress.description' />
    <PreviewItem title='Typ av artikel' :text='this.savedProgress.type' />
    <PreviewItem title='Kategori' :text='this.savedProgress.category' />
    <PreviewItem title='Tid' :text='this.savedProgress.endDate' />
    <PreviewItem title='Plats' :text='this.savedProgress.city' />
    <PreviewItem title='Pris' :text='this.savedProgress.price' />
    <PreviewItem title='Bilder' text='' />
  </div>
</template>

<script>
import PreviewItem from './PreviewItem.vue'

export default {
  name: 'PreviewArticle',
  components: {
    PreviewItem    
  },
  props: ['savedProgress']
}
</script>

<style scoped>
* {
    font-family: 'Ubuntu', sans-serif;
    padding: 0;
    margin: 0;
    box-sizing: border-box;
}

h2 {
    font-weight: 400;
    font-size: 24px;
    letter-spacing: 0.06em;
    font-style: normal;
}

p {
    font-style: normal;
    font-weight: 300;
    font-size: 16px;
    line-height: 18px;
    letter-spacing: 0.03em;
    margin-top: 10px;
}

#center-header {
    text-align: center;
    margin-top: 20px;
}

#items-container {
    width: 500px;
}

@media (max-width: 600px) {
  #items-container {
    width: 80%;
  }

  h2 {
    font-size: 20px;
  }

  p {
    font-size: 13px;
  }
}

</style>
