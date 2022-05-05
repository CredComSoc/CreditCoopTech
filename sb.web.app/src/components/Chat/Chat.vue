<template>
  <h1 id="title">MEDDELANDEN</h1>
  <div class="container-chat">
    <ChatHistory :history="this.history"/>
    <ChatBox reciever="Annas Kanelbullar" />
  </div>
</template>

<script>
import ChatHistory from './ChatHistory.vue'
import ChatBox from './ChatBox.vue'

export default {
  name: 'Chat',
  components: {
    ChatHistory,
    ChatBox
  },
  data () {
    return {
      history: [[0, 'Hello'], [1, 'Anna Book'], [2, 'Alicia Bergman']]
    }
  },
  methods: {
  }

}
</script>

<style scoped>
 @import url('https://fonts.googleapis.com/css2?family=Ubuntu:wght@700&display=swap');

 * {
    font-family: 'Ubuntu', sans-serif;
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
    width: 1000px;
    height: 700px;
    position: relative;
    display: flex;
    flex-direction: row;
    justify-content: center;
    gap: 80px;
    align-items: center;
  }

</style>
