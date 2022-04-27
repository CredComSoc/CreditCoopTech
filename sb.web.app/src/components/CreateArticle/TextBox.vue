<template>
  <div>
    <label :for="this.name" class="input-title"> {{ this.label }} </label><br>
    <input :maxlength="this.length" ref="titleVal" type="text" :placeholder="this.placeholder" :id="this.id" :name="this.name" @input="this.countChars" >
    <CharCount v-if='!disabled' ref="titleCount" :id="this.id + '-count'" :maxLength="this.length"/>
  </div>
</template>

<script>
import CharCount from './CharCount.vue'
export default {
  name: 'TextBox',
  props: ['id', 'name', 'label', 'placeholder', 'length', 'disabled'],
  components: {
    CharCount
  },
  methods: {
    getInput () {
      return this.$refs.titleVal.value
    },
    setValue (newValue) {
      this.$refs.titleVal.value = newValue
      if (!this.disabled) {
        this.$refs.titleCount.setValue(newValue.length)
      }
    },
    countChars () {
      if (!this.disabled) {
        this.$refs.titleCount.countChars(this.$refs.titleVal.value.length)
      }
    }
  }
}
</script>

<style scoped>

.input-title {
  font-size: 24px;
  font-family: 'Ubuntu', sans-serif;
  font-weight: 700;
  margin-bottom: 10px;
}

input {
  border-radius: 4px;
  border: 2px solid #5c5c5c;
  height: 35px;
  font-size: 13px;
  width: 420px;
  font-family: 'Ubuntu';
}

input::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
  color: #acacac;
  opacity: 0.45; /* Firefox */
}

input:-ms-input-placeholder { /* Internet Explorer 10-11 */
  color: #acacac;
}

input::-ms-input-placeholder { /* Microsoft Edge */
  color: #acacac;
}

input:focus {
  outline: none;     
  border-color: #719ECE;
  border-style: solid;     
  box-shadow: 0 0 10px #719ECE; 
}

input::placeholder {
  padding-left:3px;
  color: #5c5c5c;
}

@media (max-width: 700px) {
  input {
    width: 350px;
  }
}

@media (max-width: 620px) {
  input {
    width: 250px;
    font-size: 12px;
  }
}

@media (max-width: 400px) {
  input {
    width: 200px;
    font-size: 11px;
  }
}

</style>
