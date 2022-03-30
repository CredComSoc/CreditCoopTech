<template>
    <div>
      <div class="break" v-if="this.break"></div>
        <div>
          <img :src=this.imageURL />
          <br/>
          <input :checked="isCoverImg" class="cb" type="checkbox" :id="id" @click="clickCheckbox(id)" name="firstPic"/>
          <label for="firstPic">Välj som omslagsbild</label>
        </div>
    </div>
    
</template>

<script>
export default {
  props: ['imageURL', 'break', 'id', 'isCoverImg'],
  methods: {
    clickCheckbox (id) {
      const cbs = document.getElementsByClassName('cb')
      const checked = document.getElementById(id)
      for (var i = 0; i < cbs.length; i++) {
        cbs[i].checked = false
      }
      checked.checked = true
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

    .break{
        flex-basis: 100%;
        width: 0px; 
        height: 0px; 
        overflow: hidden;
    }

  @media (max-width: 470px) {
    img{
      width: 100px;
    }
  }
</style>
