<template>
  <div className="flexbox-container2 flexbox-item">
    
    <div className="image container-item">
      <img :src="this.logoURL" alt="Profile Logo" style="object-fit:contain;max-width:240px;max-height:240px;">
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
import { EXPRESS_URL, getMember } from './../../serverFetch'

export default {
  data () {
    return {
      logoURL: '',
      profileData: [],
      getMember
    }
  },
  methods: {
    getProfile (member) {
      console.log(member)
      this.getMember(member).then(res => {
        console.log(res)
        this.profileData = res
        this.getImgURL()
      })
    },
    getImgURL () {
      this.logoURL = EXPRESS_URL + '/image/' + this.profileData.logo
    }
  },
  created: function () {
    this.getProfile(this.$route.params.userprofile)
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
