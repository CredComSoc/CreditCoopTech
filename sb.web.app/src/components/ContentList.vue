<template>
    <div class="list-container" >
        <ListElement
        v-for="(el) in data.slice(0,counter)"
        :elementInfo="el"
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
  props: ['data', 'screenWidth'],
  watch: {
    screenWidth: {
      handler: function (scrWidth) {
        if (scrWidth > 1212) {
          this.counter = 5
        } else if (scrWidth < 1212 && scrWidth > 900) {
          this.counter = 4
        } else if (scrWidth < 900 && scrWidth > 750) {
          this.counter = 3
        } else if (scrWidth < 600) {
          this.counter = 2
        }
      }
    }
  },
  data () {
    return {
      // make a variable that signal start of for loop above
      counter: 5
    }
  }
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
        height: 43.85px;
        margin-top: 45px;
    }

</style>
