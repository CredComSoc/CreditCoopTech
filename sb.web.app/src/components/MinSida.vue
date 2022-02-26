<template>
  <h1> MIN SIDA </h1>
  <div class="topnav" id="myTopnav">
    <a href="#profil" class="active" id="profil">Min profil</a>
    <a href="#kop" name="#kop">Mina köp</a>
    <a href="#artiklar" id="artiklar">Mina artiklar</a>
    <a href="#statistik" id="statistik">Min statistik</a>
    <a href="#kopforfragningar" id="kopforfragningarr">Mina köpförfrågningar</a>
    <a href="#installningar" id="installningar">Inställningar</a>
  </div>
    <div>
      <MinaKop/>
    </div>
</template>

<script>
// @ is an alias to /src
import MinaKop from '@/components/MinaKop.vue'

export default {
  components: {
    MinaKop
  }
}

const paramString = window.location.href
const test = paramString.split('/')
const test2 = test[test.length - 1]
console.log(test)
console.log(test2)
var current = document.getElementsByClassName('active')
console.log(typeof current[0])
// current[0].className = current[0].className.replace(' active', '')
var newActive = document.getElementsByName(test2)
newActive.className += 'active'

</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
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
