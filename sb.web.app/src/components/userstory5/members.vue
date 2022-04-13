<template>
  <div class="wrapper">

    <div>
      <h2 class="center-text">Medlemmar</h2>
    </div>

    <div class="center">
        <Searchfield @searchEvent="triggerSearch" :place-holder-message="'Vem vill du söka efter idag?'"/>
    </div>
    <br>
    <div class="main">
      <!-- KOLYMN FÖR PRODUKTER -->
      <!-- <div><h3>Admin</h3></div> -->

      <div class="listings">
        <div v-if="this.SearchData.length !== 0">
          <AllMembers :key=SearchData :search-data=SearchData />
        </div>
      </div>
    </div>
    
  </div>
</template>

<script>
import Searchfield from '@/components/userstory4/searchfield.vue'
import AllMembers from '@/components/userstory5/all_members.vue'
import { getAllMembers } from './../../serverFetch.js'

export default {

  data () {
    return {
      SearchData: [],
      singleListingData: [],
      popupActive: false,
      listingObjPopup: Object,
      getAllMembers
    }
  },

  components: {
    Searchfield,
    AllMembers
  },

  methods: {
    triggerSearch (newSearchWord) {
      this.getAllMembers(newSearchWord).then(res => {
        return res
      })
        .then(data => {
          console.log(data.allMembers)
          console.log(typeof (data.allMembers))
          this.SearchData = data.allMembers
        })
    }
  },
  
  created: function () {
    this.triggerSearch('')
  }
}
</script>

<style scoped>

.wrapper {
  display: flex;
  flex-direction: column;
}

.center {
  justify-content: center;
}

.main {
  display: flex;
  flex-direction: row;
  
}

.categories {
  flex-basis: 20%;
  height: auto;

}

.listings {
  flex-basis: 100%;
  width: auto;
}

.center-text {
  text-align: center;
}

h2 {
  margin-top: 2rem;
  font-size: 3rem;
}

h3 {
  margin-left: 1rem;
}

</style>
