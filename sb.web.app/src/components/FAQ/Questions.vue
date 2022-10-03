<template>  
  <div> 
    <h1><b> Vanliga frågor </b></h1>
  </div>
  <button class="accordion" @click="expand">Hur tjänar jag barterkrediter?
</button>
  <div class="panel">
    <p>Precis som ett bankkonto, när du först öppnar ditt barterkonto är din balans noll. Du tjänar barterkrediter genom att sälja dina varor eller tjänster till andra medlemmar i ringen. Du kan annonsera i våra e-posterbjudanden och webbshop, eller så kan du rådgöra med din bartermäklare om andra sätt att marknadsföra ditt företag inom ringen.  Ett annat sätt att är att skapa barterkrediter genom att använda din kreditlina. Det är så barterkrediterna inom nätverket skapas. Ju mer du köper och säljer i nätverket på regelbunden basis, ju större kreditlina kommer du att få av din bartermäklare.
</p>
  </div>
  <button class="accordion" @click="expand">Hur spenderar jag mina barterkrediter?
</button>
  <div class="panel">
    <p>När du vill göra ett köp för din organisation eller dig personligen kan du göra det direkt på webbshopen. Är det något du behöver som inte finns i shopen så kontaktar du barterringen som letar reda på det, antingen hos befintliga medlemmar eller genom att rekrytera nya medlemmar.
</p>
  </div>
  
  <button class="accordion" @click="expand">Kan jag få lojalitetspoäng?
</button>
  <div class="panel">
    <p>Nej, barterringen är inget lojalitetsprogram där poäng löses för varor utan en komplementär marknadsplats - en "sluten ekonomi". Dina barterkrediter är en komplementär valuta med exakt samma värde som en svensk krona. Ditt handelskonto är som vilket bankkonto som helst, förutom att du måste spendera eller tjäna dina barterkronor (interna handelskrediter nominerade i svenska kronor)  inom vår begränsade handelskrets av företag och organisationer. 
</p>
  </div>
  
  <button class="accordion"  @click="expand">Har mina barterkrediter en skattepåverkan?

</button>
  <div class="panel">
    <p>Absolut, en barterkrona redovisas på samma sätt som en svensk krona i din bokföring. Skattekraven är desamma. Enda skillnaden är att du inte kan betala skatt med barterkronorna. Istället använder du t ex de kontanta besparingar du gör genom barterhandeln till detta. Om du går plus minus noll i handeln utgår ingen vinstbeskattning. Moms betalas alltid på sedvanligt vis i svenska kronor. Förslagsvis skapar du ett eget barterkonto i bokföringen för att hålla ordning på din merförsäljning. På dina sidor på handelsplattformen hittar du din handelshistorik.

Önskas ytterligare information rekommenderar vi <a href="https://www4.skatteverket.se/rattsligvagledning/347753.html">skatteverkets officiella ställningstagande för beskattning av medlem i bytesring.</a>
</p>
  </div>
</template>

<script>
export default {
  methods: {
    expand () {
      var acc = document.getElementsByClassName('accordion')
      var i
      
      for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener('click', function () {
          /* Toggle between adding and removing the "active" class,
          to highlight the button that controls the panel */
          this.classList.toggle('active')
          
          /* Toggle between hiding and showing the active panel */
          var panel = this.nextElementSibling
          if (panel.style.display === 'block') {
            panel.style.display = 'none'
          } else {
            panel.style.display = 'block'
          }
        })
      }
    }
  }
}
</script>

<style scoped>  
  h1 {
    padding: 10px 0px 10px 0px;
    font-size: 1.6rem;
  }
  /* Style the buttons that are used to open and close the accordion panel */
  .accordion {
    background-color: #eee;
    color: #444;
    cursor: pointer;
    padding: 18px;
    width: 100%;
    text-align: left;
    border: none;
    outline: none;
    transition: 0.4s;
  }
  
  /* Add a background color to the button if it is clicked on (add the .active class with JS), and when you move the mouse over it (hover) */
  .active, .accordion:hover {
    background-color: #ccc;
  }
  
  /* Style the accordion panel. Note: hidden by default */
  .panel {
    padding: 0 18px;
    background-color: white;
    display: none;
    overflow: hidden;
  }
  .accordion:after {
    content: '\02795'; /* Unicode character for "plus" sign (+) */
    font-size: 13px;
    color: #777;
    float: right;
    margin-left: 5px;
  }
  
  .active:after {
    content: "\2796"; /* Unicode character for "minus" sign (-) */
  }
</style>
