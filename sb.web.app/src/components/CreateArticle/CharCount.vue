<template>
    <p :id="this.id">0/{{this.maxLength}}</p>
</template>

<script>
export default {
  name: 'CharCount',
  props: ['id', 'maxLength'],
  data () {
    return {
      latestLength: 0
    }
  },
  methods: {
    setValue (newValueLength) {
      this.latestLength = newValueLength
      document.getElementById(this.id).innerHTML = this.latestLength + '/' + this.maxLength
    },
    deleteChar () {
      if (this.latestLength > 0) {
        document.getElementById(this.id).innerHTML = this.latestLength - 1 + '/' + this.maxLength
        this.latestLength = this.latestLength - 1
      }
    },
    countChars (text) {
      const textLength = text.value.length + 1
      document.getElementById(this.id).innerHTML = textLength + '/' + this.maxLength
    
      if (textLength > this.maxLength) {
        document.getElementById(this.id).innerHTML = this.maxLength + '/' + this.maxLength
      } else {
        this.latestLength = textLength
      }
    }    
  }
}
</script>
