<template>
    <div id=saldo-card-outline>
      <div class="arrow-button" id="right-arrow-button" type="button" @click="closeCard" v-if="isActive">
        <img src="../assets/sidecard_logos/right-arrow.png"/>
      </div>
      <div class="arrow-button" id="left-arrow-button" type=button v-if="!isActive">
         <img src="../assets/sidecard_logos/left-arrow.png"  @click="openCard"/>
      </div>
      <div id='saldo-card' v-if="isActive">
          <figure id='top-log'>
            <a href="#">
              <img src="../assets/sidecard_logos/Saldo.png"/>
              <figcaption class="l-text" id='top-logo-text'> Saldo: {{ saldo }} bKr</figcaption>
            </a>
          </figure>
          <div id="line"></div>
          <figure>
            <a href="#">
              <img src="../assets/sidecard_logos/KundDots.png" id="kund"/>
              <figcaption class="l-text" id='bottom-logo-text'> Kundtjänst </figcaption>
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
          outline.style.height = '25px'
        } else if (scrWidth >= 1212 && this.opend) {
          this.isActive = true
          this.opend = false
          outline.style.width = '150px'
          outline.style.height = '100px'
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
      outline.style.height = '25px'
    },
    openCard (scrWidth) {
      this.isActive = true
      if (scrWidth.x < 1212) {
        this.opend = true
      }
      const outline = document.getElementById('saldo-card-outline')
      outline.style.width = '150px'
      outline.style.height = '100px'
    }
  }
}
</script>

<style scoped>

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  #saldo-card-outline{
    width: 150px;
    height: 100px;
    top:50%;
    bottom: 50%;
    position:fixed;
    right: 0;
    z-index: 2;
  }

  #saldo-card{
      background-color: #F9C661;
      width: 85%;
      height: 100%;
      border-radius: 10px 0px 0px 10px;
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
        font-size: 70%;
        text-align: center;
        font-weight: 500;
    }

    figure{
        text-align: center;
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

</style>
