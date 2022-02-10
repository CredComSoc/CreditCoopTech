<template>
    <div class="welcome">
        <div class="text">
            <h1 id="welcome_text">Välkommen Florist AB</h1><!--Looks weird when name to large-->
            <hr id="top_ine"/>
             <h1 id="inspired_text">Bli inspererad nedan</h1>
             <hr/>
        </div>
    </div>
</template>

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
        line-height: 35px;
        letter-spacing: 7px;
    }

    #inspired_text{
        font-size: 120%;
        margin:-3px;
        letter-spacing: 3px
    }

    hr{
        width:250px;
        border-width:2px;
        color:white;
    }
</style>
