<template>
    <p :id="this.id" :v-model="this.currentLength"> {{this.currentLength}}/{{this.maxLength}}</p>
</template>

<script>
export default {
  name: 'CharCount',
  props: ['id', 'maxLength'],
  data () {
    return {
      latestLength: 0,
      currentLength: 0
    }
  },
  methods: {
    setValue (newValueLength) {
      this.latestLength = newValueLength
      this.currentLength = newValueLength
    },
    countChars (textLen) {
      this.currentLength = textLen
    }    
  }
}
</script>
