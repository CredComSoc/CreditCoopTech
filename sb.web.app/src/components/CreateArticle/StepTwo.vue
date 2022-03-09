<template>
<div id="input-field" class="input">
  <TextBox name="title" label="Pris" placeholder="Hur mycket kostar din?"/>
</div>

</template>

<script>
import TextBox from './TextBox.vue'

export default {
  name: 'StepTwo',
  components: {
    TextBox
  }  
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Ubuntu:wght@700&display=swap');
 #input-field {
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
