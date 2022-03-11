<template>
  <Combobox ref="datePicker" :name="this.name" :label="this.label" :placeholder="this.placeholder" :isDatePicker="true"/>
</template>

<script>
import Combobox from './Combobox.vue'

export default {
  name: 'DatePicker',
  components: {
    Combobox
  },
  props: ['name', 'label', 'placeholder'],
  methods: {
    getInput () {
      return this.$refs.datePicker.getInput()
    },
    setValue (newValue) {
      this.$refs.datePicker.setValue(newValue)
    }
  }
}
</script>

<style scoped>

</style>
