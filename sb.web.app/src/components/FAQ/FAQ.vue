<template>
  <div className="flexbox-container">
  <div>
    <h1 className="title">HANDELSREGLER & TIPS</h1>
  </div>

  <div className='topnav flexbox-item' id='myTopnav'>
      <a href='#' @click="this.tab='questions'" :class="{ active: this.tab!='background' && this.tab!='usefulcontacts' && this.tab!='traderules' && this.tab!='statues' }" id='questions'>Vanliga frågor</a>
      <a href='#' @click="this.tab='background'" :class="{ active: this.tab==='background' }" id='background'>Bakgrund</a>
      <a href='#' @click="this.tab='usefulcontacts'" :class="{ active: this.tab==='usefulcontacts' }" id='usefulcontacts'>Användbara kontakter</a>
      <a href='#' @click="this.tab='traderules'" :class="{ active: this.tab==='traderules' }" id='traderules'>Handlingsregler</a>
      <a href='#' @click="this.tab='statues'" :class="{ active: this.tab==='statues' }" id='statues'>Stadgar</a>
    </div>
    <div className="content flexbox-item">
      <Background v-if="this.tab==='background'"/>
      <UsefulContacts v-else-if="this.tab==='usefulcontacts'"/>
      <TradeRules v-else-if="this.tab==='traderules'"/>
      <Statues v-else-if="this.tab==='statues'"/>
      <Questions v-else/>
    </div>
  </div>
</template>

<script>
// @ is an alias to /src
import Questions from '@/components/FAQ/Questions.vue'
import Background from '@/components/FAQ/Background.vue'
import UsefulContacts from '@/components/FAQ/UsefulContacts.vue'
import TradeRules from '@/components/FAQ/TradeRules.vue'
import Statues from '@/components/FAQ/Statues.vue'

export default {
  data () {
    return {
    }
  },
  components: {
    Questions,
    Background,
    UsefulContacts,
    TradeRules,
    Statues
  },
  created () {
    if (this.$route.params.tab) {
      this.tab = this.$route.params.tab
    }
  },
  beforeUpdate () {
    if (this.$route.params.tab) {
      this.tab = this.$route.params.tab
    }
  }
}

</script>

<style scoped>
 * {
  font-family: Ubuntu;
  font-style: normal;
  font-weight: normal;
  letter-spacing: 0.05em;
  padding: 0;
  margin:0;
}

@media screen and (min-width: 860px) {
  .flexbox-container {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    min-width: 860px;
    justify-content: center;
    flex-direction: column;
  }

  .flexbox-item {
  margin: 10px;
  max-width: 1100px;
  width: 1100px;
  }

}

.title {
  margin-top: 4rem;
  margin-bottom: 4rem;
  font-size: 2.2rem;
  letter-spacing: 0.3em;
}

.topnav {
  text-align: center;
}

.topnav a {
  text-align: center;
  padding: 8px 25px 8px 25px;
  text-decoration: none;
  font-size: 15px;
  color: steelblue;
  border-style: solid;
  border-color: gainsboro;
  border-radius: 5px;
  margin-top: -1px;
  margin-bottom: 2px;
  margin-left: 1px;
  margin-right: -1px;
  border-width: 2px;
  font-size: 1.24rem;
}

.topnav a:hover {
      box-shadow: 2px 2px 2px rgba(0, 0, 0, 0.25);  
}

.topnav a.active {
  color: dimgrey;
  font-weight: bold;
}

.mobnav {
  position:fixed;
}

.botnav a {
  text-align: center;
  text-decoration: none;
  color: steelblue;
  border-style: solid;
  border-color: gainsboro;
  margin-left: -1px;
  margin-right: -1px;
  font-size: 1.1rem;
}

h1  {
  text-align: center;
}

</style>
