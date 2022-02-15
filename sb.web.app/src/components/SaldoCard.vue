<template>
    <div id='card'>
        <figure id='top-logo'>
            <a href="#">
              <img src="../assets/sidecard_logos/Saldo.png" alt="shop knapp" id="event-logo"/>
              <figcaption class="l-text" id='top-logo-text'> Saldo </figcaption>
            </a>
          </figure>

          <figure>
            <a href="#">
              <img src="../assets/sidecard_logos/Kund.png" alt="shop knapp" id="event-logo"/>
              <figcaption class="l-text" id='bottom-logo-text'> Kundtjänst </figcaption>
            </a>
          </figure>
    </div>
</template>

<style scoped>
    #card{
        background-color: #F9C661;
        width:150px;
        height: 110px;
        position:fixed;
        right: 0;
        border-radius: 10px 0px 0px 10px;
    }

    a{
        text-decoration: none;
    }

    .l-text{
        color:black;
        font-family: 'Ubuntu';
        font-size: 90%;
        text-align: center;
    }

    figure{
        text-align: center;
    }

    #top-logo{
        border-bottom: 0.5px solid  black;
    }

    #top-logo-text{
        padding-bottom: 6px;
    }

    #bottom-logo-text{
        padding-bottom: 6px;
    }

</style>
