<template>
  <h1> MIN SIDA </h1>
  <div class='topnav' id='myTopnav'>
    <a href='#profil' @click="activate('profil'); profil=true" :class="{ active: profil }" id='profil'>Min profil</a>
    <a href='#kop' @click="activate('kop'); kop=true" :class="{ active: kop }" id='kop'>Mina köp</a>
    <a href='#artiklar' @click="activate('artiklar'); artiklar=true" :class="{ active: artiklar }" id='artiklar'>Mina artiklar</a>
    <a id='statistik'>Min statistik</a>
    <a href='#forfragningar' @click="activate('forfragningar'); forfragningar=true" :class="{ active: forfragningar }" id='kopforfragningar'>Mina köpförfrågningar</a>
    <a id='installningar'>Inställningar</a>
  </div>
    <div>
      <MinaKop v-if="kop"/>
      <MinaArtiklar v-else-if="artiklar"/>
      <MinaKopforfragningar v-else-if="forfragningar"/>
      <MinProfil v-else/>
    </div>
</template>

<script>
// @ is an alias to /src
import MinProfil from '@/components/MinProfil.vue'
import MinaKop from '@/components/MinaKop.vue'
import MinaArtiklar from '@/components/MinaArtiklar.vue'
import MinaKopforfragningar from '@/components/MinaKopforfragningar.vue'

export default {
  data () {
    return {
      profil: !((window.location.href.indexOf('#kop') > -1) || (window.location.href.indexOf('#artiklar') > -1) || (window.location.href.indexOf('#forfragningar') > -1)),
      kop: (window.location.href.indexOf('#kop') > -1),
      artiklar: (window.location.href.indexOf('#artiklar') > -1),
      forfragningar: (window.location.href.indexOf('#forfragningar') > -1)
    }
  },
  components: {
    MinProfil,
    MinaKop,
    MinaArtiklar,
    MinaKopforfragningar
  },
  methods: {
    activate: function (element) {
      this.profil = (element === 'profil')
      this.kop = (element === 'kop')
      this.artiklar = (element === 'artiklar')
      this.forfragningar = (element === 'forfragningar')
    }
  }
}

</script>

<!-- Add 'scoped' attribute to limit CSS to this component only -->
<style scoped>
.topnav {
  overflow: hidden;
  display: flex;
  justify-content: center;
}

/* Style the links inside the navigation bar */
.topnav a {
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  font-size: 17px;
  display: inline-block;
  color: steelblue;
  border-style: solid;
  border-color: gainsboro;
  border-radius: 5px;
  margin-top: -1px;
  margin-bottom: -1px;
  margin-left: -1px;
  margin-right: -1px;
  border-width: 2px;

}

/* Add an active class to highlight the current page */
.topnav a.active {
  color: dimgrey;
  border-bottom: none;
}

h1  {
  text-align: center;
}

</style>
