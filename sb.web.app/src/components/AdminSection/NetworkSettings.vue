<template>
  <!-- Ingen implementerad funktionalitet, bara designen. -->
  <div class="wrapper">
    
    <div class="center">
      <h2 class="center-text" >{{ $t('network_settings') }}</h2>
    </div>
    <div class="main">
      <h3>{{ $t('transaction_fee') }}</h3>
      <div>
        <input type="text" placeholder="$t('seller_percentage')">
        <input type="text" placeholder="$t('buyer_percentage')">
      </div>
        <h3>{{ $t('interest_rate_on_negative_balances') }}</h3>
      <div>
        <input type="text" placeholder="$t(percentage)">
      </div>
      <h3>{{ $t('default_balance_limits') }}</h3> <!-- Ska ändras till svenska -->
      <div>
        <input type="text" placeholder="$t('positive')">
        <input type="text" placeholder="$t('negative')">
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


.wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
}
.center-text {
  text-align: center;
  margin: 0rem 0rem;
  font-size: 2.2rem;
  margin-top: 2rem;
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
  height: 70vh;
  width:35vw; /* OBS, har minskat hela fönstret, ändra tillbaka vid tillägg */
  padding: 3%;
  gap :5%;
}

h3 {
  margin-top: 1rem;
  font-size:1.5rem;
  margin-bottom: 0.5rem;
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
