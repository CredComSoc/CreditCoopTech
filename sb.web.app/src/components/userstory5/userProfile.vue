<template>
  <div className="flexbox-container2">
    <div>
      <div className="flexbox-container2">
        <!-- måste fixa rätt bild här -->
        <div className="image container-item">
          <img src="../../assets/list_images/Ellipse_3.png" alt="Städservice AB">
        </div>
        <div className="container-item">
          <h1> Företagsnamn </h1>
          <p> {{profileData.accountname}} </p>

          <h1> Beskrivning </h1>
          <p> {{profileData.description}} </p>
  
          <h1> Adress </h1>
          <p> {{profileData.adress}} </p>

          <h1> Stad/ort </h1>
          <p> {{profileData.city}} </p>

          <h1> Faktureringsuppgifter </h1>
          <p> {{profileData.billing_name}}<br/>{{profileData.billing_box}}<br/>{{profileData.billing_adress}}<br/> {{profileData.billing_orgNumber}} </p>
        </div>
        <div className="right container-item">
          <div>
            <h1> Kontaktuppgifter </h1>
            <p :key="profileData"> {{"Email: " + profileData.contact_email}}<br/><br/> {{"Tel: " + profileData.contact_phone}} </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { getUserProfile } from './../../serverFetch'

export default {
  data () {
    return {
      profileData: [],
      getUserProfile
    }
  },
  props: {
    accountname: String
  },
  methods: {
    getProfile (accountname) {
      console.log(accountname + ' is accountname')
      this.getUserProfile(accountname).then(res => {
        this.profileData = res
        console.log(this.profileData)
      })
    }
  },
  created: function () {
    console.log('params is ' + this.accountname)
    this.getProfile(this.accountname)
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
