<template>
  <div id="frame-chatbox">
    <p v-if="this.reciever.length > 0"> {{ this.reciever }} </p>
    <p v-else> &nbsp;</p>
    <Button class="start-chat" @click="this.$emit('showMembers')"> Starta nytt Chat</Button>
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
      this.$emit('sendMessage', { sender: this.user, reciever: this.reciever, message: message.message, messagetype: message.messagetype, filename: message.filename })
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
    .start-chat{
      border-radius: 5px;
      background-color: #4690CD;
      color: white;
      font-family: 'Ubuntu', sans-serif;
      border: none;
      width: fit-content;
      height: fit-content;
      padding: 2px 8px;
      position: absolute;
      top: 0px;
      right: 0px;
    }
    .start-chat:hover{
     background-color: #0a60a6;
    }
    
</style>
