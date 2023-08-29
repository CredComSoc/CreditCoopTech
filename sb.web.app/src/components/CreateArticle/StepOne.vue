<template>
  <div>
    <!-- To change character limit on textfields change value for length prop  -->
    <div id="buy-or-sell" class="input">
      <Combobox ref="buyOrSellInput" name="butOrSell-type" :label="$t('shop_items.want_or_offer')" :options="['Want', 'Offer']" :placeholder="$t('shop_items.to_buy_or_sell')" />
    </div>
    <div id="title-field" class="input">
      <TextBox ref="titleInput" id='title' name="title" :placeholder="$t('shop_items.item_title_prompt')" length="30" :label="$t('shop_items.item_title')"/>
    </div>
    <div id="desc" class="input" >
      <TextArea ref="descInput" name="description" :label="$t('shop_items.item_description')" length="200" :placeholder="$t('shop_items.item_description_short_prompt')"/>
    </div>
    <!-- <div id="category" class="input">
      <Combobox ref="categoryInput" name="category-type" :label="$t('category')" 
        :options="[... categories]" 
        :placeholder="$t('shop_items.item_category_prompt')" 
      /> -->
    <div class="select-container">
      <div>
    <label class="select-label" for="my-select">{{ $t('category') }}</label>
  </div>
    <select ref="categories" class="select" id="my-select" v-model="categorySelected" :placeholder="$t('shop_items.item_category_prompt')">
      <option v-for="option in categories" :key="option" :value="option"><p class="options">{{  option }}</p></option>
    </select>
  </div>
    <!-- </div> -->
    <div id="type" class="input">
      <Combobox ref="typeInput" name="articale-type" :label="$t('shop_items.product_or_service')" :options="['Product', 'Service']" :placeholder="$t('shop.is_item_product_or_service')" />
    </div>
  </div>
</template>

<script>
import Combobox from './Combobox.vue'
import TextBox from '@/components/SharedComponents/TextBox.vue'
import TextArea from '@/components/SharedComponents/TextArea.vue'
import { getCategories } from '../../serverFetch'
import { ref } from 'vue'

export default {
  name: 'UserInput',
  components: {
    Combobox,
    TextBox,
    TextArea
  },
  props: ['savedProgress'],
  data () {
    return {
      categories: [],
      categoriesObject: [],
      categorySelected: ''
    }
  },
  methods: {
    getStepOneInputs () {
      return { 
        title: this.$refs.titleInput.getInput(),
        longDesc: this.$refs.descInput.getInput(),
        article: this.$refs.typeInput.getInput(), 
        category: this.categorySelected,
        status: this.$refs.buyOrSellInput.getInput()
      }
    },
    validateStepOne () {
      const title = this.$refs.titleInput.getInput()
      const description = this.$refs.descInput.getInput()
      const type = this.$refs.typeInput.getInput()
      const category = this.categorySelected
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
      this.categorySelected = (this.savedProgress.category)
    }
    if ('status' in this.savedProgress) {
      this.$refs.buyOrSellInput.setValue(this.savedProgress.status)
    }
  },
  mounted () {
    getCategories().then((res) => {
      this.categoriesObject = res
      this.categories = res.map(element => element.name)
    })
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
      this.categorySelected = (this.savedProgress.category)
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
.select-label
{
  font-size: 24px;
  font-family: 'Ubuntu', sans-serif;
  font-weight: 700;
  margin-bottom: 10px;
}
.select 
{
  color:black;
  font-family: 'Ubuntu';
  font-weight: 300;
  font-size: 12px;
  font-style: normal;
  padding: 12px 0px 12px 5px;
  margin: 0;
  border-bottom: 1px solid #CBCACA; 
  max-height: 200px;
  overflow-y: auto;
  background-color: #E5E5E5;
  width: 420px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}

.options {
  margin-top: 5px;
  margin-left: 2px;
  font-family: 'Ubuntu';
  font-size: 13px;
  color:black;
  font-family: 'Ubuntu';
  font-weight: 300;
  font-size: 12px;
  font-style: normal;
  padding: 12px 0px 12px 5px;
  margin: 0;
  border-bottom: 1px solid #CBCACA;
}

@media (max-width: 700px) {
  .select {
    width: 350px;
  }
}

@media (max-width: 620px) {
  .select{
    width: 250px;
  }
}

@media (max-width: 400px) {
  .select {
    width: 200px;
  }
  .input {
    margin-left: 40px;
  }
}

</style>
