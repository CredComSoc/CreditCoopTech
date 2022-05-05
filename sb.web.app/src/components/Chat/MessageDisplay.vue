<template>
<div id="frame">
  <div id="container-msg-disp">
    <SpeechBubble v-for="(msg) in this.messages"
          :sender="msg[1]"
          :key="msg[0]"
          :message="msg[2]"
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
  props: ['messages']
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
