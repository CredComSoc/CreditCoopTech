<template>
  <div id="title-field" class="input" v-if="this.type === 'regular'">
    <lable :for="this.name" class="input-title"> {{ this.label }} </lable><br>
    <input type="text" :placeholder="this.placeholder" id="title" :name="this.name">
  </div>
  <div class="input" v-if="this.type === 'textarea'">
    <lable :for="this.name" class="input-title"> {{ this.label }} </lable><br>
    <textarea :placeholder="this.placeholder" id="description" :name="this.name"/>
  </div>
  <div class="input" v-if=" this.type === 'combobox'">
    <lable :for="this.name" class="input-title"> {{ this.label }}</lable><br>
    <select :id="this.name" :name="this.name" @click="handleSelect">
      <option value="" disabled selected hidden> {{ this.placeholder }} </option>
      <!-- <option v-for="option in this.options" :value="option" :key="option">{{ option }}</option> -->
    </select>
    <div class="dropdown">
      <div :id="this.name+'-dropdown'" class="dropdown-content">
        <p v-for="i in this.options" :key="i"> {{ i }} </p>
      </div>
    </div>    
  </div>
</template>

<script>
export default {
  name: 'UserInput',
  props: ['type', 'name', 'label', 'placeholder', 'options'],
  methods: {
    handleSelect () {
      const box = document.getElementById(this.name + '-dropdown')
      box.style.display = 'block'
    }
  }
}
</script>

<style>

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
  margin-top: 15px;
}

input {
  width: 318px;
  margin-top:10px;
  border-radius: 4px;
  border: 2px solid #5c5c5c;
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
  font-size: 13px;
  width: 420px;
}

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

</style>
