<template>
    <div class="fill">
        <div v-if='sender === user' class="speech-bubble" id="blue-speech-bubble">
            <div v-if= 'messagetype === "image/jpeg" || messagetype === "image/png" || messagetype === "image/gif"'> <img :src= 'getImgURL(filename)' class= "image" alt="Picture"></div>
            <div v-else-if= 'messagetype === "file" || messagetype === "text/plain"|| messagetype === "application/pdf"'><a target="_blank" :href='getFileURL(filename)'>{{message}}</a></div>
            <div v-else><p>{{message}}</p></div>
        </div> 
        <div v-else class="speech-bubble" id="gray-speech-bubble">
            <p>{{message}}</p>
            <img src="../../assets/sb2.png" alt="Picture">
        </div>
    </div>
</template>

<script>
import { EXPRESS_URL } from '../../serverFetch' 

export default {   
  /*data () {
    return {
      filetype: 'image/jpeg'
    }
  },*/
  props: ['sender', 'message', 'reciever', 'user', 'messagetype', 'filename'],

  methods: {
    getImgURL (filename) {
      return EXPRESS_URL + '/image/' + filename 
    },
    getFileURL (filename) {
      return EXPRESS_URL + '/file/' + filename 
    }
  } 
}
</script>

<style scoped>
    .fill{
        width: 100%;
        display: block;
        margin-bottom: 1rem;
        margin-top: 0.7rem;
    }
    .speech-bubble{
        border-radius: 10px;
        border: 10px;
        width: 50%;
        padding: 10px;
        overflow: auto;
    }
    
    .image{
        object-fit: cover;
        width: 100%;
        height: 60%;
    }

    #blue-speech-bubble{
        background-color: #5C9BCF4D;
        float: right;
    }

    #gray-speech-bubble{
        background-color: #C7CDD14D;
    }

    p{
        font-size: 16px;
        word-wrap: break-word;
    }

    @media (max-width: 580px) {
        p {
            font-size: 14px;
        }
  }

</style>
