<template>
  <div className="flexbox-container2 flexbox-item">
    
    <div className="image container-item">
      <img src="../../assets/list_images/Ellipse_3.png" alt="Städservice AB">
    </div>
    <div className="right container-item">
      <h1> Företagsnamn </h1>
      <p> {{profileData.name}} </p>

      <h1> Beskrivning </h1>
      <p> {{profileData.description}} </p>

      <h1> Adress </h1>
      <p> {{profileData.adress}} </p>

      <h1> Stad/ort </h1>
      <p> {{profileData.city}} </p>

      <h1> Faktureringsuppgifter </h1>
      <p> {{profileData.billingName}}<br/>{{profileData.billingBox}}<br/>{{profileData.billingAdress}}<br/> {{profileData.orgNumber}} </p>
    </div>
    <div className="right container-item">
      <div>
        <h1> Kontaktuppgifter </h1>
        <p :key="profileData"> {{"Email: " + profileData.email}}<br/><br/> {{"Tel: " + profileData.phone}} </p>
      </div>
    </div>
  </div>
</template>

<script>
import { profile } from './../../serverFetch'

export default {
  data () {
    return {
      profileData: [],
      profile
    }
  },
  methods: {
    getProfile () {
      this.profile().then(res => {
        console.log(res)
        this.profileData = res
      })
    }
  },
  created: function () {
    this.getProfile()
  }
}

</script>

<style scoped>

.container-item {
  flex: 2;
  padding-left: 30px;
  padding-right: 30px;
}

img {
  display: block;
  margin-left: auto;
  margin-right: auto;
  width: 100%;
}

.right {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.flexbox-item {
  margin: 10px;
  max-width: 1100px;
  width: 100%;
}

h1 {
  font-size: 2rem;
}

.image {
  /**margin-left: auto;
  margin-right: auto;*/
  width: 50%;
}

@media screen and (min-width: 860px) {
  .flexbox-container2 {
    padding-top: 50px;
    display: flex;
    align-content: center;
  }
  .image {
    width: 50%;
  }
}

</style>
