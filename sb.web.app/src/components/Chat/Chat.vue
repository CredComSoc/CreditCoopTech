<template>
  <h1 id="title">MEDDELANDEN</h1>
  <SpeechBubble :sender="false" :message="'hello this is me'"/>
  <div class="container-chat">
    <ChatHistory />
    <InputField />
  </div>
</template>

<script>
import InputField from './InputField.vue'
import ChatHistory from './ChatHistory.vue'
import SpeechBubble from './SpeechBubble.vue'

export default {
  name: 'Chat',
  components: {
    InputField,
    ChatHistory,
    SpeechBubble
  }

}
</script>

<style scoped>
 @import url('https://fonts.googleapis.com/css2?family=Ubuntu:wght@700&display=swap');

 * {
    font-family: Ubuntu;
    padding: 0;
    margin: 0;
  }

  #title {
    margin-top: 4rem;
    margin-bottom: 4rem;
    font-size: 1.5rem;
    letter-spacing: 0.3em;  
    text-align: center;
  }

  .container-chat {
    margin: 0 auto;
    margin-top: 30px;
    width: 90%;
    position: relative;
    display: flex;
    flex-direction: row;
    justify-content:space-evenly;
    align-items: center;
  }

</style>
