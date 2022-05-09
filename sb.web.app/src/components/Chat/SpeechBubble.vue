<template>
    <div class="fill">
        <div v-if='sender === user' class="speech-bubble" id="blue-speech-bubble">
            <p>{{message}}</p>
        </div> 
        <div v-else class="speech-bubble" id="gray-speech-bubble">
            <p>{{message}}</p>
        </div>
    </div>
</template>

<script>
export default {   
  props: ['sender', 'message', 'reciever', 'user']

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
