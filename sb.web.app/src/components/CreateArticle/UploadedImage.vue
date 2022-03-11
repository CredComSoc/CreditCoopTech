<template>
    <div>
      <div class="break" v-if="this.break"></div>
        <div>
          <img :src=this.imageURL />
          <br/>
          <input type="checkbox" :id="id" @click="checkCheckbox(id)" name="firstPic"/>
          <label for="firstPic">Välj som omslagsbild</label>
        </div>
    </div>
    
</template>

<script>
export default {
  props: ['imageURL', 'break', 'id'],
  methods: {
    checkCheckbox (id) {
      console.log(id)
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
</style>
