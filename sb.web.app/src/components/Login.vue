<template>
<div>
  <div id="header-box" class="header-container">
    <header>
      <nav>
        <div class="middle-logo">
          <div class="navlogo">
            <figure>
              <img src="../assets/navbar_logos/sb.png" alt="shop knapp"/>
            </figure>
          </div>
        </div>
      </nav>
    </header>
  </div>
  <div class="login-box">
    <form @submit.prevent="handleSubmit">
      <div class="box-text">Logga in på Svensk Barter</div>
      <div>
        <label class="box-label">E-postadress</label>
        <input class="box-input" type="text" v-model="username" name="" placeholder="e-postadress@hemsida.sv" id="email-input" required>
      </div>
      <div>
        <label class="box-label">Lösenord</label>
        <input class="box-input" type="password" v-model="password" name="" placeholder="Lösenord" id="password-input" required>
      </div>
      <button id="login-button" >Logga in</button>
    </form>
    <div class="box-link">
      <a href="/forgot">Återställ lösenord</a>
    </div> 
    <div class="box-error" v-if="this.error">
      Fel epost eller lösenord ({{ loginCount }})
    </div>
  </div>
</div>

</template>

<script>
import { login } from '../serverFetch'

export default {
  name: 'Login',
  data () {
    return {
      username: '',
      password: '',
      error: false,
      loginCount: 0
    }
  },
  methods: {
    async handleSubmit () {
      login(this.username.toLowerCase(), this.password).then((response) => {
        console.log(response)
        if (response) {
          this.error = false  
          this.loginCount = 0   
          window.location.reload()
          this.error = false 
        } else {
          this.error = true
          this.loginCount += 1
        } 
      })
    }
  }

}

</script>

<!-- Add 'scoped' attribute to limit CSS to this component only -->
<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.login-box {
    font-family: Ubuntu;
    font-style: Regular;
    font-size:  20px;
    text-align: left;
    letter-spacing: 0.05em;
    padding-left: 19px;
    padding-right: 19px;
    width: 300px;
    height: 570px;
    border-radius: 20px;
    margin: auto;
    margin-top: 15%;
    position: relative;
    background: rgba(246, 202, 115, 0.27);
}

button {
  margin-right: 10px;
  border-radius: 5px;
  font-size: 1.2rem;
  padding: 2px 12px 2px 12px;
  background-color: #F3F3F3;
}

.box-text {
  padding-top: 20px;
  padding-bottom: 48px;
}

.box-label {
  padding-bottom: 8px;
}

.box-input {
  border: 0px;
  width: 240px;
  height: 34px;
  background-color: #F3F3F3;
  margin-left: 20px;
  margin-bottom: 36px;
  padding-left: 8px;
}

.box-error{
  font-size: 14px;
  text-align: center;
  color: red;
  margin-top: 50px;
}

.box-link {
  margin-top: 50px;
  text-align: center;
  font-size: 16px;
}

input {
  font-size: 18px;
}

input::placeholder {
  font-size:  14px;
  font-style: italic;
}

input:focus,
select:focus,
textarea:focus,
button:focus {
    outline: none;
}

html {
  scroll-behavior: smooth;
}

.header-container {
  width: 100%;
  height: 80px;
  top: 0;
  z-index: 3;
  position: fixed;
  background: #fff;
  border-bottom: 1px solid black;
}

 nav {
  background-color: white;
  overflow: visible;
  display: flex;
  margin-bottom: 0;
  margin-top: 0;
  flex-direction: row;
  justify-content: space-evenly;
}

.middle-logo {
  flex-shrink: 0;
  margin-left: 30px;
  margin-right: 30px;
  margin-bottom: 3px;
  margin-top: 3px;
  height: 100%;
}

a {
  text-decoration: none;
  color: black;
}

a:hover {
  text-decoration: underline;
  color: black;
}

</style>
