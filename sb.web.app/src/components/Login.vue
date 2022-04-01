<template>
  <form @submit.prevent="handleSubmit">
    <h1>Login</h1>
    <div>
      <label>Username</label>
      <input type="text" v-model="username" name="">
    </div>
    <div>
      <label>Password</label>
      <input type="password" v-model="password" name="">
    </div>
    <button>Login</button>
  </form>
</template>

<script>
import { login } from '../serverFetch'
import { useRouter } from 'vue-router'
const router = useRouter()

export default {
  name: 'Login',
  data () {
    return {
      username: '',
      password: ''
    }
  },
  methods: {
    async handleSubmit () {
      login(this.username, this.password).then((response) => {
        console.log(response)
        if (response) {     
          this.$router.push({ name: 'Home' })
        }
      })
    }
  }

}

</script>

<!-- Add 'scoped' attribute to limit CSS to this component only -->
<style scoped>

</style>
