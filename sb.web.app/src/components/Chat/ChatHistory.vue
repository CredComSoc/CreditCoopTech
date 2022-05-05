<template>
<div id='container-frame'>
    <h3>DINA CHATTAR</h3>
    <div id="container-outer">
        <div id="container-history">
            <UserChat v-for="(h) in this.history"
                        :userchat="h[1]"
                        :key="h[0]"
                        />
        </div>
    </div>
</div>
</template>

<script>

import UserChat from './UserChat.vue'

export default {
  name: 'ChatHistory',
  props: ['history'],
  components: {
    UserChat
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
        overflow-y: scroll;
        overflow-x: hidden;
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
</style>
