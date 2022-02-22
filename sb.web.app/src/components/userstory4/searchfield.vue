<template>
    <div>
        <input v-model="message" placeholder="Vad vill du söka efter idag?" />
        <!--  emit(EVENT_NAME, VALUE)  -->
        <button @click="$emit('searchEvent', message)">click me</button>
    </div>

</template>

<script>
export default {
  data () {
    return {
      message: ''
    }
  }
}
</script>
<style scoped>

.inputField {}

</style>
