<template>
<div id='container-frame'>
    <h3>DINA CHATTAR</h3>
    <div id="container-outer">
        <div id="container-history">
            <UserChat @openChat="this.openChat" v-for="(h, i) in this.history"
                        :userchat="h"
                        :key="i"
                        :chosenChat="this.chosenChat"
                        />
        </div>
    </div>
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
      this.$emit('openChat', userchat)
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Ubuntu:wght@700&display=swap');
    * {
        box-sizing: border-box;
        padding: 0;
        margin: 0;
        font-family: 'Ubuntu', sans-serif;
    }

    h3 {
        font-weight: 700;
        font-size: 18px;
        text-align: center;
        margin-bottom: 10px;        
    }

    #container-history {
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
