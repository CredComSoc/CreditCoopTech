<template>
  <div id="frame-chatbox">
    <p> {{ this.reciever }} </p>
    <div id="container-chatbox">
      <MessageDisplay />
      <InputField />
    </div>
  </div>
</template>

<script>
import InputField from './InputField.vue'
import MessageDisplay from './MessageDisplay.vue'

export default {
  name: 'ChatBox',
  components: {
    InputField,
    MessageDisplay
  },
  props: ['reciever']
}
</script>

<style scoped>
    #container-chatbox {
      border-top: 2px solid #A8A8A8;
      width: 100%;
      height: 100%;
    }

    p {
      font-family: 'Ubuntu';
      font-style: normal;
      font-weight: 400;
      font-size: 15px;
      margin-left: 20px;
    }

    #frame-chatbox {
      position: relative;
      height: 100%;
      width: 100%;
    }
</style>
