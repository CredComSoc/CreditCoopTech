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
    <h3 :for="this.name" class="input-title"> {{ this.label }}</h3>
    <div :id="this.name" class="combobox" :name="this.name" @click="handleSelect(this.name)">
      <p :data-placeholder="this.selectedValue" :id="this.name + '-combo-placeholder'"></p>
      <img id="combo-arrow" src="../../assets/link_arrow/combobox-arrow-down.png" />
      <!-- <option value="" disabled selected hidden> {{ this.placeholder }} </option> -->
    </div>
    <div class="dropdown-combo">
      <div :id="this.name+'-dropdown'" class="dropdown-content-combo">
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
    handleSelect (id) {
      const box = document.getElementById(this.name + '-dropdown')
      const outerBox = document.getElementById(id)
      if (box.style.display === 'block') {
        box.style.display = 'none'
        outerBox.classList.remove('combobox-active')
      } else {
        box.style.display = 'block'
        outerBox.classList.add('combobox-active')
      }
    }
  },
  mounted () {
    this.selectedValue = this.placeholder
    if (this.type === 'combobox') {
      document.querySelectorAll('.dropdown-content-combo').forEach((list) => {
        for (const item of list.children) {
          item.addEventListener('click', () => {
            this.selectedValue = item.innerText
            item.parentNode.style.display = 'none'
            document.getElementById(this.name + '-combo-placeholder').style.color = 'black'
            document.getElementById(this.name).classList.remove('combobox-active')
          })
        }
      })
    }
  },
  data () {
    return { 
      selectedValue: ''
    }
  }
}
</script>

<style>

.combobox {
  width: 420px;
  height: 38px;
  background: white;
  border: 2px solid #5c5c5c;
  border-radius: 4px;
  position: relative;
  cursor: pointer;
}

p:empty:not(:focus)::before {
  content: attr(data-placeholder);
  margin-top: 5px;
  margin-left: 2px;
  font-family: 'Ubuntu';
  font-size: 13px;
  color: #acacac;
}

#combo-arrow {
  position: absolute;
  right: 2%;
  top: 50%;
}

.combobox-active {
  border-color: #719ECE;
  box-shadow: 0 0 10px #719ECE; 
}

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

input {
  width: 318px;
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

.dropdown-combo {
  position: relative;
}

.dropdown-content-combo p{
  color:black;
  font-family: 'Ubuntu';
  font-weight: 300;
  font-size: 12px;
  font-style: normal;
  padding: 12px 16px;
  margin: 0;
  border-bottom: 1px solid #CBCACA;
}

.dropdown-content-combo {
  display: none;
  position: absolute;
  background-color: #E5E5E5;
  width: 420px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}

 /* Style the links inside the navigation bar  */
.dropdown-content-combo p:hover {
  background-color: #E5F0FD;
}

@media (max-width: 700px) {
  .input {
    width: 350px;
  }
}

@media (max-width: 620px) {
  .input {
    width: 250px;
    font-size: 12px;
  }
}

@media (max-width: 400px) {
  .input {
    width: 250px;
    font-size: 10px;
    margin-left: 20px;
  }
}

</style>
