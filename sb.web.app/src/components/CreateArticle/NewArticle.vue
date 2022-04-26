<template>
<div id="input-form">
  <CreateHeader :ButtonText="buttonText" :link="this.backLink" :imgURL="this.imgURL" @goBackStep="goBackStep" />
  <div id="center">
    <StepOne v-if="this.currentStep === 1" ref='stepOne' :savedProgress="this.newArticle" />
    <StepTwo v-if="this.currentStep === 2" ref='stepTwo' :chosenType="this.newArticle.article" :savedProgress="this.newArticle" @dateError="this.changePopupText(`Datumet är felaktigt.\nVar god ändra detta och försök igen.`)" @priceError="this.changePopupText(`Pris måste anges som ett tal.\nVar god ändra detta och försök igen.`)" />
    <StepThree v-if="this.currentStep === 3" ref='stepThree' name="image-selector" label="Ladda upp bilder" :savedProgress="this.newArticle" @emptyImageError="this.changePopupText(`Minst en bild måste läggas till innan du kan gå vidare.`)" @emptyCoverImage="this.changePopupText(`En omslagsbild måste väljas innan du kan gå vidare.`)" @fileSizeError='this.fileSizeError' />
    <PreviewArticle v-if="this.currentStep === 4" ref='previewArticle' :savedProgress="this.newArticle" :isPublished="this.isPublished" />
  </div>
  <NewArticleFooter :buttonText="nextBtnText" @click="goForwardStep" />
  <PopupCard v-if="this.error" @closePopup="this.closePopup" btnText="Ok" title="Felaktig inmatning" :btnLink="null" :cardText="this.popupCardText" />
</div>
</template>

<script>
import CreateHeader from './CreateHeader.vue'
import StepOne from './StepOne.vue'
import StepTwo from './StepTwo.vue'
import StepThree from './StepThree.vue'
import NewArticleFooter from './NewArticleFooter.vue'
import PreviewArticle from './PreviewArticle.vue'
import PopupCard from './PopupCard.vue'
import { uploadArticle } from '../../serverFetch'

export default {
  name: 'NewArticle',
  components: {
    CreateHeader,
    StepOne,
    StepTwo,
    StepThree,
    PreviewArticle,
    NewArticleFooter,
    PopupCard
  },
  data () {
    return {
      backLink: '/Shop',
      currentStep: 1,
      imgURL: 'one_three.png',
      buttonText: 'Shop',
      nextBtnText: 'Nästa',
      newArticle: {},
      isPublished: false,
      error: false,
      popupCardText: 'Ett eller flera inmatningsfält har lämnats tomma.\n Var god fyll i dessa.'
    }
  },
  methods: {
    closePopup () {
      this.error = false
    },
    changePopupText (text) {
      this.popupCardText = text
    },
    fileSizeError () {
      this.error = true
      this.changePopupText('Filen måste vara en bild med filändelse .png, .jpeg eller .gif\noch ha mindre storlek än maxgränsen på 1MB.\nVar god försök igen.')
    },
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
        if (this.$refs.stepOne.validateStepOne()) {
          this.saveFirstStep()
          this.currentStep = 2
          this.imgURL = 'two_three.png'
          this.buttonText = 'Tillbaka'
          this.backLink = '#'
        } else {
          this.error = true
        }       
      } else if (this.currentStep === 2) {
        if (this.$refs.stepTwo.validateStepTwo()) {
          this.saveSecondStep()
          this.currentStep = 3
          this.imgURL = 'three_three.png'
          this.buttonText = 'Tillbaka'
          this.backLink = '#'
          this.nextBtnText = 'Förhandsgranska'
        } else {
          this.error = true
        }
      } else if (this.currentStep === 3) {
        if (this.$refs.stepThree.validateStepThree()) {
          this.saveThreeStep()
          this.currentStep = 4
          this.imgURL = null
          this.buttonText = 'Tillbaka'
          this.backLink = '#'
          this.nextBtnText = 'Publicera'
        } else {
          this.error = true
        }
      } else if (this.currentStep === 4) {
        this.addUploadDate()
        this.sanitizeArticle()
        this.uploadArticle()
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
    },
    uploadArticle () {
      const data = new FormData()
      let index = 0
      for (const file of this.newArticle.img) {
        if (file.isCoverImg) {
          data.append('coverImgInd', index)
        } 
        data.append('file', file, file.name)
        ++index
      }
      data.append('article', JSON.stringify(this.newArticle))
      // This will upload the article to the server
      uploadArticle(data).then((res) => {
        if (res.ok) {
          this.isPublished = true // open popup with success message
        }
      })
    },
    addUploadDate () {
      const options = {
        year: 'numeric', 
        month: '2-digit', 
        day: '2-digit'
      }
      this.newArticle.uploadDate = new Date().toLocaleString('sv-SE', options)
    },
    sanitizeArticle () {
      // sanitize the article field
      switch (this.newArticle.article) {
        case 'Produkt':
          this.newArticle.article = 'product'
          break
        case 'Tjänst':
          this.newArticle.article = 'service'
          break
      }
  
      // sanitize the destination field
      switch (this.newArticle.destination) {
        case 'Linköping':
          this.newArticle.destination = 'linkoping'
          break
        case 'Norrköping':
          this.newArticle.destination = 'norrkoping'
          break
        case 'Söderköping':
          this.newArticle.destination = 'soderkoping'
          break
      }
      // sanitize the category field
      switch (this.newArticle.category) {
        case 'Affärsutveckling & strategi':
          this.newArticle.category = 'affarsutveckling'
          break
        case 'Arbetsyta':
          this.newArticle.category = 'arbetsyta'
          break
        case 'Fotografering':
          this.newArticle.category = 'fotografering'
          break
        case 'Kök & restaurang':
          this.newArticle.category = 'restaurang'
          break
        case 'Marknadsföring':
          this.newArticle.category = 'marknadsforing'
          break
        case 'Rengöring & städ':
          this.newArticle.category = 'rengoring&stad'
          break
        case 'Skönhet':
          this.newArticle.category = 'skonhet'
          break
        case 'Sömnad & tyg':
          this.newArticle.category = 'somnad&tyg'
          break
      }
      // sanitize the status field
      switch (this.newArticle.status) {
        case 'Köpes':
          this.newArticle.status = 'buying'
          break
        case 'Säljes':
          this.newArticle.status = 'selling'
          break
      } 

      switch (this.newArticle['end-date']) {
        case null:
          delete this.newArticle['end-date']
          break
        default:
          // this.newArticle['end-date'] = '2022-04-22'
          this.newArticle['end-date'] += ' 24:00:00'
          //this.newArticle['end-date'] = new Date(this.newArticle['end-date'])
          break 
      }
    }
  }
}

</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Ubuntu:wght@700&display=swap');

 #input-form {
     margin: 0 auto;
     margin-top: 100px;
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
