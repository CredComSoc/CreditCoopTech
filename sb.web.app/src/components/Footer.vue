<template>
    <div class="footer">
        <p class="line-right"><a href="#">Om oss</a></p>
        <p class="line-right"><a href="#">Om Barter</a></p>
        <p><a href="#">Kontakta oss</a></p>
    </div>
</template>

<style scoped>
    .footer{
        position: fixed;
        left: 0;
        bottom: 0;
        height: 5%;
        width: 100%;
        min-width:600px;
        text-align: center;
    }

    p{
        display: inline;
        padding-right: 10%;
        padding-left:10%;
        padding-top:0.5%;
    }

    .line-right{
        border-right: 0.5px solid  black;
    }

    a{
        color:black;
        text-decoration: none;
    }
</style>
