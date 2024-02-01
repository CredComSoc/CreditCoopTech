<!--All text här är i nuläget hårdkodad och översatt till html via wordtohtml.net. 
Detta kommer i framtiden behöva ändras så att admin kan ändra texten via adminpage,
och att texten ligger {{ $t('user.saveLabel') }}d i databasen istället för att vara hårdkodad här.-->
<template>  
  <template v-if="!isSBProject()">
    <div>
        <p>
          Lorem ipsum dolor sit amet, consectetur adipiscing elit.
          Sed rutrum quam sit amet elit scelerisque ullamcorper. Interdum et malesuada fames ac ante ipsum primis in faucibus.
          Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. In euismod rutrum dui, et pulvinar libero hendrerit et. Sed suscipit quam non velit scelerisque, ut finibus arcu aliquet.
          Vivamus ullamcorper interdum scelerisque. Duis commodo diam eget nulla lobortis molestie. Suspendisse sagittis nibh est, sit amet porttitor eros dictum eu. Donec quis eros sit amet justo semper accumsan.
          Praesent venenatis mollis sem eget tempus. Duis accumsan tempor ex, non dictum leo hendrerit eu. Sed posuere egestas est vitae egestas. Ut porta eget metus non sodales. Nullam in dictum tellus, pretium placerat felis.
        </p>
    </div>
  </template>
  <template v-else>
    <ol>
      <li style="font-weight: bold;"><strong>TILL&Auml;MPNING</strong>
          <ol style="font-weight: initial;">
              <li>Dessa Anv&auml;ndarvillkor g&auml;ller f&ouml;r tj&auml;nster som tillhandah&aring;lls av Svensk Barter Ekonomisk F&ouml;rening, org.nr 769637-0241 via webbplatsen<a href="http://www.svenskbarter.se/">&nbsp;www.svenskbarter.se</a> (&rdquo;Svensk Barter&rdquo;), respektive http://sb.mutualcredit.services/login.</li>
              <li>Villkoren g&auml;ller f&ouml;r alla som beviljats medlemskap i f&ouml;reningen, registrerar ett konto p&aring; webbplatsen och som deltar i byteshandeln.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>OM TJ&Auml;NSTEN</strong>
          <ol style="font-weight: initial;">
              <li>Svensk Barter &auml;r ett handelskooperativ som ocks&aring; fungerar som internt clearinghouse och tredjeparts bokh&aring;llare av byteshandel mellan sina medlemmar, vilka &auml;r aff&auml;rs&auml;gare och yrkesverksamma som avtalar med Svensk Barter f&ouml;r att organisera och underl&auml;tta byte av sina varor och tj&auml;nster med varandra.&nbsp;</li>
              <li>Medlemmar med s&auml;te i Sverige &auml;r &auml;ven del&auml;gare i &rdquo;Svensk Barter ekonomisk f&ouml;rening&rdquo; med r&ouml;str&auml;tt p&aring; f&ouml;reningsst&auml;mman.&nbsp;<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>DEFINITIONER</strong>
          <ol style="font-weight: initial;">
              <li>&rdquo;Medlem&rdquo; eller &rdquo;Kontoinnehavare&rdquo; &auml;r ett legitimt f&ouml;retag eller f&ouml;rening som p&aring; frivillig basis vill utbyta varor och / eller tj&auml;nster med andra medlemmar och vill teckna sig f&ouml;r Svensk Barters tj&auml;nster f&ouml;r bokhantering och byteshandelsm&auml;kleri.&nbsp;</li>
              <li>&rdquo;Barteraff&auml;r&rdquo; eller &rdquo;Barteravtal&rdquo; &auml;r ett byte, k&ouml;p eller f&ouml;rs&auml;ljning av varor och / eller tj&auml;nster d&auml;r betalning sker i Svensk Barters interna kreditsystem registrerat p&aring; medlemmens konto enligt dessa regler.</li>
              <li>&rdquo;Barterkrona&rdquo; &auml;r en redovisningsenhet som anv&auml;nds f&ouml;r att registrera v&auml;rdet av handeln. &Auml;garandel i s&aring;dana komplement&auml;ra valutaenheter betecknar r&auml;tten att erh&aring;lla varor eller tj&auml;nster som finns tillg&auml;ngliga inom bytesringssystemet. Barterkronor f&aring;r anv&auml;ndas endast p&aring; det s&auml;tt och f&ouml;r det syfte som anges i dessa regler. De kommer inte att betraktas som lagligt betalningsmedel, v&auml;rdepapper eller r&aring;varor. De f&aring;r inte l&ouml;sas in mot kontanter, med undantag f&ouml;r vad som godk&auml;nts i dessa regler.<br>&nbsp;</li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>ANV&Auml;NDNING AV TJ&Auml;NSTEN</strong>
          <ol style="font-weight: initial;">
              <li>Som Kontoinnehavare ansvarar du f&ouml;r all anv&auml;ndning av Tj&auml;nsten och f&ouml;r att dina inloggningsuppgifter skyddas mot obeh&ouml;rig &aring;tkomst. Det &auml;r ej till&aring;tet att &ouml;verl&aring;ta eller l&aring;ta n&aring;gon annan anv&auml;nda ditt konto.&nbsp;</li>
              <li>Webbplatsen &auml;r endast till f&ouml;r medlemmar i Svensk Barter eller deltagande anv&auml;ndare som f&aring;tt s&auml;rskilt godk&auml;nnande av styrelsen, och f&aring;r inte anv&auml;ndas av andra parter &auml;n dessa. Kontoinnehavaren f&ouml;rst&aring;r att bristande &ouml;verensst&auml;mmelse med denna best&auml;mmelse kan leda till att kontot avslutas.</li>
              <li>Genom att acceptera dessa villkor garanterar Kontoinnehavaren att Tj&auml;nsten inte kommer anv&auml;ndas f&ouml;r &auml;ndam&aring;l som strider mot g&auml;llande lagstiftning, myndighetsf&ouml;reskrift eller som p&aring; annat s&auml;tt kan medf&ouml;ra skada eller ol&auml;genhet f&ouml;r Svensk Barter eller tredjeman.</li>
              <li>Tj&auml;nsten f&aring;r inte heller anv&auml;ndas f&ouml;r att skicka o&ouml;nskad reklam eller marknadsf&ouml;ring till andra medlemmar. Andra f&ouml;rbjudna aktiviteter inkluderar, men &auml;r inte begr&auml;nsade till, utbredning av maskar, trojaner och virus och anv&auml;ndande av n&auml;tverket f&ouml;r att till&aring;ta obeh&ouml;rigt tilltr&auml;de till n&aring;gon annan tj&auml;nst som &auml;r tillg&auml;nglig via n&auml;tverket.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>M&Auml;KLARTJ&Auml;NSTER</strong>
          <ol style="font-weight: initial;">
              <li>Svensk Barter tillhandah&aring;ller tj&auml;nster i m&auml;kleri vid organisering och underl&auml;ttande av handel bland medlemmarna.&nbsp;</li>
              <li>Ansvaret f&ouml;r handeln ligger uteslutande hos de tv&aring; medlemmar som deltar i en aff&auml;rsuppg&ouml;relse inom bytesringssystemet. Svensk Barter kommer att g&ouml;ra sitt b&auml;sta f&ouml;r att m&auml;kla varor eller tj&auml;nster till de handlande medlemmarna, noggrant registrera och bokf&ouml;ra aff&auml;rer i barterkronor och att f&ouml;rvalta dessa regler i enlighet med villkoren. Medlemmen bekr&auml;ftar dock att de enda g&auml;llande principerna f&ouml;r all handel inom bytesringssystemet &auml;r att ansvaret ligger hos k&ouml;paren och s&auml;ljaren, handeln &auml;r frivilligt ingiven, att handlande medlemmar inte &auml;r medlemmar hos andra handlande medlemmar, att barterm&auml;klare inte deltar via eget f&ouml;retag som medlem, att Svensk Barter inte f&ouml;retr&auml;der n&aring;gon enskild medlem som agent i handeln och inte heller &auml;r borgen&auml;r f&ouml;r medlemmars negativa saldon i barterkronor. Enda undantaget f&ouml;r att medlemmar kan agera barterm&auml;klare &auml;r vid uppstart av en geografiskt ny barterhub och efter styrelsebeslut.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>TJ&Auml;NSTENS TILLG&Auml;NGLIGHET</strong>
          <ol style="font-weight: initial;">
              <li>Svensk Barter avser att h&aring;lla tj&auml;nsten tillg&auml;nglig alla dagar om &aring;ret men f&ouml;rbeh&aring;ller sig r&auml;tten att vid underh&aring;ll eller uppdateringar st&auml;nga ned hela eller delar av tj&auml;nsten.</li>
              <li>Svensk Barter garanterar inte att anv&auml;ndningen av tj&auml;nsten kommer vara oavbruten eller fri fr&aring;n fel.</li>
              <li>Svensk Barter ansvarar d&auml;rmed inte f&ouml;r eventuella tekniska fel, oavsett felens orsak, eller andra st&ouml;rningar i tj&auml;nstens tillg&auml;nglighet som orsakas av till exempel bristf&auml;llig internetuppkoppling, st&ouml;rningar hos webbhotellet eller i samband med uppdateringar och underh&aring;ll.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>ANSVARSFRIHET</strong>
          <ol style="font-weight: initial;">
              <li>Svensk Barter har f&ouml;r avsikt att all information p&aring; webbplatsen ska vara korrekt och s&aring; uppdaterad som m&ouml;jligt men detta &auml;r ingenting som Svensk Barter p&aring; n&aring;got vis kan garantera.</li>
              <li>Svensk Barter ansvarar endast f&ouml;r &aring;taganden och representationer som skriftligen gjorts av en vederb&ouml;rligen auktoriserad tj&auml;nsteman i Svensk Barter, m&auml;klare, agenter eller anst&auml;lld, g&auml;llande n&aring;gon som helst skyldighet.&nbsp;</li>
              <li>Kontoinnehavaren anv&auml;nder och deltar i tj&auml;nsten p&aring; egen risk. Svensk Barter &auml;r inte ansvarig f&ouml;r eventuella skador eller f&ouml;rluster som &auml;r resultatet av Kontoinnehavarens anv&auml;ndning eller deltagande i tj&auml;nsten.</li>
              <li>Svensk Barter ger inga garantier, varken uttryckligt eller underf&ouml;rst&aring;tt, och fr&aring;ns&auml;ger sig allt ansvar avseende funktion, kvalitet, leveransdatum, s&auml;ljbarhet, priser eller eventuella villkor f&ouml;r alla handelstransaktioner i bytesringssystemet. Medlemmen f&ouml;rbinder sig h&auml;rmed att h&aring;lla Svensk Barter skadefri med avseende p&aring; anspr&aring;k, skuld eller ansvarsskyldighet som uppst&aring;r genom transaktioner d&auml;r medlemmen &auml;r en k&ouml;pare eller s&auml;ljare.</li>
              <li>Medlemmen bekr&auml;ftar vidare att varje handelstransaktion i vilken den deltar, &auml;r frivillig och att Svensk Barter varken &auml;r sin egen agent eller agent f&ouml;r n&aring;gon annan medlem i utbyten d&auml;r Svensk Barter sj&auml;lv inte &auml;r en k&ouml;pare eller s&auml;ljare.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>SKADEL&Ouml;SHETSF&Ouml;RBINDELSE</strong>
          <ol style="font-weight: initial;">
              <li>Drift av denna tj&auml;nst medf&ouml;r inneboende risker f&ouml;r Svensk Barter eftersom Svensk Barter bokstavligen inte kan kontrollera alla aspekter av tj&auml;nsten hela tiden. Genom att bekr&auml;fta detta avtal accepterar du att ers&auml;tta Svensk Barter f&ouml;r eventuella skador, kostnader och utgifter, inklusive rimliga juristkostnader, som uppkommer f&ouml;r Svensk Barter till f&ouml;ljd av eller som uppst&aring;r p&aring; grund av din verksamhet p&aring; Tj&auml;nsten inklusive, men inte begr&auml;nsat till, eventuella p&aring;st&aring;enden om brott mot upphovsr&auml;tt, varum&auml;rke eller skyddat material, eventuella anspr&aring;k p&aring; skadelidande, f&ouml;rtal, &auml;rekr&auml;nkning, diskriminering eller liknande, &nbsp;eller eventuell anv&auml;ndning av Tj&auml;nsten f&ouml;r olagliga &auml;ndam&aring;l.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>GEMENSAMT OCH ENSKILT ANSVAR</strong>
          <ol style="font-weight: initial;">
              <li>Efter styrelsens godk&auml;nnande och inbetald insats och &aring;rsavgift, vilket m&aring;ste sammanfalla med godk&auml;nnande av stadgar, detta avtal och eventuella andra policys, &nbsp;blir ett bolag eller f&ouml;rening medlem av Svensk Barter, utifr&aring;n detta tar medlemmen och den enskilda undertecknaren f&ouml;r medlemmen ut ett gemensamt och separat ansvar f&ouml;r alla ink&ouml;p och avgifter som anges i detta avtal och den enskilda undertecknaren som verkar p&aring; uppdrag av ett bolag eller f&ouml;rening samtycker till att vara personligt ansvarig f&ouml;r full prestering av n&auml;mnda bolag eller f&ouml;rening i enlighet med detta avtal.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>TILLG&Auml;NGLIGHET AV PRODUKTER OCH TJ&Auml;NSTER</strong>
          <ol style="font-weight: initial;">
              <li>Bytesringssystemet kan bara best&aring; av ett visst antal varor och tj&auml;nster f&ouml;r ett visst antal medlemmar vid varje givet tillf&auml;lle.</li>
              <li>Svensk Barter ska g&ouml;ra sitt b&auml;sta f&ouml;r att ansluta nya f&ouml;retag som medlemmar, vars produkter eller tj&auml;nster &auml;r &ouml;nskade av befintliga medlemmar, f&ouml;r att tillgodose alla medlemmars behov.</li>
              <li>Medlemmen &auml;r inf&ouml;rst&aring;dd med att en bytesring &auml;r en begr&auml;nsad marknadsplats och att Svensk Barter inte kan uppfylla alla ink&ouml;psf&ouml;rfr&aring;gningar hela tiden. Svensk Barter &auml;r inte ansvarig om en medlem inte kan hitta specifika produkter eller tj&auml;nster att k&ouml;pa fr&aring;n medlemmar.</li>
              <li>Svensk Barter &aring;tar sig &auml;ven att efter b&auml;sta f&ouml;rm&aring;ga motverka oh&aring;llbar deflation i systemet genom att inte ansluta f&ouml;r m&aring;nga medlemmar med samma utbud av varor och tj&auml;nster<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>MARKNADSF&Ouml;RING</strong>
          <ol style="font-weight: initial;">
              <li>Om inte annat &ouml;verenskommits mellan parterna har Svensk Barter r&auml;tten att marknadsf&ouml;ra och annonsera till andra medlemmar om tillg&auml;ngligheten f&ouml;r medlemmens produkter eller tj&auml;nster.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>PRISER OCH BETALNINGSVILLKOR</strong>
          <ol style="font-weight: initial;">
              <li>Alla avgifter, inklusive initiala anslutningssavgifter, provisioner och andra avgifter som medlemmarna betalar till Svensk Barter anv&auml;nds f&ouml;r att t&auml;cka kostnaden f&ouml;r &nbsp;tj&auml;nster som g&ouml;rs i behandlingen av medlemmen i bytesringssystemet, drift av clearinghus och m&auml;klarfunktioner, administration av dessa regler, uppr&auml;ttande och underh&aring;ll av register och underl&auml;ttande av medlemmars anv&auml;ndning av bytesringssystemet. &Ouml;verskott fr&aring;n avgifter kan &auml;ven stadgeenligt investeras i samh&auml;llsnytta (Corporate Social Responsibility).</li>
              <li>Medlemmen f&ouml;rbinder sig att betala Svensk Barter samtliga s&aring;dana avgifter inom 30 dagar efter det m&aring;natliga kontoutl&aring;tandet eller fakturering, vid sen betalning kan dr&ouml;jsm&aring;lsr&auml;nta l&auml;ggas p&aring; det utest&aring;ende saldot till dess att full betalning erlagts.</li>
              <li>Om medlemmen &auml;r i dr&ouml;jsm&aring;l med betalning av kontantavgifter i mer &auml;n trettio (30) dagar, accepterar medlemmen att avst&aring; fr&aring;n alla sina r&auml;ttigheter f&ouml;r anv&auml;ndning av eventuella positiva saldon i barterkronor p&aring; sitt konto och ger Svensk Barter r&auml;tten att h&aring;lla s&aring;dan balans s&aring; l&auml;nge som eventuella kontantavgifter &auml;r utest&aring;ende.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>SKYLDIGHETER VID ANV&Auml;NDNING AV BARTERKRONOR</strong>
          <ol style="font-weight: initial;">
              <li>Medlemmen erk&auml;nner att barterkronor i bytesringssystemet (positiva saldon) &auml;r skulder hos juridiska personer / f&ouml;retag som har spenderat mer &auml;n sina int&auml;kter (negativa saldon), att det finns en borgen&auml;r / g&auml;lden&auml;rsf&ouml;rh&aring;llande mellan s&aring;dana personer, och att transaktioner med barterkronor som utbytesmedel medf&ouml;r de normala aff&auml;rsriskerna som &auml;r f&ouml;rknippade med en kredittransaktion.</li>
              <li>Barterkronor &auml;r som regel de skulder som medlemmarna &auml;r skyldiga till bytesringssystemet, och inte till Svensk Barter, som enbart &auml;r ansvarig f&ouml;r sin egen skulds&auml;ttning i bytesringssystemet.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>UPPR&Auml;TTANDE AV RESERVFOND</strong>
          <ol style="font-weight: initial;">
              <li>Svensk Barter &auml;r auktoriserad att uppr&auml;tta en fordringsreservfond genom att regelbundet ta ut en avgift fr&aring;n alla konton i barterkronor som ska betalas in i en s&aring;dan fond. Eventuella positiva saldon som &ouml;vergivits eller &ouml;verl&auml;mnats av tidigare medlemmar i enlighet med Reglerna kan ocks&aring; betalas in i reservfonden. &nbsp;Fordringsreservfonden finns till f&ouml;r att vid behov t&auml;cka negativa saldon som l&auml;mnas av- eller inte kan betalas tillbaka av medlemmar. Detta f&ouml;r att uppeh&aring;lla h&aring;llbara balanser i f&ouml;reningens interna kreditsystem.</li>
              <li>F&ouml;r att motverka oh&aring;llbara obalanser p&aring; grund av l&aring;ngvariga negativa saldon hos medlemmar som missk&ouml;ter sin st&auml;llning, samt vid etablering av lokala barterklubbar p&aring; nya orter, f&aring;r Svensk Barter lyfta serviceavgiften hos nya medlemmar efter eget godtycke upp till ett (1) &aring;r f&ouml;r att snabbt skapa tillv&auml;xt i bytesringssystemet.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>L&Aring;N I BARTERKRONOR </strong>
          <ol style="font-weight: initial;">
              <li>Medlemmar beviljar Svensk Barter r&auml;tten och ansvaret att ge ut l&aring;n i barterkronor till en medlem p&aring; villkor som &auml;r f&ouml;renliga med medlemmens kreditv&auml;rdighet och f&ouml;rm&aring;ga att betala tillbaka. Ut&ouml;vandet av denna makt ska vara enligt eget gottfinnande av Svensk Barters f&ouml;rvaltning med avseende p&aring; stadgar, saldo, r&auml;nta, villkor, utl&aring;ningspolitik och andra uppgifter. Svensk Barter f&ouml;rbeh&aring;ller sig r&auml;tten att kr&auml;va att medlemmen ska signera ett formellt l&aring;neavtal, beg&auml;ra bokslut, kredithistoria och s&auml;kerhet innan l&aring;n godk&auml;nns.</li>
              <li>Svensk Barter &auml;r inte skyldig att bevilja checkkredit eller ge ut l&aring;n i barterkronor n&auml;r som helst. Svensk Barter kommer att g&ouml;ra sitt b&auml;sta f&ouml;r att s&auml;kerst&auml;lla r&auml;ttvisa &aring;terbetalningar av amortering och r&auml;nta med l&auml;mplig s&auml;kerhet i alla l&aring;n.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>CHECKKREDIT I BARTERKRONOR</strong>
          <ol style="font-weight: initial;">
              <li>Svensk Barter kan uppr&auml;tta en kreditgr&auml;ns f&ouml;r varje medlem, som kan justeras fr&aring;n tid till annan. Det utest&aring;ende saldot p&aring; krediten, utifr&aring;n hur den utnyttjas, kommer att behandlas som ett l&aring;n enligt dessa regler. En medlem som g&ouml;r ink&ouml;p ut&ouml;ver gr&auml;nsen f&ouml;r sin checkkredit m&aring;ste s&auml;lja sina produkter eller tj&auml;nster till 100% barter tills ett s&aring;dant underskott balanserats.</li>
              <li>Svensk Barter kan f&ouml;r egen del anv&auml;nda checkkredit i barterkronor motsvarande upp till tre &aring;rs budgeterade int&auml;kter eller vid styrelsebeslut om att anv&auml;nda checkkrediten tillf&auml;lligt f&ouml;r att stimulera handeln i systemet genom att k&ouml;pa in varor och tj&auml;nster som sedan s&auml;ljs vidare utan egen s&auml;ljmarginal. Svensk Barter &aring;tar sig att motverka inflation i kreditsystemet genom att sj&auml;lv vara m&aring;ttlig i anv&auml;ndandet av sin egen checkkredit och aldrig, vare sig g&auml;llande huvudsakligt handelskonto eller subkonton, anv&auml;nda sin informationsf&ouml;rdel f&ouml;r att konkurrera med medlemmar om attraktiva varor och tj&auml;nster.<br>&nbsp;</li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>GODK&Auml;NNANDE AV HANDEL</strong>
          <ol style="font-weight: initial;">
              <li>Alla ink&ouml;p som g&ouml;rs i bytesringssystemet m&aring;ste godk&auml;nnas av Svensk Barter f&ouml;re f&ouml;rs&auml;ljning och ha ett auktoriseringsnummer utf&auml;rdat och registrerat av Svensk Barter eller liknande auktoriseringsfunktion tilldelad av medlemmens barterm&auml;klare. Om auktorisation inte erh&aring;lls vid tidpunkten f&ouml;r f&ouml;rs&auml;ljningen har Svensk Barter ingen skyldighet att l&aring;ta genomf&ouml;ra transaktionen.</li>
              <li>Svensk Barter f&ouml;rbeh&aring;ller sig r&auml;tten att f&ouml;rv&auml;gra en medlem auktorisering av en transaktion under n&aring;got av f&ouml;ljande villkor:<ol style="list-style-type: lower-alpha;">
                      <li>Om k&ouml;paren inte har tillr&auml;ckligt saldo med barterkronor f&ouml;r att g&ouml;ra ett s&aring;dant k&ouml;p;</li>
                      <li>Om n&aring;gon part inte &auml;r en medlem i god st&auml;llning; eller</li>
                      <li>Om s&auml;ljaren misslyckas med att ringa till Svensk Barter eller anv&auml;nda f&ouml;reningens automatiserade handelstillst&aring;ndssystem, f&ouml;r auktorisering av transaktionen vid f&ouml;rs&auml;ljningstillf&auml;llet.<br><br></li>
                  </ol>
              </li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>FELAKTIGHETER I BOKH&Aring;LLNING OCH REGISTER</strong>
          <ol style="font-weight: initial;">
              <li>Svensk Barter har r&auml;tt att &aring;terkalla en transaktion p&aring; grund av bristande efterlevnad av dessa regler, i vilket fall det &auml;r s&auml;ljarens ansvar att &aring;terta barterkronor fr&aring;n k&ouml;paren.</li>
              <li>Alla transaktioner &auml;r f&ouml;rem&aring;l f&ouml;r revision och verifikation av Svensk Barter, och i h&auml;ndelse av felaktigheter kan Svensk Barter debitera eller kreditera medlemmens konto utan f&ouml;rvarning till f&ouml;ljd av ett s&aring;dant fel.</li>
              <li>Svensk Barter kommer att tillhandah&aring;lla handelshistorik till medlemmar som &aring;terspeglar kontoaktivitet och saldo i barterkronor i bytesringssystemet. Handelshistoriken anses vara korrekt som tryckt s&aring;vida inte medlem meddelar Svensk Barter skriftligen om eventuella avvikelser inom trettio (30) dagar efter rapporteringsdatum<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>&Ouml;VERPRISS&Auml;TTNING</strong>
          <ol style="font-weight: initial;">
              <li>Medlemmarna &auml;r skyldiga att s&auml;lja sina varor och tj&auml;nster till sina normala sedvanliga priser som de s&auml;ljer sina varor och tj&auml;nster till i den vanliga kontanta handelsmilj&ouml;n.</li>
              <li>Svensk Barter f&ouml;rbeh&aring;ller sig r&auml;tten att unders&ouml;ka klagom&aring;l om &ouml;verpriss&auml;ttning fr&aring;n en medlem. Brott kan leda till upps&auml;gning av medlemmens konto och / eller omedelbar justering av de ber&ouml;rda transaktionerna.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>TILL&Auml;GGSPRISER</strong>
          <ol style="font-weight: initial;">
              <li>Som ett undantag fr&aring;n regeln att handel ska ske till r&aring;dande marknadspriser (definierat som normalt eller sedvanligt kontantpris) kan till&auml;ggspriser till&aring;tas f&ouml;r vissa kategorier som mat, bensin, apparater, elektronik och produkter d&auml;r vinstmarginalen &auml;r relativt l&aring;g, och utbudet av s&aring;dana produkter i bytesringen &auml;r l&aring;gt. Svensk Barter kommer att fastst&auml;lla till&auml;ggspriset med den ber&ouml;rda medlemmen. Medlemmarna kommer att bli informerade om eventuella till&auml;ggspriser.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>DIREKT BARTERHANDEL MELLAN MEDLEMMAR</strong>
          <ol style="font-weight: initial;">
              <li>Direkta barteravtal mellan medlemmar f&ouml;r att undvika m&auml;klararvode till Svensk Barter &auml;r f&ouml;rbjudna, och f&ouml;rem&aring;l f&ouml;r dubbel transaktionsavgift om p&aring;kommen.</li>
              <li>S&aring;dan direkt barterhandel kan &auml;ven leda till upps&auml;gning av medlemskap.</li>
              <li>Kontanta transaktioner mellan medlemmar vid sidan om barterhandeln ser f&ouml;reningen d&auml;remot positivt p&aring; och uppmuntrar.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>SKATTER</strong>
          <ol style="font-weight: initial;">
              <li>S&auml;ljaren ska ta ut l&auml;mpliga f&ouml;rs&auml;ljnings-, merv&auml;rdes- och punktskatter och samla in och registrera dessa p&aring; transaktionsformul&auml;ret vid tidpunkten f&ouml;r f&ouml;rs&auml;ljningen. Under inga omst&auml;ndigheter kommer Svensk Barter att vara ansvarig f&ouml;r att betala n&aring;gon f&ouml;rs&auml;ljningsskatt, merv&auml;rdesskatt, punktskatt eller annan s&aring;dan skatt f&ouml;r n&aring;gon medlems r&auml;kning.</li>
              <li>Medlemmar uppmanas att behandla transaktioner som involverar barterkronor generellt som skattepliktiga h&auml;ndelser och f&ouml;lja skatteverkets rekommendationer f&ouml;r deltagande i bytesringsverksamhet. Ansvaret f&ouml;r deklarering och redovisning av alla till&auml;mpliga skatter som h&auml;rr&ouml;r fr&aring;n handelstransaktioner i bytesringssystemet ligger uteslutande hos medlemmen.</li>
              <li>Svensk Barter kommer att samverka med myndigheter om s&aring; p&aring;kallas f&ouml;r att reda ut misst&auml;nkta skattebrott.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>MEDLEMMENS GRUNDL&Auml;GGANDE ANSVAR F&Ouml;R ATT UPPEH&Aring;LLA GOD ST&Auml;LLNING</strong>
          <ol style="font-weight: initial;">
              <li>En medlem som bedriver handel och uppf&ouml;r sig i enlighet med dessa regler, stadgar och f&ouml;reningens &ouml;vriga medlemsrelaterade styrdokument, sk&ouml;ter sina &aring;tagande g&auml;llande avgifter och har ett giltigt, ej utg&aring;nget, medlemskonto anses vara en &quot;medlem i god st&auml;llning&quot; som tar sitt grundl&auml;ggande medlemsansvar.</li>
              <li>Endast medlemmar i god st&auml;llning erbjuds tj&auml;nster av Svensk Barter.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>F&Ouml;RM&Aring;NSBEHANDLING</strong>
          <ol style="font-weight: initial;">
              <li>Medlemmen erk&auml;nner att regleringen av medlemmar, ink&ouml;p, kontroll av kreditgivning och f&ouml;rdelning av varor eller tj&auml;nster som &auml;r bristf&auml;lliga &auml;r inbyggda i Svensk Barters ledningsansvar, skall ut&ouml;vas enligt Svensk Barters eget gottfinnande, och utg&ouml;r inte f&ouml;rm&aring;nsbehandling enligt dessa regler.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>MEDLEMS NEKANDE AV GILTIG INK&Ouml;PSBEG&Auml;RAN</strong>
          <ol style="font-weight: initial;">
              <li>Det erk&auml;nns, f&ouml;rst&aring;s och godk&auml;nns av medlemmen, att om medlemmen inte vill hedra och acceptera ink&ouml;p fr&aring;n en annan medlem som anv&auml;nder bytesringens handelssystem enligt dessa regler, utom n&auml;r ledamot &auml;r i v&auml;ntel&auml;ge eller reservstatus i enlighet med dessa regler, ska medlemmens agerande anses vara v&auml;sentligt i strid med detta avtal. Medlemmen &auml;r inf&ouml;rst&aring;dd med att om en s&aring;dan h&auml;ndelse intr&auml;ffar kommer skador att uppkomma till Svensk Barter i aff&auml;rsf&ouml;rh&aring;llandet mellan Svensk Barter och medlemmen vars ink&ouml;psbeg&auml;ran inte hedrades, och att den nekande medlemmen &auml;r ansvarig f&ouml;r s&aring;dana skador.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>MISSBRUK AV TJ&Auml;NSTEN</strong>
          <ol style="font-weight: initial;">
              <li>Svensk Barter f&ouml;rbeh&aring;ller sig r&auml;tten att ensidigt besluta om att st&auml;nga av Kontoinnehavare fr&aring;n tj&auml;nsten. Avst&auml;ngning kan till exempel ske till f&ouml;ljd av klagom&aring;l, brott mot dessa anv&auml;ndarvillkor eller om tj&auml;nsten anv&auml;nds p&aring; ett s&auml;tt som kan orsaka Svensk Barter eller tredjeman skada.</li>
              <li>Att Svensk Barter underl&aring;ter att genomdriva en viss regel eller policy vid ett tillf&auml;lle eller mot en Kontoinnehavare utesluter inte att samma regel eller policy till&auml;mpas vid en annan tidpunkt eller mot en annan Kontoinnehavare.</li>
              <li>&Ouml;vertr&auml;delser av dessa Anv&auml;ndarvillkor eller Svensk Barters stadgar &auml;r oetiska och kan vara brottsliga. Kontoinnehavare f&ouml;rv&auml;ntas rapportera till Svensk Barter om eventuella uppgifter som du kan ha ang&aring;ende fall d&auml;r denna &ouml;verenskommelse eller anv&auml;ndningsvillkoren har blivit &ouml;vertr&auml;dda eller brutits mot. N&auml;r Svensk Barter blir medveten om eventuella &ouml;vertr&auml;delser, kommer f&ouml;reningen att inleda en unders&ouml;kning. Samtidigt, f&ouml;r att f&ouml;rhindra ytterligare m&ouml;jlig obeh&ouml;rig verksamhet, kan Svensk Barter efter eget gottfinnande {{ $t('user.cancelLabel') }}a alla inblandade konton som har tillg&aring;ng till Tj&auml;nsten. Konstaterade &ouml;vertr&auml;delser kan leda till omedelbar upps&auml;gning av enskilda konton och/eller r&auml;ttsliga p&aring;f&ouml;ljder. Kontoupph&auml;vandet kan &aring;terst&auml;llas efter eget gottfinnande av Svensk Barter efter betalning av en &aring;terst&auml;llningsavgift.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>UPPS&Auml;GNING AV HANDELSKONTO I BYTESRINGSSYSTEMET</strong>
          <ol style="font-weight: initial;">
              <li>Endera parten kan s&auml;ga upp detta avtal med tio (10) dagars varsel. Upps&auml;gning ska ske skriftligen.</li>
              <li>Omedelbart efter upps&auml;gning, dock senast inom 30 dagar, ska samtliga utest&aring;ende avgifter betalas till Svensk Barter, och:<ol style="list-style-type: lower-alpha;">
                      <li>Om medlemmen har en negativ handelsbalans (dvs. k&ouml;p &ouml;verstiger f&ouml;rs&auml;ljningen) ska medlemmen betala kontot med barterkronor eller acceptabla produkter eller tj&auml;nster inom trettio dagar efter upps&auml;gningstidpunkten. Om handelsbalansen fortfarande &auml;r negativ efter dessa 30 dagar ska medlemmen betala resterande belopp till Svensk Barter med kontanter.</li>
                      <li>Om medlemmen har en positiv handelsbalans (f&ouml;rs&auml;ljningen &ouml;verstiger ink&ouml;p) har medlemmen r&auml;tt att forts&auml;tta vara aktiv till dess att det positiva saldot &auml;r f&ouml;rbrukat. Under den tiden ska samtliga kontantavgifter och serviceavgifter till Svensk Barter betalas i f&ouml;rskott.&nbsp;</li>
                  </ol>
              </li>
              <li>Om upps&auml;gning av handelskonto skall f&ouml;ljas av uttr&auml;de ur den ekonomiska f&ouml;reningen f&ouml;ljs f&ouml;reningens stadgar.</li>
              <li>Svensk Barter har ingen skyldighet under n&aring;gra omst&auml;ndigheter, d&auml;ribland upph&ouml;rande av medlemmarnas handelsf&ouml;rpliktelser, att omvandla ett positivt handelskontos saldo till riktiga kontanter i lagligt betalningsmedel (Svenska kronor). Vid upps&auml;gning &aring;terbetalas inga erlagda medlemsavgifter, serviceavgifter eller provisioner.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>&Ouml;VERL&Aring;TELSE AV KONTO</strong>
          <ol style="font-weight: initial;">
              <li>En medlems konto kan inte &ouml;verl&aring;tas till annan part utan att Svensk Barter uttryckligen meddelat skriftligt samtycke.</li>
              <li>En medlem kan d&auml;remot ha subkonton till anst&auml;llda eller f&ouml;r &auml;gares privata bruk om mjukvaran till&aring;ter det.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>HANTERING AV PERSONUPPGIFTER</strong>
          <ol style="font-weight: initial;">
              <li>N&auml;r du ans&ouml;ker om medlemskap eller registrerar dig som Kontoinnehavare hos Svensk Barter f&ouml;rser du oss med vissa personuppgifter som &auml;r n&ouml;dv&auml;ndiga f&ouml;r att vi ska tillhandah&aring;lla tj&auml;nsten. Genom att registrera ett anv&auml;ndarkonto godk&auml;nner du att vi samlar in och behandlar personuppgifter i enlighet med v&aring;r Integritetspolicy.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>IMMATERIELLA R&Auml;TTIGHETER</strong>
          <ol style="font-weight: initial;">
              <li>Svensk Barter &auml;ger samtliga r&auml;ttigheter, inklusive varum&auml;rke, upphovsr&auml;tt och andra immateriella r&auml;ttigheter till tj&auml;nsten. Det &auml;r inte till&aring;tet att som Anv&auml;ndare av tj&auml;nsten kopiera, modifiera, distribuera eller p&aring; annat s&auml;tt sprida information och inneh&aring;ll fr&aring;n tj&auml;nsten till allm&auml;nheten via internet eller p&aring; annat s&auml;tt.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>UPPL&Ouml;SNING</strong>
          <ol style="font-weight: initial;">
              <li>I h&auml;ndelse av att bytesringssystemet uppl&ouml;ses eller p&aring; annat s&auml;tt upph&ouml;r med att g&ouml;ra aff&auml;rer ska alla medlemmar i negativ handelsposition betala belopp de &auml;r skyldiga i kontanter (en barterkrona &auml;r nominellt lika med en svensk krona i lagligt betalningsmedel) till en fond. Fonden, minus utgifter och plus eventuella lager, kommer att f&ouml;rdelas proportionellt till alla medlemmar som befinner sig i en positiv handelsposition g&auml;llande barterkronor. S&aring;ledes kommer alla medlemmar i en positiv position att f&aring; pengar f&ouml;r sina barterkronor i den utstr&auml;ckning som medel till&aring;ter. Svensk Barter ansvarar inte gentemot n&aring;gra medlemmar f&ouml;r kontanter eller barterkronor ut&ouml;ver f&ouml;rdelningen av s&aring;dana medel som n&auml;mnts ovan.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>&Auml;NDRINGAR OCH TILL&Auml;GG</strong>
          <ol style="font-weight: initial;">
              <li>Svensk Barter f&ouml;rbeh&aring;ller sig r&auml;tten att ensidigt besluta om &auml;ndringar och till&auml;gg till dessa Anv&auml;ndarvillkor samt att justera &aring;rliga medlemsavgifter, provisioner, transaktionsavgifter och eventuella serviceavgifter. Alla &auml;ndringar ska meddelas skriftligen via brev eller e-post trettio senast (30) dagar f&ouml;re &auml;ndringen tr&auml;der i kraft.</li>
              <li>Aktuella villkor och avgifter finns alltid publicerade p&aring; webbplatsen och du som medlem ansvarar f&ouml;r att h&aring;lla dig uppdaterad om eventuella &auml;ndringar.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>TVISTER</strong>
          <ol style="font-weight: initial;">
              <li>Eventuella handelstvister ska endast vara mellan k&ouml;paren och s&auml;ljaren och ska l&ouml;sas av dessa parter sj&auml;lva. Emellertid f&ouml;rbeh&aring;ller sig Svensk Barter r&auml;tten att agera som medlare f&ouml;r att f&ouml;rs&ouml;ka l&ouml;sa tvister n&auml;r Svensk Barter anser att det &auml;r i sitt b&auml;sta intresse och f&ouml;r bytesringen i stort att agera som s&aring;dan funktion.<br><br></li>
          </ol>
      </li>
      <li style="font-weight: bold;"><strong>VAL AV LAG OCH FORUM</strong>
          <ol style="font-weight: initial;">
              <li>Svensk lag ska till&auml;mpas p&aring; detta Avtal.</li>
              <li>Tvist mellan Svensk Barter och medlem i anledning av Avtalet ska slutligen avg&ouml;ras av svensk allm&auml;n domstol.&nbsp;</li>
          </ol>
      </li>
    </ol>
  </template>
</template>
    
<script>
export default {
  methods: {
    isSBProject () {
      if (process.env.VUE_APP_NAME === 'SB') {
        return true
      }
      return false
    }
  }
}
</script>

<style scoped>  
  h1 {
    padding: 10px 0px 10px 0px;
    font-size: 1.6rem;
  }
  h2 {
    padding: 10px 0px 10px 0px;
    font-size: 1.4rem;
  }
  h3 {
    padding: 10px 0px 10px 0px;
    font-size: 1.2rem;
    font-weight: bold;
  }
  p {
      padding: 0px;
      font-size: 1rem;
      font-weight: normal;
  }
</style>
