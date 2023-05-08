<template>
  <div>
    <!-- To change character limit on textfields change value for length prop  -->
    <div id="buy-or-sell" class="input">
      <Combobox ref="buyOrSellInput" name="butOrSell-type" :label="$t('shop_items.need_or_offer')" :options="['Need', 'Offer']" :placeholder="$t('shop_items.to_buy_or_sell')" />
    </div>
    <div id="title-field" class="input">
      <TextBox ref="titleInput" id='title' name="title" :placeholder="$t('shop_items.item_title_prompt')" length="30" :label="$t('shop_items.item_title')"/>
    </div>
    <div id="desc" class="input" >
      <TextArea ref="descInput" name="description" :label="$t('shop_items.item_description')" length="200" :placeholder="$t('shop_items.item_description_short_prompt')"/>
    </div>
    <div id="category" class="input">
      <Combobox ref="categoryInput" name="category-type" :label="$t('category')" 
        :options='[
          "Facilities",
          "Antiques",
          "Business Networking",
          "Used & Recycling",
          "Staffing",
          "Construction & Property Maintenance",
          "Cafés, Restaurants, Bars",
          "Finance, Law, Business Development",
          "Fuel and Power",
          "Photography, Film & Marketing",
          "Leisure & Play",
          "Coaching",
          "Hotels & Conferences",
          "IT, Computers & Telephony",
          "Clothes, Shoes & Fashion",
          "Art",
          "Consultancy",
          "Office Space",
          "Office Supplies",
          "Body Care, Beauty, Hair & Health",
          "Agriculture & Forestry",
          "Food",
          "Metal & Sheet Metal",
          "Brokerage & Real Estate",
          "Furniture & Interiors",
          "Entertainment",
          "Jewelry",
          "Carpentry & Wood Products",
          "Sanitary, Plumbing & Electricity",
          "Technology, Electronics & Appliances",
          "Textiles & Sewing",
          "Interpretation & Translation",
          "Gardening & Flowers",
          "Tourism & Travel",
          "Laundry & Cleaning",
          "Other"
        ]' 
        :placeholder="$t('shop_items.item_category_prompt')" 
      />
    </div>
    <div id="type" class="input">
      <Combobox ref="typeInput" name="articale-type" :label="$t('shop_items.item_type')" :options="['product', 'service']" :placeholder="$t('shop.is_item_product_or_service')" />
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
