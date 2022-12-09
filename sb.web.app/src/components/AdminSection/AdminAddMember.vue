<template>
  <div class="container">
    <H1 v-if="!registered">
      LÄGG TILL MEDLEM
    </H1>
    <form v-if="!registered" className="flexbox-container2" @submit.prevent="">
      <div className="container-item">
        <h3>Allmänt</h3>
        <label for="logo">Logo Bild:</label><br/>
        <div class="image">
          <img v-if="localURL === '' " src="../../assets/list_images/user.png" alt="Profile Logo" style="object-fit:contain;max-width:120px;max-height:120px;">
          <img v-if="localURL !== ''" :src="this.localURL" alt="Profile Logo" style="object-fit:contain;max-width:120px;max-height:120px;">
        </div>
        <input type="file" name="logo" @change="addLogo"><br/>
        <label for="checkbox" >Admin:&nbsp;&nbsp;&nbsp;&nbsp;</label>
        <input type="checkbox" id="checkbox" v-model="profileData.isadmin" /><br/>
        <label for="name">Företagsnamn:</label><br/>
        <input type="text" id="name" v-model="profileData.name" required><br/>
        <label for="description">Beskrivning:</label><br/>
        <textarea name="description" rows="5" cols="30" v-model="profileData.description" required></textarea><br/>
        <label for="adress">Adress:</label><br/>
        <input type="text" id="adress" v-model="profileData.adress" required><br/>
        <label for="location">Stad/ort:</label><br/>
        <input type="text" id="location" v-model="profileData.city" required><br/>
      </div>
      <div className="container-item">
        <h3>Faktureringsuppgifter</h3>
        <label for="billingName">Namn:</label><br/>
        <input name="billingName" v-model="profileData.billingName" required><br/>
        <label for="billingBox">Box:</label><br/>
        <input name="billingBox" v-model="profileData.billingBox" required><br/>
        <label for="billingAdress">Adress:</label><br/>
        <input name="billingAdress" v-model="profileData.billingAdress" required><br/>
        <label for="orgNumber">Organisationsnummer:</label><br/>
        <input name="orgNumber" v-model="profileData.orgNumber" required><br/><br/>
        <h3>Kontaktuppgifter</h3>
        <label for="email">Epost:</label><br/>
        <input type="email" id="email" v-model="profileData.email" required><br/>
        <label for="phone">Telefon:</label><br/>
        <input type="tel" id="phone" v-model="profileData.phone" required><br/><br/>
        
        <button @click="submit" class="buttonflex">
          <p style="padding-right:7px" > Registrera </p>
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="30" height="30" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
            <circle cx="12" cy="14" r="2" />
            <polyline points="14 4 14 8 8 8 8 4" />
          </svg>
        </button>
      </div>
    </form>
    <div v-if="registered">
      <h1>Personen är registrerat!</h1>
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
      registered: false
    }
  },
  mounted () {
    /*profile()
    .then(res => {
      this.profileData = res
      //console.log(this.profileData)
      this.getImgURL()
    })*/
    this.profileData.isadmin = false
  },
  methods: {
    addLogo (e) {
      this.profileData.logo = e.target.files[0]
      //console.log(this.profileData.logo)
      this.localURL = URL.createObjectURL(this.profileData.logo)
    },
    async submit () {
      var randomstring = Math.random().toString(36).slice(-8)
      const result = await this.register(
        this.profileData.isadmin,
        this.profileData.name, 
        randomstring,
        this.profileData.description, 
        this.profileData.adress, 
        this.profileData.city, 
        this.profileData.billingName, 
        this.profileData.billingBox, 
        this.profileData.billingAdress, 
        this.profileData.orgNumber, 
        this.profileData.email, 
        this.profileData.phone,
        this.profileData.logo
      )
      if (result) {
        this.registered = true
      }
    },
    reset () {
      this.profileData.isadmin = false
      this.profileData.name = ''
      this.profileData.description = '' 
      this.profileData.adress = '' 
      this.profileData.city = '' 
      this.profileData.billingName = ''  
      this.profileData.billingBox = '' 
      this.profileData.billingAdress = '' 
      this.profileData.orgNumber = '' 
      this.profileData.email = '' 
      this.profileData.phone = '' 
      this.profileData.logo = ''
      this.localURL = ''
      this.registered = false
    }
  }
}

</script>

<style scoped>
* {
  font-family: 'Ubuntu', sans-serif;
  padding: 0;
  margin: 0;
}

@media screen and (min-width: 860px) {
  .container{
    width:100%;
    height: fit-content;
    margin-top: 2%;
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

h1 {
  font-size: 2rem;
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
