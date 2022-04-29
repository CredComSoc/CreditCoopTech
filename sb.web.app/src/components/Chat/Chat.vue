<template>
  <div class="chat">
  <h1 id="title">MEDDELANDEN</h1>
  <InputField/>
  </div>
</template>

<script>
import InputField from './InputField.vue'
export default {
  name: 'Chat',
  components: {
    InputField
  }

}
</script>

<style scoped>
 @import url('https://fonts.googleapis.com/css2?family=Ubuntu:wght@700&display=swap');

 * {
  font-family: Ubuntu;
  }

  #title {
    margin-top: 4rem;
    margin-bottom: 4rem;
    font-size: 1.5rem;
    letter-spacing: 0.3em;
  }

  .chat {
    margin: 0 auto;
    margin-top: 30px;
    width: 80%;
    font-family: 'Ubuntu';
    position: relative;
    text-align: center;
  }

</style>
