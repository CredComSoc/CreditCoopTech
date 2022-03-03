<template>
<div id="input-field">
    <div id="header">
        <a href="#" id="shop"><img class="arrow" src="../assets/link_arrow/left_arrow_link.png"/>Shop</a>
        <img src="../assets/link_arrow/one_three.png" id="one-three"/>
    </div>
    <div id="title-field" class="input">
        <lable for="title" class="input-title">Rubrik</lable><br>
        <input type="text" placeholder="Vad ska din artikel heta?" id="title" name="title"><br><br>
    </div>
    <div class="input">
        <lable for="description" class="input-title">Beskriving</lable><br>
        <textarea placeholder="Beskriv vad som ingår i din artikel" id="description" name="description"/><br><br>
    </div>
    <div class="input">
        <lable for="artical-type" class="input-title">Typ av artikel</lable><br>
        <select id="artical-type" name="artical-type" @click="colorDropDown">
            <option value="" disabled selected hidden>Är din artikel en produkt eller tjänst?</option>
            <option value="product" >Produkt</option>
            <option value="service">Tjänst</option>
        </select><br><br>
    </div>
    <div class="input">
        <lable class="input-title" for="category">Kategori</lable><br>
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
@import url('https://fonts.googleapis.com/css2?family=Ubuntu:wght@700&display=swap');
 #input-field{
     margin: 0 auto;
     margin-top: 30px;
     width:50%;
     font-family: 'Ubuntu';
   }

   .input-title{
       font-size: 24px;
       font-family: 'Ubuntu', sans-serif;
       font-weight: 700;
   }

 a{
     color: black;
     float: right;
  }

  a:hover{
      color: black;
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
     width: 420px;
     height: 35px;
     margin-top:10px;
     border-radius: 4px;
     border: 2px solid #5c5c5c;
     font-family: 'Ubuntu';
 }

 #title{
     height: 30px;
     font-size: 13px;
     width: 420px;
 }

 select{
     color:#5c5c5c;
     font-size: 13px;
     background-color: white ;
 }

 .input{
     margin-left: 80px;
     margin-top: 15px;
 }

 #description{
     height: 100px;
     width: 420px;
     resize: none;
     margin-top:10px;
     border-radius: 4px;
     border: 2px solid #5c5c5c;
     font-family: 'Ubuntu';
     font-size: 13px;
 }

 input {
     width: 318px;
     margin-top:10px;
     border-radius: 4px;
     border: 2px solid #5c5c5c;
 }

 input::placeholder{
     padding-left:3px;
     color: #5c5c5c;
 }

 textarea::placeholder{
     padding-left:3px;
     color: #5c5c5c;
 }

 input:focus::placeholder {
  color: transparent;
 }

 textarea:focus::placeholder {
  color: transparent;
 }
   @media (max-width: 800px) {
      #input-field{
          width: 70%;
      }
   }

   @media (max-width: 700px) {
      #input-field{
          width: 80%;
      }
   }

   @media (max-width: 600px) {
      #input-field{
          width: 90%;
      }
   }

   @media (max-width: 500px) {
      #input-field{
          width: 100%;
      }
   }

   @media (max-width: 400px) {

       input {
         width: 250px;
        }
       #description{
           width: 250px;
           font-size: 10px;
       }
       #title{
           font-size: 10px;
       }
       select{
         width: 250px;
         font-size: 10px;
       }

      .input{
         margin-left: 20px;
       }
   }
</style>
