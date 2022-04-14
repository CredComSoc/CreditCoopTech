<template>
  <div className="flexbox-container2">
    <div>
      <div className="flexbox-container2" v-if="!edit">
        <div className="image container-item">
          <img src="../../assets/list_images/Ellipse_3.png" alt="Städservice AB">
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
        </div>
        <div style="align-self: flex-end;">
          <button @click="edit = !edit"> Redigera <img style="width: 25px;" src="../../assets/edit.png" alt="Redigera"/></button>
        </div>
      </div>
    </div>

    <div v-if="edit">
      <form className="flexbox-container2" @submit.capture="submit">
        <div className="container-item">
          <h1>Allmänt</h1>
          <label for="logo">Logotyp:</label><br/>
          <input type="file" name="logo"><br/>
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

          <input type="submit" value="Spara">
          <button @click="edit = !edit"> Avbryt <img style="width: 25px;" src="../../assets/edit.png" alt="Redigera"></button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { profile, updateProfile } from '../../serverFetch'

export default {
  data () {
    return {
      edit: false,
      profileData: [],
      updateProfile
    }
  },
  mounted () {
    profile()
      .then(res => {
        this.profileData = res
        console.log(this.profileData)
      })
  },
  methods: {
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
        this.profileData.phone
      )
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
}

.container-item {
  padding-left: 30px;
  padding-right: 30px;
}

.image {
  align-self: left;
  margin: 20px;
  }

.right {
  display: flex;
  flex-direction: column;
  justify-content: space-between;

}

h1 {
  font-size: 2rem;
}

</style>
