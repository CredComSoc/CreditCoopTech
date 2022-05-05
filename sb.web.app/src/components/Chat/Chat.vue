<template>
  <h1 id="title">MEDDELANDEN</h1>
  <div id="container-chat">
    <ChatHistory />
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

  #container-chat {
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

  @media (max-width: 1090px) {
    #container-chat {
      width: 700px;
      gap: 50px; 
    }
  }

  @media (max-width: 740px) {
    #container-chat {
      width: 550px;
      gap: 30px; 
    }
  }

  @media (max-width: 580px) {
    #container-chat {
      width: 400px;
      gap: 20px; 
    }
  }

  @media (max-width: 350px) {
    #container-chat {
      width: 300px;
      gap: 10px; 
    }
  }

  @media (max-height: 1169px) {
    #container-chat {
      height: 600px;
    }
  }

  @media (max-height: 1053px) {
    #container-chat {
      height: 550px;
    }
  }

  @media (max-height: 1022px) {
    #container-chat {
      height: 500px;
    }
  }

  @media (max-height: 855px) {
    #container-chat {
      height: 400px;
    }
  }

  @media (max-height: 733px) {
    #container-chat {
      height: 300px;
    }

    #title {
      margin-top: 30px;
      margin-bottom: 30px;
      font-size: 17px;
    }
  }

  @media (max-height: 400px) {
    #container-chat {
      height: 300px;
    }

    #title {
      margin-top: 20px;
      margin-bottom: 20px;
      font-size: 21px;
    }
    
  }

</style>
