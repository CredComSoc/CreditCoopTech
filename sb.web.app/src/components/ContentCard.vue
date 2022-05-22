<template>
    <div :class="[`card-themed`, theme]">
        <h3> {{ title }} </h3>
        <h4> {{ description }} </h4>
        <ContentList :data=data :screenWidth=screenWidth :name=name />
        <div :class="[`outer-btn`, theme_btn]">
            <router-link style="text-decoration:none;" :to="{name: this.name}">
                <button :class="[`all-action`, theme_btn]" >{{ btn_txt }}</button>
            </router-link>
        </div>
    </div>
</template>

<script>
// Component that represent the outer container that hold ContentList

import ContentList from './ContentList.vue'

export default {
  name: 'ContentCard',
  components: {
    ContentList
  },
  props: ['title', 'description', 'theme', 'theme_btn', 'btn_txt', 'data', 'screenWidth', 'name']
}
</script>

<style scoped>
    * {
        font-family: Ubuntu;
        font-style: normal;
        font-weight: normal;
        letter-spacing: 0.05em;
        padding: 0;
        margin:0;
    }

    h3 {
        padding-top: 30px;
        font-size: 24px;
        text-align: center;
    }

    h4 {
        font-weight: 300;
        font-size: 16px;
        line-height: 18px;
        text-align: center;
        margin-top: 20px;
    }

    .card-themed {
        width: 45%;
        height: 373px;
        border-radius: 20px;
        margin: auto;
        margin-top: 30px;
        position: relative;
    }

    .blue-card {
        background: rgba(92, 155, 207, 0.26);
    }

    .yellow-card {
        background: rgba(246, 202, 115, 0.27);
    }

    .outer-btn {
        width: 160px;
        height: 36px;
        border: 1px solid #FFCF70;
        box-sizing: border-box;
        border-radius: 10px;
        box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
        margin-top: 20px;
        position: absolute;
        bottom:7%;
        right:5%;
    }

    .all-action {
        font-size: 17px;
        line-height: 23px;
        letter-spacing: 0.06em;
        color: #FFF;
        border: none;
        display: block;
        width: 100%;
        height:100%;
        border-radius: 10px;
        cursor: pointer;
    }

    .yellow-btn {
        background: #FFCF70;
        border: 1px solid #FFCF70;
    }

    .blue-btn {
        background: #4690CD;
        border: 1px solid #4690CD;
    }

    @media (max-width: 1994px) {
        .card-themed {
            width: 60%;
        }
    }

     @media (max-width: 1513px) {
        .card-themed {
            width: 75%;
        }
    }

     @media (max-width: 1212px) {
        .card-themed {
            width: 98%;
        }
    }

    /* @media (max-width: 929px) {
        .card-themed {
        }
    } */
</style>
