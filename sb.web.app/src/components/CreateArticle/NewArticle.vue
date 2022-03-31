<template>
<div id="input-form">
  <CreateHeader :ButtonText="buttonText" :link="this.backLink" :imgURL="this.imgURL" @goBackStep=goBackStep />
  <div id="center">
    <StepOne v-if="this.currentStep === 1" ref='stepOne' :savedProgress="this.newArticle" />
    <StepTwo v-if="this.currentStep === 2" ref='stepTwo' :chosenType="this.newArticle.type" :savedProgress="this.newArticle" />
    <StepThree v-if="this.currentStep === 3" ref='stepThree' name="image-selector" label="Ladda upp bilder" :savedProgress="this.newArticle"/>
    <PreviewArticle v-if="this.currentStep === 4" ref='previewArticle' :savedProgress="this.newArticle" :isPublished="this.isPublished" />
  </div>
  <NewArticleFooter :ButtonText="nextBtnText" @click=goForwardStep />
</div>
</template>

<script>
import CreateHeader from './CreateHeader.vue'
import StepOne from './StepOne.vue'
import StepTwo from './StepTwo.vue'
import StepThree from './StepThree.vue'
import NewArticleFooter from './NewArticleFooter.vue'
import PreviewArticle from './PreviewArticle.vue'

export default {
  name: 'NewArticle',
  components: {
    CreateHeader,
    StepOne,
    StepTwo,
    StepThree,
    PreviewArticle,
    NewArticleFooter
  },
  data () {
    return {
      backLink: '/Shop',
      currentStep: 1,
      imgURL: 'one_three.png',
      buttonText: 'Shop',
      nextBtnText: 'Nästa',
      newArticle: {},
      isPublished: false
    }
  },
  methods: {
    saveFirstStep () {
      this.newArticle = { ...this.newArticle, ...this.$refs.stepOne.getStepOneInputs() }
      console.dir(this.newArticle)
    }, 
    saveSecondStep () {
      this.newArticle = { ...this.newArticle, ...this.$refs.stepTwo.getStepTwoInputs() }
      console.dir(this.newArticle)
    },
    saveThreeStep () {
      this.newArticle = { ...this.newArticle, ...this.$refs.stepThree.getStepThreeInputs() }
      console.dir(this.newArticle)
    },
    goForwardStep () {
      if (this.currentStep === 1) {
        this.saveFirstStep()
        this.currentStep = 2
        this.imgURL = 'two_three.png'
        this.buttonText = 'Tillbaka'
        this.backLink = '#'
      } else if (this.currentStep === 2) {
        this.saveSecondStep()
        this.currentStep = 3
        this.imgURL = 'three_three.png'
        this.buttonText = 'Tillbaka'
        this.backLink = '#'
        this.nextBtnText = 'Förhandsgranska'
      } else if (this.currentStep === 3) {
        this.saveThreeStep()
        this.currentStep = 4
        this.imgURL = null
        this.buttonText = 'Tillbaka'
        this.backLink = '#'
        this.nextBtnText = 'Publicera'
      } else if (this.currentStep === 4) {
        this.isPublished = true
      }
    },
    goBackStep () {
      if (this.currentStep === 2) {
        this.saveSecondStep()
        this.currentStep = 1
        this.imgURL = 'one_three.png'
        this.buttonText = 'Shop'
      } else if (this.currentStep === 3) {
        this.saveThreeStep()
        this.currentStep = 2
        this.imgURL = 'two_three.png'
        this.nextBtnText = 'Nästa'
      } else if (this.currentStep === 4) {
        this.currentStep = 3
        this.imgURL = 'three_three.png'
        this.nextBtnText = 'Förhandsgranska'
      } else if (this.currentStep === 1) {
        this.backLink = '/Shop'
      }
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
     position: relative;
  }

  #center{
    clear: both;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    min-height: 450px;
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
