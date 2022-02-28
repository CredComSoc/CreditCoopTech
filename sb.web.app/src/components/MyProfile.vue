<template>
    <div>
      <img src="städning.png" alt="Städservice AB">
      <div className="left">
        <h1> Företagsnamn </h1>
        <p> Florist AB </p>
        <h1> Beskrivning </h1>
        <p> Hej! Vi är en blockButik i Söderköping med fem glada florister. Se våra artiklar för aktuella blockbuketter just nu. Men vi erbjuder också specialbeställda buketter. Se ''även vår senaste artikel "Hudvårdspaket". OBS! Finns endast i butik.<br/><br/>Hos oss får du hemleverans eller hämtning i butik.<br/>nAnsvarig konstaktperson: Eva Sundin </p>
        <h1> Adress </h1>
        <p> Hantverksgatan 14 </p>
        <h1> Stad/ort </h1>
        <p> Söderköping </p>
        <h1> Faktureringsuppgifter </h1>
        <p> Florist AB<br/>Box: 031 456<br/>123 123 Söderköping<br/>Org. nummer: 012345 - 0123 </p>
      </div>
      <div className="right">
        <h1> Kontaktuppgifter </h1>
        <p> Email: florist@florist.com<br/><br/>Tel: +46 123 456 789 </p>
      </div>
    </div>
</template>

<script>

export default {}

</script>

<style scoped>
img {
  float: left;
   border-radius: 50px;
   height:100px;
   width:100px;
}

.right {
  float: right;
  width: 25%;
}

.left {
  float: left;
  width: 25%;
}
</style>
