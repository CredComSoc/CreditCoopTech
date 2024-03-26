<template>

<div>
  <div id="header-box" class="header-container">
    <header class="login_page">
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
      <span class="box-error" v-if="this.error">
      {{ $t('wrong_email_password') }} ({{ loginCount }})
      </span>
      <button id="login-button" >{{ $t('login.login_button')}}</button> 
      
    </form>
    <div class="box-link">
      <a :href="contactLink">{{ $t('login.need_help') }}<br/>{{ $t('login.contact_us') }}</a>
      <div class="box-msg" v-if="this.mailtoClicked">
          <a :href="contactLink">{{ contactEmail }}</a>
      </div>
      <div>
        <p>Please do check your spam folder for any emails from us.</p>
      </div>
      <div class="forgot_link">
      <router-link :to="{name:'Forgot'}"> {{ $t('reset_password') }} </router-link>
    </div>
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
      loginCount: 0,
      // eslint-disable-next-line
      mainLogo: process.env.VUE_APP_NAME == 'SB' ? '/sb.png' : '/logo.png',
      language: window.localStorage.getItem('VUE_APP_I18N_LOCALE'),
      // eslint-disable-next-line
      enableLanguageChange: process.env.VUE_APP_ENABLE_LANGUAGE_CHANGE == 'enable' ? true : false, // Set enableLanguageChange from env file
      contactLink: 'mailto:' + process.env.VUE_APP_SUPPORT_EMAIL,
      contactEmail: process.env.VUE_APP_SUPPORT_EMAIL
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

.login-box {
    font-family: Ubuntu;
    font-style: Regular;
    font-size:  20px;
    text-align: left;
    letter-spacing: 0.05em;
    padding-left: 19px;
    padding-right: 19px;
    width: 300px;
    border-radius: 20px;
    margin: auto;
    margin-top: 10%;
    margin-bottom: 20px;
    position: relative;
    background: rgba(246, 202, 115, 0.27);
    padding-top: 25px;
    padding-bottom: 25px;;
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
  margin-top: 30px;
  text-align: center;
  font-size: 16px;
  margin-bottom: 20px;
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
.forgot_link {
  margin-top: 20px;
  margin-bottom: 20px;
}


.box-msg a {
  font-size: 14px;
  text-align: center;
  color: rgb(27, 77, 2);
  margin-top: 15px;
}
.language {
  position: absolute;
  top: 0;
  right: 0;
  margin-top: 10px;
  margin-right: 10px;
}


</style>
