<template>
<div id="title-field">
  <label :for="this.name" class="input-title"> {{ this.label }} </label>
  <div  class="input">
    <div id="pic">
        <p>Välj fil</p>
    </div>
    <button ref="addFile" id="upload-button" @click=upload>Bläddra</button>
    <input type='file' id="getFile" @change=getFile :name="this.name">
  </div>
  <div id="images"> 
    <UploadedImage @removeImg="this.deleteImg" class="img" textboxLabel="Välj som omslagsbild" :isPreview="false"
    v-for="(img) in this.images"
          :imageURL="img[0]"
          :key="img[0]"
          :id="img[1]"
          :isCoverImg="img[2]"
    />
  </div>
</div> 
</template>

<script>

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
      imageObjs: []
    }
  },
  methods: {
    getStepThreeInputs () {
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
    },
    upload () {
      document.getElementById('getFile').click()
    },
    getFile (e) {
      const imageObj = e.target.files[0]
      if (this.validateImageFile(imageObj) && this.validatedFileSize(imageObj.size)) {
        const URLImg = URL.createObjectURL(imageObj)
        this.$refs.addFile.innerText = 'Välj fler'
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
      if (this.imageObjs.length === 0) {
        this.$emit('emptyImageError')
        return false
      } else {
        const hasCoverImg = this.getStepThreeInputs().img.some((img) => img.isCoverImg === true)
        if (!hasCoverImg) {
          this.$emit('emptyCoverImage')
        }
        return hasCoverImg
      }
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
        this.$refs.addFile.innerText = 'Bläddra'
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
      this.$refs.addFile.innerText = 'Välj fler'
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
        this.$refs.addFile.innerText = 'Välj fler'
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
