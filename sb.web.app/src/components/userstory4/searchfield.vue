<template>
  <div class="item-center">
    <input :type="search" v-model="message" :placeholder="[[ placeHolderMessage ]]"/>
    <button class="d-inline-block" @click="$emit('searchEvent', message)">
      <!-- Image of magnifying glas -->
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
      </svg>
    </button>
  </div>
</template>

<script>
export default {
  data () {
    return {
      message: '',
      search: String
    }
  },
  props: {
    placeHolderMessage: String
  }
}
</script>
<style scoped>

.item-center {
  display: flex;
  justify-content: center;
}

input {
  width: 30rem;
  border-radius: 10px 0px 0px 10px;
  border-color: black;
}

input:focus{
  outline: none;
}

button {
  height: 100%;
  width: 3rem;
  border-color: black;
  border-radius: 0px 10px 10px 0px;
  border-left: transparent;
}

</style>
