<template>
  <div class="user-chat" type="button" v-if="!active" @click="openChat()">
      <p>{{userchat}}</p>
  </div>
  <div class="user-chat active" v-if="active" type="button">
      <p>{{userchat}}</p>
  </div>  
</template>

<script>
export default {
  name: 'UserChat',
  props: ['userchat'],
  methods: {
    openChat () { 
      this.active = true
    }
  },
  data () {
    return {
      active: false
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
    }

    .active{
      background-color: #C4C4C433;
    }

    p{
        font-size: 15px;
    }

</style>
