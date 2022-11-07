<template>
  <Combobox ref="dateFilter" :name="this.name" :label="this.label" :placeholder="this.placeholder" :isDateFilter="true" />
</template>
  
<script>
import Combobox from '@/components/CreateArticle/Combobox.vue'
  
export default {
  name: 'DateFilter',
  components: {
    Combobox
  },
  props: ['name', 'label', 'placeholder'],
  methods: {
    getInput () {
      return this.$refs.dateFilter.getInput()
    },
    setValue (newValue) {
      this.$refs.dateFilter.setValue(newValue)
    },
    clearDatePicker () {
      this.$refs.dateFilter.clearDatePicker()
    }
  }
}
</script>
  
<style scoped>
  
</style>
