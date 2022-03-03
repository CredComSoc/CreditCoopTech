<template>
    <div class="welcome-container">
        <div class="text-container">
            <h1 id="welcome-text">Välkommen {{ companyName }}</h1>
            <h1 id="inspired-text">Bli inspirerad nedan</h1>
        </div>
    </div>
</template>

<script>
/* Component that contains the welcome message in the banner */
export default {
  // component that represent the container for the welcome text

  name: 'WelcomeMessage',
  components: {
  },
  props: ['companyName']
}
</script>

<style scoped>
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
    }

    #inspired-text{
        font-size: 3vw;
        letter-spacing: 3px;
        margin-bottom: 40px;
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
            font-size: 250%;
        }
       #inspired-text{
           font-size: 120%;
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
