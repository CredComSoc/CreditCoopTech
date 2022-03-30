<template>
  <div id="create-header">
    <img v-if="imgURL !== null" class="step-indicator-img" :src="require(`../../assets/link_arrow/${this.imgURL}`)" />
    <a :href=this.link @click=goBackStep><img class="arrow" src="../../assets/link_arrow/left_arrow_link.png"/>{{ this.ButtonText }}</a>
  </div>
</template>

<script>
export default {
  name: 'CreateHeader',
  props: ['ButtonText', 'link', 'imgURL'],
  methods: {
    goBackStep () {
      this.$emit('goBackStep')
    }
  }
}
</script>

<style scoped>

#create-header {
  width: 100%;
}

.arrow{
  width: 12px;
  margin-left: 2px;
}

.step-indicator-img {
  width: 80px;
  float: right;
}

a{
  float: left;
  color: black;
  font-size: 22px;
}

a:hover{
  color: black;
}

@media (max-width: 400px) {
  a{
    font-size: 18px;
  }

}
</style>
