<template>
<div id="frame-input">
  <div id='container-input'>
    <div class="text-area">
      <textarea ref="textValue" id="message-field" name="message" oninput='this.style.height = this.scrollHeight +"(px)"'></textarea>
      <div id="file">
        <span>File Name:&nbsp;</span>
        <p id="file-title"></p>
        <div type="button" class="g-can" @click="removeFile">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M160 400C160 408.8 152.8 416 144 416C135.2 416 128 408.8 128 400V192C128 183.2 135.2 176 144 176C152.8 176 160 183.2 160 192V400zM240 400C240 408.8 232.8 416 224 416C215.2 416 208 408.8 208 400V192C208 183.2 215.2 176 224 176C232.8 176 240 183.2 240 192V400zM320 400C320 408.8 312.8 416 304 416C295.2 416 288 408.8 288 400V192C288 183.2 295.2 176 304 176C312.8 176 320 183.2 320 192V400zM317.5 24.94L354.2 80H424C437.3 80 448 90.75 448 104C448 117.3 437.3 128 424 128H416V432C416 476.2 380.2 512 336 512H112C67.82 512 32 476.2 32 432V128H24C10.75 128 0 117.3 0 104C0 90.75 10.75 80 24 80H93.82L130.5 24.94C140.9 9.357 158.4 0 177.1 0H270.9C289.6 0 307.1 9.358 317.5 24.94H317.5zM151.5 80H296.5L277.5 51.56C276 49.34 273.5 48 270.9 48H177.1C174.5 48 171.1 49.34 170.5 51.56L151.5 80zM80 432C80 449.7 94.33 464 112 464H336C353.7 464 368 449.7 368 432V128H80V432z"/></svg>
        </div>
      </div>
    </div>
    <div class="buttons">
      <div class="image-upload">
        <label for="file-input">
          <div class="file-clip">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M396.2 83.8c-24.4-24.4-64-24.4-88.4 0l-184 184c-42.1 42.1-42.1 110.3 0 152.4s110.3 42.1 152.4 0l152-152c10.9-10.9 28.7-10.9 39.6 0s10.9 28.7 0 39.6l-152 152c-64 64-167.6 64-231.6 0s-64-167.6 0-231.6l184-184c46.3-46.3 121.3-46.3 167.6 0s46.3 121.3 0 167.6l-176 176c-28.6 28.6-75 28.6-103.6 0s-28.6-75 0-103.6l144-144c10.9-10.9 28.7-10.9 39.6 0s10.9 28.7 0 39.6l-144 144c-6.7 6.7-6.7 17.7 0 24.4s17.7 6.7 24.4 0l176-176c24.4-24.4 24.4-64 0-88.4z"/></svg>
          </div>
        </label>
        <input id="file-input" ref="fileValue" type="file" @change="addFile" class="file-input" accept=".jpg, .png, .jpeg, .pdf, .txt "/>
      </div>
      <button id="send-message" @click="send()">Skicka</button>
    </div>
  </div>
</div>
<!--    -->
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
        document.getElementById('file-title').parentElement.style.display = 'none'
        document.getElementById('message-field').style.display = 'block'
      }
    },
    addFile (e) {
      this.fileData.file = e.target.files[0]
      //console.log(e.target.files[0])
      var fileName = ''
      if (this.fileData.file) {
        fileName = this.fileData.file.name
      }

      var label = document.getElementById('file-title')
      var textbox = document.getElementById('message-field')
      if (fileName !== '') {
        label.innerHTML = fileName
        label.parentElement.style.display = 'flex'
        textbox.value = ''
        textbox.style.display = 'none'
      } else {
        label.innerHTML = 0
        label.parentElement.style.display = 'none'
        textbox.style.display = 'block'
      }
    },
    removeFile () {
      document.getElementById('file-input').value = ''
      document.getElementById('file-title').parentElement.style.display = 'none'
      document.getElementById('message-field').style.display = 'block'
    }
  }
  
}
</script>

<style scoped>
  #container-input {
    margin-top: 8px;
    gap: 8px;
    position: relative;
    width: 100%;
    display: flex;
    flex-direction: column;
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

.text-area{
  width: 100%;
  height: fit-content;
  display: flex;
  flex-direction: column;
  align-items: center;
}

#file{
display: none;
flex-direction: row;
justify-content: flex-start;
align-items: center;
width: 100%;
height: fit-content;

}

#file>p{
  margin: 0px;
}

.buttons{
  width: 100%;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  gap: 15px;
}

.g-can {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
}
.g-can svg {
  width: 15px;
}
.g-can:hover svg path, .g-can:hover {
  background-color: #a60a0a;
  fill: white;
}

.file-clip {
  cursor: pointer;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #4690CD;
}
.file-clip svg {
  width: 20px;
  fill: white;
}
.file-clip:hover svg path, .file-clip:hover {
  background-color: #0a60a6;
  fill: white;
}

</style>
