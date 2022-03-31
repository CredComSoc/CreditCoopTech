<template>
<div id="title-field">
  <label :for="this.name" class="input-title"> {{ this.label }} </label>
  <div  class="input">
    <div id="pic">
        <p>Välj fil</p>
    </div>
    <button ref="addFile" @click=upload>Bläddra</button>
    <input type='file' id="getFile" @change=getFile :name="this.name">
  </div>
  <div id="images"> 
    <UploadedImage class="img" textboxLabel="Välj som omslagsbild" :isPreview="false"
    v-for="(img) in this.images"
          :imageURL="img[0]"
          :key="img[0]"
          :id="img[1]"
          :isCoverImg="img[2]"
    />
  </div>
</div> 
</template>

<script>

import UploadedImage from './UploadedImage.vue'

export default {
  name: 'StepThree',
  components: {
    UploadedImage
  },
  props: ['name', 'label', 'savedProgress'],
  data () {
    return {
      images: [],
      imageObjs: []
    }
  },
  methods: {
    getStepThreeInputs () {
      const cbs = document.getElementsByClassName('cb')
      for (var i = 0; i < cbs.length; i++) {
        if (cbs[i].checked) {
          this.imageObjs[i].isCoverImg = true
        } else {
          this.imageObjs[i].isCoverImg = false
        }
      }
      return { 
        image: this.imageObjs
      }
    },
    upload () {
      document.getElementById('getFile').click()
    },
    getFile (e) {
      const imageObj = e.target.files[0]
      const URLImg = URL.createObjectURL(imageObj)
      this.$refs.addFile.innerText = 'Välj fler'
      this.images.push([URLImg, this.images.length, false])
      this.imageObjs.push(imageObj)
    }
  },
  mounted () {
    if ('image' in this.savedProgress) {
      for (const img of this.savedProgress.image) {
        const URLImg = URL.createObjectURL(img)
        this.images.push([URLImg, this.images.length, img.isCoverImg])
        this.imageObjs.push(img)
      }
    } 
  }
}
</script>

<style scoped>

 #title-field {
  margin-top: 40px;
  display: flex;
  flex-direction: column;
  width: fit-content;
}

p{
    margin-left: 3px;
    color:#bebebe;
    font-family: 'Ubuntu';
    font-size: 14px;
}

.input-title {
  font-size: 24px;
  font-family: 'Ubuntu', sans-serif;
  font-weight: 700;
  margin-bottom: 10px;
}

#pic{
    height: 35px;
    width: 400px;
    border: 2px solid #797979;
    border-radius: 4px 0px 0px 4px;
    border-right: white;
    line-height: 35px;
    white-space: nowrap;
}

#images{
    display: flex;
    flex-direction: row;
    align-items: flex-end;
    gap: 40px;
    flex-wrap: wrap;
    max-width: 480px;
}

input{
    display:none;
}
button{
    display:block;
    width:80px; 
    height:35px;
    border-radius: 0px 4px 4px 0px;
    background-color: rgb(236, 236, 236) ;
    border: 2px solid #797979; 
    font-size: 15px;
    font-family: 'Ubuntu';
}

.input {
  margin-top: 20px;
  display: flex;
  margin-bottom: 20px;
}

@media (max-width: 470px) {
  .input{
    width: 300px;
  }
  #images{
    width: 300px;
  }
  button{
    font-size: 12px;
  }
}

@media (max-width: 550px) {
  #pic {
    width: 350px;
  }
  #images{
    max-width: 430px;
  }
}

</style>
