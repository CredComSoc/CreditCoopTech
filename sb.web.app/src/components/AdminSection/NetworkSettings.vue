<template>
  <!-- Inte implementerat Funktionalitet men bara designen-->
  <div class="wrapper">
    
    <div class="center">
      <h2 class="center-text">Nätverk Administration</h2>
    </div>
    <div class="main">
      <h3>Transaktionsavgift</h3>
      <div>
        <input type="text" placeholder="Säljare Procentsats">
        <input type="text" placeholder="Köpare Procentsats">
      </div>
      <h3>Ränta på BSEK</h3>
      <div>
        <input type="text" placeholder="Procentsats">
      </div>
      <h3>Default Balance Limits</h3>
      <div>
        <input type="text" placeholder="Maximum">
        <input type="text" placeholder="Minimum">
      </div>
    </div>
  </div>
</template>

<script>

export default {
  
  data () {
    return {
      SearchData: [],
      singleListingData: [],
      popupActive: false,
      listingObjPopup: Object,
      profileName: ''
    }
  },
  
  methods: {
  },
  mounted: function () {
  }
}
</script>

<style scoped>

* {
  font-family: 'Ubuntu', sans-serif;
  padding: 0;
  margin: 0;
}

.wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
}
.center-text {
  text-align: center;
  margin: 0rem 0rem;
  font-size: 2.2rem;
  letter-spacing: 0.3em;  
  text-align: center;
}
h2 {
  margin-top: 2rem;
  font-size: 3rem;
}

.center {
  display: flex;
  flex-direction: row;
  justify-content: flex-start;
  margin-bottom: 40px;
}

.main {
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: flex-start;
  border: 3px solid black;
  height: 70vh;
  width:70vw;
  padding: 3%;
  gap :5%;
}

h3 {
  margin-left: 1rem;
}

::-webkit-scrollbar {
  width: 4px;
}

/* Track */
::-webkit-scrollbar-track {
  background: #f1f1f1; 
  margin-right: 2px;
}

/* Handle */
::-webkit-scrollbar-thumb {
  background: #888; 
  
  border-radius: 2px;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: #555; 
}

</style>
