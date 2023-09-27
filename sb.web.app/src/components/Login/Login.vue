<template>

<div>
  <div id="header-box" class="header-container">
    <header class="login_page">
      <nav>
        <div class="middle-logo">
          <div class="navlogo">
            <figure>
              <img src="/logo.png" alt=""/>
            </figure>
          </div>
        </div>
      </nav>
    </header>
  </div>
  <div class="login-box">
    <form @submit.prevent="handleSubmit">
      <div class="box-text">{{ $t('login.login_to_label') }} {{ $t('org.name') }}</div>
      <div>
        <label class="box-label">{{ $t('login.email_label') }}</label>
        <input class="box-input" type="text" v-model="username" :placeholder="$t('login.email_placeholder')" name="" id="email-input" required>
        
      </div>
      <div>
        <label class="box-label">{{ $t('login.password_label') }}</label>
        <input class="box-input" type="password" v-model="password" :placeholder="$t('login.password_placeholder')" name="" id="password-input" required>
        
      </div>
      <button id="login-button" >{{ $t('login.login_button')}}</button>
    </form>
    <div class="box-link">
      <a href="#" @click="handleMailToClick($t('org.contact_email'))">{{ $t('login.need_help') }}<br/>{{ $t('login.contact_us') }}</a>
      <div class="box-msg" v-if="this.mailtoClicked">
          <a :href="$t('org.contact_link')">{{ $t('org.contact_email') }}</a>
      </div>
      <!-- bring this back later -->
      <router-link :to="{name:'Forgot'}"> {{ $t('reset_password') }} </router-link>
    </div> 
    <div class="box-error" v-if="this.error">
      {{ $t('wrong_email_password') }} ({{ loginCount }})
    </div>
    <LoadingComponent ref="loadingComponent" />
  </div>
</div>

</template>

<script>
import { login, setStoreData } from '../../serverFetch'
import LoadingComponent from '../SharedComponents/LoadingComponent.vue'

export default {
  name: 'Login',
  components: {
    LoadingComponent
  },
  data () {
    return {
      username: '',
      password: '',
      error: false,
      mailtoClicked: false,
      loginCount: 0
    }
  },
  methods: {
    async handleMailToClick (emailAddress) {
      this.mailtoClicked = true
      window.location.href = 'mailto:' + emailAddress
    },
    async handleSubmit () {
      this.$refs.loadingComponent.showLoading()
      login(this.username.toLowerCase(), this.password).then(async (response) => {
        if (response) {
          this.error = false  
          this.loginCount = 0   
          this.error = false 
          await setStoreData()
          this.$refs.loadingComponent.hideLoading()

          window.location.href = '/'
        } else {
          this.error = true
          this.loginCount += 1
          this.$refs.loadingComponent.hideLoading()
        } 
      })
    }
  }

}

</script>

<!-- Add 'scoped' attribute to limit CSS to this component only -->
<style scoped>

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




.box-msg a {
  font-size: 14px;
  text-align: center;
  color: rgb(27, 77, 2);
  margin-top: 15px;
  margin-bottom: -58px
}

</style>
