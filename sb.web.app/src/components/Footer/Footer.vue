<template>
    <footer>
        <div class="footer-container">
            <p class="side-items" id="om-sidan"><router-link :to="{name:'About'}">{{ $t('about') }}</router-link></p>
            <p id="middle-item"><router-link :to="{name:'Home'}">{{ $t('home') }}</router-link></p>
            <p class="side-items"><a :href="contactLink">{{ $t('contact') }}</a></p>
        </div>
    </footer>
</template>

<script>
// Component that represent the footer of the website

export default {
  name: 'Footer',
  data () {
    return {
      contactLink: 'mailto:' + process.env.VUE_APP_SUPPORT_EMAIL,
      contactEmail: process.env.VUE_APP_SUPPORT_EMAIL    
    }
  }
}
</script>

<style scoped>
    footer {
        margin: 0 auto;
        width: 60%;
    }

    .footer-container{
        margin-top: 100px;
        display: flex;
        justify-content: space-evenly;
        justify-items: center;
        width: 100%;
        text-align: center;
    }

    p{
        display: inline;
        font-family: 'Ubuntu';
        font-weight: 300;
        font-size:95%;
    }

    #middle-item{
        border-left: 0.5px solid  black;
        padding-left: 15%;
        border-right: 0.5px solid  black;
        padding-right: 15%;
        flex-shrink: 0;
    }

    .side-items{
        margin-left: 10%;
        margin-right: 10%;
        flex-shrink: 0;
    }

    a {
        color:black;
        text-decoration: none;
    }

    a:hover {
        color:black;
        text-decoration-line: underline;
    }
</style>
