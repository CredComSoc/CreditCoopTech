<template>
  <div>
  <div>
    <h2 v-if="!this.$route.path.includes('edit')" class="center-text">{{ $t('shop_items.new_article') }}</h2>
    <h2 v-else class="center-text">{{ $t('shop_items.edit_article') }}</h2>
  </div>
  <div id="input-form">
    <div v-if="this.currentStep !== 1" id="create-header" >
      <img v-if="imgURL !== null" class="step-indicator-img" :src="require(`../../assets/link_arrow/${this.imgURL}`)" />
      <a href="#" @click=goBackStep><img class="left-arrow" src="../../assets/link_arrow/left_arrow_link.png"/>{{ $t('Back') }}</a>
    </div>
    <div id="center">
      <StepOne v-if="this.currentStep === 1" ref='stepOne' :savedProgress="this.newArticle" />
      <StepTwo v-if="this.currentStep === 2" ref='stepTwo' :chosenType="this.newArticle.article" :savedProgress="this.newArticle" @dateError="this.changePopupText(this.invalid_date_message)" @priceError="this.changePopupText(this.invalid_price_message)" />
      <StepThree v-if="this.currentStep === 3" ref='stepThree' name="image-selector" :label="$t('shop_items.upload_images')" :savedProgress="this.newArticle" @emptyImageError="this.changePopupText($t('shop_items.at_least_one_image'))" @emptyCoverImage="this.changePopupText($t('shop_items.choose_main_image'))" @fileSizeError='this.fileSizeError' />
      <PreviewArticle v-if="this.currentStep === 4" ref='previewArticle' :savedProgress="this.newArticle" :isPublished="this.isPublished" />
    </div>
    <NewArticleFooter :buttonText="nextBtnText" @click="goForwardStep" />
    <PopupCard v-if="this.error" @closePopup="this.closePopup" btnText="Ok" :title="$t('shop_items.invalid_entry')" :btnLink="null" :cardText="this.popupCardText" />
  </div>
  </div>
</template>

<script>
import StepOne from './StepOne.vue'
import StepTwo from './StepTwo.vue'
import StepThree from './StepThree.vue'
import NewArticleFooter from './NewArticleFooter.vue'
import PreviewArticle from './PreviewArticle.vue'
import PopupCard from '@/components/SharedComponents/PopupCard.vue'
import { uploadArticle, editArticle, deleteCart, EXPRESS_URL } from '../../serverFetch'

import { isProxy, toRaw } from 'vue'

export default {
  name: 'NewArticle',
  components: {
    StepOne,
    StepTwo,
    StepThree,
    PreviewArticle,
    NewArticleFooter,
    PopupCard
  },
  data () {
    return {
      backLink: '#',
      currentStep: 1,
      imgURL: 'one_three.png',
      nextBtnText: this.$i18n.t('Next'),
      newArticle: {},
      isPublished: false,
      error: false,
      popupCardText: this.$i18n.t('shop_items.fields_left_empty') + '\n' + this.$i18n.t('shop_items.fill_them_out'),
      invalid_date_message: this.$i18n.t('shop_items.invalid_date') + '\n' + this.$i18n.t('shop_items.try_again'),
      file_size_error_message: this.$i18n.t('shop_items.image_file_extension_must_be') + '\n' + this.$i18n.t('shop_items.smaller_than_2mb'),
      image_upload_error_message: this.$i18n.t('shop_items.image_upload_failed') + '\n' + this.$i18n.t('shop_items.try_again_later'),
      invalid_price_message: this.$i18n.t('shop_items.price_positive_integer') + '\n' + this.$i18n.t('shop_items.try_again'),
      inEditMode: false
    }
  },
  created () {
    if (this.$route.params.artID) {
      fetch(EXPRESS_URL + '/article/' + this.$route.params.artID, {
        method: 'GET',
        credentials: 'include'
      }).then(
        success => {
          success.json().then(
            res => {
              if (res.listing) {
                this.newArticle = res.listing
              } else {
                this.newArticle = res
              }

              // get print format from db object and assign to frontend
              if (typeof res.printFormat !== 'undefined') {
                this.newArticle.destination = res.printFormat.destination
                this.newArticle['end-date'] = res.printFormat['end-date']
                this.newArticle.status = res.printFormat.status
                this.newArticle.article = res.printFormat.article
                this.newArticle.category = res.printFormat.category
              } else {
                this.newArticle.destination = res.listing.destination
                this.newArticle['end-date'] = res.listing['end-date']
                this.newArticle.status = res.listing.status === 'buying' ? 'want' : res.listing.status === 'selling' ? 'offer' : res.listing.status
                this.newArticle.article = res.listing.article
                this.newArticle.category = res.listing.category
              }
            }
          )
        }
      ).catch(
        error => console.log('Error: ', error)
      )
    } else {
      console.log('Param artID not found!')
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
      this.changePopupText(this.file_size_error_message)
    },
    saveFirstStep () {
      this.newArticle = { ...this.newArticle, ...this.$refs.stepOne.getStepOneInputs() }
      //console.dir(this.newArticle)
    }, 
    saveSecondStep () {
      this.newArticle = { ...this.newArticle, ...this.$refs.stepTwo.getStepTwoInputs() }
      //console.dir(this.newArticle)
    },
    saveThreeStep () {
      this.newArticle = { ...this.newArticle, ...this.$refs.stepThree.getStepThreeInputs() }
      //console.dir(this.newArticle)
    },
    goForwardStep () {
      if (this.currentStep === 1) {
        if (this.$refs.stepOne.validateStepOne()) {
          this.saveFirstStep()
          this.currentStep = 2
          this.imgURL = 'two_three.png'
        } else {
          this.error = true
        }       
      } else if (this.currentStep === 2) {
        if (this.$refs.stepTwo.validateStepTwo()) {
          this.saveSecondStep()
          this.currentStep = 3
          this.imgURL = 'three_three.png'
          this.nextBtnText = this.$i18n.t('shop_items.preview')
        } else {
          this.error = true
        }
      } else if (this.currentStep === 3) {
        if (this.$refs.stepThree.validateStepThree()) {
          this.saveThreeStep()
          this.currentStep = 4
          this.imgURL = null
          this.nextBtnText = this.$i18n.t('shop_items.publish') //'Publicera'
        } else {
          this.error = true
        }
      } else if (this.currentStep === 4) {
        this.addUploadDate()
        this.sanitizeArticle()
        if (this.$route.path.includes('edit')) {
          this.editArticle()
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
        this.nextBtnText = this.$i18n.t('next')
      } else if (this.currentStep === 3) {
        this.saveThreeStep()
        this.currentStep = 2
        this.imgURL = 'two_three.png'
        this.nextBtnText = 'Next'
      } else if (this.currentStep === 4) {
        this.currentStep = 3
        this.imgURL = 'three_three.png'
        this.nextBtnText = this.$i18n.t('shop_items.preview')
      }
    },
    uploadArticle () {
      const createdArticle = isProxy(this.newArticle) ? toRaw(this.newArticle) : this.newArticle
      const data = new FormData()
      let index = 0
      for (const file of createdArticle.img) {
        if (file.isCoverImg) {
          data.append('coverImgInd', index)
        } 
        data.append('file', file, file.name)
        ++index
      }
      data.append('article', JSON.stringify(createdArticle))
      // This will upload the article to the server
      uploadArticle(data).then((res) => {
        if (res.status === 200) {
          this.isPublished = true // open popup with success message
        } else {
          console.error(res)
          this.error = true
          this.popupCardText = this.image_upload_error_message
        }
      })
    },
    editArticle () {
      const newdata = new FormData()
      let index = 0
      for (const file of this.newArticle.img) {
        if (file.isCoverImg) {
          newdata.append('coverImgInd', index)
        }
        newdata.append('file', file, file.name)
        ++index
      }
      newdata.append('article', JSON.stringify(this.newArticle))

      editArticle(this.$route.params.artID, newdata).then((res) => {
        if (res.status === 200 || res.status === '200') {
          //console.log('Item edit successful')
          this.$router.push({ path: '/shop' })
        } else {
          console.error('Edit unsuccessful ', res.status, res)
        }
      }).catch((error) => {
        console.error('Error: ', error)
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
        case 'product':
          this.newArticle.article = 'product'
          break
        case 'service':
          this.newArticle.article = 'service'
          break
      }

      // sanitize the status field
      switch (this.newArticle.status) {
        case 'Want':
          this.newArticle.status = 'want'
          break
        case 'Offer':
          this.newArticle.status = 'offer'
          break
      } 

      const now = new Date()
      switch (this.newArticle['end-date']) {
        case null: 
          this.newArticle['end-date'] = new Date(now.getFullYear() + 100, now.getMonth(), now.getDate())
          break
        default:
          this.newArticle['end-date'] += ' 24:00:00'
          break 
      }
    }
  }
}

</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Ubuntu:wght@700&display=swap');

  
.center-text {
  text-align: center;
  margin-top: 4rem;
  margin-bottom: 0rem;
  font-size: 2.2rem;
  letter-spacing: 0.3em;
  text-transform: uppercase;
}

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

  #create-header {
  width: 100%;
}

  .left-arrow{
    width: 12px;
    margin-left: 2px;
  }

  .step-indicator-img {
    width: 80px;
    float: right;
  }

  #create-header a{
    float: left;
    color: black;
    font-size: 22px;
  }

  #create-header a:hover{
    color: black;
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

  @media (max-width: 400px) {
    a{
      font-size: 18px;
    }

  }
</style>
