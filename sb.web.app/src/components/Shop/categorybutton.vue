<!--
*   Categorybutton, displays relevant prop values and will emit filterEvent-event if pressed
-->

<template>
    <div className="label">
        <input type="checkbox" v-model="checked" @click="$emit('filterEvent', checked, type, value)" >
        <p>{{title}}</p>
    </div>
</template>

<script>
export default {

  data () {
    return {
      checked: false
    }
  },
  props: {
    type: String,
    value: String,
    title: String
  }
}
</script>
<style scoped>
  input {
    margin-right:0.2rem;
    margin-top: 0.1rem
  }

  .label {
    display:flex;
    padding:0;
    line-height: 0.9;
  }
</style>
