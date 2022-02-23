<template>
    <div :id="name" :class="['list-container']" >
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
        } else if (scrWidth <= 1212 && scrWidth > 900) {
          this.endIndex = 4
        } else if (scrWidth <= 900 && scrWidth > 650) {
          this.endIndex = 3
        } else if (scrWidth < 650 && scrWidth > 350) {
          this.endIndex = 2
        } else if (scrWidth <= 350) {
          this.endIndex = 1
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
    list.addEventListener('animationend', () => {
      list.classList.remove('animate__fadeInLeft')
      list.classList.remove('animate__fadeInRight')
    })
  },
  methods: {
    rotateLeft () {
      // const list = document.getElementById(this.name)
      // list.classList.add('animate__fadeInLeft')
      const dataCopy = this.data
      for (let i = 0; i < this.endIndex; i++) {
        dataCopy.unshift(dataCopy.pop())
      }
    },
    rotateRight () {
      const dataCopy = this.data
      for (let i = 0; i < this.endIndex; i++) {
        dataCopy.push(dataCopy.shift())
      }
      const returnVal = { arr: dataCopy, n: this.name }
      this.$emit('updateData', returnVal)
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
