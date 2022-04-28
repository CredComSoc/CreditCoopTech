<template>
  <div>
    <div class="input">
      <Combobox ref="buyOrSellInput" name="butOrSell-type" label="Artikelns syfte" :options="['Köpes', 'Säljes']" placeholder="Önskas artikeln att köpas eller ska den säljas?" />
    </div>
    <div id="title-field" class="input">
      <TextBox ref="titleInput" id='title' name="title" placeholder="Vad ska din artikel heta?" length="30" label="Rubrik"/>
    </div>
    <div class="input">
      <TextArea ref="shortDescInput" name="short-description" label="Kort beskrivning" length="50" placeholder="Gör en kort beskriv av vad som ingår i din artikel"/>
    </div>
    <div class="input" >
      <TextArea ref="descInput" name="description" label="Beskrivning" length="200" placeholder="Beskriv vad som ingår i din artikel"/>
    </div>
    <div class="input">
      <Combobox ref="typeInput" name="articale-type" label="Typ av artikel" :options="['Produkt', 'Tjänst']" placeholder="Är din artikel en produkt eller tjänst?" />
    </div>
    <div class="input">
      <Combobox ref="categoryInput" name="category-type" label="Kategori" :options="['Affärsutveckling & strategi', 'Arbetsyta', 'Fotografering', 'Kök & restaurang', 'Marknadsföring', 'Rengöring & städ', 'Skönhet', 'Sömnad & tyg']" placeholder="Vilken av Kategorierna nedan tillhör din produkt?" />
    </div>
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
  props: ['savedProgress'],
  methods: {
    getStepOneInputs () {
      return { 
        title: this.$refs.titleInput.getInput(),
        longDesc: this.$refs.descInput.getInput(),
        article: this.$refs.typeInput.getInput(), 
        category: this.$refs.categoryInput.getInput(),
        shortDesc: this.$refs.shortDescInput.getInput(),
        status: this.$refs.buyOrSellInput.getInput()
      }
    },
    validateStepOne () {
      const title = this.$refs.titleInput.getInput()
      const description = this.$refs.descInput.getInput()
      const type = this.$refs.typeInput.getInput()
      const category = this.$refs.categoryInput.getInput()
      const shortDesc = this.$refs.shortDescInput.getInput()
      const status = this.$refs.buyOrSellInput.getInput()
      if (title.length === 0) {
        return false
      }
      if (description.length === 0) {
        return false
      }
      if (type === null) {
        return false
      }
      if (category === null) {
        return false
      }
      if (shortDesc.length === 0) {
        return false
      }
      if (status === null) {
        return false
      }
      return true
    }
  },
  beforeUpdate () {
    if ('title' in this.savedProgress) {
      this.$refs.titleInput.setValue(this.savedProgress.title) 
    } 
    if ('longDesc' in this.savedProgress) {
      this.$refs.descInput.setValue(this.savedProgress.longDesc)
    } 
    if ('article' in this.savedProgress) {
      this.$refs.typeInput.setValue(this.savedProgress.article)
    } 
    if ('category' in this.savedProgress) {
      this.$refs.categoryInput.setValue(this.savedProgress.category)
    }
    if ('shortDesc' in this.savedProgress) {
      this.$refs.shortDescInput.setValue(this.savedProgress.shortDesc)
    }
    if ('status' in this.savedProgress) {
      this.$refs.buyOrSellInput.setValue(this.savedProgress.status)
    }
  },
  mounted () {
    if ('title' in this.savedProgress) {
      this.$refs.titleInput.setValue(this.savedProgress.title) 
    } 
    if ('longDesc' in this.savedProgress) {
      this.$refs.descInput.setValue(this.savedProgress.longDesc)
    } 
    if ('article' in this.savedProgress) {
      this.$refs.typeInput.setValue(this.savedProgress.article)
    } 
    if ('category' in this.savedProgress) {
      this.$refs.categoryInput.setValue(this.savedProgress.category)
    }
    if ('shortDesc' in this.savedProgress) {
      this.$refs.shortDescInput.setValue(this.savedProgress.shortDesc)
    }
    if ('status' in this.savedProgress) {
      this.$refs.buyOrSellInput.setValue(this.savedProgress.status)
    }
  }
}
</script>

<style scoped>

.input {
  margin-top: 40px;
}

@media (max-width: 400px) {
  .input {
    margin-left: 40px;
  }
}

</style>
