<template>
    <div id=saldo-card-outline>
      <div class="arrow-button" id="right-arrow-button" type="button" @click="closeCard" v-if="isActive">
        <img id="right-arrow-img" src="../assets/sidecard_logos/right-arrow.png"/>
      </div>
      <div class="arrow-button" id="left-arrow-button" type=button v-if="!isActive">
         <img id="left-arrow-img" src="../assets/sidecard_logos/left-arrow.png"  @click="openCard"/>
      </div>
      <div id='saldo-card' v-if="isActive">
          <figure id='top-log'>
            <a href="#">
              <img src="../assets/sidecard_logos/Saldo.png"/>
              <figcaption class="l-text" id='top-logo-text'> {{ $t('balance') }}: {{ this.$store.state.saldo }} {{ $t('org.tkn') }}</figcaption>
            </a>
          </figure>
      </div>
      <div class="border" v-if="isActive"></div>
      <div id='credit-card' v-if="isActive">
          <figure id='top-log'>
            <a href="#">
              <img src="../assets/sidecard_logos/credit-card.png"/>
              <!-- state.saldo ska ändras till att connecta till kredit istället-->
              <figcaption class="l-text" id='top-logo-text'> {{ $t('credit') }}: {{ this.availableBalance(this.$store.state) }} {{ $t('org.tkn') }}</figcaption>
            </a>
          </figure>
      </div>
    </div>
</template>

<script>
/* Component that contains the saldo card */
export default {
  data () {
    return {
      isActive: true,
      opend: false
    }
  },
  name: 'SaldoCard',
  components: {
  },
  props: ['saldo', 'screenWidth'],
  watch: {
    screenWidth: {
      handler: function (scrWidth) {
        const outline = document.getElementById('saldo-card-outline')
        if (scrWidth < 1212 && !this.opend) {
          this.isActive = false
          outline.style.width = '25px'
          outline.style.height = '40px'
        } else if (scrWidth >= 1212 && this.opend) {
          this.isActive = true
          this.opend = true
          outline.style.width = '130px'
          outline.style.height = '70px'
        }
      }
    }
  },
  methods: {
    closeCard (scrWidth) {
      this.isActive = false
      if (scrWidth.x >= 1212) {
        this.opend = false
      }
      const outline = document.getElementById('saldo-card-outline')
      outline.style.width = '25px'
      outline.style.height = '40px'
    },
    openCard (scrWidth) {
      this.isActive = true
      if (scrWidth.x < 1212) {
        this.opend = true
      }
      const outline = document.getElementById('saldo-card-outline')
      outline.style.width = '130px'
      outline.style.height = '70px'
    },
    availableBalance (state) {
      const saldo = state.saldo
      const creditLine = state.creditLine
      return (saldo < 0) ? creditLine : saldo + creditLine
    }
  }
}
</script>

<style scoped>


  #saldo-card-outline{
    width: 135px;
    height: 70px;
    top: calc(50% - 75px);
    bottom: 50%;
    position: fixed;
    right: 0;
    z-index: 2;
  }

  #saldo-card{
      background-color: #F9C661;
      width: 85%;
      height: 100%;
      border-radius: 10px 0px 0px 0px;
      float: right;
      display: flex;
      justify-content: space-evenly;
      flex-direction: column;
      align-items: center;
  }

  #credit-card{
      background-color: #F9C661;
      width: 85%;
      height: 100%;
      border-radius: 0px 0px 0px 10px;
      float: right;
      display: flex;
      justify-content: space-evenly;
      flex-direction: column;
      align-items: center;
  }

    a{
        text-decoration: none;
    }

    .l-text{
        color:black;
        font-family: 'Ubuntu';
        font-size: 90%;
        text-align: center;
        font-weight: 500;
    }

    figure{
        text-align: center;
    }

    .border{
      border: 0px solid #dee2e6 !important;
      width: 85%;
      float: right;
      height: 2px;
      background-color: black;
      display: flex;
    }
    #kund{
        position: relative;
    }

    #line{
        border-top: 1px solid #000000;
        width:100%;
    }

    #top-log{
      width: 100%;
    }

    .arrow-button{
        background-color: #F9C661;
        position:fixed;
        border-radius: 10px 0px 0px 10px;
        cursor: pointer;
    }

    #right-arrow-button{
      width: 100%;
      z-index: -1;
    }

    #left-arrow-button{
        right: 0;
        width: 25px;
    }

    .arrow-button img:hover {
      transform: scale(1.2);
    }

</style>
