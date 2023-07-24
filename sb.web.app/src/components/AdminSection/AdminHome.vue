<template>
  <div class="wrapper">
    <div>
      <h2 class="center-text">{{ $t('memberCAPS') }}</h2>
    </div>
    <Searchfield @searchEvent="triggerSearch" :place-holder-message="'Who do you want to search for today?'"/>
    <br>
    <div class="main">
      <div class="listings">
        <div v-if="this.SearchData.length !== 0">
          <AllMembers :key=SearchData :search-data=SearchData @openProfile="this.openProfile"/>
        </div>
        <h3 v-if="this.allMembersArraySize === 0 && this.adminMembersArraySize === 0" class="text-center">No users found for '{{this.searchWord}}'</h3>
      </div>
    </div>
  </div>
  <div v-if="this.showProfile == true" class="userprofile">
    <userProfile class="userprof"  :userprofile="this.profileName" />
    <div class="overlaybg" @click="this.showProfile = false"></div>
  </div>
</template>

<script>
import Searchfield from '@/components/SharedComponents/searchfield.vue'
import AllMembers from '@/components/AdminSection/Members/all_members.vue'
import userProfile from '@/components/AdminSection/Members/userProfile.vue'

export default {

  data () {
    return {
      SearchData: [],
      singleListingData: [],
      popupActive: false,
      listingObjPopup: Object,
      showProfile: false,
      profileName: ''
    }
  },
  components: {
    Searchfield,
    AllMembers,
    userProfile
  },

  methods: { 
    //filter and sorts according to list of cities
    //**to be implimented--{ to not sort according to cities and just according to alphabetical order}
    triggerSearch (newSearchWord) {
      this.searchWord = newSearchWord
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
          //console.log(member)
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
      this.allMembersArraySize = allMembersArray.size
      this.adminMembersArraySize = adminMembersArray.size
      const sortedMap = new Map([...allMembersArray].sort((a, b) => String(a[0]).localeCompare(b[0], 'sv')))
      const finishMap = new Map([...adminMembersArray, ...sortedMap])

      this.SearchData = finishMap
    },
    openProfile (message) {
      this.profileName = message.name
      this.showProfile = true
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
  margin-left: -7rem;
  margin-bottom: 40px;
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
  margin: 2rem 0rem;
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

.userprofile{
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 100vw;
  height: 100vh;
  position: fixed;
  top: 0px;
  left: 0px;
}

.userprof{
  background-color: white;
  border: 1px solid black;
  border-radius: 2px;
  z-index: 2;
  width: fit-content;
  max-height: 80vh;
  overflow-y:scroll;
}

.overlaybg{
  position: absolute;
  top: 0px;
  left: 0px;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.2);
  z-index: 1;
  
}

::-webkit-scrollbar {
  width: 4px;
}

/* Track */
::-webkit-scrollbar-track {
  background: #f1f1f1; 
  margin-right: 2px;
}
 
/* Handle */
::-webkit-scrollbar-thumb {
  background: #888; 
  
  border-radius: 2px;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: #555; 
}

</style>
