<template>
    <div v-if='sender' class="speech-bubble" id="blue-speech-bubble">
        <p>{{message}}</p>
    </div> 
    <div v-if='!sender' class="speech-bubble" id="gray-speech-bubble">
        <p>{{message}}</p>
    </div>
</template>

<script>
export default {   
  props: ['sender', 'message']

}
</script>

<style scoped>

    .speech-bubble{
        border-radius: 10px;
        border: 10px;
        width: 250px;
        margin-left: 500px;
        padding: 10px;
    }

    #blue-speech-bubble{
        background-color: #5C9BCF4D;
    }

    #gray-speech-bubble{
        background-color: #C7CDD14D;
    }

    p{
        font-size: 16px;
        word-wrap: break-word;
    }

</style>
