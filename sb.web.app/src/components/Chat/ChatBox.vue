<template>
  <div id="frame-chatbox">
    <p v-if="this.reciever.length > 0"> {{ this.reciever }} </p>
    <p v-else> Välj någon att chatta med </p>
    <div id="container-chatbox">
      <MessageDisplay ref="msgDisp" :messages="this.activeChat" :user="this.user" />
      <InputField v-if="this.reciever.length > 0" @sendMessage="this.sendMessage"/>
    </div>
  </div>
</template>

<script>
import InputField from './InputField.vue'
import MessageDisplay from './MessageDisplay.vue'
import { nextTick } from 'vue'

export default {
  name: 'ChatBox',
  components: {
    InputField,
    MessageDisplay
  },
  props: ['reciever', 'activeChat', 'user'],
  methods: {
    sendMessage (message) {
      this.$emit('sendMessage', { sender: this.user, reciever: this.reciever, message: message })
      this.scrolltoBottom()
    },
    scrolltoBottom () {
      nextTick(function () {
        const container = document.getElementById('container-msg-disp').lastElementChild
        container.scrollIntoView({ behavior: 'smooth', block: 'end' })
      })
    }
  }
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
