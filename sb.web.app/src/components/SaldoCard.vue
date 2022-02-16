<template>
    <div id='card'>
        <figure id='top-logo'>
            <a href="#">
              <img src="../assets/sidecard_logos/Saldo.png"/>
              <figcaption class="l-text" id='top-logo-text'> Saldo: {{saldo}} bKr</figcaption>
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
</template>

<script>
export default {
  data () {
    return {
      isActive: false
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
      if (window.innerWidth < 861) {
        this.isActive = true
      } else {
        this.isActive = false
      }
    }
  }
}
</script>

<style scoped>
    #card{
        background-color: #F9C661;
        width:140px;
        height: 100px;
        position:fixed;
        right: 0;
        border-radius: 10px 0px 0px 10px;
        top:50%;
        bottom: 50%;
        z-index: 2;
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
        top:60px;
        left: 62px;
    }

    #kund{
        position: relative;
    }

    .line{
        border-top: 1px solid #000000;
        top:50%;
        margin-top:-11px;
    }

    #top-logo{
        margin-top:4px
    }

</style>
