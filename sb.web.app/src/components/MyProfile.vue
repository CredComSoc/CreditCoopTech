<template>
    <div className="flexbox-container2">
      <div className="image container-item">
      <img src="../assets/list_images/Ellipse_3.png" alt="Städservice AB">
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
        <p> Florist AB<br/>Box: 031 456<br/>123 123 Söderköping<br/>Org. nummer: 012345 - 0123 </p>
      </div>
      <div className="right container-item">
        <div>
          <h1> Kontaktuppgifter </h1>
          <p> Email: florist@florist.com<br/><br/>Tel: +46 123 456 789 </p>
        </div>
        <div style="align-self: flex-end;">
          <a href=""> Redigera <img style="width: 25px;" src="../assets/edit.png" alt="Redigera"/></a>
        </div>
      </div>
    </div>
</template>

<script>

export default {}

</script>

<style scoped>

.flexbox-container2 {
  display: flex;
  margin-right: 100px;
  margin-top: 50px;
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
