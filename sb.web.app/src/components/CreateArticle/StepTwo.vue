<template>
<div id="title-field" class="input">
  <DatePicker name="end-date-picker" label="Tid" placeholder="Hur länge ska din produkt finnas tillgänglig?"/>
</div>
<div class="input">
  <TextBox id="title" name="title" label="Pris" placeholder="Hur mycket kostar din?"/>
</div>
</template>

<script>
import TextBox from './TextBox.vue'
import DatePicker from './DatePicker.vue'

export default {
  name: 'StepTwo',
  components: {
    TextBox,
    DatePicker
  }  
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
