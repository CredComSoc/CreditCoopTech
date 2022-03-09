<template>
<div id="input-form">
  <CreateHeader :ButtonText="buttonText" :link="this.backLink" :imgURL="this.imgURL" @goBackStep=goBackStep />
  <StepOne v-if="this.currentStep === 1" ref='stepOne'/>
  <StepTwo v-if="this.currentStep === 2" />
  <NewArticleFooter :ButtonText="nextBtnText" @click=goForwardStep />
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
      imgURL: 'one_three.png',
      buttonText: 'Shop',
      nextBtnText: 'Nästa',
      newArticle: {}
    }
  },
  methods: {
    saveFirstStep () {
      this.newArticle = this.$refs.stepOne.getStepOneInputs()
      console.dir(this.newArticle)
    }, 
    goForwardStep () {
      this.saveFirstStep()
      if (this.currentStep === 1) {
        this.currentStep = 2
        this.imgURL = 'two_three.png'
        this.buttonText = 'Tillbaka'
        this.backLink = '#'
      } else if (this.currentStep === 2) {
        this.currentStep = 3
        this.imgURL = 'three_three.png'
        this.buttonText = 'Tillbaka'
        this.backLink = '#'
        this.nextBtnText = 'Förhandsgranska'
      }
    },
    goBackStep () {
      if (this.currentStep === 2) {
        this.currentStep = 1
        this.imgURL = 'one_three.png'
        this.buttonText = 'Shop'
      } else if (this.currentStep === 3) {
        this.currentStep = 2
        this.imgURL = 'two_three.png'
        this.nextBtnText = 'Nästa'
      } else if (this.currentStep === 1) {
        this.backLink = '/Shop'
      }
    },
    changeHeader () {
      
    }
  }
}

</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Ubuntu:wght@700&display=swap');
 #input-form {
     margin: 0 auto;
     margin-top: 30px;
     width: 1000px;
     font-family: 'Ubuntu';
     height: 650px;
     position: relative;
  }

  @media (max-width: 1350px) {
      #input-form{
        width: 70%;
      }
  }

 @media (max-width: 900px) {
      #input-form{
          width: 80%;
      }
 }
   @media (max-width: 750px) {
      #input-form{
          width: 90%;
      }
   }

   @media (max-width: 540px) {
      #input-form{
          width: 100%;
      }
   }

</style>
