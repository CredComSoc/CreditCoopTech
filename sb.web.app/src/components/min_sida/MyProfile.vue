<template>
  <div className="flexbox-container2">
    <div>
      <div className="flexbox-container2" v-if="!edit">
        <div className="image container-item">
          <img src="../../assets/list_images/Ellipse_3.png" alt="Städservice AB">
        </div>
        <div className="container-item">
          <h1> Företagsnamn </h1>
          <p> Florist AB </p>

          <h1> Beskrivning </h1>
          <p> Hej! Vi är en blockbutik i Söderköping med fem glada florister. Se våra artiklar för aktuella blockbuketter just nu. Men vi erbjuder också specialbeställda buketter. Se även vår senaste artikel "Hudvårdspaket". OBS! Finns endast i butik.<br/><br/>Hos oss får du hemleverans eller hämtning i butik.<br/>Ansvarig konstaktperson: Eva Sundin </p>
  
          <h1> Adress </h1>
          <p> Hantverksgatan 14 </p>

          <h1> Stad/ort </h1>
          <p> Söderköping </p>

          <h1> Faktureringsuppgifter </h1>
          <p> Florist AB<br/>Box: 031 456<br/>123 123 Söderköping<br/>Org. nummer: 012345 - 0123 </p>
        </div>
        <div className="right container-item">
          <div>
            <h1> Kontaktuppgifter </h1>
            <p> Email: florist@florist.com<br/><br/>Tel: +46 123 456 789 </p>
          </div>
        </div>
        <div style="align-self: flex-end;">
          <button @click="edit = !edit"> Redigera <img style="width: 25px;" src="../../assets/edit.png" alt="Redigera"/></button>
        </div>
      </div>
    </div>

    <div v-if="edit">
      <form className="flexbox-container2">
        <div className="container-item">
          <label for="logo">Logotyp:</label><br/>
          <input type="file" name="logo"><br/>
          <label for="name">Företagsnamn:</label><br/>
          <input type="text" id="name" required><br/>
          <label for="description">Beskrivning:</label><br/>
          <textarea name="description" rows="10" cols="30" required></textarea><br/>
        </div>
        <div className="container-item">
          <label for="adress">Adress:</label><br/>
          <input type="text" id="adress" required><br/>
          <label for="location">Stad/ort:</label><br/>
          <input type="text" id="location" required><br/>
          <label for="billing">Faktureringsuppgifter:</label><br/>
          <textarea name="billing" rows="4" cols="30" required></textarea><br/>
          <label for="contact">Kontaktuppgifter:</label><br/>
          <input type="text" id="contact" required><br/><br/>
          <input type="submit">
          <button @click="edit = !edit"> Avbryt <img style="width: 25px;" src="../../assets/edit.png" alt="Redigera"></button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { profile } from '../../serverFetch'

export default {
  data () {
    return {
      edit: false,
      profileData: []
    }
  }/*,
  mounted () {
    profile('TestAdmin', '123')
      .then(res => {
        this.profileData = res
        console.log(this.profileData)
      })
  }*/
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
