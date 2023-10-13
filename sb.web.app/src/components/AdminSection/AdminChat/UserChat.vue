<template>
  <div :id="this.userchat" class="user-chat" type="button" @click="this.openChat">
      <p>{{userchat}}</p>
      <p v-if="online" style="color: green;">{{ $t('online') }}</p>
      <p v-if="!online" style="color: red;">{{ $t('offline') }}</p>
  </div> 
</template>

<script>
export default {
  name: 'UserChat',
  props: ['userchat', 'chosenChat'],
  data () {
    return {
      online: false
    }
  },
  methods: {
    openChat () {
      const chats = document.getElementsByClassName('user-chat')
      for (const chat of chats) {
        chat.classList.remove('active')
      } 
      document.getElementById(this.userchat).classList.add('active')
      this.$emit('openChat', this.userchat)
    },
    checkOnlineStatus () {
      for (const user of this.$store.state.allMembers) {
        if (user.accountName === this.userchat && user.last_online) {
          const lastOnline = new Date(user.last_online)
          if (Date.now() - lastOnline < 1000 * 60) { // 1 min
            this.online = true
            return
          }
        }
      }
      this.online = false
    }
  },
  mounted () {
    if (this.chosenChat === this.userchat) {
      this.openChat()
    }
    this.checkOnlineStatus()
    setInterval(async () => {
      this.checkOnlineStatus()
    }, 2000)
  }
}
</script>

<style scoped>
    .user-chat{
      width: 100%;
      height: 75px;
      text-align: center;
      vertical-align: middle;
      line-height: 5px;
      background-color: white; 
      display: flex;
      flex-direction: column;
      padding-top: 25px;
    }
    .user-chat:hover{
      background-color: rgb(230, 230, 230);
    }

    .active{
      background-color: #C4C4C433;
    }

    p {
      font-size: 15px;

    }

</style>
