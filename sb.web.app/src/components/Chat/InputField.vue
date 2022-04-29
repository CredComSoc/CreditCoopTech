<template>
  <textarea type="text" id="message-field" name="message"/>
  <button id="send-message">Skicka</button>
</template>

<script>
export default {

}
</script>

<style scoped>
  #message-field{
    border-radius: 8px;
    border: 2px solid #5c5c5c;
    width: 400px;
    min-height: 35px;
    resize: none;
    overflow: hidden;
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
