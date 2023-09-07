<template>
<div id="title-field">
  <label :for="this.name" class="input-title"> {{ this.label }} </label>
  <div  class="input">
    <div id="pic">
        <p>{{ $t('shop_items.choose_file') }}</p>
    </div>
    <button ref="addFile" id="upload-button" @click=upload>{{ $t('browse') }}</button>
    <input type='file' id="getFile" @change=getFile :name="this.name">
  </div>
  <div id="images"> 
    <UploadedImage @removeImg="this.deleteImg" class="img" :textboxLabel="$t('shop_items.item_set_main_image')" :isPreview="false"
    v-for="(img) in this.images"
          :imageURL="img[0]"
          :key="img[0]"
          :id="img[1]"
          :isCoverImg="img[2]"
    />
  </div>
  <div id="images_default" v-if="this.imageObjs.length == 0"> 
    <h3>{{ $t('default_images') }}</h3>
    <UploadedImage  class="img" :textboxLabel="$t('shop_items.item_set_main_image')" :isPreview="true"
    v-for="(img) in this.imagesDefault"
          :imageURL="img[0]"
          :key="img[0]"
          :id="img[1]"
          :isCoverImg="img[2]"
    />
  </div>
</div> 
</template>

<script>
/* eslint-disable */
import UploadedImage from './UploadedImage.vue'
import { EXPRESS_URL, getImg } from '../../serverFetch'

export default {
  name: 'StepThree',
  components: {
    UploadedImage
  },
  props: ['name', 'label', 'savedProgress'],
  data () {
    return {
      images: [],
      imageObjs: [],
      imagesDefault: []
    }
  },
  methods: {
    getStepThreeInputs () {
      if(this.imageObjs.length>0)
      {
        const cbs = document.getElementsByClassName('cb')
      for (var i = 0; i < cbs.length; i++) {
        if (cbs[i].checked) {
          this.imageObjs[i].isCoverImg = true
        } else {
          this.imageObjs[i].isCoverImg = false
        }
      }
      return { 
        img: this.imageObjs
      }
      }

    },
    upload () {
      document.getElementById('getFile').click()
    },
    getFile (e) {
      const imageObj = e.target.files[0]
      if (this.validateImageFile(imageObj) && this.validatedFileSize(imageObj.size)) {
        const URLImg = URL.createObjectURL(imageObj)
        this.$refs.addFile.innerText = 'Choose more'
        this.images.push([URLImg, this.images.length, false])
        this.imageObjs.push(imageObj)
        if (this.images.length === 5) {
          document.getElementById('upload-button').disabled = true
        }
      } else {
        this.$emit('fileSizeError')
      }
    },
    validateStepThree () {
      return true
    },
    deleteImg (imgId) {
      for (let i = imgId; i < this.images.length; i++) {
        if (imgId !== i) {
          this.images[i][1] = i - 1
        }
      }
      this.images.splice(imgId, 1)
      this.imageObjs.splice(imgId, 1)
      if (this.images.length === 0) {
        this.$refs.addFile.innerText = this.$i18n.t('browse')
      } 

      if (this.images.length < 5) {
        document.getElementById('upload-button').disabled = false
      }
    },
    // less then 2MB
    validatedFileSize (byteSize) {
      return byteSize <= 2000000
    },
    validateImageFile (file) {
      const validImageTypes = ['image/gif', 'image/jpeg', 'image/png']
      return validImageTypes.includes(file.type)
    },
    displayImg () {
      this.$refs.addFile.innerText = 'Choose more'
      for (const img of this.savedProgress.img) {
        const URLImg = URL.createObjectURL(img)
        this.images.push([URLImg, this.images.length, img.isCoverImg])
        this.imageObjs.push(img)

        if (this.images.length === 5) {
          document.getElementById('upload-button').disabled = true
        }
      }
    }
  },
  mounted () {
    // in edit mode
    console.log(this.l=this.imageObjs.length == 0)
    if ('coverImg' in this.savedProgress) {
      if (this.savedProgress.img.length > 0) {
        if (typeof this.savedProgress.img[0] === 'object') {
          this.displayImg()
          return
        }
      }
      getImg(this.savedProgress.coverImg).then((res) => {
        if (res.ok) {
          return res.blob()
        }
      }).then(data => {
        const URLImg = URL.createObjectURL(data)
        this.imageObjs.push(new File([data], this.savedProgress.coverImg, { type: 'image/' + this.savedProgress.coverImg.split('.').pop() }))
        this.images.push([URLImg, this.images.length, true])
        this.$refs.addFile.innerText = 'Choose more'
      })
      // multiple images uploaded
      if ('img' in this.savedProgress) {
        for (const img of this.savedProgress.img) {
          getImg(img).then((res) => {
            if (res.ok) {
              return res.blob()
            }
          }).then(data => {
            const URLImg = URL.createObjectURL(data)
            this.imageObjs.push(new File([data], img, { type: 'image/' + img.split('.').pop() }))
            this.images.push([URLImg, this.images.length, false])
            if (this.images.length === 5) {
              document.getElementById('upload-button').disabled = true
            }
          })
        }
      }     
    } else if ('img' in this.savedProgress) { // not in edit mode
      this.displayImg()
    } else {
      let categorySelected = this.savedProgress.categories.filter(el => el.name == this.savedProgress.category)[0]
      getImg(categorySelected.defaultMainImage).then((res) => {
        if (res.ok) {
          return res.blob()
        }
      }).then(data => {
        const URLImg = URL.createObjectURL(data)
        this.imagesDefault.push([URLImg, this.images.length, true])
      })
      // multiple images uploaded
      if ('defaultImage' in categorySelected) {
        for (const img of categorySelected.defaultImage) {
          getImg(img).then((res) => {
            if (res.ok) {
              return res.blob()
            }
          }).then(data => {
            const URLImg = URL.createObjectURL(data)
            this.imagesDefault.push([URLImg, this.images.length, false])
          })
        }
      } 
    }
  }
}
</script>

<style scoped>

 #title-field {
  margin-top: 40px;
  display: flex;
  flex-direction: column;
  width: fit-content;
}

p{
    margin-left: 3px;
    color:#bebebe;
    font-family: 'Ubuntu';
    font-size: 14px;
}

.input-title {
  font-size: 24px;
  font-family: 'Ubuntu', sans-serif;
  font-weight: 700;
  margin-bottom: 10px;
}

#pic{
    height: 35px;
    width: 400px;
    border: 2px solid #797979;
    border-radius: 4px 0px 0px 4px;
    border-right: white;
    line-height: 35px;
    white-space: nowrap;
}

#images{
    display: flex;
    flex-direction: row;
    align-items: flex-end;
    gap: 40px;
    flex-wrap: wrap;
    max-width: 480px;
}
#images_default
{
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 40px;
  flex-wrap: wrap;
  max-width: 480px;
  margin-top: 30%;
}

input{
    display:none;
}

button{
    display:block;
    width:80px; 
    height:35px;
    border-radius: 0px 4px 4px 0px;
    background-color: rgb(236, 236, 236) ;
    border: 2px solid #797979; 
    font-size: 15px;
    font-family: 'Ubuntu';
}

.input {
  margin-top: 20px;
  display: flex;
  margin-bottom: 20px;
}

@media (max-width: 470px) {
  .input{
    width: 300px;
  }
  #images{
    width: 300px;
  }
  button{
    font-size: 12px;
  }
}

@media (max-width: 550px) {
  #pic {
    width: 350px;
  }
  #images{
    max-width: 430px;
  }
}

</style>
