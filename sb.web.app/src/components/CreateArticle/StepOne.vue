<template>
  <div id="title-field" class="input">
    <TextBox id='title' name="title" placeholder="Vad ska din artikel heta?" label="Rubrik"/>
  </div>
  <div class="input" >
    <TextArea name="description" label="Beskrivning" placeholder="Beskriv vad som ingår i din artikel"/>
  </div>
  <div class="input">
    <Combobox name="articale-type" label="Typ av artikel" :options="['Produkt', 'Tjänst']" placeholder="Är din artikel en produkt eller tjänst?" />
  </div>
  <div class="input">
    <Combobox name="category-type" label="Kategori" :options="['Hej', 'san']" placeholder="Vilken av Kategorierna nedan tillhör din produkt?" />
  </div>
</template>

<script>
import Combobox from './Combobox.vue'
import TextBox from './TextBox.vue'
import TextArea from './TextArea.vue'

export default {
  name: 'UserInput',
  components: {
    Combobox,
    TextBox,
    TextArea
  }
}
</script>

<style scoped>

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
