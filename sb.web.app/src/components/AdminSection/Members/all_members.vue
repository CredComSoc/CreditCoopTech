
<template>
  <!--
  <div class="container_all_listings">
    <ul>
      <li v-for="item in searchData" :key="item.title">
        <Member :listingObj="item"/>
      </li>
    </ul>
  </div>
  -->
  
  <div class="container_all_listings">
    <ul>
      <div v-for="city in searchData" :key="city">
          <!-- city[1] = array of members in city -->
          <li v-for="member in city[1]" :key="member.title">
            <Member :listingObj="member" @openProfile="this.openProfile"/>
          </li>
      </div>
    </ul>
  </div>

</template>

<script>

import Member from '@/components/AdminSection/Members/member.vue'

export default {
  components: {
    Member
  },
 
  props: {
    searchData: Map
  },
  mounted: function () {
    console.log(this.searchData)
  },
  methods: {
    openProfile (message) {
      this.$emit('openProfile', { name: message.name })
    }
  }
}
</script>

<style scoped>
.container_all_listings {
  display: flex;
  flex-direction: column;
  flex-wrap: wrap;
  width: max(20rem, 50%);
  margin: auto;
}

ul {
  padding: 0;
  margin:auto;

}

.container_all_listings > * {
  flex-basis: 100%;
  width: 100%;
}

li {
  list-style-type: none;
  margin-bottom: 15px;
}

h3 {
  margin-top: 20px;
  margin-bottom: 10px;
}

/* @media only screen and (min-width: 1200) {
  li {
    display: inline-block;
    flex-grow: 1;
    width: calc(100% * (1/4) - 10px - 1px);
  }
} */

</style>
