<template>
    <div id=outline>
      <div class="arrow" id="right-arrow-button" type="button" @click="closeCard" v-if="isActive">
        <img src="../assets/sidecard_logos/right-arrow.png"/>
      </div>
      <div class="arrow" id="left-arrow-button" type=button v-if="!isActive">
         <img src="../assets/sidecard_logos/left-arrow.png"  @click="openCard"/>
      </div>
      <div id='card' v-if="isActive">
          <figure id='top-log'>
            <a href="#">
              <img src="../assets/sidecard_logos/Saldo.png"/>
              <figcaption class="l-text" id='top-logo-text'> Saldo: {{ saldo }} bKr</figcaption>
            </a>
          </figure>
          <div class="line"></div>
          <figure>
            <a href="#">
              <img src="../assets/sidecard_logos/Kund.png" id="kund"/>
              <img src="../assets/sidecard_logos/Dots.png" id="dots"/>
              <figcaption class="l-text" id='bottom-logo-text'> Kundtjänst </figcaption>
            </a>
          </figure>
      </div>
    </div>
</template>

<script>
export default {
  data () {
    return {
      isActive: true
    }
  },
  name: 'SaldoCard',
  components: {
  },
  props: ['saldo'],
  mounted () {
    this.onResize()
    window.addEventListener('resize', this.onResize)
  },
  methods: {
    onResize () {
      if (window.innerWidth < 1212) {
        this.isActive = false
      } else {
        this.isActive = true
      }
    },
    closeCard () {
      this.isActive = false
    },
    openCard () {
      this.isActive = true
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

  #outline{
    width: 150px;
    height: 100px;
    top:50%;
    bottom: 50%;
    position:fixed;
    right: 0;
    z-index: 2;
  }

  #card{
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

    #dots{
        position: absolute;
        top: 66px;
        left: 78px;
    }

    #kund{
        position: relative;
    }

    .line{
        border-top: 1px solid #000000;
        width:100%;
    }

    #top-log{
      width: 100%;
    }

    .arrow{
        background-color: #F9C661;
        position:fixed;
        border-radius: 10px 0px 0px 10px;
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
