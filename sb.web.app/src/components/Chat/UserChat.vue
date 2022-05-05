<template>
  <div :id="id" class="user-chat" type="button" v-if="!active" @click="openChat()">
      <p>{{userchat}}</p>
  </div> 
</template>

<script>
export default {
  name: 'UserChat',
  props: ['userchat', 'id'],
  methods: {
    openChat () {
      const chats = document.getElementsByClassName('user-chat')
      for (const chat of chats) {
        chat.classList.remove('active')
      } 
      document.getElementById(this.id).classList.add('active')
    }
  }

}
</script>

<style scoped>
    .user-chat{
      width: 100%;
      height: 75px;
      text-align: center;
      vertical-align: middle;
      line-height: 75px;
      background-color: white; 
    }

    .active{
      background-color: #C4C4C433;
    }

    p{
        font-size: 15px;
    }

</style>
