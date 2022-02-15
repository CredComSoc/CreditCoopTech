<template>
    <div class="element-container">
        <img :src="require(`../assets/list_images/${elementInfo.img_path}`)" alt="Coffea">
        <h4 class="element-title"> {{ elementInfo.title }} </h4>
        <p class="element-desc"> {{ elementInfo.desc }}  </p>
    </div>
</template>

<script>
export default {
  name: 'ListElement',
  components: {
  },
  props: ['elementInfo']
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
        font-size: 13px;
    }

    .element-container {
        width: 160px;
        height: 160px;
        background: #FFFFFF;
        box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
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
