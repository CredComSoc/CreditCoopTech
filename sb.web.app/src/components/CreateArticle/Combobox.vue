<template>
   <h3 :for="this.name" class="input-title"> {{ this.label }}</h3>
    <div tabindex="0" :id="this.name" class="combobox" :name="this.name" @click="handleSelect(this.name)">
      <p unselectable="on" :data-placeholder="this.selectedValue" :id="this.name + '-combo-placeholder'"></p>
      <img id="combo-arrow" src="../../assets/link_arrow/combobox-arrow-down.png" />
    </div>
    <div class="dropdown-combo">
      <div :id="this.name + '-dropdown'" class="dropdown-content-combo">
        <p unselectable="on" v-for="i in this.options" :key="i"> {{ i }} </p>
      </div>
    </div>  
</template>

<script>
export default {
  name: 'Combobox',
  props: ['name', 'label', 'options', 'placeholder'],
  methods: {
    handleSelect (id) {
      const box = document.getElementById(this.name + '-dropdown')
      const outerBox = document.getElementById(id)
      if (box.style.display === 'block') {
        box.style.display = 'none'
        outerBox.classList.remove('combobox-active')
        outerBox.blur()
      } else {
        box.style.display = 'block'
        outerBox.classList.add('combobox-active')
      }
    }
  },
  mounted () {
    this.selectedValue = this.placeholder
    const list = document.getElementById(this.name + '-dropdown')
    for (const item of list.childNodes) {
      item.addEventListener('click', () => {
        item.parentNode.style.display = 'none'
        const selectedVal = document.getElementById(this.name + '-combo-placeholder')
        selectedVal.innerText = item.innerText
        selectedVal.style.color = 'black'
        document.getElementById(this.name).classList.remove('combobox-active')
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

.combobox p {
  margin-top: 5px;
  margin-left: 2px;
  font-family: 'Ubuntu';
  font-size: 13px;
}

.combobox p, .dropdown-content-combo p {
  -webkit-user-select: none; /* Safari */        
  -moz-user-select: none; /* Firefox */
  -ms-user-select: none; /* IE10+/Edge */
  user-select: none; /* Standard */
}

p:empty:not(:focus)::before {
  content: attr(data-placeholder);
  color: #acacac;
}

#combo-arrow {
  position: absolute;
  right: 2%;
  top: 50%;
}

.combobox-active, .combobox:focus {
  border-color: #719ECE;
  box-shadow: 0 0 10px #719ECE; 
  outline: none;
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
  padding: 12px 0px 12px 5px;
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

</style>
