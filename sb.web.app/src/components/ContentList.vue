<template>
    <div class="list-container" >
        <ListElement
        v-for="(el, index) in toList"
        :elementInfo="el"
        :index="index"
        :key="el.id"
        ></ListElement>
        <img class="arrow" src="../assets/list_images/arrow.png" alt="rotera shop" />
    </div>
</template>

<script>
import ListElement from './ListElement.vue'

export default {
  name: 'ContentList',
  components: {
    ListElement
  },
  props: ['toList']
}
</script>

<style scoped>
    * {
        font-family: Ubuntu;
        font-style: normal;
        font-weight: normal;
        letter-spacing: 0.05em;
        padding: 0;
        margin:0;
    }

    .list-container {
        margin-top: 20px;
        display: flex;
        flex-direction: row;
        justify-content: space-evenly;
    }

    .arrow {
        height:43.85px;
        margin-top: 45px;
    }

</style>
