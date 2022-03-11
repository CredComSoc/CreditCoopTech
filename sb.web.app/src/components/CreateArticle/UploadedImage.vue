<template>
    <div>
        <img src="../../../public/städning.png" />
        <br/>
        <input type="checkbox" name="firstPic"/>
        <lable for="firstPic">Välj som omslagsbild</lable>
    </div>
    
</template>

<script>
export default {
  props: ['image']
}
</script>

<style scoped>
    input{
        width:20px;
        height:15px;
        margin-top:5px;
    }
    lable{
        font-size:14px;
        margin-left: 4px;
    }
</style>
