<template>
    <div>
      <ul>
        <li v-for="item in searchData['allListings']" :key="item.title">
          {{ item.title }}
        </li>
      </ul>
    </div>
</template>

<script>
//  import Searchfield from '@/components/userstory4/searchfield.vue'

export default {
  props: {
    searchData: Array
  }
}
</script>
