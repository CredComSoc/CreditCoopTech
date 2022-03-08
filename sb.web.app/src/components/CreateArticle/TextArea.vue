<template>
  <label :for="this.name" class="input-title"> {{ this.label }} </label><br>
  <textarea :placeholder="this.placeholder" id="description" :name="this.name"/>
</template>

<script>
export default {
  name: 'TextArea',
  props: ['name', 'label', 'placeholder']
}
</script>

<style>

#description{
  height: 100px;
  width: 420px;
  resize: none;
  margin-top: 10px;
  border-radius: 4px;
  font-family: 'Ubuntu';
  font-size: 13px;
  border: 2px solid #5c5c5c;
}

textarea::placeholder {
  padding-left:3px;
  color: #5c5c5c;
}

#description:focus {
  outline: none;     
  border-color: #719ECE;
  border-style: solid;     
  box-shadow: 0 0 10px #719ECE; 
}

</style>
