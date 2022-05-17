<template>
  <div :id="this.userchat" class="user-chat" type="button" @click="this.openChat">
      <p>{{userchat}}</p>
  </div> 
</template>

<script>
export default {
  name: 'UserChat',
  props: ['userchat', 'chosenChat'],
  methods: {
    openChat () {
      const chats = document.getElementsByClassName('user-chat')
      for (const chat of chats) {
        chat.classList.remove('active')
      } 
      document.getElementById(this.userchat).classList.add('active')
      this.$emit('openChat', this.userchat)
    }
  },
  mounted () {
    console.log(this.listIndex)
    if (this.chosenChat === this.userchat) {
      this.openChat()
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
