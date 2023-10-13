<template>  
  <div> 
    <h1><b> {{ $t('faq.common_questions') }} </b></h1>
  </div>
  <div>
    <p>
      Lorem ipsum dolor sit amet, consectetur adipiscing elit.
      Sed rutrum quam sit amet elit scelerisque ullamcorper. Interdum et malesuada fames ac ante ipsum primis in faucibus.
      Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. In euismod rutrum dui, et pulvinar libero hendrerit et. Sed suscipit quam non velit scelerisque, ut finibus arcu aliquet.
      Vivamus ullamcorper interdum scelerisque. Duis commodo diam eget nulla lobortis molestie. Suspendisse sagittis nibh est, sit amet porttitor eros dictum eu. Donec quis eros sit amet justo semper accumsan.
      Praesent venenatis mollis sem eget tempus. Duis accumsan tempor ex, non dictum leo hendrerit eu. Sed posuere egestas est vitae egestas. Ut porta eget metus non sodales. Nullam in dictum tellus, pretium placerat felis.
    </p>
  </div>
  <!--
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

  <button class="accordion"  @click="expand">Hur hittar jag andra medlemmar?

</button>
  <div class="panel">
    <p>Genom vår virtuella handelsplattform, medlemsbrev och handelsmöten får du kontakt med nya handelspartners. Våra stödansvariga i föreningen kan i viss mån hjälpa till att “bartermäkla”.</p>
  </div>

  <button class="accordion"  @click="expand">Hur beräknar jag lönsamheten för min handel i barterringen?

</button>
  <div class="panel">
    <p>För att få ut mesta möjliga av dina barterkronor måste du ta reda på hur mycket det kostar dig att tjäna in dem. Först bedöms din kostnad för att göra affärer: till exempel om du kör ett konsultföretag och din bruttomarginal är 70% då kostar det 30 SEK kontant att tjäna in hundra barterkronor. Tillverkare tenderar att ha lägre marginaler och media har ofta en mycket låg kostnad eftersom de kan sälja "oanvända" lager.</p>
    <p>Oavsett din marginal (bruttoresultat efter din kostnad för att göra affärer) är barterringens syfte att bringa dig nya affärer och hjälpa dig att spara pengar. Det handlar om att bestämma vad du ska sälja, och när det är vettigt att sälja på barteravtal. Vissa företag säljer inte på barteravtal under högsäsong eftersom den typen av merförsäljning inte behövs vid den tiden. De är upptagna nog som det är då. Men det kan vara bra att sälja på barteravtal under mindre intensiva tider på året. Därför, oavsett vilken marginal som helst, kan nästan vilket företag som helst utnyttja försäljnings- och köpkraften hos en barterring. Vid någon tid på året kommer det i princip alltid att finnas en överkapacitet att avyttra. </p>
    <p>Du kan även använda barterhandeln som MARKNADSFÖRING och då behöver du inte tänka på när du säljer utan då handlar det om att öka totalförsäljningen via nya kunder, oavsett tid på året. Detta kan också vara en klok strategi för att få fart på omsättningen på ditt handelskonto. </p>
    <p>Om du vill kan du utveckla en plan för att handla mest effektivt inom systemet. En fast strategi för att successivt täcka månatliga omkostnader med barteravtal kan vara mycket främjande för din soliditet. Ett exempel på sådan strategi kan vara att skapa fasta handelscirklar med andra medlemmar, där ni skriver avtal om att göra specifika barteraffärer med varandra varje månad över en längre tid. </p>
  </div>

  <button class="accordion"  @click="expand">Varför finns olika varor och tjänster tillgängliga vid olika tillfällen?

</button>
  <div class="panel">
    <p>Som nämndes under förra frågan så försöker medlemmar ibland att skapa mest merförsäljning under mindre aktiva tider över året. Därför kommer vissa varor och tjänster inte att vara tillgängliga vissa tider på året. </p>
  </div>

  <button class="accordion"  @click="expand">Hur fungerar prissättningen i barterringen?

</button>
  <div class="panel">
  <p>Barterpriserna är företrädesvis slutkundpriser utan rabatter eller andra former av förmånserbjudanden. Om någon medlem säljer under vad som kan sägas vara i linje med rekommenderat slutkundspris för en viss vara eller tjänst kan det leda till varning och fortsätter beteendet kan det innebära uteslutning. Att ta överpriser kan efter godkännande av admin dock vara godtagbart förutsatt att det gäller ”hårda produkter” med låg bruttomarginal och ett stort värde för våra medlemmar, t ex datorer, mobiltelefoner, båtar, verktyg, maskiner och så vidare. Sådana produkter kan annars vara mycket svåra att få in i barterringen överhuvudtaget.Barterpriserna är företrädesvis slutkundpriser utan rabatter eller andra former av förmånserbjudanden. Om någon medlem säljer under vad som kan sägas vara i linje med rekommenderat slutkundspris för en viss vara eller tjänst kan det leda till varning och fortsätter beteendet kan det innebära uteslutning. Att ta överpriser kan efter godkännande av admin dock vara godtagbart förutsatt att det gäller ”hårda produkter” med låg bruttomarginal och ett stort värde för våra medlemmar, t ex datorer, mobiltelefoner, båtar, verktyg, maskiner och så vidare. Sådana produkter kan annars vara mycket svåra att få in i barterringen överhuvudtaget.</p>
  </div>  
  -->
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
