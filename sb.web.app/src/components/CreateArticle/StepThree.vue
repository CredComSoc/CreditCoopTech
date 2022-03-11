<template>
<div id="title-field">
  <div  class="input">
    <div id="pic">
        <p>Välj fil</p>
    </div>
    <button @click=upload>Bläddra</button>
    <input type='file' id="getFile" @change=getFile>
  </div>
  <div id="images"> 
    <UploadedImage/>
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
  methods: {
    upload () {
      document.getElementById('getFile').click()
    },
    getFile (e) {
      console.log(URL.createObjectURL(e.target.files[0]))
    }
  }
}
</script>

<style scoped>

 #title-field {
  padding-top: 50px;
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
    align-items: baseline;
    gap: 40px;
}

input{
    display:none;
}
button{
    display:block;
    width:120px; 
    height:35px;
    border-radius: 0px 4px 4px 0px;
    background-color: rgb(236, 236, 236) ;
    border: 2px solid #797979; 
}

.input {
  margin-top: 40px;
  display: flex;
  margin-bottom: 20px;
}

@media (max-width: 400px) {
  .input {
    margin-left: 40px;
  }
}

</style>
