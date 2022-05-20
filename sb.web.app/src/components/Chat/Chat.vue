<template>
  <div>
    <h1 id="title">MEDDELANDEN</h1>
    <div id="container-chat">
      <ChatHistory @openChat="this.openChat" :history="this.history" :chosenChat="this.chosenChat"/>
      <ChatBox ref="chatbox" :activeChat="activeChat" :reciever="this.reciever" :user="this.user" @sendMessage="this.sendMessage"/>
    </div>
  </div>
</template>

<script>
import ChatHistory from './ChatHistory.vue'
import ChatBox from './ChatBox.vue'
import io from 'socket.io-client'
import { CHAT_URL, getChatHistory, getChatHistories } from '../../serverFetch.js'

export default {
  name: 'Chat',
  components: {
    ChatHistory,
    ChatBox
  },
  data () {
    return {
      history: [],
      history_values: {},
      activeChat: [],
      reciever: '',
      socket: 0,
      all_chatIDs: {},
      user: '',
      chosenChat: null
    }
  },
  methods: {
    openChat (userchat) {
      if (this.reciever !== '') {
        this.socket.emit('leave', this.all_chatIDs[this.reciever])
      }
      const chatRoom = {
        user: this.user,
        chatID: this.all_chatIDs[userchat]
      }
      this.socket.emit('join', chatRoom)
      this.reciever = userchat
      this.getChatHistory(this.all_chatIDs[userchat])
    },
    sendMessage (message) {
      this.activeChat.push(message)
      this.socket.emit('message', {
        message: message.message,
        sender: this.user,
        id: this.all_chatIDs[this.reciever],
        reciever: this.reciever
      })
    },
    getChatHistory (chatID) {
      getChatHistory(chatID)
        .then(res => res.json())
        .then(data => {
          this.history_values[this.reciever] = data
          this.activeChat = this.history_values[this.reciever]
          if (this.activeChat.length > 0) {
            this.$refs.chatbox.scrolltoBottom()
          }
        })
        .catch(err => console.log(err))
    },
    getChatHistories () {
      getChatHistories()
        .then(res => res.json())
        .then(data => {
          if (data.histories) {
            for (const [key, value] of Object.entries(data.histories)) {
              this.all_chatIDs[value] = key
              this.history.push(value)
              if (this.$route.params.chatID) {
                if (this.$route.params.chatID === key) {
                  this.chosenChat = value
                }
              }
            }
            this.user = data.username
          }
        })
        .catch(err => console.log(err))
    }
  },
  created () {
    this.getChatHistories()
    this.socket = io(CHAT_URL)
  
    this.socket.on('message', (data) => {
      this.activeChat.push(data)
      this.$refs.chatbox.scrolltoBottom()
    })

    this.socket.onAny((event, ...args) => {
      console.log(event, args)
    })
  },
  beforeUnmount () {
    this.socket.disconnect()
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
