<template>
  <div className="flexbox-container2">
    <div>
      <div className="flexbox-container2" v-if="!edit">
        <div className="image container-item">
          <img v-if="localURL === ''" :src="this.logoURL" alt="Profile Logo" style="object-fit:contain;max-width:120px;max-height:120px;">
          <img v-if="localURL !== ''" :src="this.localURL" alt="Profile Logo" style="object-fit:contain;max-width:120px;max-height:120px;">
        </div>
        <div className="container-item">
          <h1> Företagsnamn </h1>
          <p> {{profileData.name}} </p>

          <h1> Beskrivning </h1>
          <p> {{profileData.description}} </p>
  
          <h1> Adress </h1>
          <p> {{profileData.adress}} </p>

          <h1> Stad/ort </h1>
          <p> {{profileData.city}} </p>

          <h1> Faktureringsuppgifter </h1>
          <p> {{profileData.billingName}}<br/>Box: {{profileData.billingBox}}<br/>{{profileData.billingAdress}}<br/> Org. nummer: {{profileData.orgNumber}} </p>
        </div>
        <div className="right container-item">
          <div>
            <h1> Kontaktuppgifter </h1>
            <p> {{"Email: " + profileData.email}}<br/><br/> {{"Tel: " + profileData.phone}} </p>
          </div>
          <div>
            <button @click="edit = !edit" class="buttonflex"> 
              <p style="padding-right:7px" > Redigera </p>
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" />
                <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" />
                <line x1="16" y1="5" x2="19" y2="8" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="edit">
      <form className="flexbox-container2" @submit.prevent="">
        <div className="container-item">
          <h1>Allmänt</h1>
          <label for="logo">Logotyp:</label><br/>
          <div class="image">
            <img :src="this.localURL" style="object-fit:contain;max-width:120px;max-height:120px;">
            <img v-if="localURL === ''" :src="this.logoURL" alt="Profile Logo" style="object-fit:contain;max-width:120px;max-height:120px;">
          </div>
          <input type="file" name="logo" @change="addLogo"><br/>
          <label for="name">Företagsnamn:</label><br/>
          <input type="text" id="name" v-model="profileData.name" required><br/>
          <label for="description">Beskrivning:</label><br/>
          <textarea name="description" rows="10" cols="30" v-model="profileData.description" required></textarea><br/>
          <label for="adress">Adress:</label><br/>
          <input type="text" id="adress" v-model="profileData.adress" required><br/>
          <label for="location">Stad/ort:</label><br/>
          <input type="text" id="location" v-model="profileData.city" required><br/>
        </div>
        <div className="container-item">
          <h1>Faktureringsuppgifter</h1>
          <label for="billingName">Namn:</label><br/>
          <input name="billingName" v-model="profileData.billingName" required><br/>
          <label for="billingBox">Box:</label><br/>
          <input name="billingBox" v-model="profileData.billingBox" required><br/>
          <label for="billingAdress">Adress:</label><br/>
          <input name="billingAdress" v-model="profileData.billingAdress" required><br/>
          <label for="orgNumber">Organisationsnummer:</label><br/>
          <input name="orgNumber" v-model="profileData.orgNumber" required><br/><br/>
          <h1>Kontaktuppgifter</h1>
          <label for="email">Epost:</label><br/>
          <input type="email" id="email" v-model="profileData.email" required><br/>
          <label for="phone">Telefon:</label><br/>
          <input type="tel" id="phone" v-model="profileData.phone" required><br/><br/>

          <button @click="submit" class="buttonflex">
            <p style="padding-right:7px" > Spara </p>
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="30" height="30" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
              <circle cx="12" cy="14" r="2" />
              <polyline points="14 4 14 8 8 8 8 4" />
          </svg>
          </button>
          <button @click="edit = !edit" class="buttonflex"> 
            <p style="padding-right:0px" > Avbryt </p>
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="30" height="30" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
          </svg>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { EXPRESS_URL, profile, updateProfile, mail } from '../../serverFetch'

export default {
  data () {
    return {
      edit: false,
      profileData: [],
      updateProfile,
      logoURL: '',
      localURL: ''
    }
  },
  mounted () {
    profile()
      .then(res => {
        this.profileData = res
        //console.log(this.profileData)
        this.getImgURL()
      })
  },
  methods: {
    addLogo (e) {
      this.profileData.logo = e.target.files[0]
      //console.log(this.profileData.logo)
      this.localURL = URL.createObjectURL(this.profileData.logo)
      console.log(this.localURL)
      console.log(this.logoURL)
    },
    submit () {
      this.updateProfile(
        this.profileData.name, 
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
      console.log(this.localURL)
      console.log(this.logoURL)
      if (this.localUrl) {
        this.logoURL = this.localURL
      }
      console.log(this.localURL)
      console.log(this.logoURL)
      this.edit = !this.edit
    },
    getImgURL () {
      this.logoURL = EXPRESS_URL + '/image/' + this.profileData.logo
    }
  }
}

</script>

<style scoped>

@media screen and (min-width: 860px) {
  .flexbox-container2 {
    display: flex;
    margin-right: 100px;
    margin-top: 50px;
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
  display: flex;
  justify-content: space-around;
}

</style>
