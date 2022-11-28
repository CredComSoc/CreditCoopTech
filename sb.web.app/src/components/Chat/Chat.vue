<template>
  <div>
    <h1 id="title">MEDDELANDEN</h1>
    <div id="container-chat">
      <ChatHistory @openChat="this.openChat" :history="this.history" :chosenChat="this.chosenChat"/>
      <ChatBox ref="chatbox" :activeChat="activeChat" :reciever="this.reciever" :user="this.user" @sendMessage="this.sendMessage" @showMembers="this.showMembers"/>
    </div>
    <div v-if="this.showMemberlist" class="member-list-container">
      <H4 v-if="this.allmembers.length === 0">Har startat chat med alla medlemmar</H4>
      <div v-if="this.allmembers.length !== 0" class="member-list">
        <!--<input type="checkbox" :id="member.accountName"  :value="member.accountName" v-model="this.checkedNames">
        <label :for="member.accountName">{{member.accountName}}</label> -->
        <button  v-for="member in this.allmembers" v-bind:key="member" @click="goToChat(member)">{{member}}</button>
      </div>
      <div class="overlaybg" @click="this.showMemberlist = false"></div>
    </div>
  </div>
</template>

<script>
import ChatHistory from './ChatHistory.vue'
import ChatBox from './ChatBox.vue'
import io from 'socket.io-client'
import { EXPRESS_URL, CHAT_URL, getChatHistory, getChatHistories, uploadFile} from '../../serverFetch.js'

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
      chosenChat: null,
      allmembers: [],
      showMemberlist: false,
      checkedNames: []
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
    async sendMessage (message) {
      if (message.messagetype !== 'string') {
        const res = await uploadFile (message.message)
        message.filename = res.name
        message.messagetype = res.fileType
        message.message = res.message
        this.socket.emit('message', {
          message: res.message,
          messagetype: res.fileType,
          filename: res.name,
          sender: this.user,
          id: this.all_chatIDs[this.reciever],
          reciever: this.reciever
        })
      } else {
        this.socket.emit('message', {
          message: message.message,
          messagetype: message.messagetype,
          filename: message.filename,
          sender: this.user,
          id: this.all_chatIDs[this.reciever],
          reciever: this.reciever
        })
      }
      
      this.activeChat.push(message)
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
    getChatHistories (chatid) {
      getChatHistories()
        .then(res => res.json())
        .then(data => {
          if (data.histories) {
            while (this.history.length > 0) {
              this.history.pop()
            }
            for (const [key, value] of Object.entries(data.histories)) {
              this.all_chatIDs[value] = key
              this.history.push(value)
              if (chatid) {
                if (chatid === key) {
                  this.chosenChat = value
                }
              } else if (this.$route.params.chatID) {
                if (this.$route.params.chatID === key) {
                  this.chosenChat = value
                }
              }
            }
            this.user = data.username
            this.getAllMembers()
          }
        })
        .catch(err => console.log(err))
    },
    getAllMembers () {
      while (this.allmembers.length > 0) {
        this.allmembers.pop()
      }
      for (const member of this.$store.state.allMembers) {
        if (this.user === member.accountName) {
          continue
        } else if (this.history.includes(member.accountName)) {
          continue
        } else {
          this.allmembers.push(member.accountName)
        }
      }
      console.log(this.allmembers)
    },
    showMembers () {
      this.showMemberlist = true
    },
    goToChat (accountName) {
      fetch(EXPRESS_URL + '/chat/' + accountName, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include'
      }).then(res => res.json())
        .then(data => {
          if (data !== false) {
            this.getChatHistories(data)
            this.showMemberlist = false
          } else {
            console.log('chat error!!')
            this.chatError = true
          }
        }).catch(err => console.log(err))
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
    font-size: 2.2rem;
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

  .member-list-container{
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100vw;
    height: 100vh;
    position: fixed;
    top: 0px;
    left: 0px;
  }
  .member-list{
    background-color: white;
    border: 1px solid black;
    border-radius: 2px;
    z-index: 2;
    width: 30%;
    max-height: 50vh;
    overflow-y:scroll;
    display: flex;
    flex-direction: column;
  }
  .member-list>button{
    width: 100%;
    padding: 1.5em;
    background-color: white;
    border: 0px ;
  }
  .member-list>button:hover{
    background-color: rgb(230, 230, 230);
  }
  .overlaybg{
  position: absolute;
  top: 0px;
  left: 0px;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.2);
  z-index: 1;  
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
      font-size: 2.2em;
    }
  }

  @media (max-height: 400px) {
    #container-chat {
      height: 300px;
    }

    #title {
      margin-top: 20px;
      margin-bottom: 20px;
      font-size: 2.2em;
    }
    
  }

</style>
