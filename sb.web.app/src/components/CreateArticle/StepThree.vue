<template>
<div>
<div id="title-field" class="input">
    <div id="pic">
        <p>Välj fil</p>
    </div>
    <button @click=upload>Bläddra</button>
    <input type='file' id="getFile" @change=getFile> 
</div> 
<div id="images"> 
</div>
</div>
</template>

<script>
export default {
  name: 'StepThree',
  methods: {
    upload () {
      document.getElementById('getFile').click()
    },
    getFile (e) {  
      const file = e.target.files[0]
      const img = document.createElement('img')
      img.src = URL.createObjectURL(file)
      img.style.width = '200px'
      const parent = document.getElementById('images')
      parent.appendChild(img)
    }
  }
}
</script>

<style scoped>

 #title-field {
  padding-top: 50px;
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
  margin-left: 80px;
  margin-top: 40px;
  display: flex;
  justify-content: center;
}

@media (max-width: 400px) {
  .input {
    margin-left: 40px;
  }
}

</style>
