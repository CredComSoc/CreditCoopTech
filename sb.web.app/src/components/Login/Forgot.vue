<template>
<div>
  <div id="header-box" class="header-container">
    <header>
      <nav>
        <div class="middle-logo">
          <div class="navlogo">
            <figure>
              <img src="../../assets/navbar_logos/sb.png" :alt="$t('shop.go_to_shop')"/>
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
        <label class="box-label">{{ $t('login.email_label') }}</label>
        <input class="box-input" type="text" v-model="email" name="" :placeholder="$t('login.email_placeholder')" id="email-input" required>
      </div>
      <button id="reset-button" >{{ $t('reset') }}</button>
    </form>
    <div class="box-error" v-if="error">
      {{ $t('there_is_no_one') }} {{ $t('user.member_label') }} {{ $t('with_that_email_address') }}
    </div>
    <div class="box-sent" v-if="sent">
      {{ $t('reset_password_email_sent') }}
    </div>
    <router-link :to="{name:'Login'}">
      <button id="login-button" ><p>&larr; {{ $t('log_in') }}</p></button>
    </router-link>
  </div>
</div>
<LoadingComponent ref="loadingComponent" />

</template>

<script>
import { resetPassword } from '../../serverFetch'
import { useRouter } from 'vue-router'
import LoadingComponent from '../SharedComponents/LoadingComponent.vue'

const router = useRouter()

export default {
  name: 'Forgot',
  components: {
    LoadingComponent
  },
  data () {
    return {
      email: '',
      error: false,
      sent: false
    }
  },
  methods: {
    async handleSubmit () {
      this.$refs.loadingComponent.showLoading()
      resetPassword(this.email).then((response) => {
        if (response) {
          this.error = false
          this.sent = true  
          this.$refs.loadingComponent.hideLoading()   
        } else {
          this.error = true
          this.sent = false
          this.$refs.loadingComponent.hideLoading()
        } 
      })
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
  margin-top: 15px;
  margin-bottom: -58px
}

.box-sent{
  font-size: 14px;
  text-align: center;
  color: green;
  margin-top: 15px;
  margin-bottom: -58px
}

.box-link {
  margin-top: 0px;
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
  margin-top: 280px;

}

input:focus,
select:focus,
textarea:focus,
button:focus {
    outline: none;
}




</style>
