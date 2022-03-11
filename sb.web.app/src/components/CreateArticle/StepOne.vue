<template>
  <div id="title-field" class="input">
    <TextBox ref="titleInput" id='title' name="title" placeholder="Vad ska din artikel heta?" label="Rubrik"/>
  </div>
  <div class="input" >
    <TextArea ref="descInput" name="description" label="Beskrivning" placeholder="Beskriv vad som ingår i din artikel"/>
  </div>
  <div class="input">
    <Combobox ref="typeInput" name="articale-type" label="Typ av artikel" :options="['Produkt', 'Tjänst']" placeholder="Är din artikel en produkt eller tjänst?" />
  </div>
  <div class="input">
    <Combobox ref="categoryInput" name="category-type" label="Kategori" :options="['Affärsutveckling & strategi', 'Arbetsyta', 'Fotografering', 'Kök & restaurang', 'Marknadsföring', 'Rengöring & städ', 'Skönhet', 'Sömnad & tyg']" placeholder="Vilken av Kategorierna nedan tillhör din produkt?" />
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
  },
  methods: {
    getStepOneInputs () {
      return { 
        title: this.$refs.titleInput.getInput(),
        description: this.$refs.descInput.getInput(),
        type: this.$refs.typeInput.getInput(), 
        category: this.$refs.categoryInput.getInput() 
      }
    }
  }
}
</script>

<style scoped>

#title-field {
  padding-top: 50px;
}

.input {
  margin-top: 40px;
}

@media (max-width: 400px) {
  .input {
    margin-left: 40px;
  }
}

</style>
