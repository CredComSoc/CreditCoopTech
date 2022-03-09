<template>
<div>
  <div id="title-field" class="input" v-if="this.type === 'regular'">
    <lable :for="this.name" class="input-title"> {{ this.label }} </lable><br>
    <input type="text" :placeholder="this.placeholder" id="title" class="textbox" :name="this.name">
  </div>
  <div class="input" v-if="this.type === 'textarea'">
    <lable :for="this.name" class="input-title"> {{ this.label }} </lable><br>
    <textarea :placeholder="this.placeholder" class="textbox" id="description" :name="this.name"/>
  </div>
  <!--
  <div class="input" v-if=" this.type === 'combobox'">
    <lable :for="this.name" class="input-title"> {{ this.label }}</lable><br>
    <select :id="this.name" :name="this.name" @click="handleSelect">
      <option value="" disabled selected hidden> {{ this.placeholder }} </option>
      <option v-for="option in this.options" :value="option" :key="option">{{ option }}</option>
    </select>
    <div class="dropdown">
      <div :id="this.name+'-dropdown'" class="dropdown-content">
        <p v-for="i in this.options" :key="i"> {{ i }} </p>
      </div>
    </div>    
  </div>-->
</div>
</template>

<script>
export default {
  name: 'UserInput',
  props: ['type', 'name', 'label', 'placeholder', 'options']
}
</script>

<style scoped>

#title-field {
  padding-top: 50px;
}

.input-title {
  font-size: 24px;
  font-family: 'Ubuntu', sans-serif;
  font-weight: 700;
}

.input {
  margin-left: 80px;
  margin-top: 40px;
}

input:focus, #description:focus, select:focus {
  outline: none;     
  border-color: #719ECE;
  border-style: solid;     
  box-shadow: 0 0 10px #719ECE; 
}

input::placeholder, textarea::placeholder {
  padding-left:3px;
  color: #5c5c5c;
}

#title {
  height: 35px;
}

.textbox{
    width: 420px;
    font-size: 13px;
    font-family: 'Ubuntu';
    border-radius: 4px;
    border: 2px solid #5c5c5c;
}

#description{
  height: 100px;
  resize: none;
  margin-top: 10px;
}

select {
  width: 420px;
  height: 35px;
  margin-top:10px;
  border-radius: 4px;
  border: 2px solid #5c5c5c;
  font-family: 'Ubuntu';
  color: #5c5c5c;
  font-size: 13px;
  background-color: white;
}

.dropdown {
  position: relative;
}

.dropdown-content p{
  color:black;
  font-family: Roboto;
  font-weight: 300;
  font-size: 12px;
  font-style: normal;
  padding: 12px 16px;
  margin: 0;
  border-bottom: 1px solid #CBCACA;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #E5E5E5;
  width: 420px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}

 /* Style the links inside the navigation bar  */
.dropdown-content p:hover {
  background-color: #E5F0FD;
}

@media (max-width: 700px) {
  .textbox {
    width: 350px;
  }
}

@media (max-width: 620px) {
  .textbox {
    width: 250px;
    font-size: 12px;
  }
}

@media (max-width: 400px) {
  .textbox {
    width: 200px;
    font-size: 11px;
  }

  .input{
    margin-left: 40px;
  }
}

</style>
