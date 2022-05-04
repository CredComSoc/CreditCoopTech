<template> 
<div>
  <div id="title-field" class="input">
    <DatePicker ref="endDateInput" name="end-date-picker" label="Tid" :placeholder="`Hur länge ska din ` + this.chosenType.toLowerCase() + ` finnas tillgänglig?`" @clearNoEndDateCheckbox='clearNoEndDateCheckbox' /><br>
    <input @click="clearDatePicker" ref="noEndDate" id="no-end-date" type="checkbox" name="end-date"/>
    <label for="end-date"> På obestämd tid </label>
  </div>
  <div class="input">
    <Combobox ref="cityInput" name="city-new-article" label="Plats" :options="[`Linköping`, `Norrköping`, `Söderköping`]" :placeholder="`Var finns din ` + this.chosenType.toLowerCase() + `?`" />
  </div>
  <div class="input" id="new-article-price">
    <TextBox ref="priceInput" id="price-new-article" name="price" label="Pris" :placeholder="`Hur mycket kostar din ` + this.chosenType.toLowerCase() + `?`" :disabled='true' length="20" />
    <h3> Bkr </h3>
  </div>
</div>
</template>

<script>
import TextBox from './TextBox.vue'
import DatePicker from './DatePicker.vue'
import Combobox from './Combobox.vue'

export default {
  name: 'StepTwo',
  components: {
    TextBox,
    DatePicker,
    Combobox
  },
  props: ['chosenType', 'savedProgress'],
  emits: ['priceError', 'dateError'],
  methods: {
    getStepTwoInputs () {
      let endDate = null
      if (!this.$refs.noEndDate.checked) {
        endDate = this.$refs.endDateInput.getInput()
      }
      return { 
        'end-date': endDate,
        destination: this.$refs.cityInput.getInput(),
        price: this.$refs.priceInput.getInput()
      }
    },
    clearDatePicker () {
      if (this.$refs.noEndDate.checked) {
        this.$refs.endDateInput.clearDatePicker()
      }
    },
    clearNoEndDateCheckbox () {
      this.$refs.noEndDate.checked = false
    },
    validateStepTwo () {
      const endDate = this.$refs.endDateInput.getInput()
      const city = this.$refs.cityInput.getInput()
      const price = this.$refs.priceInput.getInput()
      if (!this.$refs.noEndDate.checked) {
        if (endDate === null) {
          return false
        } else if (!this.isDateWithinOneMonthFromNow(endDate)) {
          this.$emit('dateError')  
          return false
        }
      }
      if (city === null) {
        return false
      }
      if (!this.isNumeric(price)) {
        this.$emit('priceError')
        return false
      }
      return true
    },
    isNumeric (str) {
      return Number.isInteger(Number(str)) && Number(str) > 0
    },
    isDateWithinOneMonthFromNow (date) {
      const now = new Date()
      const chosenDate = new Date(date)
      const oneMonthFromNow = new Date(now.getFullYear() + 1, now.getMonth(), now.getDate() + 1)
      return now.getTime() <= chosenDate.getTime() && chosenDate.getTime() <= oneMonthFromNow.getTime()
    }
  },
  mounted () {
    if ('end-date' in this.savedProgress) {
      if (this.savedProgress['end-date'] === null) {
        this.$refs.noEndDate.checked = true
      } else {
        this.$refs.endDateInput.setValue(this.savedProgress['end-date']) 
      }
    } 
    if ('destination' in this.savedProgress) {
      this.$refs.cityInput.setValue(this.savedProgress.destination)
    } 
    if ('price' in this.savedProgress) {
      this.$refs.priceInput.setValue(this.savedProgress.price)
    } 
  }  
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Ubuntu:wght@700&display=swap');

#no-end-date {
  margin-right: 10px;
  width: 15px;
  height: 15px;
}

.input {
  margin-top: 40px;
  position: relative;
}

#new-article-price {
  width: 110%;
} 

h3 {
  display: inline;
  margin: 0;
  padding: 0;
  position: absolute;
  bottom: 0;
  right: 0;
  font-family: 'Ubuntu';
  font-size: 23px;
  margin-left: 15px;
}

@media (max-width: 700px) {
  #new-article-price {
    width: 390px;
  }
}

@media (max-width: 620px) {
  #new-article-price {
    width: 290px;
  }
}

@media (max-width: 400px) {
  .input {
    margin-left: 40px;
  }

  #new-article-price {
    width: 239px;
  }
}

</style>
