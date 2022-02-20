<template>
    <div :id="name" :class="['list-container']" >
        <img class="arrow" src="../assets/list_images/left_arrow.png" alt="rotera shop" v-if="startIndex != 0" @click="rotateLeft"/>
        <ListElement
        v-for="(el) in data.slice(startIndex,endIndex)"
        :elementInfo="el"
        :key="el.id"
        ></ListElement>
        <img class="arrow" src="../assets/list_images/right_arrow.png" alt="rotera shop" v-if="endIndex != data.length" @click="rotateRight" />
    </div>
</template>

<script>
import ListElement from './ListElement.vue'

export default {
  name: 'ContentList',
  components: {
    ListElement
  },
  props: ['data', 'screenWidth', 'name'],
  watch: {
    screenWidth: {
      handler: function (scrWidth) {
        if (scrWidth > 1212) {
          this.endIndex = 5
        } else if (scrWidth < 1212 && scrWidth > 900) {
          this.endIndex = 4
        } else if (scrWidth < 900 && scrWidth > 750) {
          this.endIndex = 3
        } else if (scrWidth < 600) {
          this.endIndex = 2
        }
      }
    }
  },
  data () {
    return {
      // make a variable that signal start of for loop above
      startIndex: 0,
      endIndex: 5
    }
  },
  mounted () {
    const list = document.getElementById(this.name)
    list.classList.add('animate__animated')
    list.classList.add('animate__faster')
    list.addEventListener('animationend', () => {
      list.classList.remove('animate__backInLeft')
      list.classList.remove('animate__backInRight')
    })
  },
  methods: {
    rotateLeft () {
      if (this.endIndex - this.data.length >= 5) {
        this.endIndex -= 5
        this.startIndex -= 5
      } else {
        this.endIndex -= this.data.length - this.endIndex
        this.startIndex -= this.endIndex + 1
        console.log(this.endIndex)
        console.log(this.startIndex)
      }
      const list = document.getElementById(this.name)
      list.classList.add('animate__backInLeft')
    },
    rotateRight () {
      if (this.data.length - this.endIndex >= 5) {
        this.endIndex += 5
        this.startIndex += 5
      } else {
        this.endIndex += this.data.length - this.endIndex
        this.startIndex += this.endIndex - 1
        console.log(this.endIndex)
        console.log(this.startIndex)
      }
      const list = document.getElementById(this.name)
      list.classList.add('animate__backInRight')
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
