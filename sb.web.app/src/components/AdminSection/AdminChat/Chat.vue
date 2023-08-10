<template>
  <div>
    <h1 id="title">{{ $t("chat.title") }}</h1>
    <div id="container-chat">
      <ChatHistory @openChat="this.openChat" :history="this.history" :chosenChat="this.chosenChat" @showHistory="this.showMembersList = true"/>
      <ChatBoxMult ref="chatbox" :activeChat="this.activeChat" :reciever="this.reciever" :user="this.user"  @sendMessage="this.sendMessage" @storeMsg="this.storeMsg"/>
    </div>
    <div v-if="this.showMembersList" class="member-list-container">
      <H4 v-if="this.allmembers.length === 0">Ingen medlemmar</H4>
      <div v-if="this.allmembers.length !== 0" class="member-list">
        <label :for="index" v-for=" ( member,index ) in this.allmembers" :key="index">
          {{member}}
          <input type="checkbox" :id="index" :value="member" v-model="this.checkedNames">
        </label>
        <button @click="openMultipleChat">{{ $t('chat.start') }}</button>
      </div>
      <div class="overlaybg" @click="this.showMembersList = false, this.checkedNames = []"></div>
    </div>
  </div>
</template>

<script>
import ChatHistory from './ChatHistory.vue'
import ChatBoxMult from './ChatBoxMult.vue'
import io from 'socket.io-client'
import { EXPRESS_URL, CHAT_URL, getChatHistory, getChatHistories } from '@/serverFetch.js'

export default {
  name: 'Chat',
  components: {
    ChatHistory,
    ChatBoxMult
  },
  data () {
    return {
      history: [],
      history_values: {},
      activeChat: [],
      socket: 0,
      all_chatIDs: {},
      user: '',
      chosenChat: null,
      allmembers: [],
      showMembersList: false,
      checkedNames: [],
      reciever: [],
      msg: {}
    }
  },
  methods: {
    //when clicking a user on ChatHistory
    openChat (userchat) {
      //puts one name in checkedNames
      this.checkedNames.push(userchat)
      this.openMultipleChat()
    },
    
    //can open single or multiple chat depending on how many users are in checkedNames
    async openMultipleChat () {
      //disconnect previous chats
      this.leaveChat()
      
      //If there is no chat id for the user then this creates it
      for (var name of this.checkedNames) {
        if (!this.all_chatIDs[name]) {
          await this.goToChat(name)
        }
      }

      //creates chatroom and joins them for all the users selected
      for (var names of this.checkedNames) {
        const chatRoom = {
          user: this.user,
          chatID: this.all_chatIDs[names]
        }
        this.socket.emit('join', chatRoom)
      }
    
      this.reciever = this.checkedNames
      this.showMembersList = false
      this.checkedNames = []

      //if only one user then load chat history from database
      if (this.reciever.length === 1) {
        this.getChatHistory(this.all_chatIDs[this.reciever[0]])
      } 
    },
    //disconnects all chats that are active
    leaveChat () {
      if (this.reciever.length !== 0) {
        for (var name of this.reciever) {
          this.socket.emit('leave', this.all_chatIDs[name])
        }
        this.reciever = []
        this.activeChat = []
      }
    },
    //call a function in backend for storing message to the database
    sendMessage (message) {
      this.socket.emit('message', {
        message: message.message,
        messagetype: message.messagetype,
        filename: message.filename,
        sender: this.user,
        id: this.all_chatIDs[message.reciever],
        reciever: message.reciever
      })
      //save an instance of that message for putting it in activeChat
      this.msg = message
    },
    //putting the instance of a message in activeChat
    storeMsg () {
      this.activeChat.push(this.msg)
    },
    getChatHistory (chatID) {
      //load chat history from database
      getChatHistory(chatID)
        .then(res => res.json())
        .then(data => {
          this.history_values[this.reciever] = data
          //put all the chat history in the activeChat for Chatbox to show it
          this.activeChat = this.history_values[this.reciever]
          if (this.activeChat.length > 0) {
            this.$refs.chatbox.scrolltoBottom()
          }
        })
        .catch(err => console.log(err))
    },
    async getChatHistories (chatid) {
      //get the list of user chat id that this user have initiated
      await getChatHistories()
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
      if (this.allmembers.length > 0) {
        this.allmembers = []
      }
      for (const member of this.$store.state.allMembers) {
        if (this.user === member.accountName) {
          continue
        } else {
          this.allmembers.push(member.accountName)
        }
      }
    },
    async goToChat (accountName) {
      await fetch(EXPRESS_URL + '/chat/' + accountName, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include'
      }).then(res => res.json())
        .then(async data => {
          if (data !== false) {
            await this.getChatHistories(data)
          } else {
            alert("Chat error!")
            //console.log('chat error!!')
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
      //console.log(event, args)
    })
  },
  beforeUnmount () {
    this.leaveChat()
    this.socket.disconnect()
  }
}
</script>

<style scoped>
 @import url('https://fonts.googleapis.com/css2?family=Ubuntu:wght@700&display=swap');


 

  #title {
    font-variant: all-petite-caps;
    margin-top: 2rem;
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
  .member-list>label{
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    width: 100%;
    padding: .5rem 1.5rem;
  }
  .member-list>label:hover{
    background-color: rgb(230, 230, 230);
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
      font-variant: all-petite-caps;
      margin-top: 5px;
      margin-bottom: 30px;
      font-size: 2.2em;
    }
  }

  @media (max-height: 400px) {
    #container-chat {
      height: 300px;
    }

    #title {
      font-variant: all-petite-caps;
      margin-top: 5px;
      margin-bottom: 20px;
      font-size: 2.2em;
    }
    
  }

</style>
