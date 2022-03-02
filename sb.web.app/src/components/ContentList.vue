<template>
    <div :id="name" class="list-container" >
        <img class="arrow" src="../assets/list_images/left_arrow.png" alt="rotera shop" @click="rotateLeft"/>
          <ListElement
          v-for="(el) in this.data.slice(0,endIndex)"
          :elementInfo="el"
          :key="el.id"
          ></ListElement>
        <img class="arrow" src="../assets/list_images/right_arrow.png" alt="rotera shop" @click="rotateRight" />
    </div>
</template>

<script>
// Component that contain ListElements and is able to rotate between items

import ListElement from './ListElement.vue'

export default {
  name: 'ContentList',
  components: {
    ListElement
  },
  props: ['data', 'screenWidth', 'name'],
  watch: {
    // when screen width resizes check how many items is gonna be shown in list
    screenWidth: {
      handler: function (scrWidth) {
        this.setItems(scrWidth)
      }
    }
  },
  data () {
    return {
      startIndex: 0,
      endIndex: 5
    }
  },
  mounted () {
    this.setItems(this.screenWidth)

    // const list = document.getElementById(this.name)
    // list.classList.add('animate__animated')
    // list.addEventListener('animationend', () => {
    //   list.classList.remove('animate__fadeInLeft')
    //   list.classList.remove('animate__fadeInRight')
    // })
  },
  methods: {
    // Set the number of items in list, depending on screen width
    setItems (scrWidth) {
      if (scrWidth > 1212) {
        this.endIndex = 5
      } else if (scrWidth <= 1212 && scrWidth > 900) {
        this.endIndex = 4
      } else if (scrWidth <= 900 && scrWidth > 650) {
        this.endIndex = 3
      } else if (scrWidth < 650 && scrWidth > 350) {
        this.endIndex = 2
      } else if (scrWidth <= 350) {
        this.endIndex = 1
      }
    },
    // rotate items in list to left number of endIndex times, the rotated items gets placed last in list
    rotateLeft () {
      // const list = document.getElementById(this.name)
      // list.classList.add('animate__fadeInLeft')
      const dataCopy = this.data
      for (let i = 0; i < this.endIndex; i++) {
        dataCopy.unshift(dataCopy.pop())
      }
    },
    // rotate items in list to right number of endIndex times, the rotated items gets placed first in list
    rotateRight () {
      const dataCopy = this.data
      for (let i = 0; i < this.endIndex; i++) {
        dataCopy.push(dataCopy.shift())
      }

      // const list = document.getElementById(this.name)
      // list.classList.add('animate__fadeInRight')
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
        position: relative;
    }

    span {
      display: block;
    }

    .arrow {
        height: 43.85px;
        margin-top: 45px;
    }

    img {
      cursor: pointer;
    }

    img:hover {
      transform: scale(1.1);
    }

</style>
