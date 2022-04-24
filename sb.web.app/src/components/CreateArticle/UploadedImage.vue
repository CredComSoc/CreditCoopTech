<template>
  <div class="img-container">
    <div id="delete" type="button" v-if="!this.isPreview" @click="this.removePic"> <img id="delete-img" src="../../assets/link_arrow/remove_img.png" /></div>
    <img id="uploaded-img" :src=this.imageURL />
    <br/>
    <input v-if="!this.isPreview || (this.isCoverImg && this.isPreview)" :checked="this.isCoverImg" class="cb" type="checkbox" :id="id" @click="clickCheckbox(id)" name="firstPic"/>
    <label v-if="!this.isPreview || (this.isCoverImg && this.isPreview)" for="firstPic"> {{ this.textboxLabel }} </label>
  </div>
</template>

<script>
export default {
  props: ['imageURL', 'break', 'id', 'textboxLabel', 'isCoverImg', 'isPreview'],
  methods: {
    clickCheckbox (id) {
      const cbs = document.getElementsByClassName('cb')
      const checked = document.getElementById(id)
      for (var i = 0; i < cbs.length; i++) {
        cbs[i].checked = false
      }
      checked.checked = true
    },
    removePic () {
      //const parent = document.getElementById(this.id).parentNode
      //parent.style.display = 'none'
      this.$emit('removeImg', this.id)
    }
  }
}
</script>

<style scoped>
    input{
        width:20px;
        height:15px;
        margin-top:5px;
    }

    label{
        font-size:14px;
        margin-left: 4px;
    }

    #uploaded-img{
        width:170px;
    }

    .img-container{
      position: relative;
      padding-top: 5px;
      padding-right: 5px;
    }
    #delete{
      background-color: white;
      border-radius: 100px;
      position: absolute;
      top: 0;
      right: 0;
      display: flex;
      width: 20px;
      height: 20px;
      border: 1px solid black;
    }

    #delete-img{
      width: 17px;
      height: 20px;
      position: absolute;
      margin: auto;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
    }
</style>
