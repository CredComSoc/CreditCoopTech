<template>
    <div>
    <div v-if="elementInfo.theme === `regular`" class="element-container">
        <img :class="elementInfo.theme" :src="require(`../assets/list_images/${elementInfo.img_path}`)" alt="Coffea">
        <h4 class="element-title"> {{ elementInfo.title }} </h4>
        <p class="element-desc"> {{ formatText(elementInfo.desc) }}  </p>
    </div>
    <div v-if="elementInfo.theme === `ellipse`" class="ellipse-container">
        <figure>
            <img :class="elementInfo.theme" :src="require(`../assets/list_images/${elementInfo.img_path}`)" alt="Coffea">
        </figure>
        <div class="chin-card">
            <h4 class="element-title"> {{ elementInfo.title }} </h4>
        </div>
    </div>
    </div>
</template>

<script>
export default {
  name: 'ListElement',
  components: {
  },
  props: ['elementInfo'],
  methods: {
    formatText (str) {
      if (str.length > 35) {
        if (str.substring(0, 35).replace(/[a-z]/g, '').length > 20) {
          return str.substring(0, 30) + '...'
        } else {
          return str.substring(0, 35) + '...'
        }
      } else {
        if (str.length > 30) {
          if (str.replace(/[a-z]/g, '').length > 25) {
            return str.substring(0, 30) + '...'
          } else {
            return str
          }
        } else {
          return str
        }
      }
    }
  }
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
        font-weight: 500;
        font-size: 11px;
    }

    .regular {
        width: 100%;
        height: 70%;
    }

    .ellipse {
        height: 120px;
        width: 133px;
        border-radius: 50%;
    }

    .element-container {
        width: 160px;
        height: 160px;
        background: #FFFFFF;
        box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
        word-break: break-all;
    }

    .ellipse-container {
        width: 150px;
        height: 205px;
        display: flex;
        flex-direction: column;
        background: transparent;
        border: none;
    }

     .element-container h4 {
        margin-top: 4px;
        font-weight: bold;
     }

    figure {
        background-color: transparent;
        margin: 0 auto;
        z-index: 1;
    }

    .chin-card {
        box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
        height:100%;
        text-align: center;
        background-color: #FFFFFF;
        transform: translateY(-60%);
        width: 100%;
        overflow-wrap: break-word;
    }

    .chin-card h4 {
        margin-top: 60px;
        width: 90%;
        font-weight: bold;
    }

    .element-title {
        margin-left: 8px;
    }

    .element-desc {
        color: grey;
        margin-left: 9px;
        margin-right: 30px;
    }

</style>
