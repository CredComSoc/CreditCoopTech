<template>
<div id="title-field" class="input">
  <DatePicker name="end-date-picker" label="Tid" :placeholder="`Hur länge ska din ` + this.chosenType.toLowerCase() + ` finnas tillgänglig?`"/>
</div>
<div class="input">
  <Combobox name="city-new-article" label="Plats" :options="[`Linköping`, `Norrköping`, `Söderköping`]" :placeholder="`Var finns din ` + this.chosenType.toLowerCase() + `?`" />
</div>
<div class="input">
  <TextBox id="price-new-article" name="price" label="Pris" :placeholder="`Hur mycket kostar din ` + this.chosenType.toLowerCase() + `?`" />
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
  props: ['chosenType']  
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Ubuntu:wght@700&display=swap');
 #title-field {
  padding-top: 50px;
}

.input {
  margin-left: 80px;
  margin-top: 40px;
}

@media (max-width: 400px) {
  .input {
    margin-left: 40px;
  }
}

</style>
