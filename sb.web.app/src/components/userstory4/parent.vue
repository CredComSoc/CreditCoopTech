<template>
  <b-container fluid>
    <b-row align-h="center">
      <b-col cols="4" class="d-flex align-items-center justify-content-center">
        <h2>SHOP</h2>
      </b-col>
    </b-row>
    <b-row align-h="center">
      <b-col cols="4" class="d-flex align-items-center justify-content-center">
        <Searchfield @searchEvent="testMethod" />
      </b-col>
    </b-row>
    <b-row>
      <Alllistings :key=searchData :search-data=searchData />
    </b-row>
  </b-container>
</template>

<script>
import Searchfield from '@/components/userstory4/searchfield.vue'
import Alllistings from '@/components/userstory4/all_listings.vue'

export default {

  data () {
    return {
      searchData: []
    }
  },

  components: {
    Searchfield,
    Alllistings
  },

  methods: {
    testMethod (newSearchWord) {
      this.searchData = this.backendFunction()
    },
    backendFunction () {
      return {
        allListings: [
          { title: 'annas bullar', ign: 'random', desc: 'goda bullar för virtuella pengar' },
          { title: 'kalles bullar', ign: 'random', desc: 'fina bullar jaja' },
          { title: 'thomas bullar', ign: 'random', desc: 'kom o köp bullar' },
          { title: 'thomas bullar', ign: 'random', desc: 'kom o köp bullar' },
          { title: 'thomas bullar', ign: 'random', desc: 'kom o köp bullar' }
        ]
      }
    }
  }
}
</script>
