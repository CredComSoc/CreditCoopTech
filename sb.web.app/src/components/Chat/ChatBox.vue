<template>
  <div id="frame-chatbox">
    <p> {{ this.reciever }} </p>
    <div id="container-chatbox">
      <MessageDisplay ref="msgDisp" :messages="this.messages"/>
      <InputField @sendMessage="this.sendMessage"/>
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
  props: ['reciever'],
  data () {
    return {
      messages: [[0, true, 'bllsndjksdns ckskd clkcjnsdcnjks c s csaskldkasjdlkasjdlkjasd lkasjldkjaslkdjlas dlaksjdkljaslkdja dlaskjdkljaslkdjklasjdlkaja']]//, [1, true, 'ndjjansjd'], [2, false, 'hererer']]
    }
  },
  methods: {
    sendMessage (message) {
      this.messages.push([this.messages.length - 1, true, message])
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
      display: flex;
      flex-direction: column;
      align-items: space-around;
      justify-content: center;
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
