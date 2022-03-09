<template>
<div id="input-field">
  <CreateHeader ButtonText="Tillbaka" link="/add_article/1"/>
  <img src="../../assets/link_arrow/three_three.png" id="one-three"/>
  <!-- <UserInput type="combobox" name="category" label="Kategori" placeholder="Vilken av Kategorierna nedan tillhör din produkt?" :options="['Produkt', 'Tjänst']" /> -->
  <NewArticleFooter ButtonText="Nästa" link="#"/>
</div>

</template>

<script>
import CreateHeader from './CreateHeader.vue'
import NewArticleFooter from './NewArticleFooter.vue'

export default {
  name: 'NewArticle2',
  components: {
    CreateHeader,
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
