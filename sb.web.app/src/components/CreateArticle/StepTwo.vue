<template>
<div id="title-field" class="input">
  <DatePicker ref="endDateInput" name="end-date-picker" label="Tid" :placeholder="`Hur länge ska din ` + this.chosenType.toLowerCase() + ` finnas tillgänglig?`" @clearNoEndDateCheckbox='clearNoEndDateCheckbox' /><br>
  <input @click="clearDatePicker" ref="noEndDate" id="no-end-date" type="checkbox" name="end-date"/>
  <label for="end-date"> På obestämd tid </label>
</div>
<div class="input">
  <Combobox ref="cityInput" name="city-new-article" label="Plats" :options="[`Linköping`, `Norrköping`, `Söderköping`]" :placeholder="`Var finns din ` + this.chosenType.toLowerCase() + `?`" />
</div>
<div class="input" id="new-article-price">
  <TextBox ref="priceInput" id="price-new-article" name="price" label="Pris" :placeholder="`Hur mycket kostar din ` + this.chosenType.toLowerCase() + `?`" :disabled='true' />
  <h3> Bkr </h3>
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
  methods: {
    getStepTwoInputs () {
      let endDate = null
      if (!this.$refs.noEndDate.checked) {
        endDate = this.$refs.endDateInput.getInput()
      }
      return { 
        endDate: endDate,
        city: this.$refs.cityInput.getInput(),
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
    }
  },
  mounted () {
    if ('endDate' in this.savedProgress) {
      if (this.savedProgress.endDate === null) {
        this.$refs.noEndDate.checked = true
      } else {
        this.$refs.endDateInput.setValue(this.savedProgress.endDate) 
      }
    } 
    if ('city' in this.savedProgress) {
      this.$refs.cityInput.setValue(this.savedProgress.city)
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
  margin-top: 20px;
  width:15px;
  height:15px;
}

.input {
  margin-top: 40px;
  position: relative;
}

h3 {
  display: inline;
  margin: 0;
  padding: 0;
  position: absolute;
  bottom: 0;
  font-family: 'Ubuntu';
  font-size: 23px;
  margin-left: 15px;
}

@media (max-width: 400px) {
  .input {
    margin-left: 40px;
  }
}

</style>
