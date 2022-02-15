<template>
    <div class="footer">
        <p class="side-items" id="e"><a href="#">Om oss</a></p>
        <p id="middle-item"><a href="#">Om Barter</a></p>
        <p class="side-items"><a href="#">Kontakta oss</a></p>
    </div>
</template>

<style scoped>
    .footer{
        display: flex;
        position: absolute;
        bottom: 0;
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
        padding-left:15%;
        border-right: 0.5px solid  black;
        padding-right:15%;
    }

    .side-items{
        margin-left:10%;
        margin-right: 10%;
    }

    a{
        color:black;
        text-decoration: none;
    }
</style>
