<template>
  <div>
   <h3 :for="this.name" class="input-title"> {{ this.label }}</h3>
    <div v-if="!this.isDatePicker" tabindex="0" :id="this.name" class="combobox" :name="this.name" @click="handleSelect(this.name)" >
      <p unselectable="on" :data-placeholder="placeholder" :id="this.name + '-combo-placeholder'"></p>
      <img id="combo-arrow" src="../../assets/link_arrow/combobox-arrow-down.png" />
    <!-- <input :id="this.name + `-date-picker`" ref="dateVal" v-if="this.isDatePicker" class="date-picker" name="date" type="date" @change=setDate> -->
    </div>
    <div v-if="!this.isDatePicker" class="dropdown-combo">
      <div :id="this.name + '-dropdown'" class="dropdown-content-combo">
        <p unselectable="on" v-for="i in this.options" :key="i"> {{ i }} </p>
      </div>
    </div>
    <input required v-if="this.isDatePicker" :id="this.name + `-date-picker`" ref="dateVal" :placeholder="this.placeholder" onfocus="(this.type = 'date')" class="date-picker" name="date" type="text" @change=setDate>
  </div>
</template>

<script>
export default {
  name: 'Combobox',
  props: ['name', 'label', 'options', 'placeholder', 'isDatePicker'],
  emits: ['clearNoEndDateCheckbox'],
  methods: {
    handleSelect (id) {
      const outerBox = document.getElementById(id)
      if (!this.isDatePicker) {
        const box = document.getElementById(this.name + '-dropdown')
        if (box.style.display === 'block') {
          box.style.display = 'none'
          outerBox.classList.remove('combobox-active')
          outerBox.blur()
        } else {
          box.style.display = 'block'
          outerBox.classList.add('combobox-active')
        }
      } else {
        const datePicker = document.getElementById(this.name + '-date-picker')
        datePicker.click()
      }
    },
    getInput () {
      return this.selectedValue
    },
    setDate () { 
      this.$refs.dateVal.type = 'text'
      const options = { 
        month: '2-digit', 
        day: '2-digit'
      }
      this.selectedValue = this.$refs.dateVal.value
      this.$refs.dateVal.value = new Date().toLocaleString('sv-SE', options).replaceAll('-', '/') + ' - ' + new Date(this.$refs.dateVal.value).toLocaleString('sv-SE', options).replaceAll('-', '/')
      this.$refs.dateVal.blur()
      this.$emit('clearNoEndDateCheckbox')
    },
    formatDate (endDate) {
      const options = { 
        month: '2-digit', 
        day: '2-digit'
      }
      console.log(endDate)
      const now = new Date().toLocaleString('sv-SE', options).replaceAll('-', '/')
      return now + ' - ' + new Date(endDate).toLocaleString('sv-SE', options).replaceAll('-', '/')
    },
    setValue (newValue) {
      if (this.isDatePicker) {
        this.$refs.dateVal.type = 'text'
        this.$refs.dateVal.value = this.formatDate(newValue)
        this.$refs.dateVal.blur()
        this.selectedValue = newValue
      } else {
        const selectedVal = document.getElementById(this.name + '-combo-placeholder')
        selectedVal.innerText = newValue
        selectedVal.style.color = 'black'
        this.selectedValue = newValue
      }
    },
    clearDatePicker () {
      this.$refs.dateVal.type = 'text'
      this.$refs.dateVal.value = null
      this.selectedValue = null
    } 
  },
  mounted () {
    if (!this.isDatePicker) {
      const list = document.getElementById(this.name + '-dropdown')
      for (const item of list.childNodes) {
        item.addEventListener('click', () => {
          item.parentNode.style.display = 'none'
          const selectedVal = document.getElementById(this.name + '-combo-placeholder')
          selectedVal.innerText = item.innerText
          selectedVal.style.color = 'black'
          this.selectedValue = item.innerText
          document.getElementById(this.name).classList.remove('combobox-active')
        })
      }
    } else {
      const minLimitDate = new Date()
      const datePicker = document.getElementById(this.name + '-date-picker')
      minLimitDate.setDate(minLimitDate.getDate() + 1)
      datePicker.setAttribute('min', minLimitDate.toISOString().split('T')[0])
      const maxLimitDate = new Date()
      maxLimitDate.setFullYear(maxLimitDate.getFullYear() + 1)
      datePicker.setAttribute('max', maxLimitDate.toISOString().split('T')[0])
    } 
  },
  data () {
    return { 
      selectedValue: null
    }
  }
}
</script>

<style scoped>

.input-title {
  font-size: 24px;
  font-family: 'Ubuntu', sans-serif;
  font-weight: 700;
  margin-bottom: 10px;
}

/* .date-picker {
  position:absolute;
  top:0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color:white;
  cursor: pointer;
} */

.date-picker {
  width: 420px;
  height: 38px;
  background: white;
  border: 2px solid #5c5c5c;
  border-radius: 4px;
  position: relative;
  cursor: pointer;
  font-size: 13px;
}

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

.combobox p, .dropdown-content-combo p, .date-picker {
  -webkit-user-select: none; /* Safari */        
  -moz-user-select: none; /* Firefox */
  -ms-user-select: none; /* IE10+/Edge */
  user-select: none; /* Standard */
}

p:empty:not(:focus)::before, input::placeholder {
  content: attr(data-placeholder);
  color: #acacac;
  opacity: 1; /* Firefox */
}

#combo-arrow {
  position: absolute;
  right: 2%;
  top: 45%;
}

.combobox-active, .combobox:focus, .date-picker:focus {
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
  max-height: 200px;
  overflow-y: auto;
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
  .dropdown-content-combo, .combobox, .date-picker {
    width: 350px;
  }
}

@media (max-width: 620px) {
  .dropdown-content-combo, .combobox, .date-picker {
    width: 250px;
  }

  .dropdown-content-combo p, .combobox p, .date-picker {
    font-size: 10px;
  }
}

@media (max-width: 400px) {
  .dropdown-content-combo, .combobox, .date-picker {
    width: 200px;
  }

  .dropdown-content-combo p, .combobox p, .date-picker {
    font-size: 10px;
  }
}

</style>
