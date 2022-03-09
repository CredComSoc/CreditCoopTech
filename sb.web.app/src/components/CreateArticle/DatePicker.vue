<template>
  <Combobox :name="this.name" :label="this.label" :placeholder="this.placeholder" :isDatePicker="true"/>
</template>

<script>
import Combobox from './Combobox.vue'

export default {
  name: 'DatePicker',
  components: {
    Combobox
  },
  props: ['name', 'label', 'placeholder']
}
</script>

<style scoped>

</style>
