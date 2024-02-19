<template>
  <div class="container">
    <H1 v-if="!registered" class="center-text">
      {{ $t('user.add_member') }}
    </H1>
    <form v-if="!registered" className="flexbox-container2" @submit.prevent="" @submit="submit">
      <div className="container-item">
        <h2>{{ $t('user.general_information') }}</h2>
        <label for="logo">{{ $t('profile_picture') }}:</label><br/>
        <div class="image">
          <img v-if="localURL === '' " src="../../assets/list_images/user.png" alt="$t('user.alt_profile_logo')" style="object-fit:contain;max-width:120px;max-height:120px;">
          <img v-if="localURL !== ''" :src="this.localURL" alt="$t('user.alt_profile_logo')" style="object-fit:contain;max-width:120px;max-height:120px;">
        </div>
        <input type="file" name="logo" @change="addLogo"><br/>
        <label for="checkbox" >Admin:&nbsp;&nbsp;&nbsp;&nbsp;</label>
        <input type="checkbox" id="checkbox" v-model="profileData.isadmin" /><br/>
        <label for="name">{{ $t('user.business') }}:</label><br/>
        <input type="text" id="name" v-model="profileData.name" required><br/>
        <label for="description">{{ $t('user.description') }}:</label><br/>
        <textarea name="description" rows="5" cols="30" v-model="profileData.description" required></textarea><br/>
        <label for="address">{{ $t('user.street_address') }}:</label><br/>
        <input type="text" id="address" v-model="profileData.address" required><br/>
        <label for="location">{{ $t('user.town') }}:</label><br/>
        <input type="text" id="location" v-model="profileData.city" required><br/>
      </div>
      <div className="container-item">
        <h2>{{ $t('user.billing') }}</h2>
        <label for="billingName">{{ $t('user.billingnamelabel') }}:</label><br/>
        <input name="billingName" v-model="profileData.billingName" required><br/>
        <label for="billingBox">{{ $t('user.box') }}:</label><br/>
        <input name="billingBox" v-model="profileData.billingBox" required><br/>
        <label for="billingAddress">{{ $t('user.street_address') }}:</label><br/>
        <input name="billingAddress" v-model="profileData.billingAddress" required><br/>
        <label for="orgNumber">{{ $t('user.orgnumberlabel') }}:</label><br/>
        <input name="orgNumber" v-model="profileData.orgNumber" required><br/><br/>
        <h2>{{ $t('user.contact') }}</h2>
        <label for="email">{{ $t('user.email') }}:</label><br/>
        <input type="email" id="email" v-model="profileData.email" required><br/>
        <label for="phone">{{ $t('user.telephonecontactlabel') }}:</label><br/>
        <input type="tel" id="phone" v-model="profileData.phone" required><br/><br/>
        <div v-if="!registered && registered_fail">
          <p style="color: red">{{this.registeredText}}</p>
        </div>
        <button type="submit" value="Submit" class="buttonflex"> 
          <p style="padding-right:7px" > {{ $t('user.register') }} </p>
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="30" height="30" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
            <circle cx="12" cy="14" r="2" />
            <polyline points="14 4 14 8 8 8 8 4" />
          </svg>
        </button>
      </div>
    </form>
  <!--<div v-if="registered">
    //  <h1>Den nya medlemmen är nu registrerad. Ett mail med inloggningsuppgifter har skickats till angivna mailaddressen.</h1>
-->
    <div v-if="registered && !registered_fail">
      <h1>{{this.registeredText}}</h1>
      <button @click="reset" class="buttonflex">OK</button>
    </div>
  </div>
  
</template>

<script>
import { register } from '../../serverFetch'

export default {
  data () {
    return {
      profileData: [],
      register,
      localURL: '',
      registered: false,
      registered_fail: false,
      registeredText: ''
    }
  },
  mounted () {
    this.profileData.isadmin = false
  },
  methods: {
    addLogo (e) {
      this.profileData.logo = e.target.files[0]
      this.localURL = URL.createObjectURL(this.profileData.logo)
    },
    
    async submit () {
      var randomstring = Math.random().toString(36).slice(-8) //for password
      //calls a register function in serverfetch
      const res = await register(
        this.profileData.isadmin,
        this.profileData.name, 
        randomstring,
        this.profileData.description, 
        this.profileData.address,
        this.profileData.city, 
        this.profileData.billingName, 
        this.profileData.billingBox, 
        this.profileData.billingAddress,
        this.profileData.orgNumber, 
        this.profileData.email, 
        this.profileData.phone,
        this.profileData.logo
      )
      if (res.ok) {
        this.registered = true //could register
      } else {
        this.registered_fail = true //couldnet register
      }
      res.text()
        .then((text) => {
          this.registeredText = text //message from backend
        })
    },
    reset () {
      this.profileData.isadmin = false
      this.profileData.name = ''
      this.profileData.description = '' 
      this.profileData.address = ''
      this.profileData.city = '' 
      this.profileData.billingName = ''  
      this.profileData.billingBox = '' 
      this.profileData.billingAddress = ''
      this.profileData.orgNumber = '' 
      this.profileData.email = '' 
      this.profileData.phone = '' 
      this.profileData.logo = ''
      this.localURL = ''
      this.registered = false
      this.registered_fail = false
      this.registeredText = ''
    }
  }
}

</script>

<style scoped>


@media screen and (min-width: 860px) {
  .container{
    width:100%;
    height: fit-content;
    margin-top: 2%;
    margin-left: 15rem;
    display: flex;
    flex-direction: column;
    justify-content:center;
    align-items: center;
  }
  .flexbox-container2 {
    display: flex;
    margin-top: 2%;
  }
  
  .edit {
    align-self: flex-end;
  }
}
@media screen and (max-width: 859px) {
  .edit {
    display: flex;
    justify-content: center;
  }
}

.container-item {
  padding-left: 30px;
  padding-right: 30px;
}

.image {
  align-self: left;
  margin: 20px;
  display: inline-block; 
  position: relative; 
  width: 100px; 
  height: 100px; 
  overflow: hidden; 
  border-radius: 50%;
  display: flex;
  justify-content: space-around;
}

.right {
  display: flex;
  justify-content: space-around;
  flex-direction: column;
  justify-content: space-between;
  
}

.center-text {
  text-align: center;
  margin: 2rem 0rem;
  margin-top: 0rem;
  margin-left: 0rem;
  font-size: 2.2rem;
  letter-spacing: 0.3em;  
  text-align: center;
}

h1 {
  font-size: 2rem;
}

h2 {
  font-size: 1.34rem;
  font-weight: bold;
}

button {
  padding: 0;
  border: none;
  background: none;
}

.buttonflex {
  cursor: pointer;
  width: fit-content;
  height: fit-content;
  padding: 2% 2%;
  border-radius: 10px;
  display: flex;
  justify-content: space-around;
  align-items: center;
  background-color: #4690CD;
  color: white;
}
.buttonflex>svg {
  fill:#4690CD;
  stroke: white;
}

.buttonflex:hover svg, .buttonflex:hover {
  background-color: #0a60a6;
  fill: #0a60a6;
}

</style>
