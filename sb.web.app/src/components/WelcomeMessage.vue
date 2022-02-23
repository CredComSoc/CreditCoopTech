<template>
    <div class="welcome">
        <div class="text">
            <h1 id="welcome_text">Välkommen {{ companyName }}</h1><!--Looks weird when name to large-->
            <h1 id="inspired_text">Bli inspererad nedan</h1>
        </div>
    </div>
</template>

<script>
export default {
  name: 'WelcomeMessage',
  components: {
  },
  props: ['companyName']
}
</script>

<style scoped>
    .welcome{
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

    .text{
        margin-top: 60px;
    }

    h1{
        font-family: "Lora";
        font-style: normal;
        font-weight: 600;
        text-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
    }

    #welcome_text{
        font-size: 250%;
        margin-left:50px;
        margin-right:50px;
        line-height: 50px;
        letter-spacing: 7px;
    }

    #inspired_text{
        font-size: 120%;
        letter-spacing: 3px;
        margin-bottom: 40px;
    }

    #inspired_text::after, #inspired_text::before {
        display:block;
        content : "";
        max-width: 350px;
        border-bottom: 2px solid white;
        margin: 0 auto;
        padding: 5px 5px;
    }

    @media (max-width: 500px) {
        .text{
            margin-top: 30px;
        }
       #welcome_text {
           font-size: 150%;
       }

       #inspired_text{
           font-size: 100%;
       }

       #inspired_text::after, #inspired_text::before {
        max-width: 250px;
       }
    }

</style>
