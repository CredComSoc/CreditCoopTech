<template>
    <div>
        <h1>Add new category</h1>
    </div>
    <div id="main">
      <div class="">
        <input ref="name" placeholder="Category Name" type="text" v-model="name"  id="name" />
      <br/>
      </div>
      <div class="input-upload">
        <div id="pic">
        <p>{{ $t('shop_items.choose_file') }}</p>
    </div>
        <button ref="addFile" id="upload-button" @click="upload">
          {{ $t("browse") }}
        </button>
        <input type="file" id="getFile"  @change="getFile" style="display: none;" :name="this.name" />
      </div>
      <div id="images">
        <UploadedImage
          @removeImg="this.deleteImg"
          class="img"
          :isPreview="false"
          v-for="img in this.images"
          :imageURL="img[0]"
          :key="img[0]"
          :id="img[1]"
          :isCoverImg="img[2]"
        />
      </div>
      <button class="btn-submit" @click="saveCategories">Submit</button>
      <!-- change to locale -->
    </div>
    <PopupCard v-if="this.error" @closePopup="this.closePopup" btnText="Ok" :title="$t('shop_items.invalid_entry')" :btnLink="null" :cardText="this.popupCardText" />

  </template>
  
<script>
/* eslint-disable */
import UploadedImage from "../CreateArticle/UploadedImage.vue"
import { createNewCategories } from '../../serverFetch'
import { useRouter } from 'vue-router'
import PopupCard from '@/components/SharedComponents/PopupCard.vue'

const router = useRouter()

  export default {
    name: "CreateNewCategories",
    components: {
      UploadedImage,
      PopupCard
    },
    props: [],
    data() {
      return {
        error: false,
        popupCardText: 'Error While Saving New Category',
        name: '',
        images: [],
        imageObjs: [],
      };
    },
    methods: {
        closePopup () {
      this.error = false
         },
      getStepThreeInputs() {
        const cbs = document.getElementsByClassName("cb");
        for (var i = 0; i < cbs.length; i++) {
          if (cbs[i].checked) {
            this.imageObjs[i].isCoverImg = true;
          } else {
            this.imageObjs[i].isCoverImg = false;
          }
        }
      },
      upload() {
        document.getElementById("getFile").click();
      },
      getFile(e) {
        const imageObj = e.target.files[0];
        if (
          this.validateImageFile(imageObj) &&
          this.validatedFileSize(imageObj.size)
        ) {
          const URLImg = URL.createObjectURL(imageObj);
          this.images.push([URLImg, this.images.length, false]);
          this.imageObjs.push(imageObj);
          if (this.images.length === 5) {
            document.getElementById("upload-button").disabled = true;
          }
        } else {
          this.$emit("fileSizeError");
        }
      },
      deleteImg(imgId) {
        for (let i = imgId; i < this.images.length; i++) {
          if (imgId !== i) {
            this.images[i][1] = i - 1;
          }
        }
        this.images.splice(imgId, 1);
        this.imageObjs.splice(imgId, 1);
        if (this.images.length === 0) {
          this.$refs.addFile.innerText = this.$i18n.t("browse");
        }
  
        if (this.images.length < 5) {
          document.getElementById("upload-button").disabled = false;
        }
      },
      // less then 2MB
      validatedFileSize(byteSize) {
        return byteSize <= 2000000;
      },
      validateImageFile(file) {
        const validImageTypes = ["image/gif", "image/jpeg", "image/png"];
        return validImageTypes.includes(file.type);
      },
      saveCategories() {
        this.getStepThreeInputs();
        const data = new FormData();
        let index = 0;
        for (const file of this.imageObjs) {
          if (file.isCoverImg) {
            data.append("coverImgInd", index)
          }
          data.append("file", file, file.name)
          ++index;
        }
        data.append('name', this.name)
        createNewCategories(data).then((res) => {
        if (res.status === 200) {
            this.$router.push({ name: 'AdminHome' }) 
        } else {
            this.error = true
        }
      })
      }
    },
    mounted() {}
  };
  </script>
  
  <style scoped>
  #main {
  border: 2px solid #797979;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  height: fit-content;
  width: fit-content;
  padding: 10px;
  margin: auto;
  font-family: Arial, sans-serif;
  }
  
  p {
    margin-left: 3px;
    color: #bebebe;
    font-family: "Ubuntu";
    font-size: 14px;
  }
  
  .input-title {
    font-size: 24px;
    font-family: "Ubuntu", sans-serif;
    font-weight: 700;
    margin-bottom: 10px;
  }
  
  #pic {
    height: 35px;
    width: 400px;
    border: 2px solid #797979;
    border-radius: 4px 0px px 4px;
    border-right: white;
    line-height: 35px;
    white-space: nowrap;
  }

  #name {
    height: 35px;
    width: 400px;
    border: 2px solid #797979;
    border-radius: 4px 4px 4px 4px;
    line-height: 35px;
    white-space: nowrap;
  }
  
  #images {
    display: flex;
    flex-direction: row;
    align-items: flex-end;
    gap: 40px;
    flex-wrap: wrap;
    max-width: 480px;
  }
  
  button {
    display: block;
    width: 80px;
    height: 35px;
    background-color: rgb(236, 236, 236);
    border: 2px solid #797979;
    font-size: 15px;
    background-color: #008CBA;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-family: "Ubuntu";
  }
  

  .input-upload {
    margin-top: 20px;
    display: flex;
    display: flex;
    margin-bottom: 20px;
  }
  .submit-button {
  padding: 10px 20px;
  background-color: #008CBA;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

button:hover {
  background-color: #007B9A;
}
.btn-submit
{
  justify-content: right;
  align-items: right;
}
  
  @media (max-width: 470px) {
    .input {
      width: 300px;
    }
  
    #images {
      width: 300px;
    }
  
    button {
      font-size: 12px;
    }
  }
  
  @media (max-width: 550px) {
    #pic {
      width: 350px;
    }
  
    #images {
      max-width: 430px;
    }
  }
  </style>
  