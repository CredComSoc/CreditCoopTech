<template>
<div id="input-form">
  <CreateHeader :ButtonText="buttonText" :link="this.backLink" :imgURL="this.imgURL" @goBackStep="goBackStep" />
  <div id="center">
    <StepOne v-if="this.currentStep === 1" ref='stepOne' :savedProgress="this.newArticle" />
    <StepTwo v-if="this.currentStep === 2" ref='stepTwo' :chosenType="this.newArticle.article" :savedProgress="this.newArticle" @dateError="this.changePopupText(`Datumet är felaktigt.\nVar god ändra detta och försök igen.`)" @priceError="this.changePopupText(`Pris måste anges som ett positivt heltal.\nVar god ändra detta och försök igen.`)" />
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
import { uploadArticle, deletePost, deleteCart, EXPRESS_URL } from '../../serverFetch'

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
      popupCardText: 'Ett eller flera inmatningsfält har lämnats tomma.\n Var god fyll i dessa.',
      inEditMode: false
    }
  },
  created () {
    if (this.$route.params.artID) {
      fetch(EXPRESS_URL + '/post/' + this.$route.params.artID, {
        method: 'GET',
        credentials: 'include'
      }).then(
        success => {
          success.json().then(
            res => {
              this.newArticle = res
              // get print format from db object and assign to frontend
              this.newArticle.destination = res.printFormat.destination
              this.newArticle['end-date'] = res.printFormat['end-date']
              this.newArticle.status = res.printFormat.status
              this.newArticle.article = res.printFormat.article
              this.newArticle.category = res.printFormat.category
            }
          )
        }
      ).catch(
        error => console.log(error)
      )
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
        // if we edit a article
        if ('coverImg' in this.newArticle) {
          // delete old one from posts
          deletePost(this.newArticle.id, this.newArticle.imgIDs)
            .then(r1 => {
              // delete old one from all carts then upload
              if (r1.status === 200) {
                deleteCart(this.newArticle.id)
                  .then (r2 => {
                    if (r2.status === 200 || r2.status === 204) {
                      this.uploadArticle()
                    } else {
                      this.error = true
                      this.popupCardText = 'Något gick fel när artikeln skulle uppdateras.\nVar god försök igen senare.'
                    }
                  })
              } else {
                this.error = true
                this.popupCardText = 'Något gick fel när artikeln skulle uppdateras.\nVar god försök igen senare.'
              }
            }) 
        } else {
          this.uploadArticle()
        }
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
        if (res.status === 200) {
          this.isPublished = true // open popup with success message
        } else {
          this.error = true
          this.popupCardText = 'Något gick fel när artikeln skulle laddas upp.\nVar god försök igen senare.'
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
      // Frontend printing format for these fields to store in db
      const printFormat = {}
      printFormat.article = this.newArticle.article
      printFormat.destination = this.newArticle.destination
      printFormat.category = this.newArticle.category
      printFormat.status = this.newArticle.status
      printFormat['end-date'] = this.newArticle['end-date']
      this.newArticle.printFormat = printFormat 

      // sanitize the article field
      switch (this.newArticle.article) {
        case 'Produkt':
          this.newArticle.article = 'product'
          break
        case 'Tjänst':
          this.newArticle.article = 'service'
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

      this.newArticle.destination = this.setDbFormat(this.newArticle.destination)
      this.newArticle.category = this.setDbFormat(this.newArticle.category)
    },
    setDbFormat (field) {
      field = field.toLowerCase()

      field = field.replace(/\s/g, '')
      field = field.replace(/[åä]/g, 'a')
      field = field.replace(/ö/g, 'o')
    
      return field
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
