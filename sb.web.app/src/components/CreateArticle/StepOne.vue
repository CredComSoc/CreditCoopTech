<template>
  <div>
    <!-- To change character limit on textfields change value for length prop  -->
    <div id="buy-or-sell" class="input">
      <Combobox ref="buyOrSellInput" name="butOrSell-type" label="Artikelns syfte" :options="['purchase', 'sell']" placeholder="Önskas artikeln att köpas eller ska den säljas?" />
    </div>
    <div id="title-field" class="input">
      <TextBox ref="titleInput" id='title' name="title" placeholder="Vad ska din artikel heta?" length="30" label="Rubrik"/>
    </div>
    <div id="desc" class="input" >
      <TextArea ref="descInput" name="description" label="Beskrivning" length="200" placeholder="Beskriv vad som ingår i din artikel"/>
    </div>
    <div id="category" class="input">
      <Combobox ref="categoryInput" name="category-type" label="Kategori" 
        :options='[
          "Affärsnätverk",
          "Anläggning",
          "Antikviteter",
          "Auktion",
          "Bank & Försäkring",
          "Begagnat & Återbruk",
          "Bemanning",
          "Butiker",
          "Bygg och Fastighetsunderhåll",
          "Café, Pub & Servering",
          "Ekonomi, Juridik & Affärsutveckling",
          "Energi",
          "Engagemang & CSR",
          "Entreprenad",
          "Fordon & Transport",
          "Foto, Film & Marknadsföring",
          "Fritid & Lek",
          "företagcoach",
          "Försäljning",
          "Hotell & Konferenser",
          "IT, Datorer & Telefoni",
          "Ideella Föreningar",
          "Investeringar",
          "Kaffe, Te & Fruktkorg",
          "Kiosk",
          "Kläder, Skor & Mode",
          "Konst",
          "Konsult",
          "Kontorslokaler",
          "Kontorsmaterial",
          "Kroppsvård & Hälsa",
          "Kultur",
          "Lantbruk och Skog",
          "Livsmedel",
          "Metall & Plåt",
          "Mäklare & Fastigheter",
          "Mässor & Utställningar",
          "Möbler & Inredning",
          "Nattklubb & Barer",
          "Nöjen & Underhållning",
          "Presentkort",
          "Psykolog",
          "Reklam & Media",
          "Restaurang & Catering",
          "Skönhet",
          "Smycken",
          "Snickeri & Trävaror",
          "Sociala företag",
          "Spel & Gaming",
          "Städ, VVS & El",
          "Teknik, Elektronik & Vitvaror",
          "Textil & Sömnad",
          "Tolk & Översättning",
          "Trycksaker & Presenter",
          "Trädgård & Blommor",
          "Turism & Resor",
          "Tvätteri & Rengöring",
          "Upplevelser & Evenemang",
          "Välgörenhet & Sponsring",
          "Övrigt"
        ]' 
        placeholder="Vilken av Kategorierna nedan tillhör din produkt?" 
      />
    </div>
    <div id="type" class="input">
      <Combobox ref="typeInput" name="articale-type" label="Typ av artikel" :options="['product', 'service']" :placeholder="$t('shop.is_item_product_or_service')" />
    </div>
  </div>
</template>

<script>
import Combobox from './Combobox.vue'
import TextBox from '@/components/SharedComponents/TextBox.vue'
import TextArea from '@/components/SharedComponents/TextArea.vue'

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
        status: this.$refs.buyOrSellInput.getInput()
      }
    },
    validateStepOne () {
      const title = this.$refs.titleInput.getInput()
      const description = this.$refs.descInput.getInput()
      const type = this.$refs.typeInput.getInput()
      const category = this.$refs.categoryInput.getInput()
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
