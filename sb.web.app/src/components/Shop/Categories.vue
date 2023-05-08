<!--
*   Contains all category buttons, will event is triggered by CategoryButton a new event will trigger
-->

<template>
    <div className="categories">

        <h1>{{ $t('I_want_to') }}</h1>
        <CategoryButton @filterEvent="triggerFilterEvent" type='status' value='selling' :title="$t('buy')"/>
        <CategoryButton @filterEvent="triggerFilterEvent" type='status' value='buying' :title="$t('sell')"/>

        <h1>{{ $t('shop_items.item_type') }}</h1>
        <CategoryButton @filterEvent="triggerFilterEvent" type='article' value='product' :title="$t('product')"/>
        <CategoryButton @filterEvent="triggerFilterEvent" type='article' value='service' :title="$t('services')"/>

        <h1>{{ $t('location') }}</h1>
        <CategoryButton @filterEvent="triggerFilterEvent" type='destination' value='burlington' title='Burlington'/>
        <CategoryButton @filterEvent="triggerFilterEvent" type='destination' value='rutland' title='Rutland'/>
        <CategoryButton @filterEvent="triggerFilterEvent" type='destination' value='montpelier' title='Montpelier'/>

        <h1>{{ $t('category')}}er</h1>
        <CategoryButton v-for="data in this.categoriesJson.categories" :key="data.value" @filterEvent="triggerFilterEvent" type='category' :value="data.value" :title="data.title"/>

    </div>
</template>

<script>
import CategoryButton from '@/components/Shop/categorybutton.vue'
import jsonWithCategories from '@/components/Shop/categories.json'

export default {
  data () {
    return {
      categoriesJson: jsonWithCategories
    }
  },
  methods: {
    triggerFilterEvent (checked, type, value) {
      this.$emit('filterEvent', checked, type, value)
    }
  },
  components: {
    CategoryButton
  }
}
</script>

<style scoped>
  h1 {
    font-size: 18px;
  }

  .categories {
    box-shadow: 0px 4px 4px 0px #00000040;
    border: 3px solid #C4C4C4;
    height: 100%;
    margin: 1rem;
    padding: 1rem;
  }

</style>
