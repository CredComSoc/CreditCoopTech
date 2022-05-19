<template>
<div id="frame">
  <div id="container-msg-disp">
    <SpeechBubble :v-if="this.messages.length > 0" v-for="(msg,i) in this.messages"
          :sender="msg.sender"
          :reciever="msg.reciever"
          :key="i"
          :message="msg.message"
          :user="this.user"
        />
  </div>
</div>
</template>

<script>

import SpeechBubble from './SpeechBubble.vue'

export default {   
  name: 'MessageDisplay',
  components: {
    SpeechBubble
  },
  props: ['messages', 'user']
}
</script>

<style scoped>

    #container-msg-disp {
        width: 100%;
        max-height: 100%;
        overflow-y: auto;
        position: absolute;
        bottom: 0;
        display: flex;
        flex-direction: column;
        
    }

    #frame {
      overflow:   none;
      position:   relative;
      width:      100%;
      height:     100%;
    }

</style>
