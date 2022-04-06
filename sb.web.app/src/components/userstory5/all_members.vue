
<template>
  <div class="container_all_listings">
    <ul>
      <li v-for="item in searchData" :key="item.title">
        <Member :listingObj="item"/>
      </li>
    </ul>
  </div>
</template>

<script>

import Member from '@/components/userstory5/member.vue'

export default {
  components: {
    Member
  },
 
  props: {
    searchData: Array
  }
}
</script>

<style scoped>
.container_all_listings {
  display: flex;
  flex-wrap: wrap;
}

ul {
  padding: 0;
}

.container_all_listings > * {
  flex-basis: 100%;
}

li {
  display: inline-block;
  flex: 1 0 calc(25% - 10px);
  vertical-align: top;
}

/* @media only screen and (min-width: 1200) {
  li {
    display: inline-block;
    flex-grow: 1;
    width: calc(100% * (1/4) - 10px - 1px);
  }
} */

</style>
