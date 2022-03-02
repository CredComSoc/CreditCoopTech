<template>
<div id="input-field">
    <a href="#" id="shop"><img class="arrow" src="../assets/link_arrow/left_arrow_link.png"/>Shop</a>
    <img src="../assets/link_arrow/one_three.png" id="one-three"/>
    <div id="title-field" class="input">
        <lable for="title"><strong>Rubrik</strong></lable><br>
        <input type="text" placeholder="Vad ska din artikel heta?" id="title" name="title"><br><br>
    </div>
    <div class="input">
        <lable for="description"><strong>Beskriving</strong></lable><br>
        <textarea placeholder="Beskriv vad som ingår i din artikel" id="description" name="description"/><br><br>
    </div>
    <div class="input">
        <lable for="artical-type"><strong>Typ av artikel</strong></lable><br>
        <select id="artical-type" name="artical-type" @click="colorDropDown">
            <option value="" disabled selected hidden>Är din artikel en produkt eller tjänst?</option>
            <option value="product" >Produkt</option>
            <option value="service">Tjänst</option>
        </select><br><br>
    </div>
    <div class="input">
        <lable for="category"><strong>Kategori</strong></lable><br>
        <select id="category" name="category" @click="colorDropDown">
            <option value="" disabled selected hidden>Vilken av Kategorierna nedan tillhör din produkt?</option>
            <option value="product" >Produkt</option>
            <option value="service">Tjänst</option>
        </select><br><br>
    </div>
     <a href="#">Nästa<img class="arrow" src="../assets/link_arrow/Right_arrow_link.png"/></a>
</div>
</template>

<script>
export default {
  methods: {
    colorDropDown (id) {
      const selcetOption = document.getElementById(id.target.id)
      selcetOption.style.color = 'black'
    }
  }
}

</script>

<style scoped>
 #input-field{
     margin: 0 auto;
     margin-top: 30px;
     width:50%;
     font-family: 'Ubuntu';
   }

 a{
     color: black;
     float: right;
  }

 a#shop{
     float:left;
 }

 div#title-field{
     padding-top: 50px;
 }

 img.arrow{
     width: 12px;
     margin-left: 2px;
 }

 img#one-three{
     width: 90px;
     float:right;

 }

 select{
     width: 328px;
     height: 35px;
     margin-top:10px;
     border-radius: 4px;
     border: 2px solid rgb(190, 190, 190);
     font-family: 'Ubuntu';
 }

 #title{
     height: 30px;
 }

 select{
     color:rgb(190, 190, 190);
 }

 .input{
     margin-left: 80px;
 }

 #description{
     height: 100px;
     width: 318px;
     resize: none;
     margin-top:10px;
     border-radius: 4px;
     border: 2px solid rgb(190, 190, 190);
     font-family: 'Ubuntu';
 }

 input {
     width: 318px;
     margin-top:10px;
     border-radius: 4px;
     border: 2px solid rgb(190, 190, 190);
 }

 input::placeholder{
     padding-left:3px;
     color: rgb(190, 190, 190);
 }

 textarea::placeholder{
     padding-left:3px;
     color: rgb(190, 190, 190);
 }

 input:focus::placeholder {
  color: transparent;
 }

 textarea:focus::placeholder {
  color: transparent;
 }
</style>
