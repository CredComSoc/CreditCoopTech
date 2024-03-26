<template>
<div>
  <div id="header-box" class="header-container">
    <header>
      <nav>
        <div class="middle-logo">
          <div class="navlogo">
            <figure>
              <img :src=mainLogo />
            </figure>
          </div>
        </div>
        <div v-if="enableLanguageChange" class="navlogo language">
          <select class="language-select" @change="changeLanguage" v-model="language">
            <option value="en">en</option>
            <option value="se">se</option>
          </select>
        </div>
      </nav>
    </header>
  </div>
  <div class="reset-box">
    <form @submit.prevent="handleSubmit" >
      <div class="box-text">{{ $t('reset_password') }}</div>
      <div>
        <label class="box-label">{{ $t('new_password') }}</label>
        <input v-if="!showPassword1" class="box-input" type="password" v-model="password" name="" :placeholder="$t('new_password')" id="password" required>
        <input v-else class="box-input" type="text" v-model="password" name="" :placeholder="$t('new_password')" id="password" required>
        <button type="button" @click="showPasswordToggle('password')" class="password-toggle" >
          <!-- <span class="password-toggle-icon"><i class="fas fa-eye"></i></span> -->
          <svg v-if="!showPassword1" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
            <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/>
            <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/>
            <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>
          </svg>
        </button>
        <input v-if="!showPassword2" class="box-input" type="password" v-model="password2" name="" :placeholder="$t('re_password')" id="re_password" required>
        <input v-else class="box-input" type="text" v-model="password2" name="" :placeholder="$t('re_password')" id="re_password" required>
        <button type="button" @click="showPasswordToggle('re_password')" class="password-toggle" >
          <!-- <span class="password-toggle-icon"><i class="fas fa-eye"></i></span> -->
          <svg v-if="!showPassword2" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
            <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/>
            <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/>
            <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>
          </svg>
        </button>
      </div>
      <button id="reset-button" @click="handleSubmit">{{ $t('reset') }}</button>
    </form>
    <div class="box-error" v-if="error">
      {{this.errorText}}
    </div>
    <router-link :to="{name:'Login'}">
      <button id="login-button" ><span>&larr; {{ $t('login.login_button') }}</span></button>
    </router-link>
  </div>
  <LoadingComponent ref="loadingComponent" />
</div>
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
      showPassword1: false,
      showPassword2: false,
      password: '',
      password2: '',
      // eslint-disable-next-line
      mainLogo: process.env.VUE_APP_NAME == 'SB' ? '/sb.png' : '/logo.png',
      language: window.localStorage.getItem('VUE_APP_I18N_LOCALE'),
      // eslint-disable-next-line
      enableLanguageChange: process.env.VUE_APP_ENABLE_LANGUAGE_CHANGE == 'enable' ? true : false // Set enableLanguageChange from env file
    }
  },
  methods: {
    showPasswordToggle (inputField) {
      if (inputField === 'password') {
        this.showPassword1 = !this.showPassword1
      } else {
        this.showPassword2 = !this.showPassword2
      }
    },
    async handleSubmit () {
      const uri = window.location.href.split('/')
      try {
        var token = uri[4]
        console.log('Token is: ', token)
      
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
          }).catch((err) => {
            console.error('Error occured when resetting password: ', err)
          })
        } else {
          this.errorText = this.$i18n.t('passwords_not_matched')
          this.error = true
        }
      } catch (e) {
        console.error('Error! \n', e)
      }  
    },
    changeLanguage (event) {
      const selectedLanguage = event.target.value
      this.language = selectedLanguage
      if (selectedLanguage === 'en') {
        window.localStorage.setItem('VUE_APP_I18N_LOCALE', 'en')
        window.localStorage.setItem('VUE_APP_I18N_FALLBACK_LOCALE', 'en-LCC')
      } else {
        window.localStorage.setItem('VUE_APP_I18N_LOCALE', 'se')
        window.localStorage.setItem('VUE_APP_I18N_FALLBACK_LOCALE', 'se-SB')
      }
      this.$i18n.locale = selectedLanguage
      this.$i18n.fallbackLocale = selectedLanguage === 'en' ? 'en-LCC' : 'se-SB' // Set fallback locale
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
    width: 350px;
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
  font-size: 16px;
  background: transparent;
  width: 100%;
}

input::placeholder {
  font-size:  13px;
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

.language {
  position: absolute;
  top: 0;
  right: 0;
  margin-top: 10px;
  margin-right: 10px;
}

.password-toggle {
  position: relative;
  border-style: hidden;
}
.password-toggle-icon {
  /* position: absolute; */
  top: 50%;
  right: 10px;
  transform: translateY(-50%);
  cursor: pointer;
}

.password-toggle-icon i {
  font-size: 18px;
  line-height: 1;
  color: #333;
  transition: color 0.3s ease-in-out;
  margin-bottom: 20px;
}

.password-toggle-icon i:hover {
  color: #000;
}

</style>
