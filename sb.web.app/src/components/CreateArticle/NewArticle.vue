<template>
<div id="input-field">
  <CreateHeader ButtonText="Shop" link="/Shop" :imgURL="this.imgURL" />
  <StepOne v-if="this.currentStep === 1" />
  <StepTwo v-if="this.currentStep === 2" />
  <NewArticleFooter ButtonText="Nästa" @click=changeStep />
</div>
</template>

<script>
import CreateHeader from './CreateHeader.vue'
import StepOne from './StepOne.vue'
import StepTwo from './StepTwo.vue'
import NewArticleFooter from './NewArticleFooter.vue'

export default {
  name: 'NewArticle',
  components: {
    CreateHeader,
    StepOne,
    StepTwo,
    NewArticleFooter
  },
  data () {
    return {
      backLink: '/Shop',
      currentStep: 1,
      imgURL: 'one_three.png'
    }
  },
  methods: {
    changeStep () {
      if (this.currentStep === 1) {
        this.currentStep = 2
        this.imgURL = 'two_three.png'
      } else if (this.currentStep === 2) {
        this.currentStep = 3
      }
    },
    changeHeader () {
      
    }
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
