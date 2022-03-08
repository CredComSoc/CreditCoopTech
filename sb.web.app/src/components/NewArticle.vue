<template>
<div id="input-field">
  <CreateHeader />
  <StepOne />
  <NewArticleFooter ButtonText="Nästa"/>
</div>
</template>

<script>
import CreateHeader from './CreateArticle/CreateHeader.vue'
import StepOne from './CreateArticle/StepOne.vue'
import NewArticleFooter from '@/components/NewArticleFooter.vue'

export default {
  name: 'NewArticle',
  components: {
    CreateHeader,
    StepOne,
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
