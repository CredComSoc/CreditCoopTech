<template>
    <div class="fill">
        <div v-if='sender' class="speech-bubble" id="blue-speech-bubble">
            <p>{{message}}</p>
        </div> 
        <div v-if='!sender' class="speech-bubble" id="gray-speech-bubble">
            <p>{{message}}</p>
        </div>
    </div>
</template>

<script>
export default {   
  props: ['sender', 'message']

}
</script>

<style scoped>
    .fill{
        width: 100%;
        display: block;
        margin-bottom: 15px;
    }
    .speech-bubble{
        border-radius: 10px;
        border: 10px;
        width: 300px;
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

</style>
