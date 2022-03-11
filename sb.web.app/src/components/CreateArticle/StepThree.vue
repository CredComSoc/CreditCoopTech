<template>
<div id="title-field">
  <label :for="this.name" class="input-title"> {{ this.label }} </label>
  <div  class="input">
    <div id="pic">
        <p>Välj fil</p>
    </div>
    <button @click=upload>Bläddra</button>
    <input type='file' id="getFile" @change=getFile :name="this.name">
  </div>
  <div id="images"> 
    <UploadedImage class="img"
    v-for="(img) in this.images"
          :imageURL="img[0]"
          :key="img[0]"
          :break="img[1]"
          :id="img[2]"
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
  props: ['name', 'label'],
  data () {
    return {
      images: []
    }
  },
  methods: {
    upload () {
      document.getElementById('getFile').click()
    },
    getFile (e) {
      const URLImg = URL.createObjectURL(e.target.files[0])
      if (this.images.length % 2 === 0) {
        this.images.push([URLImg, true, this.images.length])
      } else {
        this.images.push([URLImg, false, this.images.length])
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
    font-family: 'Ubuntu';
}

.input {
  margin-top: 20px;
  display: flex;
  margin-bottom: 20px;
}

@media (max-width: 400px) {
  .input {
    margin-left: 40px;
  }
}

</style>
