<template>
  <div>
    <label :for="this.name" class="input-title"> {{ this.label }} </label><br>
    <textarea ref="descVal" :placeholder="this.placeholder" :name="this.name" :maxlength="this.length" @input="this.countChars"/>
    <CharCount ref="descriptionCount" :id="this.name + '-count'" :maxLength="this.length"/>
  </div>
</template>

<script>
import CharCount from './CharCount.vue'
export default {
  name: 'TextArea',
  props: ['name', 'label', 'placeholder', 'length'],
  components: {
    CharCount
  },
  methods: {
    getInput () {
      return this.$refs.descVal.value
    },
    setValue (newValue) {
      this.$refs.descVal.value = newValue
      this.$refs.descriptionCount.setValue(newValue.length)
    },
    countChars () {
      this.$refs.descriptionCount.countChars(this.$refs.descVal.value.length)
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

textarea::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
  color: #acacac;
  opacity: 0.45; /* Firefox */
}

textarea:-ms-input-placeholder { /* Internet Explorer 10-11 */
  color: #acacac;
}

textarea::-ms-input-placeholder { /* Microsoft Edge */
  color: #acacac;
}

textarea {
  height: 100px;
  width: 420px;
  resize: none;
  border-radius: 4px;
  font-family: 'Ubuntu';
  font-size: 13px;
  border: 2px solid #5c5c5c;
}

textarea::placeholder {
  padding-left:3px;
  color: #5c5c5c;
}

textarea:focus {
  outline: none;     
  border-color: #719ECE;
  border-style: solid;     
  box-shadow: 0 0 10px #719ECE; 
}

@media (max-width: 700px) {
  textarea {
    width: 350px;
  }
}

@media (max-width: 620px) {
  textarea {
    width: 250px;
    font-size: 12px;
  }
}

@media (max-width: 400px) {
  textarea {
    width: 200px;
    font-size: 11px;
  }
}

</style>
