<template>
    <div>
        <img :src=this.imageURL />
        <br/>
        <input type="checkbox" name="firstPic"/>
        <label for="firstPic">Välj som omslagsbild</label>
    </div>
    
</template>

<script>
export default {
  props: ['imageURL']
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
</style>
