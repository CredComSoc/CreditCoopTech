<template>
<div id="frame-input">
  <div id='container-input'>
    <textarea ref="textValue" id="message-field" name="message" oninput='this.style.height = this.scrollHeight +"(px)"'></textarea>
    <button id="send-message" @click="send()">Skicka</button>
    <div class="image-upload">
      <label for="file-input">
       <img src="https://icons.iconarchive.com/icons/dtafalonso/android-lollipop/128/Downloads-icon.png"/>
      </label>
      <input id="file-input" ref="fileValue" type="file" @change="addFile" class="file-input" accept=".jpg, .png, .jpeg, .pdf, .txt "/>
    </div>
  </div>
</div>
</template>

<script>
export default {
  data () {
    return {
      fileData: [],
      messagetype: '',
      filename: ''
    }
  },
  name: 'InputField',
  methods: {
    send () {
      if (this.$refs.textValue.value !== '') {
        this.messagetype = 'string'
        this.filename = ' '
        this.$emit('sendMessage', { message: this.$refs.textValue.value, messagetype: this.messagetype, filename: this.filename })
        this.$refs.textValue.value = ''
      } else if (this.$refs.fileValue.value !== '') {
        this.messagetype = 'file'
        this.filename = ' '
        this.$emit('sendMessage', { message: this.fileData.file, messagetype: this.messagetype, filename: this.filename })
        this.$refs.fileValue.value = ''
      }
    },
    addFile (e) {
      this.fileData.file = e.target.files[0]
      //console.log(this.profileData.logo)
    }
  }
  
}
</script>

<style scoped>
  #container-input {
    position: relative;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    -ms-overflow-style: none;  /* Internet Explorer 10+ */
    scrollbar-width: none;  /* Firefox */
  }

  #container-input::-webkit-scrollbar { 
    display: none;  /* Safari and Chrome */
  }

  #message-field{
    border-radius: 8px;
    border: 2px solid #A8A8A8;
    width: 100%;
    min-height: 85px;
    max-height: 172px;
    resize: none;
    overflow-y: scroll;
    padding: 10px 20px;
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

 #send-message:hover{
     background-color: #0a60a6;
 }
 
 @media (max-height: 500px) {
    #message-field {
      min-height: 40px;
      max-height: 50px;
    }
  } 

  .image-upload>input {
  display: none;
}
</style>
