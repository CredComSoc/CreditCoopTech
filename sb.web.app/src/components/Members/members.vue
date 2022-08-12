<template>
  <div class="wrapper">

    <div>
      <h2 class="center-text">MEDLEMMAR</h2>
    </div>

    <div class="center">
        <Searchfield @searchEvent="triggerSearch" :place-holder-message="'Vem vill du söka efter idag?'"/>
    </div>
    <br>
    <div class="main">
      <div class="listings">
        <div v-if="this.SearchData.length !== 0">
          <AllMembers :key=SearchData :search-data=SearchData />
        </div>
      </div>
    </div>
    
  </div>
</template>

<script>
import Searchfield from '@/components/SharedComponents/searchfield.vue'
import AllMembers from '@/components/Members/all_members.vue'
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
      let searchWord = newSearchWord.split(' ')
      searchWord = searchWord.filter(function (value) {
        return value !== ''
      })

      const allMembersArray = new Map()
      const adminMembersArray = new Map()

      for (const member of this.$store.state.allMembers) {
        const name = member.accountName
    
        let foundSearchword = true
        if (searchWord.length !== 0) {
          for (let i = 0; i < searchWord.length; i++) {
            if (!name.match(new RegExp(searchWord[i], 'i'))) {
              foundSearchword = false
              break
            } 
          }
          if (!foundSearchword) {
            continue
          }
        }

        if (member.is_admin) {
          if (!adminMembersArray.has('Admin')) {
            adminMembersArray.set('Admin', [])
          }
          adminMembersArray.get('Admin').push(member)
        } else {
          console.log(member)
          if (!allMembersArray.has(member.city)) {
            allMembersArray.set(member.city, [])
          }
          allMembersArray.get(member.city).push(member)
        }
      }

      //Sort alphabetically by swedish.
      for (const value of allMembersArray.values()) {
        value.sort((a, b) => a.accountName.localeCompare(b.accountName))
      }
      const sortedMap = new Map([...allMembersArray].sort((a, b) => String(a[0]).localeCompare(b[0], 'sv')))
      const finishMap = new Map([...adminMembersArray, ...sortedMap])

      this.SearchData = finishMap
    }
  },
  mounted: function () {
    console.log(this.$store.state.allMembers)
    this.triggerSearch('')
  }
  
}
</script>

<style scoped>

 * {
    font-family: 'Ubuntu', sans-serif;
    padding: 0;
    margin: 0;
  }

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
  margin-top: 4rem;
  margin-bottom: 4rem;
  font-size: 2.2rem;
  letter-spacing: 0.3em;  
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
