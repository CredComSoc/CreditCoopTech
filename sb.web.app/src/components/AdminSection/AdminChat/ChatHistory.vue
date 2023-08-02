<template>
  <div id='container-frame'>
    <h3>{{ $t('chat.your_chats') }}</h3>
    <div id="container-outer">
      <div id="container-history">
        <UserChat @openChat="this.openChat" v-for="(h, i) in this.history"
        :userchat="h"
        :key="i"
        :chosenChat="this.chosenChat"
        />
      </div>
    </div>
    <button class="ChatBtn" @click="this.$emit('showHistory')">{{ $t('chat.start_new') }}</button>
  </div>
</template>

<script>

import UserChat from './UserChat.vue'

export default {
  name: 'ChatHistory',
  props: ['history', 'chosenChat'],
  components: {
    UserChat
  },
  methods: {
    openChat (userchat) {
      //call a function in Chat.vue
      this.$emit('openChat', userchat)
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Ubuntu:wght@700&display=swap');



h3 {
  font-weight: 700;
  font-size: 18px;
  text-align: center;
  margin-bottom: 10px;        
}

#container-history {
  border-radius: 10px;
  width: 300px;
  height: 100%;
  overflow-y: auto;
  overflow-x: hidden;
  -ms-overflow-style: none;  /* Internet Explorer 10+ */
  scrollbar-width: none;  /* Firefox */
}

#container-history::-webkit-scrollbar { 
  display: none;  /* Safari and Chrome */
}

#container-outer {
  border-radius: 10px;
  height: 100%;
  border: 2px solid #A8A8A8;
}

#container-frame {
  position: relative;        
  height: 100%;
}

.ChatBtn{
  border-radius: 4px;
  background-color: #4690CD;
  color: white;
  font-family: 'Ubuntu', sans-serif;
  border: none;
  width: fit-content;
  height: fit-content;
  margin-top: 10px;
  padding: 4px 12px;
}

.ChatBtn:hover{
  background-color: #0a60a6;
}

@media (max-width: 1090px) {
  #container-history {
    width: 220px;
  }
}

@media (max-width: 740px) {
  #container-history {
    width: 150px;
  }
}

@media (max-width: 580px) {
  #container-history {
    width: 129px; 
  }
  h3 {
    font-size: 14px;
  }
}

@media (max-width: 350px) {
  #container-history{
    width: 110px;
  }
  
  h3 {
    font-size: 14px;
  }
}

</style>
