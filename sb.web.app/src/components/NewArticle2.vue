<template>
<div id="input-field">
  <CreateHeader ButtonText="Tillbaka" link="/add_article/2"/>
  <img src="../assets/link_arrow/two_three.png" id="one-three"/>
  <UserInput type="regular" name="title" label="Pris" placeholder="Hur mycket kostar din?"/>
  <!-- <UserInput type="combobox" name="category" label="Kategori" placeholder="Vilken av Kategorierna nedan tillhör din produkt?" :options="['Produkt', 'Tjänst']" /> -->
  <NewArticleFooter ButtonText="Nästa" link="/add_article/3"/>
</div>

</template>

<script>
import CreateHeader from './CreateArticle/CreateHeader.vue'
import UserInput from './CreateArticle/UserInput.vue'
import NewArticleFooter from './CreateArticle/NewArticleFooter.vue'

export default {
  name: 'NewArticle2',
  components: {
    CreateHeader,
    UserInput,
    NewArticleFooter
  }  

}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Ubuntu:wght@700&display=swap');
 #input-field{
     margin: 0 auto;
     margin-top: 30px;
     width:1000px;
     font-family: 'Ubuntu';
   }
  img#one-three {
    width: 90px;
    float: right;
  }
  @media (max-width: 1350px) {
      #input-field{
          width: 60%;
      }
  }

  @media (max-width: 1150px) {
      #input-field{
          width: 70%;
      }
  }

 @media (max-width: 900px) {
      #input-field{
          width: 80%;
      }
 }
   @media (max-width: 750px) {
      #input-field{
          width: 90%;
      }
   }

   @media (max-width: 540px) {
      #input-field{
          width: 100%;
      }
   }
</style>
