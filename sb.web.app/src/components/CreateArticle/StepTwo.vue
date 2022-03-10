<template>
<div id="title-field" class="input">
  <DatePicker ref="endDateInput" name="end-date-picker" label="Tid" :placeholder="`Hur länge ska din ` + this.chosenType.toLowerCase() + ` finnas tillgänglig?`"/><br>
  <input ref="noEndDate" id="no-end-date" type="checkbox" name="end-date"/>
  <label for="end-date"> På obestämd tid </label>
</div>
<div class="input">
  <Combobox ref="cityInput" name="city-new-article" label="Plats" :options="[`Linköping`, `Norrköping`, `Söderköping`]" :placeholder="`Var finns din ` + this.chosenType.toLowerCase() + `?`" />
</div>
<div class="input" id="new-article-price">
  <TextBox ref="priceInput" id="price-new-article" name="price" label="Pris" :placeholder="`Hur mycket kostar din ` + this.chosenType.toLowerCase() + `?`" />
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
  props: ['chosenType'],
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
    }
  }  
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Ubuntu:wght@700&display=swap');
 #title-field {
  padding-top: 50px;
}

#no-end-date {
  margin-right: 10px;
  margin-top: 20px;
}

.input {
  margin-left: 80px;
  margin-top: 40px;
  position: relative;
}

h3 {
  display: inline;
  margin: 0;
  padding: 0;
  position: absolute;
  bottom: 0;
  margin-left: 20px;
  font-family: 'Ubuntu';
  font-size: 23px;
}

@media (max-width: 400px) {
  .input {
    margin-left: 40px;
  }
}

</style>
