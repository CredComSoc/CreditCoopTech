<template>
  <div id="center-header">
    <h2> FÖRHANDSGRANSKNING </h2>
    <p> Kontrollera att uppgifterna stämmer </p>
  </div>
  <div id="items-container">
    <PreviewItem title='Rubrik' :text='this.savedProgress.title' :images="null" />
    <PreviewItem title='Beskrivning' :text='this.savedProgress.description' :images="null" />
    <PreviewItem title='Typ av artikel' :text='this.savedProgress.type' :images="null" />
    <PreviewItem title='Kategori' :text='this.savedProgress.category' :images="null" />
    <PreviewItem title='Tid' :text='this.savedProgress.endDate' :images="null" />
    <PreviewItem title='Plats' :text='this.savedProgress.city' :images="null" />
    <PreviewItem title='Pris' :text='this.savedProgress.price' :images="null" />
    <PreviewItem title='Bilder' :text="null" :images='this.images' />
  </div>
  <div id="publish-confirmation">
    <p>HEllo </p> 
  </div>
</template>

<script>
import PreviewItem from './PreviewItem.vue'

export default {
  name: 'PreviewArticle',
  components: {
    PreviewItem    
  },
  mounted () {
    for (const img of this.savedProgress.image) {
      const URLImg = URL.createObjectURL(img)
      if (this.images.length % 2 === 0) {
        this.images.push([URLImg, true, this.images.length])
      } else {
        this.images.push([URLImg, false, this.images.length])
      }
    }
  },
  props: ['savedProgress'],
  data () {
    return {
      images: []
    }
  }
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

#publish-confirmation {
  width: 500px;
  height: 232px;
  border: 1px solid #CED4DA;
  box-sizing: border-box;
  border-radius: 0px 0px 12px 12px;
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
