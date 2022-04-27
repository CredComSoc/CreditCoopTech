<template>
  <div>
    <h3> {{ this.title }} </h3>
    <p v-if="this.images === null"> {{ this.text }} </p>
    <div v-if="this.images !== null" id="images-container">
      <UploadedImage class="img" textboxLabel="Vald som omslagsbild" :isPreview="true"
        v-for="(img) in this.images"
          :imageURL="img[0]"
          :key="img[0]"
          :break="img[1]"
          :id="img[2]"
          :isCoverImg="img[3]"
      />
    </div>
  </div>
</template>

<script>
import UploadedImage from './UploadedImage.vue'

export default {
  name: 'PreviewItem',
  components: {
    UploadedImage
  },
  props: ['title', 'text', 'images']
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

#images-container {
  display: flex;
  flex-direction: row;
  gap: 40px;
  flex-wrap: wrap;
  max-width: 480px;
}

</style>
