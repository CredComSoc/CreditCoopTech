<template>
<div>
  <div id="header-box" class="header-container">
    <header>
      <nav>
        <div class="middle-logo">
          <div class="navlogo">
            <figure>
              <img src="/logo.png"/>
            </figure>
          </div>
        </div>
      </nav>
    </header>
  </div>
  <div class="reset-box">
    <form @submit.prevent="handleSubmit" v-on:keyup.enter="handleSubmit">
      <div class="box-text">{{ $t('reset_password') }}</div>
      <div>
        <label class="box-label">{{ $t('new_password') }}</label>
        <input class="box-input" type="password" v-model="password" name="" :placeholder="$t('new_password')" id="password" required>
        <input class="box-input" type="password" v-model="password2" name="" :placeholder="$t('re_password')" id="re_password" required>
      </div>
      <button id="reset-button" >{{ $t('reset') }}</button>
    </form>
    <div class="box-error" v-if="error">
      {{this.errorText}}
    </div>
    <router-link :to="{name:'Login'}">
      <button id="login-button" ><span>&larr; {{ $t('login.login_button') }}</span></button>
    </router-link>
  </div>
</div>
<LoadingComponent ref="loadingComponent" />
</template>

<script>
import { resetToken } from '../../serverFetch'
import { useRouter } from 'vue-router'
import LoadingComponent from '../SharedComponents/LoadingComponent.vue'

const router = useRouter()

export default {
  name: 'Reset',
  components: {
    LoadingComponent
  },
  data () {
    return {
      error: false,
      errorText: '',
      password: '',
      password2: ''
    }
  },
  methods: {
    async handleSubmit () {
      const uri = window.location.href.split('/')
      const token = uri[4]
      if (this.password === this.password2) {
        this.$refs.loadingComponent.showLoading()
        resetToken(token, this.password).then((response) => {
          if (response) {
            this.error = false
            this.$refs.loadingComponent.showLoading()
            setTimeout(() => {
              this.$router.push({ name: 'Home' }) 
            }, 1000)
          } else {
            this.errorText = this.$i18n.t('password_reset_failed')
            this.$refs.loadingComponent.showLoading()
            this.error = true
          }
        })
      } else {
        this.errorText = this.$i18n.t('passwords_not_matched')
        this.error = true
      }  
    }
  }

}

</script>

<!-- Add 'scoped' attribute to limit CSS to this component only -->
<style scoped>

.reset-box {
    font-family: Ubuntu;
    font-style: Regular;
    font-size:  20px;
    text-align: left;
    letter-spacing: 0.05em;
    padding-left: 19px;
    padding-right: 19px;
    width: 300px;
    height: fit-content;
    border-radius: 20px;
    margin: auto;
    margin-top: 5%;
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
  margin-bottom: 12px;
  padding-left: 8px;
}

.box-error{
  font-size: 14px;
  text-align: center;
  color: red;
  margin-top: 15px;
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

#login-button {
  margin-top: 260px;
}

input:focus,
select:focus,
textarea:focus,
button:focus {
    outline: none;
}






</style>
