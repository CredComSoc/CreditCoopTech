<template>
<div id="input-field">
  <CreateHeader />
  <img src="../assets/link_arrow/one_three.png" id="one-three"/>
  <UserInput type="regular" name="title" label="Rubrik" placeholder="Vad ska din artikel heta?"/>
  <UserInput type="textarea" name="description" label="Beskrivning" placeholder="Beskriv vad som ingår i din artikel"/>
  <UserInput type="combobox" name="articale-type" label="Typ av artikel" placeholder="Är din artikel en produkt eller tjänst?" :options="['Produkt', 'Tjänst']" />
  <!-- <UserInput type="combobox" name="category" label="Kategori" placeholder="Vilken av Kategorierna nedan tillhör din produkt?" :options="['Produkt', 'Tjänst']" /> -->
  <a href="#">Nästa<img class="arrow" src="../assets/link_arrow/Right_arrow_link.png"/></a>   
</div>
</template>

<script>
import CreateHeader from './CreateArticle/CreateHeader.vue'
import UserInput from './CreateArticle/UserInput.vue'

export default {
  methods: {
    colorDropDown (id) {
      const selcetOption = document.getElementById(id.target.id)
      selcetOption.style.color = 'black'
    }
  },
  components: {
    CreateHeader,
    UserInput
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

 a{
     color: black;
     float: right;
  }

  a:hover{
      color: black;
  }

  img#one-three {
    width: 90px;
    float: right;
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
