<template>
<div id='container-input'>
  <textarea type="text" ref="textValue" id="message-field" name="message"/>
  <button id="send-message" @click="send()">Skicka</button>
</div>
</template>

<script>
export default {
  name: 'InputField',
  methods: {
    send () {
      const message = this.$refs.textValue.value
      this.$emit('sendMessage', message)
      this.$refs.textValue.value = ''
    }
  }
  
}
</script>

<style scoped>

  #container-input {
    position: relative;
    display: flex;
    flex-direction: row;
    align-items: center;
    width: 100%;
  }

  #message-field{
    border-radius: 8px;
    border: 2px solid #A8A8A8;
    width: 100%;
    min-height: 35px;
    resize: none;
    overflow: hidden;
 }

 textarea:focus {
    outline: none;
    box-shadow: 0 0 10px #4690CD;
 }

 #send-message{
     margin-left: 15px;
     border-radius: 4px;
     background-color: #4690CD;
     color: white;
     font-family: 'Ubuntu', sans-serif;
     border: none;
     width: 70px;
     height: 30px;
 }

</style>
