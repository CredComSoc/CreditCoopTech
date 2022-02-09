<template>
    <div class="welcome">
        <div class="text">
            <h1 id="welcome_text">Välkommen Florist AB</h1>
            <hr/>
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
        width: 700px;
        height: 200px;
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
        line-height: 24px;
        letter-spacing: 7px;
    }

    #welcome_text{
        font-size: 48px;
    }

    #inspired_text{
        font-size: 20px;
        margin:-3px;
    }

    hr{
        width:50%;
        border-width:2px;
        color:white;
    }
</style>
