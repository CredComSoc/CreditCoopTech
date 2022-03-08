<template>
  <label :for="this.name" class="input-title"> {{ this.label }} </label><br>
  <input type="text" :placeholder="this.placeholder" :id="this.id" :name="this.name">
</template>

<script>
export default {
  name: 'TextBox',
  props: ['id', 'name', 'label', 'placeholder']
}
</script>

<style>

input {
  width: 318px;
  border-radius: 4px;
  border: 2px solid #5c5c5c;
}

#title {
  height: 35px;
  font-size: 13px;
  width: 420px;
}

input:focus {
  outline: none;     
  border-color: #719ECE;
  border-style: solid;     
  box-shadow: 0 0 10px #719ECE; 
}

input::placeholder {
  padding-left:3px;
  color: #5c5c5c;
}

</style>
