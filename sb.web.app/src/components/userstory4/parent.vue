<template>
    <Searchfield @searchEvent="testMethod" />
    <br>
    <Alllistings :key=searchKeyword :search-keyword=searchKeyword />
</template>

<script>
import Searchfield from '@/components/userstory4/searchfield.vue'
import Alllistings from '@/components/userstory4/all_listings.vue'

export default {

  data () {
    return {
      searchKeyword: ''
    }
  },

  components: {
    Searchfield,
    Alllistings
  },

  methods: {
    testMethod (newSearchWord) {
      this.searchKeyword = newSearchWord
    }
  }
}
</script>
