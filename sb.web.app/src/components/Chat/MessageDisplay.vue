<template>
  <div id="container-msg-disp">
    <SpeechBubble v-for="(msg) in this.messages"
          :sender="msg[1]"
          :key="msg[0]"
          :message="msg[2]"
        />
  </div>
</template>

<script>

import SpeechBubble from './SpeechBubble.vue'

export default {   
  name: 'MessageDisplay',
  components: {
    SpeechBubble
  },
  data () {
    return {
      messages: [[0, true, 'bllsndjksdns ckskd clkcjnsdcnjks c s csaskldkasjdlkasjdlkjasd lkasjldkjaslkdjlas dlaksjdkljaslkdja dlaskjdkljaslkdjklasjdlkaja'], [1, true, 'ndjjansjd'], [2, false, 'hererer']]
    }
  }
}
</script>

<style scoped>

    #container-msg-disp {
        width: 100%;
        height: 100%;
        overflow-y: scroll;
    }

</style>
