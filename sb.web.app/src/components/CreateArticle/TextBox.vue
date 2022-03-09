<template>
  <div>
    <label :for="this.name" class="input-title"> {{ this.label }} </label><br>
    <input type="text" :placeholder="this.placeholder" :id="this.id" :name="this.name">
  </div>
</template>

<script>
export default {
  name: 'TextBox',
  props: ['id', 'name', 'label', 'placeholder']
}
</script>

<style scoped>

.input-title {
  font-size: 24px;
  font-family: 'Ubuntu', sans-serif;
  font-weight: 700;
  margin-bottom: 10px;
}

input {
  border-radius: 4px;
  border: 2px solid #5c5c5c;
  height: 35px;
  font-size: 13px;
  width: 420px;
  font-family: 'Ubuntu';
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

@media (max-width: 700px) {
  input {
    width: 350px;
  }
}

@media (max-width: 620px) {
  input {
    width: 250px;
    font-size: 12px;
  }
}

@media (max-width: 400px) {
  input {
    width: 200px;
    font-size: 11px;
  }
}

</style>
