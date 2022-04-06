<template>
        <div id="img-container">
          <img :src=this.imageURL />
          <br/>
          <input v-if="!this.isPreview || (this.isCoverImg && this.isPreview)" :checked="this.isCoverImg" class="cb" type="checkbox" :id="id" @click="clickCheckbox(id)" name="firstPic"/>
          <label v-if="!this.isPreview || (this.isCoverImg && this.isPreview)" for="firstPic"> {{ this.textboxLabel }} </label>
          <br/>
          <button @click="removePic()">Ta bort bild</button>
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
      const parent = document.getElementById(this.id).parentNode
      parent.remove()
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

    img{
        width:170px;
    }

/* @media (max-width: 470px) {
    img{
      width: 100px;
    }
  } */
</style>
