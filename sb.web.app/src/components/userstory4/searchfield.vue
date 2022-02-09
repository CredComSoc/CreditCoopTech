<template>
    <div>
        <input v-model="message" placeholder="edit me" />
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
