<template>
    <Searchfield @searchEvent="testMethod" />
</template>

<script>
import Searchfield from '@/components/userstory4/searchfield.vue'

export default {
  components: {
    Searchfield
  },

  methods: {
    testMethod (n) {
      console.log(n)
    }
  }
}
</script>
