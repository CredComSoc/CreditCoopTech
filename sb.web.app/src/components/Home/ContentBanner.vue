<template>
  <div class="banner">
    <img src="../../assets/banner.jpg" alt="välkommen">
    <div class="welcome-container">
        <div class="text-container">
            <h1 id="welcome-text">{{ $t('welcome') }} {{ companyName }}</h1>
            <h1 id="inspired-text">{{ $t('get_inspired') }}</h1>
        </div>
    </div>
  </div>
</template>

<script>
// Component that represent outer container for WelcomeMessage and banner image

export default {
  name: 'Banner',
  props: ['companyName']
}
</script>

<style scoped>
  .banner{
      width: 100%;
      height: 405px;
      position: relative;
  }
  img{
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

      .welcome-container{
        background-color: white;
        background: hsla(0, 0%, 100%, 0.4);
        position: absolute;
        top: 50%;
        left: 50%;
        margin-right: -50%;
        transform: translate(-50%, -50%);
        max-width: 90%;
        width: fit-content;
        height: fit-content;
        border-radius: 25px;
        text-align: center;
        vertical-align: middle;
        display: table-cell;
        color: #fdca63;
    }

    .text-container{
        margin-top: 60px;
        word-wrap: break-word
    }

    h1{
        font-family: "Lora";
        font-style: normal;
        font-weight: 600;
        text-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
    }

    #welcome-text{
        font-size: 4vw;
        margin-left:50px;
        margin-right:50px;
        letter-spacing: 5px;
        text-shadow: 0 0 1px black, 0 0 1px black, 0 0 1px black, 0 0 1px black;
    }

    #inspired-text{
        font-size: 3vw;
        letter-spacing: 3px;
        margin-bottom: 40px;
        text-shadow: 0 0 1px black, 0 0 1px black, 0 0 1px black;
    }

    #inspired-text::after, #inspired-text::before {
        display:block;
        content : "";
        max-width: 350px;
        border-bottom: 2px solid white;
        margin: 0 auto;
        padding: 5px 0px 5px 5px;
    }

    @media (min-width: 710px) {
        #welcome-text{
            font-size: 350%;
        }
       #inspired-text{
           font-size: 200%;
       }
    }

    @media (max-width: 500px) {
        #welcome-text{
            font-size: 110%;
        }

        #inspired-text::after, #inspired-text::before {
            max-width: 230px;
            padding: 3px;
        }
    }
</style>
