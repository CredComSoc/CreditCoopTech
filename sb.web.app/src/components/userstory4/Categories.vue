<!--
*   Contains all category buttons, will event is triggered by CategoryButton a new event will trigger
-->

<template>
    <div className="categories">
      
        <h1>Jag vill</h1>
        <CategoryButton @filterEvent="triggerFilterEvent" type='status' value='selling' title='Köpa'/>
        <CategoryButton @filterEvent="triggerFilterEvent" type='status' value='buying' title='Sälja'/>

        <h1>Typ av artikel</h1>
        <CategoryButton @filterEvent="triggerFilterEvent" type='article' value='product' title='Produkter'/>
        <CategoryButton @filterEvent="triggerFilterEvent" type='article' value='service' title='Tjänster'/>

        <h1>Plats</h1>
        <CategoryButton @filterEvent="triggerFilterEvent" type='destination' value='norrkoping' title='Norrköping'/>
        <CategoryButton @filterEvent="triggerFilterEvent" type='destination' value='soderkoping' title='Söderköping'/>
        <CategoryButton @filterEvent="triggerFilterEvent" type='destination' value='linkoping' title='Linköping'/>

        <h1>Kategorier</h1>
        <CategoryButton v-for="data in this.categoriesJson.categories" :key="data.value" @filterEvent="triggerFilterEvent" type='category' :value="data.value" :title="data.title"/>

    </div>
</template>

<script>
import CategoryButton from '@/components/userstory4/categorybutton.vue'
import jsonWithCategories from '@/components/userstory4/categories.json'

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
